<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Inbox
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Inbox\Helper;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;
use RuntimeException;

/**
 * The storage of the inbox: private messages and notifications of the forum.
 *
 * @since   Kunena 7.2
 */
class InboxHelper
{
    /**
     * The number of unread entries, by user: it is asked for several times on a page.
     *
     * @var     int[]
     * @since   Kunena 7.2
     */
    protected static array $unread = [];

    /**
     * @return  DatabaseInterface
     *
     * @since   Kunena 7.2
     */
    protected static function db(): DatabaseInterface
    {
        return Factory::getContainer()->get(DatabaseInterface::class);
    }

    /**
     * Does the user want an e-mail as well as the entry in the inbox.
     *
     * @param   int       $userid  The user.
     * @param   Registry  $params  The parameters of the plugin.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function wantsEmail(int $userid, Registry $params): bool
    {
        return (int) KunenaUserHelper::getJoomlaParam($userid, 'kunena_inbox_email', (int) $params->get('emailDefault', 1)) === 1;
    }

    /**
     * The number of unread entries of the user.
     *
     * @param   int  $userid  The user.
     *
     * @return  int
     *
     * @since   Kunena 7.2
     */
    public static function unreadCount(int $userid): int
    {
        if ($userid < 1) {
            return 0;
        }

        if (!isset(self::$unread[$userid])) {
            $db    = self::db();
            $query = $db->createQuery()
                ->select('COUNT(*)')
                ->from($db->quoteName('#__kunena_inbox'))
                ->where($db->quoteName('user_id') . ' = :user')
                ->where($db->quoteName('read_time') . ' = 0')
                ->where($db->quoteName('deleted_recipient') . ' = 0')
                ->bind(':user', $userid, ParameterType::INTEGER);

            self::$unread[$userid] = (int) $db->setQuery($query)->loadResult();
        }

        return self::$unread[$userid];
    }

