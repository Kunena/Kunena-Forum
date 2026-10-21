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
use Joomla\Database\DatabaseFactory;
use mysqli;
use RuntimeException;

/**
 * The database of the integration tests: the connection, the tables and a clean state for every test.
 *
 * The database is a database of its own (never the database of a Joomla site). Where it is:
 *
 *  1. the constants KTEST_DB_HOST, KTEST_DB_NAME, KTEST_DB_USER and KTEST_DB_PASSWORD of phpunit.xml, or
 *  2. the environment variables KUNENA_TEST_DB_HOST, KUNENA_TEST_DB_NAME, KUNENA_TEST_DB_USER, KUNENA_TEST_DB_PASSWORD (and
 *     KUNENA_TEST_DB_PORT), or
 *  3. the server and the user of the Joomla installation of KUNENA_TEST_JOOMLA (its configuration.php), with the database
 *     <name of its database>_phpunit.
 *
 * When there is no database the tests which need one are skipped.
 */
class DBTestHelper
{
    /**
     * The prefix of the tables of the tests.
     */
    public const PREFIX = 'ktest_';

    /**
     * @var     DatabaseDriver|null
     */
    protected static $driver;

    /**
     * @var     string[]  Datasets which are loaded (their tables exist).
     */
    protected static $loaded = [];

    /**
     * @var     string[]  The tables (with #__) of each dataset.
     */
    protected static $tablesOf = [];

    /**
     * @var     string[]  Tables which a test has put rows in.
     */
    protected static $dirty = [];

    /**
     * Connect, load the datasets of the test and empty their tables.
     *
     * @param   IntegrationTestCase  $test  The test.
     *
     * @return  void
     *
     * @throws  DBUnavailableException  When there is no database for the tests.
     */
    public static function setupTest(IntegrationTestCase $test): void
    {
        $driver = self::driver();

        $test->setDBDriver($driver);

        // The tables of a dataset which is used for the first time can have rows of an earlier run: they are emptied once. After
        // that only the tables which a test has put rows in are emptied (see markDirty()): emptying all tables each time is slow.
        $tables = self::$dirty;

        foreach ($test->getSchemasToLoad() as $dataset) {
            if (!isset(self::$loaded[$dataset])) {
                self::$tablesOf[$dataset] = self::load($driver, $dataset);
                self::$loaded[$dataset]   = $dataset;
                $tables                   = array_merge($tables, self::$tablesOf[$dataset]);
            }
        }

        self::$dirty = [];

        self::truncate($driver, $tables);
    }

    /**
     * A test has put rows in a table: it is emptied before the next test.
     *
     * @param   string  $table  The table, with the prefix #__.
     *
     * @return  void
     */
    public static function markDirty(string $table): void
    {
        self::$dirty[$table] = $table;
    }

    /**
     * Empty tables.
     *
     * @param   DatabaseDriver  $driver  The driver.
     * @param   string[]        $tables  The tables, with the prefix #__.
     *
     * @return  void
     */
    protected static function truncate(DatabaseDriver $driver, array $tables): void
    {
        if (!$tables) {
            return;
        }

        $driver->setQuery('SET FOREIGN_KEY_CHECKS = 0')->execute();

        foreach (array_unique($tables) as $table) {
            $driver->setQuery('TRUNCATE TABLE ' . $driver->quoteName($table))->execute();
        }

        $driver->setQuery('SET FOREIGN_KEY_CHECKS = 1')->execute();
    }

    /**
     * The settings of the database.
     *
     * @return  array{host:string,name:string,user:string,password:string,port:int}|null  Null when there is none.
     */
    public static function settings(): ?array
    {
        // 1. phpunit.xml.
        if (\defined('KTEST_DB_NAME') && \defined('KTEST_DB_USER')) {
            return [
                'host'     => \defined('KTEST_DB_HOST') ? (string) \constant('KTEST_DB_HOST') : 'localhost',
                'name'     => (string) \constant('KTEST_DB_NAME'),
                'user'     => (string) \constant('KTEST_DB_USER'),
                'password' => \defined('KTEST_DB_PASSWORD') ? (string) \constant('KTEST_DB_PASSWORD') : '',
                'port'     => 3306,
            ];
        }

        // 2. The environment.
        if (getenv('KUNENA_TEST_DB_NAME') !== false && getenv('KUNENA_TEST_DB_USER') !== false) {
            return [
                'host'     => getenv('KUNENA_TEST_DB_HOST') !== false ? (string) getenv('KUNENA_TEST_DB_HOST') : 'localhost',
                'name'     => (string) getenv('KUNENA_TEST_DB_NAME'),
                'user'     => (string) getenv('KUNENA_TEST_DB_USER'),
                'password' => getenv('KUNENA_TEST_DB_PASSWORD') !== false ? (string) getenv('KUNENA_TEST_DB_PASSWORD') : '',
                'port'     => getenv('KUNENA_TEST_DB_PORT') !== false ? (int) getenv('KUNENA_TEST_DB_PORT') : 3306,
            ];
        }

        // 3. The server of the Joomla installation, with a database of its own.
        $config = self::joomlaConfiguration();

        if ($config) {
            return [
                'host'     => (string) $config->host,
                'name'     => $config->db . '_phpunit',
                'user'     => (string) $config->user,
                'password' => (string) $config->password,
                'port'     => 3306,
            ];
        }

        return null;
    }

