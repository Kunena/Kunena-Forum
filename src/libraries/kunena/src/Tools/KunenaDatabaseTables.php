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
use Throwable;

/**
 * The tables of Kunena in the database (tool "Database tables"): their rows, their size and the space they could give back,
 * optimized or analyzed on demand. MySQL and MariaDB only.
 *
 * @since   Kunena 8.0
 */
class KunenaDatabaseTables
{
    /**
     * The operations: name => SQL statement.
     *
     * @since   Kunena 8.0
     */
    public const OPERATIONS = ['optimize' => 'OPTIMIZE TABLE', 'analyze' => 'ANALYZE TABLE'];

    /**
     * Is the database one whose tables this tool knows (MySQL, MariaDB)?
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isSupported(): bool
    {
        return Factory::getContainer()->get('DatabaseDriver')->getServerType() === 'mysql';
    }

    /**
     * The tables of Kunena: by name, their engine, rows, data, indexes, free space and collation.
     *
     * @return  array<string, array{engine: string, rows: int, data: int, index: int, free: int, collation: string}>
     *
     * @since   Kunena 8.0
     */
    public static function listOnSite(): array
    {
        if (!self::isSupported()) {
            return [];
        }

        $db     = Factory::getContainer()->get('DatabaseDriver');
        $prefix = str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $db->getPrefix() . 'kunena_');
        $tables = [];

        foreach ((array) $db->setQuery('SHOW TABLE STATUS LIKE ' . $db->quote($prefix . '%'))->loadAssocList() as $row) {
            $tables[(string) $row['Name']] = [
                'engine'    => (string) ($row['Engine'] ?? ''),
                'rows'      => (int) ($row['Rows'] ?? 0),
                'data'      => (int) ($row['Data_length'] ?? 0),
                'index'     => (int) ($row['Index_length'] ?? 0),
                'free'      => (int) ($row['Data_free'] ?? 0),
                'collation' => (string) ($row['Collation'] ?? ''),
            ];
        }

        ksort($tables);

        return $tables;
    }

    /**
     * The names of the request which are tables of the list (never another name in the SQL).
     *
     * @param   array  $requested  The names asked.
     * @param   array  $known      The tables (listOnSite()).
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function filterNames(array $requested, array $known): array
    {
        return array_values(array_intersect(array_unique(array_filter($requested, 'is_string')), array_keys($known)));
    }

    /**
     * Runs an operation on the chosen tables.
     *
     * @param   string    $operation  A key of OPERATIONS.
     * @param   string[]  $names      The tables.
     *
     * @return  array<string, string>  By table, the message of the database.
     *
     * @since   Kunena 8.0
     */
    public static function runOnSite(string $operation, array $names): array
    {
        if (!isset(self::OPERATIONS[$operation])) {
            return [];
        }

        $db      = Factory::getContainer()->get('DatabaseDriver');
        $results = [];

        foreach (self::filterNames($names, self::listOnSite()) as $name) {
            try {
                $rows = (array) $db->setQuery(self::OPERATIONS[$operation] . ' ' . $db->quoteName($name))->loadAssocList();
                $last = end($rows) ?: [];

                $results[$name] = (string) ($last['Msg_text'] ?? 'OK');
            } catch (Throwable $e) {
                $results[$name] = $e->getMessage();
            }
        }

        return $results;
    }
}
