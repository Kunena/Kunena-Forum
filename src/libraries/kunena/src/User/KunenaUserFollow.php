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
 * Follow list: users whose new topics a user wants to see and to get an e-mail about.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaUserFollow
{
    /**
     * The follow lists which were loaded in this request.
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

        return isset($config->enableFollow) ? (bool) $config->enableFollow : true;
    }

    /**
     * Can the user follow the other user. Guests can not follow anybody, nobody can follow himself and a user
     * who is on the ignore list of the user can not be followed.
     *
     * @param   KunenaUser  $user    The user who wants to follow.
     * @param   KunenaUser  $target  The user who would be followed.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function canFollow(KunenaUser $user, KunenaUser $target): bool
    {
        return self::isEnabled()
            && $user->exists() && $target->exists()
            && $user->userid != $target->userid
            && !KunenaUserIgnore::isIgnoring((int) $user->userid, (int) $target->userid);
    }

    /**
     * The ids of the users which the user follows.
     *
     * @param   int  $userid  The user.
     *
     * @return  int[]
     *
     * @since   Kunena 7.2
     */
    public static function getFollowing(int $userid): array
    {
        if ($userid < 1 || !self::isEnabled()) {
            return [];
        }

        if (!isset(self::$lists[$userid])) {
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select($db->quoteName('followed_id'))
                ->from($db->quoteName('#__kunena_user_follow'))
                ->where($db->quoteName('user_id') . ' = :userid')
                ->order($db->quoteName('created_time') . ' DESC')
                ->bind(':userid', $userid, ParameterType::INTEGER);

            self::$lists[$userid] = array_map('intval', (array) $db->setQuery($query)->loadColumn());
        }

        return self::$lists[$userid];
    }

    /**
     * The follow list of the user with the time on which each user was added, the newest first.
     *
     * @param   int  $userid  The user.
     *
     * @return  object[]  Rows with followed_id and created_time.
     *
     * @since   Kunena 7.2
     */
    public static function getFollowingList(int $userid): array
    {
        if ($userid < 1 || !self::isEnabled()) {
            return [];
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select($db->quoteName(['followed_id', 'created_time']))
            ->from($db->quoteName('#__kunena_user_follow'))
            ->where($db->quoteName('user_id') . ' = :userid')
            ->order($db->quoteName('created_time') . ' DESC')
            ->bind(':userid', $userid, ParameterType::INTEGER);

        return (array) $db->setQuery($query)->loadObjectList();
    }

    /**
     * The users which follow the user with the time on which they started, the newest first.
     *
     * @param   int  $followedid  The followed user.
     *
     * @return  object[]  Rows with user_id and created_time.
     *
     * @since   Kunena 7.2
     */
    public static function getFollowersList(int $followedid): array
    {
        if ($followedid < 1 || !self::isEnabled()) {
            return [];
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select($db->quoteName(['user_id', 'created_time']))
            ->from($db->quoteName('#__kunena_user_follow'))
            ->where($db->quoteName('followed_id') . ' = :followedid')
            ->order($db->quoteName('created_time') . ' DESC')
            ->bind(':followedid', $followedid, ParameterType::INTEGER);

        return (array) $db->setQuery($query)->loadObjectList();
    }

    /**
     * The number of users which follow the user.
     *
     * @param   int  $followedid  The followed user.
     *
     * @return  int
     *
     * @since   Kunena 7.2
     */
    public static function countFollowers(int $followedid): int
    {
        if ($followedid < 1 || !self::isEnabled()) {
            return 0;
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_user_follow'))
            ->where($db->quoteName('followed_id') . ' = :followedid')
            ->bind(':followedid', $followedid, ParameterType::INTEGER);

        return (int) $db->setQuery($query)->loadResult();
    }

    /**
     * Does the user follow the other user.
     *
     * @param   int  $userid      The user.
     * @param   int  $followedid  The possibly followed user.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function isFollowing(int $userid, int $followedid): bool
    {
        return $followedid > 0 && \in_array($followedid, self::getFollowing($userid), true);
    }

    /**
     * Who gets an e-mail when the user starts a new topic: the followers whose Joomla account is enabled (and who
     * accept system e-mails when the forum is set to respect that).
     *
     * @param   int  $followedid  The user who starts the topic.
     *
     * @return  object[]  Rows with id, name, username and email.
     *
     * @since   Kunena 7.2
     */
    public static function getRecipients(int $followedid): array
    {
        if ($followedid < 1 || !self::isEnabled()) {
            return [];
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select($db->quoteName(['u.id', 'u.name', 'u.username', 'u.email']))
            ->from($db->quoteName('#__kunena_user_follow', 'f'))
            ->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('f.user_id'))
            ->where($db->quoteName('f.followed_id') . ' = :followedid')
            ->where($db->quoteName('u.block') . ' = 0')
            ->bind(':followedid', $followedid, ParameterType::INTEGER);

        if (KunenaConfig::getInstance()->useSystemEmails) {
            $query->where($db->quoteName('u.sendEmail') . ' = 1');
        }

        return (array) $db->setQuery($query)->loadObjectList();
    }

    /**
     * Follow a user.
     *
     * @param   KunenaUser  $user    The user who follows.
     * @param   KunenaUser  $target  The user who is followed.
     *
     * @return  boolean  False if it is not allowed.
     *
     * @since   Kunena 7.2
     */
    public static function follow(KunenaUser $user, KunenaUser $target): bool
    {
        if (!self::canFollow($user, $target)) {
            return false;
        }

        if (!self::isFollowing($user->userid, $target->userid)) {
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $now   = time();
            $uid   = (int) $user->userid;
            $tid   = (int) $target->userid;
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__kunena_user_follow'))
                ->columns($db->quoteName(['user_id', 'followed_id', 'created_time']))
                ->values(':userid, :followedid, :created')
                ->bind(':userid', $uid, ParameterType::INTEGER)
                ->bind(':followedid', $tid, ParameterType::INTEGER)
                ->bind(':created', $now, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
        }

        unset(self::$lists[(int) $user->userid]);

        return true;
    }

    /**
     * Stop following a user.
     *
     * @param   int  $userid      The user who follows.
     * @param   int  $followedid  The user who is followed.
     *
     * @return  boolean  True if the user was followed.
     *
     * @since   Kunena 7.2
     */
    public static function unfollow(int $userid, int $followedid): bool
    {
        if ($userid < 1 || $followedid < 1) {
            return false;
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__kunena_user_follow'))
            ->where($db->quoteName('user_id') . ' = :userid')
            ->where($db->quoteName('followed_id') . ' = :followedid')
            ->bind(':userid', $userid, ParameterType::INTEGER)
            ->bind(':followedid', $followedid, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        unset(self::$lists[$userid]);

        return $db->getAffectedRows() > 0;
    }
}
