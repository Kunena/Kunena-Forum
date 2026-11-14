<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

/**
 * The name of a gallery of avatars: one folder below media/kunena/avatars/gallery.
 *
 * The name of the request was put behind the path of the galleries as it came, so "../../.." was the list of the files of any
 * folder of the server.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaAvatarGalleryName
{
    /**
     * @param   mixed  $name  The name of the request.
     *
     * @return  string|null  The name of one folder, or null when it is none.
     *
     * @since   Kunena 8.0
     */
    public static function clean($name): ?string
    {
        if (!\is_string($name)) {
            return null;
        }

        $name = trim($name);

        // One part of a path: letters, digits, space, "_", "-" and "." (not at the start, so no "." and ".." and no hidden folder).
        if ($name === '' || \strlen($name) > 100 || !preg_match('/^[\p{L}\p{N}_\-][\p{L}\p{N}_\- .]*$/Du', $name) || str_contains($name, '..')) {
            return null;
        }

        return $name;
    }

    /**
     * Whether a name is the name of one picture of a gallery: a name which clean() keeps as it is, with the extension of an image.
     *
     * @param   mixed  $name  The name of a file.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isPicture($name): bool
    {
        return \is_string($name) && self::clean($name) === $name && preg_match('/\.(?:gif|png|jpe?g|webp)$/Di', $name) === 1;
    }

    /**
     * Whether a value is an avatar of the galleries: "gallery/<picture>" or "gallery/<folder>/<picture>".
     *
     * @param   mixed  $value  The value of the avatar.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isAvatar($value): bool
    {
        if (!\is_string($value)) {
            return false;
        }

        $parts = explode('/', $value);

        if ($parts[0] !== 'gallery' || !self::isPicture(end($parts))) {
            return false;
        }

        return \count($parts) === 2 || (\count($parts) === 3 && self::clean($parts[1]) === $parts[1]);
    }

    /**
     * The pictures of one gallery, relative to the folder of the galleries and sorted by name.
     *
     * @param   string  $galleriesPath  The folder of the galleries.
     * @param   string  $gallery        The name of the gallery: '' for the pictures directly in the folder of the galleries.
     *
     * @return  string[]  "<file>" for the pictures of '', "<folder>/<file>" for those of a folder.
     *
     * @since   Kunena 8.0
     */
    public static function pictures(string $galleriesPath, string $gallery): array
    {
        if ($gallery !== '' && self::clean($gallery) !== $gallery) {
            return [];
        }

        $folder = $gallery === '' ? $galleriesPath : $galleriesPath . '/' . $gallery;

        if (!is_dir($folder)) {
            return [];
        }

        $files = [];

        foreach ((array) scandir($folder) as $file) {
            if (self::isPicture($file) && is_file($folder . '/' . $file)) {
                $files[] = $file;
            }
        }

        sort($files, SORT_STRING);

        return $gallery === '' ? $files : array_map(static fn ($file) => $gallery . '/' . $file, $files);
    }
}
