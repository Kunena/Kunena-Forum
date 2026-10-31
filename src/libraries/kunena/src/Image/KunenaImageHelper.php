<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Image
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Image;

\defined('_JEXEC') or die;

use Exception;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Kunena\Forum\Libraries\Folder\KunenaFolder;
use Kunena\Forum\Libraries\Path\KunenaPath;

/**
 * Helper class for image manipulation.
 *
 * @since   Kunena 6.0
 */
class KunenaImageHelper
{
    /**
     * The most pixels of an image which Kunena loads into memory, whatever the memory limit of PHP is.
     *
     * @var    integer
     * @since  Kunena 7.1
     */
    public const MAX_PIXELS = 40000000;

    /**
     * What GD needs for one pixel of the image which is loaded, and for the copy that is made while it is resized.
     *
     * @var    integer
     * @since  Kunena 7.1
     */
    public const BYTES_PER_PIXEL = 5;

    /**
     * Memory which is kept free for everything else in the request.
     *
     * @var    integer
     * @since  Kunena 7.1
     */
    public const MEMORY_RESERVE = 16777216;

    /**
     * The value of a php.ini size (128M, 1G, 512K, 1048576) in bytes.
     *
     * @param   mixed  $value  The value.
     *
     * @return  integer|null  Null when there is no limit (-1) or when the value is not understood.
     *
     * @since   Kunena 7.1
     */
    public static function parseBytes($value): ?int
    {
        $value = strtolower(trim((string) $value));

        if ($value === '' || $value === '-1' || !preg_match('/^(\d+)\s*([kmg]?)b?$/', $value, $m)) {
            return null;
        }

        $bytes = (int) $m[1];

        switch ($m[2]) {
            case 'g':
                $bytes *= 1024;
                // no break
            case 'm':
                $bytes *= 1024;
                // no break
            case 'k':
                $bytes *= 1024;
        }

        // A value which is larger than an integer becomes a float on the way: that is more than anybody has, so no limit.
        return \is_int($bytes) && $bytes > 0 ? $bytes : null;
    }

    /**
     * May an image of this size be loaded into memory. The size on the disk says nothing: a few kilobytes of PNG can
     * describe an image of billions of pixels, and loading it ends the request with a fatal error (which no try/catch
     * can stop), every time somebody looks at the page that shows it.
     *
     * @param   integer       $width        Width in pixels.
     * @param   integer       $height       Height in pixels.
     * @param   integer|null  $memoryLimit  The memory limit of PHP in bytes, null when there is none.
     * @param   integer       $memoryInUse  The memory which is used already.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isSafeSize(int $width, int $height, ?int $memoryLimit = null, int $memoryInUse = 0): bool
    {
        if ($width < 1 || $height < 1) {
            return false;
        }

        $pixels = $width * $height;

        if ($pixels > self::MAX_PIXELS) {
            return false;
        }

        if ($memoryLimit !== null) {
            return $pixels * self::BYTES_PER_PIXEL + self::MEMORY_RESERVE <= $memoryLimit - $memoryInUse;
        }

        return true;
    }

    /**
     * Is this file an image which may be loaded into memory (see isSafeSize()). Only the header of the file is read.
     *
     * @param   string  $file  The path of the file.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isSafeFile(string $file): bool
    {
        if (!is_file($file)) {
            return false;
        }

        $info = @getimagesize($file);

        if (!$info || empty($info[0]) || empty($info[1])) {
            return false;
        }

        return self::isSafeSize((int) $info[0], (int) $info[1], self::parseBytes(ini_get('memory_limit')), memory_get_usage(true));
    }

    /**
     * Does the photo have to be turned? True for a JPEG of which the EXIF data says that it is stored rotated or mirrored.
     *
     * Nothing else needs it: the orientation is a property of the EXIF data of a JPEG, and a PNG, a GIF (which may be an animation
     * that loading would flatten) or a document must stay as it was uploaded.
     *
     * @param   string  $file  The path of the file.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function needsOrientationFix(string $file): bool
    {
        if (!\function_exists('exif_read_data') || !self::isSafeFile($file)) {
            return false;
        }

        $info = @getimagesize($file);

        if (!$info || ($info[2] ?? 0) !== IMAGETYPE_JPEG) {
            return false;
        }

        $exif = @exif_read_data($file);

        return \is_array($exif) && isset($exif['Orientation']) && (int) $exif['Orientation'] > 1 && (int) $exif['Orientation'] <= 8;
    }

    /**
     * Throws when the file is not an image, or when it is too large to be loaded into memory.
     *
     * @param   string  $file  The path of the file.
     *
     * @return  void
     *
     * @throws  \RuntimeException
     * @since   Kunena 7.1
     */
    public static function assertSafeFile(string $file): void
    {
        if (!self::isSafeFile($file)) {
            throw new \RuntimeException('The file is not an image, or the image is too large to be processed.', 400);
        }
    }

