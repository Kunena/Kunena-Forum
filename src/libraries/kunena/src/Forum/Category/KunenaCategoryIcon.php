<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Category
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Category;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use RuntimeException;

/**
 * The image a category has as its own icon: the file media/kunena/category_images/icon_<category id>.<extension>.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaCategoryIcon
{
    /**
     * The types of the images which can be uploaded and their extension.
     *
     * @since   Kunena 7.2
     */
    public const TYPES = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'];

    /**
     * The biggest file which can be uploaded, in bytes.
     *
     * @since   Kunena 7.2
     */
    public const MAX_SIZE = 1048576;

    /**
     * The icon files of all the categories, by category id (one look in the folder per request).
     *
     * @var     string[]|null
     * @since   Kunena 7.2
     */
    protected static $files = null;

    /**
     * Remove the icon of the category.
     *
     * @param   int  $id  The category id.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function delete(int $id): void
    {
        foreach (self::TYPES as $extension) {
            $path = self::folder() . '/icon_' . $id . '.' . $extension;

            if (is_file($path)) {
                @unlink($path);
            }
        }

        self::$files = null;
    }

    /**
     * The folder of the icons.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    protected static function folder(): string
    {
        return JPATH_ROOT . '/media/kunena/category_images';
    }

    /**
     * The tag of the icon of the category.
     *
     * @param   int  $id    The category id.
     * @param   int  $size  The size (the biggest width and height) of the image in pixels.
     *
     * @return  string  The image, or an empty string when the category has no icon of its own.
     *
     * @since   Kunena 7.2
     */
    public static function getImage(int $id, int $size = 48): string
    {
        $url = self::getUrl($id);

        if ($url === null) {
            return '';
        }

        return '<img loading="lazy" class="kcategory-icon-image" style="max-width:' . $size . 'px;max-height:' . $size . 'px" src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" alt="" />';
    }

    /**
     * The address of the icon of the category.
     *
     * @param   int  $id  The category id.
     *
     * @return  string|null  The address, or null when the category has no icon of its own.
     *
     * @since   Kunena 7.2
     */
    public static function getUrl(int $id): ?string
    {
        if (self::$files === null) {
            self::$files = [];

            foreach (glob(self::folder() . '/icon_*.*') ?: [] as $file) {
                if (preg_match('/^icon_(\d+)\.(png|jpg|gif|webp)$/', basename($file), $match)) {
                    self::$files[(int) $match[1]] = basename($file);
                }
            }
        }

        if (!isset(self::$files[$id])) {
            return null;
        }

        // The time of the file is in the address: a new icon is shown at once, not the old one from the cache.
        return Uri::root(true) . '/media/kunena/category_images/' . self::$files[$id] . '?' . (int) filemtime(self::folder() . '/' . self::$files[$id]);
    }

    /**
     * Save the uploaded image as the icon of the category.
     *
     * @param   int    $id    The category id.
     * @param   array  $file  The entry of the uploaded file in $_FILES.
     *
     * @return  void
     *
     * @throws  RuntimeException  When the file is not an image which can be used.
     * @since   Kunena 7.2
     */
    public static function save(int $id, array $file): void
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return;
        }

        if ($error !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException(Text::_('COM_KUNENA_CATEGORY_ICON_IMAGE_UPLOAD_FAILED'));
        }

        if ((int) $file['size'] > self::MAX_SIZE) {
            throw new RuntimeException(Text::sprintf('COM_KUNENA_CATEGORY_ICON_IMAGE_TOO_BIG', (int) (self::MAX_SIZE / 1024)));
        }

        // The type is read from the content of the file, never from its name.
        $info = @getimagesize($file['tmp_name']);

        if (!$info || !isset(self::TYPES[$info[2]])) {
            throw new RuntimeException(Text::_('COM_KUNENA_CATEGORY_ICON_IMAGE_WRONG_TYPE'));
        }

        if (!is_dir(self::folder()) && !@mkdir(self::folder(), 0755, true)) {
            throw new RuntimeException(Text::_('COM_KUNENA_CATEGORY_ICON_IMAGE_UPLOAD_FAILED'));
        }

        // One icon per category: the old one (it may have another extension) goes.
        self::delete($id);

        if (!File::upload($file['tmp_name'], self::folder() . '/icon_' . $id . '.' . self::TYPES[$info[2]])) {
            throw new RuntimeException(Text::_('COM_KUNENA_CATEGORY_ICON_IMAGE_UPLOAD_FAILED'));
        }

        self::$files = null;
    }
}
