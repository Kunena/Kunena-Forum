<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Item;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Event\KunenaPrepareEvent;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageFinder;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageBookmarks;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHistory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageReactions;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\KunenaPrivate\Message\KunenaPrivateMessageFinder;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Layout\Widget\WidgetModule;
use RuntimeException;

/**
 * Class ComponentTopicControllerItemDisplay
 *
 * @since   Kunena 4.0
 */
class TopicItemDisplay extends KunenaControllerDisplay
{
    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $me = null;

    /**
     * @var     KunenaCategory
     * @since   Kunena 6.0
     */
    public mixed $category = null;

    /**
     * @var     KunenaTopic
     * @since   Kunena 6.0
     */
    public mixed $topic = null;

    /**
     * @var     KunenaPagination
     * @since   Kunena 6.0
     */
    public mixed $pagination = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $headerText = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'Topic/Item';

    public mixed $allowed = null;

    public mixed $cache = null;

    public mixed $catParams = null;

    public mixed $categorylist = null;

    public $message;

    public mixed $messages = null;

    /**
     * The first message of the topic when it is shown on top of a page which does not have it (the template option
     * "Show the first post on every page"), else null.
     *
     * @var     KunenaMessage|null
     * @since   Kunena 8.0
     */
    public mixed $stickyFirst = null;

    public mixed $threaded = null;

    public mixed $userTopic = null;

    public mixed $quickReply = null;

    public mixed $image = null;

    public mixed $params = null;

    public mixed $pmHasBody = null;

    /**
     * Prepare topic display.
     *
     * @return  mixed
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $catid  = $this->input->getInt('catid');
        $id     = $this->input->getInt('id');
        $mesid  = $this->input->getInt('mesid');
        $start  = $this->input->getInt('limitstart', 0);
        $limit  = $this->input->getInt('limit', 0);
        $Itemid = $this->input->getInt('Itemid');
        $format = $this->input->getInt('format');

        if (!$Itemid && $format != 'feed' && $this->config->sefRedirect) {
            try {
                $itemid = KunenaRoute::fixMissingItemID();

                $params = [
                    'option' => 'com_kunena',
                    'view'   => 'topic',
                    'catid'  => $catid,
                    'id'     => $id,
                    'Itemid' => $itemid,
                ];

                return $this->app->redirect(KunenaRoute::_('index.php?' . http_build_query(KunenaRoute::keepQuery($params)), false), 301);
            } catch (Exception $e) {
                throw new RuntimeException('Failed to create controller: ' . $e->getMessage());
            }
        }

        if ($limit < 1 || $limit > 100) {
            $limit = $this->config->messagesPerPage;
        }

        // A topic of another forum of the site goes to that forum.
        KunenaCategoryHelper::redirectToForum((int) $catid, 'index.php?option=com_kunena&view=topic&catid=' . (int) $catid . '&id=' . (int) $id . ($mesid ? '&mesid=' . $mesid : ''));

        $this->me = KunenaUserHelper::getMyself();

        $this->allowed = md5(serialize(KunenaAccess::getInstance()->getAllowedCategories()));
        $options       = ['defaultgroup' => 'com_kunena'];
        $this->cache   = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController('output', $options);

        /*
        if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template'))
         {
         return;
         }*/

        $options    = [];
        $options[]  = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FORUM_TOP'));

        // The forum jump: the sections and the categories which the user may read.
        $this->catParams    = ['sections' => 1, 'catid' => 0];
        $this->categorylist = HTMLHelper::_(
            'kunenaforum.categorylist',
            'catid',
            0,
            $options,
            $this->catParams,
            'class="form-select" data-bs-toggle="tooltip" title="' . Text::_('COM_KUNENA_FORUM_TOP') . '" size="1" aria-label="' . Text::_('COM_KUNENA_CATEGORY') . '"',
            'value',
            'text'
        );

