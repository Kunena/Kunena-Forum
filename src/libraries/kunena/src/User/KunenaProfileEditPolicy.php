<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

/**
 * Who may change the profile of a user.
 *
 * There are two different things in a profile save:
 *
 *   - the Kunena profile (signature, location, avatar, settings ...): the user himself, and a moderator for other users,
 *   - the Joomla account (name, email, user name, password, parameters): this is a login. It is the user himself, or a
 *     person who may edit users in Joomla.
 *
 * A moderator of the forum is not a manager of the site, so a moderator must never be able to change the password or
 * the email address of somebody else (which gives the account away), and nobody but an administrator may touch an
 * administrator or another moderator. This is the same rule as KunenaBan::canBan() has for banning.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaProfileEditPolicy
{
    /**
     * May the actor change the Kunena profile of the target.
     *
     * @param   boolean  $isSelf            The actor edits his own profile.
     * @param   boolean  $actorIsModerator  The actor is a (global) moderator or an administrator.
     * @param   boolean  $actorIsAdmin      The actor is an administrator (of the forum or Super User).
     * @param   boolean  $targetIsAdmin     The target is an administrator (of the forum or Super User).
     * @param   boolean  $targetIsModerator The target is a moderator.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function canEditProfile(bool $isSelf, bool $actorIsModerator, bool $actorIsAdmin, bool $targetIsAdmin, bool $targetIsModerator): bool
    {
        if ($isSelf) {
            return true;
        }

        if (!$actorIsModerator) {
            return false;
        }

        // Only an administrator may touch an administrator, or another moderator.
        if (($targetIsAdmin || $targetIsModerator) && !$actorIsAdmin) {
            return false;
        }

        return true;
    }

    /**
     * May the actor change the Joomla account (name, email, user name, password, parameters) of the target.
     *
     * @param   boolean  $isSelf             The actor edits his own account.
     * @param   boolean  $actorMayEditUsers  The actor has the permission of Joomla to edit users (core.edit in com_users).
     * @param   boolean  $actorIsSuperUser   The actor is a Super User (core.admin).
     * @param   boolean  $targetIsSuperUser  The target is a Super User.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function canEditAccount(bool $isSelf, bool $actorMayEditUsers, bool $actorIsSuperUser, bool $targetIsSuperUser): bool
    {
        if ($isSelf) {
            return true;
        }

        if (!$actorMayEditUsers) {
            return false;
        }

        // A Super User can only be changed by a Super User, otherwise the permission to edit users would be the permission
        // to take over the site.
        return !$targetIsSuperUser || $actorIsSuperUser;
    }
}
