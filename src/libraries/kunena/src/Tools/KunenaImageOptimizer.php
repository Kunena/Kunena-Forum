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
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Image\KunenaImage;
use Throwable;

/**
 * The uploaded images which take more room than needed (tool "Optimize images"): bigger than the image size of the
 * settings, or heavy. They are resized to that size and saved again, optionally as WebP; a new file replaces the old one
 * only when it is smaller. A GIF may be animated: it is left as it is.
 *
 * @since   Kunena 8.0
 */
class KunenaImageOptimizer
{
    /**
     * An image of this size or more (bytes) is a candidate, even when it is not too big.
     *
     * @since   Kunena 8.0
     */
    public const HEAVY = 300 * 1024;

    /**
     * The quality of a JPEG or a WebP which is saved again.
     *
     * @since   Kunena 8.0
     */
    public const QUALITY = 82;

    /**
     * At most this many images in one run (resizing takes time and memory).
     *
     * @since   Kunena 8.0
     */
    public const BATCH = 20;

    /**
     * At most this many images, the heaviest first, are looked at.
     *
     * @since   Kunena 8.0
     */
    public const SCAN_LIMIT = 500;

    /**
     * The types which are optimized: mime => [image type, extension].
     *
     * @since   Kunena 8.0
     */
    public const TYPES = [
        'image/jpeg'  => [IMAGETYPE_JPEG, 'jpg'],
        'image/pjpeg' => [IMAGETYPE_JPEG, 'jpg'],
        'image/png'   => [IMAGETYPE_PNG, 'png'],
        'image/webp'  => [IMAGETYPE_WEBP, 'webp'],
    ];

    /**
     * The size of an image which fits into the box (never bigger than it was).
     *
     * @param   int  $width      The width.
     * @param   int  $height     The height.
     * @param   int  $maxWidth   The width of the box.
     * @param   int  $maxHeight  The height of the box.
     *
     * @return  array{0: int, 1: int}
     *
     * @since   Kunena 8.0
     */
    public static function fit(int $width, int $height, int $maxWidth, int $maxHeight): array
    {
        if ($width <= 0 || $height <= 0 || $maxWidth <= 0 || $maxHeight <= 0) {
            return [max(0, $width), max(0, $height)];
        }

        $ratio = min(1, $maxWidth / $width, $maxHeight / $height);

        return [max(1, (int) round($width * $ratio)), max(1, (int) round($height * $ratio))];
    }

