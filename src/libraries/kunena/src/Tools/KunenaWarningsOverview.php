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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserWarnings;
use Throwable;

/**
 * The warnings of the users (tool "Warnings overview"): the users with the most active warning points, the warnings of a
 * period, the moderators who give them, and the users who reached the points of a ban without being banned (the warnings
 * were given before the ban was switched on, or the ban was lifted).
 *
 * @since   Kunena 8.0
 */
class KunenaWarningsOverview
{
    /**
     * The periods, in days.
     *
     * @since   Kunena 8.0
     */
    public const PERIODS = [30, 90, 365];

    /**
     * The users with their active points, the most first; a user without active points is left out.
     *
     * @param   array  $rows       user_id, points, expires_time, created_time.
     * @param   int[]  $banned     The users who are banned now.
     * @param   int    $threshold  The points of a ban (0: no ban).
     * @param   int    $now        The current time.
     *
     * @return  array<int, array{points: int, warnings: int, last: int, reaches: bool}>
     *
     * @since   Kunena 8.0
     */
    public static function users(array $rows, array $banned, int $threshold, int $now): array
    {
        $byUser = [];

        foreach ($rows as $row) {
            $byUser[(int) $row['user_id']][] = (object) $row;
        }

        $banned = array_flip(array_map('intval', $banned));
        $users  = [];

        foreach ($byUser as $user => $warnings) {
            $points = KunenaUserWarnings::activePoints($warnings, $now);

            if ($user <= 0 || $points <= 0) {
                continue;
            }

            $users[$user] = [
                'points'   => $points,
                'warnings' => \count($warnings),
                'last'     => max(array_map(static fn ($warning) => (int) $warning->created_time, $warnings)),
                'reaches'  => KunenaUserWarnings::reachesBan($points, $threshold, isset($banned[$user])),
            ];
        }

        uksort($users, static fn ($a, $b) => [$users[$b]['points'], $users[$b]['last']] <=> [$users[$a]['points'], $users[$a]['last']]);

        return $users;
    }

    /**
     * The warnings of this site.
     *
     * @param   int  $days  The period of the list of warnings (PERIODS).
     * @param   int  $now   The current time.
     *
     * @return  array{days: int, enabled: bool, threshold: int, users: array, recent: array, moderators: array<int, int>, names: array<int, string>}
     *
     * @since   Kunena 8.0
     */
    public static function reportOnSite(int $days, int $now): array
    {
        $config    = KunenaConfig::getInstance();
        $days      = \in_array($days, self::PERIODS, true) ? $days : self::PERIODS[0];
        $threshold = (int) $config->warningBanPoints;
        $db        = Factory::getContainer()->get('DatabaseDriver');
        $query     = $db->createQuery()
            ->select($db->quoteName(['id', 'user_id', 'moderator_id', 'message_id', 'points', 'reason', 'created_time', 'expires_time']))
            ->from($db->quoteName('#__kunena_warnings'))
            ->order($db->quoteName('id') . ' DESC');
        $rows = (array) $db->setQuery($query)->loadAssocList();

        $ids    = array_values(array_unique(array_filter(array_map('intval', array_column($rows, 'user_id')))));
        $banned = [];

        if ($ids) {
            $sqlNow = Factory::getDate($now)->toSql();
            $query  = $db->createQuery()
                ->select('DISTINCT ' . $db->quoteName('userid'))
                ->from($db->quoteName('#__kunena_users_banned'))
                ->whereIn($db->quoteName('userid'), $ids)
                ->andWhere([$db->quoteName('expiration') . ' = ' . $db->quote(KunenaBanReview::LIFETIME), $db->quoteName('expiration') . ' > ' . $db->quote($sqlNow)], 'OR');
            $banned = (array) $db->setQuery($query)->loadColumn();
        }

        $since      = $now - $days * 86400;
        $recent     = array_values(array_filter($rows, static fn ($row) => (int) $row['created_time'] >= $since));
        $moderators = KunenaReportsOverview::top($recent, 'moderator_id');
        $users      = self::users($rows, $banned, $threshold, $now);
        $names      = [];
        $people     = array_values(array_unique(array_merge(array_keys($users), array_keys($moderators), array_map('intval', array_column($recent, 'user_id')))));

        if ($people) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'username']))->from($db->quoteName('#__users'))->whereIn($db->quoteName('id'), $people);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'username'));
        }

        return [
            'days'       => $days,
            'enabled'    => (bool) $config->warnings,
            'threshold'  => $threshold,
            'users'      => $users,
            'recent'     => \array_slice($recent, 0, KunenaReportsOverview::LIMIT),
            'moderators' => $moderators,
            'names'      => $names,
        ];
    }

    /**
     * Bans the chosen users who reached the points of a ban, as a new warning would have done.
     *
     * @param   int[]  $ids  The users.
     * @param   int    $now  The current time.
     *
     * @return  int  The users banned.
     *
     * @since   Kunena 8.0
     */
    public static function banOnSite(array $ids, int $now): int
    {
        $config    = KunenaConfig::getInstance();
        $threshold = (int) $config->warningBanPoints;
        $banned    = 0;

        if ($threshold <= 0) {
            return 0;
        }

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            try {
                $user   = KunenaUserHelper::get($id);
                $points = KunenaUserWarnings::activePoints(KunenaUserWarnings::getWarnings($id), $now);

                if ($user->exists() && KunenaUserWarnings::banIfReached($user, $points, $threshold, (int) $config->warningBanDays)) {
                    $banned++;
                }
            } catch (Throwable $e) {
                // A user who cannot be banned (an administrator) stays as he is.
            }
        }

        return $banned;
    }
}
