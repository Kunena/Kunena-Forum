<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\User\User;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Database\Object\KunenaFinder;
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
        $categories = $user->getAllowedCategories();
        $list       = implode(',', $categories);

        if (!empty($list)) {
            $this->query->where('a.category_id  IN (' . $list . ')');
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
    public function filterByTime(Date $starting = null, Date $ending = null, $lastPost = true): KunenaTopicFinder
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
                $this->query->where($this->db->quoteName('ut.posts') . ' => 0');
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
        $this->query->where($this->db->quoteName('a.id') . ' NOT IN (' . $topicsId . ')');

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
    protected function build(QueryInterface $query = null): void
    {
        if (!empty($this->hold)) {
            $this->hold = ArrayHelper::toInteger($this->hold, 0);
            $hold       = implode(',', $this->hold);
            $query->where($this->db->quoteName('a.hold') . ' IN (' . $hold . ')');
        }
    }
}
