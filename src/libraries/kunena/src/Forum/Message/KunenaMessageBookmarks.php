<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Message
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * The bookmarks of the messages: a user keeps a message to find it again (the list My bookmarks, the posts of the mode bookmarks).
 * A bookmark is private: only its user sees it.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaMessageBookmarks
{
    /**
     * The bookmarks of the users on the messages which are loaded: [user id => [message id => true or false]].
     *
     * @var    array
     * @since  Kunena 8.0
     */
    protected static array $loaded = [];

    /**
     * Whether the user may bookmark messages: a user who is logged in, when the setting is on.
     *
     * @param   KunenaUser  $user     The user.
     * @param   bool        $enabled  The setting bookmarks.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function mayBookmark(KunenaUser $user, bool $enabled): bool
    {
        return $enabled && $user->exists() && (int) $user->userid > 0;
    }

    /**
     * Load which messages a user bookmarked, in one query (the messages of a page).
     *
     * @param   int[]  $ids     The ids of the messages.
     * @param   int    $userid  The user.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function load(array $ids, int $userid): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0)));

        if (!$ids || $userid <= 0) {
            return;
        }

        foreach ($ids as $id) {
            self::$loaded[$userid][$id] = false;
        }

        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName('message_id'))
            ->from($db->quoteName('#__kunena_bookmarks'))
            ->where($db->quoteName('user_id') . ' = :userid')
            ->whereIn($db->quoteName('message_id'), $ids)
            ->bind(':userid', $userid, ParameterType::INTEGER);

        foreach ((array) $db->setQuery($query)->loadColumn() as $id) {
            self::$loaded[$userid][(int) $id] = true;
        }
    }

    /**
     * Whether the user bookmarked the message (loaded with load(), else loaded now).
     *
     * @param   int  $id      The id of the message.
     * @param   int  $userid  The user.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isBookmarked(int $id, int $userid): bool
    {
        if ($id <= 0 || $userid <= 0) {
            return false;
        }

        if (!isset(self::$loaded[$userid][$id])) {
            self::load([$id], $userid);
        }

        return self::$loaded[$userid][$id] ?? false;
    }

    /**
     * Bookmark the message for the user, or remove his bookmark.
     *
     * @param   int  $id      The id of the message.
     * @param   int  $userid  The user.
     *
     * @return  boolean  True when the message is bookmarked now, false when the bookmark was removed.
     *
     * @since   Kunena 8.0
     */
    public static function toggle(int $id, int $userid): bool
    {
        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->delete($db->quoteName('#__kunena_bookmarks'))
            ->where($db->quoteName('user_id') . ' = :userid')
            ->where($db->quoteName('message_id') . ' = :id')
            ->bind(':userid', $userid, ParameterType::INTEGER)
            ->bind(':id', $id, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        unset(self::$loaded[$userid][$id]);

        if ($db->getAffectedRows() > 0) {
            return false;
        }

        $time  = time();
        $query = $db->createQuery()
            ->insert($db->quoteName('#__kunena_bookmarks'))
            ->columns($db->quoteName(['user_id', 'message_id', 'created_time']))
            ->values(':userid, :id, :time')
            ->bind(':userid', $userid, ParameterType::INTEGER)
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':time', $time, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        return true;
    }

    /**
     * Forget the loaded bookmarks (the tests).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function clearCache(): void
    {
        self::$loaded = [];
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
