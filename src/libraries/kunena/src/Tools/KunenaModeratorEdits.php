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
 * The posts edited by somebody else than their author (tool "Moderator edits"): who edited whose post, when and why, and
 * the edits without a reason. The last edit of each post is read (the post keeps who edited it last).
 *
 * @since   Kunena 8.0
 */
class KunenaModeratorEdits
{
    /**
     * The periods, in days.
     *
     * @since   Kunena 8.0
     */
    public const PERIODS = [7, 30, 90, 365];

    /**
     * The edits shown.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 100;

    /**
     * The editors with their edits and their edits without a reason, the most first.
     *
     * @param   array  $rows  modified_by, modified_reason.
     *
     * @return  array<int, array{edits: int, noreason: int}>
     *
     * @since   Kunena 8.0
     */
    public static function editors(array $rows): array
    {
        $editors = [];

        foreach ($rows as $row) {
            $editor = (int) $row['modified_by'];

            $editors[$editor] ??= ['edits' => 0, 'noreason' => 0];
            $editors[$editor]['edits']++;

            if (trim((string) $row['modified_reason']) === '') {
                $editors[$editor]['noreason']++;
            }
        }

        uksort($editors, static fn ($a, $b) => [$editors[$b]['edits'], $a] <=> [$editors[$a]['edits'], $b]);

        return $editors;
    }

    /**
     * The edits of this site over the last days, the newest first.
     *
     * @param   int   $days      One of PERIODS.
     * @param   int   $now       The current time.
     * @param   bool  $noReason  Only the edits without a reason.
     *
     * @return  array{days: int, noreason: bool, total: int, editors: array, edits: array, names: array<int, string>}
     *
     * @since   Kunena 8.0
     */
    public static function reportOnSite(int $days, int $now, bool $noReason = false): array
    {
        $days  = \in_array($days, self::PERIODS, true) ? $days : self::PERIODS[1];
        $since = $now - $days * 86400;
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.thread', 'm.catid', 'm.subject', 'm.userid', 'm.name', 'm.modified_by', 'm.modified_time', 'm.modified_reason']))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->where($db->quoteName('m.modified_by') . ' > 0')
            ->where($db->quoteName('m.modified_by') . ' <> ' . $db->quoteName('m.userid'))
            ->where($db->quoteName('m.modified_time') . ' >= :since')
            ->bind(':since', $since, ParameterType::INTEGER)
            ->order($db->quoteName('m.modified_time') . ' DESC');
        $rows = (array) $db->setQuery($query)->loadAssocList();

        $editors = self::editors($rows);

        if ($noReason) {
            $rows = array_values(array_filter($rows, static fn ($row) => trim((string) $row['modified_reason']) === ''));
        }

        $edits = \array_slice($rows, 0, self::LIMIT);
        $users = array_values(array_unique(array_filter(array_merge(array_keys($editors), array_map('intval', array_column($edits, 'userid'))))));
        $names = [];

        if ($users) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'username']))->from($db->quoteName('#__users'))->whereIn($db->quoteName('id'), $users);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'username'));
        }

        return ['days' => $days, 'noreason' => $noReason, 'total' => \count($rows), 'editors' => $editors, 'edits' => $edits, 'names' => $names];
    }
}