        // Load topic and message.
        if ($mesid) {
            // If message was set, use it to find the current topic.
            $this->message = KunenaMessageHelper::get($mesid);
            $this->topic   = $this->message->getTopic();
        } else {
            // Note that redirect loops throw RuntimeException because of we added \Kunena\Forum\Libraries\Forum\Topic\Topic::getTopic() call!
            $this->topic   = KunenaTopicHelper::get($id)->getTopic();

            // The first and the last post are both needed: load them with one query.
            KunenaMessageHelper::getMessages([$this->topic->first_post_id, $this->topic->last_post_id]);
            $this->message = KunenaMessageHelper::get($this->topic->first_post_id);
        }

        // Load also category (prefer the URI variable if available).
        if ($catid && $catid != $this->topic->category_id) {
            $this->category = KunenaCategoryHelper::get($catid);
            $this->category->tryAuthorise();
        } else {
            $this->category = $this->topic->getCategory();
        }

        // Access check.
        $this->message->tryAuthorise();

        // Check if we need to redirect (category or topic mismatch, or resolve permanent URL).
        if ($this->primary) {
            $channels = $this->category->getChannels();

            // Also when the topic of the address is not the topic of the message (the message was split into another topic since):
            // the other topic was shown under the address of the old one, and the links of its pages went to the old topic.
            if (
                $this->message->thread != $this->topic->id
                || ($mesid && $id && $id != $this->topic->id)
                || ($this->topic->category_id != $this->category->id && !isset($channels[$this->topic->category_id]))
            ) {
                $this->app->redirect($this->message->getUrl(null, false));
            }
        }

        // Load messages from the current page and set the pagination.
        // The states which the user may see in the category of the topic: the category of the address may be one which shows
        // the topic as a channel, and its moderator got the unapproved replies in the count (pages without messages).
        $topicCategory = $this->topic->getCategory();
        $hold          = KunenaAccess::getInstance()->getAllowedHold($this->me, $topicCategory->id, false);
        $finder        = new KunenaMessageFinder();
        $finder->where('thread', '=', $this->topic->id);

        // A user sees his own messages which wait for approval, as the rights of a message allow (a reply held for review, a scheduled
        // reply with its notice): only the author of a topic which waits for approval saw them, the author of a reply in a published
        // topic was sent to the topic and did not find his reply, nor its button to edit it.
        if ($this->me->exists() && (int) $this->me->userid > 0 && !$this->me->isModerator($topicCategory)) {
            $finder->filterByHoldOrOwnUnapproved($hold, $this->me);
        } else {
            $finder->filterByHold($hold);
        }

        // The page of the message starts at its page: the place of the message was the start, so a page began with it (posts 13 to
        // 22 of a page of 10), the posts before it on its page were seen nowhere, and the pagination called it another page.
        if ($mesid) {
            $start = $limit > 0 ? intdiv($this->topic->getPostLocation($mesid), $limit) * $limit : 0;
        }
        $this->pagination = new KunenaPagination($finder->count(), $start, $limit);
        $this->pagination->setUri($this->getPaginationUri());

        // The messages of the same second in the order of their ids: without it the database chose their order for each page, and
        // at the end of a page one of them was shown twice and the other on no page.
        $direction      = $this->me->getMessageOrdering() == 'asc' ? 1 : -1;
        $this->messages = $finder
            ->order('time', $direction)
            ->order('id', $direction)
            ->start($this->pagination->limitstart)
            ->limit($this->pagination->limit)
            ->find();

        // The links of the messages and the number of replies need the position of these messages: load them together.
        KunenaMessageHelper::getMessages([$this->topic->last_post_id]);
        KunenaMessageHelper::loadLocation(array_merge(array_keys($this->messages), [(int) $this->topic->last_post_id]));

        $this->prepareMessages($mesid);
        $doc = $this->app->getDocument();

        if ($this->me->exists()) {
            $pmFinder = new KunenaPrivateMessageFinder();
            $pmFinder->filterByMessageIds(array_keys($this->messages))->order('id');

            // The moderators of the category of the topic: the category of the address can be one of which it is a channel.
            if (!$this->me->isModerator($this->topic->getCategory())) {
                $pmFinder->filterByUser($this->me);
            }

            $pms = $pmFinder->find();

            foreach ($pms as $pm) {
                $registry = new Registry($pm->params);
                $posts    = $registry->get('receivers.posts');

                foreach ((array) $posts as $post) {
                    // The private message may be attached to a post on another page.
                    if (!isset($this->messages[$post])) {
                        continue;
                    }

                    if (!isset($this->messages[$post]->pm)) {
                        $this->messages[$post]->pm = [];
                    }

                    $this->messages[$post]->pm[$pm->id] = $pm;
                }
            }
        }

