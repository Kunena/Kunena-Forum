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
 * For whom a member may act or look, when the request names a user.
 *
 * The lists of the subscriptions and the favorites of a user, and the tasks that remove subscriptions, took the id of the user
 * from the request. The forms with that id are only shown to the user and to moderators, but the address works for everybody: the
 * subscriptions of any member could be read and removed.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaUserScope
{
    /**
     * May the member change something of the user (remove a subscription)? For oneself, and a moderator for anybody.
     *
     * @param   integer  $actorId      The member who asks (0 for a guest).
     * @param   boolean  $isModerator  Is the member a moderator?
     * @param   mixed    $targetId     The user of the request; nothing or 0 is the member.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function mayActFor(int $actorId, bool $isModerator, $targetId): bool
    {
        if ($actorId <= 0) {
            return false;
        }

        $targetId = (int) $targetId;

        return $targetId <= 0 || $targetId === $actorId || $isModerator;
    }

    /**
     * May the member see a list of the user? The favorites are for the user only, the subscriptions for the user and for
     * moderators (as the tabs of the profile); every other list is of what everybody can see in the forum.
     *
     * @param   string   $list         The list: favorites, subscriptions, or another mode.
     * @param   integer  $viewerId     The member who asks (0 for a guest).
     * @param   boolean  $isModerator  Is the member a moderator?
     * @param   mixed    $targetId     The user of the list.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function maySeeList(string $list, int $viewerId, bool $isModerator, $targetId): bool
    {
        $list = strtolower($list);

        if ($list !== 'favorites' && $list !== 'subscriptions') {
            return true;
        }

        if ($viewerId <= 0) {
            return false;
        }

        if ((int) $targetId === $viewerId) {
            return true;
        }

        return $list === 'subscriptions' && $isModerator;
    }
}
