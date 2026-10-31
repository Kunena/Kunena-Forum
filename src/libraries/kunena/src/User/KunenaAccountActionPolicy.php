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
 * May the administrator of the forum ban, block, remove or log out this account?
 *
 * Those tasks of the administrator change the account of the site (Joomla blocks the account, deletes it, ends its sessions), not
 * only the profile in the forum. Having the right to manage Kunena is not the right to manage the users of the site, and the tasks
 * did not even use the rules of KunenaBan::canBan() that the forum applies to its moderators (no ban of yourself or of an
 * administrator). A forum manager was able to block or log out a Super User, and to delete an Administrator.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaAccountActionPolicy
{
    public const BAN     = 'ban';
    public const UNBAN   = 'unban';
    public const BLOCK   = 'block';
    public const UNBLOCK = 'unblock';
    public const REMOVE  = 'remove';
    public const LOGOUT  = 'logout';

    public const SELF       = 'self';
    public const ADMIN      = 'admin';
    public const PERMISSION = 'permission';

    /**
     * The right of com_users that the task needs.
     */
    private const RIGHTS = [
        self::BAN     => 'core.edit.state',
        self::UNBAN   => 'core.edit.state',
        self::BLOCK   => 'core.edit.state',
        self::UNBLOCK => 'core.edit.state',
        self::LOGOUT  => 'core.edit.state',
        self::REMOVE  => 'core.delete',
    ];

    /**
     * @param   string    $action             One of the constants of this class.
     * @param   integer   $actorId            The administrator who does it.
     * @param   integer   $targetId           The account.
     * @param   boolean   $targetIsSuperUser  The account has the right core.admin of the site.
     * @param   boolean   $targetIsForumAdmin The account is an administrator of the forum (KunenaUser::isAdmin()).
     * @param   boolean   $actorIsSuperUser   The administrator has the right core.admin of the site.
     * @param   callable  $can                (string $right): bool  Does the administrator have this right on com_users?
     *
     * @return  string|null  null when allowed, else SELF, ADMIN or PERMISSION.
     *
     * @since   Kunena 7.1
     */
    public static function refusal(
        string $action,
        int $actorId,
        int $targetId,
        bool $targetIsSuperUser,
        bool $targetIsForumAdmin,
        bool $actorIsSuperUser,
        callable $can
    ): ?string {
        if (!isset(self::RIGHTS[$action])) {
            // An unknown task is never allowed.
            return self::PERMISSION;
        }

        // The accounts of the site can only be managed by those who may manage them.
        if (!$actorIsSuperUser && !$can(self::RIGHTS[$action])) {
            return self::PERMISSION;
        }

        // To end an own session is harmless; everything else on an own account is refused, like KunenaBan::canBan() does.
        if ($actorId === $targetId && !\in_array($action, [self::LOGOUT, self::UNBAN, self::UNBLOCK], true)) {
            return self::SELF;
        }

        // Only a Super User touches the account of a Super User.
        if ($targetIsSuperUser && !$actorIsSuperUser) {
            return self::ADMIN;
        }

        // An administrator of the forum is not banned or blocked (canBan()); lifting a ban is always fine.
        if ($targetIsForumAdmin && \in_array($action, [self::BAN, self::BLOCK], true)) {
            return self::ADMIN;
        }

        return null;
    }
}
