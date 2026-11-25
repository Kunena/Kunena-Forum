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

use FilesystemIterator;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

/**
 * The caches of Kunena (tool "Empty the caches"): the cache of the site and of the administrator, the compiled CSS of the
 * templates and the resized avatars. All of them are made again when they are needed.
 *
 * @since   Kunena 8.0
 */
class KunenaCacheFolders
{
    /**
     * The files of a folder which stay: they protect it.
     *
     * @since   Kunena 8.0
     */
    public const KEEP = ['index.html', '.htaccess', 'web.config'];

    /**
     * The folders, from the root of Joomla: name => path.
     *
     * @return  array<string, string>
     *
     * @since   Kunena 8.0
     */
    public static function folders(): array
    {
        return [
            'site'     => 'cache/kunena',
            'admin'    => 'administrator/cache/kunena',
            'template' => 'media/kunena/cache',
            'avatars'  => 'media/kunena/avatars/resized',
        ];
    }

    /**
     * The number of files and their size in a folder (links not followed, the files of KEEP at its top not counted).
     *
     * @param   string  $dir  The folder.
     *
     * @return  array{files: int, bytes: int}
     *
     * @since   Kunena 8.0
     */
    public static function measure(string $dir): array
    {
        $result = ['files' => 0, 'bytes' => 0];

        if (!is_dir($dir) || is_link($dir)) {
            return $result;
        }

        foreach (self::items($dir, RecursiveIteratorIterator::LEAVES_ONLY) as $item) {
            if ($item->isFile() && !$item->isLink() && !self::isKept($dir, $item->getPathname())) {
                $result['files']++;
                $result['bytes'] += (int) $item->getSize();
            }
        }

        return $result;
    }

    /**
     * Empties a folder: everything but the files of KEEP at its top. A link inside is removed, never followed.
     *
     * @param   string  $dir  The folder.
     *
     * @return  array{files: int, bytes: int}  What was deleted.
     *
     * @since   Kunena 8.0
     */
    public static function emptyFolder(string $dir): array
    {
        $result = ['files' => 0, 'bytes' => 0];

        if (!is_dir($dir) || is_link($dir)) {
            return $result;
        }

        foreach (self::items($dir, RecursiveIteratorIterator::CHILD_FIRST) as $item) {
            $path = $item->getPathname();

            if (self::isKept($dir, $path)) {
                continue;
            }

            if ($item->isDir() && !$item->isLink()) {
                @rmdir($path);

                continue;
            }

            $size = $item->isLink() ? 0 : (int) $item->getSize();

            if (@unlink($path) || (is_link($path) && @rmdir($path))) {
                $result['files'] += $item->isLink() ? 0 : 1;
                $result['bytes'] += $size;
            }
        }

        return $result;
    }

    /**
     * The folders of this site with what they hold.
     *
     * @return  array<string, array{path: string, files: int, bytes: int}>
     *
     * @since   Kunena 8.0
     */
    public static function measureOnSite(): array
    {
        $list = [];

        foreach (self::folders() as $name => $path) {
            $list[$name] = ['path' => $path] + self::measure(JPATH_ROOT . '/' . $path);
        }

        return $list;
    }

    /**
     * Empties the caches of this site: the folders and the caches of Joomla which Kunena uses (KunenaCacheHelper::clearAll()).
     *
     * @param   string[]  $names  The folders to empty (keys of folders()); all of them when empty.
     *
     * @return  array{files: int, bytes: int}
     *
     * @since   Kunena 8.0
     */
    public static function emptyOnSite(array $names = []): array
    {
        $result  = ['files' => 0, 'bytes' => 0];
        $folders = self::folders();
        $names   = $names ? array_values(array_intersect($names, array_keys($folders))) : array_keys($folders);

        foreach ($names as $name) {
            $deleted = self::emptyFolder(JPATH_ROOT . '/' . $folders[$name]);
            $result['files'] += $deleted['files'];
            $result['bytes'] += $deleted['bytes'];
        }

        try {
            KunenaCacheHelper::clear();
            KunenaCacheHelper::clearAccess();
            KunenaCacheHelper::clearStatCache();
        } catch (Throwable $e) {
            // The folders are empty already.
        }

        return $result;
    }

    /**
     * The items of a folder, without following a link.
     *
     * @param   string  $dir   The folder.
     * @param   int     $mode  The mode of RecursiveIteratorIterator.
     *
     * @return  RecursiveIteratorIterator
     *
     * @since   Kunena 8.0
     */
    private static function items(string $dir, int $mode): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
            $mode
        );
    }

    /**
     * Is the path a file of KEEP at the top of the folder?
     *
     * @param   string  $dir   The folder.
     * @param   string  $path  The path.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    private static function isKept(string $dir, string $path): bool
    {
        $dir  = rtrim(str_replace('\\', '/', $dir), '/');
        $path = str_replace('\\', '/', $path);

        return \dirname($path) === $dir && \in_array(basename($path), self::KEEP, true);
    }
}
