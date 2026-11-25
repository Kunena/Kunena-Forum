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
use Kunena\Forum\Libraries\Forum\Topic\Poll\KunenaPollHelper;
use Throwable;

/**
 * All the polls of the forum in one list (tool "Polls overview"): open or ended, their votes and their topic; a poll is
 * closed now or deleted with its votes. A poll could only be handled in its topic.
 *
 * @since   Kunena 8.0
 */
class KunenaPollsOverview
{
    /**
     * The filters.
     *
     * @since   Kunena 8.0
     */
    public const STATES = ['all', 'open', 'ended', 'novotes'];

    /**
     * At most this many polls, the newest first.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 500;

    /**
     * The moment a poll ends, from the date which Kunena keeps (UTC); 0 for a poll without an end.
     *
     * @param   string|null  $date  The date (polltimetolive).
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function endsAt(?string $date): int
    {
        $time = $date ? strtotime($date . ' UTC') : false;

        // An empty date of the database (1000-01-01, 0000-00-00) is no end.
        return $time !== false && $time > 0 ? $time : 0;
    }

    /**
     * Does a poll match the filter?
     *
     * @param   string  $state  The filter (STATES).
     * @param   int     $ends   endsAt().
     * @param   int     $votes  Its votes.
     * @param   int     $now    The current time.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function matches(string $state, int $ends, int $votes, int $now): bool
    {
        $ended = $ends > 0 && $ends <= $now;

        return match ($state) {
            'open'    => !$ended,
            'ended'   => $ended,
            'novotes' => $votes === 0,
            default   => true,
        };
    }

    /**
     * The polls of this site which match the filter, the newest first.
     *
     * @param   string  $state  The filter.
     * @param   int     $now    The current time.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function listOnSite(string $state, int $now): array
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $options = $db->createQuery()
            ->select($db->quoteName('pollid'))
            ->select('COUNT(*) AS ' . $db->quoteName('options'))
            ->select('COALESCE(SUM(' . $db->quoteName('votes') . '), 0) AS ' . $db->quoteName('votes'))
            ->from($db->quoteName('#__kunena_polls_options'))
            ->group($db->quoteName('pollid'));
        $voters = $db->createQuery()
            ->select($db->quoteName('pollid'))
            ->select('COUNT(*) AS ' . $db->quoteName('voters'))
            ->from($db->quoteName('#__kunena_polls_users'))
            ->group($db->quoteName('pollid'));
        $query = $db->createQuery()
            ->select($db->quoteName(['p.id', 'p.title', 'p.threadid', 'p.polltimetolive', 't.subject', 't.category_id', 't.hold', 'c.name']))
            ->select('COALESCE(' . $db->quoteName('o.options') . ', 0) AS ' . $db->quoteName('options'))
            ->select('COALESCE(' . $db->quoteName('o.votes') . ', 0) AS ' . $db->quoteName('votes'))
            ->select('COALESCE(' . $db->quoteName('v.voters') . ', 0) AS ' . $db->quoteName('voters'))
            ->from($db->quoteName('#__kunena_polls', 'p'))
            ->leftJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('p.threadid'))
            ->leftJoin($db->quoteName('#__kunena_categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('t.category_id'))
            ->leftJoin('(' . $options . ') AS ' . $db->quoteName('o') . ' ON ' . $db->quoteName('o.pollid') . ' = ' . $db->quoteName('p.id'))
            ->leftJoin('(' . $voters . ') AS ' . $db->quoteName('v') . ' ON ' . $db->quoteName('v.pollid') . ' = ' . $db->quoteName('p.id'))
            ->order($db->quoteName('p.id') . ' DESC');

        $list = [];

        foreach ((array) $db->setQuery($query, 0, self::LIST_LIMIT)->loadAssocList() as $row) {
            $ends = self::endsAt((string) $row['polltimetolive']);

            if (self::matches($state, $ends, (int) $row['votes'], $now)) {
                $list[] = $row + ['ends' => $ends, 'ended' => $ends > 0 && $ends <= $now];
            }
        }

        return $list;
    }

    /**
     * Ends the chosen open polls now.
     *
     * @param   int[]  $ids  The polls.
     * @param   int    $now  The current time.
     *
     * @return  integer  The polls closed.
     *
     * @since   Kunena 8.0
     */
    public static function closeOnSite(array $ids, int $now): int
    {
        $closed = 0;
        $date   = gmdate('Y-m-d H:i:s', $now);
        $db     = Factory::getContainer()->get('DatabaseDriver');

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            $poll = KunenaPollHelper::get($id);

            if (!$poll->exists()) {
                continue;
            }

            $ends = self::endsAt((string) $poll->polltimetolive);

            if ($ends > 0 && $ends <= $now) {
                continue;
            }

            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_polls'))
                ->set($db->quoteName('polltimetolive') . ' = :date')
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':date', $date)
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
            $closed++;
        }

        return $closed;
    }

    /**
     * Deletes the chosen polls with their options and votes (the topics stay, without a poll).
     *
     * @param   int[]  $ids  The polls.
     *
     * @return  array{deleted: int, failed: int}
     *
     * @since   Kunena 8.0
     */
    public static function deleteOnSite(array $ids): array
    {
        $result = ['deleted' => 0, 'failed' => 0];

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            try {
                $poll = KunenaPollHelper::get($id);
                $ok   = $poll->exists() && $poll->delete();
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'deleted' : 'failed']++;
        }

        return $result;
    }
}
