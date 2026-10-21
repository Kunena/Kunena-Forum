<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Tests\Integration;

use SimpleXMLElement;

/**
 * The tables of Kunena for the tests of the database: they are made from the schema which the installer of Kunena uses
 * (src/admin/sql/install/schema/install.xml), so the tests always have the tables of the code which they test.
 */
abstract class SchemaGenerator
{
    /**
     * The file of the schema, relative to the root of the project.
     */
    public const SCHEMA = 'src/admin/sql/install/schema/install.xml';

    /**
     * Types without a start value in MySQL (a text can not have a default).
     */
    private const NO_DEFAULT = ['text', 'tinytext', 'mediumtext', 'longtext', 'blob', 'tinyblob', 'mediumblob', 'longblob', 'json'];

    /**
     * The definition of the tables.
     *
     * @return  array<string,SimpleXMLElement>  The element of each table, by the name of the table without the prefix
     *                                          (kunena_messages).
     */
    public static function tables(): array
    {
        $xml    = simplexml_load_string((string) file_get_contents(\dirname(__DIR__, 2) . '/' . self::SCHEMA));
        $tables = [];

        foreach ($xml->table as $table) {
            $tables[(string) $table['name']] = $table;
        }

        return $tables;
    }

    /**
     * The columns of a table.
     *
     * @param   SimpleXMLElement  $table  The table of the schema.
     *
     * @return  string[]
     */
    public static function columns(SimpleXMLElement $table): array
    {
        $columns = [];

        foreach ($table->field as $field) {
            $columns[] = (string) $field['name'];
        }

        return $columns;
    }

    /**
     * The columns of the primary key of a table.
     *
     * @param   SimpleXMLElement  $table  The table of the schema.
     *
     * @return  string[]
     */
    public static function primaryKey(SimpleXMLElement $table): array
    {
        foreach ($table->key as $key) {
            if (strtoupper((string) $key['name']) === 'PRIMARY') {
                return array_map(static fn ($column) => (string) preg_replace('/\(.*$/', '', trim($column)), explode(',', (string) $key['columns']));
            }
        }

        $primary = [];

        foreach ($table->field as $field) {
            if ((string) $field['primary_key'] === 'yes') {
                $primary[] = (string) $field['name'];
            }
        }

        return $primary;
    }

    /**
     * The statement which makes a table (with the prefix #__ of Joomla in front of the name).
     *
     * @param   SimpleXMLElement  $table  The table of the schema.
     *
     * @return  string
     */
    public static function createStatement(SimpleXMLElement $table): string
    {
        $lines = [];

        foreach ($table->field as $field) {
            $lines[] = '  ' . self::column($field);
        }

        $primary = self::primaryKey($table);

        if ($primary) {
            $lines[] = '  PRIMARY KEY (' . implode(', ', array_map(static fn ($column) => '`' . $column . '`', $primary)) . ')';
        }

        foreach ($table->key as $key) {
            if (strtoupper((string) $key['name']) === 'PRIMARY') {
                continue;
            }

            $columns = [];

            foreach (explode(',', (string) $key['columns']) as $column) {
                $columns[] = (string) preg_replace('/^(\w+)(\(\d+\))?$/', '`$1`$2', trim($column));
            }

            $lines[] = '  ' . ((string) $key['unique'] === '1' ? 'UNIQUE KEY' : 'KEY') . ' `' . $key['name'] . '` (' . implode(', ', $columns) . ')';
        }

        return 'CREATE TABLE IF NOT EXISTS `#__' . $table['name'] . "` (\n" . implode(",\n", $lines) . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }

    /**
     * The definition of a column.
     */
    private static function column(SimpleXMLElement $field): string
    {
        $type = (string) $field['type'];
        $sql  = '`' . $field['name'] . '` ' . $type . ((string) $field['null'] === '0' ? ' NOT NULL' : ' NULL');

        $base = strtolower((string) preg_replace('/\(.*$/', '', $type));

        if (isset($field['default']) && !\in_array($base, self::NO_DEFAULT, true)) {
            $default = (string) $field['default'];

            // A zero date is refused by MySQL in its strict modes. The date which Kunena uses for "no date" is 1000-01-01, but a
            // timestamp starts in 1970 (in the time zone of the session: a year later is valid everywhere).
            if (str_starts_with($default, '0000-00-00')) {
                $default = $base === 'timestamp' ? '1971-01-01 00:00:00' : str_replace('0000-00-00', '1000-01-01', $default);
            }

            $sql .= is_numeric($default) || strtoupper($default) === 'CURRENT_TIMESTAMP' ? ' DEFAULT ' . $default : " DEFAULT '" . str_replace("'", "''", $default) . "'";
        }

        if ((string) $field['extra'] !== '') {
            $sql .= ' ' . strtoupper((string) $field['extra']);
        }

        return $sql;
    }
}
