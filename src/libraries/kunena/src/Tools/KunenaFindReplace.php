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
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHistory;
use Kunena\Forum\Libraries\User\KunenaUser;
use Throwable;

/**
 * Find and replace a text in all the messages (tool "Find and replace"): a domain which changed, http:// images which must
 * be https://, an internal link after a migration. Plain text, not a pattern; each change goes to the history of the
 * message first, so an edit can be undone from there.
 *
 * @since   Kunena 8.0
 */
class KunenaFindReplace
{
    /**
     * The shortest text which can be searched.
     *
     * @since   Kunena 8.0
     */
    public const MIN_LENGTH = 3;

    /**
     * At most this many messages are changed in one run (the tool is run again for the rest).
     *
     * @since   Kunena 8.0
     */
    public const BATCH = 300;

    /**
     * At most this many matches are shown.
     *
     * @since   Kunena 8.0
     */
    public const PREVIEW = 50;

    /**
     * The options from a request.
     *
     * @param   array  $input  The request.
     *
     * @return  array{search: string, replace: string, subjects: bool, case: bool}
     *
     * @since   Kunena 8.0
     */
    public static function options(array $input): array
    {
        return [
            'search'   => (string) ($input['search'] ?? ''),
            'replace'  => (string) ($input['replace'] ?? ''),
            'subjects' => !empty($input['subjects']),
            'case'     => !empty($input['case']),
        ];
    }

    /**
     * Can the text be searched?
     *
     * @param   array  $options  The options.
     *
     * @return  string|null  The language key of the error, null when it can.
     *
     * @since   Kunena 8.0
     */
    public static function check(array $options): ?string
    {
        if (mb_strlen(trim($options['search'])) < self::MIN_LENGTH) {
            return 'COM_KUNENA_TOOLS_REPLACE_ERROR_SHORT';
        }

        if ($options['search'] === $options['replace']) {
            return 'COM_KUNENA_TOOLS_REPLACE_ERROR_SAME';
        }

        return null;
    }

    /**
     * Replaces the text.
     *
     * @param   string  $text     The text.
     * @param   string  $search   What to find.
     * @param   string  $replace  What it becomes.
     * @param   bool    $case     Is the case of the letters taken into account?
     *
     * @return  array{0: string, 1: int}  The new text and the number of replacements.
     *
     * @since   Kunena 8.0
     */
    public static function replace(string $text, string $search, string $replace, bool $case): array
    {
        if ($search === '') {
            return [$text, 0];
        }

        $count = 0;

        if ($case) {
            $new = str_replace($search, $replace, $text, $count);
        } else {
            $new = preg_replace('/' . preg_quote($search, '/') . '/iu', str_replace(['\\', '$'], ['\\\\', '\\$'], $replace), $text, -1, $count) ?? $text;
        }

        return [$new, $count];
    }

    /**
     * A short part of the text around the first match.
     *
     * @param   string  $text    The text.
     * @param   string  $search  What to find.
     * @param   bool    $case    Is the case taken into account?
     * @param   int     $around  The characters before and after.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function snippet(string $text, string $search, bool $case, int $around = 60): string
    {
        $pos = $case ? mb_strpos($text, $search) : mb_stripos($text, $search);

        if ($pos === false) {
            return mb_substr($text, 0, 2 * $around);
        }

        $start = max(0, $pos - $around);
        $part  = mb_substr($text, $start, mb_strlen($search) + 2 * $around);

        return ($start > 0 ? '…' : '') . $part . ($start + mb_strlen($part) < mb_strlen($text) ? '…' : '');
    }

    /**
     * The messages which contain the text: the first ones with a preview, and their number.
     *
     * @param   array  $options  The options.
     *
     * @return  array{total: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(array $options): array
    {
        $ids   = self::matchingIds($options);
        $items = [];

        foreach (\array_slice($ids, 0, self::PREVIEW) as $id) {
            $row = self::row($id);

            if (!$row) {
                continue;
            }

            [$text, $count] = self::replace((string) $row['message'], $options['search'], $options['replace'], $options['case']);
            [$subject, $sc] = $options['subjects'] ? self::replace((string) $row['subject'], $options['search'], $options['replace'], $options['case']) : [$row['subject'], 0];

            // The same part of the text before and after: the change applied to the part shown.
            $before = self::snippet((string) $row['message'], $options['search'], $options['case']);

            $items[] = [
                'id'         => (int) $row['id'],
                'thread'     => (int) $row['thread'],
                'catid'      => (int) $row['catid'],
                'subject'    => (string) $row['subject'],
                'newSubject' => (string) $subject,
                'before'     => $before,
                'after'      => self::replace($before, $options['search'], $options['replace'], $options['case'])[0],
                'count'      => $count + $sc,
            ];
        }

        return ['total' => \count($ids), 'items' => $items];
    }

    /**
     * Replaces the text in the messages, at most BATCH of them: the old text goes to the history of each message first.
     *
     * @param   array       $options  The options.
     * @param   KunenaUser  $editor   Who changes them.
     *
     * @return  array{changed: int, left: int}
     *
     * @since   Kunena 8.0
     */
    public static function applyOnSite(array $options, KunenaUser $editor): array
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $ids     = self::matchingIds($options);
        $changed = 0;
        $now     = time();
        $reason  = 'Find and replace';

