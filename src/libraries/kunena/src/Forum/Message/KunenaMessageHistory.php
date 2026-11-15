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
 * The edit history of the messages: each edit keeps the subject and the text of the message before it, with who edited, when and
 * the reason of the edit. The history page shows each edit as the change from that text to the next one (word by word).
 *
 * @since   Kunena 8.0
 */
abstract class KunenaMessageHistory
{
    /**
     * The setting editHistory: no history is kept.
     *
     * @since   Kunena 8.0
     */
    public const int OFF = 0;

    /**
     * The setting editHistory: the moderators of the category see the history.
     *
     * @since   Kunena 8.0
     */
    public const int MODERATORS = 1;

    /**
     * The setting editHistory: everybody who may read the message sees the history.
     *
     * @since   Kunena 8.0
     */
    public const int EVERYBODY = 2;

    /**
     * The most versions which are kept per message: the oldest ones go.
     *
     * @since   Kunena 8.0
     */
    public const int MAX_VERSIONS = 20;

    /**
     * The biggest comparison (the parts which differ, words times words) which is made word by word; a bigger one is shown as the
     * old text removed and the new one added (the table of the comparison takes memory).
     *
     * @since   Kunena 8.0
     */
    public const int MAX_DIFF_CELLS = 1000000;

    /**
     * The numbers of versions of the messages which are loaded: [message id => number].
     *
     * @var    array
     * @since  Kunena 8.0
     */
    protected static array $counts = [];

    /**
     * Whether the user sees the history of the message.
     *
     * @param   int   $setting    The setting editHistory.
     * @param   bool  $moderator  The user is a moderator of the category of the message.
     * @param   bool  $canRead    The user may read the message.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function canSee(int $setting, bool $moderator, bool $canRead): bool
    {
        return match ($setting) {
            self::MODERATORS => $moderator,
            self::EVERYBODY  => $moderator || $canRead,
            default          => false,
        };
    }

    /**
     * Whether an edit changed the subject or the text: only then is the old version kept.
     *
     * @param   array          $previous  The subject and the text before the edit.
     * @param   KunenaMessage  $message   The message after the edit.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function changed(array $previous, KunenaMessage $message): bool
    {
        return (string) ($previous['subject'] ?? '') !== (string) $message->subject
            || (string) ($previous['message'] ?? '') !== (string) $message->message;
    }

    /**
     * Keep the version before an edit, when the edit changed the subject or the text, and remove the oldest versions beyond
     * MAX_VERSIONS.
     *
     * @param   KunenaMessage  $message   The message after the edit (saved).
     * @param   array          $previous  The subject and the text before the edit.
     * @param   KunenaUser     $editor    The user who edited.
     *
     * @return  boolean  Whether a version was kept.
     *
     * @since   Kunena 8.0
     */
    public static function record(KunenaMessage $message, array $previous, KunenaUser $editor): bool
    {
        if ((int) $message->id <= 0 || !self::changed($previous, $message)) {
            return false;
        }

        $db      = self::getDatabase();
        $id      = (int) $message->id;
        $by      = (int) $editor->userid;
        $time    = (int) ($message->modified_time ?: time());
        $reason  = mb_substr((string) $message->modified_reason, 0, 255, 'UTF-8');
        $subject = mb_substr((string) ($previous['subject'] ?? ''), 0, 255, 'UTF-8');
        $text    = (string) ($previous['message'] ?? '');

        $query = $db->createQuery()
            ->insert($db->quoteName('#__kunena_messages_history'))
            ->columns($db->quoteName(['message_id', 'edited_by', 'edited_time', 'reason', 'subject', 'message']))
            ->values(':id, :by, :time, :reason, :subject, :message')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':by', $by, ParameterType::INTEGER)
            ->bind(':time', $time, ParameterType::INTEGER)
            ->bind(':reason', $reason)
            ->bind(':subject', $subject)
            ->bind(':message', $text);
        $db->setQuery($query)->execute();

