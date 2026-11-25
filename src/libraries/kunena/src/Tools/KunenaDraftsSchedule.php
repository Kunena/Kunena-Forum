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
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageSchedule;

/**
 * The drafts and the scheduled replies of all the users (tool "Drafts and scheduled replies"): the drafts nobody touched for
 * a long time are deleted, and a scheduled reply is published now, with the checks of the scheduled task.
 *
 * @since   Kunena 8.0
 */
class KunenaDraftsSchedule
{
    /**
     * The filter of the drafts: not changed for so many days (0: all).
     *
     * @since   Kunena 8.0
     */
    public const AGES = [0, 30, 90, 180, 365];

    /**
     * At most this many drafts are listed.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 500;

    /**
     * The moment a draft was last changed.
     *
     * @param   array  $draft  created_time, modified_time.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function lastChange(array $draft): int
    {
        return max((int) ($draft['created_time'] ?? 0), (int) ($draft['modified_time'] ?? 0));
    }

    /**
     * The drafts of this site not changed for so many days, the oldest first.
     *
     * @param   int  $age  The days (AGES).
     * @param   int  $now  The current time.
     *
     * @return  array{total: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function draftsOnSite(int $age, int $now): array
    {
        $age    = \in_array($age, self::AGES, true) ? $age : 0;
        $before = $now - $age * 86400;
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $last   = 'GREATEST(' . $db->quoteName('d.created_time') . ', ' . $db->quoteName('d.modified_time') . ')';
        $query  = $db->createQuery()
            ->from($db->quoteName('#__kunena_drafts', 'd'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('d.user_id'))
            ->leftJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('d.topic_id'))
            ->where($last . ' <= :before')
            ->bind(':before', $before, ParameterType::INTEGER);

        $count = clone $query;
        $total = (int) $db->setQuery($count->select('COUNT(*)'))->loadResult();

        $query->select($db->quoteName(['d.id', 'd.user_id', 'd.topic_id', 'd.message_id', 'd.subject', 'd.created_time', 'd.modified_time', 'u.username']))
            ->select($db->quoteName('t.subject', 'topic'))
            ->order($last . ' ASC');

        return ['total' => $total, 'items' => (array) $db->setQuery($query, 0, self::LIST_LIMIT)->loadAssocList()];
    }

    /**
     * Deletes the chosen drafts.
     *
     * @param   int[]  $ids  The drafts.
     *
     * @return  integer  The drafts deleted.
     *
     * @since   Kunena 8.0
     */
    public static function deleteDraftsOnSite(array $ids): int
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!$ids) {
            return 0;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $db->setQuery($db->createQuery()->delete($db->quoteName('#__kunena_drafts'))->whereIn($db->quoteName('id'), $ids))->execute();

        return $db->getAffectedRows();
    }

    /**
     * The scheduled replies of this site, the next first.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function scheduledOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.thread', 'm.catid', 'm.subject', 'm.userid', 'm.name', 'm.scheduled_time', 'm.scheduled_review']))
            ->select($db->quoteName('t.subject', 'topic'))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->leftJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('m.thread'))
            ->where($db->quoteName('m.hold') . ' = 1')
            ->where($db->quoteName('m.scheduled_time') . ' > 0')
            ->order($db->quoteName('m.scheduled_time') . ' ASC');

        return (array) $db->setQuery($query, 0, self::LIST_LIMIT)->loadAssocList();
    }

    /**
     * Publishes the chosen scheduled replies now, with the checks of the scheduled task (the topic is open, the author may
     * still reply...). A reply which waits for a moderator is left: it is published by the moderator.
     *
     * @param   int[]  $ids  The replies.
     * @param   int    $now  The current time.
     *
     * @return  array{published: int, skipped: int}
     *
     * @since   Kunena 8.0
     */
    public static function publishNowOnSite(array $ids, int $now): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!$ids) {
            return ['published' => 0, 'skipped' => 0];
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->update($db->quoteName('#__kunena_messages'))
            ->set($db->quoteName('scheduled_time') . ' = :now')
            ->whereIn($db->quoteName('id'), $ids)
            ->where($db->quoteName('hold') . ' = 1')
            ->where($db->quoteName('scheduled_time') . ' > :now2')
            ->where($db->quoteName('scheduled_review') . ' = 0')
            ->bind(':now', $now, ParameterType::INTEGER)
            ->bind(':now2', $now, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        // The replies which are due are published as by the scheduled task: the chosen ones, and the ones which were already due.
        return KunenaMessageSchedule::publishDue($now, self::LIST_LIMIT);
    }
}
