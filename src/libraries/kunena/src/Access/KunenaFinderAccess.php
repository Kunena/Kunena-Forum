<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Access
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Access;

\defined('_JEXEC') or die();

/**
 * The access level which Smart Search stores for a message.
 *
 * Smart Search shows an item to a visitor when the access of the item is one of the view levels of the visitor, so it has to be
 * the id of a view level (1 Public, 2 Registered, ...). The plugin stored 1 when guests could read the category and 0 when they
 * could not: 0 is no view level, so the messages of a category for Registered users were hidden from the registered users too,
 * and 1 is Public, which is right only when the category is for everybody.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaFinderAccess
{
    /**
     * Nobody has this view level: the item is not shown.
     */
    public const NOBODY = 0;

    /**
     * @param   string   $accessType  accesstype of the category: joomla.level, joomla.group, ...
     * @param   integer  $access      The view level of a category of the type joomla.level.
     * @param   integer  $pubAccess   The group which may read a category of the type joomla.group.
     * @param   array    $levels      All view levels of the site: id => list of the groups of the level.
     * @param   array    $groups      For joomla.group: the rules of the category and the groups of the site, see groupLevel().
     *
     * @return  integer  The id of a view level, or NOBODY.
     *
     * @since   Kunena 7.1
     */
    public static function viewLevel(string $accessType, int $access, int $pubAccess, array $levels, array $groups = []): int
    {
        if ($accessType === 'joomla.level') {
            // The level of the category is the level of its messages -- if it is a level of the site.
            return isset($levels[$access]) ? $access : self::NOBODY;
        }

        if ($accessType === 'joomla.group') {
            return self::groupLevel($pubAccess, $levels, $groups);
        }

        // Other types of access (the ones of other extensions) can not be expressed as a view level: not shown.
        return self::NOBODY;
    }

    /**
     * The view level of a category of a user group: a level which only users who may read the category have.
     *
     * Kunena gives the category to the members of its group (pubAccess), with the members of the groups under it when pubRecurse is
     * set, to the members of its admin group (adminAccess, adminRecurse) and to the administrators of the site. Joomla gives a view
     * level to the members of its groups and of all the groups under them. So a level can be used only when each of its groups, with
     * all the groups under it, may read the category. The level which was used before ("the fewest groups") gave the messages of a
     * category "this group only" to the groups under it, and those of a category of a group to the other groups of the level.
     * Of the levels which may be used, one with the group of the category, the narrowest, the lowest id; when none, nobody.
     *
     * @param   integer  $pubAccess  The group of the category.
     * @param   array    $levels     The view levels: id => groups.
     * @param   array    $groups     'parents' (group id => id of its parent group; without it each group has no group under it),
     *                               'pubRecurse' (default true), 'adminAccess' (0: none), 'adminRecurse' (default true) and 'always'
     *                               (the groups which read every category: the administrators of the site).
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function groupLevel(int $pubAccess, array $levels, array $groups = []): int
    {
        $parents  = array_map('intval', (array) ($groups['parents'] ?? []));
        $children = [];

        foreach ($parents as $child => $parent) {
            $children[$parent][] = (int) $child;
        }

        // A group with all the groups under it.
        $subtree = static function (int $group) use ($children): array {
            $found = [$group => true];
            $todo  = [$group];

            while ($todo) {
                foreach ($children[array_pop($todo)] ?? [] as $child) {
                    if (!isset($found[$child])) {
                        $found[$child] = true;
                        $todo[]        = $child;
                    }
                }
            }

            return $found;
        };

        // The groups whose members may read the category.
        $readers = [];

        foreach ([[$pubAccess, $groups['pubRecurse'] ?? true], [(int) ($groups['adminAccess'] ?? 0), $groups['adminRecurse'] ?? true]] as [$group, $recurse]) {
            if ($group > 0) {
                $readers += $recurse ? $subtree($group) : [$group => true];
            }
        }

        foreach ((array) ($groups['always'] ?? []) as $group) {
            $readers += $subtree((int) $group);
        }

        $best = self::NOBODY;
        $rank = null;

        foreach ($levels as $id => $levelGroups) {
            $levelGroups = array_map('intval', (array) $levelGroups);

            if (!$levelGroups) {
                continue;
            }

            // Every group of the level, with the groups under it, reads the category.
            foreach ($levelGroups as $group) {
                if (array_diff_key($subtree($group), $readers)) {
                    continue 2;
                }
            }

            // The group of the category first, then the narrowest level, then the lowest id.
            $candidate = [\in_array($pubAccess, $levelGroups, true) ? 0 : 1, \count($levelGroups), (int) $id];

            if ($rank === null || $candidate < $rank) {
                $rank = $candidate;
                $best = (int) $id;
            }
        }

        return $best;
    }
}
