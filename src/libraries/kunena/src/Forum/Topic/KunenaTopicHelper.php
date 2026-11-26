<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Topic
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\Language\KunenaMultilanguage;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\TopicHelper
 *
 * @since   Kunena 6.0
 */
abstract class KunenaTopicHelper
{
    /**
     * @var     KunenaTopic[]
     * @since   Kunena 6.0
     */
    protected static mixed $_instances = [];

    /**
     * @var     integer[]  The topics to load with the next topics which are loaded (by id).
     * @since   Kunena 8.0
     */
    protected static array $_also = [];

    /**
     * Load these topics with the next topics which are loaded, with one query (a page which loads messages, and with them their
     * topics, and other topics too: the tabs of a profile).
     *
     * @param   int[]  $ids  Topic ids.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function loadAlso(array $ids): void
    {
        foreach ($ids as $id) {
            $id = (int) $id;

            if ($id > 0 && empty(self::$_instances[$id])) {
                self::$_also[$id] = $id;
            }
        }
    }

    /**
     * Free up memory by cleaning up all cached items.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public static function cleanup(): void
    {
        self::$_instances = [];
    }

    /**
     * Method to delete selected topics.
     *
     * @param   array|int  $ids  ids
     *
     * @return  integer Count of deleted topics.
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function delete($ids)
    {
        if (empty($ids)) {
            return 0;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        if (\is_array($ids)) {
            // For array of IDs, we need to create a proper IN clause
            $placeholders = [];
            foreach ($ids as $id) {
                $placeholders[] = (int) $id;
            }
            $idlist = implode(',', $placeholders);
        } else {
            $idlist = (int) $ids;
        }

        $queries = [];

        // Delete user topics
        $query = $db->createQuery();
        $query->delete('#__kunena_user_topics')
              ->where('topic_id IN (' . $idlist . ')');
        $queries [] = $query;

        // Delete user read
        $query = $db->createQuery();
        $query->delete('#__kunena_user_read')
              ->where('topic_id IN (' . $idlist . ')');
        $queries [] = $query;

        // Delete thank yous
        $messageIds = $db->createQuery();
        $messageIds->select($db->quoteName('mm.id'))
            ->from($db->quoteName('#__kunena_messages', 'mm'))
            ->where($db->quoteName('mm.thread') . ' IN (' . $idlist . ')');

        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_thankyou'))
              ->where($db->quoteName('postid') . ' IN (' . $messageIds . ')');
        $queries [] = $query;

        // Delete reactions
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_reactions'))
              ->where($db->quoteName('message_id') . ' IN (' . $messageIds . ')');
        $queries [] = $query;

        // Delete the edit history
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_messages_history'))
              ->where($db->quoteName('message_id') . ' IN (' . $messageIds . ')');
        $queries [] = $query;

        // Delete the posts collected for the digests
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_digest'))
              ->where($db->quoteName('message_id') . ' IN (' . $messageIds . ')');
        $queries [] = $query;

        // Delete the bookmarks
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_bookmarks'))
              ->where($db->quoteName('message_id') . ' IN (' . $messageIds . ')');
        $queries [] = $query;

        // Delete poll users, poll options and polls (if not shadow)
        foreach ([['#__kunena_polls_users', 'pollid'], ['#__kunena_polls_options', 'pollid'], ['#__kunena_polls', 'id']] as [$table, $column]) {
            $pollIds = $db->createQuery();
            $pollIds->select($db->quoteName('tt.poll_id'))
                    ->from($db->quoteName('#__kunena_topics', 'tt'))
                    ->where($db->quoteName('tt.id') . ' IN (' . $idlist . ')')
                    ->where($db->quoteName('tt.moved_id') . ' = 0');

            $query = $db->createQuery();
            $query->delete($db->quoteName($table))
                  ->where($db->quoteName($column) . ' IN (' . $pollIds . ')');
            $queries [] = $query;
        }

        // Delete message texts
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_messages_text'))
              ->where($db->quoteName('mesid') . ' IN (' . $messageIds . ')');
        $queries [] = $query;

        // Delete messages
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_messages'))
              ->where($db->quoteName('thread') . ' IN (' . $idlist . ')');
        $queries [] = $query;

        // The ratings of the topics and their Joomla tags (as KunenaTopic::delete() does): the rows stayed for topics which are gone.
        $query = $db->createQuery();
        $query->delete($db->quoteName('#__kunena_rate'))
              ->where($db->quoteName('topic_id') . ' IN (' . $idlist . ')');
        $queries [] = $query;

        $query = $db->createQuery();
        $query->delete($db->quoteName('#__contentitem_tag_map'))
              ->where($db->quoteName('type_alias') . ' = ' . $db->quote(KunenaTopicTags::TYPE_ALIAS))
              ->where($db->quoteName('content_item_id') . ' IN (' . $idlist . ')');
        $queries [] = $query;

        // Delete topics last, because the previous queries look up data in the topics table
        $query = $db->createQuery();
        $query->delete('#__kunena_topics')
              ->where('id IN (' . $idlist . ')');
        $queries [] = $query;

        // The files of the messages go with them, as in KunenaTopic::delete() (the row, and the file when no other row uses it):
        // they stayed as rows without a message, and as files which the site served without the protection of their message.
        // They are found before the queries delete the messages.
        try {
            $mesids = $db->setQuery(
                $db->createQuery()
                    ->select($db->quoteName('id'))
                    ->from($db->quoteName('#__kunena_messages'))
                    ->where($db->quoteName('thread') . ' IN (' . $idlist . ')')
            )->loadColumn();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            return false;
        }

        if ($mesids) {
            foreach (KunenaAttachmentHelper::getByMessage(array_map('intval', $mesids), 'none') as $attachment) {
                $attachment->delete();
            }
        }

        foreach ($queries as $query) {
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);

                return false;
            }
        }

        return $db->getAffectedRows();
    }

    /**
     * @param   mixed  $ids    ids
     * @param   bool   $value  value
     * @param   mixed  $user   user
     *
     * @return  integer
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function favorite($ids, $value = true, $user = null): int
    {
        // Pre-load all items
        KunenaTopicUserHelper::getTopics($ids, $user);
        $count = 0;

        foreach ($ids as $id) {
            $usertopic = KunenaTopicUserHelper::get($id, $user);

            if ($usertopic->favorite != (int) $value) {
                $count++;
            }

            $usertopic->favorite = (int) $value;
            $usertopic->save();
        }

        return $count;
    }

    /**
     * @param   array  $topics  Topics
     * @param   mixed  $user    User
     *
     * @return  array|boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function fetchNewStatus(array $topics, $user = null)
    {
        $user = KunenaUserHelper::get($user);

        if (!KunenaFactory::getConfig()->showNew || empty($topics) || !$user->exists()) {
            return [];
        }

        $session = KunenaFactory::getSession();

        // The categories of the topics are needed one by one below: load the data of the user for all of them with one query.
        KunenaCategoryUserHelper::getCategories(array_unique(array_map(static fn ($topic) => (int) $topic->category_id, $topics)), $user);

        $ids = [];

        foreach ($topics as $topic) {
            if ($topic->last_post_time < $session->getAllReadTime()) {
                continue;
            }

            $allreadtime = $topic->getCategory()->getUserInfo()->allreadtime;

            if ($allreadtime && $topic->last_post_time < $allreadtime) {
                continue;
            }

            $ids[] = $topic->id;
        }

        if ($ids) {
            $idstr = implode(",", $ids);

            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery();
            $query->select($db->quoteName('m.thread') . ' AS ' . $db->quoteName('id') . ', MIN(' . $db->quoteName('m.id') . ') AS ' . $db->quoteName('lastread') . ', SUM(1) AS ' . $db->quoteName('unread'))
                ->from($db->quoteName('#__kunena_messages', 'm'))
                ->leftJoin($db->quoteName('#__kunena_user_read', 'ur') . ' ON ' . $db->quoteName('ur.topic_id') . ' = ' . $db->quoteName('m.thread') . ' AND ' . $db->quoteName('user_id') . ' = ' . $db->quote($user->userid))
                ->where($db->quoteName('m.hold') . ' = 0')
                ->andWhere($db->quoteName('m.moved') . ' = 0')
                ->andWhere($db->quoteName('m.thread') . ' IN (' . $idstr . ')')
                ->andWhere($db->quoteName('m.time') . ' > ' . $db->quote($session->getAllReadTime()))
                ->andWhere('(' . $db->quoteName('ur.time') . ' IS NULL OR ' . $db->quoteName('m.time') . ' > ' . $db->quoteName('ur.time') . ')')
                ->group($db->quoteName('thread'));
            $db->setQuery($query);

            try {
                $topiclist = (array) $db->loadObjectList('id');
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);

                return false;
            }
        }

        $list = [];

        foreach ($topics as $topic) {
            if (isset($topiclist[$topic->id])) {
                $topic->lastread = $topiclist[$topic->id]->lastread;
                $topic->unread   = $topiclist[$topic->id]->unread;
            } else {
                $topic->lastread = $topic->last_post_id;
                $topic->unread   = 0;
            }

            $list[$topic->id] = $topic->lastread;
        }

        return $list;
    }

    /**
     * Returns \Kunena\Forum\Libraries\Forum\Topic\Topic object.
     *
     * @param   int   $identifier  The topic to load - Can be only an integer.
     * @param   bool  $reload      reload
     *
     * @return  KunenaTopic
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function get($identifier = null, $reload = false)
    {
        if ($identifier instanceof KunenaTopic) {
            return $identifier;
        }

        $id = (int) $identifier;

        if ($id < 1) {
            return new KunenaTopic();
        }

        if (empty(self::$_instances[$id])) {
            $instance = new KunenaTopic();

            // Only load topics which haven't been preloaded before (including missing ones).
            $instance->load(!\array_key_exists($id, self::$_instances) ? $id : null);
            $instance->id          = $id;
            self::$_instances[$id] = $instance;
        } elseif ($reload) {
            self::$_instances[$id]->load();
        }

        return self::$_instances[$id];
    }

    /**
     * The ids of the categories whose topics a list shows: the given categories (with their channels) which the user may
     * read, or with $exclude every category the user may read except the given ones.
     *
     * @param   mixed    $categories  The ids of the categories, false for all.
     * @param   boolean  $exclude     True: the categories are left out of the readable ones.
     * @param   mixed    $reverse     As KunenaCategoryHelper::getCategories().
     *
     * @return  integer[]  Empty when the list shows nothing.
     *
     * @since   Kunena 8.0
     */
    protected static function listedCategoryIds($categories, bool $exclude, $reverse): array
    {
        // No category asked means all the readable ones (the RSS feed with no category set), not none.
        if (!$exclude && !$reverse && !array_filter((array) $categories)) {
            $categories = false;
        }

        if (!$exclude) {
            $categories = KunenaCategoryHelper::getCategories($categories, $reverse);
        } else {
            $categories = KunenaCategoryHelper::getCategories($categories, 0);
        }

        $catlist = [];

        foreach ($categories as $category) {
            $catlist += $category->getChannels();
        }

        if ($exclude) {
            // Every category the user may read except the given ones. A plain NOT IN would also let through the
            // categories which the user is not allowed to read.
            $readable = [];

            foreach (KunenaCategoryHelper::getCategories(false) as $category) {
                $readable += $category->getChannels();
            }

            $catlist = array_diff_key($readable, $catlist);
        }

        return array_map('intval', array_keys($catlist));
    }

