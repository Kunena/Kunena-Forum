<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Layout
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Layout;

\defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Html\KunenaParser;

/**
 * The modules of the positions of the forum through the Joomla content plugins: the controllers of the pages give their positions a
 * target ("Run plugins on" of the system plugin of Kunena), the module widget runs the plugins of the first target which is chosen.
 *
 * A position is a name ("kunena_index_top") or the start of names with an asterisk ("kunena_msg_*": kunena_msg_1, kunena_msg_2...).
 * A position can have several targets: the frame of every page gives kunena_top the target page_top, a list of topics gives it
 * topic_list_default too. The target given last is the first one asked, and the plugins run once, for one target.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaModuleContent
{
    /**
     * The targets of the positions of this page: position => targets, the one given last at the end.
     *
     * @var     array<string, string[]>
     * @since   Kunena 8.0
     */
    private static array $targets = [];

    /**
     * Give a position (or the positions which start the same: "kunena_msg_*") a target.
     *
     * @param   string  $position  The position, or its start with an asterisk.
     * @param   string  $target    The target.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function prepareWith(string $position, string $target): void
    {
        if ($position === '' || $position === '*' || $target === '') {
            return;
        }

        $targets                    = array_values(array_diff(self::$targets[$position] ?? [], [$target]));
        $targets[]                  = $target;
        self::$targets[$position]   = $targets;
    }

    /**
     * The targets of a position, the one to ask first at the start: the targets of its name, then of the starts which match it,
     * the longest start first.
     *
     * @param   string  $position  The position.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function targetsFor(string $position): array
    {
        $targets = array_reverse(self::$targets[$position] ?? []);
        $starts  = [];

        foreach (self::$targets as $key => $list) {
            $key = (string) $key;

            if (str_ends_with($key, '*') && str_starts_with($position, substr($key, 0, -1))) {
                $starts[$key] = $list;
            }
        }

        uksort($starts, static fn ($a, $b) => \strlen($b) <=> \strlen($a));

        foreach ($starts as $list) {
            $targets = array_merge($targets, array_reverse($list));
        }

        return array_values(array_unique($targets));
    }

    /**
     * The modules of a position, through the Joomla content plugins of the first of its targets which is chosen.
     *
     * @param   string  $position  The position.
     * @param   string  $html      The modules.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public static function prepare(string $position, string $html): string
    {
        if ($html === '') {
            return $html;
        }

        foreach (self::targetsFor($position) as $target) {
            if (KunenaParser::isContentTarget($target)) {
                return (string) KunenaParser::prepareContent($html, $target);
            }
        }

        return $html;
    }

    /**
     * Forget the targets (a new page, the tests).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function reset(): void
    {
        self::$targets = [];
    }
}