        if ($this->topic->unread) {
            $doc->setMetaData('robots', 'noindex, follow');
        }

        // Run events.
        $params = new Registry();
        $params->set('ksource', 'kunena');
        $params->set('kunena_view', 'topic');
        $params->set('kunena_layout', 'default');

        PluginHelper::importPlugin('kunena');
        // The Joomla content plugins of the target topic_top (the system plugin of Kunena) run on the modules at the top of the topic (kunena_topictitle).
        WidgetModule::prepareWith('kunena_topictitle', 'topic_top');
        WidgetModule::prepareWith('kunena_poll', 'topic_poll');

        // Between the messages: kunena_msg_<n> (and kunena_msg_row_<n> of the layout sets of Agora).
        WidgetModule::prepareWith('kunena_msg_*', 'topic_messages');

        // In the profile of the author of a message: kunena_topicprofile(more), kunena_profile_default(_<rank or user>),
        // kunena_profile_horizontal(_<rank or user>).
        WidgetModule::prepareWith('kunena_topicprofile', 'topic_profiles');
        WidgetModule::prepareWith('kunena_topicprofilemore', 'topic_profiles');
        WidgetModule::prepareWith('kunena_profile_*', 'topic_profiles');
        $prepareEvent = new KunenaPrepareEvent('onKunenaPrepare', [
            'context' => 'kunena.topic',
            'subject' => $this->topic,
            'params'  => $params,
            'page'    => 0,
        ]);
        $this->app->getDispatcher()->dispatch('onKunenaPrepare', $prepareEvent);
        foreach ($this->messages as &$message) {
            $prepareEvent = new KunenaPrepareEvent('onKunenaPrepare', [
                'context' => 'kunena.messages',
                'subject' => $message,
                'params'  => $params,
                'page'    => 0,
            ]);
            $this->app->getDispatcher()->dispatch('onKunenaPrepare', $prepareEvent);
        }
        // Get user data, captcha & quick reply.
        $this->userTopic  = $this->topic->getUserTopic();
        $this->quickReply = $this->topic->isAuthorised('reply') && $this->me->exists() && $this->config->quickReply;

        // The subject as text: it is the description of the page when the first post has no text, which Joomla escapes.
        // displayField() escapes it already, and parseText() escaped it again ("Q&A" was "Q&amp;amp;A" in the description).
        $this->headerText = (string) $this->topic->subject;

        $data = $this->getStructuredData(KunenaMessageHelper::get($this->topic->first_post_id));

