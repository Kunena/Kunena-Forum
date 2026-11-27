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

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Throwable;

/**
 * The accounts nobody uses (tool "Inactive accounts"): registered long ago and never posted (mostly spam registrations), or
 * not seen for years. Their subscriptions are removed, or they are blocked or deleted, in batches. The administrators and
 * the moderators of the forum and the Super Users are never listed.
 *
 * @since   Kunena 8.0
 */
class KunenaInactiveUsers
{
    /**
     * Registered long ago and never posted.
     *
     * @since   Kunena 8.0
     */
    public const NEVER_POSTED = 'never_posted';

    /**
     * Not seen for a long time.
     *
     * @since   Kunena 8.0
     */
    public const NOT_SEEN = 'not_seen';

    /**
     * At most this many accounts in a list and in one run of an action.
     *
     * @since   Kunena 8.0
     */
    public const BATCH = 100;

    /**
     * The actions.
     *
     * @since   Kunena 8.0
     */
    public const ACTIONS = ['unsubscribe', 'block', 'delete'];

    /**
     * The criteria from a request.
     *
     * @param   array  $input  The request.
     *
     * @return  array{kind: string, months: int}
     *
     * @since   Kunena 8.0
     */
    public static function criteria(array $input): array
    {
        $kind   = (string) ($input['kind'] ?? '');
        $months = (int) ($input['months'] ?? 0);

        return [
            'kind'   => $kind === self::NOT_SEEN ? self::NOT_SEEN : self::NEVER_POSTED,
            'months' => \in_array($months, [3, 6, 12, 24, 36, 60], true) ? $months : 12,
        ];
    }

