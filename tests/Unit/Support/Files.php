<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Tests\Unit\Support;

/**
 * The files of the source which the tests of the definitions read.
 */
abstract class Files
{
    public static function root(): string
    {
        return \dirname(__DIR__, 3);
    }

    public static function path(string $relative): string
    {
        return self::root() . '/' . ltrim($relative, '/');
    }

    public static function read(string $relative): string
    {
        $content = file_get_contents(self::path($relative));

        if ($content === false) {
            throw new \RuntimeException('The file ' . $relative . ' can not be read.');
        }

        return str_replace("\r\n", "\n", $content);
    }

    /**
     * The strings of a language file: key => text. A line is KEY = "text"; comments and empty lines are skipped.
     *
     * @return  array<string,string>
     */
    public static function language(string $relative): array
    {
        $strings = [];

        foreach (explode("\n", self::read($relative)) as $line) {
            if (preg_match('/^([A-Z0-9_]+)\s*=\s*"(.*)"\s*$/', $line, $match)) {
                $strings[$match[1]] = $match[2];
            }
        }

        return $strings;
    }

    /**
     * All the strings of the English language files of a folder.
     *
     * @return  array<string,string>
     */
    public static function languageFolder(string $relative): array
    {
        $strings = [];

        foreach (glob(self::path($relative) . '/*.ini') ?: [] as $file) {
            $strings += self::language($relative . '/' . basename($file));
        }

        return $strings;
    }
}
