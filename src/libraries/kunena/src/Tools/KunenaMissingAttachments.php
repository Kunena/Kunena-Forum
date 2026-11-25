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
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Throwable;

/**
 * The attachments whose file is gone (tool "Missing attachment files"), the opposite of the orphan files: the posts show a
 * broken picture or a download which fails, after a move of the site or a folder deleted by hand. The record of such an
 * attachment can be deleted.
 *
 * @since   Kunena 8.0
 */
class KunenaMissingAttachments
{
    /**
     * At most this many attachments, the newest first, are looked at.
     *
     * @since   Kunena 8.0
     */
    public const SCAN_LIMIT = 5000;

    /**
     * At most this many are listed.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 200;

    /**
     * The path of the file of an attachment from the root of the site; empty when the folder or the name is not one which
     * Kunena writes (".." or a folder out of media/kunena).
     *
     * @param   array  $row  folder, filename.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function path(array $row): string
    {
        $folder = trim(str_replace('\\', '/', (string) ($row['folder'] ?? '')), '/');
        $name   = (string) ($row['filename'] ?? '');

        if ($folder === '' || $name === '' || str_contains($folder . '/' . $name, '..') || str_contains($name, '/') || !str_starts_with($folder, 'media/kunena/')) {
            return '';
        }

        return $folder . '/' . $name;
    }

    /**
     * The attachments whose file is not there.
     *
     * @param   array     $rows    The attachments.
     * @param   callable  $exists  Is there a file at this path (from the root of the site)?
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function missing(array $rows, callable $exists): array
    {
        return array_values(array_filter($rows, static function (array $row) use ($exists): bool {
            $path = self::path($row);

            return $path === '' || !$exists($path);
        }));
    }

    /**
     * The attachments of this site whose file is gone, the newest first.
     *
     * @return  array{checked: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $rows  = self::rows([]);
        $items = self::missing($rows, static fn (string $path) => is_file(JPATH_ROOT . '/' . $path));

        return ['checked' => \count($rows), 'items' => \array_slice($items, 0, self::LIST_LIMIT)];
    }

    /**
     * Deletes the records of the chosen attachments whose file is still missing.
     *
     * @param   int[]  $ids  The attachments.
     *
     * @return  array{deleted: int, skipped: int}
     *
     * @since   Kunena 8.0
     */
    public static function deleteOnSite(array $ids): array
    {
        $ids    = array_values(array_unique(array_filter(array_map('intval', $ids))));
        $result = ['deleted' => 0, 'skipped' => 0];

        if (!$ids) {
            return $result;
        }

        // Checked again: a file which came back in the meantime keeps its attachment.
        $missing = array_column(self::missing(self::rows($ids), static fn (string $path) => is_file(JPATH_ROOT . '/' . $path)), 'id');

        foreach ($ids as $id) {
            if (!\in_array($id, array_map('intval', $missing), true)) {
                $result['skipped']++;

                continue;
            }

            try {
                $ok = KunenaAttachmentHelper::get($id)->delete();
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'deleted' : 'skipped']++;
        }

        return $result;
    }

    /**
     * The attachments with their post: all of them (the newest first, up to SCAN_LIMIT) or the given ones.
     *
     * @param   int[]  $ids  The attachments (none: all).
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function rows(array $ids): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['a.id', 'a.mesid', 'a.userid', 'a.folder', 'a.filename', 'a.filename_real', 'a.filetype', 'a.size']))
            ->select($db->quoteName(['m.thread', 'm.catid', 'm.subject', 'm.name', 'm.time']))
            ->from($db->quoteName('#__kunena_attachments', 'a'))
            ->leftJoin($db->quoteName('#__kunena_messages', 'm') . ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('a.mesid'))
            ->order($db->quoteName('a.id') . ' DESC');

        if ($ids) {
            $query->whereIn($db->quoteName('a.id'), $ids);
        }

        return (array) $db->setQuery($query, 0, $ids ? 0 : self::SCAN_LIMIT)->loadAssocList();
    }
}
