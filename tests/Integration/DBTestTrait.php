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

use Joomla\Database\DatabaseDriver;

/**
 * The parts of DBTestInterface which every test needs, and helpers to put rows in the tables and to read them.
 */
trait DBTestTrait
{
    /**
     * @var     DatabaseDriver
     */
    private $driver;

    /**
     * @param   DatabaseDriver  $driver  The driver of the database of the tests.
     *
     * @return  void
     */
    public function setDBDriver(DatabaseDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return  DatabaseDriver
     */
    public function getDBDriver(): DatabaseDriver
    {
        return $this->driver;
    }

    /**
     * The datasets of the test: the tables of Kunena.
     *
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    /**
     * Put a row in a table.
     *
     * @param   string  $table  The table, with the prefix #__ (#__kunena_messages).
     * @param   array   $row    column => value.
     *
     * @return  void
     */
    protected function insertRow(string $table, array $row): void
    {
        DBTestHelper::markDirty($table);

        $db      = $this->getDBDriver();
        $columns = array_map(static fn ($column) => $db->quoteName($column), array_keys($row));
        $values  = array_map(static fn ($value) => $value === null ? 'NULL' : $db->quote((string) $value), array_values($row));

        $db->setQuery('INSERT INTO ' . $db->quoteName($table) . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')')->execute();
    }

    /**
     * Put rows in a table.
     *
     * @param   string  $table  The table, with the prefix #__.
     * @param   array   $rows   A list of rows (column => value).
     *
     * @return  void
     */
    protected function insertRows(string $table, array $rows): void
    {
        foreach ($rows as $row) {
            $this->insertRow($table, $row);
        }
    }

    /**
     * The number of rows of a table.
     *
     * @param   string  $table  The table, with the prefix #__.
     *
     * @return  integer
     */
    protected function countRows(string $table): int
    {
        $db = $this->getDBDriver();

        return (int) $db->setQuery('SELECT COUNT(*) FROM ' . $db->quoteName($table))->loadResult();
    }

    /**
     * The rows of a query.
     *
     * @param   string  $sql  The query (with the prefix #__).
     *
     * @return  array
     */
    protected function fetchAll(string $sql): array
    {
        return $this->getDBDriver()->setQuery($sql)->loadAssocList();
    }
}
