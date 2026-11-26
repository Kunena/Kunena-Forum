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

/**
 * The filter on the size of a file (Attachments): "500k", ">2MB", "<= 100 kB", "1M-5M", "=1234". A number without a unit is
 * in bytes; k, kB, M, MB, G and GB are steps of 1024.
 *
 * @since   Kunena 8.0
 */
class KunenaSizeFilter
{
    /**
     * The bytes of a unit.
     *
     * @since   Kunena 8.0
     */
    private const UNITS = ['' => 1, 'b' => 1, 'k' => 1024, 'kb' => 1024, 'm' => 1048576, 'mb' => 1048576, 'g' => 1073741824, 'gb' => 1073741824];

    /**
     * A size in bytes: "500", "1.5 MB".
     *
     * @param   string  $size  The size.
     *
     * @return  int|null  Null when it is not a size.
     *
     * @since   Kunena 8.0
     */
    public static function bytes(string $size): ?int
    {
        if (!preg_match('/^\s*(\d+(?:[.,]\d+)?)\s*([a-z]*)\s*$/i', $size, $match)) {
            return null;
        }

        $unit = strtolower($match[2]);

        if (!isset(self::UNITS[$unit])) {
            return null;
        }

        return (int) round((float) str_replace(',', '.', $match[1]) * self::UNITS[$unit]);
    }

    /**
     * The range of a filter.
     *
     * @param   string  $filter  The filter.
     *
     * @return  array{0: int|null, 1: int|null}|null  [the least bytes, the most bytes] (null: no bound), null when the filter
     *          is not a size.
     *
     * @since   Kunena 8.0
     */
    public static function range(string $filter): ?array
    {
        $filter = trim($filter);

        // A range: "1M-5M" (the smaller one first, whatever their order).
        if (preg_match('/^([^<>=-]+)-([^<>=-]+)$/', $filter, $match)) {
            $from = self::bytes($match[1]);
            $to   = self::bytes($match[2]);

            return $from === null || $to === null ? null : [min($from, $to), max($from, $to)];
        }

        if (!preg_match('/^(<=|>=|<|>|=)?\s*(.+)$/', $filter, $match)) {
            return null;
        }

        $bytes = self::bytes($match[2]);

        if ($bytes === null) {
            return null;
        }

        return match ($match[1]) {
            '<'     => [null, max(0, $bytes - 1)],
            '<='    => [null, $bytes],
            '>'     => [$bytes + 1, null],
            '>='    => [$bytes, null],
            default => [$bytes, $bytes],
        };
    }
}
