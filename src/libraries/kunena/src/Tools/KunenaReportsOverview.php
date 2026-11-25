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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Log\KunenaLog;

/**
 * The reports of posts (tool "Reports overview"): the reports of a period from the log of Kunena, the users whose posts are
 * reported the most, and the users who report the most (a user who reports all the time abuses the button).
 *
 * @since   Kunena 8.0
 */
class KunenaReportsOverview
{
    /**
     * The periods, in days.
     *
     * @since   Kunena 8.0
     */
    public const PERIODS = [7, 30, 90, 365];

    /**
     * The reports shown.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 50;

    /**
     * The users the most often in a column of the reports.
     *
     * @param   array   $rows    The reports.
     * @param   string  $column  "user_id" (who reported) or "target_user" (whose post was reported).
     * @param   int     $limit   The users returned.
     *
     * @return  array<int, int>  user => reports, the most first.
     *
     * @since   Kunena 8.0
     */
    public static function top(array $rows, string $column, int $limit = 10): array
    {
        $counts = [];

        foreach ($rows as $row) {
            $user = (int) ($row[$column] ?? 0);

            if ($user > 0) {
                $counts[$user] = ($counts[$user] ?? 0) + 1;
            }
        }

        // The most first; the same number: the lowest id first.
        uksort($counts, static fn ($a, $b) => [$counts[$b], $a] <=> [$counts[$a], $b]);

        return \array_slice($counts, 0, $limit, true);
    }

    /**
     * A report as it is shown: its reason, its text and its post from the data of the log.
     *
     * @param   array  $row  id, time, user_id, target_user, topic_id, data.
     *
     * @return  array{id: int, time: int, reporter: int, author: int, topic: int, message: int, reason: string, text: string}
     *
     * @since   Kunena 8.0
     */
    public static function entry(array $row): array
    {
        $data = json_decode((string) ($row['data'] ?? ''), true);
        $data = \is_array($data) ? $data : [];

        return [
            'id'       => (int) $row['id'],
            'time'     => (int) $row['time'],
            'reporter' => (int) $row['user_id'],
            'author'   => (int) $row['target_user'],
            'topic'    => (int) $row['topic_id'],
            'message'  => (int) ($data['mesid'] ?? 0),
            'reason'   => trim((string) ($data['reason'] ?? '')),
            'text'     => trim((string) ($data['message'] ?? '')),
        ];
    }

    /**
     * The reports of this site over the last days.
     *
     * @param   int  $days  One of PERIODS.
     * @param   int  $now   The current time.
     *
     * @return  array{days: int, total: int, reports: array, reporters: array, authors: array, names: array, topics: array, logging: bool}
     *
     * @since   Kunena 8.0
     */
    public static function reportOnSite(int $days, int $now): array
    {
        $days  = \in_array($days, self::PERIODS, true) ? $days : self::PERIODS[1];
        $since = $now - $days * 86400;
        $type  = KunenaLog::TYPE_REPORT;
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'time', 'user_id', 'target_user', 'topic_id', 'data']))
            ->from($db->quoteName('#__kunena_logs'))
            ->where($db->quoteName('type') . ' = :type')
            ->where($db->quoteName('time') . ' >= :since')
            ->bind(':type', $type, ParameterType::INTEGER)
            ->bind(':since', $since, ParameterType::INTEGER)
            ->order($db->quoteName('id') . ' DESC');
        $rows      = (array) $db->setQuery($query)->loadAssocList();
        $reports   = array_map([self::class, 'entry'], \array_slice($rows, 0, self::LIMIT));
        $reporters = self::top($rows, 'user_id');
        $authors   = self::top($rows, 'target_user');

        $users  = array_values(array_unique(array_merge(array_keys($reporters), array_keys($authors), array_column($reports, 'reporter'), array_column($reports, 'author'))));
        $topics = array_values(array_unique(array_filter(array_column($reports, 'topic'))));
        $names  = [];
        $titles = [];

        if ($users) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'username']))->from($db->quoteName('#__users'))->whereIn($db->quoteName('id'), $users);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'username'));
        }

        if ($topics) {
            $query  = $db->createQuery()->select($db->quoteName(['id', 'category_id', 'subject', 'hold']))->from($db->quoteName('#__kunena_topics'))->whereIn($db->quoteName('id'), $topics);
            $titles = (array) $db->setQuery($query)->loadAssocList('id');
        }

        return [
            'days'      => $days,
            'total'     => \count($rows),
            'reports'   => $reports,
            'reporters' => $reporters,
            'authors'   => $authors,
            'names'     => $names,
            'topics'    => $titles,
            'logging'   => (bool) (KunenaConfig::getInstance()->logModeration ?? true),
        ];
    }
}