    /**
     * Store a notification of a message for the users.
     *
     * @param   KunenaMessage  $message  The message.
     * @param   string         $type     subscription, moderation, follow or mention.
     * @param   int[]          $userids  The users.
     * @param   Registry       $params   The parameters of the plugin.
     * @param   int            $senderId The user who causes the notification (the author of the message by default).
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function addNotification(KunenaMessage $message, string $type, array $userids, Registry $params, int $senderId = 0): void
    {
        self::$unread = [];

        $db        = self::db();
        $now       = time();
        $subject   = (string) $message->getTopic()->subject;
        $author    = $senderId ?: (int) $message->userid;
        $messageId = (int) $message->id;
        $topicId   = (int) $message->thread;

        foreach ($userids as $userid) {
            $userid = (int) $userid;

            if ($userid < 1 || $userid === $author) {
                continue;
            }

            // A user who is blocked gives no notification of mentions and thanks.
            if (\in_array($type, ['mention', 'thanks'], true) && self::isBlocked($userid, $author)) {
                continue;
            }

            // The same message gives one entry for a user and sender, it is not stored again for a second reason.
            $query = $db->createQuery()
                ->select('COUNT(*)')
                ->from($db->quoteName('#__kunena_inbox'))
                ->where($db->quoteName('user_id') . ' = :user')
                ->where($db->quoteName('message_id') . ' = :message')
                ->where($db->quoteName('sender_id') . ' = :sender')
                ->where($db->quoteName('type') . ' <> ' . $db->quote('message'))
                ->bind(':user', $userid, ParameterType::INTEGER)
                ->bind(':message', $messageId, ParameterType::INTEGER)
                ->bind(':sender', $author, ParameterType::INTEGER);

            if ((int) $db->setQuery($query)->loadResult()) {
                continue;
            }

            $row = (object) [
                'user_id'         => $userid,
                'sender_id'       => $author,
                'type'            => $type,
                'conversation_id' => 0,
                'subject'         => mb_substr($subject, 0, 255),
                'body'            => '',
                'topic_id'        => $topicId,
                'message_id'      => $messageId,
                'created_time'    => $now,
            ];

            $db->insertObject('#__kunena_inbox', $row);
        }

        // Read notifications are deleted after a number of days (when the plugin is set so).
        $days = (int) $params->get('deleteReadDays', 0);

        if ($days > 0) {
            $limit = $now - $days * 86400;
            $query = $db->createQuery()
                ->delete($db->quoteName('#__kunena_inbox'))
                ->where($db->quoteName('type') . ' <> ' . $db->quote('message'))
                ->where($db->quoteName('read_time') . ' > 0')
                ->where($db->quoteName('read_time') . ' < :limit')
                ->bind(':limit', $limit, ParameterType::INTEGER);

            $db->setQuery($query)->execute();
        }
    }

    /**
     * Send a private message.
     *
     * @param   int     $from     The sender.
     * @param   int     $to       The recipient (ignored for a reply).
     * @param   string  $subject  The subject.
     * @param   string  $body     The text.
     * @param   int     $replyTo  The id of the message which is answered (0 for a new conversation).
     *
     * @return  int  The id of the new message.
     *
     * @throws  RuntimeException
     * @since   Kunena 7.2
     */
    public static function send(int $from, int $to, string $subject, string $body, int $replyTo = 0): int
    {
        self::$unread = [];

        $db           = self::db();
        $conversation = 0;
        $subject      = trim(mb_substr($subject, 0, 150));
        $body         = trim(mb_substr($body, 0, 10000));

        if ($replyTo) {
            $parent = self::getRow($replyTo);

            if (!$parent || $parent->type !== 'message' || ($parent->user_id != $from && $parent->sender_id != $from)) {
                throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR'));
            }

            $conversation = (int) ($parent->conversation_id ?: $parent->id);
            $to           = (int) ($parent->user_id == $from ? $parent->sender_id : $parent->user_id);

            if ($subject === '') {
                $subject = preg_match('/^Re:/i', (string) $parent->subject) ? (string) $parent->subject : 'Re: ' . $parent->subject;
            }
        }

        if ($to === $from) {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_SELF'));
        }

        $recipient = KunenaUserHelper::get($to);

        if (!$recipient->exists() || $recipient->blocked) {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_RECIPIENT'));
        }

        if ($body === '' || $subject === '') {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_EMPTY'));
        }

        // A user who ignores or has blocked the sender gets no message from him.
        if (\in_array($to, KunenaUserIgnore::getIgnoredBy($from), true) || self::isBlocked($to, $from)) {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_IGNORED'));
        }

        $row = (object) [
            'user_id'         => $to,
            'sender_id'       => $from,
            'type'            => 'message',
            'conversation_id' => $conversation,
            'subject'         => $subject,
            'body'            => $body,
            'created_time'    => time(),
        ];

        $db->insertObject('#__kunena_inbox', $row);
        $id = (int) $db->insertid();

        // The first message of a conversation is the start of it.
        if (!$conversation) {
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_inbox'))
                ->set($db->quoteName('conversation_id') . ' = :id')
                ->where($db->quoteName('id') . ' = :id2')
                ->bind(':id', $id, ParameterType::INTEGER)
                ->bind(':id2', $id, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
        }

        return $id;
    }

    /**
     * Has the user blocked the other user. Moderators and administrators can not be blocked.
     *
     * @param   int  $userid   The user who may have blocked.
     * @param   int  $blocked  The other user.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function isBlocked(int $userid, int $blocked): bool
    {
        if ($userid < 1 || $blocked < 1 || KunenaUserHelper::get($blocked)->isModerator()) {
            return false;
        }

        $db    = self::db();
        $query = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_inbox_blocks'))
            ->where($db->quoteName('user_id') . ' = :user')
            ->where($db->quoteName('blocked_id') . ' = :blocked')
            ->bind(':user', $userid, ParameterType::INTEGER)
            ->bind(':blocked', $blocked, ParameterType::INTEGER);

        return (bool) $db->setQuery($query)->loadResult();
    }

    /**
     * Block a user: he can not send private messages to the user, and gives no notifications of mentions and thanks.
     *
     * @param   int  $userid  The user who blocks.
     * @param   int  $target  The user who is blocked.
     *
     * @return  void
     *
     * @throws  RuntimeException
     * @since   Kunena 7.2
     */
    public static function block(int $userid, int $target): void
    {
        $user = KunenaUserHelper::get($target);

        if ($target === $userid) {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_SELF'));
        }

        if (!$user->exists()) {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_RECIPIENT'));
        }

        if ($user->isModerator()) {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR_BLOCK_STAFF'));
        }

        $db  = self::db();
        $row = (object) ['user_id' => $userid, 'blocked_id' => $target, 'created_time' => time()];

        try {
            $db->insertObject('#__kunena_inbox_blocks', $row);
        } catch (Exception $e) {
            // Blocked already.
        }
    }

    /**
     * @param   int  $userid  The user who blocked.
     * @param   int  $target  The user who is not blocked any more.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function unblock(int $userid, int $target): void
    {
        $db    = self::db();
        $query = $db->createQuery()
            ->delete($db->quoteName('#__kunena_inbox_blocks'))
            ->where($db->quoteName('user_id') . ' = :user')
            ->where($db->quoteName('blocked_id') . ' = :blocked')
            ->bind(':user', $userid, ParameterType::INTEGER)
            ->bind(':blocked', $target, ParameterType::INTEGER);

        $db->setQuery($query)->execute();
    }

    /**
     * The users which the user has blocked.
     *
     * @param   int  $userid  The user.
     *
     * @return  array[]  id and name.
     *
     * @since   Kunena 7.2
     */
    public static function getBlocked(int $userid): array
    {
        $db    = self::db();
        $query = $db->createQuery()
            ->select($db->quoteName('blocked_id'))
            ->from($db->quoteName('#__kunena_inbox_blocks'))
            ->where($db->quoteName('user_id') . ' = :user')
            ->order($db->quoteName('created_time') . ' DESC')
            ->bind(':user', $userid, ParameterType::INTEGER);

        return array_map(
            static fn ($id) => ['id' => (int) $id, 'name' => KunenaUserHelper::get((int) $id)->getName()],
            $db->setQuery($query)->loadColumn()
        );
    }

    /**
     * @param   int  $id  The id of the entry.
     *
     * @return  object|null
     *
     * @since   Kunena 7.2
     */
    protected static function getRow(int $id): ?object
    {
        $db    = self::db();
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__kunena_inbox'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        return $db->setQuery($query)->loadObject() ?: null;
    }

    /**
     * The list of entries of a user.
     *
     * @param   int     $userid  The user.
     * @param   string  $filter  all, message, notification or sent.
     * @param   int     $start   The first entry.
     * @param   int     $limit   The number of entries.
     *
     * @return  array  [total, entries]
     *
     * @since   Kunena 7.2
     */
    public static function getList(int $userid, string $filter, int $start, int $limit): array
    {
        $db    = self::db();
        $query = $db->createQuery()->from($db->quoteName('#__kunena_inbox'));

        if ($filter === 'sent') {
            $query->where($db->quoteName('sender_id') . ' = :user')
                ->where($db->quoteName('type') . ' = ' . $db->quote('message'))
                ->where($db->quoteName('deleted_sender') . ' = 0');
        } else {
            $query->where($db->quoteName('user_id') . ' = :user')
                ->where($db->quoteName('deleted_recipient') . ' = 0');

            if ($filter === 'message') {
                $query->where($db->quoteName('type') . ' = ' . $db->quote('message'));
            } elseif ($filter === 'notification') {
                $query->where($db->quoteName('type') . ' <> ' . $db->quote('message'));
            }
        }

        $query->bind(':user', $userid, ParameterType::INTEGER);

        $count = clone $query;
        $count->select('COUNT(*)');
        $total = (int) $db->setQuery($count)->loadResult();

        $query->select('*')->order($db->quoteName('id') . ' DESC');
        $query->setLimit($limit, $start);
        $rows = $db->setQuery($query)->loadObjectList();

        return [$total, self::withFiles(array_map(static fn ($row) => self::toArray($row, $userid, $filter === 'sent'), $rows))];
    }

    /**
     * The messages of a conversation (marked as read for the user), or a notification.
     *
     * @param   int  $userid  The user.
     * @param   int  $id      The id of an entry of the conversation.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    public static function getThread(int $userid, int $id): array
    {
        $row = self::getRow($id);

        if (!$row || ($row->user_id != $userid && $row->sender_id != $userid)) {
            throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR'));
        }

        if ($row->type !== 'message') {
            // A notification belongs to the user who gets it: the user who caused it (the sender) has no business in it.
            if ($row->user_id != $userid) {
                throw new RuntimeException(Text::_('PLG_KUNENA_INBOX_ERROR'));
            }

            self::markRead($userid, [$id]);

            return self::withFiles([self::toArray(self::getRow($id), $userid, false)]);
        }

        $db           = self::db();
        $conversation = (int) ($row->conversation_id ?: $row->id);
        $query        = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__kunena_inbox'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('message'))
            ->where('(' . $db->quoteName('conversation_id') . ' = :conversation OR ' . $db->quoteName('id') . ' = :conversation2)')
            ->where('((' . $db->quoteName('user_id') . ' = :user AND ' . $db->quoteName('deleted_recipient') . ' = 0) OR ('
                . $db->quoteName('sender_id') . ' = :user2 AND ' . $db->quoteName('deleted_sender') . ' = 0))')
            ->order($db->quoteName('id') . ' ASC')
            ->bind(':conversation', $conversation, ParameterType::INTEGER)
            ->bind(':conversation2', $conversation, ParameterType::INTEGER)
            ->bind(':user', $userid, ParameterType::INTEGER)
            ->bind(':user2', $userid, ParameterType::INTEGER);

        $rows = $db->setQuery($query)->loadObjectList();

        self::markRead($userid, array_map(static fn ($item) => (int) $item->id, $rows));

        return self::withFiles(array_map(static fn ($item) => self::toArray($item, $userid, false, true), $rows));
    }

    /**
     * Mark entries of the user as read (all when no ids are given).
     *
     * @param   int    $userid  The user.
     * @param   int[]  $ids     The entries.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function markRead(int $userid, array $ids = []): void
    {
        self::$unread = [];

        $db    = self::db();
        $now   = time();
        $query = $db->createQuery()
            ->update($db->quoteName('#__kunena_inbox'))
            ->set($db->quoteName('read_time') . ' = :now')
            ->where($db->quoteName('user_id') . ' = :user')
            ->where($db->quoteName('read_time') . ' = 0')
            ->bind(':now', $now, ParameterType::INTEGER)
            ->bind(':user', $userid, ParameterType::INTEGER);

        if ($ids) {
            $ids = array_map('intval', $ids);
            $query->whereIn($db->quoteName('id'), $ids);
        }

        $db->setQuery($query)->execute();
    }

    /**
     * Delete entries of the user: his side of a conversation, or a notification.
     *
     * @param   int    $userid  The user.
     * @param   int[]  $ids     The entries.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function delete(int $userid, array $ids): void
    {
        $ids = array_map('intval', $ids);

        if (!$ids) {
            return;
        }

        self::$unread = [];

        $db = self::db();

        foreach (['deleted_recipient' => 'user_id', 'deleted_sender' => 'sender_id'] as $flag => $field) {
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_inbox'))
                ->set($db->quoteName($flag) . ' = 1')
                ->where($db->quoteName($field) . ' = :user')
                ->whereIn($db->quoteName('id'), $ids)
                ->bind(':user', $userid, ParameterType::INTEGER);

            // A notification has no sender side.
            if ($flag === 'deleted_sender') {
                $query->where($db->quoteName('type') . ' = ' . $db->quote('message'));
            }

            $db->setQuery($query)->execute();
        }

        // What both sides have deleted (and notifications) is removed, with the files.
        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__kunena_inbox'))
            ->where($db->quoteName('deleted_recipient') . ' = 1')
            ->where('(' . $db->quoteName('deleted_sender') . ' = 1 OR ' . $db->quoteName('type') . ' <> ' . $db->quote('message') . ')')
            ->whereIn($db->quoteName('id'), $ids);

        InboxAttachments::deleteFiles(array_map('intval', $db->setQuery($query)->loadColumn()));

        $query = $db->createQuery()
            ->delete($db->quoteName('#__kunena_inbox'))
            ->where($db->quoteName('deleted_recipient') . ' = 1')
            ->where('(' . $db->quoteName('deleted_sender') . ' = 1 OR ' . $db->quoteName('type') . ' <> ' . $db->quote('message') . ')')
            ->whereIn($db->quoteName('id'), $ids);

        $db->setQuery($query)->execute();
    }

    /**
     * Add the files to the entries.
     *
     * @param   array[]  $items  The entries.
     *
     * @return  array[]
     *
     * @since   Kunena 7.2
     */
    protected static function withFiles(array $items): array
    {
        $files = InboxAttachments::getFiles(array_column($items, 'id'));

        foreach ($items as &$item) {
            $item['files'] = $files[$item['id']] ?? [];
        }

        return $items;
    }

    /**
     * An entry as it is shown by the page.
     *
     * @param   object  $row     The row.
     * @param   int     $userid  The user who looks at it.
     * @param   bool    $sent    The list of sent messages (the other user is the recipient).
     * @param   bool    $full    With the whole text.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    protected static function toArray(object $row, int $userid, bool $sent, bool $full = false): array
    {
        $name  = KunenaUserHelper::get((int) $row->sender_id)->getName();
        $url   = '';

        if ($row->type !== 'message') {
            $message = $row->message_id ? KunenaMessageHelper::get((int) $row->message_id) : null;
            $url     = $message && $message->exists() ? (string) $message->getPermaUrl() : '';
            $text    = Text::sprintf('PLG_KUNENA_INBOX_NOTIFY_' . strtoupper($row->type), $name, $row->subject);
        } else {
            $text = $full ? (string) $row->body : mb_substr(preg_replace('/\s+/', ' ', (string) $row->body), 0, 160);
        }

        return [
            'id'      => (int) $row->id,
            'type'    => $row->type,
            'from'    => $name,
            'from_id' => (int) $row->sender_id,
            'to_id'   => (int) $row->user_id,
            'to'      => $row->type === 'message' ? KunenaUserHelper::get((int) $row->user_id)->getName() : '',
            'mine'    => (int) $row->sender_id === $userid && $row->type === 'message',
            'subject' => (string) $row->subject,
            'text'    => $text,
            'url'     => $url,
            'time'    => HTMLHelper::_('date', (int) $row->created_time, Text::_('DATE_FORMAT_LC5')),
            'read'    => $row->read_time > 0 || (int) $row->user_id !== $userid,
        ];
    }
}
