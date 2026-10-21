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
 * A test which uses the database of the tests (see IntegrationTestCase and DBTestHelper).
 */
interface DBTestInterface
{
    /**
     * @param   DatabaseDriver  $driver  The driver of the database of the tests.
     *
     * @return  void
     */
    public function setDBDriver(DatabaseDriver $driver);

    /**
     * @return  DatabaseDriver
     */
    public function getDBDriver(): DatabaseDriver;

    /**
     * The datasets which the test needs: 'kunena' (the tables of Kunena, made from the schema of the installer) and the files of
     * datasets/mysqli (joomla.sql: the tables of Joomla which Kunena uses).
     *
     * @return  string[]
     */
    public function getSchemasToLoad(): array;
}
