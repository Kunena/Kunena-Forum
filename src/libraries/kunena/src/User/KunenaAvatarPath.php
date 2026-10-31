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
 * The value of the avatar of a user is a path inside media/kunena/avatars, and it is used to read, resize and delete
 * files. It is therefore only accepted in the two forms Kunena itself makes:
 *
 *   - users/avatar<userid>.<ext>        the picture which the user has uploaded
 *   - gallery/<file> or gallery/<folder>/<file>   a picture of the avatar gallery
 *
 * Anything else (folders going up, absolute paths, other folders, odd file types) is refused.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaAvatarPath
{
    /**
     * The file types of an avatar.
     *
     * @var    string[]
     * @since  Kunena 7.1
     */
    public const EXTENSIONS = ['gif', 'jpg', 'jpeg', 'png', 'webp', 'avif'];

    /**
     * A part of a path: a name without slash, which does not start with a dot.
     */
    private const SEGMENT = '[\p{L}\p{N}_][\p{L}\p{N}_ .()\-]*';

    /**
     * Is this a value which Kunena makes for an avatar.
     *
     * @param   mixed  $avatar  The value of the avatar.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isValid($avatar): bool
    {
        return self::isUpload($avatar) || self::isGallery($avatar);
    }

    /**
     * Is this the picture which a user has uploaded (users/avatar<userid>.<ext>).
     *
     * @param   mixed  $avatar  The value of the avatar.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isUpload($avatar): bool
    {
        return self::isSafeString($avatar)
            && preg_match('#^users/avatar\d{1,10}\.(?:' . implode('|', self::EXTENSIONS) . ')\z#i', $avatar) === 1;
    }

    /**
     * Is this the picture of the avatar gallery.
     *
     * @param   mixed  $avatar  The value of the avatar.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isGallery($avatar): bool
    {
        return self::isSafeString($avatar)
            && preg_match('#^gallery/(?:' . self::SEGMENT . '/)?' . self::SEGMENT . '\.(?:' . implode('|', self::EXTENSIONS) . ')\z#iu', $avatar) === 1;
    }

    /**
     * Is this the picture which the user with this id has uploaded. Only that file may be deleted for the user: the
     * pictures of the gallery are shared, and the one of somebody else is not his.
     *
     * @param   mixed    $avatar  The value of the avatar.
     * @param   integer  $userid  The id of the user.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    public static function isUploadOf($avatar, int $userid): bool
    {
        return $userid > 0 && self::isUpload($avatar) && preg_match('#^users/avatar(\d+)\.#', $avatar, $m) === 1 && (int) $m[1] === $userid;
    }

    /**
     * The path of the avatar file inside the folder with the avatars, or null when the value is not an avatar or the
     * file is not inside that folder (also after links are followed).
     *
     * @param   string  $baseDir  The folder with the avatars.
     * @param   mixed   $avatar   The value of the avatar.
     *
     * @return  string|null
     *
     * @since   Kunena 7.1
     */
    public static function getPath(string $baseDir, $avatar): ?string
    {
        if (!self::isValid($avatar)) {
            return null;
        }

        $base = realpath($baseDir);

        if ($base === false) {
            return null;
        }

        $path = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $avatar);

        if (file_exists($path) || is_link($path)) {
            $real = realpath($path);

            if ($real === false || !str_starts_with($real, $base . DIRECTORY_SEPARATOR)) {
                return null;
            }
        }

        return $path;
    }

    /**
     * A plain string without characters that have no place in a path.
     *
     * @param   mixed  $value  The value.
     *
     * @return  boolean
     *
     * @since   Kunena 7.1
     */
    private static function isSafeString($value): bool
    {
        return \is_string($value)
            && $value !== ''
            && \strlen($value) <= 255
            && !str_contains($value, '..')
            && !str_contains($value, '\\')
            && preg_match('/[\x00-\x1f\x7f]/', $value) === 0;
    }
}
