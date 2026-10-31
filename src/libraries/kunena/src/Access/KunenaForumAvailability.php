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
 * Decides if the forum may be shown at all: the board can be offline, or open for registered users only.
 *
 * This is the one place for that rule, so every view (the page, but also the raw and the json output of the AJAX
 * calls) applies the same one.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaForumAvailability
{
    /**
     * The HTTP status when the visitor is not allowed to see the forum, or null when he is.
     *
     * @param   boolean  $offline   The board is offline.
     * @param   boolean  $isAdmin   The visitor is an administrator of the forum (they can see an offline board).
     * @param   boolean  $regOnly   The forum is for registered users only.
     * @param   boolean  $isMember  The visitor is logged in.
     * @param   boolean  $teaser    The view is a teaser, which is shown to guests too.
     *
     * @return  integer|null  503 (offline), 403 (registered users only) or null.
     *
     * @since   Kunena 7.1
     */
    public static function getBlockingStatus(bool $offline, bool $isAdmin, bool $regOnly, bool $isMember, bool $teaser = false): ?int
    {
        if ($offline && !$isAdmin) {
            return 503;
        }

        if ($regOnly && !$isMember && !$teaser) {
            return 403;
        }

        return null;
    }
}
