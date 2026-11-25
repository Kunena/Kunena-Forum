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
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Throwable;

/**
 * The messages posted several times with the same text (tool "Duplicate posts"): a double post, or the same spam by one or
 * several accounts. The first copy stays; the others go to the trash on demand.
 *
 * @since   Kunena 8.0
 */
class KunenaDuplicatePosts
{
    /**
     * A shorter text is no duplicate ("Thanks!", "+1").
     *
     * @since   Kunena 8.0
     */
    public const MIN_LENGTH = 30;

    /**
     * At most this many recent messages are compared.
     *
     * @since   Kunena 8.0
     */
    public const SCAN_LIMIT = 5000;

    /**
     * At most this many groups are listed.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 100;

    /**
     * The text compared: no BBCode tag, no case, the spaces folded. Empty when it is too short to tell.
     *
     * @param   string  $text  The message.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function normalize(string $text): string
    {
        $text = preg_replace('#\[/?[a-z*][^\]]*\]#i', ' ', $text) ?? $text;
        $text = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $text) ?? $text));

        return mb_strlen($text) >= self::MIN_LENGTH ? $text : '';
    }

    /**
     * The groups of the messages with the same text, the biggest first; in a group the oldest message first (it stays).
     *
     * @param   array  $rows  The messages (id, time, message, and what else to show).
     *
     * @return  array  A list of groups, each a list of rows.
     *
     * @since   Kunena 8.0
     */
    public static function groups(array $rows): array
    {
        $byText = [];

        foreach ($rows as $row) {
            $key = self::normalize((string) ($row['message'] ?? ''));

            if ($key !== '') {
                $byText[md5($key)][] = $row;
            }
        }

        $groups = [];

        foreach ($byText as $group) {
            if (\count($group) < 2) {
                continue;
            }

            usort($group, static fn ($a, $b) => [(int) $a['time'], (int) $a['id']] <=> [(int) $b['time'], (int) $b['id']]);
            $groups[] = $group;
        }

        usort($groups, static fn ($a, $b) => [\count($b), (int) end($b)['time']] <=> [\count($a), (int) end($a)['time']]);

        return $groups;
    }

    /**
     * The copies of the groups: all the messages but the first of each group.
     *
     * @param   array  $groups  The groups (groups()).
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    public static function copies(array $groups): array
    {
        $ids = [];

        foreach ($groups as $group) {
            foreach (\array_slice($group, 1) as $row) {
                $ids[] = (int) $row['id'];
            }
        }

        return $ids;
    }

    /**
     * The groups among the recent published and unapproved messages of this site.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.thread', 'm.catid', 'm.subject', 'm.time', 'm.userid', 'm.name', 'm.hold', 't.message']))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('t.mesid') . ' = ' . $db->quoteName('m.id'))
            ->whereIn($db->quoteName('m.hold'), [KunenaForum::PUBLISHED, KunenaForum::UNAPPROVED])
            ->order($db->quoteName('m.id') . ' DESC');

        return \array_slice(self::groups((array) $db->setQuery($query, 0, self::SCAN_LIMIT)->loadAssocList()), 0, self::LIST_LIMIT);
    }

    /**
     * Moves the chosen copies to the trash: only messages which are copies now (never the first of a group).
     *
     * @param   int[]  $ids  The messages.
     *
     * @return  array{trashed: int, skipped: int}
     *
     * @since   Kunena 8.0
     */
    public static function trashOnSite(array $ids): array
    {
        $copies = array_flip(self::copies(self::findOnSite()));
        $result = ['trashed' => 0, 'skipped' => 0];

        foreach (array_unique(array_filter(array_map('intval', $ids))) as $id) {
            if (!isset($copies[$id])) {
                $result['skipped']++;

                continue;
            }

            try {
                $ok = KunenaMessageHelper::get($id)->publish(KunenaForum::DELETED);
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'trashed' : 'skipped']++;
        }

        return $result;
    }
}
