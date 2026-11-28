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
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * The reactions on the messages: a user gives one emoji of the list of the setting to a message of another user. One reaction per
 * user and message: another emoji replaces it, the same one removes it. The emoji is stored binary (the collation of the forum
 * compares many emoji as equal).
 *
 * @since   Kunena 8.0
 */
abstract class KunenaMessageReactions
{
    /**
     * The most reactions of the list.
     *
     * @since   Kunena 8.0
     */
    public const int MAX_REACTIONS = 8;

    /**
     * The longest reaction, in characters (an emoji with its modifiers, or a short text).
     *
     * @since   Kunena 8.0
     */
    public const int MAX_LENGTH = 16;

    /**
     * The longest reaction in bytes, the length of the column reaction.
     *
     * @since   Kunena 8.0
     */
    public const int MAX_BYTES = 64;

    /**
     * The numbers of the reactions of the messages which are loaded: [message id => [reaction => number]].
     *
     * @var    array
     * @since  Kunena 8.0
     */
    protected static array $counts = [];

    /**
     * The reaction of a user on the messages which are loaded: [user id => [message id => reaction or '']].
     *
     * @var    array
     * @since  Kunena 8.0
     */
    protected static array $mine = [];

    /**
     * The reactions of the setting, separated by spaces or lines: without the duplicates (the first one is kept), the ones which are
     * too long and the ones with a character of HTML, at most MAX_REACTIONS.
     *
     * @param   string|null  $setting  The setting reactions.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function parse(?string $setting): array
    {
        $reactions = [];

        foreach ((array) preg_split('/\s+/u', trim((string) $setting)) as $reaction) {
            $reaction = (string) $reaction;

            if ($reaction === '' || \in_array($reaction, $reactions, true) || preg_match('/[<>"\'&]/', $reaction)) {
                continue;
            }

            if (mb_strlen($reaction, 'UTF-8') > self::MAX_LENGTH || \strlen($reaction) > self::MAX_BYTES) {
                continue;
            }

            $reactions[] = $reaction;

            if (\count($reactions) === self::MAX_REACTIONS) {
                break;
            }
        }

        return $reactions;
    }

    /**
     * The reaction which a request sends, when it is one of the list.
     *
     * @param   mixed     $value      The value of the request.
     * @param   string[]  $reactions  The reactions of the list (see parse).
     *
     * @return  string|null
     *
     * @since   Kunena 8.0
     */
    public static function clean(mixed $value, array $reactions): ?string
    {
        if (!\is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' && \in_array($value, $reactions, true) ? $value : null;
    }

    /**
     * Whether the user may react to the message: a user who is logged in and not banned, on a published message of another user in
     * a published topic which is not locked, when the list has reactions.
     *
     * @param   KunenaTopic    $topic      The topic of the message.
     * @param   KunenaMessage  $message    The message.
     * @param   KunenaUser     $user       The user.
     * @param   string[]       $reactions  The reactions of the list (see parse).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function mayReact(KunenaTopic $topic, KunenaMessage $message, KunenaUser $user, array $reactions): bool
    {
        if (!$reactions || !$user->exists() || !(int) $user->userid || $user->isBanned()) {
            return false;
        }

        if ((int) $message->id <= 0 || (int) $message->userid === (int) $user->userid) {
            return false;
        }

        // Nor in a locked category (an archive, the announcements), where the thank yous and the replies are refused.
        return (int) $message->hold === 0 && (int) $topic->hold === 0 && !$topic->locked && !$topic->getCategory()->locked;
    }

    /**
     * The reactions in the order of the list, with their numbers: the ones which are not in the list any more come after them.
     *
     * @param   array     $counts     The numbers of the reactions of a message (see getCounts).
     * @param   string[]  $reactions  The reactions of the list (see parse).
     *
     * @return  array  [reaction => number], only the reactions which were given.
     *
     * @since   Kunena 8.0
     */
    public static function sort(array $counts, array $reactions): array
    {
        $sorted = [];

        foreach ($reactions as $reaction) {
            if (!empty($counts[$reaction])) {
                $sorted[$reaction] = (int) $counts[$reaction];
            }
        }

        foreach ($counts as $reaction => $count) {
            if (!isset($sorted[$reaction]) && (int) $count > 0) {
                $sorted[(string) $reaction] = (int) $count;
            }
        }

        return $sorted;
    }

    /**
     * Load the numbers of the reactions of messages, and the reactions of a user on them, in two queries (the messages of a page).
     *
     * @param   int[]  $ids     The ids of the messages.
     * @param   int    $userid  The user (0: no reactions of a user).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function load(array $ids, int $userid = 0): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0)));

        if (!$ids) {
            return;
        }

        foreach ($ids as $id) {
            self::$counts[$id] = [];

            if ($userid > 0) {
                self::$mine[$userid][$id] = '';
            }
        }

        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select([$db->quoteName('message_id'), $db->quoteName('reaction'), 'COUNT(*) AS ' . $db->quoteName('number')])
            ->from($db->quoteName('#__kunena_reactions'))
            ->whereIn($db->quoteName('message_id'), $ids)
            ->group([$db->quoteName('message_id'), $db->quoteName('reaction')]);

        foreach ((array) $db->setQuery($query)->loadObjectList() as $row) {
            self::$counts[(int) $row->message_id][(string) $row->reaction] = (int) $row->number;
        }

        if ($userid > 0) {
            $query = $db->createQuery()
                ->select([$db->quoteName('message_id'), $db->quoteName('reaction')])
                ->from($db->quoteName('#__kunena_reactions'))
                ->whereIn($db->quoteName('message_id'), $ids)
                ->where($db->quoteName('user_id') . ' = :userid')
                ->bind(':userid', $userid, ParameterType::INTEGER);

            foreach ((array) $db->setQuery($query)->loadObjectList() as $row) {
                self::$mine[$userid][(int) $row->message_id] = (string) $row->reaction;
            }
        }
    }

    /**
     * The numbers of the reactions of a message (loaded with load(), else loaded now).
     *
     * @param   int  $id  The id of the message.
     *
     * @return  array  [reaction => number]
     *
     * @since   Kunena 8.0
     */
    public static function getCounts(int $id): array
    {
        if (!isset(self::$counts[$id])) {
            self::load([$id]);
        }

        return self::$counts[$id] ?? [];
    }

    /**
     * The reaction of a user on a message (loaded with load(), else loaded now).
     *
     * @param   int  $id      The id of the message.
     * @param   int  $userid  The user.
     *
     * @return  string|null  The reaction, null when he has none.
     *
     * @since   Kunena 8.0
     */
    public static function getMine(int $id, int $userid): ?string
    {
        if ($userid <= 0) {
            return null;
        }

        if (!isset(self::$mine[$userid][$id])) {
            self::load([$id], $userid);
        }

        $reaction = self::$mine[$userid][$id] ?? '';

        return $reaction !== '' ? $reaction : null;
    }

    /**
     * Give the reaction of a user to a message: it replaces his reaction, or removes it when it is the same.
     *
     * @param   int     $id        The id of the message.
     * @param   int     $userid    The user.
     * @param   string  $reaction  The reaction (checked with clean before).
     *
     * @return  boolean  True when the reaction is saved, false when it was removed.
     *
     * @since   Kunena 8.0
     */
    public static function toggle(int $id, int $userid, string $reaction): bool
    {
        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName('reaction'))
            ->from($db->quoteName('#__kunena_reactions'))
            ->where($db->quoteName('message_id') . ' = :id')
            ->where($db->quoteName('user_id') . ' = :userid')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':userid', $userid, ParameterType::INTEGER);
        $current = $db->setQuery($query)->loadResult();

        $query = $db->createQuery()
            ->delete($db->quoteName('#__kunena_reactions'))
            ->where($db->quoteName('message_id') . ' = :id')
            ->where($db->quoteName('user_id') . ' = :userid')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':userid', $userid, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        unset(self::$counts[$id], self::$mine[$userid][$id]);

        if ($current !== null && (string) $current === $reaction) {
            return false;
        }

        $time  = time();
        $query = $db->createQuery()
            ->insert($db->quoteName('#__kunena_reactions'))
            ->columns([$db->quoteName('message_id'), $db->quoteName('user_id'), $db->quoteName('reaction'), $db->quoteName('created_time')])
            ->values(':id, :userid, :reaction, :time')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':userid', $userid, ParameterType::INTEGER)
            ->bind(':reaction', $reaction)
            ->bind(':time', $time, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        return true;
    }

    /**
     * Forget the loaded reactions (the tests, and a page which loads them again).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function clearCache(): void
    {
        self::$counts = [];
        self::$mine   = [];
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
