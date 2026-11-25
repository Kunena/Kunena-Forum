<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Template;

\defined('_JEXEC') or die();

use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use stdClass;

/**
 * The list of the files of a template with their SHA-256, written by the build into the folder of each shipped template, and
 * the comparison of a template on the site with its list. Plain PHP: the build script loads it without Joomla.
 *
 * @since  Kunena 8.0
 */
abstract class KunenaTemplateChecksums
{
    /**
     * The name of the list, at the root of the folder of the template.
     *
     * @since  Kunena 8.0
     */
    public const FILENAME = 'checksums.json';

    /**
     * The algorithms of the lists: SHA-256 (checksums.json), SHA-512 (checksums.sha512.json).
     *
     * @since  Kunena 8.0
     */
    public const ALGORITHMS = ['sha256', 'sha512'];

    /**
     * The path of the list of an algorithm: the list of SHA-256 itself, "checksums.sha512.json" for SHA-512.
     *
     * @param   string  $list       The path of the list of SHA-256.
     * @param   string  $algorithm  The algorithm.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function listName(string $list, string $algorithm): string
    {
        if ($algorithm === 'sha256') {
            return $list;
        }

        return str_ends_with($list, '.json') ? substr($list, 0, -5) . '.' . $algorithm . '.json' : $list . '.' . $algorithm;
    }

    /**
     * Compute the list of a template folder and write it into <folder>/checksums.json: every regular file, by its path relative
     * to the folder (forward slashes), without config/params.ini (the settings of the site) and the list itself.
     *
     * @param   string  $folder  The folder of the template.
     *
     * @return  array<string, string>  The list which was written.
     *
     * @since   Kunena 8.0
     */
    public static function write(string $folder): array
    {
        return self::writeList($folder, self::FILENAME);
    }

    /**
     * Compute the list of a folder of Kunena and write it into the folder: every regular file (or only those of the given
     * entries at the top of the folder, the folders and files which the manifest installs), by its path relative to the
     * folder, without the settings of the templates (config/params.ini) and the list itself.
     *
     * @param   string    $folder     The folder.
     * @param   string    $list       The path of the list of SHA-256, relative to the folder (listName() gives the one of
     *                                another algorithm).
     * @param   string[]  $entries    The folders and files at the top of the folder to list (none: all).
     * @param   string    $algorithm  One of ALGORITHMS.
     *
     * @return  array<string, string>  The list which was written.
     *
     * @since   Kunena 8.0
     */
    public static function writeList(string $folder, string $list, array $entries = [], string $algorithm = 'sha256'): array
    {
        if (!\in_array($algorithm, self::ALGORITHMS, true)) {
            throw new InvalidArgumentException('Unknown algorithm ' . $algorithm);
        }

        // The lists of all the algorithms are no files of the list.
        $lists     = array_map(static fn ($algo) => self::listName($list, $algo), self::ALGORITHMS);
        $folder    = rtrim(str_replace('\\', '/', $folder), '/');
        $checksums = [];
        $files     = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS)
        );

        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path     = str_replace('\\', '/', $file->getPathname());
            $relative = ltrim(substr($path, \strlen($folder)), '/');

            if (\in_array($relative, $lists, true) || $relative === 'config/params.ini' || str_ends_with($relative, '/config/params.ini')) {
                continue;
            }

            if ($entries && !\in_array(explode('/', $relative)[0], $entries, true)) {
                continue;
            }

            $checksums[$relative] = hash_file($algorithm, $path);
        }

        ksort($checksums, SORT_STRING);

        file_put_contents(
            $folder . '/' . self::listName($list, $algorithm),
            json_encode((object) $checksums, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n"
        );

        return $checksums;
    }

    /**
     * Compare a template with its list.
     *
     * @param   string  $templates  The folder of all the templates.
     * @param   string  $name       The name of the template.
     *
     * @return  array{modified: string[], missing: string[]}|null  The modified and the missing files, or null when the name is
     *                                                            not safe or the template has no valid list.
     *
     * @since   Kunena 8.0
     */
    public static function compare(string $templates, string $name): ?array
    {
        if (!preg_match('/^[\w\-]+$/', $name)) {
            return null;
        }

        return self::compareList($templates . '/' . $name, self::FILENAME);
    }

    /**
     * Compare a folder of Kunena with its list.
     *
     * @param   string  $folder     The folder.
     * @param   string  $file       The path of the list of SHA-256, relative to the folder.
     * @param   string  $algorithm  One of ALGORITHMS: its list is read (listName()).
     *
     * @return  array{modified: string[], missing: string[]}|null  The modified and the missing files, or null when the folder has
     *                                                            no valid list (a copy of the code which was not built).
     *
     * @since   Kunena 8.0
     */
    public static function compareList(string $folder, string $file, string $algorithm = 'sha256'): ?array
    {
        if (!\in_array($algorithm, self::ALGORITHMS, true)) {
            return null;
        }

        $list = $folder . '/' . self::listName($file, $algorithm);

        if (!is_dir($folder) || !is_file($list)) {
            return null;
        }

        $data = json_decode((string) file_get_contents($list));

        if (!$data instanceof stdClass) {
            return null;
        }

        $checksums = [];

        foreach ($data as $path => $hash) {
            if (!\is_string($hash)) {
                return null;
            }

            // Pairs, not an array by path: PHP would turn a path such as "404" into an integer key.
            $checksums[] = [(string) $path, $hash];
        }

        $root     = realpath($folder);
        $modified = [];
        $missing  = [];

        foreach ($checksums as [$path, $hash]) {
            if (!self::isSafePath($path)) {
                continue;
            }

            $file = $folder . '/' . $path;

            if (!is_file($file)) {
                $missing[] = $path;

                continue;
            }

            $real = realpath($file);

            if ($root === false || $real === false || !str_starts_with($real, $root . DIRECTORY_SEPARATOR)) {
                continue;
            }

            if (!hash_equals(strtolower($hash), (string) hash_file($algorithm, $real))) {
                $modified[] = $path;
            }
        }

        sort($modified, SORT_STRING);
        sort($missing, SORT_STRING);

        return ['modified' => $modified, 'missing' => $missing];
    }

    /**
     * Is a path of the list a relative path inside the folder of the template?
     *
     * @param   string  $path  The path.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected static function isSafePath(string $path): bool
    {
        if ($path === '' || str_contains($path, '\\') || str_contains($path, "\0") || str_contains($path, ':') || $path[0] === '/') {
            return false;
        }

        foreach (explode('/', $path) as $segment) {
            if ($segment === '..' || $segment === '.') {
                return false;
            }
        }

        return true;
    }
}
