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
     *
     * @return  integer  A view level, or NOBODY.
     *
     * @since   Kunena 7.1
     */
    public static function viewLevel(string $accessType, int $access, int $pubAccess, array $levels): int
    {
        if ($accessType === 'joomla.level') {
            // The level of the category is the level of its messages -- if it is a level of the site.
            return isset($levels[$access]) ? $access : self::NOBODY;
        }

        if ($accessType === 'joomla.group') {
            // The level which is read by the fewest groups, of those which the group belongs to: the visitors of other groups
            // must not get a level which is wider than the category.
            $best = self::NOBODY;
            $size = PHP_INT_MAX;

            foreach ($levels as $id => $groups) {
                $groups = array_map('intval', (array) $groups);

                if (!\in_array($pubAccess, $groups, true)) {
                    continue;
                }

                if (\count($groups) < $size || (\count($groups) === $size && (int) $id < $best)) {
                    $best = (int) $id;
                    $size = \count($groups);
                }
            }

            return $best;
        }

        // Other types of access (the ones of other extensions) can not be expressed as a view level: not shown.
        return self::NOBODY;
    }
}