        // The oldest versions beyond the limit.
        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__kunena_messages_history'))
            ->where($db->quoteName('message_id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->order($db->quoteName('id') . ' DESC');
        $old = \array_slice(array_map('intval', (array) $db->setQuery($query)->loadColumn()), self::MAX_VERSIONS);

        if ($old) {
            $query = $db->createQuery()
                ->delete($db->quoteName('#__kunena_messages_history'))
                ->whereIn($db->quoteName('id'), $old);
            $db->setQuery($query)->execute();
        }

        unset(self::$counts[$id]);

        return true;
    }

    /**
     * The versions of a message, the oldest first.
     *
     * @param   int  $id  The id of the message.
     *
     * @return  object[]  Rows with id, message_id, edited_by, edited_time, reason, subject and message.
     *
     * @since   Kunena 8.0
     */
    public static function getVersions(int $id): array
    {
        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__kunena_messages_history'))
            ->where($db->quoteName('message_id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->order($db->quoteName('id') . ' ASC');

        return (array) $db->setQuery($query)->loadObjectList();
    }

    /**
     * Load the numbers of versions of messages in one query (the messages of a page).
     *
     * @param   int[]  $ids  The ids of the messages.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function load(array $ids): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0)));

        if (!$ids) {
            return;
        }

        foreach ($ids as $id) {
            self::$counts[$id] = 0;
        }

        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select([$db->quoteName('message_id'), 'COUNT(*) AS ' . $db->quoteName('number')])
            ->from($db->quoteName('#__kunena_messages_history'))
            ->whereIn($db->quoteName('message_id'), $ids)
            ->group($db->quoteName('message_id'));

        foreach ((array) $db->setQuery($query)->loadObjectList() as $row) {
            self::$counts[(int) $row->message_id] = (int) $row->number;
        }
    }

    /**
     * The number of versions of a message (loaded with load(), else loaded now).
     *
     * @param   int  $id  The id of the message.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function getCount(int $id): int
    {
        if (!isset(self::$counts[$id])) {
            self::load([$id]);
        }

        return self::$counts[$id] ?? 0;
    }

    /**
     * The change from a text to another one, word by word, as HTML: the removed words in <del>, the added ones in <ins>, all escaped.
     * The words which both texts have at their start and their end are left out of the comparison; when the parts which remain are
     * too big (MAX_DIFF_CELLS), they are shown as removed and added.
     *
     * @param   string  $old  The text before.
     * @param   string  $new  The text after.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function diff(string $old, string $new): string
    {
        $escape = static fn (string $text): string => htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        if ($old === $new) {
            return $escape($new);
        }

        // Words and the spaces between them, both kept: the text comes back as it was.
        $a = preg_split('/(\s+)/u', $old, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        $b = preg_split('/(\s+)/u', $new, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];

        // The same words at the start and at the end: kept as they are, out of the comparison.
        $head = 0;

        while ($head < \count($a) && $head < \count($b) && $a[$head] === $b[$head]) {
            $head++;
        }

        $tail = 0;

        while ($tail < \count($a) - $head && $tail < \count($b) - $head && $a[\count($a) - 1 - $tail] === $b[\count($b) - 1 - $tail]) {
            $tail++;
        }

        $start = implode('', \array_slice($a, 0, $head));
        $end   = $tail ? implode('', \array_slice($a, -$tail)) : '';
        $a     = \array_slice($a, $head, \count($a) - $head - $tail);
        $b     = \array_slice($b, $head, \count($b) - $head - $tail);
        $n     = \count($a);
        $m     = \count($b);

        if (($n + 1) * ($m + 1) > self::MAX_DIFF_CELLS) {
            $removed = implode('', $a);
            $added   = implode('', $b);

            return $escape($start) . ($removed !== '' ? '<del>' . $escape($removed) . '</del>' : '')
                . ($added !== '' ? '<ins>' . $escape($added) . '</ins>' : '') . $escape($end);
        }

        // The longest common subsequence, from the end: $lcs[$i][$j] is its length for $a from $i and $b from $j.
        $lcs = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));

        for ($i = $n - 1; $i >= 0; $i--) {
            for ($j = $m - 1; $j >= 0; $j--) {
                $lcs[$i][$j] = $a[$i] === $b[$j] ? $lcs[$i + 1][$j + 1] + 1 : max($lcs[$i + 1][$j], $lcs[$i][$j + 1]);
            }
        }

        // The parts in order: [type, text] with the type '=', '-' or '+'; following parts of the same type are joined.
        $parts = [];
        $add   = static function (string $type, string $text) use (&$parts): void {
            $last = \count($parts) - 1;

            if ($last >= 0 && $parts[$last][0] === $type) {
                $parts[$last][1] .= $text;
            } else {
                $parts[] = [$type, $text];
            }
        };

        $i = 0;
        $j = 0;

        while ($i < $n && $j < $m) {
            if ($a[$i] === $b[$j]) {
                $add('=', $a[$i++]);
                $j++;
            } elseif ($lcs[$i + 1][$j] >= $lcs[$i][$j + 1]) {
                $add('-', $a[$i++]);
            } else {
                $add('+', $b[$j++]);
            }
        }

        while ($i < $n) {
            $add('-', $a[$i++]);
        }

        while ($j < $m) {
            $add('+', $b[$j++]);
        }

        $html = $escape($start);

        foreach ($parts as [$type, $text]) {
            $html .= match ($type) {
                '-'     => '<del>' . $escape($text) . '</del>',
                '+'     => '<ins>' . $escape($text) . '</ins>',
                default => $escape($text),
            };
        }

        return $html . $escape($end);
    }

    /**
     * Forget the loaded numbers (the tests).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function clearCache(): void
    {
        self::$counts = [];
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
