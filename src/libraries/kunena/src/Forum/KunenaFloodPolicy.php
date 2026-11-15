<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum;

\defined('_JEXEC') or die();

/**
 * The rule of the flood protection: the number of seconds a poster has to wait between two consecutive posts (a new
 * topic or a reply). The controller collects the time of the last post of the poster from the places where it is known
 * (the posts of the user, or of the IP address for a guest, and the session for those posts which keep no user), and this
 * class decides.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaFloodPolicy
{
    /**
     * How many seconds the poster still has to wait before the next post.
     *
     * @param   integer  $now            The time now (unix time).
     * @param   integer  $window         The setting of the flood protection in seconds, 0 or less is off.
     * @param   array    $lastPostTimes  Times (unix) of the last post, from every place where it is known. Empty values are ignored.
     *
     * @return  integer  0 when the poster may post.
     *
     * @since   Kunena 7.1
     */
    public static function secondsToWait(int $now, int $window, array $lastPostTimes): int
    {
        if ($window <= 0) {
            return 0;
        }

        $last = 0;

        foreach ($lastPostTimes as $time) {
            $time = (int) $time;

            if ($time > $last) {
                $last = $time;
            }
        }

        if ($last <= 0) {
            return 0;
        }

        // A time in the future (the clock was changed) never makes the wait longer than the setting.
        $wait = min($window, $window - ($now - $last));

        return $wait > 0 ? $wait : 0;
    }
}
