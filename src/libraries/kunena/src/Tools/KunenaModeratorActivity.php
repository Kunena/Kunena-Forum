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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Log\KunenaLog;

/**
 * What the moderators do (tool "Moderator activity"): the moderation actions of the log of Kunena of each moderator over a
 * period, the moderators who did nothing, and the posts which wait for a moderator. The log must be on (Configuration,
 * Logging) for the actions to be counted.
 *
 * @since   Kunena 8.0
 */
class KunenaModeratorActivity
{
    /**
     * The periods, in days.
     *
     * @since   Kunena 8.0
     */
    public const PERIODS = [7, 30, 90, 365];

    /**
     * The actions per user and operation, summed: user => [total, last, operations (operation => count, the most first)].
     *
     * @param   array  $rows  user_id, operation, count, last.
     *
     * @return  array<int, array{total: int, last: int, operations: array<string, int>}>  The most active first.
     *
     * @since   Kunena 8.0
     */
    public static function summarize(array $rows): array
    {
        $users = [];

        foreach ($rows as $row) {
            $user  = (int) $row['user_id'];
            $count = (int) $row['count'];

            $users[$user] ??= ['total' => 0, 'last' => 0, 'operations' => []];
            $users[$user]['total'] += $count;
            $users[$user]['last']                                   = max($users[$user]['last'], (int) $row['last']);
            $users[$user]['operations'][(string) $row['operation']] = ($users[$user]['operations'][(string) $row['operation']] ?? 0) + $count;
        }

        foreach ($users as &$user) {
            arsort($user['operations']);
        }

        unset($user);
        uasort($users, static fn ($a, $b) => $b['total'] <=> $a['total']);

        return $users;
    }

    /**
     * The activity of the moderators of this site over the last days.
     *
     * @param   int  $days  The period (PERIODS).
     * @param   int  $now   The current time.
     *
     * @return  array{days: int, users: array, moderators: array, idle: array, names: array<int, string>, queue: array{count: int, oldest: int}, logging: bool}
     *
     * @since   Kunena 8.0
     */
    public static function reportOnSite(int $days, int $now): array
    {
        $days  = \in_array($days, self::PERIODS, true) ? $days : self::PERIODS[1];
        $since = $now - $days * 86400;
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $type  = KunenaLog::TYPE_MODERATION;

        $query = $db->createQuery()
            ->select($db->quoteName(['user_id', 'operation']))
            ->select('COUNT(*) AS ' . $db->quoteName('count'))
            ->select('MAX(' . $db->quoteName('time') . ') AS ' . $db->quoteName('last'))
            ->from($db->quoteName('#__kunena_logs'))
            ->where($db->quoteName('type') . ' = :type')
            ->where($db->quoteName('time') . ' >= :since')
            ->where($db->quoteName('user_id') . ' > 0')
            ->bind(':type', $type, ParameterType::INTEGER)
            ->bind(':since', $since, ParameterType::INTEGER)
            ->group($db->quoteName(['user_id', 'operation']));
        $users = self::summarize((array) $db->setQuery($query)->loadAssocList());

        // The moderators of categories and the global ones (category 0), with the number of their categories.
        $query = $db->createQuery()
            ->select($db->quoteName('user_id'))
            ->select('SUM(' . $db->quoteName('category_id') . ' > 0) AS ' . $db->quoteName('categories'))
            ->select('MAX(' . $db->quoteName('category_id') . ' = 0) AS ' . $db->quoteName('global'))
            ->from($db->quoteName('#__kunena_user_categories'))
            ->where($db->quoteName('role') . ' = 1')
            ->group($db->quoteName('user_id'));
        $moderators = (array) $db->setQuery($query)->loadAssocList('user_id');
        $idle       = array_diff_key($moderators, $users);

        // The names of all of them.
        $ids   = array_map('intval', array_unique(array_merge(array_keys($users), array_keys($moderators))));
        $names = [];

        if ($ids) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'username']))->from($db->quoteName('#__users'))->whereIn($db->quoteName('id'), $ids);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'username'));
        }

        // The posts which wait for a moderator (a scheduled reply waits for its date, not for a moderator).
        $query = $db->createQuery()
            ->select('COUNT(*) AS ' . $db->quoteName('count'))
            ->select('MIN(' . $db->quoteName('time') . ') AS ' . $db->quoteName('oldest'))
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('hold') . ' = 1')
            ->where($db->quoteName('scheduled_time') . ' = 0');
        $queue = (array) $db->setQuery($query)->loadAssoc();

        return [
            'days'       => $days,
            'users'      => $users,
            'moderators' => $moderators,
            'idle'       => $idle,
            'names'      => $names,
            'queue'      => ['count' => (int) ($queue['count'] ?? 0), 'oldest' => (int) ($queue['oldest'] ?? 0)],
            'logging'    => (bool) (KunenaConfig::getInstance()->logModeration ?? true),
        ];
    }
}
