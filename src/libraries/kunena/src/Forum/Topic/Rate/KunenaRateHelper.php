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

namespace Kunena\Forum\Libraries\Forum\Topic\Rate;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

/**
 * Kunena Forum Topic Rate Helper Class
 *
 * @since 5.0
 */
abstract class KunenaRateHelper
{
    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected static mixed $_instances = [];

    /**
     * @var     array  Number of ratings by topic id, loaded before in this request.
     * @since   Kunena 8.0
     */
    protected static array $_counts = [];

    /**
     * @var     array  The topics whose number of ratings is loaded when a number is first asked for: all of them with one query
     *                 (the topics of several lists of a page are loaded one list after the other).
     * @since   Kunena 8.0
     */
    protected static array $_pending = [];

    /**
     * The topics which a user rated.
     *
     * @param   int  $userid  The user.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    public static function topicsRatedBy(int $userid): array
    {
        if ($userid <= 0) {
            return [];
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select('DISTINCT ' . $db->quoteName('topic_id'))
            ->from($db->quoteName('#__kunena_rate'))
            ->where($db->quoteName('userid') . ' = :userid')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        return array_map('intval', (array) $db->setQuery($query)->loadColumn());
    }

    /**
     * The rating of the topics again, from their ratings: the rating is stored with the topic (the lists of topics show it) and is
     * written when a user rates, so a removed rating stayed in it. The rounded average (as getSelected()), 0 without any rating.
     *
     * @param   int[]  $ids  The topics.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function recountTopics(array $ids): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!$ids) {
            return;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        foreach (array_chunk($ids, 500) as $chunk) {
            $average = $db->createQuery()
                ->select('COALESCE(ROUND(AVG(' . $db->quoteName('r.rate') . ')), 0)')
                ->from($db->quoteName('#__kunena_rate', 'r'))
                ->where($db->quoteName('r.topic_id') . ' = ' . $db->quoteName('t.id'));
            $query   = $db->createQuery()
                ->update($db->quoteName('#__kunena_topics', 't'))
                ->set($db->quoteName('t.rating') . ' = (' . $average . ')')
                ->whereIn($db->quoteName('t.id'), $chunk);
            $db->setQuery($query)->execute();
        }

        foreach ($ids as $id) {
            unset(self::$_counts[$id]);
        }
    }

    /**
     * Load the number of ratings of these topics when a number is first asked for, with the ones of the other topics which
     * were loaded by then (one query instead of one for each list of topics).
     *
     * @param   int[]  $ids  Topic ids.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function deferCounts(array $ids): void
    {
        foreach ($ids as $id) {
            $id = (int) $id;

            if ($id > 0 && !isset(self::$_counts[$id])) {
                self::$_pending[$id] = $id;
            }
        }
    }

    /**
     * Forget the loaded number of ratings of a topic (after a new rating).
     *
     * @param   int  $id  Topic id.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function flushCount(int $id): void
    {
        unset(self::$_counts[$id]);
    }

    /**
     * Returns \Kunena\Forum\Libraries\Forum\Topic\TopicRate object
     *
     * @access    public
     *
     * @internal  param The $identifier rate object to load - Can be only an integer.
     *
     * @param   KunenaRate|int|null  $identifier  identifier
     *
     * @param   bool  $reload      reload
     *
     * @return  KunenaRate The rate object.
     *
     * @since     Kunena 5.0
     *
     * @throws  Exception
     */
    public static function get($identifier = null, $reload = false): KunenaRate
    {
        if ($identifier instanceof KunenaRate) {
            return $identifier;
        }

        $id = \intval($identifier);

        if ($id < 1) {
            return new KunenaRate();
        }

        if ($reload || empty(self::$_instances [$id])) {
            self::$_instances [$id] = new KunenaRate($id);
        }

        return self::$_instances [$id];
    }

    /**
     * Return sum of all rates given to a topics by all users
     *
     * @param   integer  $id  id
     *
     * @return  float
     *
     * @since   Kunena 6.0
     */
    public static function getCount(int $id): float
    {
        if (isset(self::$_counts[$id])) {
            return self::$_counts[$id];
        }

        // The topics which wait for their number: loaded together, this one with them.
        if (self::$_pending) {
            $pending        = self::$_pending + [$id => $id];
            self::$_pending = [];
            self::loadCounts($pending);

            if (isset(self::$_counts[$id])) {
                return self::$_counts[$id];
            }
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->select('(COUNT(' . $db->quoteName('rate') . ')) AS ' . $db->quoteName('selected'))
            ->from($db->quoteName('#__kunena_rate'))
            ->where($db->quoteName('topic_id') . ' = ' . $db->quote($id));
        $db->setQuery($query);

        return self::$_counts[$id] = (float) round((float) $db->loadResult());
    }

    /**
     * Return rate by id and userid
     *
     * @param   integer  $id      id
     * @param   integer  $userid  userid
     *
     * @return  int
     *
     * @since   Kunena 6.0
     */
    public static function getRate(int $id, int $userid): int
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->select($db->quoteName('rate'))
            ->from($db->quoteName('#__kunena_rate'))
            ->where($db->quoteName('topic_id') . ' = ' . $db->quote($id))
            ->andWhere($db->quoteName('userid') . ' = ' . $db->quote($userid));
        $db->setQuery($query);

        // One query: the result was loaded twice.
        return (int) $db->loadResult();
    }

    /**
     * Return sum of all rates given to a topics by all users
     *
     * @param   integer  $id  id
     *
     * @return  float
     *
     * @since   Kunena 6.0
     */
    public static function getSelected(int $id): float
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->select('(SUM(' . $db->quoteName('rate') . ')/COUNT(' . $db->quoteName('rate') . ')) AS ' . $db->quoteName('selected'))
            ->from($db->quoteName('#__kunena_rate'))
            ->where($db->quoteName('topic_id') . ' = ' . $db->quote($id));
        $db->setQuery($query);

        if (empty($db->loadResult())) {
            return '0';
        }

        return round($db->loadResult());

    }

    /**
     * Load the number of ratings of many topics with one query (a list of topics shows the rating of each).
     *
     * @param   array  $ids  Topic ids.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function loadCounts(array $ids): void
    {
        $ids = array_values(array_filter(array_map('intval', $ids), static fn ($id): bool => $id > 0 && !isset(self::$_counts[$id])));

        if (!$ids) {
            return;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->select([$db->quoteName('topic_id'), 'COUNT(' . $db->quoteName('rate') . ') AS ' . $db->quoteName('selected')])
            ->from($db->quoteName('#__kunena_rate'))
            ->where($db->quoteName('topic_id') . ' IN (' . implode(',', $ids) . ')')
            ->group($db->quoteName('topic_id'));
        $db->setQuery($query);

        foreach ($ids as $id) {
            self::$_counts[$id] = 0.0;
        }

        foreach ((array) $db->loadObjectList() as $row) {
            self::$_counts[(int) $row->topic_id] = (float) $row->selected;
        }
    }
}
