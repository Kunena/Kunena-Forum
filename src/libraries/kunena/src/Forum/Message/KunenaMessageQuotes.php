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

use Kunena\Forum\Libraries\Html\KunenaParser;

/**
 * The quotes of a reply: the [quote] block of a message, and the blocks of several messages which the user chose with Multi-quote
 * (the parameter quotes of the reply form: ids separated by commas).
 *
 * @since   Kunena 8.0
 */
abstract class KunenaMessageQuotes
{
    /**
     * The most messages which one reply quotes.
     *
     * @since   Kunena 8.0
     */
    public const int MAX_QUOTES = 10;

    /**
     * The ids of the parameter quotes: positive numbers, without duplicates, in their order, at most MAX_QUOTES.
     *
     * @param   mixed  $value  The value of the request ("12,15,18").
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    public static function parseIds(mixed $value): array
    {
        if (!\is_string($value) && !\is_int($value)) {
            return [];
        }

        $ids = [];

        foreach (explode(',', (string) $value) as $part) {
            $part = trim($part);

            if ($part === '' || !ctype_digit($part)) {
                continue;
            }

            $id = (int) $part;

            if ($id > 0 && !\in_array($id, $ids, true)) {
                $ids[] = $id;
            }

            if (\count($ids) === self::MAX_QUOTES) {
                break;
            }
        }

        return $ids;
    }

    /**
     * The [quote] block of a message: the name of its author (anonymous without one), its id and the id of the author, and its text
     * without the confidential and the private parts (the parser hides the confidential text, the private text is for nobody).
     *
     * @param   int     $id      The id of the message.
     * @param   int     $userid  The author.
     * @param   string  $name    The name of the author.
     * @param   string  $text    The text of the message.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function block(int $id, int $userid, string $name, string $text): string
    {
        $name = trim(str_replace(['"', '[', ']'], '', $name));
        $name = $name !== '' ? $name : 'anonymous';
        $text = KunenaParser::removeBlocks($text, ['confidential', 'private']);

        return "[quote=\"{$name} post={$id} userid={$userid}\"]" . $text . '[/quote]';
    }

    /**
     * The blocks of the messages which a reply quotes: the messages of the topic which the user may read (published, or which he may
     * see), the oldest first, separated by an empty line.
     *
     * @param   KunenaMessage[]  $messages  The messages (from the ids of the request).
     * @param   int              $topicId   The topic of the reply.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function build(array $messages, int $topicId): string
    {
        $quoted = array_filter(
            $messages,
            static fn ($message): bool => $message instanceof KunenaMessage && $message->exists() && (int) $message->thread === $topicId
                && $message->isAuthorised('read')
        );

        usort($quoted, static fn (KunenaMessage $a, KunenaMessage $b): int => (int) $a->id <=> (int) $b->id);

        $blocks = array_map(
            static fn (KunenaMessage $message): string => self::block(
                (int) $message->id,
                (int) $message->userid,
                (string) $message->getAuthor()->getName(),
                (string) $message->message
            ),
            $quoted
        );

        return $blocks ? implode("\n\n", $blocks) . "\n\n" : '';
    }
}
