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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * The warnings of the users: a moderator gives the author of a message a warning with points (1 to 5) and a reason. The points count
 * until they expire (the setting warningExpireDays); when the active points of a user reach the threshold (warningBanPoints), he is
 * banned automatically for warningBanDays days. A moderator may warn the users whom he may ban (KunenaBan::canBan()).
 *
 * @since   Kunena 8.0
 */
abstract class KunenaUserWarnings
{
    /**
     * The fewest points of a warning.
     *
     * @since   Kunena 8.0
     */
    public const int MIN_POINTS = 1;

    /**
     * The most points of a warning.
     *
     * @since   Kunena 8.0
     */
    public const int MAX_POINTS = 5;

    /**
     * The points which a request sends, when they are in the range.
     *
     * @param   mixed  $value  The value of the request.
     *
     * @return  integer|null
     *
     * @since   Kunena 8.0
     */
    public static function cleanPoints(mixed $value): ?int
    {
        if (!is_numeric($value) || (int) $value != $value) {
            return null;
        }

        $value = (int) $value;

        return $value >= self::MIN_POINTS && $value <= self::MAX_POINTS ? $value : null;
    }

    /**
     * The time when a warning expires (0: never).
     *
     * @param   int  $created  The time of the warning.
     * @param   int  $days     The setting warningExpireDays.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function expires(int $created, int $days): int
    {
        return $days > 0 ? $created + $days * 86400 : 0;
    }

    /**
     * The points of the warnings which are active now (not expired).
     *
     * @param   object[]  $warnings  The warnings (points, expires_time).
     * @param   int       $now       Now.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function activePoints(array $warnings, int $now): int
    {
        $points = 0;

        foreach ($warnings as $warning) {
            $expires = (int) ($warning->expires_time ?? 0);

            if ($expires === 0 || $expires > $now) {
                $points += (int) ($warning->points ?? 0);
            }
        }

        return $points;
    }

    /**
     * Whether the user is banned automatically: the threshold is set, his active points reach it, and he is not banned yet.
     *
     * @param   int   $points     His active points.
     * @param   int   $threshold  The setting warningBanPoints (0: no automatic ban).
     * @param   bool  $banned     He is banned now.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function reachesBan(int $points, int $threshold, bool $banned): bool
    {
        return $threshold > 0 && $points >= $threshold && !$banned;
    }

    /**
     * Whether a moderator may warn a user: the setting is on, the moderator moderates the category of the message, and the user is
     * one whom he may ban (not himself, not an administrator, a moderator only for an administrator).
     *
     * @param   bool  $enabled        The setting warnings.
     * @param   bool  $moderator      The user who warns moderates the category of the message.
     * @param   int   $meId           The user who warns.
     * @param   int   $targetId       The user who is warned.
     * @param   bool  $targetExists   He exists.
     * @param   bool  $targetIsAdmin  He is an administrator.
     * @param   bool  $targetIsMod    He is a moderator.
     * @param   bool  $meIsAdmin      The user who warns is an administrator.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function mayWarn(bool $enabled, bool $moderator, int $meId, int $targetId, bool $targetExists, bool $targetIsAdmin, bool $targetIsMod, bool $meIsAdmin): bool
    {
        if (!$enabled || !$moderator || $meId <= 0 || $targetId <= 0 || !$targetExists || $targetId === $meId) {
            return false;
        }

        if ($targetIsAdmin) {
            return false;
        }

        return !$targetIsMod || $meIsAdmin;
    }

    /**
     * Give a warning.
     *
     * @param   int     $userid       The user who is warned.
     * @param   int     $moderatorId  The moderator.
     * @param   int     $messageId    The message of the warning (0: none).
     * @param   int     $points       The points (see cleanPoints).
     * @param   string  $reason       The reason.
     * @param   int     $now          Now.
     * @param   int     $expireDays   The setting warningExpireDays.
     *
     * @return  integer  The id of the warning.
     *
     * @since   Kunena 8.0
     */
    public static function add(int $userid, int $moderatorId, int $messageId, int $points, string $reason, int $now, int $expireDays): int
    {
        $db      = self::getDatabase();
        $reason  = mb_substr(trim($reason), 0, 255, 'UTF-8');
        $expires = self::expires($now, $expireDays);
        $query   = $db->createQuery()
            ->insert($db->quoteName('#__kunena_warnings'))
            ->columns($db->quoteName(['user_id', 'moderator_id', 'message_id', 'points', 'reason', 'created_time', 'expires_time']))
            ->values(':user, :moderator, :message, :points, :reason, :created, :expires')
            ->bind(':user', $userid, ParameterType::INTEGER)
            ->bind(':moderator', $moderatorId, ParameterType::INTEGER)
            ->bind(':message', $messageId, ParameterType::INTEGER)
            ->bind(':points', $points, ParameterType::INTEGER)
            ->bind(':reason', $reason)
            ->bind(':created', $now, ParameterType::INTEGER)
            ->bind(':expires', $expires, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        return (int) $db->insertid();
    }

    /**
     * The warnings of a user, the newest first.
     *
     * @param   int  $userid  The user.
     *
     * @return  object[]
     *
     * @since   Kunena 8.0
     */
    public static function getWarnings(int $userid): array
    {
        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__kunena_warnings'))
            ->where($db->quoteName('user_id') . ' = :user')
            ->bind(':user', $userid, ParameterType::INTEGER)
            ->order($db->quoteName('created_time') . ' DESC, ' . $db->quoteName('id') . ' DESC');

        return (array) $db->setQuery($query)->loadObjectList();
    }

    /**
     * A warning.
     *
     * @param   int  $id  The id of the warning.
     *
     * @return  object|null
     *
     * @since   Kunena 8.0
     */
    public static function getWarning(int $id): ?object
    {
        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__kunena_warnings'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        return $db->setQuery($query)->loadObject() ?: null;
    }

    /**
     * Remove a warning.
     *
     * @param   int  $id  The id of the warning.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function remove(int $id): void
    {
        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->delete($db->quoteName('#__kunena_warnings'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);
        $db->setQuery($query)->execute();
    }

    /**
     * Ban the user automatically, when his active points reach the threshold: a Kunena ban (not a block of Joomla) for the days of
     * the setting, with the reason of the warnings.
     *
     * @param   KunenaUser  $user       The user.
     * @param   int         $points     His active points.
     * @param   int         $threshold  The setting warningBanPoints.
     * @param   int         $days       The setting warningBanDays (0: permanent).
     *
     * @return  boolean  Whether he was banned.
     *
     * @since   Kunena 8.0
     */
    public static function banIfReached(KunenaUser $user, int $points, int $threshold, int $days): bool
    {
        $ban = KunenaBan::getInstanceByUserid((int) $user->userid, true);

        if (!self::reachesBan($points, $threshold, $ban->isEnabled())) {
            return false;
        }

        $expiration = $days > 0 ? Factory::getDate('+' . min($days, 36500) . ' days')->toSql() : null;
        $reason     = Text::sprintf('COM_KUNENA_WARNING_BAN_REASON', $points);

        if (!$ban->id) {
            $ban->ban((int) $user->userid, null, 0, $expiration, $reason, $reason, '');
        } else {
            $ban->blocked = 0;
            $ban->setExpiration($expiration, '');
            $ban->setReason($reason, $reason);
        }

        $saved = $ban->save();

        if ($saved) {
            KunenaUserHelper::recountBanned();
        }

        return (bool) $saved;
    }

    /**
     * The database.
     *
     * @return  DatabaseInterface
     *
     * @since   Kunena 8.0
     */
    protected static function getDatabase(): DatabaseInterface
    {
        return Factory::getContainer()->get(DatabaseInterface::class);
    }
}
