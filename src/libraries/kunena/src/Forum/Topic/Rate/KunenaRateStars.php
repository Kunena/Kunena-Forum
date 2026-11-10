<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic.Rate
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic\Rate;

\defined('_JEXEC') or die();

/**
 * The number of stars of a rating.
 *
 * The rating of a topic is the average of the stored values. The value of the request was stored as it came, so one member could
 * send a million (or a negative number) and decide the average of the topic alone.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaRateStars
{
    public const MIN = 1;

    public const MAX = 5;

    /**
     * @param   mixed  $value  The value of the request.
     *
     * @return  integer|null  1 to 5, or null when it is no number of stars.
     *
     * @since   Kunena 7.1
     */
    public static function clean($value): ?int
    {
        if (\is_string($value) && preg_match('/^\d$/D', $value)) {
            $value = (int) $value;
        }

        if (!\is_int($value) || $value < self::MIN || $value > self::MAX) {
            return null;
        }

        return $value;
    }
}
