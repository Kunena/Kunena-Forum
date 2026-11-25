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
use Kunena\Forum\Libraries\User\KunenaAvatarPath;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The avatars of the users which need a look (tool "Avatar files"): a value which is not an avatar Kunena makes, a file
 * which is gone, and an uploaded file bigger than the upload limit (uploaded before the limit was lowered). The uploads
 * no user has any more are found by Orphan files.
 *
 * @since   Kunena 8.0
 */
class KunenaAvatarFiles
{
    /**
     * The folder of the avatars, from the root of Joomla.
     *
     * @since   Kunena 8.0
     */
    public const FOLDER = 'media/kunena/avatars';

    /**
     * The problem of each avatar; an avatar without a problem is left out.
     *
     * @param   array     $rows      userid, username, avatar.
     * @param   callable  $size      The size of the file of an avatar value in bytes, null when it is gone: fn (string): ?int.
     * @param   int       $maxBytes  The upload limit (0: none).
     *
     * @return  array<int, array{userid: int, username: string, avatar: string, problem: string, size: int}>
     *
     * @since   Kunena 8.0
     */
    public static function check(array $rows, callable $size, int $maxBytes): array
    {
        $result = [];

        foreach ($rows as $row) {
            $avatar = (string) $row['avatar'];
            $bytes  = 0;

            if ($avatar === '') {
                continue;
            }

            if (!KunenaAvatarPath::isValid($avatar)) {
                $problem = 'invalid';
            } elseif (($bytes = $size($avatar)) === null) {
                $problem = 'missing';
                $bytes   = 0;
            } elseif ($maxBytes > 0 && $bytes > $maxBytes && KunenaAvatarPath::isUpload($avatar)) {
                $problem = 'large';
            } else {
                continue;
            }

            $result[] = ['userid' => (int) $row['userid'], 'username' => (string) $row['username'], 'avatar' => $avatar, 'problem' => $problem, 'size' => (int) $bytes];
        }

        // The worst first: a value which can not be read, then a file which is gone, then the biggest files.
        $order = ['invalid' => 0, 'missing' => 1, 'large' => 2];
        usort($result, static fn ($a, $b) => [$order[$a['problem']], -$a['size'], $a['userid']] <=> [$order[$b['problem']], -$b['size'], $b['userid']]);

        return $result;
    }

    /**
     * The avatars of this site.
     *
     * @return  array{users: int, maxBytes: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function checkOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['k.userid', 'u.username', 'k.avatar']))
            ->from($db->quoteName('#__kunena_users', 'k'))
            ->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('k.userid'))
            ->where($db->quoteName('k.avatar') . ' IS NOT NULL')
            ->where($db->quoteName('k.avatar') . ' <> ' . $db->quote(''));
        $rows = (array) $db->setQuery($query)->loadAssocList();
        $base = JPATH_ROOT . '/' . self::FOLDER;
        $max  = (int) KunenaConfig::getInstance()->avatarSize * 1024;

        $size = static function (string $avatar) use ($base): ?int {
            $path = KunenaAvatarPath::getPath($base, $avatar);

            return $path !== null && is_file($path) ? (int) filesize($path) : null;
        };

        return ['users' => \count($rows), 'maxBytes' => $max, 'items' => self::check($rows, $size, $max)];
    }

    /**
     * Removes the avatars of users: the value, and the file when it is the user's own upload (a picture of the gallery is
     * shared and stays).
     *
     * @param   int[]  $ids  The users.
     *
     * @return  int  The avatars removed.
     *
     * @since   Kunena 8.0
     */
    public static function removeOnSite(array $ids): int
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $base    = JPATH_ROOT . '/' . self::FOLDER;
        $removed = 0;

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            $query  = $db->createQuery()->select($db->quoteName('avatar'))->from($db->quoteName('#__kunena_users'))
                ->where($db->quoteName('userid') . ' = :id')->bind(':id', $id, ParameterType::INTEGER);
            $avatar = (string) $db->setQuery($query)->loadResult();

            if ($avatar === '') {
                continue;
            }

            // The upload and its resized copies, as the profile does when an avatar is replaced.
            if (KunenaAvatarPath::isUploadOf($avatar, $id)) {
                foreach (array_merge([$base], glob($base . '/resized/*', GLOB_ONLYDIR) ?: []) as $folder) {
                    $path = KunenaAvatarPath::getPath($folder, $avatar);

                    if ($path !== null && is_file($path)) {
                        @unlink($path);
                    }
                }
            }

            $query = $db->createQuery()->update($db->quoteName('#__kunena_users'))->set($db->quoteName('avatar') . ' = ' . $db->quote(''))
                ->where($db->quoteName('userid') . ' = :id')->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
            $removed++;

            // The user may be in the cache of this request.
            KunenaUserHelper::get($id)->avatar = '';
        }

        return $removed;
    }
}