    /**
     * The configuration of the Joomla installation of the tests (its configuration.php).
     *
     * @return  object|null
     */
    protected static function joomlaConfiguration(): ?object
    {
        $root = \defined('JPATH_ROOT') ? (string) \constant('JPATH_ROOT') : '';
        $file = $root . '/configuration.php';

        if ($root === '' || !is_file($file)) {
            return null;
        }

        if (!class_exists('JConfig', false)) {
            require_once $file;
        }

        return class_exists('JConfig', false) ? new \JConfig() : null;
    }

    /**
     * The driver of the database of the tests (made once).
     *
     * @return  DatabaseDriver
     *
     * @throws  DBUnavailableException
     */
    public static function driver(): DatabaseDriver
    {
        if (self::$driver) {
            return self::$driver;
        }

        if (!class_exists(DatabaseFactory::class)) {
            throw new DBUnavailableException('The database package of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $settings = self::settings();

        if (!$settings) {
            throw new DBUnavailableException('No database for the tests: see tests/Integration/README.md.');
        }

        // The tests empty their tables: the database of the Joomla site is never used.
        $site = self::joomlaConfiguration();

        if ($site && strcasecmp((string) $site->db, $settings['name']) === 0) {
            throw new DBUnavailableException('The database of the tests is the database of the Joomla site (' . $settings['name'] . '): use a database of its own.');
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $settings['name'])) {
            throw new DBUnavailableException('The name of the database of the tests is not valid.');
        }

        mysqli_report(MYSQLI_REPORT_OFF);

        $link = @new mysqli($settings['host'], $settings['user'], $settings['password'], '', $settings['port']);

        if ($link->connect_errno) {
            throw new DBUnavailableException('The server of the database of the tests does not answer: ' . $link->connect_error);
        }

        if (!$link->query('CREATE DATABASE IF NOT EXISTS `' . $settings['name'] . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')) {
            throw new DBUnavailableException('The database ' . $settings['name'] . ' can not be made: ' . $link->error);
        }

        $link->close();

        try {
            self::$driver = (new DatabaseFactory())->getDriver('mysqli', [
                'host'     => $settings['host'],
                'user'     => $settings['user'],
                'password' => $settings['password'],
                'database' => $settings['name'],
                'prefix'   => self::PREFIX,
                'port'     => $settings['port'],
            ]);

            // The tests put rows in with the columns they need: a column without a start value is not an error.
            self::$driver->setQuery("SET SESSION sql_mode = ''")->execute();
        } catch (\Throwable $e) {
            self::$driver = null;

            throw new DBUnavailableException('The database of the tests can not be opened: ' . $e->getMessage());
        }

        return self::$driver;
    }

    /**
     * Load a dataset.
     *
     * @param   DatabaseDriver  $driver   The driver.
     * @param   string          $dataset  'kunena' (the tables of the schema of the installer of Kunena) or a file of
     *                                    datasets/mysqli (joomla, or joomla.sql).
     *
     * @return  string[]  The tables (with #__) which it makes.
     */
    protected static function load(DatabaseDriver $driver, string $dataset): array
    {
        $statements = [];

        if ($dataset === 'kunena') {
            foreach (SchemaGenerator::tables() as $table) {
                $statements[] = SchemaGenerator::createStatement($table);
            }
        } else {
            $file = __DIR__ . '/datasets/mysqli/' . (str_ends_with($dataset, '.sql') ? $dataset : $dataset . '.sql');

            if (!is_file($file)) {
                throw new RuntimeException('The dataset ' . $dataset . ' does not exist.');
            }

            $statements = array_values(array_filter(array_map('trim', preg_split('/;\s*\n/', (string) file_get_contents($file)))));
        }

        $tables = [];

        foreach ($statements as $sql) {
            if (preg_match('/CREATE TABLE IF NOT EXISTS `(#__\w+)`/', $sql, $match)) {
                $tables[] = $match[1];
            }

            $driver->setQuery($sql)->execute();
        }

        return $tables;
    }
}