        foreach (\array_slice($ids, 0, self::BATCH) as $id) {
            $row = self::row($id);

            if (!$row) {
                continue;
            }

            [$text, $count]     = self::replace((string) $row['message'], $options['search'], $options['replace'], $options['case']);
            [$subject, $count2] = $options['subjects'] ? self::replace((string) $row['subject'], $options['search'], $options['replace'], $options['case']) : [(string) $row['subject'], 0];

            if ($count + $count2 === 0) {
                continue;
            }

            try {
                $message                  = KunenaMessageHelper::get($id);
                $message->subject         = $subject;
                $message->message         = $text;
                $message->modified_time   = $now;
                $message->modified_reason = $reason;
                KunenaMessageHistory::record($message, ['subject' => (string) $row['subject'], 'message' => (string) $row['message']], $editor);

                $editorId = (int) $editor->userid;
                $query    = $db->createQuery()
                    ->update($db->quoteName('#__kunena_messages_text'))
                    ->set($db->quoteName('message') . ' = :message')
                    ->where($db->quoteName('mesid') . ' = :id')
                    ->bind(':message', $text)
                    ->bind(':id', $id, ParameterType::INTEGER);
                $db->setQuery($query)->execute();

                $query = $db->createQuery()
                    ->update($db->quoteName('#__kunena_messages'))
                    ->set($db->quoteName('subject') . ' = :subject')
                    ->set($db->quoteName('modified_by') . ' = :by')
                    ->set($db->quoteName('modified_time') . ' = :time')
                    ->set($db->quoteName('modified_reason') . ' = :reason')
                    ->where($db->quoteName('id') . ' = :id')
                    ->bind(':subject', $subject)
                    ->bind(':by', $editorId, ParameterType::INTEGER)
                    ->bind(':time', $now, ParameterType::INTEGER)
                    ->bind(':reason', $reason)
                    ->bind(':id', $id, ParameterType::INTEGER);
                $db->setQuery($query)->execute();

                $changed++;
            } catch (Throwable $e) {
                // The others are still changed.
            }
        }

        if ($changed && $options['subjects']) {
            // The subject of a topic is the one of its first message.
            $query = 'UPDATE ' . $db->quoteName('#__kunena_topics') . ' AS t INNER JOIN ' . $db->quoteName('#__kunena_messages') . ' AS m ON m.id = t.first_post_id'
                . ' SET t.subject = m.subject WHERE t.subject <> m.subject';

            try {
                $db->setQuery($query)->execute();
            } catch (Throwable $e) {
                // The messages are changed.
            }
        }

        KunenaCacheHelper::clear();

        return ['changed' => $changed, 'left' => max(0, \count($ids) - $changed)];
    }

    /**
     * The ids of the messages which contain the text (the database finds without the case, PHP checks the case when asked).
     *
     * @param   array  $options  The options.
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    private static function matchingIds(array $options): array
    {
        if (self::check($options) !== null) {
            return [];
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $like  = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $options['search']) . '%';
        $query = $db->createQuery()
            ->select([$db->quoteName('m.id'), $db->quoteName('m.subject'), $db->quoteName('t.message')])
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('t.mesid') . ' = ' . $db->quoteName('m.id'))
            ->where(
                '(' . $db->quoteName('t.message') . ' LIKE :like1'
                . ($options['subjects'] ? ' OR ' . $db->quoteName('m.subject') . ' LIKE :like2' : '') . ')'
            )
            ->bind(':like1', $like)
            ->order($db->quoteName('m.id'));

        if ($options['subjects']) {
            $query->bind(':like2', $like);
        }

        $ids = [];

        foreach ((array) $db->setQuery($query)->loadAssocList() as $row) {
            [, $count]  = self::replace((string) $row['message'], $options['search'], $options['replace'], $options['case']);
            [, $count2] = $options['subjects'] ? self::replace((string) $row['subject'], $options['search'], $options['replace'], $options['case']) : [null, 0];

            if ($count + $count2 > 0) {
                $ids[] = (int) $row['id'];
            }
        }

        return $ids;
    }

    /**
     * A message with its text.
     *
     * @param   int  $id  The message.
     *
     * @return  array|null
     *
     * @since   Kunena 8.0
     */
    private static function row(int $id): ?array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.thread', 'm.catid', 'm.subject', 't.message']))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('t.mesid') . ' = ' . $db->quoteName('m.id'))
            ->where($db->quoteName('m.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        return $db->setQuery($query)->loadAssoc() ?: null;
    }
}
