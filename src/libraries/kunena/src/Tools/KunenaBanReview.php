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
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Throwable;

/**
 * The bans which need a look (tool "Ban review"): the bans which end soon, the permanent bans without a reason, and the
 * banned users who still moderate or still get the e-mails of their subscriptions.
 *
 * @since   Kunena 8.0
 */
class KunenaBanReview
{
    /**
     * A ban which ends within these days ends soon.
     *
     * @since   Kunena 8.0
     */
    public const SOON = 7;

    /**
     * The expiration of a permanent ban.
     *
     * @since   Kunena 8.0
     */
    public const LIFETIME = '9999-12-31 23:59:59';

    /**
     * The problems of the active bans; a ban without a problem is left out.
     *
     * @param   array      $bans         id, userid, ip, expiration, reason_private, reason_public.
     * @param   int[]      $moderators   The users who moderate a category or all of them.
     * @param   int[]      $subscribers  The users with a subscription.
     * @param   int        $now          The current time.
     *
     * @return  array{active: int, items: array<int, array{ban: array, problems: string[]}>}
     *
     * @since   Kunena 8.0
     */
    public static function review(array $bans, array $moderators, array $subscribers, int $now): array
    {
        $moderators  = array_flip(array_map('intval', $moderators));
        $subscribers = array_flip(array_map('intval', $subscribers));
        $active      = 0;
        $items       = [];

        foreach ($bans as $ban) {
            $expiration = (string) $ban['expiration'];
            $lifetime   = $expiration === self::LIFETIME;
            $end        = $lifetime ? PHP_INT_MAX : (int) strtotime($expiration . ' UTC');

            if (!$lifetime && ($expiration === '' || $end <= $now)) {
                continue;
            }

            $active++;
            $user     = (int) $ban['userid'];
            $problems = [];

            if (!$lifetime && $end - $now <= self::SOON * 86400) {
                $problems[] = 'soon';
            }

            if ($lifetime && trim((string) $ban['reason_private']) === '' && trim((string) $ban['reason_public']) === '') {
                $problems[] = 'noreason';
            }

            if ($user > 0 && isset($moderators[$user])) {
                $problems[] = 'moderator';
            }

            if ($user > 0 && isset($subscribers[$user])) {
                $problems[] = 'subscribed';
            }

            if ($problems) {
                $items[] = ['ban' => $ban, 'problems' => $problems, 'end' => $lifetime ? 0 : $end];
            }
        }

        // The bans which end first, then the permanent ones.
        usort($items, static fn ($a, $b) => [$a['end'] ?: PHP_INT_MAX, (int) $a['ban']['id']] <=> [$b['end'] ?: PHP_INT_MAX, (int) $b['ban']['id']]);

        return ['active' => $active, 'items' => $items];
    }

    /**
     * The review of the bans of this site.
     *
     * @param   int  $now  The current time.
     *
     * @return  array{active: int, items: array, names: array<int, string>}
     *
     * @since   Kunena 8.0
     */
    public static function reviewOnSite(int $now): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'userid', 'ip', 'blocked', 'expiration', 'created_time', 'reason_private', 'reason_public']))
            ->from($db->quoteName('#__kunena_users_banned'))
            ->order($db->quoteName('id') . ' DESC');
        $bans = (array) $db->setQuery($query)->loadAssocList();

        $users = array_values(array_unique(array_filter(array_map('intval', array_column($bans, 'userid')))));
        $mods  = [];
        $subs  = [];
        $names = [];

        if ($users) {
            $query = $db->createQuery()->select('DISTINCT ' . $db->quoteName('user_id'))->from($db->quoteName('#__kunena_user_categories'))
                ->where($db->quoteName('role') . ' = 1')->whereIn($db->quoteName('user_id'), $users);
            $mods = (array) $db->setQuery($query)->loadColumn();

            foreach (['#__kunena_user_categories', '#__kunena_user_topics'] as $table) {
                $query = $db->createQuery()->select('DISTINCT ' . $db->quoteName('user_id'))->from($db->quoteName($table))
                    ->where($db->quoteName('subscribed') . ' = 1')->whereIn($db->quoteName('user_id'), $users);
                $subs = array_merge($subs, (array) $db->setQuery($query)->loadColumn());
            }

            $query = $db->createQuery()->select($db->quoteName(['id', 'username']))->from($db->quoteName('#__users'))->whereIn($db->quoteName('id'), $users);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'username'));
        }

        return self::review($bans, $mods, $subs, $now) + ['names' => $names];
    }

    /**
     * Takes the moderator rights and the subscriptions of banned users away.
     *
     * @param   int[]  $ids  The users.
     *
     * @return  array{moderator: int, subscriptions: int}  The rights and the subscriptions taken away.
     *
     * @since   Kunena 8.0
     */
    public static function cleanOnSite(array $ids): array
    {
        $result = ['moderator' => 0, 'subscriptions' => 0];
        $ids    = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!$ids) {
            return $result;
        }

        // Only users who are banned now.
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $now    = Factory::getDate()->toSql();
        $query  = $db->createQuery()
            ->select('DISTINCT ' . $db->quoteName('userid'))
            ->from($db->quoteName('#__kunena_users_banned'))
            ->whereIn($db->quoteName('userid'), $ids)
            ->andWhere([$db->quoteName('expiration') . ' = ' . $db->quote(self::LIFETIME), $db->quoteName('expiration') . ' > ' . $db->quote($now)], 'OR');
        $banned = array_map('intval', (array) $db->setQuery($query)->loadColumn());

        if (!$banned) {
            return $result;
        }

        $query = $db->createQuery()
            ->select($db->quoteName(['user_id', 'category_id']))
            ->from($db->quoteName('#__kunena_user_categories'))
            ->where($db->quoteName('role') . ' = 1')
            ->whereIn($db->quoteName('user_id'), $banned);
        $access = KunenaAccess::getInstance();

        foreach ((array) $db->setQuery($query)->loadAssocList() as $row) {
            try {
                $category = (int) $row['category_id'] > 0 ? KunenaCategoryHelper::get((int) $row['category_id']) : null;

                if ($access->setModerator($category, (int) $row['user_id'], false)) {
                    $result['moderator']++;
                }
            } catch (Throwable $e) {
                // A category which is gone: its row is cleaned by Diagnostics.
            }
        }

        foreach ($banned as $id) {
            $result['subscriptions'] += KunenaSubscriptions::unsubscribeOnSite('user', $id);
        }

        return $result;
    }
}
