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
use Throwable;

/**
 * Who can read which category, for the whole forum at once (tool "Access overview"): the groups of Joomla of each
 * category (from its access level, or its groups), whether everybody can read it, its moderators, and the categories
 * which nobody can read. The Permission checker answers for one user; this answers for the forum.
 *
 * @since   Kunena 8.0
 */
class KunenaAccessOverview
{
    /**
     * The groups which can read a category, each with "and its child groups" or not.
     *
     * @param   array  $category  The category (accesstype, access, pubAccess, pubRecurse, adminAccess, adminRecurse).
     * @param   array  $levels    The access levels: id => list of group ids (their rules).
     *
     * @return  array<int, bool>  group id => the child groups too.
     *
     * @since   Kunena 8.0
     */
    public static function readers(array $category, array $levels): array
    {
        $type = (string) ($category['accesstype'] ?? 'joomla.level');

        if ($type === 'joomla.level') {
            $groups = [];

            // An access level of Joomla includes the child groups of its groups.
            foreach ($levels[(int) ($category['access'] ?? 0)] ?? [] as $group) {
                $groups[(int) $group] = true;
            }

            return $groups;
        }

        if ($type === 'joomla.group') {
            $groups = [];

            foreach ([['pubAccess', 'pubRecurse'], ['adminAccess', 'adminRecurse']] as [$field, $recurse]) {
                $group = (int) ($category[$field] ?? 0);

                if ($group > 0) {
                    $groups[$group] = ($groups[$group] ?? false) || !empty($category[$recurse]);
                }
            }

            return $groups;
        }

        // Another type of access (a plugin): not known here.
        return [];
    }

    /**
     * Can everybody, the guests too, read with these groups (the group at the top of the tree, with its children)?
     *
     * @param   array<int, bool>  $readers  The groups (readers()).
     * @param   array             $tree     The groups: id => parent id.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function everybody(array $readers, array $tree): bool
    {
        foreach ($readers as $group => $children) {
            if ($children && isset($tree[$group]) && (int) $tree[$group] === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * The categories of this site with their readers, in the order of the tree.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function listOnSite(): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query  = $db->createQuery()->select($db->quoteName(['id', 'title', 'parent_id']))->from($db->quoteName('#__usergroups'));
        $groups = (array) $db->setQuery($query)->loadAssocList('id');
        $tree   = array_map(static fn ($g) => (int) $g['parent_id'], $groups);

        $query  = $db->createQuery()->select($db->quoteName(['id', 'title', 'rules']))->from($db->quoteName('#__viewlevels'));
        $levels = [];
        $titles = [];

        foreach ((array) $db->setQuery($query)->loadAssocList() as $level) {
            $levels[(int) $level['id']] = array_map('intval', (array) json_decode((string) $level['rules'], true));
            $titles[(int) $level['id']] = (string) $level['title'];
        }

        $access = KunenaAccess::getInstance();
        $rows   = KunenaCategoryTransfer::sortTree(KunenaCategoryTransfer::rowsOnSite());
        $depth  = [];
        $list   = [];

        foreach ($rows as $row) {
            $id          = (int) $row['id'];
            $depth[$id]  = isset($depth[(int) $row['parentid']]) ? $depth[(int) $row['parentid']] + 1 : 0;
            $readers     = self::readers($row, $levels);
            $moderators  = 0;

            try {
                $moderators = \count($access->getModerators($id));
            } catch (Throwable $e) {
                // Shown as none.
            }

            $list[] = [
                'id'        => $id,
                'name'      => (string) $row['name'],
                'depth'     => $depth[$id],
                'published' => (int) $row['published'] === 1,
                'locked'    => (bool) $row['locked'],
                'type'      => (string) $row['accesstype'],
                'level'     => (string) $row['accesstype'] === 'joomla.level' ? ($titles[(int) $row['access']] ?? '#' . (int) $row['access']) : '',
                'readers'   => array_map(
                    static fn ($group, $children) => ['title' => (string) ($groups[$group]['title'] ?? '#' . $group), 'children' => $children],
                    array_keys($readers),
                    array_values($readers)
                ),
                'everybody'  => self::everybody($readers, $tree),
                'nobody'     => !$readers && \in_array((string) $row['accesstype'], ['joomla.level', 'joomla.group'], true),
                'moderators' => $moderators,
            ];
        }

        return $list;
    }
}
