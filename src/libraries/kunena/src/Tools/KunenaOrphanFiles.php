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
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

/**
 * The files which no row of the database names (tool "Orphaned files"):
 * - an attachment file without its row (the row was deleted, the file stayed), with its copies thumb/ and raw/;
 * - an uploaded avatar which no user has any more (the user was deleted or changed his avatar);
 * and the other way round, the attachments whose file is missing (the message shows a broken file).
 *
 * A file younger than MIN_AGE is never an orphan: an upload which is being saved has no row yet.
 *
 * @since   Kunena 8.0
 */
class KunenaOrphanFiles
{
    /**
     * The folder of the attachments, from the root of Joomla.
     *
     * @since   Kunena 8.0
     */
    public const ATTACHMENTS = 'media/kunena/attachments';

    /**
     * The folder of the uploaded avatars, from the root of Joomla (the gallery is chosen by the administrator).
     *
     * @since   Kunena 8.0
     */
    public const AVATARS = 'media/kunena/avatars/users';

    /**
     * A file must be at least this old (seconds) to be an orphan.
     *
     * @since   Kunena 8.0
     */
    public const MIN_AGE = 86400;

    /**
     * The files of a folder which are no upload: they protect it.
     *
     * @since   Kunena 8.0
     */
    public const KEEP = ['index.html', '.htaccess', 'web.config'];

    /**
     * The files below $folder which no path of $used names: by path from the root, in the order of the paths.
     *
     * A symbolic link inside the folder is not followed, a file of KEEP and a file younger than $minAge are kept.
     *
     * @param   string    $root    The root of Joomla.
     * @param   string    $folder  The folder, from the root (ATTACHMENTS or AVATARS).
     * @param   string[]  $used    The paths, from the root, which the database names.
     * @param   int       $now     The current time.
     * @param   int       $minAge  The age (seconds) which a file needs to be an orphan.
     *
     * @return  array<string, array{size: int, time: int}>
     *
     * @since   Kunena 8.0
     */
    public static function find(string $root, string $folder, array $used, int $now, int $minAge = self::MIN_AGE): array
    {
        $root  = rtrim(str_replace('\\', '/', $root), '/');
        $base  = $root . '/' . trim($folder, '/');
        $used  = array_flip(array_map('strtolower', $used));
        $found = [];

        if (!is_dir($base)) {
            return [];
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS)
        );

        foreach ($files as $file) {
            if ($file->isLink() || !$file->isFile() || \in_array($file->getFilename(), self::KEEP, true)) {
                continue;
            }

            $path = trim($folder, '/') . '/' . ltrim(substr(str_replace('\\', '/', $file->getPathname()), \strlen($base)), '/');

            if (isset($used[strtolower($path)]) || self::isBelowLink($root, $path) || $now - (int) $file->getMTime() < $minAge) {
                continue;
            }

            $found[$path] = ['size' => (int) $file->getSize(), 'time' => (int) $file->getMTime()];
        }

        ksort($found, SORT_NATURAL | SORT_FLAG_CASE);