        KunenaTemplate::getInstance()->addScriptDeclaration(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP), 'application/ld+json');
    }

    /**
     * The structured data (schema.org DiscussionForumPosting) of the topic, for the JSON-LD block in the head of the page.
     * The values are plain text: json_encode() with JSON_HEX_TAG and JSON_HEX_AMP keeps them from closing the script element.
     *
     * @param   KunenaMessage  $first  The first message of the topic.
     *
     * @return  array
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function getStructuredData(KunenaMessage $first): array
    {
        $host = Uri::getInstance()->toString(['scheme', 'host', 'port']);
        $url  = $host . $this->topic->getPermaUrl(null, false);

        $data = [
            '@context'      => 'https://schema.org',
            '@type'         => 'DiscussionForumPosting',
            'url'           => $url,
            'discussionUrl' => $url,
            'headline'      => (string) $this->topic->subject,
        ];

        // The text of [hide], [confidential] and [private] is taken out by stripBBCode() for every viewer.
        $text = trim(preg_replace('/\s+/', ' ', (string) KunenaParser::stripBBCode((string) $first->message, 500, false)));

        if ($text !== '') {
            $data['text'] = $text;
        }

        $data['datePublished'] = $this->topic->getFirstPostTime()->toISO8601();

        if (!empty($first->modified_time)) {
            $data['dateModified'] = (new KunenaDate($first->modified_time))->toISO8601();
        }

        $data['author'] = $this->getStructuredAuthor($this->topic->getFirstPostAuthor());

        $data['interactionStatistic'] = [
            '@type'                => 'InteractionCounter',
            'interactionType'      => 'https://schema.org/CommentAction',
            'userInteractionCount' => (int) $this->topic->getReplies(),
        ];

        $category         = $this->topic->getCategory();
        $data['isPartOf'] = [
            '@type' => 'CollectionPage',
            'name'  => (string) $category->name,
            'url'   => $host . $category->getUrl(null, false),
        ];

        $data['publisher'] = [
            '@type' => 'Organization',
            'name'  => $this->config->boardTitle,
            'url'   => Uri::root(),
        ];

        $data['mainEntityOfPage'] = ['@type' => 'WebPage', '@id' => $url];

        // The image of the topic (an image of its first message), else the avatar of its author or the image of the forum.
        $image = $this->topicImage();

        if ($image) {
            $data['image'] = $image;
        }

        // The replies of this page, as the search engines read a discussion: the replies of the other pages are on their own page.
        $comments = [];

        // A guest reads no reply where the replies are hidden for guests (the teaser): the page shows him a note in their place,
        // and the structured data gave him their text.
        $hideReplies = $this->me && !$this->me->userid && $category->hidesRepliesForGuests();

        foreach ($this->messages ?: [] as $message) {
            // Not the first message (it is the posting itself), nor a deleted or unapproved message which a moderator sees.
            if ((int) $message->id === (int) $this->topic->first_post_id || (int) $message->hold !== 0) {
                continue;
            }

            $comment = [
                '@type'         => 'Comment',
                'url'           => $this->absoluteUrl($message->getPermaUrl(null, false)),
                'datePublished' => $message->getTime()->toISO8601(),
                'author'        => $this->getStructuredAuthor($message->getAuthor()),
            ];

            if (!empty($message->modified_time)) {
                $comment['dateModified'] = (new KunenaDate($message->modified_time))->toISO8601();
            }

            $text = $hideReplies ? '' : trim(preg_replace('/\s+/', ' ', (string) KunenaParser::stripBBCode((string) $message->message, 500, false)));

            if ($text !== '') {
                $comment['text'] = $text;
            }

            $comments[] = $comment;
        }

        if ($comments) {
            $data['comment'] = $comments;
        }

        return $data;
    }

    /**
     * The author of a message for the structured data: the name which the forum shows (the username is the name with which the user
     * logs in, and the forum may keep it for itself) and the address of the profile, which a guest has not, nor a profile which the
     * viewer may not see.
     *
     * @param   KunenaUser  $author  The author.
     *
     * @return  array
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function getStructuredAuthor(KunenaUser $author): array
    {
        $person  = ['@type' => 'Person', 'name' => $author->getName('', false)];
        $profile = $author->getURL(false);

        if ($profile) {
            $person['url'] = $this->absoluteUrl($profile);
        }

        return $person;
    }

    /**
     * An address with the scheme and the host of the site: the routes of Joomla are relative.
     *
     * @param   string  $url  An address, relative or absolute.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected function absoluteUrl(string $url): string
    {
        return preg_match('#^https?://#i', $url) ? $url : Uri::getInstance()->toString(['scheme', 'host', 'port']) . $url;
    }

    /**
     * The image of the topic, for Open Graph, Twitter and the structured data: the first image attached to the first message which
     * everybody may see, else the first [img] of that message outside its hidden parts, else the avatar of its author or the image
     * of the forum.
     *
     * @return  string  An absolute address.
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function topicImage(): string
    {
        if ($this->topic->attachments > 0) {
            foreach (KunenaAttachmentHelper::getByMessage($this->topic->first_post_id) as $attach) {
                // A protected file (private, or for some groups) is no image for the visitors of a shared link.
                if (!$attach->isImage() || (int) $attach->protected > KunenaAttachment::PROTECTION_PUBLIC
                    || !is_file(JPATH_SITE . '/' . $attach->folder . '/' . $attach->filename)) {
                    continue;
                }

                return $this->absoluteUrl($attach->getUrl(false, true, false));
            }
        }

        // An image of a hidden part of the message is no image of the page: every visitor and the search engines read the head.
        // The first image: the address ends at its own [/img] (".*" took everything up to the last [/img] of the message).
        $message = KunenaParser::removeBlocks((string) $this->topic->first_post_message, ['confidential', 'hide', 'private']);

        if (preg_match('#\[img(?:[\s=][^\]]*)?\]\s*(https?://[^\s\[\]"<>]+)\s*\[/img\]#iu', $message, $match)) {
            return $match[1];
        }

        return (string) $this->docImage();
    }

    /**
     * Prepare messages for display.
     *
     * @param   int  $mesid  Selected message Id.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function prepareMessages($mesid)
    {
        // The first message of the topic, when it is shown on top of the page: it is loaded with the messages of the page.
        $first = $this->getStickyFirst();
        $batch = $first ? $this->messages + [(int) $first->id => $first] : $this->messages;

        // Get thank yous for all messages in the page
        $thankyous = KunenaMessageThankyouHelper::getByMessage($batch);

        // The reactions of all the messages of the page, and the ones of the user, in two queries.
        KunenaMessageReactions::load(array_map(static fn ($message): int => (int) $message->id, $batch), (int) $this->me->userid);

        // The bookmarks of the user on the messages of the page, in one query.
        KunenaMessageBookmarks::load(array_map(static fn ($message): int => (int) $message->id, $batch), (int) $this->me->userid);

        // The numbers of the earlier versions of the messages of the page (the link to the edit history), in one query.
        if ((int) ($this->config->editHistory ?? KunenaMessageHistory::MODERATORS) !== KunenaMessageHistory::OFF) {
            KunenaMessageHistory::load(array_map(static fn ($message): int => (int) $message->id, $batch));
        }

        // First collect ids and users.
        $layout         = $this->me->getTopicLayout($this->layout);
        $threaded       = ($layout == 'indented' || $layout == 'threaded');
        $userlist       = [];
        $this->threaded = [];
        $location       = $this->pagination->limitstart;

        foreach ($this->messages as $message) {
            $message->replynum = ++$location;

            if ($threaded) {
                // Threaded ordering
                if (isset($this->messages[$message->parent])) {
                    $this->threaded[$message->parent][] = $message->id;
                } else {
                    $this->threaded[0][] = $message->id;
                }
            }

            $userlist[(int) $message->userid]      = (int) $message->userid;
            $userlist[(int) $message->modified_by] = (int) $message->modified_by;

            // The users who are mentioned are loaded with the others, not one by one while the message is shown.
            foreach ($message::getMentionedUserIds((string) $message->message) as $mentionedId) {
                $userlist[$mentionedId] = $mentionedId;
            }

            $thankyou_list     = $thankyous[$message->id]->getList();
            $message->thankyou = [];

            if (!empty($thankyou_list)) {
                $message->thankyou = $thankyou_list;
            }
        }

        if (!isset($mesid)) {
            $mesidTmp = '';
        } else {
            $mesidTmp = $mesid;
        }

        if (!isset($this->messages[$mesidTmp]) && !empty($this->messages)) {
            $this->message = reset($this->messages);
        }

        // A page without a message which the user may see has no tree.
        if ($threaded && $this->messages) {
            if (!isset($this->messages[$this->topic->first_post_id])) {
                $this->messages = $this->getThreadedOrdering(0, ['edge']);
            } else {
                $this->messages = $this->getThreadedOrdering();
            }
        }

        if ($first) {
            $firstId                             = (int) $first->id;
            $first->replynum                     = 1;
            $first->thankyou                     = isset($thankyous[$firstId]) ? (array) $thankyous[$firstId]->getList() : [];
            $userlist[(int) $first->userid]      = (int) $first->userid;
            $userlist[(int) $first->modified_by] = (int) $first->modified_by;
        }

        // Prefetch all users/avatars to avoid user by user queries during template iterations
        KunenaUserHelper::loadUsers($userlist);

        // Prefetch attachments.
        KunenaAttachmentHelper::getByMessage($batch);

        // Prefetch the other users of the IP addresses, for the viewers who see the addresses.
        $category = $this->topic->getCategory();

        if ($category->isAuthorised('admin') || ($category->isAuthorised('moderate') && !$this->config->hideIp)) {
            KunenaMessageHelper::getUsersByIp(
                Factory::getContainer()->get(DatabaseInterface::class),
                array_map(static fn ($message) => (string) $message->ip, $batch)
            );
        }

        $this->stickyFirst = $first;
    }

    /**
     * The first message of the topic, to be shown on top of the pages which do not have it themselves.
     *
     * @return  KunenaMessage|null
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function getStickyFirst(): ?KunenaMessage
    {
        $firstId = (int) $this->topic->first_post_id;

        if (!$firstId || !KunenaFactory::getTemplate()->params->get('stickyFirstPost', 0) || isset($this->messages[$firstId])) {
            return null;
        }

        $first = KunenaMessageHelper::get($firstId);

        return $first->exists() && $first->isAuthorised('read') ? $first : null;
    }

    /**
     * The address of the page links: the current address without the message, which would open the page of that message again.
     *
     * @return  Uri
     *
     * @since   Kunena 8.0
     */
    protected function getPaginationUri(): Uri
    {
        $uri = clone KunenaRoute::$current;
        $uri->delVar('mesid');

        return $uri;
    }

    /**
     * Change ordering of the displayed messages and apply threading.
     *
     * @param   int    $parent  Parent Id.
     * @param   array  $indent  Indent for the current object.
     *
     * @return  array
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function getThreadedOrdering($parent = 0, $indent = [])
    {
        $list = [];

        if (empty($this->threaded[$parent])) {
            return $list;
        }

        if (\count($indent) == 1 && $this->topic->getTotal() > $this->pagination->limitstart + $this->pagination->limit) {
            $last = -1;
        } else {
            $last = end($this->threaded[$parent]);
        }

        foreach ($this->threaded[$parent] as $mesid) {
            $message = $this->messages[$mesid];
            $skip    = $message->id != $this->topic->first_post_id
                && $message->parent != $this->topic->first_post_id && !isset($this->messages[$message->parent]);

            if ($mesid != $last) {
                // Default sibling edge
                $indent[] = 'crossedge';
            } else {
                // Last sibling edge
                $indent[] = 'lastedge';
            }

            end($indent);
            $key = key($indent);

            if ($skip) {
                $indent[] = 'gap';
            }

            $list[$mesid]         = $this->messages[$mesid];
            $list[$mesid]->indent = $indent;

            if (empty($this->threaded[$mesid])) {
                // No children node: the first post of the topic alone, or a reply without replies.
                $list[$mesid]->indent[] = ($mesid == $this->topic->first_post_id) ? 'single' : 'leaf';
            } else {
                // Has children node: the first post of the topic, or a reply with replies.
                $list[$mesid]->indent[] = ($mesid == $this->topic->first_post_id) ? 'root' : 'node';
            }

            if (!empty($this->threaded[$mesid])) {
                // Fix edges
                if ($mesid != $last) {
                    $indent[$key] = 'edge';
                } else {
                    $indent[$key] = 'empty';
                }

                if ($skip) {
                    $indent[$key + 1] = 'empty';
                }

                $list += $this->getThreadedOrdering($mesid, $indent);
            }

            if ($skip) {
                array_pop($indent);
            }

            array_pop($indent);
        }

        return $list;
    }

    /**
     * Prepare document.
     *
     * @return  string
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function docImage()
    {
        if (is_file(JPATH_SITE . '/media/kunena/avatars/' . KunenaFactory::getUser($this->topic->getAuthor()->id)->avatar)) {
            $image = Uri::root() . 'media/kunena/avatars/' . KunenaFactory::getUser($this->topic->getAuthor()->id)->avatar;
        } elseif ($this->topic->getAuthor()->avatar == null) {
            if (is_file(JPATH_SITE . '/' . $this->config->emailHeader)) {
                $image = Uri::base() . $this->config->emailHeader;
            } else {
                $image = Uri::base() . '/media/kunena/email/hero-wide.png';
            }
        } else {
            $image = $this->topic->getAuthor()->getAvatarURL('Profile', '200');
        }

        return $image;
    }

    /**
     * After render update topic data for the user.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function after()
    {
        parent::after();

        $this->topic->hit();

        $this->topic->markRead();

        // Check if subscriptions have been sent and reset the value.
        if ($this->topic->isAuthorised('subscribe') && $this->userTopic->subscribed == 2) {
            $this->userTopic->subscribed = 1;
            $this->userTopic->save();
        }
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {
        $this->image = '';
        $doc         = $this->app->getDocument();
        $this->setMetaData('og:url', Uri::current(), 'property');
        $this->setMetaData('og:type', 'article', 'property');
        // The subject as text: Joomla escapes the meta tags (the HTML of displayField() showed "Q&amp;A" in a share).
        $this->setMetaData('og:title', (string) $this->topic->subject, 'property');
        $this->setMetaData('profile:username', $this->topic->getAuthor()->getName('', false), 'property');

        $image = $this->topicImage();

        $multispaces_replaced = '';
        if (!empty($this->topic->first_post_message)) {
            $firstPostMessage     = KunenaParser::stripBBCode($this->topic->first_post_message, 160, false);
            $multispaces_replaced = preg_replace('/\s+/', ' ', $firstPostMessage);
        }

        $this->setMetaData('og:description', $multispaces_replaced, 'property');
        if (!empty($image)) {
            $this->setMetaData('og:image', $image, 'property');
        }
        $this->setMetaData('article:published_time', $this->topic->getFirstPostTime()->toISO8601(), 'property');
        $this->setMetaData('article:section', $this->topic->getCategory()->name, 'property');
        $this->setMetaData('twitter:card', 'summary', 'name');
        $this->setMetaData('twitter:title', (string) $this->topic->subject, 'name');
        if (!empty($image)) {
            $this->setMetaData('twitter:image', $image, 'name');
        }
        $this->setMetaData('twitter:description', $multispaces_replaced);

        $robots = $this->app->get('robots');

        if ($robots == 'noindex, follow') {
            $this->setMetaData('robots', 'noindex, follow');
        } elseif ($robots == 'index, nofollow') {
            $this->setMetaData('robots', 'index, nofollow');
        } elseif ($robots == 'noindex, nofollow') {
            $this->setMetaData('robots', 'noindex, nofollow');
        } else {
            $this->setMetaData('robots', 'index, follow');
        }

        $page       = (int) $this->pagination->pagesCurrent;
        $total      = (int) $this->pagination->pagesTotal;
        $headerText = $this->headerText . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');

        $pagdata = $this->pagination->getData();

        if ($pagdata->previous->link) {
            $pagdata->previous->link = str_replace('?limitstart=0', '', $pagdata->previous->link);
            $doc->addHeadLink($pagdata->previous->link, 'prev');
        }

        if ($pagdata->next->link) {
            $doc->addHeadLink($pagdata->next->link, 'next');
        }

        if ($page > 1) {
            $canonicalUri = $this->getCanonicalUri(['option', 'view', 'catid', 'id', 'Itemid'], ($page - 1) * $this->pagination->limit);
            $this->setCanonicalLink($doc, KunenaRoute::_($canonicalUri));
        } else {
            $this->setCanonicalLink($doc, trim(strtok($this->topic->getUrl(), '?')));
        }

        $menu_item = $this->app->getMenu()->getActive();

        if ($menu_item) {
            $this->params = $menu_item->getParams();
            $subject      = $this->topic->subject;

            // Each page of a long topic its own title (as the pages of a category): "Subject - Pages 2".
            $this->setTitle($subject . ($total > 1 && $page > 1 ? ' - ' . Text::_('COM_KUNENA_PAGES') . " {$page}" : ''));

            $multispaces_replaced_desc = preg_replace('/\s+/', ' ', $this->topic->first_post_message);

            if ($total > 1 && $page > 1) {
                $small = KunenaParser::stripBBCode($multispaces_replaced_desc, 130, false);

                if (empty($small)) {
                    $small = $headerText;
                }

                $this->setDescription($small . " - " . Text::_('COM_KUNENA_PAGES') . " {$page}");
            } else {
                $small = KunenaParser::stripBBCode($multispaces_replaced_desc, 160, false);

                if (empty($small)) {
                    $small = $headerText;
                }

                $this->setDescription($small);
            }
        }
    }
}
