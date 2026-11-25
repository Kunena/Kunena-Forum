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

use Joomla\CMS\Factory;

/**
 * Where the space of the attachments goes (tool "Storage"): by kind of file, by user, by category, and the biggest files.
 *
 * @since   Kunena 8.0
 */
class KunenaStorageReport
{
    /**
     * At most this many rows in each list.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 15;

    /**
     * The kinds of files by extension.
     *
     * @since   Kunena 8.0
     */
    public const KINDS = [
        'image'    => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp', 'heic', 'heif', 'tif', 'tiff'],
        'video'    => ['mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v', 'ogv'],
        'audio'    => ['mp3', 'ogg', 'wav', 'm4a', 'flac', 'aac'],
        'document' => ['pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'xls', 'xlsx', 'ods', 'csv', 'ppt', 'pptx', 'odp', 'md'],
        'archive'  => ['zip', 'rar', '7z', 'gz', 'tgz', 'bz2', 'xz', 'tar'],
    ];

    /**
     * The kind of a file from its name.
     *
     * @param   string  $filename  The name.
     *
     * @return  string  A key of KINDS, or other.
     *
     * @since   Kunena 8.0
     */
    public static function kindOf(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        foreach (self::KINDS as $kind => $extensions) {
            if (\in_array($extension, $extensions, true)) {
                return $kind;
            }
        }

        return 'other';
    }

    /**
     * The report of this site.
     *
     * @return  array{files: int, bytes: int, kinds: array, users: array, categories: array, biggest: array}
     *
     * @since   Kunena 8.0
     */
    public static function reportOnSite(): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // By kind: the names of all the files, summed here (the kind comes from the extension).
        $query = $db->createQuery()
            ->select([$db->quoteName('filename_real'), $db->quoteName('filename'), $db->quoteName('size')])
            ->from($db->quoteName('#__kunena_attachments'));
        $kinds = [];
        $files = 0;
        $bytes = 0;

        // Row by row: a forum with many attachments does not load them all at once.
        foreach ($db->setQuery($query)->getIterator() as $row) {
            $kind = self::kindOf((string) ($row->filename_real ?: $row->filename));

            $kinds[$kind]['files'] = ($kinds[$kind]['files'] ?? 0) + 1;
            $kinds[$kind]['bytes'] = ($kinds[$kind]['bytes'] ?? 0) + (int) $row->size;
            $files++;
            $bytes += (int) $row->size;
        }

        uasort($kinds, static fn ($a, $b) => $b['bytes'] <=> $a['bytes']);

        $sum   = 'SUM(' . $db->quoteName('a.size') . ') AS ' . $db->quoteName('bytes');
        $count = 'COUNT(*) AS ' . $db->quoteName('files');

        $query = $db->createQuery()
            ->select([$db->quoteName('a.userid'), $db->quoteName('u.username'), $count, $sum])
            ->from($db->quoteName('#__kunena_attachments', 'a'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.userid'))
            ->group([$db->quoteName('a.userid'), $db->quoteName('u.username')])
            ->order('SUM(' . $db->quoteName('a.size') . ') DESC');
        $users = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();

        $query = $db->createQuery()
            ->select([$db->quoteName('c.id'), $db->quoteName('c.name'), $count, $sum])
            ->from($db->quoteName('#__kunena_attachments', 'a'))
            ->innerJoin($db->quoteName('#__kunena_messages', 'm') . ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('a.mesid'))
            ->innerJoin($db->quoteName('#__kunena_categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('m.catid'))
            ->group([$db->quoteName('c.id'), $db->quoteName('c.name')])
            ->order('SUM(' . $db->quoteName('a.size') . ') DESC');
        $categories = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();

        $query = $db->createQuery()
            ->select($db->quoteName(['a.id', 'a.mesid', 'a.userid', 'a.size', 'a.filename', 'a.filename_real', 'a.folder', 'u.username', 'm.thread', 'm.catid']))
            ->from($db->quoteName('#__kunena_attachments', 'a'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.userid'))
            ->leftJoin($db->quoteName('#__kunena_messages', 'm') . ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('a.mesid'))
            ->order($db->quoteName('a.size') . ' DESC');
        $biggest = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();

        return ['files' => $files, 'bytes' => $bytes, 'kinds' => $kinds, 'users' => $users, 'categories' => $categories, 'biggest' => $biggest];
    }
}
