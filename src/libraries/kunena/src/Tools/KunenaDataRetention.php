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
use Kunena\Forum\Libraries\Log\KunenaLog;
use Throwable;

/**
 * The tables which only grow (tool "Data retention"): the log of Kunena, the earlier versions of edited posts and the read
 * markers. Their rows stay valid (Diagnostics removes the broken ones), but after a while nobody needs them; on a big forum
 * they become the biggest tables. The rows older than an age are deleted:
 * - the log keeps the changes of the settings (Settings history);
 * - a read marker is only deleted when its topic has had no post for longer than Kunena shows a topic as new (91 days,
 *   KunenaSession::getAllReadTime()): such a marker is never read again.
 *
 * @since   Kunena 8.0
 */
class KunenaDataRetention
{
    /**
     * The tables: key => [table, column of the time].
     *
     * @since   Kunena 8.0
     */
    public const TABLES = [
        'logs'    => ['#__kunena_logs', 'time'],
        'history' => ['#__kunena_messages_history', 'edited_time'],
        'read'    => ['#__kunena_user_read', 'time'],
    ];

    /**
     * The ages, in days.
     *
     * @since   Kunena 8.0
     */
    public const AGES = [90, 180, 365, 730, 1825];

    /**
     * Kunena shows a topic as new for so many days at most.
     *
     * @since   Kunena 8.0
     */
    public const NEW_DAYS = 91;

    /**
     * The time before which a row is deleted.
     *
     * @param   string  $key   A key of TABLES.
     * @param   int     $days  One of AGES.
     * @param   int     $now   The current time.
     *
     * @return  int
     *
     * @since   Kunena 8.0
     */
    public static function cutoff(string $key, int $days, int $now): int
    {
        $cutoff = $now - $days * 86400;

        // A read marker of a topic which can still be new is needed.
        return $key === 'read' ? min($cutoff, $now - (self::NEW_DAYS + 1) * 86400) : $cutoff;
    }

    /**
     * The numbers of the tables of this site: rows, size and the oldest row.
     *
     * @return  array<string, array{rows: int, bytes: int, oldest: int}>
     *
     * @since   Kunena 8.0
     */
    public static function statsOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $stats = [];

        foreach (self::TABLES as $key => [$table, $column]) {
            $query = $db->createQuery()
                ->select('COUNT(*) AS ' . $db->quoteName('rows'))
                ->select('MIN(' . $db->quoteName($column) . ') AS ' . $db->quoteName('oldest'))
                ->from($db->quoteName($table));
            $row   = (array) $db->setQuery($query)->loadAssoc();
            $bytes = 0;

            try {
                $name  = $db->replacePrefix($table);
                $query = $db->createQuery()
                    ->select('(' . $db->quoteName('DATA_LENGTH') . ' + ' . $db->quoteName('INDEX_LENGTH') . ')')
                    ->from($db->quoteName('information_schema.TABLES'))
                    ->where($db->quoteName('TABLE_SCHEMA') . ' = DATABASE()')
                    ->where($db->quoteName('TABLE_NAME') . ' = :name')
                    ->bind(':name', $name);
                $bytes = (int) $db->setQuery($query)->loadResult();
            } catch (Throwable $e) {
                // Without access to information_schema, no size.
            }

            $stats[$key] = ['rows' => (int) ($row['rows'] ?? 0), 'bytes' => $bytes, 'oldest' => (int) ($row['oldest'] ?? 0)];
        }

        return $stats;
    }

    /**
     * The rows of a table which are older than the age.
     *
     * @param   string  $key   A key of TABLES.
     * @param   int     $days  One of AGES.
     * @param   int     $now   The current time.
     *
     * @return  int
     *
     * @since   Kunena 8.0
     */
    public static function countOnSite(string $key, int $days, int $now): int
    {
        if (!isset(self::TABLES[$key])) {
            return 0;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        return (int) $db->setQuery(self::query($key, $days, $now, true))->loadResult();
    }

    /**
     * Deletes the rows of a table which are older than the age.
     *
     * @param   string  $key   A key of TABLES.
     * @param   int     $days  One of AGES.
     * @param   int     $now   The current time.
     *
     * @return  int  The rows deleted.
     *
     * @since   Kunena 8.0
     */
    public static function pruneOnSite(string $key, int $days, int $now): int
    {
        if (!isset(self::TABLES[$key]) || !\in_array($days, self::AGES, true)) {
            return 0;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $db->setQuery(self::query($key, $days, $now, false))->execute();

        return (int) $db->getAffectedRows();
    }

    /**
     * The statement which counts or deletes the old rows of a table.
     *
     * @param   string  $key    A key of TABLES.
     * @param   int     $days   The age.
     * @param   int     $now    The current time.
     * @param   bool    $count  Count (true) or delete (false).
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private static function query(string $key, int $days, int $now, bool $count): string
    {
        $db             = Factory::getContainer()->get('DatabaseDriver');
        [$table, $time] = self::TABLES[$key];
        $cutoff         = self::cutoff($key, $days, $now);

        if ($key === 'read') {
            // Only the markers of the topics without a post since the cutoff (a topic which is gone: Diagnostics).
            $from  = $db->quoteName($table, 'r') . ' INNER JOIN ' . $db->quoteName('#__kunena_topics', 't')
                . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('r.topic_id');
            $where = $db->quoteName('r.time') . ' < ' . $cutoff . ' AND ' . $db->quoteName('t.last_post_time') . ' < ' . $cutoff;

            return ($count ? 'SELECT COUNT(*) FROM ' : 'DELETE ' . $db->quoteName('r') . ' FROM ') . $from . ' WHERE ' . $where;
        }

        $where = $db->quoteName($time) . ' < ' . $cutoff;

        // The log keeps the changes of the settings.
        if ($key === 'logs') {
            $where .= ' AND ' . $db->quoteName('operation') . ' <> ' . $db->quote(KunenaLog::LOG_CONFIG_EDIT);
        }

        return ($count ? 'SELECT COUNT(*) FROM ' : 'DELETE FROM ') . $db->quoteName($table) . ' WHERE ' . $where;
    }
}