        return $found;
    }

    /**
     * The paths which the attachments name: the file, its thumbnail and its raw copy.
     *
     * @param   array<array{folder: string, filename: string}>  $rows  The rows of #__kunena_attachments.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function attachmentPaths(array $rows): array
    {
        $paths = [];

        foreach ($rows as $row) {
            $folder   = trim(str_replace('\\', '/', (string) ($row['folder'] ?? '')), '/');
            $filename = (string) ($row['filename'] ?? '');

            if ($folder === '' || $filename === '') {
                continue;
            }

            $paths[] = $folder . '/' . $filename;
            $paths[] = $folder . '/thumb/' . $filename;
            $paths[] = $folder . '/raw/' . $filename;
        }

        return $paths;
    }

    /**
     * The paths which the avatars of the users name (only the uploads: users/avatarNNN.ext).
     *
     * @param   string[]  $avatars  The values of #__kunena_users.avatar.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function avatarPaths(array $avatars): array
    {
        $paths = [];

        foreach ($avatars as $avatar) {
            $avatar = (string) $avatar;

            if (str_starts_with($avatar, 'users/')) {
                $paths[] = 'media/kunena/avatars/' . $avatar;
            }
        }

        return $paths;
    }

    /**
     * Deletes the orphans which find() lists now (never a path of the request: the list is made again).
     *
     * @param   string    $root    The root of Joomla.
     * @param   string    $folder  The folder, from the root.
     * @param   string[]  $used    The paths which the database names.
     * @param   int       $now     The current time.
     *
     * @return  array{deleted: int, failed: int, bytes: int}
     *
     * @since   Kunena 8.0
     */
    public static function delete(string $root, string $folder, array $used, int $now): array
    {
        $root   = rtrim(str_replace('\\', '/', $root), '/');
        $result = ['deleted' => 0, 'failed' => 0, 'bytes' => 0];

        foreach (self::find($root, $folder, $used, $now) as $path => $file) {
            if (@unlink($root . '/' . $path)) {
                $result['deleted']++;
                $result['bytes'] += $file['size'];
            } else {
                $result['failed']++;
            }
        }

        return $result;
    }

    /**
     * The attachments whose file is missing: by id.
     *
     * @param   string  $root  The root of Joomla.
     * @param   array   $rows  The rows of #__kunena_attachments (id, mesid, folder, filename, filename_real).
     *
     * @return  array<int, array>
     *
     * @since   Kunena 8.0
     */
    public static function findMissing(string $root, array $rows): array
    {
        $root    = rtrim(str_replace('\\', '/', $root), '/');
        $missing = [];

        foreach ($rows as $row) {
            $folder   = trim(str_replace('\\', '/', (string) ($row['folder'] ?? '')), '/');
            $filename = (string) ($row['filename'] ?? '');

            if ($folder === '' || $filename === '' || !is_file($root . '/' . $folder . '/' . $filename)) {
                $missing[(int) $row['id']] = $row;
            }
        }

        return $missing;
    }

    /**
     * The orphans of this site, for a folder (ATTACHMENTS or AVATARS).
     *
     * @param   string  $folder  The folder.
     *
     * @return  array<string, array{size: int, time: int}>
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(string $folder): array
    {
        return self::find(JPATH_ROOT, $folder, self::usedOnSite($folder), time());
    }

    /**
     * Deletes the orphans of this site, for a folder.
     *
     * @param   string  $folder  The folder.
     *
     * @return  array{deleted: int, failed: int, bytes: int}
     *
     * @since   Kunena 8.0
     */
    public static function deleteOnSite(string $folder): array
    {
        return self::delete(JPATH_ROOT, $folder, self::usedOnSite($folder), time());
    }

    /**
     * The attachments of this site whose file is missing.
     *
     * @return  array<int, array>
     *
     * @since   Kunena 8.0
     */
    public static function findMissingOnSite(): array
    {
        return self::findMissing(JPATH_ROOT, self::attachmentRows());
    }

    /**
     * Deletes the attachments of this site whose file is missing (the row, through the attachment object: the message is
     * updated). Only the ids of the request which are missing now.
     *
     * @param   int[]  $ids  The ids.
     *
     * @return  int  The number deleted.
     *
     * @since   Kunena 8.0
     */
    public static function deleteMissingOnSite(array $ids): int
    {
        $missing = self::findMissingOnSite();
        $deleted = 0;

        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if (!isset($missing[$id])) {
                continue;
            }

            try {
                if (KunenaAttachment::getInstance($id)->delete()) {
                    $deleted++;
                }
            } catch (Throwable $e) {
                // The others are still deleted.
            }
        }

        return $deleted;
    }

    /**
     * The paths which the database of this site names, for a folder.
     *
     * @param   string  $folder  The folder.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    private static function usedOnSite(string $folder): array
    {
        if ($folder === self::AVATARS) {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery()
                ->select($db->quoteName('avatar'))
                ->from($db->quoteName('#__kunena_users'))
                ->where($db->quoteName('avatar') . ' LIKE ' . $db->quote('users/%'));

            return self::avatarPaths((array) $db->setQuery($query)->loadColumn());
        }

        return self::attachmentPaths(self::attachmentRows());
    }

    /**
     * The rows of the attachments.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function attachmentRows(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'mesid', 'userid', 'folder', 'filename', 'filename_real', 'size']))
            ->from($db->quoteName('#__kunena_attachments'));

        return (array) $db->setQuery($query)->loadAssocList();
    }

    /**
     * Is a folder of the path, below the folder of Kunena, a symbolic link (the path leaves the folder)?
     *
     * @param   string  $root  The root.
     * @param   string  $path  The path, from the root.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    private static function isBelowLink(string $root, string $path): bool
    {
        $parts   = explode('/', $path);
        $current = $root . '/' . array_shift($parts) . '/' . array_shift($parts) . '/' . array_shift($parts);

        // The folders below media/kunena/<folder>: media/kunena itself is a link on a site which runs a git clone.
        array_pop($parts);

        foreach ($parts as $part) {
            $current .= '/' . $part;

            if (is_link($current)) {
                return true;
            }
        }

        return false;
    }
}
