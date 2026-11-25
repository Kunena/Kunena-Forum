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
 * The access of one category given to others (tool "Copy category permissions"): who may read (the access level or the
 * groups of Joomla), and optionally its moderators, after a preview of what changes. The Access overview shows the
 * differences; this removes them without editing each category.
 *
 * @since   Kunena 8.0
 */
class KunenaCategoryPermissions
{
    /**
     * The fields of the access of a category.
     *
     * @since   Kunena 8.0
     */
    public const FIELDS = ['accesstype', 'access', 'pubAccess', 'pubRecurse', 'adminAccess', 'adminRecurse'];

    /**
     * The fields of a category which another one would get: field => [from, to].
     *
     * @param   array  $source  The category to copy.
     * @param   array  $target  The category which gets them.
     *
     * @return  array<string, array{from: string, to: string}>
     *
     * @since   Kunena 8.0
     */
    public static function differences(array $source, array $target): array
    {
        $changes = [];

        foreach (self::FIELDS as $field) {
            $from = (string) ($target[$field] ?? '');
            $to   = (string) ($source[$field] ?? '');

            if ($from !== $to) {
                $changes[$field] = ['from' => $from, 'to' => $to];
            }
        }

        return $changes;
    }

    /**
     * The moderators which a category gets and loses.
     *
     * @param   int[]  $source  The moderators of the category to copy.
     * @param   int[]  $target  The moderators of the category which gets them.
     *
     * @return  array{add: int[], remove: int[]}
     *
     * @since   Kunena 8.0
     */
    public static function moderatorChanges(array $source, array $target): array
    {
        $source = array_values(array_unique(array_map('intval', $source)));
        $target = array_values(array_unique(array_map('intval', $target)));

        return ['add' => array_values(array_diff($source, $target)), 'remove' => array_values(array_diff($target, $source))];
    }

    /**
     * The names of the access levels and the user groups of this site, and of users, to show the values.
     *
     * @param   int[]  $users  The users to name.
     *
     * @return  array{levels: array<int, string>, groups: array<int, string>, users: array<int, string>}
     *
     * @since   Kunena 8.0
     */
    public static function namesOnSite(array $users): array
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $levels = (array) $db->setQuery($db->createQuery()->select($db->quoteName(['id', 'title']))->from($db->quoteName('#__viewlevels')))->loadAssocList('id', 'title');
        $groups = (array) $db->setQuery($db->createQuery()->select($db->quoteName(['id', 'title']))->from($db->quoteName('#__usergroups')))->loadAssocList('id', 'title');
        $names  = [];
        $users  = array_values(array_filter(array_map('intval', $users)));

        if ($users) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'username']))->from($db->quoteName('#__users'))->whereIn($db->quoteName('id'), $users);
            $names = (array) $db->setQuery($query)->loadAssocList('id', 'username');
        }

        return ['levels' => $levels, 'groups' => $groups, 'users' => $names];
    }

    /**
     * The value of a field of the access as people read it.
     *
     * @param   string  $field  The field (FIELDS).
     * @param   string  $value  The value.
     * @param   array   $names  namesOnSite().
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function label(string $field, string $value, array $names): string
    {
        return match ($field) {
            'access'                     => (string) ($names['levels'][(int) $value] ?? '#' . $value),
            'pubAccess', 'adminAccess'   => (int) $value > 0 ? (string) ($names['groups'][(int) $value] ?? '#' . $value) : '—',
            'pubRecurse', 'adminRecurse' => (int) $value ? '✓' : '✗',
            default                      => $value,
        };
    }

    /**
     * The moderators of categories of this site (not the global ones): category => user ids.
     *
     * @param   int[]  $ids  The categories.
     *
     * @return  array<int, int[]>
     *
     * @since   Kunena 8.0
     */
    public static function moderatorsOnSite(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));

        if (!$ids) {
            return [];
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['category_id', 'user_id']))
            ->from($db->quoteName('#__kunena_user_categories'))
            ->where($db->quoteName('role') . ' = 1')
            ->whereIn($db->quoteName('category_id'), $ids);
        $list = array_fill_keys($ids, []);

        foreach ((array) $db->setQuery($query)->loadAssocList() as $row) {
            $list[(int) $row['category_id']][] = (int) $row['user_id'];
        }

        return $list;
    }

    /**
     * What copying a category to others changes on this site.
     *
     * @param   array  $rows        The categories (KunenaCategoryTransfer::rowsOnSite()), by id.
     * @param   int    $source      The category to copy.
     * @param   int[]  $targets     The categories which get its access.
     * @param   bool   $moderators  Copy the moderators too.
     *
     * @return  array<int, array{fields: array, add: int[], remove: int[]}>  Only the categories which change.
     *
     * @since   Kunena 8.0
     */
    public static function previewOnSite(array $rows, int $source, array $targets, bool $moderators): array
    {
        if (!isset($rows[$source])) {
            return [];
        }

        $targets = array_values(array_filter(array_unique(array_map('intval', $targets)), static fn ($id) => $id !== $source && isset($rows[$id])));
        $mods    = $moderators ? self::moderatorsOnSite(array_merge([$source], $targets)) : [];
        $preview = [];

        foreach ($targets as $id) {
            $fields  = self::differences($rows[$source], $rows[$id]);
            $changes = $moderators ? self::moderatorChanges($mods[$source] ?? [], $mods[$id] ?? []) : ['add' => [], 'remove' => []];

            if ($fields || $changes['add'] || $changes['remove']) {
                $preview[$id] = ['fields' => $fields] + $changes;
            }
        }

        return $preview;
    }

    /**
     * Copies the access of a category of this site to others.
     *
     * @param   int    $source      The category to copy.
     * @param   int[]  $targets     The categories which get its access.
     * @param   bool   $moderators  Copy the moderators too.
     *
     * @return  array{changed: int, failed: int}
     *
     * @since   Kunena 8.0
     */
    public static function copyOnSite(int $source, array $targets, bool $moderators): array
    {
        $rows    = array_column(KunenaCategoryTransfer::rowsOnSite(), null, 'id');
        $preview = self::previewOnSite($rows, $source, $targets, $moderators);
        $access  = KunenaAccess::getInstance();
        $result  = ['changed' => 0, 'failed' => 0];

        foreach ($preview as $id => $change) {
            try {
                $category = KunenaCategoryHelper::get($id);

                foreach ($change['fields'] as $field => $values) {
                    $category->$field = $values['to'];
                }

                $ok = !$change['fields'] || $category->save();

                foreach ($change['add'] as $user) {
                    $ok = $access->setModerator($category, $user, true) && $ok;
                }

                foreach ($change['remove'] as $user) {
                    $ok = $access->setModerator($category, $user, false) && $ok;
                }
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'changed' : 'failed']++;
        }

        $access->clearCache();

        return $result;
    }
}
