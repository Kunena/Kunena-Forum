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
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Throwable;

/**
 * One topic edited in the administration (view "Topics", Edit): its subject, category, lock and stickiness, and the
 * subject and text of each of its messages. A message is saved as an edit on the site: the edit history keeps the
 * version before, the topic and the category are updated, no e-mail is sent.
 *
 * @since   Kunena 8.0
 */
class KunenaTopicEditor
{
    /**
     * The messages of a page.
     *
     * @since   Kunena 8.0
     */
    public const PAGE_SIZE = 20;

    /**
     * The longest subject (the column of the table).
     *
     * @since   Kunena 8.0
     */
    public const SUBJECT_LENGTH = 255;

    /**
     * The messages which the form changes: the ones whose subject or text differs from the saved one. A text is compared
     * with the line ends of the saved one (a browser sends CRLF).
     *
     * @param   array  $saved   The messages as they are: id => ['subject' => ..., 'message' => ...].
     * @param   array  $posted  The form: id => ['subject' => ..., 'message' => ...].
     *
     * @return  array{changes: array<int, array{subject: string, message: string}>, empty: int[], unknown: int[]}
     *          The changes; the messages left without a text or a subject (not saved); the ids which are not in the topic.
     *
     * @since   Kunena 8.0
     */
    public static function changes(array $saved, array $posted): array
    {
        $result = ['changes' => [], 'empty' => [], 'unknown' => []];

        foreach ($posted as $id => $fields) {
            $id = (int) $id;

            if (!isset($saved[$id])) {
                $result['unknown'][] = $id;

                continue;
            }

            $fields  = \is_array($fields) ? $fields : [];
            $subject = self::subject((string) ($fields['subject'] ?? $saved[$id]['subject']));
            $message = str_replace(["\r\n", "\r"], "\n", (string) ($fields['message'] ?? $saved[$id]['message']));
            $before  = str_replace(["\r\n", "\r"], "\n", (string) $saved[$id]['message']);

            if ($subject === (string) $saved[$id]['subject'] && $message === $before) {
                continue;
            }

            if ($subject === '' || trim($message) === '') {
                $result['empty'][] = $id;

                continue;
            }

            $result['changes'][$id] = ['subject' => $subject, 'message' => $message];
        }

        return $result;
    }

    /**
     * A subject as it is saved: one line, no space around it, not longer than the column.
     *
     * @param   string  $subject  The subject.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function subject(string $subject): string
    {
        return mb_substr(trim((string) preg_replace('/[\r\n\t]+/', ' ', $subject)), 0, self::SUBJECT_LENGTH);
    }

    /**
     * A topic of this site with a page of its messages, all states included.
     *
     * @param   int  $id    The topic.
     * @param   int  $page  The page of the messages.
     *
     * @return  array|null  topic, messages, total, page, pages; null when there is no such topic.
     *
     * @since   Kunena 8.0
     */
    public static function loadOnSite(int $id, int $page): ?array
    {
        $topic = KunenaTopicHelper::get($id);

        // A shadow of a moved topic has no messages of its own.
        if (!$topic->exists() || (int) $topic->moved_id) {
            return null;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('thread') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);
        $total = (int) $db->setQuery($query)->loadResult();
        $pages = max(1, (int) ceil($total / self::PAGE_SIZE));
        $page  = min(max(1, $page), $pages);

        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.parent', 'm.subject', 'm.userid', 'm.name', 'm.time', 'm.hold', 'm.modified_time', 'm.modified_reason', 't.message']))
            ->select($db->quoteName('u.username'))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('t.mesid') . ' = ' . $db->quoteName('m.id'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('m.userid'))
            ->where($db->quoteName('m.thread') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->order($db->quoteName('m.time') . ' ASC, ' . $db->quoteName('m.id') . ' ASC');

        return [
            'topic' => [
                'id'          => (int) $topic->id,
                'subject'     => (string) $topic->subject,
                'category_id' => (int) $topic->category_id,
                'locked'      => (bool) $topic->locked,
                'sticky'      => (int) $topic->ordering > 0,
                'hold'        => (int) $topic->hold,
                'first'       => (int) $topic->first_post_id,
            ],
            'messages' => (array) $db->setQuery($query, ($page - 1) * self::PAGE_SIZE, self::PAGE_SIZE)->loadAssocList('id'),
            'total'    => $total,
            'page'     => $page,
            'pages'    => $pages,
        ];
    }

    /**
     * Saves a topic of this site: its settings, then the changed messages, each as an edit of the current user.
     *
     * @param   int     $id       The topic.
     * @param   array   $fields   The topic: category, locked, sticky.
     * @param   array   $posted   The messages of the form: id => ['subject' => ..., 'message' => ...].
     * @param   string  $reason   The reason of the edit, shown under the edited messages.
     *
     * @return  array{saved: int, empty: int, failed: int, moved: bool|null, topic: bool}  moved: null when it stays.
     *
     * @since   Kunena 8.0
     */
    public static function saveOnSite(int $id, array $fields, array $posted, string $reason): array
    {
        $result = ['saved' => 0, 'empty' => 0, 'failed' => 0, 'moved' => null, 'topic' => false];
        $topic  = KunenaTopicHelper::get($id);

        if (!$topic->exists() || (int) $topic->moved_id) {
            return $result;
        }

        // The messages first: the subject of the first one is the subject of the topic.
        $ids   = array_values(array_filter(array_map('intval', array_keys($posted))));
        $saved = [];

        foreach ($ids ? KunenaMessageHelper::getMessages($ids, 'none') : [] as $message) {
            if ((int) $message->thread === $id) {
                $saved[(int) $message->id] = ['subject' => (string) $message->subject, 'message' => (string) $message->message, 'object' => $message];
            }
        }

        $found            = self::changes($saved, $posted);
        $result['empty']  = \count($found['empty']);
        $result['failed'] = \count($found['unknown']);
        $me               = KunenaUserHelper::getMyself();
        $reason           = mb_substr(trim($reason), 0, 200);

        foreach ($found['changes'] as $mesid => $change) {
            $message = $saved[$mesid]['object'];

            try {
                $message->edit(['subject' => $change['subject'], 'message' => $change['message'], 'modified_reason' => $reason], $me);
                $ok = $message->save();
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'saved' : 'failed']++;

            if ($ok && $mesid === (int) $topic->first_post_id) {
                $topic->subject = $change['subject'];
            }
        }

        // The settings of the topic.
        $topic->locked   = !empty($fields['locked']) ? 1 : 0;
        $topic->ordering = !empty($fields['sticky']) ? 1 : 0;

        try {
            $result['topic'] = (bool) $topic->save();
        } catch (Throwable $e) {
            $result['topic'] = false;
        }

        $target = (int) ($fields['category'] ?? 0);

        if ($target > 0 && $target !== (int) $topic->category_id) {
            $category = KunenaCategoryHelper::get($target);

            try {
                // A section holds no topic.
                $result['moved'] = $category->exists() && !$category->isSection() && (bool) $topic->move($category);
            } catch (Throwable $e) {
                $result['moved'] = false;
            }
        }

        return $result;
    }
}
