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

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

/**
 * May this user give a rank to that user, and which ranks may be given?
 *
 * The rank given to a user (KunenaUser::rank) is changed in the backend and on the profile page of the user in the forum. Both
 * use this class, so they offer the same ranks and follow the same rule: the administrators and the global moderators change the
 * ranks; a moderator who is not an administrator does not change his own rank nor the rank of an administrator.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaUserRankPolicy
{
    public const PERMISSION = 'permission';
    public const SELF       = 'self';
    public const ADMIN      = 'admin';
    public const USER       = 'user';
    public const RANK       = 'rank';

    /**
     * @param   integer  $actorId                 The user who changes the rank.
     * @param   integer  $targetId                The user who gets the rank.
     * @param   boolean  $actorIsAdmin            The actor is a global administrator of the forum.
     * @param   boolean  $actorIsGlobalModerator  The actor is a global moderator (or a global administrator).
     * @param   boolean  $targetExists            The target is a user of the forum.
     * @param   boolean  $targetIsAdmin           The target is a global administrator of the forum.
     * @param   integer  $rankId                  The rank to give (0: no rank given).
     * @param   array    $allowedRankIds          The ranks which may be given.
     *
     * @return  string|null  null when allowed, else PERMISSION, USER, SELF, ADMIN or RANK.
     *
     * @since   Kunena 8.0
     */
    public static function refusal(
        int $actorId,
        int $targetId,
        bool $actorIsAdmin,
        bool $actorIsGlobalModerator,
        bool $targetExists,
        bool $targetIsAdmin,
        int $rankId,
        array $allowedRankIds
    ): ?string {
        if (!$actorIsAdmin && !$actorIsGlobalModerator) {
            return self::PERMISSION;
        }

        if (!$targetExists || $targetId === 0) {
            return self::USER;
        }

        if (!$actorIsAdmin && $targetId === $actorId) {
            return self::SELF;
        }

        if (!$actorIsAdmin && $targetIsAdmin) {
            return self::ADMIN;
        }

        if (!\in_array($rankId, array_map('intval', $allowedRankIds), true)) {
            return self::RANK;
        }

        return null;
    }

    /**
     * The special ranks, which are given to a user by hand.
     *
     * @return  array  rankId => rankTitle (not translated), in the order of the database. Without the entry "no rank given".
     *
     * @since   Kunena 8.0
     */
    public static function getAssignableRanks(): array
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->select($db->quoteName(['rankId', 'rankTitle']))
            ->from($db->quoteName('#__kunena_ranks'))
            ->where($db->quoteName('rankSpecial') . ' = 1');
        $db->setQuery($query);

        $ranks = [];

        foreach ((array) $db->loadObjectList() as $rank) {
            $ranks[(int) $rank->rankId] = (string) $rank->rankTitle;
        }

        return $ranks;
    }
}