    /**
     * The accounts which match, without the staff: their number and the first BATCH of them.
     *
     * @param   array  $criteria  The criteria (criteria()).
     *
     * @return  array{total: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(array $criteria, string $action = ''): array
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $before = Factory::getDate('-' . $criteria['months'] . ' months')->toSql();
        $staff  = self::staff();

        // Without the roles nobody is listed, so no account is touched (the staff would be among the others).
        if ($staff === null) {
            return ['total' => 0, 'items' => []];
        }

        $query  = $db->createQuery()
            ->select($db->quoteName(['u.id', 'u.username', 'u.name', 'u.email', 'u.block', 'u.registerDate', 'u.lastvisitDate']))
            ->select('COALESCE(' . $db->quoteName('ku.posts') . ', 0) AS ' . $db->quoteName('posts'))
            ->from($db->quoteName('#__users', 'u'))
            ->leftJoin($db->quoteName('#__kunena_users', 'ku') . ' ON ' . $db->quoteName('ku.userid') . ' = ' . $db->quoteName('u.id'))
            ->bind(':before', $before)
            ->order($db->quoteName('u.registerDate'));

        if ($criteria['kind'] === self::NEVER_POSTED) {
            $query->where($db->quoteName('u.registerDate') . ' < :before')
                ->where('COALESCE(' . $db->quoteName('ku.posts') . ', 0) = 0')
                // No message at all, the unapproved and the trashed ones too.
                ->where('NOT EXISTS (SELECT 1 FROM ' . $db->quoteName('#__kunena_messages') . ' AS m WHERE m.userid = u.id)');
        } else {
            $query->where('COALESCE(' . $db->quoteName('u.lastvisitDate') . ', ' . $db->quoteName('u.registerDate') . ') < :before');
        }

        if ($staff) {
            $query->whereNotIn($db->quoteName('u.id'), $staff);
        }

        // An action takes the accounts which it still changes: the batch was always the first one of the list, so a run
        // again blocked or unsubscribed the same accounts, and the ones after the first batch were never reached.
        if ($action === 'block') {
            $query->where($db->quoteName('u.block') . ' = 0');
        } elseif ($action === 'unsubscribe') {
            $query->where('(EXISTS (SELECT 1 FROM ' . $db->quoteName('#__kunena_user_categories') . ' AS uc WHERE uc.user_id = u.id AND uc.subscribed <> 0)'
                . ' OR EXISTS (SELECT 1 FROM ' . $db->quoteName('#__kunena_user_topics') . ' AS ut WHERE ut.user_id = u.id AND ut.subscribed <> 0))');
        }

        $count = clone $query;
        $count->clear('select')->clear('order')->select('COUNT(*)');

        return [
            'total' => (int) $db->setQuery($count)->loadResult(),
            'items' => (array) $db->setQuery($query, 0, self::BATCH)->loadAssocList('id'),
        ];
    }

    /**
     * Applies an action to the accounts which match now (never the ids of a request): at most BATCH of them.
     *
     * @param   array     $criteria  The criteria.
     * @param   string    $action    One of ACTIONS.
     * @param   callable  $allowed   fn(int $userid): bool, may the current user do it to this account?
     *
     * @return  array{done: int, skipped: int, left: int}
     *
     * @since   Kunena 8.0
     */
    public static function applyOnSite(array $criteria, string $action, callable $allowed): array
    {
        $result = ['done' => 0, 'skipped' => 0, 'left' => 0];

        if (!\in_array($action, self::ACTIONS, true)) {
            return $result;
        }

        $found   = self::findOnSite($criteria, $action);
        $factory = Factory::getContainer()->get(UserFactoryInterface::class);
        $db      = Factory::getContainer()->get('DatabaseDriver');

        foreach (array_keys($found['items']) as $id) {
            $id = (int) $id;

            if (!$allowed($id)) {
                $result['skipped']++;

                continue;
            }

            try {
                if ($action === 'unsubscribe') {
                    foreach (['#__kunena_user_categories', '#__kunena_user_topics'] as $table) {
                        $query = $db->createQuery()
                            ->update($db->quoteName($table))
                            ->set($db->quoteName('subscribed') . ' = 0')
                            ->where($db->quoteName('user_id') . ' = :id')
                            ->bind(':id', $id, ParameterType::INTEGER);
                        $db->setQuery($query)->execute();
                    }

                    $ok = true;
                } elseif ($action === 'block') {
                    $user        = $factory->loadUserById($id);
                    $user->block = 1;
                    $ok          = $user->id && $user->save(true);
                } else {
                    $user = $factory->loadUserById($id);
                    $ok   = $user->id && $user->delete();
                }
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'done' : 'skipped']++;
        }

        // What is left: the accounts which the action still has to change.
        $result['left'] = max(0, $found['total'] - $result['done']);

        return $result;
    }

    /**
     * The administrators and the moderators of the forum, and the Super Users.
     *
     * @return  int[]|null  Null when the roles could not be read.
     *
     * @since   Kunena 8.0
     */
    private static function staff(): ?array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $staff = [];

        try {
            // The administrators and the moderators of the forum, of each category and of all of them (0).
            $access = KunenaAccess::getInstance();
            $query  = $db->createQuery()->select($db->quoteName('id'))->from($db->quoteName('#__kunena_categories'));

            foreach (array_merge([0], array_map('intval', (array) $db->setQuery($query)->loadColumn())) as $catid) {
                $staff += $access->getAdmins($catid) + $access->getModerators($catid);
            }

            // The Super Users: the members of the groups which have core.admin (a few groups, not every account).
            $query = $db->createQuery()->select($db->quoteName('id'))->from($db->quoteName('#__usergroups'));

            foreach (array_map('intval', (array) $db->setQuery($query)->loadColumn()) as $group) {
                if (Access::checkGroup($group, 'core.admin')) {
                    $staff += array_flip(array_map('intval', Access::getUsersByGroup($group, true)));
                }
            }
        } catch (Throwable $e) {
            // Without the roles, nobody is listed: no account is touched. (It gave [0], which left out nobody.)
            return null;
        }

        return array_map('intval', array_keys($staff));
    }
}
