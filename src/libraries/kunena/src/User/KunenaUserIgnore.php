<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    User
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * Ignore list: users whose messages and topics a user does not want to see.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaUserIgnore
{
    /**
     * The ignore lists which were loaded in this request.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    protected static $lists = [];

    /**
     * Is the feature switched on in the configuration (it is by default).
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function isEnabled(): bool
    {
        $config = KunenaConfig::getInstance();

        return isset($config->enableIgnore) ? (bool) $config->enableIgnore : true;
    }

    /**
     * Can the user put the other user on his ignore list. Guests can not ignore anybody, nobody can ignore himself
     * and moderators and administrators can not be ignored, as users need to be able to hear from them.
     *
     * @param   KunenaUser  $user    The user who wants to ignore.
     * @param   KunenaUser  $target  The user who would be ignored.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function canIgnore(KunenaUser $user, KunenaUser $target): bool
    {
        return self::isEnabled()
            && $user->exists() && $target->exists()
            && $user->userid != $target->userid
            && !$target->isAdmin() && !$target->isModerator();
    }

    /**
     * The ids of the users which the user ignores.
     *
     * @param   int  $userid  The user.
     *
     * @return  int[]
     *
     * @since   Kunena 7.2
     */
    public static function getIgnored(int $userid): array
    {
        if ($userid < 1 || !self::isEnabled()) {
            return [];
        }

        if (!isset(self::$lists[$userid])) {
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->createQuery()
                ->select($db->quoteName('ignored_id'))
                ->from($db->quoteName('#__kunena_user_ignore'))
                ->where($db->quoteName('user_id') . ' = :userid')
                ->order($db->quoteName('created_time') . ' DESC')
                ->bind(':userid', $userid, ParameterType::INTEGER);

            self::$lists[$userid] = array_map('intval', (array) $db->setQuery($query)->loadColumn());
        }

        return self::$lists[$userid];
    }

    /**
     * The ignore list of the user with the time on which each user was put on it, the newest first.
     *
     * @param   int  $userid  The user.
     *
     * @return  object[]  Rows with ignored_id and created_time.
     *
     * @since   Kunena 7.2
     */
    public static function getIgnoredList(int $userid): array
    {
        if ($userid < 1 || !self::isEnabled()) {
            return [];
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->select($db->quoteName(['ignored_id', 'created_time']))
            ->from($db->quoteName('#__kunena_user_ignore'))
            ->where($db->quoteName('user_id') . ' = :userid')
            ->order($db->quoteName('created_time') . ' DESC')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        return (array) $db->setQuery($query)->loadObjectList();
    }

    /**
     * Does the user ignore the other user.
     *
     * @param   int  $userid    The user.
     * @param   int  $targetid  The possibly ignored user.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function isIgnoring(int $userid, int $targetid): bool
    {
        return $targetid > 0 && \in_array($targetid, self::getIgnored($userid), true);
    }

    /**
     * The ids of the users which ignore the user (those get no e-mails about his posts).
     *
     * @param   int  $targetid  The user who is possibly ignored.
     *
     * @return  int[]
     *
     * @since   Kunena 7.2
     */
    public static function getIgnoredBy(int $targetid): array
    {
        if ($targetid < 1 || !self::isEnabled()) {
            return [];
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->select($db->quoteName('user_id'))
            ->from($db->quoteName('#__kunena_user_ignore'))
            ->where($db->quoteName('ignored_id') . ' = :targetid')
            ->bind(':targetid', $targetid, ParameterType::INTEGER);

        return array_map('intval', (array) $db->setQuery($query)->loadColumn());
    }

    /**
     * Put a user on the ignore list.
     *
     * @param   KunenaUser  $user    The user who ignores.
     * @param   KunenaUser  $target  The user who is ignored.
     *
     * @return  boolean  False if it is not allowed.
     *
     * @since   Kunena 7.2
     */
    public static function ignore(KunenaUser $user, KunenaUser $target): bool
    {
        if (!self::canIgnore($user, $target)) {
            return false;
        }

        if (!self::isIgnoring($user->userid, $target->userid)) {
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $now   = time();
            $uid   = (int) $user->userid;
            $tid   = (int) $target->userid;
            $query = $db->createQuery()
                ->insert($db->quoteName('#__kunena_user_ignore'))
                ->columns($db->quoteName(['user_id', 'ignored_id', 'created_time']))
                ->values(':userid, :targetid, :created')
                ->bind(':userid', $uid, ParameterType::INTEGER)
                ->bind(':targetid', $tid, ParameterType::INTEGER)
                ->bind(':created', $now, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
        }

        // Somebody whom a user ignores is not followed anymore.
        KunenaUserFollow::unfollow((int) $user->userid, (int) $target->userid);

        unset(self::$lists[(int) $user->userid]);

        return true;
    }

    /**
     * Take a user off the ignore list.
     *
     * @param   int  $userid    The user who ignores.
     * @param   int  $targetid  The user who is ignored.
     *
     * @return  boolean  True if the user was on the list.
     *
     * @since   Kunena 7.2
     */
    public static function unignore(int $userid, int $targetid): bool
    {
        if ($userid < 1 || $targetid < 1) {
            return false;
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->delete($db->quoteName('#__kunena_user_ignore'))
            ->where($db->quoteName('user_id') . ' = :userid')
            ->where($db->quoteName('ignored_id') . ' = :targetid')
            ->bind(':userid', $userid, ParameterType::INTEGER)
            ->bind(':targetid', $targetid, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        unset(self::$lists[$userid]);

        return $db->getAffectedRows() > 0;
    }
}
