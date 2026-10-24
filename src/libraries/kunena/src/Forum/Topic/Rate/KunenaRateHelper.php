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
     * @since   Kunena 7.2
     */
    protected static array $_counts = [];

    /**
     * Forget the loaded number of ratings of a topic (after a new rating).
     *
     * @param   int  $id  Topic id.
     *
     * @return  void
     *
     * @since   Kunena 7.2
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
     * @param   null  $identifier  identifier
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
     * @since   Kunena 7.2
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
