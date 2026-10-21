<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Notification
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Notification;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The popups (toasts) of a user: somebody mentions him, replies in his topic or in a topic he follows, or starts a topic in
 * a category he follows. Nothing is stored: the new messages are asked from the messages, after the last message the
 * browser has seen.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaPopupNotification
{
    /**
     * Messages older than this (seconds) are never shown, whatever the last message of the browser was.
     */
    protected const MAX_AGE = 86400;

    /**
     * Most popups of one request: the others are only counted.
     */
    protected const MAX_ITEMS = 3;

    /**
     * The text (language string) of the title of the popup for each reason.
     */
    protected const TITLES = [
        'mention'    => 'COM_KUNENA_POPUP_MENTION',
        'reply'      => 'COM_KUNENA_POPUP_REPLY_OWN',
        'subscribed' => 'COM_KUNENA_POPUP_REPLY_SUBSCRIBED',
        'topic'      => 'COM_KUNENA_POPUP_NEW_TOPIC',
    ];

    /**
     * Are the popups on for the user (the forum setting, and the choice of the user)?
     *
     * @param   integer  $userid  The user.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function isEnabledFor(int $userid): bool
    {
        return $userid > 0
            && !empty(KunenaConfig::getInstance()->popupNotifications)
            && KunenaUserHelper::getJoomlaParam($userid, 'kunena_popup_notifications', 1) != 0;
    }

    /**
     * The id of the last message: the cursor of a browser which has seen all.
     *
     * @param   DatabaseInterface  $db  The database.
     *
     * @return  integer
     *
     * @since   Kunena 7.2
     */
    public static function cursor(DatabaseInterface $db): int
    {
        $query = $db->createQuery()
            ->select('MAX(' . $db->quoteName('id') . ')')
            ->from($db->quoteName('#__kunena_messages'));
        $db->setQuery($query);

        return (int) $db->loadResult();
    }

    /**
     * The messages after the cursor of the browser which are of interest for the user: he is mentioned in them, they are in a
     * topic of his or a topic he follows, or they start a topic in a category he follows. His own messages, the messages which
     * wait for approval and the messages of more than a day ago are not in it.
     *
     * @param   DatabaseInterface  $db      The database.
     * @param   integer            $userid  The user.
     * @param   integer            $after   The last message the browser has seen.
     * @param   integer            $cursor  The last message now.
     * @param   integer|null       $now     The time now (seconds), for the tests.
     *
     * @return  object[]  id, parent, mentioned, own and topicsub of each message, the oldest first.
     *
     * @since   Kunena 7.2
     */
    public static function candidates(DatabaseInterface $db, int $userid, int $after, int $cursor, ?int $now = null): array
    {
        $mention = '%[mention userid=' . $userid . ']%';
        $query   = $db->createQuery()
            ->select([
                $db->quoteName('m.id'),
                $db->quoteName('m.parent'),
                'CASE WHEN ' . $db->quoteName('mt.message') . ' LIKE ' . $db->quote($mention) . ' THEN 1 ELSE 0 END AS ' . $db->quoteName('mentioned'),
                'CASE WHEN ' . $db->quoteName('t.first_post_userid') . ' = ' . $userid . ' THEN 1 ELSE 0 END AS ' . $db->quoteName('own'),
                'COALESCE(' . $db->quoteName('ut.subscribed') . ', 0) AS ' . $db->quoteName('topicsub'),
            ])
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->join('INNER', $db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('m.thread'))
            ->join('INNER', $db->quoteName('#__kunena_messages_text', 'mt') . ' ON ' . $db->quoteName('mt.mesid') . ' = ' . $db->quoteName('m.id'))
            ->join(
                'LEFT',
                $db->quoteName('#__kunena_user_topics', 'ut') . ' ON ' . $db->quoteName('ut.topic_id') . ' = ' . $db->quoteName('m.thread')
                . ' AND ' . $db->quoteName('ut.user_id') . ' = ' . $userid
            )
            ->join(
                'LEFT',
                $db->quoteName('#__kunena_user_categories', 'uc') . ' ON ' . $db->quoteName('uc.category_id') . ' = ' . $db->quoteName('m.catid')
                . ' AND ' . $db->quoteName('uc.user_id') . ' = ' . $userid
            )
            ->where($db->quoteName('m.id') . ' > ' . $after)
            ->where($db->quoteName('m.id') . ' <= ' . $cursor)
            ->where($db->quoteName('m.hold') . ' = 0')
            ->where($db->quoteName('m.userid') . ' <> ' . $userid)
            ->where($db->quoteName('m.time') . ' > ' . (($now ?? time()) - self::MAX_AGE))
            ->where(
                '(' . $db->quoteName('t.first_post_userid') . ' = ' . $userid
                . ' OR ' . $db->quoteName('ut.subscribed') . ' > 0'
                . ' OR (' . $db->quoteName('m.parent') . ' = 0 AND ' . $db->quoteName('uc.subscribed') . ' = 1)'
                . ' OR ' . $db->quoteName('mt.message') . ' LIKE ' . $db->quote($mention) . ')'
            )
            ->order($db->quoteName('m.id') . ' ASC');
        $query->setLimit(100);
        $db->setQuery($query);

        return (array) $db->loadObjectList();
    }

    /**
     * The reason of the popup of a message: one popup for a message, with the strongest reason.
     *
     * @param   object  $row  A row of candidates().
     *
     * @return  string  mention, reply (in a topic of the user), subscribed (in a topic he follows) or topic (a new topic in a
     *                  category he follows).
     *
     * @since   Kunena 7.2
     */
    public static function reason(object $row): string
    {
        if ($row->mentioned) {
            return 'mention';
        }

        if ($row->own) {
            return 'reply';
        }

        return $row->topicsub > 0 ? 'subscribed' : 'topic';
    }

    /**
     * The new messages for the user.
     *
     * @param   integer  $userid  The user.
     * @param   integer  $after   The last message the browser has seen (0 when it has seen none: only the cursor is given).
     *
     * @return  array  cursor (the last message now), items (type, id, title, topic, excerpt, url) and more (not shown).
     *
     * @since   Kunena 7.2
     */
    public static function get(int $userid, int $after): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // The cursor is taken before the question, so a message which comes in between is shown the next time.
        $cursor = self::cursor($db);
        $result = ['cursor' => $cursor, 'items' => [], 'more' => 0];

        if ($after <= 0 || $after >= $cursor) {
            return $result;
        }

        try {
            $rows = self::candidates($db, $userid, $after, $cursor);
        } catch (Exception $e) {
            return $result;
        }

        if (!$rows) {
            return $result;
        }

        // Only the messages the user may read.
        $messages = KunenaMessageHelper::getMessages(array_map(static fn ($row) => (int) $row->id, $rows), 'read');
        $items    = [];

        foreach ($rows as $row) {
            $message = $messages[(int) $row->id] ?? null;

            if (!$message) {
                continue;
            }

            $name = $message->getAuthor()->getName('', false);

            // One popup for a message, with the strongest reason.
            $type  = self::reason($row);
            $title = Text::sprintf(self::TITLES[$type], $name);

            // The name in a mention is part of the text: [mention userid=1]Anna[/mention] reads @Anna.
            $raw     = (string) preg_replace('/\[mention[^\]]*\](.*?)\[\/mention\]/is', '@$1', (string) $message->message);
            $text    = (string) KunenaParser::stripBBCode($raw, 0, false);
            $excerpt = trim((string) preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));

            $items[] = [
                'id'      => (int) $message->id,
                'type'    => $type,
                'title'   => $title,
                'topic'   => html_entity_decode((string) $message->getTopic()->subject, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'excerpt' => mb_strimwidth($excerpt, 0, 140, '…', 'UTF-8'),
                'url'     => $message->getPermaUrl(null, false),
            ];
        }

        // The newest ones are shown; the others are only counted.
        if (\count($items) > self::MAX_ITEMS) {
            $result['more'] = \count($items) - self::MAX_ITEMS;
            $items          = \array_slice($items, -self::MAX_ITEMS);
        }

        $result['items'] = $items;

        return $result;
    }
}
