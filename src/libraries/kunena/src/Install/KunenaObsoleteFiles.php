<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Install
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Install;

\defined('_JEXEC') or die();

use FilesystemIterator;
use Joomla\CMS\Factory;
use Throwable;

/**
 * The files which Kunena no longer ships: an update of Joomla does not delete the files which left the package, so they stay
 * on the sites (the nested folder media/kunena/kunena of Kunena 7.0, the rank images in GIF...). The list is written from the
 * history of git by scripts/obsoletefiles.php (administrator/components/com_kunena/install/obsolete-files.php).
 *
 * Only a file of the list is deleted, and not when:
 * - it is in use (a rank which has that image);
 * - it is reached through a symbolic link (a site which runs Kunena from a git clone: the file would be deleted in the clone).
 *
 * @since   Kunena 8.0
 */
class KunenaObsoleteFiles
{
    /**
     * The folders of Kunena on a site: a path of the list is in one of them, and they are never deleted themselves.
     *
     * @since   Kunena 8.0
     */
    public const ROOTS = ['administrator/components/com_kunena/', 'components/com_kunena/', 'media/kunena/', 'libraries/kunena/'];

    /**
     * Status of a file which can be deleted.
     *
     * @since   Kunena 8.0
     */
    public const OBSOLETE = 'obsolete';

    /**
     * Status of a file which the forum still uses (a rank image).
     *
     * @since   Kunena 8.0
     */
    public const IN_USE = 'in_use';

    /**
     * Status of a file which is reached through a symbolic link.
     *
     * @since   Kunena 8.0
     */
    public const LINKED = 'linked';

    /**
     * The list of the files, from the root of Joomla.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function getList(): array
    {
        $file = JPATH_ADMINISTRATOR . '/components/com_kunena/install/obsolete-files.php';

        return is_file($file) ? (array) require $file : [];
    }

    /**
     * A path of the list can only be a file inside a folder of Kunena, without a step up.
     *
     * @param   string  $path  The path, from the root of Joomla.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isSafePath(string $path): bool
    {
        if ($path === '' || str_contains($path, '\\') || str_contains($path, "\0") || preg_match('#(^|/)\.\.?(/|$)#', $path)) {
            return false;
        }

        foreach (self::ROOTS as $root) {
            if (str_starts_with($path, $root) && \strlen($path) > \strlen($root)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The files of the list which are on the site.
     *
     * @param   string         $root   The root of Joomla.
     * @param   string[]       $list   The paths of the list.
     * @param   callable|null  $inUse  fn(string $path): bool, true when the forum uses the file.
     *
     * @return  array<string, array{size: int, time: int, status: string}>  By path, in the order of the list.
     *
     * @since   Kunena 8.0
     */
    public static function find(string $root, array $list, ?callable $inUse = null): array
    {
        $root  = rtrim(str_replace('\\', '/', $root), '/');
        $found = [];

        foreach ($list as $path) {
            if (!self::isSafePath($path)) {
                continue;
            }

            $file = $root . '/' . $path;

            if (!is_file($file)) {
                continue;
            }

            $status = self::OBSOLETE;

            if (self::isLinked($root, $path)) {
                $status = self::LINKED;
            } elseif ($inUse && $inUse($path)) {
                $status = self::IN_USE;
            }

            $found[$path] = ['size' => (int) filesize($file), 'time' => (int) filemtime($file), 'status' => $status];
        }

        return $found;
    }

    /**
     * Deletes the given files: only the ones of the list which can be deleted (find()), and then the folders which they
     * leave empty (never a folder of ROOTS).
     *
     * @param   string         $root   The root of Joomla.
     * @param   string[]       $paths  The paths to delete (from the request: each one is checked).
     * @param   string[]       $list   The paths of the list.
     * @param   callable|null  $inUse  fn(string $path): bool, true when the forum uses the file.
     *
     * @return  array{deleted: string[], skipped: string[]}
     *
     * @since   Kunena 8.0
     */
    public static function delete(string $root, array $paths, array $list, ?callable $inUse = null): array
    {
        $root    = rtrim(str_replace('\\', '/', $root), '/');
        $listed  = array_flip($list);
        $wanted  = array_values(array_filter(array_unique(array_map('strval', $paths)), static fn ($path) => isset($listed[$path])));
        $found   = self::find($root, $wanted, $inUse);
        $result  = ['deleted' => [], 'skipped' => []];
        $folders = [];

        foreach ($paths as $path) {
            $path = (string) $path;

            if (($found[$path]['status'] ?? '') !== self::OBSOLETE) {
                $result['skipped'][] = $path;

                continue;
            }

            if (@unlink($root . '/' . $path)) {
                $result['deleted'][]      = $path;
                $folders[\dirname($path)] = true;
            } else {
                $result['skipped'][] = $path;
            }
        }

        foreach (array_keys($folders) as $folder) {
            self::removeEmptyFolders($root, $folder);
        }

        $result['skipped'] = array_values(array_unique($result['skipped']));

        return $result;
    }

    /**
     * The files of the list on this site, a rank image in use marked as such.
     *
     * @return  array<string, array{size: int, time: int, status: string}>
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        return self::find(JPATH_ROOT, self::getList(), self::rankImageInUse());
    }

    /**
     * Deletes the given files on this site.
     *
     * @param   string[]  $paths  The paths to delete.
     *
     * @return  array{deleted: string[], skipped: string[]}
     *
     * @since   Kunena 8.0
     */
    public static function deleteOnSite(array $paths): array
    {
        return self::delete(JPATH_ROOT, $paths, self::getList(), self::rankImageInUse());
    }

    /**
     * A file of the folder of the rank images is in use when a rank has it as image.
     *
     * @return  callable
     *
     * @since   Kunena 8.0
     */
    public static function rankImageInUse(): callable
    {
        $images = null;

        return static function (string $path) use (&$images): bool {
            if (!str_starts_with($path, 'media/kunena/ranks/')) {
                return false;
            }

            if ($images === null) {
                try {
                    $db     = Factory::getContainer()->get('DatabaseDriver');
                    $query  = $db->createQuery()->select($db->quoteName('rankImage'))->from($db->quoteName('#__kunena_ranks'));
                    $images = array_flip(array_map('strtolower', (array) $db->setQuery($query)->loadColumn()));
                } catch (Throwable $e) {
                    // Without the ranks, no rank image is deleted.
                    return true;
                }
            }

            return isset($images[strtolower(basename($path))]);
        };
    }

    /**
     * Is the file reached through a symbolic link (the file or one of its folders below the root of Joomla)?
     *
     * @param   string  $root  The root of Joomla.
     * @param   string  $path  The path, from the root.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    private static function isLinked(string $root, string $path): bool
    {
        $current = $root;

        foreach (explode('/', $path) as $part) {
            $current .= '/' . $part;

            if (is_link($current)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes a folder and its parents while they are empty, up to a folder of ROOTS (excluded).
     *
     * @param   string  $root    The root of Joomla.
     * @param   string  $folder  The folder, from the root.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private static function removeEmptyFolders(string $root, string $folder): void
    {
        while (self::isSafePath($folder . '/') && !\in_array($folder . '/', self::ROOTS, true)) {
            $full = $root . '/' . $folder;

            if (!is_dir($full) || is_link($full) || (new FilesystemIterator($full))->valid() || !@rmdir($full)) {
                return;
            }

            $folder = \dirname($folder);
        }
    }
}