    /**
     * The name of the file with another extension (a name without an extension, a private file, keeps its name).
     *
     * @param   string  $filename   The name.
     * @param   string  $extension  The new extension.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function rename(string $filename, string $extension): string
    {
        $dot = strrpos($filename, '.');

        return $dot === false ? $filename : substr($filename, 0, $dot) . '.' . $extension;
    }

    /**
     * A name which no file of the folder has: the name, or the name with -1, -2... before its extension.
     *
     * @param   string  $folder    The folder.
     * @param   string  $filename  The name.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private static function freeName(string $folder, string $filename): string
    {
        $dot  = strrpos($filename, '.');
        $base = $dot === false ? $filename : substr($filename, 0, $dot);
        $ext  = $dot === false ? '' : substr($filename, $dot);
        $name = $filename;

        for ($n = 1; is_file($folder . '/' . $name) || is_file($folder . '/' . $name . '.new'); $n++) {
            $name = $base . '-' . $n . $ext;
        }

        return $name;
    }

    /**
     * Can this site write WebP?
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function canWebp(): bool
    {
        return \function_exists('imagewebp');
    }

    /**
     * The images of this site which can be optimized, the heaviest first.
     *
     * @return  array{maxWidth: int, maxHeight: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $config    = KunenaConfig::getInstance();
        $maxWidth  = max(100, (int) $config->imageWidth);
        $maxHeight = max(100, (int) $config->imageHeight);
        $items     = [];

        foreach (self::rows() as $row) {
            $path = JPATH_ROOT . '/' . trim((string) $row['folder'], '/') . '/' . $row['filename'];
            $info = is_file($path) ? @getimagesize($path) : false;

            if (!$info) {
                continue;
            }

            [$width, $height] = $info;
            $size             = (int) filesize($path);
            [$newW, $newH]    = self::fit((int) $width, (int) $height, $maxWidth, $maxHeight);

            if ($newW < $width || $size >= self::HEAVY) {
                $items[] = $row + ['width' => (int) $width, 'height' => (int) $height, 'bytes' => $size, 'newWidth' => $newW, 'newHeight' => $newH];
            }
        }

        return ['maxWidth' => $maxWidth, 'maxHeight' => $maxHeight, 'items' => $items];
    }

    /**
     * Optimizes the chosen images which are candidates now, at most BATCH of them.
     *
     * @param   int[]  $ids   The attachments.
     * @param   bool   $webp  Save them as WebP.
     *
     * @return  array{done: int, kept: int, failed: int, saved: int}
     *
     * @since   Kunena 8.0
     */
    public static function optimizeOnSite(array $ids, bool $webp): array
    {
        $found  = self::findOnSite();
        $byId   = array_column($found['items'], null, 'id');
        $result = ['done' => 0, 'kept' => 0, 'failed' => 0, 'saved' => 0];
        $webp   = $webp && self::canWebp();
        $db     = Factory::getContainer()->get('DatabaseDriver');

        foreach (\array_slice(array_values(array_unique(array_map('intval', $ids))), 0, self::BATCH) as $id) {
            $row = $byId[$id] ?? null;

            if ($row === null) {
                $result['failed']++;

                continue;
            }

            $folder = JPATH_ROOT . '/' . trim((string) $row['folder'], '/');
            $path   = $folder . '/' . $row['filename'];
            [$type] = self::TYPES[strtolower((string) $row['filetype'])];
            $toType = $webp ? IMAGETYPE_WEBP : $type;
            $temp   = $folder . '/.kd-optimize-' . bin2hex(random_bytes(6));

            try {
                $image = new KunenaImage($path);

                if ($type === IMAGETYPE_JPEG) {
                    @$image->correctImageOrientation();
                }

                if ($row['newWidth'] < $row['width']) {
                    $image->resize($row['newWidth'], $row['newHeight'], false, KunenaImage::SCALE_INSIDE);
                }

                $saved = $image->toFile($temp, $toType, ['quality' => $toType === IMAGETYPE_PNG ? 9 : self::QUALITY]);
                $image->destroy();
            } catch (Throwable $e) {
                $saved = false;
            }

            clearstatcache();

            if (!$saved || !is_file($temp)) {
                @unlink($temp);
                $result['failed']++;

                continue;
            }

            $newSize = (int) filesize($temp);

            // The new file only when it is smaller.
            if ($newSize >= $row['bytes']) {
                @unlink($temp);
                $result['kept']++;

                continue;
            }

            $filename = $toType === $type ? (string) $row['filename'] : self::freeName($folder, self::rename((string) $row['filename'], 'webp'));
            $target   = $folder . '/' . $filename;

            // The file of another name is a free name: the WebP of "photo.jpg" replaced the file "photo.webp" of another attachment
            // (or of "photo.png" optimized in the same run). The old file goes once the new one is in place: when the last step
            // failed, the attachment was left without any file.
            if (!@rename($temp, $target . '.new') || !@rename($target . '.new', $target)) {
                @unlink($temp);
                @unlink($target . '.new');
                $result['failed']++;

                continue;
            }

            if ($target !== $path) {
                @unlink($path);
            }

            $filetype = $toType === $type ? (string) $row['filetype'] : 'image/webp';
            $real     = $toType === $type ? (string) $row['filename_real'] : self::rename((string) $row['filename_real'], 'webp');
            $hash     = (string) md5_file($target);
            $query    = $db->createQuery()
                ->update($db->quoteName('#__kunena_attachments'))
                ->set($db->quoteName('size') . ' = :size')
                ->set($db->quoteName('filename') . ' = :filename')
                ->set($db->quoteName('filename_real') . ' = :real')
                ->set($db->quoteName('filetype') . ' = :filetype')
                ->set($db->quoteName('hash') . ' = :hash')
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':size', $newSize, ParameterType::INTEGER)
                ->bind(':filename', $filename)
                ->bind(':real', $real)
                ->bind(':filetype', $filetype)
                ->bind(':hash', $hash)
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query)->execute();

            $result['done']++;
            $result['saved'] += $row['bytes'] - $newSize;
        }

        return $result;
    }

    /**
     * The attachments which are images of a type which is optimized, the heaviest first.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function rows(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['a.id', 'a.mesid', 'a.userid', 'a.folder', 'a.filename', 'a.filename_real', 'a.filetype', 'a.size', 'm.thread', 'm.catid']))
            ->from($db->quoteName('#__kunena_attachments', 'a'))
            ->leftJoin($db->quoteName('#__kunena_messages', 'm') . ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('a.mesid'))
            ->whereIn('LOWER(' . $db->quoteName('a.filetype') . ')', array_keys(self::TYPES), ParameterType::STRING)
            ->order($db->quoteName('a.size') . ' DESC');

        return (array) $db->setQuery($query, 0, self::SCAN_LIMIT)->loadAssocList();
    }
}
