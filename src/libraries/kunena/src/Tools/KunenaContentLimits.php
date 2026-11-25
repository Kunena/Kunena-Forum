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
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Throwable;

/**
 * The content which is over the limits of today (tool "Content over the limits"): a limit is checked when something is
 * posted or saved, so what was there before a limit was lowered stays. The attachments of a type which is no longer
 * allowed or bigger than the limit, and the signatures longer than the limit.
 *
 * @since   Kunena 8.0
 */
class KunenaContentLimits
{
    /**
     * The items shown of each kind.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 200;

    /**
     * The extensions of a setting: "jpg, PNG,gif" => ['jpg', 'png', 'gif'].
     *
     * @param   string  $setting  The setting.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function extensions(string $setting): array
    {
        return array_values(array_unique(array_filter(array_map(static fn ($type) => strtolower(trim($type, " \t.")), explode(',', $setting)))));
    }

    /**
     * The attachments over the limits.
     *
     * @param   array     $rows        id, mesid, userid, size, filetype, filename.
     * @param   string[]  $imageTypes  The allowed extensions of an image.
     * @param   string[]  $fileTypes   The allowed extensions of another file.
     * @param   int       $imageBytes  The size limit of an image (0: none).
     * @param   int       $fileBytes   The size limit of another file (0: none).
     *
     * @return  array<int, array{id: int, mesid: int, userid: int, filename: string, size: int, problem: string, limit: int}>  problem:
     *          "type" or "size".
     *
     * @since   Kunena 8.0
     */
    public static function attachments(array $rows, array $imageTypes, array $fileTypes, int $imageBytes, int $fileBytes): array
    {
        $result = [];

        foreach ($rows as $row) {
            $name    = (string) $row['filename'];
            $ext     = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $image   = str_starts_with(strtolower((string) $row['filetype']), 'image/');
            $size    = (int) $row['size'];
            $limit   = $image ? $imageBytes : $fileBytes;
            $allowed = \in_array($ext, $image ? $imageTypes : $fileTypes, true) || \in_array($ext, array_merge($imageTypes, $fileTypes), true);

            if (!$allowed) {
                $problem = 'type';
            } elseif ($limit > 0 && $size > $limit) {
                $problem = 'size';
            } else {
                continue;
            }

            $result[] = ['id' => (int) $row['id'], 'mesid' => (int) $row['mesid'], 'userid' => (int) $row['userid'], 'filename' => $name,
                'size'        => $size, 'problem' => $problem, 'limit' => $limit];
        }

        return $result;
    }

    /**
     * The signatures longer than the limit, the longest first.
     *
     * @param   array  $rows  userid, username, signature.
     * @param   int    $max   The limit in characters (0: none).
     *
     * @return  array<int, array{userid: int, username: string, length: int}>
     *
     * @since   Kunena 8.0
     */
    public static function signatures(array $rows, int $max): array
    {
        if ($max <= 0) {
            return [];
        }

        $result = [];

        foreach ($rows as $row) {
            $length = mb_strlen((string) $row['signature'], 'UTF-8');

            if ($length > $max) {
                $result[] = ['userid' => (int) $row['userid'], 'username' => (string) $row['username'], 'length' => $length];
            }
        }

        usort($result, static fn ($a, $b) => [$b['length'], $a['userid']] <=> [$a['length'], $b['userid']]);

        return $result;
    }

    /**
     * A signature shortened to the limit, without a half character or a half tag at its end.
     *
     * @param   string  $signature  The signature.
     * @param   int     $max        The limit in characters.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function shorten(string $signature, int $max): string
    {
        if ($max <= 0 || mb_strlen($signature, 'UTF-8') <= $max) {
            return $signature;
        }

        $short = mb_substr($signature, 0, $max, 'UTF-8');

        // A tag cut in two ("[ur") goes.
        return rtrim((string) preg_replace('/\[[^\]]*$/u', '', $short));
    }

    /**
     * The content of this site over its limits.
     *
     * @return  array{attachments: array, attachmentsTotal: int, signatures: array, signaturesTotal: int, limits: array}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $config = KunenaConfig::getInstance();
        $limits = [
            'imageTypes' => self::extensions((string) $config->imageTypes),
            'fileTypes'  => self::extensions((string) $config->fileTypes),
            'imageBytes' => (int) $config->imageSize * 1024,
            'fileBytes'  => (int) $config->fileSize * 1024,
            'maxSig'     => (int) $config->maxSig,
        ];
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'mesid', 'userid', 'size', 'filetype']))
            ->select('COALESCE(NULLIF(' . $db->quoteName('filename_real') . ', ' . $db->quote('') . '), ' . $db->quoteName('filename') . ') AS ' . $db->quoteName('filename'))
            ->from($db->quoteName('#__kunena_attachments'))
            ->order($db->quoteName('id') . ' DESC');
        $attachments = self::attachments((array) $db->setQuery($query)->loadAssocList(), $limits['imageTypes'], $limits['fileTypes'], $limits['imageBytes'], $limits['fileBytes']);

        $signatures = [];

        if ($limits['maxSig'] > 0) {
            $query = $db->createQuery()
                ->select($db->quoteName(['k.userid', 'u.username', 'k.signature']))
                ->from($db->quoteName('#__kunena_users', 'k'))
                ->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('k.userid'))
                ->where('CHAR_LENGTH(' . $db->quoteName('k.signature') . ') > :max')
                ->bind(':max', $limits['maxSig'], ParameterType::INTEGER);
            $signatures = self::signatures((array) $db->setQuery($query)->loadAssocList(), $limits['maxSig']);
        }

        return [
            'attachments'      => \array_slice($attachments, 0, self::LIMIT),
            'attachmentsTotal' => \count($attachments),
            'signatures'       => \array_slice($signatures, 0, self::LIMIT),
            'signaturesTotal'  => \count($signatures),
            'limits'           => $limits,
        ];
    }

    /**
     * Deletes the chosen attachments (their record and their file).
     *
     * @param   int[]  $ids  The attachments.
     *
     * @return  int  The attachments deleted.
     *
     * @since   Kunena 8.0
     */
    public static function deleteAttachmentsOnSite(array $ids): int
    {
        $deleted = 0;

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            try {
                $attachment = KunenaAttachmentHelper::get($id);

                if ($attachment->exists() && $attachment->delete()) {
                    $deleted++;
                }
            } catch (Throwable $e) {
                // An attachment which cannot be deleted stays in the list.
            }
        }

        return $deleted;
    }

    /**
     * Shortens the signatures of the chosen users to the limit.
     *
     * @param   int[]  $ids  The users.
     *
     * @return  int  The signatures shortened.
     *
     * @since   Kunena 8.0
     */
    public static function shortenSignaturesOnSite(array $ids): int
    {
        $max   = (int) KunenaConfig::getInstance()->maxSig;
        $done  = 0;
        $db    = Factory::getContainer()->get('DatabaseDriver');

        if ($max <= 0) {
            return 0;
        }

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            $query     = $db->createQuery()->select($db->quoteName('signature'))->from($db->quoteName('#__kunena_users'))
                ->where($db->quoteName('userid') . ' = :id')->bind(':id', $id, ParameterType::INTEGER);
            $signature = (string) $db->setQuery($query)->loadResult();
            $short     = self::shorten($signature, $max);

            if ($short === $signature) {
                continue;
            }

            $query = $db->createQuery()->update($db->quoteName('#__kunena_users'))->set($db->quoteName('signature') . ' = :signature')
                ->where($db->quoteName('userid') . ' = :id')->bind(':signature', $short)->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
            $done++;
        }

        return $done;
    }
}
