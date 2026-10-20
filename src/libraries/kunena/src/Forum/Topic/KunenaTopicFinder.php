<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Database\Finder\KunenaFinder;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\TopicFinder
 *
 * @since   Kunena 6.0
 */
class KunenaTopicFinder extends KunenaFinder
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $table = '#__kunena_topics';

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected $hold = [0];

    /**
     * @var     null
     * @since   Kunena 6.0
     */
    protected $moved = null;

    /**
     * Constructor.
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->limit = KunenaFactory::getConfig()->threadsPerPage;
    }

    /**
     * Filter by user access to the categories.
     *
     * It is very important to use this or category filter. Otherwise topics from unauthorized categories will be
     * included to the search results.
     *
     * @param   KunenaUser  $user  user
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function filterByUserAccess(KunenaUser $user): KunenaTopicFinder
    {
        $categories = array_map('intval', (array) $user->getAllowedCategories());

        // A user without any allowed category sees no topic: an empty list must not remove the filter.
        if (empty($categories)) {
            $this->query->where('0');

            return $this;
        }

        $this->query->where('a.category_id  IN (' . implode(',', $categories) . ')');

        // Categories where the users only see their own topics.
        $own = KunenaCategoryHelper::getOwnTopicsOnlyWhere($user, 'a.category_id', 'a.first_post_userid');

        if ($own) {
            $this->query->where($own);
        }

        return $this;
    }

    /**
     * Filter by list of categories.
     *
     * It is very important to use this or user access filter. Otherwise topics from unauthorized categories will be
     * included to the search results.
     *
     * $topics->filterByCategories($me->getAllowedCategories())->limit(20)->find();
     *
     * @param   array  $categories  categories
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByCategories(array $categories): KunenaTopicFinder
    {
        $list = [];

        foreach ($categories as $category) {
            if ($category instanceof KunenaCategory) {
                $list[] = (int) $category->id;
            } else {
                $list[] = (int) $category;
            }
        }

        $list = implode(',', $list);

        // Handle empty list as impossible filter value.
        if (!$list) {
            $list = -1;
        }

        $this->query->where($this->db->quoteName('a.category_id') . ' IN (' . $list . ')');

        return $this;
    }

    /**
     * Filter by time, either on first or last post.
     *
     * @param   Date|null  $starting  Starting date or null if older than ending date.
     * @param   Date|null  $ending    Ending date or null if newer than starting date.
     * @param   bool       $lastPost  True = last post, False = first post.
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByTime(?Date $starting = null, ?Date $ending = null, $lastPost = true): KunenaTopicFinder
    {
        $name = $lastPost ? 'last' : 'first';

        if ($starting && $ending) {
            $this->query->where($this->db->quoteName('a.' . $name . '_post_time') . ' BETWEEN ' . $this->db->quote($starting->toUnix()) . ' AND ' . $this->db->quote($ending->toUnix()));
        } elseif ($starting) {
            $this->query->where($this->db->quoteName('a.' . $name . '_post_time') . ' > ' . $this->db->quote($starting->toUnix()));
        } elseif ($ending) {
            $this->query->where($this->db->quoteName('a.' . $name . '_post_time') . ' <= ' . $this->db->quote($ending->toUnix()));
        }

        return $this;
    }

    /**
     * Filter by users role in the topic. For now use only once.
     *
     * first_post = User has posted the first post.
     * last_post = User has posted the last post.
     * owner = User owns the topic (usually has created it).
     * posted = User has posted to the topic.
     * replied = User has written a reply to the topic (but does not own it).
     * favorited = User has favorited the topic.
     * subscribed = User has subscribed to the topic.
     *
     * @param   KunenaUser  $user    user
     * @param   string      $action  Action or negation of the action (!action).
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByUser(KunenaUser $user, $action = 'owner'): KunenaTopicFinder
    {
        $this->query->innerJoin($this->db->quoteName('#__kunena_user_topics', 'ut') . ' ON ' . $this->db->quoteName('a.id') . ' = ' . $this->db->quoteName('ut.topic_id'));
        $this->query->where($this->db->quoteName('ut.user_id') . ' = ' . (int) $user->userid);

        switch ($action) {
            case 'first_post':
                $this->query->where($this->db->quoteName('a.first_post_userid') . ' = ' . (int) $user->userid);
                break;
            case '!first_post':
                $this->query->where($this->db->quoteName('a.first_post_userid') . ' != ' . (int) $user->userid);
                break;
            case 'last_post':
                $this->query->where($this->db->quoteName('a.last_post_userid') . ' = ' . (int) $user->userid);
                break;
            case '!last_post':
                $this->query->where($this->db->quoteName('a.last_post_userid') . ' != ' . (int) $user->userid);
                break;
            case 'owner':
                $this->query->where($this->db->quoteName('ut.owner') . ' = 1');
                break;
            case '!owner':
                $this->query->where($this->db->quoteName('ut.owner') . ' != 1');
                break;
            case 'posted':
                $this->query->where($this->db->quoteName('ut.posts') . ' > 0');
                break;
            case '!posted':
                $this->query->where($this->db->quoteName('ut.posts') . ' = 0');
                break;
            case 'replied':
                $this->query->where('(' . $this->db->quoteName('ut.owner') . ' = 0 AND ' . $this->db->quoteName('ut.posts') . ' > 0)');
                break;
            case '!replied':
                $this->query->where('(' . $this->db->quoteName('ut.owner') . ' = 0 AND ' . $this->db->quoteName('ut.posts') . ' = 0)');
                break;
            case 'favorited':
                $this->query->where($this->db->quoteName('ut.favorite') . ' = 1');
                break;
            case '!favorited':
                $this->query->where($this->db->quoteName('ut.favorite') . ' != 1');
                break;
            case 'subscribed':
                $this->query->where($this->db->quoteName('ut.subscribed') . ' = 1');
                break;
            case '!subscribed':
                $this->query->where($this->db->quoteName('ut.subscribed') . ' != 1');
                break;
            case 'involved':
                $this->query->where('(' . $this->db->quoteName('ut.posts') . ' > 0 OR ' . $this->db->quoteName('ut.favorite') . ' = 1 OR ' . $this->db->quoteName('ut.subscribed') . ' = 1)');
                break;
            case '!involved':
                $this->query->where('(' . $this->db->quoteName('ut.posts') . ' < 1 OR ' . $this->db->quoteName('ut.favorite') . ' = 0 OR ' . $this->db->quoteName('ut.subscribed') . ' = 0)');
        }

        return $this;
    }

    /**
     * Filter topics with topics Id given.
     *
     * @param   string  $topicsId   The list of ID of topics to filter (the strong should be like that: 1,2,3)
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterTopicNotIn(string $topicsId): KunenaTopicFinder
    {
        // Only integer ids are kept, and an empty list excludes nothing (NOT IN () is a syntax error).
        $ids = array_filter(array_map('intval', explode(',', $topicsId)));

        if ($ids) {
            $this->query->where($this->db->quoteName('a.id') . ' NOT IN (' . implode(',', $ids) . ')');
        }

        return $this;
    }

    /**
     * Filter by hold (0=published, 1=unapproved, 2=deleted, 3=topic deleted).
     *
     * @param   array  $hold  List of hold states to display.
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByHold(array $hold = [0]): KunenaTopicFinder
    {
        $this->hold = $hold;

        return $this;
    }

    /**
     * Filter by moved topics.
     *
     * @param   bool  $value  True on moved, false on not moved.
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByMoved($value = true): KunenaTopicFinder
    {
        $this->moved = (bool) $value;

        return $this;
    }

    /**
     * Only the topics which are for all languages or for the language.
     *
     * @param   string  $language  The language code, like en-GB.
     *
     * @return  $this
     *
     * @since   Kunena 7.2
     */
    public function filterByLanguage(string $language): KunenaTopicFinder
    {
        $this->query->where($this->db->quoteName('a.language') . ' IN (' . $this->db->quote('*') . ', ' . $this->db->quote($language) . ')');

        return $this;
    }

    /**
     * Only the topics which were started by one of the users (the follow list of a user). No users: no topics.
     *
     * @param   int[]  $userids  Ids of the users.
     *
     * @return  $this
     *
     * @since   Kunena 7.2
     */
    public function filterByAuthors(array $userids): KunenaTopicFinder
    {
        $userids = array_filter(ArrayHelper::toInteger($userids));

        $this->query->where($this->db->quoteName('a.first_post_userid') . ' IN (' . ($userids ? implode(',', $userids) : '-1') . ')');

        return $this;
    }

    /**
     * Leave out the topics which were started by one of the users (the ignore list of a user).
     *
     * @param   int[]  $userids  Ids of the users.
     *
     * @return  $this
     *
     * @since   Kunena 7.2
     */
    public function filterOutAuthors(array $userids): KunenaTopicFinder
    {
        $userids = array_filter(ArrayHelper::toInteger($userids));

        if ($userids) {
            $this->query->where($this->db->quoteName('a.first_post_userid') . ' NOT IN (' . implode(',', $userids) . ')');
        }

        return $this;
    }

    /**
     * Filter topics which have the text in the subject.
     *
     * @param   string  $text  Text to look for.
     *
     * @return  $this
     *
     * @since   Kunena 7.2
     */
    public function filterBySubject(string $text): KunenaTopicFinder
    {
        $text = trim($text);

        if ($text !== '') {
            $this->query->where($this->db->quoteName('a.subject') . ' LIKE ' . $this->db->quote('%' . $this->db->escape($text, true) . '%', false));
        }

        return $this;
    }

    /**
     * Filter topics which have the Joomla tag.
     *
     * @param   int  $tagId  The id of the tag (0 does not filter).
     *
     * @return  $this
     *
     * @since   Kunena 7.2
     */
    public function filterByTag(int $tagId): KunenaTopicFinder
    {
        if ($tagId > 0) {
            $this->query->where(
                $this->db->quoteName('a.id') . ' IN (SELECT ' . $this->db->quoteName('content_item_id') . ' FROM '
                . $this->db->quoteName('#__contentitem_tag_map') . ' WHERE ' . $this->db->quoteName('type_alias') . ' = '
                . $this->db->quote(KunenaTopicTags::TYPE_ALIAS) . ' AND ' . $this->db->quoteName('tag_id') . ' = ' . $tagId . ')'
            );
        }

        return $this;
    }

    /**
     * Filter topics which have at least one of the words in the subject (to find similar topics).
     *
     * @param   string[]  $words  The words to look for.
     *
     * @return  $this
     *
     * @since   Kunena 7.2
     */
    public function filterBySimilarSubject(array $words): KunenaTopicFinder
    {
        $conditions = [];

        foreach ($words as $word) {
            $conditions[] = $this->db->quoteName('a.subject') . ' LIKE ' . $this->db->quote('%' . $this->db->escape($word, true) . '%', false);
        }

        // Without words nothing is similar.
        $this->query->where($conditions ? '(' . implode(' OR ', $conditions) . ')' : '0');

        return $this;
    }

    /**
     * Filter topics which were started by a user whose username or name has the text (or by a guest with that name).
     *
     * @param   string  $text  Text to look for.
     *
     * @return  $this
     *
     * @since   Kunena 7.2
     */
    public function filterByAuthorName(string $text): KunenaTopicFinder
    {
        $text = trim($text);

        if ($text === '') {
            return $this;
        }

        $like  = $this->db->quote('%' . $this->db->escape($text, true) . '%', false);
        $query = $this->db->createQuery()
            ->select($this->db->quoteName('id'))
            ->from($this->db->quoteName('#__users'))
            ->where('(' . $this->db->quoteName('username') . ' LIKE ' . $like . ' OR ' . $this->db->quoteName('name') . ' LIKE ' . $like . ')');

        $ids = ArrayHelper::toInteger((array) $this->db->setQuery($query, 0, 200)->loadColumn());
        $or  = [$this->db->quoteName('a.first_post_guest_name') . ' LIKE ' . $like];

        if ($ids) {
            $or[] = $this->db->quoteName('a.first_post_userid') . ' IN (' . implode(',', $ids) . ')';
        }

        $this->query->where('(' . implode(' OR ', $or) . ')');

        return $this;
    }

    /**
     * Filter topics by status.
     *
     * @param   string  $status  noreplies, replied, sticky, locked or poll (anything else does not filter).
     *
     * @return  $this
     *
     * @since   Kunena 7.2
     */
    public function filterByStatus(string $status): KunenaTopicFinder
    {
        switch ($status) {
            case 'noreplies':
                $this->query->where($this->db->quoteName('a.posts') . ' = 1');
                break;
            case 'replied':
                $this->query->where($this->db->quoteName('a.posts') . ' > 1');
                break;
            case 'sticky':
                $this->query->where($this->db->quoteName('a.ordering') . ' > 0');
                break;
            case 'locked':
                $this->query->where($this->db->quoteName('a.locked') . ' > 0');
                break;
            case 'poll':
                $this->query->where($this->db->quoteName('a.poll_id') . ' > 0');
                break;
        }

        return $this;
    }

    /**
     * Get topics.
     *
     * @param   string  $access  Kunena action access control check.
     *
     * @return array
     *
     * @since   Kunena 6.0
     *
     * @throws \Exception
     */
    public function find($access = 'read'): array
    {
        $results = parent::find();

        return KunenaTopicHelper::getTopics($results, $access);
    }

    /**
     * Access to the query select
     *
     * @param   mixed  $columns  A string or an array of field names.
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function select($columns): KunenaTopicFinder
    {
        $this->query->select($columns);

        return $this;
    }

    /**
     * Get unread topics
     *
     * @param   KunenaUser  $user  user
     *
     * @return  $this
     *
     * @since   Kunena 6.0
     */
    public function filterByUserUnread(KunenaUser $user): KunenaTopicFinder
    {
        $this->query->innerJoin($this->db->quoteName('#__kunena_user_read', 'ur') . ' ON ' . $this->db->quoteName('a.id') . ' = ' . $this->db->quoteName('ur.topic_id'));
        $this->query->where($this->db->quoteName('ur.user_id') . ' != ' . (int) $user->userid);

        return $this;
    }

    /**
     * @param   QueryInterface|null  $query  query
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function build(?QueryInterface $query = null): void
    {
        if (!empty($this->hold)) {
            $this->hold = ArrayHelper::toInteger($this->hold, 0);
            $hold       = implode(',', $this->hold);
            $query->where($this->db->quoteName('a.hold') . ' IN (' . $hold . ')');
        }

        // Categories where the users only see their own topics: for every list and count, whatever filter was used.
        $own = KunenaCategoryHelper::getOwnTopicsOnlyWhere(null, $this->db->quoteName('a.category_id'), $this->db->quoteName('a.first_post_userid'));

        if ($own) {
            $query->where($own);
        }
    }
}
