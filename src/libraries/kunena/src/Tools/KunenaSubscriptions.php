<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tools;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

/**
 * Who is subscribed to what (tool "Subscriptions"): the categories and the topics with the most subscribers, the users with
 * the most subscriptions, and the removal of the subscriptions of a user, of a category or of a topic.
 *
 * @since   Kunena 8.0
 */
class KunenaSubscriptions
{
    /**
     * At most this many rows in each list.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 20;

    /**
     * At most this many topics of a user are listed.
     *
     * @since   Kunena 8.0
     */
    public const USER_LIMIT = 200;

    /**
     * The scopes of an unsubscription: the column which is matched, the tables.
     *
     * @since   Kunena 8.0
     */
    public const SCOPES = [
        'user'     => ['user_id', ['#__kunena_user_categories', '#__kunena_user_topics']],
        'category' => ['category_id', ['#__kunena_user_categories']],
        'topic'    => ['topic_id', ['#__kunena_user_topics']],
    ];

    /**
     * The overview of this site.
     *
     * @return  array{categories: int, topics: int, users: int, topCategories: array, topTopics: array, topUsers: array}
     *
     * @since   Kunena 8.0
     */
    public static function overviewOnSite(): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $total = static function (string $table, string $what) use ($db): int {
            $query = $db->createQuery()
                ->select('COUNT(' . $what . ')')
                ->from($db->quoteName($table))
                ->where($db->quoteName('subscribed') . ' = 1');

            return (int) $db->setQuery($query)->loadResult();
        };

        $query = $db->createQuery()
            ->select([$db->quoteName('c.id'), $db->quoteName('c.name'), 'COUNT(*) AS ' . $db->quoteName('total')])
            ->from($db->quoteName('#__kunena_user_categories', 's'))
            ->innerJoin($db->quoteName('#__kunena_categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('s.category_id'))
            ->where($db->quoteName('s.subscribed') . ' = 1')
            ->group([$db->quoteName('c.id'), $db->quoteName('c.name')])
            ->order('COUNT(*) DESC');
        $topCategories = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();

        $query = $db->createQuery()
            ->select([$db->quoteName('t.id'), $db->quoteName('t.subject'), $db->quoteName('t.category_id'), 'COUNT(*) AS ' . $db->quoteName('total')])
            ->from($db->quoteName('#__kunena_user_topics', 's'))
            ->innerJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('s.topic_id'))
            ->where($db->quoteName('s.subscribed') . ' = 1')
            ->group([$db->quoteName('t.id'), $db->quoteName('t.subject'), $db->quoteName('t.category_id')])
            ->order('COUNT(*) DESC');
        $topTopics = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();

        // The subscriptions of each user: to categories and to topics.
        $union = '(SELECT ' . $db->quoteName('user_id') . ' FROM ' . $db->quoteName('#__kunena_user_categories') . ' WHERE ' . $db->quoteName('subscribed') . ' = 1'
            . ' UNION ALL SELECT ' . $db->quoteName('user_id') . ' FROM ' . $db->quoteName('#__kunena_user_topics') . ' WHERE ' . $db->quoteName('subscribed') . ' = 1)';
        $query = $db->createQuery()
            ->select([$db->quoteName('u.id'), $db->quoteName('u.username'), 'COUNT(*) AS ' . $db->quoteName('total')])
            ->from($union . ' AS ' . $db->quoteName('s'))
            ->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('s.user_id'))
            ->group([$db->quoteName('u.id'), $db->quoteName('u.username')])
            ->order('COUNT(*) DESC');
        $topUsers = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();

        $query = $db->createQuery()
            ->select('COUNT(DISTINCT ' . $db->quoteName('s.user_id') . ')')
            ->from($union . ' AS ' . $db->quoteName('s'));

        return [
            'categories'    => $total('#__kunena_user_categories', '*'),
            'topics'        => $total('#__kunena_user_topics', '*'),
            'users'         => (int) $db->setQuery($query)->loadResult(),
            'topCategories' => $topCategories,
            'topTopics'     => $topTopics,
            'topUsers'      => $topUsers,
        ];
    }

    /**
     * The subscriptions of a user: his categories and his topics, by name.
     *
     * @param   int  $userid  The user.
     *
     * @return  array{categories: array, topics: array, topicsTotal: int}
     *
     * @since   Kunena 8.0
     */
    public static function ofUserOnSite(int $userid): array
    {
        $result = ['categories' => [], 'topics' => [], 'topicsTotal' => 0];

        if ($userid <= 0) {
            return $result;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select([$db->quoteName('c.id'), $db->quoteName('c.name')])
            ->from($db->quoteName('#__kunena_user_categories', 's'))
            ->innerJoin($db->quoteName('#__kunena_categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('s.category_id'))
            ->where($db->quoteName('s.user_id') . ' = :id')
            ->where($db->quoteName('s.subscribed') . ' = 1')
            ->bind(':id', $userid, ParameterType::INTEGER)
            ->order($db->quoteName('c.name'));
        $result['categories'] = (array) $db->setQuery($query)->loadAssocList();

        $query = $db->createQuery()
            ->select([$db->quoteName('t.id'), $db->quoteName('t.subject'), $db->quoteName('t.category_id'), $db->quoteName('t.last_post_time')])
            ->from($db->quoteName('#__kunena_user_topics', 's'))
            ->innerJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('s.topic_id'))
            ->where($db->quoteName('s.user_id') . ' = :id')
            ->where($db->quoteName('s.subscribed') . ' = 1')
            ->bind(':id', $userid, ParameterType::INTEGER)
            ->order($db->quoteName('t.last_post_time') . ' DESC');
        $result['topics'] = (array) $db->setQuery($query, 0, self::USER_LIMIT)->loadAssocList();

        $count = clone $query;
        $count->clear('select')->clear('order')->select('COUNT(*)');
        $result['topicsTotal'] = (int) $db->setQuery($count)->loadResult();

        return $result;
    }

    /**
     * Removes the subscriptions of a user, of a category or of a topic; with a user, only his subscription to that category
     * or topic.
     *
     * @param   string  $scope   A key of SCOPES.
     * @param   int     $id      The user, the category or the topic.
     * @param   int     $userid  The user whose subscription is removed (0: all the users).
     *
     * @return  int  The subscriptions removed.
     *
     * @since   Kunena 8.0
     */
    public static function unsubscribeOnSite(string $scope, int $id, int $userid = 0): int
    {
        if (!isset(self::SCOPES[$scope]) || $id <= 0 || $userid < 0) {
            return 0;
        }

        [$column, $tables] = self::SCOPES[$scope];
        $db                = Factory::getContainer()->get('DatabaseDriver');
        $removed           = 0;

        foreach ($tables as $table) {
            $query = $db->createQuery()
                ->update($db->quoteName($table))
                ->set($db->quoteName('subscribed') . ' = 0')
                ->where($db->quoteName($column) . ' = :id')
                ->where($db->quoteName('subscribed') . ' = 1')
                ->bind(':id', $id, ParameterType::INTEGER);

            if ($userid > 0 && $scope !== 'user') {
                $query->where($db->quoteName('user_id') . ' = :user')->bind(':user', $userid, ParameterType::INTEGER);
            }

            $db->setQuery($query)->execute();
            $removed += (int) $db->getAffectedRows();
        }

        return $removed;
    }
}
