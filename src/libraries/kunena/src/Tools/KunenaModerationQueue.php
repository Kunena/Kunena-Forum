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
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Throwable;

/**
 * The work of the moderators in the whole forum (tool "Moderation queue"): the messages which wait for approval and the
 * recent reports, approved or trashed in bulk.
 *
 * @since   Kunena 8.0
 */
class KunenaModerationQueue
{
    /**
     * At most this many items of each list.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 200;

    /**
     * The reports of the last days which are shown.
     *
     * @since   Kunena 8.0
     */
    public const REPORT_DAYS = 30;

    /**
     * The messages approved.
     *
     * @since   Kunena 8.0
     */
    public const APPROVE = 'approve';

    /**
     * The messages moved to the trash.
     *
     * @since   Kunena 8.0
     */
    public const TRASH = 'trash';

    /**
     * A report from its row of the log: the message, the reason, the text (the data of the log is JSON).
     *
     * @param   array  $row  The row of #__kunena_logs.
     *
     * @return  array{mesid: int, reason: string, text: string}
     *
     * @since   Kunena 8.0
     */
    public static function parseReport(array $row): array
    {
        $data = json_decode((string) ($row['data'] ?? ''), true);
        $data = \is_array($data) ? $data : [];

        return [
            'mesid'  => (int) ($data['mesid'] ?? 0),
            'reason' => (string) ($data['reason'] ?? ''),
            'text'   => (string) ($data['message'] ?? ''),
        ];
    }

    /**
     * The messages which wait for approval, the oldest first.
     *
     * @return  array{total: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function unapprovedOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        // Not the messages of a topic in the trash: approving one published the topic again, with that message only (its other
        // messages stayed in the trash).
        // Nor a reply which waits for its date (scheduled, not held for review): it is for no moderator, and approving it
        // published it at once, before the date which its author chose.
        $shown = $db->quoteName('tp.hold') . ' < ' . KunenaForum::DELETED
            . ' AND (' . $db->quoteName('m.scheduled_time') . ' = 0 OR ' . $db->quoteName('m.scheduled_review') . ' <> 0)';
        $query = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_topics', 'tp') . ' ON ' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('m.thread'))
            ->where($db->quoteName('m.hold') . ' = ' . KunenaForum::UNAPPROVED)
            ->where($shown);
        $total = (int) $db->setQuery($query)->loadResult();

        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.thread', 'm.parent', 'm.catid', 'm.subject', 'm.time', 'm.userid', 'm.name', 'm.ip', 't.message']))
            ->select($db->quoteName('c.name', 'category'))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('t.mesid') . ' = ' . $db->quoteName('m.id'))
            ->innerJoin($db->quoteName('#__kunena_topics', 'tp') . ' ON ' . $db->quoteName('tp.id') . ' = ' . $db->quoteName('m.thread'))
            ->leftJoin($db->quoteName('#__kunena_categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('m.catid'))
            ->where($db->quoteName('m.hold') . ' = ' . KunenaForum::UNAPPROVED)
            ->where($shown)
            ->order($db->quoteName('m.time'));

        return ['total' => $total, 'items' => (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList()];
    }

    /**
     * The reports of the last REPORT_DAYS days, the newest first, with the state of their message.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function reportsOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $since = time() - self::REPORT_DAYS * 86400;
        $query = $db->createQuery()
            ->select($db->quoteName(['l.id', 'l.time', 'l.user_id', 'l.data', 'u.username']))
            ->from($db->quoteName('#__kunena_logs', 'l'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('l.user_id'))
            ->where($db->quoteName('l.type') . ' = ' . KunenaLog::TYPE_REPORT)
            ->where($db->quoteName('l.time') . ' >= :since')
            ->bind(':since', $since, ParameterType::INTEGER)
            ->order($db->quoteName('l.time') . ' DESC');
        $rows = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();

        $reports = [];
        $ids     = [];

        foreach ($rows as $row) {
            $report    = self::parseReport($row) + ['time' => (int) $row['time'], 'reporter' => (string) ($row['username'] ?? '')];
            $reports[] = $report;
            $ids[]     = $report['mesid'];
        }

        $messages = [];
        $ids      = array_values(array_unique(array_filter($ids)));

        if ($ids) {
            $query = $db->createQuery()
                ->select($db->quoteName(['id', 'thread', 'catid', 'subject', 'hold', 'userid', 'name']))
                ->from($db->quoteName('#__kunena_messages'))
                ->whereIn($db->quoteName('id'), $ids);
            $messages = (array) $db->setQuery($query)->loadAssocList('id');
        }

        foreach ($reports as &$report) {
            $report['message'] = $messages[$report['mesid']] ?? null;
        }

        unset($report);

        return $reports;
    }

    /**
     * Approves or trashes messages.
     *
     * @param   int[]   $ids     The messages.
     * @param   string  $action  APPROVE or TRASH.
     *
     * @return  array{done: int, failed: int}
     *
     * @since   Kunena 8.0
     */
    public static function apply(array $ids, string $action): array
    {
        $result = ['done' => 0, 'failed' => 0];
        $hold   = $action === self::APPROVE ? KunenaForum::PUBLISHED : KunenaForum::DELETED;

        foreach (array_unique(array_filter(array_map('intval', $ids))) as $id) {
            try {
                $message = KunenaMessageHelper::get($id);

                // Approve only what waits for approval, in a topic which is not in the trash; trash what is not trashed yet.
                $ok = $message->exists()
                    && ($action === self::APPROVE
                        ? (int) $message->hold === KunenaForum::UNAPPROVED && (int) $message->getTopic()->hold < KunenaForum::DELETED
                            && (!(int) $message->scheduled_time || (int) $message->scheduled_review)
                        : (int) $message->hold < KunenaForum::DELETED)
                    && $message->publish($hold);
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'done' : 'failed']++;
        }

        return $result;
    }
}
