<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Finder
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Finder\Kunena\Extension;

\defined('_JEXEC') or die('');

use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Finder\AfterDeleteEvent;
use Joomla\CMS\Event\Finder\AfterSaveEvent;
use Joomla\CMS\Event\Finder\BeforeSaveEvent;
use Joomla\CMS\Log\Log;
use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Result;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\QueryInterface;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaFinderAccess;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Event\KunenaBeforeDeleteEvent;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tables\TableKunenaCategories;
use Kunena\Forum\Libraries\Tables\TableKunenaMessages;
use Kunena\Forum\Libraries\Tables\TableKunenaTopics;
use Throwable;

/**
 * Finder adapter for com_kunena.
 *
 * @since Kunena 6.0
 */
final class Kunena extends Adapter implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * The plugin identifier.
     *
     * @var    string
     * @since  2.5
     */
    protected $context = 'Kunena';

    /**
     * The extension name.
     *
     * @var    string
     * @since  2.5
     */
    protected $extension = 'com_kunena';

    /**
     * The sublayout to use when rendering the results.
     *
     * @var    string
     * @since  2.5
     */
    protected $layout = 'topic';

    /**
     * The mime type of the content the adapter indexes.
     *
     * @var    string
     * @since  2.5
     */
    protected $mime = 'txt';

    /**
     * The type of content that the adapter indexes.
     *
     * @var    string
     * @since  2.5
     */
    protected $type_title = 'Forum Post';

    /**
     * The field the published state is stored in.
     *
     * @var    string
     * @since  2.5
     */
    protected $state_field = 'published';

    /**
     * @var    ?string
     * @since  7.0.0
     */
    protected $old_cataccesstype = \null;

    /**
     * The view levels of the site (id => groups), read once.
     *
     * @var    ?array
     * @since  Kunena 7.1
     */
    protected ?array $viewLevels = \null;

    /**
     * The user groups of the site, read once: 'parents' (id => id of the parent group) and 'always' (the groups of the
     * administrators of the site).
     *
     * @var    ?array
     * @since  Kunena 8.0
     */
    protected ?array $userGroups = \null;

    /**
     * The category and the state of the topics which are being saved (id => [category, hold]), from onFinderBeforeSave().
     *
     * @var    array
     * @since  Kunena 8.0
     */
    protected array $oldTopics = [];

    /**
     * What the index took from the categories which are being saved (id => categoryIndexState()), from onFinderBeforeSave().
     *
     * @var    array
     * @since  Kunena 8.0
     */
    protected array $oldCategories = [];

    /**
     * The ids of the messages which the last batch read (getItems()), also those which gave no item.
     *
     * @var    int[]
     * @since  Kunena 8.0
     */
    protected array $batchIds = [];

    /**
     * The table name.
     *
     * @var string
     * @since  2.5
     */
    protected $table = '#__kunena_messages';

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   5.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return array_merge(parent::getSubscribedEvents(), [
            'onFinderBeforeSave'   => 'onFinderBeforeSave',
            'onFinderAfterSave'    => 'onFinderAfterSave',
            'onFinderBeforeDelete' => 'onFinderBeforeDelete',
            'onFinderAfterDelete'  => 'onFinderAfterDelete',
            'onBuildIndex'         => 'onBuildIndex',
        ]);
    }
    
    /**
     * Inject the indexer used by the Smart Search debug view.
     *
     * Joomla normally aliases Adapter to DebugAdapter for this request.
     * This explicit method also supports plugins whose Adapter parent class
     * was loaded before that alias could be established.
     *
     * @param   Indexer  $indexer  Debug indexer instance.
     *
     * @return  void
     *
     * @since   Kunena 7.0.12
     */
    public function setIndexer(Indexer $indexer): void
    {
        $this->indexer = $indexer;
    }
    
    /**
     * Build one Finder result for the Smart Search debug view.
     *
     * @param   mixed  $id  Kunena message ID.
     *
     * @return  void
     *
     * @since   Kunena 7.0.12
     */
    public function debug($id): void
    {
        $this->setup();
        $this->index($this->getItem((int) $id));
    }

    /**
     * Method to reindex the link information for an item that has been saved.
     * This event is fired before the data is actually saved so we are going
     * to queue the item to be indexed later.
     *
     * @param   BeforeSaveEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   2.5
     * @throws  Exception on database error.
     */
    public function onFinderBeforeSave(BeforeSaveEvent $event): void
    {
        $context = $event->getContext();
        $row     = $event->getItem();
        $isNew   = $event->getIsNew();

        // If a category will be change, we want to see, if the accesstype and access level has changed
        if (($row instanceof TableKunenaCategories) && !$isNew && $row->id) {
            $old_table = clone $row;
            $old_table->load();
            $this->old_cataccess                 = $old_table->access;
            $this->old_cataccesstype             = $old_table->accesstype;
            $this->oldCategories[(int) $row->id] = $this->categoryIndexState($old_table);
        }

        // The category and the state of a topic before it is saved: a topic is moved, deleted, restored or approved with its
        // messages changed in the database directly (KunenaTopic::move(), publish()), the messages are not saved one by one.
        if (($row instanceof TableKunenaTopics) && !$isNew && $row->id) {
            $old_table = clone $row;
            $old_table->load();
            $this->oldTopics[(int) $row->id] = [(int) $old_table->category_id, (int) $old_table->hold, (int) $old_table->moved_id];
        }
    }

    /**
     * Method to determine if the access level of an item changed.
     *
     * @param   AfterSaveEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   2.5
     * @throws  Exception on database error.
     */
    public function onFinderAfterSave(AfterSaveEvent $event): void
    {
        $context = $event->getContext();
        $row     = $event->getItem();
        $isNew   = $event->getIsNew();

        // If a category has been changed, we want to check if the access has been changed
        // The messages of a category are indexed again when what the index takes from the category changed: its access (a level, or a
        // user group since Kunena 7.1), its state and "own topics only". Only a change of the level of a category of levels did it:
        // the posts of a category which was unpublished, made "own topics only" or given to another group stayed in the results
        // of the search. An unpublished category hides its subcategories too: their messages are indexed again with it.
        if (($row instanceof TableKunenaCategories) && isset($this->oldCategories[(int) $row->id])) {
            $old = $this->oldCategories[(int) $row->id];
            $new = $this->categoryIndexState($row);
            unset($this->oldCategories[(int) $row->id]);

            if ($old !== $new) {
                // The state and the parent of a category give the state of its subcategories too.
                $tree   = $old['published'] !== $new['published'] || $old['parentid'] !== $new['parentid'];
                $catids = $tree ? $this->getCategoryTree((int) $row->id) : [(int) $row->id];

                foreach ($this->getMessageIdsByCategories($catids) as $id) {
                    $this->reindex($id);
                }
            }
        }

        // A topic which moved to another category or was deleted, restored or approved: its messages are indexed again with their
        // new category (its access level) and state. They kept the old ones: the posts of a topic moved into a closed category or
        // deleted stayed in the results of the search of the site.
        if (($row instanceof TableKunenaTopics) && isset($this->oldTopics[(int) $row->id])) {
            [$oldCategory, $oldHold, $oldMoved] = $this->oldTopics[(int) $row->id];
            unset($this->oldTopics[(int) $row->id]);

            $topicIds = [];

            if ($oldCategory !== (int) $row->category_id || $oldHold !== (int) $row->hold) {
                $topicIds[] = (int) $row->id;
            }

            // A topic which became the shadow of a moved topic (moved with a shadow, or merged into another topic): its messages are
            // in the topic it points to now, maybe in another category.
            if ((int) $row->moved_id > 0 && $oldMoved !== (int) $row->moved_id) {
                $topicIds[] = (int) $row->moved_id;
            }

            foreach ($topicIds as $topicId) {
                foreach ($this->getMessageIdsByTopic($topicId) as $id) {
                    // The message as it is in the database now (an object of this request has the category and state before).
                    KunenaMessageHelper::get($id, true);
                    $this->reindex($id);
                }
            }
        }

        // We only want to handle Kunena messages in here
        if ($row instanceof TableKunenaMessages) {
            // Reindex the item.
            $this->reindex($row->id);
        }
    }

    /**
     * Method to remove the link information for items that have been deleted.
     * Since Messages are getting deleted in process of deleting categories or messages, we
     * delete the finderresults before those objects are deleted.
     *
     * @param   KunenaBeforeDeleteEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   2.5
     * @throws  Exception on database error.
     */
    public function onFinderBeforeDelete(KunenaBeforeDeleteEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();

        if ($table instanceof TableKunenaCategories) {
            $messages = $this->getMessagesByCategory($table->id);

            foreach ($messages as $message) {
                $this->remove($message->id);
            }
        } elseif ($table instanceof TableKunenaTopics) {
            $messages = $this->getMessagesByTopic($table->id);

            foreach ($messages as $message) {
                $this->remove($message->id);
            }
        }
    }

    /**
     * Method to remove the link information for items that have been deleted.
     *
     * @param   AfterDeleteEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   2.5
     * @throws  Exception on database error.
     */
    public function onFinderAfterDelete(AfterDeleteEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();

        if ($context == 'com_finder.index') {
            $this->remove($table->link_id);
        } elseif ($table instanceof TableKunenaMessages) {
            $this->remove($table->id);
        }
    }

    /**
     * Method to index a batch of content items. This method can be called by
     * the indexer many times throughout the indexing process depending on how
     * much content is available for indexing. It is important to track the
     * progress correctly so we can display it to the user.
     *
     * @return   boolean  True on success.
     *
     * @since   2.5
     * @throws  Exception on error.
     */
    public function onBuildIndex(): bool
    {
        Log::add('FinderIndexerAdapter::onBuildIndex', Log::INFO);

        // Get the indexer and adapter state.
        $iState = Indexer::getState();
        $aState = $iState->pluginState[$this->context];

        // Check the progress of the indexer and the adapter.
        if ($iState->batchOffset == $iState->batchSize || $aState['offset'] == $aState['total']) {
            return true;
        }

        // Get the batch offset and size.
        $offset = (int) $aState['offset'];
        $limit  = (int) ($iState->batchSize - $iState->batchOffset);

        // The adapter state offset counts indexed items (like core adapters), while
        // the item query walks message ids. Track the id cursor separately so the
        // progress accounting adds up even when message ids have gaps.
        $lastId = (int) ($aState['last_id'] ?? 0);

        // Capture any stray output/warnings so they get logged instead of bubbling
        // up to com_finder, which treats any output as a plugin failure.
        ob_start();

        // Get the content items to index.
        try {
            $items = $this->getItems($lastId, $limit);
        } catch (Throwable $e) {
            ob_end_clean();
            Log::add("Finder Kunena plugin: getItems({$lastId}, {$limit}) threw " . \get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine(), Log::ERROR);
            throw $e;
        }

        // The buffer of getItems() is closed here too (it stayed open after every batch, and its output went to com_finder).
        $stray = trim(ob_get_clean());

        if ($stray !== '') {
            Log::add("Finder Kunena plugin: getItems({$lastId}, {$limit}) produced output: " . substr($stray, 0, 3000), Log::ERROR);
        }

        foreach ($items as $item) {
            ob_start();

            try {
                $this->index($item);
            } catch (Throwable $e) {
                ob_end_clean();
                Log::add("Finder Kunena plugin: index of message {$item->id} threw " . \get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine(), Log::ERROR);
                throw $e;
            }

            $stray = trim(ob_get_clean());

            if ($stray !== '') {
                Log::add("Finder Kunena plugin: index of message {$item->id} produced output: " . substr($stray, 0, 3000), Log::ERROR);
            }

            // The next batch starts after this message (the cursor was never moved, so every batch read the first messages again
            // and the messages after the first batch were never indexed).
            $lastId = max($lastId, (int) $item->id);

            // Adjust the offsets.
            $offset++;
            $iState->batchOffset++;
            $iState->totalItems--;
        }

        // The cursor goes past every id which the batch read, also of a message which gave no item (no row of its text): such a
        // message was read again by every batch, and a batch with one item less than its limit ended the index too early.
        if ($this->batchIds) {
            $lastId = max($lastId, max($this->batchIds));
        }

        // If the query returned fewer messages than requested, all messages are indexed.
        // Settle any difference between the counted total and the items actually
        // delivered (e.g. messages deleted while indexing was running).
        if (\count($this->batchIds) < $limit) {
            $iState->totalItems -= max(0, (int) $aState['total'] - $offset);
            $offset             = (int) $aState['total'];
        }

        // Update the indexer state.
        $aState['offset']                    = $offset;
        $aState['last_id']                   = $lastId;
        $iState->pluginState[$this->context] = $aState;
        Indexer::setState($iState);

        unset($items, $item);

        return \true;
    }

    /**
     * Method to index an item. The item must be a FinderIndexerResult object.
     *
     * @param   Result  $item  The item to index as an FinderIndexerResult object.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     * @throws  Exception on database error.
     */
    protected function index(Result $item): void
    {
        // Check if the extension is enabled
        if (ComponentHelper::isEnabled($this->extension) == \false) {
            return;
        }

        // Add the meta-data processing instructions.
        $item->addInstruction(Indexer::META_CONTEXT, 'author');

        // Add the type taxonomy data.
        $item->addTaxonomy('Type', 'Forum Post');

        // Add the author taxonomy data.
        if (!empty($item->author)) {
            $item->addTaxonomy('Author', $item->author);
        }

        // Add the category taxonomy data.
        // $item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

        // Add the language taxonomy data.
        $item->addTaxonomy('Language', $item->language);

        // Get content extras.
        Helper::getContentExtras($item);

        // Index the item.
        $this->indexer->index($item);
    }

    /**
     * Method to setup the indexer to be run.
     *
     * @return   boolean  True on success.
     *
     * @since   2.5
     * @throws  Exception
     */
    protected function setup(): bool
    {
        // Initialize CLI
        $api = JPATH_ADMINISTRATOR . '/components/com_kunena/api/api.php';

        if (is_file($api)) {
            require_once $api;
        }

        // Check if Kunena has been installed.
        if (!class_exists('Kunena\Forum\Libraries\Forum\KunenaForum') || !KunenaForum::isCompatible('7.1') || !KunenaForum::installed()) {
            return \false;
        }

        KunenaForum::setup();

        return \true;
    }

    /**
     * Method to get the number of content items available to index.
     *
     * @return   integer  The number of content items available to index.
     *
     * @since   2.5
     * @throws  Exception on database error.
     */
    protected function getContentCount(): int
    {
        Log::add('FinderIndexerAdapter::getContentCount', Log::INFO);

        // Get the list query.
        $sql = $this->db->createQuery();
        $sql->select('COUNT(*)')->from('#__kunena_messages');

        // Get the total number of content items to index.
        $this->db->setQuery($sql);

        $return = 0;

        try {
            $return = (int) $this->db->loadResult();
        } catch (Exception $e) {
            KunenaError::displayDatabaseError($e);
        }

        return (int) $return;
    }

    /**
     * Method to get a content item to index.
     *
     * @param   integer  $id  The id of the content item.
     *
     * @return  Result  A FinderIndexerResult object.
     *
     * @since   2.5
     * @throws  Exception on database error.
     */
    protected function getItem($id): Result
    {
        Log::add('FinderIndexerAdapter::getItem', Log::INFO);

        $message = KunenaMessageHelper::get($id);

        // Convert the item to a result object.
        $item = $this->createIndexerResult($message);
        unset($message);

        // Why should we cleanup here? Maybe we need the instances later on?!
        // \Kunena\Forum\Libraries\Forum\Message\Helper::cleanup();

        return $item;
    }

    /**
     * Method to get a list of content items to index.
     *
     * @param   integer         $offset  The id of the last message of the batch before (onBuildIndex() walks the message ids).
     * @param   integer         $limit   The list limit.
     * @param   QueryInterface  $sql     A QueryInterface object. [optional]
     *
     * @return  array  An array of Result objects.
     *
     * @since   2.5
     * @throws  Exception on database error.
     */
    protected function getItems($offset, $limit, $sql = \null): array
    {
        Log::add("FinderIndexerAdapter::getItems({$offset}, {$limit})", Log::INFO);

        // Get the list query: the messages after the last one of the batch before, by id.
        $sql = $this->db->createQuery();
        $sql->select('id')->from('#__kunena_messages')->where('id > ' . (int) $offset)->order('id ASC')->setLimit($limit);

        // Get the content items to index.
        $this->db->setQuery($sql);

        $ids = [];

        try {
            $ids = $this->db->loadColumn();
        } catch (Exception $e) {
            KunenaError::displayDatabaseError($e);
        }

        // The ids which the batch read (onBuildIndex() moves its cursor past them and knows the last batch by their number).
        $this->batchIds = array_map('intval', (array) $ids);

        // Convert the items to result objects.
        $messages = KunenaMessageHelper::getMessages($ids, 'none');
        $items    = [];

        foreach ($messages as &$message) {
            $items[] = $this->createIndexerResult($message);
        }

        KunenaMessageHelper::cleanup();
        KunenaRoute::cleanup();

        return $items;
    }

    /**
     * Method to create the results with Joomla! indexer
     *
     * @param   KunenaMessage $message  The KunenaMessage object
     *
     * @return  Result
     * @since   Kunena 6.0
     * @throws  Exception
     */
    protected function createIndexerResult($message): Result
    {
        // Convert the item to a result object.
        $item        = new Result();
        $item->id    = $message->id;
        $item->catid = $message->catid;

        // Set title context.
        // Replies within a thread do not have their own subject (only the
        // original post in the thread has one) — if the subject is empty, we use the subject of the entire
        // discussion (thread) so that the indexed record always has a title.
        $subject = $message->subject;

        if ($subject === \null || trim((string) $subject) === '') {
            $subject = $this->getThreadSubject($message->thread);
        }

        $item->title = $subject;

        // Build the necessary url, route, path and alias information.
        // The url is the key of the item in the index: the one of the adapter, which remove() looks for when a message is deleted.
        // The route opens the message; it is not routed here: made from the console (the indexer of the scheduler) a routed
        // address carries the placeholder of the console for the root of the site.
        // The language of the item: the one of its category, else the one of its topic. Every post was "all languages", so a post
        // of a French category was in the results of the English pages. An item of one language gets no menu item of the language
        // of the indexer (the router takes the one of the page): the link of the menu item of another language was a 404.
        $category = $message->getCategory();
        $language = $category->language && $category->language !== '*' ? (string) $category->language : (string) ($message->getTopic()->language ?: '*');
        $itemid   = $language === '*' ? KunenaRoute::fixMissingItemID() : 0;

        $item->url   = $this->getUrl($message->id, $this->extension, $this->layout);
        $item->route = 'index.php?option=com_kunena&view=topic&catid=' . (int) $message->catid . '&id=' . (int) $message->thread
            . '&mesid=' . (int) $message->id . ($itemid ? '&Itemid=' . (int) $itemid : '');
        $item->alias = KunenaRoute::stringURLSafe($subject);

        // Set body context.
        // The text for some readers only ([hide], [confidential]) is not for the index of the site: the search shows the text.
        $text          = KunenaParser::removeBlocks((string) $message->message, ['confidential', 'hide']);
        $item->body    = KunenaParser::stripBBCode($text);
        $item->summary = $item->body;

        // Set other information.
        // The indexer of Joomla stores only the state (not the published value of the result) and the search shows the items of
        // state 1: a message which waits for approval or is deleted is not in the results, nor are the messages of a category
        // where the users see only their own topics (a helpdesk: the access level of the category would show them to every user).
        $visible         = $message->hold == 0 && $category->published == 1 && !$category->params->get('ownTopicsOnly');

        // A category in an unpublished section or category is hidden with it.
        for ($parent = $category->getParent(), $depth = 0; $visible && $parent->exists() && $depth < 100; $parent = $parent->getParent(), $depth++) {
            $visible = $parent->published == 1;
        }

        $item->published = \intval($message->hold == 0);
        $item->state     = \intval($visible);
        $item->language  = $language;

        // The view level which only the users who may read the category have (getAccessLevel()).
        $item->access = $this->getAccessLevel($item->catid);

        // Set the item type.
        $item->type_id = $this->type_id;

        // Set the mime type.
        $item->mime = $this->mime;

        // Set the item layout.
        $item->layout = $this->layout;

        return $item;
    }

    /**
     * Method to retrieve the subject of the head message of a thread.
     * Used as a fallback when a reply message has no own subject.
     *
     * @param   int  $threadId  The id of the thread (head message id).
     *
     * @return  string
     * @since   Kunena 6.0
     */
    protected function getThreadSubject($threadId): string
    {
        static $cache = [];

        $threadId = (int) $threadId;

        if (\array_key_exists($threadId, $cache)) {
            return $cache[$threadId];
        }

        $db    = $this->getDatabase();
        $query = $db->createQuery();
        $query->select('subject')
            ->from('#__kunena_messages')
            ->where('id = ' . $threadId);
        $db->setQuery($query);

        try {
            $subject = (string) $db->loadResult();
        } catch (Exception $e) {
            $subject = '';
        }

        $cache[$threadId] = $subject;

        return $subject;
    }

    /**
     * Method to translate the native content states into states that the
     * indexer can use.
     *
     * @param   integer   $item      The item state.
     * @param   ?integer  $category  The category state. [optional]
     *
     * @return  integer  The translated indexer state.
     *
     * @since   2.5
     */
    protected function translateState($item, $category = \null): int
    {
        // If category is present, factor in its states as well
        if ($category !== \null) {
            if ($category != 1) {
                $item = 0;
            }
        }

        // Translate the state
        return \intval($item == 1);
    }

    /**
     * Method to retrieve the messages under the category given
     *
     * @param   int $cat_id The id of the category
     *
     * @return  mixed
     * @since   Kunena 6.0
     * @throws  Exception
     */
    protected function getMessagesByCategory($cat_id)
    {
        static $messages = [];

        // empty(): the first call of a request has no entry yet ("Undefined array key" with every deletion).
        if (empty($messages[$cat_id])) {
            $db    = $this->getDatabase();
            $query = $db->createQuery();
            $query->select('m.id');
            $query->from('#__kunena_messages as m');
            $query->join('INNER', '#__kunena_categories as c on m.catid = c.id');
            $query->where('c.id = ' . $db->quote($cat_id));
            $db->setQuery($query);
            $ids               = $db->loadColumn();
            $messages[$cat_id] = KunenaMessageHelper::getMessages($ids);
        }

        return $messages[$cat_id];
    }

    /**
     * Method to retrieve the messages under the topic given
     *
     * @param   int $topic_id The id of the topic
     *
     * @return  mixed
     * @since   Kunena 6.0
     * @throws  Exception
     */
    protected function getMessagesByTopic($topic_id)
    {
        static $messages = [];

        if (empty($messages[$topic_id])) {
            $db    = $this->getDatabase();
            $query = $db->createQuery();
            $query->select('m.*, t.message');
            $query->from('#__kunena_messages AS m');
            $query->join('INNER', '#__kunena_messages_text as t ON m.id = t.mesid');
            $query->where('m.thread = ' . $db->quote($topic_id));
            $db->setQuery($query);
            $results = $db->loadAssocList();
            $list    = [];

            foreach ($results as $result) {
                $list[] = new KunenaMessage($result);
            }

            $messages[$topic_id] = $list;
        }

        return $messages[$topic_id];
    }

    /**
     * What the index takes from a category (createIndexerResult(), getAccessLevel()).
     *
     * @param   TableKunenaCategories  $table  The table of the category.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    protected function categoryIndexState(TableKunenaCategories $table): array
    {
        $params = new Registry($table->params);

        // Also what getAccessLevel() takes of a category of groups (the child groups, the second group), and the parent (createIndexerResult()
        // hides the messages of a category under an unpublished parent): a change of them left the old access or state in the index.
        return [
            'accesstype'    => (string) $table->accesstype,
            'access'        => (int) $table->access,
            'pubAccess'     => (int) $table->pubAccess,
            'pubRecurse'    => (int) $table->pubRecurse,
            'adminAccess'   => (int) $table->adminAccess,
            'adminRecurse'  => (int) $table->adminRecurse,
            'published'     => (int) $table->published,
            'parentid'      => (int) $table->parentid,
            'language'      => (string) $table->language,
            'ownTopicsOnly' => (int) $params->get('ownTopicsOnly', 0),
        ];
    }

    /**
     * The id of a category and of all the categories under it.
     *
     * @param   int  $catid  The id of the category.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    protected function getCategoryTree(int $catid): array
    {
        $db      = $this->getDatabase();
        $tree    = [$catid => $catid];
        $parents = [$catid];

        // One level at a time (the depth is limited as in createIndexerResult()).
        for ($depth = 0; $parents && $depth < 100; $depth++) {
            $query = $db->createQuery()
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__kunena_categories'))
                ->whereIn($db->quoteName('parentid'), $parents);
            $parents = array_diff(array_map('intval', (array) $db->setQuery($query)->loadColumn()), $tree);

            foreach ($parents as $id) {
                $tree[$id] = $id;
            }
        }

        return array_values($tree);
    }

    /**
     * The ids of the messages of categories.
     *
     * @param   int[]  $catids  The ids of the categories.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    protected function getMessageIdsByCategories(array $catids): array
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__kunena_messages'))
            ->whereIn($db->quoteName('catid'), array_map('intval', $catids))
            ->order($db->quoteName('id') . ' ASC');

        return array_map('intval', (array) $db->setQuery($query)->loadColumn());
    }

    /**
     * The ids of the messages of a topic, read now (not cached: the messages of a topic change while it is saved).
     *
     * @param   int  $topicId  The id of the topic.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    protected function getMessageIdsByTopic(int $topicId): array
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('thread') . ' = ' . $topicId)
            ->order($db->quoteName('id') . ' ASC');

        return array_map('intval', (array) $db->setQuery($query)->loadColumn());
    }

    /**
     * Method to retrieve the access level of the category
     *
     * @param   int $catid The id of the category
     *
     * @return  integer
     * @since   Kunena 6.0
     * @throws  Exception
     */
    protected function getAccessLevel($catid): int
    {
        $category = KunenaCategoryHelper::get($catid);

        // Smart Search needs the id of a view level; the view levels of the site are read once (id => groups).
        if ($this->viewLevels === \null) {
            $db   = $this->getDatabase();
            $rows = $db->setQuery($db->createQuery()->select($db->quoteName(['id', 'rules']))->from($db->quoteName('#__viewlevels')))->loadObjectList();

            $this->viewLevels = [];

            foreach ($rows as $row) {
                $groups = json_decode((string) $row->rules, true);

                $this->viewLevels[(int) $row->id] = \is_array($groups) ? $groups : [];
            }
        }

        // The user groups of the site, for a category of a user group: their tree, and the groups of the administrators, who read
        // every category.
        if ($category->accesstype === 'joomla.group' && $this->userGroups === \null) {
            $db   = $this->getDatabase();
            $rows = $db->setQuery($db->createQuery()->select($db->quoteName(['id', 'parent_id']))->from($db->quoteName('#__usergroups')))->loadObjectList();

            $this->userGroups = ['parents' => [], 'always' => []];

            foreach ($rows as $row) {
                $this->userGroups['parents'][(int) $row->id] = (int) $row->parent_id;

                if (Access::checkGroup((int) $row->id, 'core.admin')) {
                    $this->userGroups['always'][] = (int) $row->id;
                }
            }
        }

        // A category of a view level has the level of the category (0 is a level which nobody has: Public, as before); a category of
        // a user group a level which only its readers have (KunenaFinderAccess::groupLevel()), else nobody.
        return KunenaFinderAccess::viewLevel(
            (string) $category->accesstype,
            (int) $category->access ?: 1,
            (int) $category->pubAccess,
            $this->viewLevels,
            ($this->userGroups ?? []) + [
                'pubRecurse'   => (bool) $category->pubRecurse,
                'adminAccess'  => (int) $category->adminAccess,
                'adminRecurse' => (bool) $category->adminRecurse,
            ]
        );
    }
}