    /**
     * @param   mixed  $categories  categories
     * @param   int    $limitstart  limitstart
     * @param   int    $limit       limit
     * @param   array  $params      params
     *
     * @return array
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public static function getLatestTopics($categories = false, $limitstart = 0, $limit = 0, $params = []): array
    {
        KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $config = KunenaFactory::getConfig();

        if ($limit < 1 && empty($params['nolimit'])) {
            $limit = $config->threadsPerPage;
        }

        $reverse   = isset($params['reverse']) ? (int) $params['reverse'] : 0;
        $exclude   = isset($params['exclude']) ? (int) $params['exclude'] : 0;
        $orderby   = isset($params['orderby']) ? (string) $params['orderby'] : 'tt.last_post_time DESC';
        $starttime = isset($params['starttime']) ? (int) $params['starttime'] : 0;
        $user      = isset($params['user']) ? KunenaUserHelper::get($params['user']) : KunenaUserHelper::getMyself();
        $hold      = isset($params['hold']) ? (string) $params['hold'] : 0;
        $moved     = isset($params['moved']) ? (string) $params['moved'] : 0;
        $where     = isset($params['where']) ? (string) $params['where'] : '';

        // On a multilingual site only the topics of the language of the page and of all languages are listed, unless the
        // language of the list is "all" or another language.
        if (KunenaMultilanguage::isEnabled() && ($params['language'] ?? '') !== 'all') {
            $language = isset(KunenaMultilanguage::getLanguages()[$params['language'] ?? '']) ? $params['language'] : KunenaMultilanguage::getCurrent();
            $where .= ' AND ' . $db->quoteName('tt.language') . ' IN (' . $db->quote('*') . ', ' . $db->quote($language) . ')';
        }

        if (str_contains($orderby, 'tt.first_post_time')) {
            $post_time_field = $db->quoteName('tt.first_post_time');
        } else {
            $post_time_field = $db->quoteName('tt.last_post_time');
        }

        $catlist = static::listedCategoryIds($categories, (bool) $exclude, $reverse);

        if (!$catlist) {
            // No readable category: the topics of other categories must not be listed.
            KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

            return [0, []];
        }

        $catlist = 'AND ' . $db->quoteName('tt.category_id') . ' IN (' . implode(',', $catlist) . ')';

        $whereuser = [];

        if (!empty($params['started'])) {
            $whereuser[] = $db->quoteName('ut.owner') . '=1';
        }

        if (!empty($params['replied'])) {
            $whereuser[] = '(' . $db->quoteName('ut.owner') . '=0 AND ' . $db->quoteName('ut.posts') . '>0)';
        }

        if (!empty($params['posted'])) {
            $whereuser[] = $db->quoteName('ut.posts') . '>0';
        }

        if (!empty($params['favorited'])) {
            $whereuser[] = $db->quoteName('ut.favorite') . '=1';
        }

        if (!empty($params['subscribed'])) {
            $whereuser[] = $db->quoteName('ut.subscribed') . '=1';
        }

        // Categories where the users only see their own topics.
        $own = KunenaCategoryHelper::getOwnTopicsOnlyWhere($user, $db->quoteName('tt.category_id'), $db->quoteName('tt.first_post_userid'));

        if ($own) {
            $where .= ' AND ' . $own;
        }

        $wheretime = ($starttime ? " AND {$post_time_field}>{$db->quote($starttime)}" : '');
        $whereuser = ($whereuser ? ' AND ' . $db->quoteName('ut.user_id') . ' = ' . $db->quote($user->userid) . ' AND (' . implode(' OR ', $whereuser) . ')' : '');

        // The exclude option is already applied in the category list above.
        $where = $db->quoteName('tt.hold') . " IN ({$hold}) {$catlist} {$whereuser} {$wheretime} {$where}";

        if (!$moved) {
            $where .= ' AND ' . $db->quoteName('tt.moved_id') . ' =0';
        }

        // Get total count
        if ($whereuser) {
            $query = $db->createQuery();
            $query->select('COUNT(*)')
                ->from($db->quoteName('#__kunena_user_topics', 'ut'))
                ->innerJoin($db->quoteName('#__kunena_topics', 'tt') . ' ON ' . $db->quoteName('tt.id') . ' = ' . $db->quoteName('ut.topic_id'))
                ->where($where);
        } else {
            $query = $db->createQuery();
            $query->select('COUNT(*)')
                ->from($db->quoteName('#__kunena_topics', 'tt'))
                ->where($where);
        }

        $db->setQuery($query);

        try {
            $total = (int) $db->loadResult();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            return [0, []];
        }

        if (!$total) {
            KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

            return [0, []];
        }

        // If out of range, use last page
        if ($limit && $total <= $limitstart) {
            $limitstart = max(0, (int) ceil($total / $limit) - 1) * $limit;
        }

        // Get items
        if ($whereuser) {
            $query = $db->createQuery();
            $query->select($db->quoteName('tt') . '.*, ' . $db->quoteName('ut.posts') . ' AS ' . $db->quoteName('myposts') . ', ' .
                          $db->quoteName('ut.last_post_id') . ' AS ' . $db->quoteName('my_last_post_id') . ', ' .
                          $db->quoteName('ut.favorite') . ', ' . $db->quoteName('tt.last_post_id') . ' AS ' . $db->quoteName('lastread') . ', 0 AS ' . $db->quoteName('unread'))
                ->from($db->quoteName('#__kunena_user_topics', 'ut'))
                ->innerJoin($db->quoteName('#__kunena_topics', 'tt') . ' ON ' . $db->quoteName('tt.id') . ' = ' . $db->quoteName('ut.topic_id'))
                ->where($where)
                ->order($orderby);
        } else {
            $query = $db->createQuery();
            $query->select($db->quoteName('tt') . '.*, ' . $db->quoteName('ut.posts') . ' AS ' . $db->quoteName('myposts') . ', ' .
                          $db->quoteName('ut.last_post_id') . ' AS ' . $db->quoteName('my_last_post_id') . ', ' .
                          $db->quoteName('ut.favorite') . ', ' . $db->quoteName('tt.last_post_id') . ' AS ' . $db->quoteName('lastread') . ', 0 AS ' . $db->quoteName('unread'))
                ->from($db->quoteName('#__kunena_topics', 'tt'))
                ->leftJoin($db->quoteName('#__kunena_user_topics', 'ut') . ' ON ' . $db->quoteName('tt.id') . ' = ' . $db->quoteName('ut.topic_id') . ' AND ' . $db->quoteName('ut.user_id') . ' = ' . $db->quote($user->userid))
                ->where($where)
                ->order($orderby);
        }

        $query->setLimit($limit, $limitstart);
        $db->setQuery($query);

        try {
            $results = (array) $db->loadAssocList('id');
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

            return [0, []];
        }

        $topics = [];

        foreach ($results as $id => $result) {
            $instance = new KunenaTopic($result);
            $instance->exists(true);
            self::$_instances [$id] = $instance;
            $topics[$id]            = $instance;
        }

        unset($results);

        // The list shows the rating of each topic: load the number of ratings with one query, not one for each topic.
        if ($topics && $config->ratingEnabled) {
            KunenaRateHelper::loadCounts(array_keys($topics));
        }

        KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

        return [$total, $topics];
    }

    /**
     * @param   mixed   $ids        ids
     * @param   string  $authorise  authorise
     *
     * @return  KunenaTopic[]
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function getTopics($ids = false, $authorise = 'read'): array
    {
        KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

        if ($ids === false) {
            return self::$_instances;
        }

        if (\is_array($ids)) {
            $ids = array_unique($ids);
        } else {
            $ids = [$ids];
        }

        self::loadTopics($ids);

        $list = [];

        foreach ($ids as $id) {
            if (!empty(self::$_instances [$id]) && self::$_instances [$id]->isAuthorised($authorise, null)) {
                $list [$id] = self::$_instances [$id];
            }
        }

        KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

        return $list;
    }

    /**
     * @param   mixed  $ids   ids
     * @param   mixed  $user  user
     *
     * @return User\KunenaTopicUser|User\KunenaTopicUser[]
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function getUserTopics($ids = false, $user = null)
    {
        if ($ids === false) {
            $ids = array_keys(self::$_instances);
        }

        return KunenaTopicUserHelper::getTopics($ids, $user);
    }

    /**
     * @param   array  $ids  ids
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected static function loadTopics(array $ids): void
    {
        // The topics which a page asked to load with the next ones (loadAlso): with this query, not with one of their own.
        if (self::$_also) {
            $ids         = array_values(array_unique(array_map('intval', array_merge($ids, array_values(self::$_also)))));
            self::$_also = [];
        }

        foreach ($ids as $i => $id) {
            $id = \intval($id);

            if (!$id || isset(self::$_instances [$id])) {
                unset($ids[$i]);
            }
        }

        if (empty($ids)) {
            return;
        }

        $idlist = implode(',', $ids);
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $query  = $db->createQuery();
        $query->select('*')
            ->from($db->quoteName('#__kunena_topics'))
            ->where($db->quoteName('id') . ' IN (' . $idlist . ')');
        $db->setQuery($query);

        $results = [];

        try {
            $results = (array) $db->loadAssocList('id');
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
        }

        foreach ($ids as $id) {
            if (isset($results[$id])) {
                $instance = new KunenaTopic($results[$id]);
                $instance->exists(true);
                self::$_instances [$id] = $instance;
            } else {
                self::$_instances [$id] = null;
            }
        }

        unset($results);

        // The numbers of ratings are loaded when the first one is shown, with the ones of the topics of the other lists of the page.
        if (KunenaFactory::getConfig()->ratingEnabled) {
            KunenaRateHelper::deferCounts($ids);
        }
    }

    /**
     * @param   mixed  $ids    ids
     * @param   int    $start  start
     * @param   int    $end    end
     *
     * @return  boolean|integer
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function recount($ids = false, $start = 0, $end = 0)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        if (\is_array($ids)) {
            // An empty list means that there is nothing to recount (not "everything", and not an empty IN () clause).
            $ids = array_values(array_filter(array_map('intval', $ids)));

            if (!$ids) {
                return 0;
            }

            $threads = 'AND ' . $db->quoteName('m.thread') . ' IN (' . implode(',', $ids) . ')';
            $empty   = 'AND ' . $db->quoteName('tt.id') . ' IN (' . implode(',', $ids) . ')';
        } elseif ((int) $ids) {
            $threads = 'AND ' . $db->quoteName('m.thread') . ' = ' . $db->quote((int) $ids);
            $empty   = 'AND ' . $db->quoteName('tt.id') . ' = ' . $db->quote((int) $ids);
        } else {
            $threads = $empty = '';
        }

        if ($end) {
            if ($start < 1) {
                $start = 1;
            }

            $topics = " AND (" . $db->quoteName('tt.id') . " BETWEEN {$start} AND {$end})";
        } else {
            $topics = '';
        }

        // Mark all empty topics as deleted
        $query = $db->createQuery();
        $query->update($db->quoteName('#__kunena_topics', 'tt'))
            ->leftJoin($db->quoteName('#__kunena_messages', 'm') . ' ON ' . $db->quoteName('m.thread') . ' = ' . $db->quoteName('tt.id') . ' AND ' . $db->quoteName('tt.hold') . ' = ' . $db->quoteName('m.hold'))
            ->set($db->quoteName('tt.hold') . ' = 4')
            ->set($db->quoteName('tt.posts') . ' = 0')
            ->set($db->quoteName('tt.attachments') . ' = 0')
            ->set($db->quoteName('tt.first_post_id') . ' = 0')
            ->set($db->quoteName('tt.first_post_time') . ' = 0')
            ->set($db->quoteName('tt.first_post_userid') . ' = 0')
            ->set($db->quoteName('tt.first_post_message') . ' =  \'\'')
            ->set($db->quoteName('tt.first_post_guest_name') . ' =  \'\'')
            ->set($db->quoteName('tt.last_post_id') . ' = 0')
            ->set($db->quoteName('tt.last_post_time') . ' = 0')
            ->set($db->quoteName('tt.last_post_userid') . ' = 0')
            ->set($db->quoteName('tt.last_post_message') . ' =  \'\'')
            ->set($db->quoteName('tt.last_post_guest_name') . ' =  \'\'')
            // The topics by their own id: a topic without messages has no m.thread, so the condition on m.thread found none of
            // them when the recount had a list of topics (the purge of the trash), and they kept their old posts and last post.
            ->where($db->quoteName('tt.moved_id') . '=0 AND ' . $db->quoteName('tt.hold') . '!=4 AND ' . $db->quoteName('m.id') . ' IS NULL ' . $topics . ' ' . $empty);
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            return false;
        }

        $rows = $db->getAffectedRows();

        // Find out if there are deleted topics with visible replies.
        $subQuery = $db->createQuery();
        $subQuery->select(
            [
                $db->quoteName('m.thread'),
                'MIN(' . $db->quoteName('m.hold') . ') AS ' . $db->quoteName('hold'),
            ]
        )
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->where($db->quoteName('m.hold') . ' IN (0,1) ' . $threads)
            ->group($db->quoteName('m.thread'));

        $query = $db->createQuery();
        $query->update($db->quoteName('#__kunena_topics', 'tt'))
            ->join('INNER', '(' . $subQuery . ') AS ' . $db->quoteName('c') . ' ON ' . $db->quoteName('tt.id') . ' = ' . $db->quoteName('c.thread'))
            ->set($db->quoteName('tt.hold') . ' = ' . $db->quoteName('c.hold'))
            ->where($db->quoteName('tt.moved_id') . ' = 0 ' . $topics)
            // A deleted topic keeps the replies which wait for approval as they were (they come back with the topic): they do not
            // bring it back. The recount made such a topic "waiting for approval" again, with the reply as its first post.
            ->where('NOT (' . $db->quoteName('tt.hold') . ' IN (2,3) AND ' . $db->quoteName('c.hold') . ' = 1)');
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            return false;
        }

        $rows += $db->getAffectedRows();

        // Recount total posts, total attachments and update first & last post information (by time)
        $subQuery = $db->createQuery();
        $subQuery->select(
            [
                $db->quoteName('m.thread'),
                $db->quoteName('m.hold'),
                'COUNT(DISTINCT ' . $db->quoteName('m.id') . ') AS ' . $db->quoteName('posts'),
                'COUNT(' . $db->quoteName('a.id') . ') AS ' . $db->quoteName('attachments'),
                'MIN(' . $db->quoteName('m.time') . ') AS ' . $db->quoteName('mintime'),
                'MAX(' . $db->quoteName('m.time') . ') AS ' . $db->quoteName('maxtime'),
            ]
        )
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->leftJoin($db->quoteName('#__kunena_attachments', 'a') . ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('a.mesid'))
            ->where($db->quoteName('m.moved') . ' = 0 ' . $threads)
            ->group([$db->quoteName('m.thread'), $db->quoteName('m.hold')]);

        $query = $db->createQuery();
        $query->update($db->quoteName('#__kunena_topics', 'tt'))
            // The group of the messages in the state of the topic: joined by the topic only, the group of the deleted messages was
            // taken too when its first or last time was the time of a message of the topic (wrong posts, files, first and last post).
            ->join('INNER', '(' . $subQuery . ') AS ' . $db->quoteName('c') . ' ON ' . $db->quoteName('tt.id') . ' = ' . $db->quoteName('c.thread') . ' AND ' . $db->quoteName('c.hold') . ' = ' . $db->quoteName('tt.hold'))
            ->join('INNER', $db->quoteName('#__kunena_messages', 'mmin') . ' ON ' . $db->quoteName('c.thread') . ' = ' . $db->quoteName('mmin.thread') . ' AND ' . $db->quoteName('mmin.hold') . ' = ' . $db->quoteName('tt.hold') . ' AND ' . $db->quoteName('mmin.time') . ' = ' . $db->quoteName('c.mintime'))
            ->join('INNER', $db->quoteName('#__kunena_messages', 'mmax') . ' ON ' . $db->quoteName('c.thread') . ' = ' . $db->quoteName('mmax.thread') . ' AND ' . $db->quoteName('mmax.hold') . ' = ' . $db->quoteName('tt.hold') . ' AND ' . $db->quoteName('mmax.time') . ' = ' . $db->quoteName('c.maxtime'))
            ->join('INNER', $db->quoteName('#__kunena_messages_text', 'tmin') . ' ON ' . $db->quoteName('tmin.mesid') . ' = ' . $db->quoteName('mmin.id'))
            ->join('INNER', $db->quoteName('#__kunena_messages_text', 'tmax') . ' ON ' . $db->quoteName('tmax.mesid') . ' = ' . $db->quoteName('mmax.id'))
            ->set($db->quoteName('tt.posts') . ' = ' . $db->quoteName('c.posts'))
            ->set($db->quoteName('tt.attachments') . ' = ' . $db->quoteName('c.attachments'))
            ->set($db->quoteName('tt.first_post_id') . ' = ' . $db->quoteName('mmin.id'))
            ->set($db->quoteName('tt.first_post_time') . ' = ' . $db->quoteName('mmin.time'))
            ->set($db->quoteName('tt.first_post_userid') . ' = ' . $db->quoteName('mmin.userid'))
            ->set($db->quoteName('tt.first_post_message') . ' = ' . $db->quoteName('tmin.message'))
            ->set($db->quoteName('tt.first_post_guest_name') . ' = ' . $db->quoteName('mmin.name'))
            ->set($db->quoteName('tt.last_post_id') . ' = ' . $db->quoteName('mmax.id'))
            ->set($db->quoteName('tt.last_post_time') . ' = ' . $db->quoteName('mmax.time'))
            ->set($db->quoteName('tt.last_post_userid') . ' = ' . $db->quoteName('mmax.userid'))
            ->set($db->quoteName('tt.last_post_message') . ' = ' . $db->quoteName('tmax.message'))
            ->set($db->quoteName('tt.last_post_guest_name') . ' = ' . $db->quoteName('mmax.name'))
            ->where($db->quoteName('tt.moved_id') . ' = 0 ' . $topics);
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);

            return false;
        }

        $rows += $db->getAffectedRows();

        return $rows;
    }

    /**
     * @param   mixed  $ids    ids
     * @param   bool   $value  value
     * @param   mixed  $user   user
     *
     * @return  integer
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function subscribe($ids, $value = true, $user = null): int
    {
        // Pre-load all items
        // Codestyle fixes: use real url
        KunenaTopicUserHelper::getTopics($ids, $user);
        $count = 0;

        foreach ($ids as $id) {
            $usertopic = KunenaTopicUserHelper::get($id, $user);

            if ($usertopic->subscribed != (int) $value) {
                $count++;
            }

            $usertopic->subscribed = (int) $value;
            $usertopic->save();
        }

        return $count;
    }

    /**
     * Whether a published topic with this subject exists in the categories (upper and lower case and the spaces at the
     * ends ignored). The collation of the subject also ignores the accents, so the candidates are compared again in PHP.
     *
     * @param   DatabaseInterface  $db              The database.
     * @param   string             $subject         The subject.
     * @param   int[]              $categoryIds     The categories the user may read.
     * @param   string             $ownTopicsWhere  The condition of the categories where the user only sees his own topics
     *                                              (on a.category_id and a.first_post_userid), or ''.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function subjectExists(DatabaseInterface $db, string $subject, array $categoryIds, string $ownTopicsWhere = ''): bool
    {
        $subject     = mb_strtolower(trim($subject));
        $categoryIds = array_map('intval', $categoryIds);

        if ($subject === '' || !$categoryIds) {
            return false;
        }

        $query = $db->createQuery()
            ->select($db->quoteName('a.subject'))
            ->from($db->quoteName('#__kunena_topics', 'a'))
            ->where($db->quoteName('a.hold') . ' = 0')
            ->where($db->quoteName('a.moved_id') . ' = 0')
            ->where($db->quoteName('a.category_id') . ' IN (' . implode(',', $categoryIds) . ')')
            ->where('TRIM(' . $db->quoteName('a.subject') . ') = ' . $db->quote($subject));

        if ($ownTopicsWhere !== '') {
            $query->where($ownTopicsWhere);
        }

        foreach ((array) $db->setQuery($query)->loadColumn() as $candidate) {
            if (mb_strtolower(trim((string) $candidate)) === $subject) {
                return true;
            }
        }

        return false;
    }

    /**
     * Method to trash topics. They will be marked as deleted, but still exist in database.
     *
     * @param   array|int  $ids  ids
     *
     * @return  integer  Count of trashed topics.
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function trash($ids)
    {
        if (empty($ids)) {
            return 0;
        }

        if (\is_array($ids)) {
            $idlist = implode(',', $ids);
        } else {
            $idlist = (int) $ids;
        }

        $db        = Factory::getContainer()->get('DatabaseDriver');

        // As the delete of a topic (KunenaTopic::publish(TOPIC_DELETED)): the messages in the state of their topic become "deleted
        // with the topic", the others keep theirs. Every message was set to deleted, so a restore of the topic (which brings back the
        // messages deleted with it) published the replies which waited for approval and the replies which were deleted before.
        $query1 = $db->createQuery();
        $query1->update($db->quoteName('#__kunena_messages', 'm'))
               ->innerJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('m.thread'))
               ->set($db->quoteName('m.hold') . ' = ' . KunenaForum::TOPIC_DELETED)
               ->set($db->quoteName('m.deleted_time') . ' = ' . time())
               ->where($db->quoteName('m.thread') . ' IN (' . $idlist . ')')
               ->where($db->quoteName('m.hold') . ' = ' . $db->quoteName('t.hold'))
               ->where($db->quoteName('t.hold') . ' != ' . KunenaForum::TOPIC_DELETED);
        $queries[] = $query1;

        // Create prepared query for topics
        $query2 = $db->createQuery();
        $query2->update($db->quoteName('#__kunena_topics'))
               ->set($db->quoteName('hold') . ' = ' . KunenaForum::TOPIC_DELETED)
               ->where($db->quoteName('id') . ' IN (' . $idlist . ')');
        $queries[] = $query2;

        foreach ($queries as $query) {
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);

                return false;
            }
        }

        return $db->getAffectedRows();
    }
}