    /**
     * Create new re-sized version of the original image.
     *
     * @param   string  $file       Incoming file
     * @param   string  $folder     Folder for the new image.
     * @param   string  $filename   Filename for the new image.
     * @param   int     $maxWidth   Maximum width for the image.
     * @param   int     $maxHeight  Maximum height for the image.
     * @param   int     $quality    Quality for the file (1-100).
     * @param   int     $scale      See available KunenaImage constants.
     * @param   int     $crop       Define if you want crop the image.
     *
     * @return  boolean  True on success.
     *
     * @throws Exception
     * @since   Kunena 6.0
     */
    public static function version($file, $folder, $filename, $maxWidth = 800, $maxHeight = 800, $quality = 70, $scale = KunenaImage::SCALE_INSIDE, $crop = 0)
    {
        // Create target directory if it does not exist.
        if (!is_dir($folder) && !Folder::create($folder)) {
            return false;
        }

        // Make sure that index.html exists in the folder.
        KunenaFolder::createIndex($folder);

        try {
            $info = KunenaImage::getImageFileProperties($file);
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());

            return false;
        }

        // The header says how large the image is, the file says nothing. Loading a huge image ends the request.
        if (!self::isSafeSize((int) $info->width, (int) $info->height, self::parseBytes(ini_get('memory_limit')), memory_get_usage(true))) {
            throw new \RuntimeException('The image is too large to be processed.', 400);
        }

        if ($info->width > $maxWidth || $info->height > $maxHeight) {
            // Make sure that quality is in allowed range.
            if ($quality < 1 || $quality > 100) {
                $quality = 70;
            }

            // Calculate quality for PNG.
            if ($info->type == IMAGETYPE_PNG) {
                $quality = \intval(($quality - 1) / 10);
            }

            $options = ['quality' => $quality];

            try {
                // Resize image and copy it to temporary file.
                $image = new KunenaImage($file);

                if ($crop && $info->width > $info->height) {
                    $image = $image->resize($info->width * $maxHeight / $info->height, $maxHeight, false, $scale);
                    $image = $image->crop($maxWidth, $maxHeight);
                } elseif ($crop && $info->width < $info->height) {
                    $image = $image->resize($maxWidth, $info->height * $maxWidth / $info->width, false, $scale);
                    $image = $image->crop($maxWidth, $maxHeight);
                } else {
                    $image = $image->resize($maxWidth, $maxHeight, false, $scale);
                }

                $temp = KunenaPath::tmpdir() . '/kunena_' . bin2hex(random_bytes(16));
                $image->toFile($temp, $info->type, $options);
                unset($image);
            } catch (Exception $e) {
                throw new \Exception($e->getMessage());

                return false;
            }

            // Move new file to its proper location.
            if (!File::move($temp, "{$folder}/{$filename}")) {
                unlink($temp);

                return false;
            }
        } else {
            // Copy original file to the new location.
            if (!File::copy($file, "{$folder}/{$filename}")) {
                return false;
            }
        }

        return true;
    }
}
