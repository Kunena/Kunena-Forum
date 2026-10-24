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

namespace Kunena\Tests\Integration\Libraries\Kunena\Install;

use Exception;
use FilesystemIterator;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use Kunena\Tests\Integration\SchemaGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The tables of the schema of the installer of Kunena (src/admin/sql/install/schema/install.xml) made in a real database: the tables
 * and the columns are what the schema says, and the code only asks for tables which the schema has.
 */
class KunenaSchemaTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    /**
     * Tables which the code names but neither the schema of the installer nor the install file of a plugin has (found by the first
     * run of this test). A new one fails; one which gets a table is taken out of the list.
     * - kunena_keywords: the class TableKunenaKeywords is for a table which is not made, and no code uses the class.
     * - kunena_karma: KunenaKarma reads and writes it, but the installer does not make it (a new install fails when karma is used).
     */
    private const KNOWN_NOT_IN_SCHEMA = ['kunena_keywords', 'kunena_karma'];

    public static function tables(): array
    {
        $cases = [];

        foreach (array_keys(SchemaGenerator::tables()) as $name) {
            $cases[$name] = [$name];
        }

        return $cases;
    }

    public function testTheSchemaHasTheTablesOfTheForum(): void
    {
        $this->assertGreaterThanOrEqual(30, \count(SchemaGenerator::tables()));

        foreach (['kunena_messages', 'kunena_messages_text', 'kunena_topics', 'kunena_categories', 'kunena_aliases', 'kunena_announcement', 'kunena_user_topics', 'kunena_user_categories'] as $table) {
            $this->assertArrayHasKey($table, SchemaGenerator::tables());
        }
    }

    public function testEveryTableOfTheSchemaIsMade(): void
    {
        $db     = $this->getDBDriver();
        $tables = array_map('strtolower', $db->getTableList());

        foreach (array_keys(SchemaGenerator::tables()) as $name) {
            $this->assertContains(strtolower($db->getPrefix() . $name), $tables, "The table $name is not made.");
        }
    }

    #[DataProvider('tables')]
    public function testTheColumnsOfTheTable(string $name): void
    {
        $db       = $this->getDBDriver();
        $expected = SchemaGenerator::columns(SchemaGenerator::tables()[$name]);
        $columns  = array_column($this->fetchAll('SHOW COLUMNS FROM ' . $db->quoteName('#__' . $name)), 'Field');

        $this->assertSame($expected, $columns, "The columns of $name are not the columns of the schema.");
    }

    #[DataProvider('tables')]
    public function testThePrimaryKeyOfTheTable(string $name): void
    {
        $db       = $this->getDBDriver();
        $expected = SchemaGenerator::primaryKey(SchemaGenerator::tables()[$name]);
        $keys     = $this->fetchAll('SHOW KEYS FROM ' . $db->quoteName('#__' . $name) . " WHERE Key_name = 'PRIMARY'");

        usort($keys, static fn ($a, $b) => (int) $a['Seq_in_index'] <=> (int) $b['Seq_in_index']);

        $this->assertSame($expected, array_column($keys, 'Column_name'), "The primary key of $name is not the primary key of the schema.");
    }

    public function testTheAliasIsTheKeyOfTheTableOfAliases(): void
    {
        $this->insertRow('#__kunena_aliases', ['alias' => 'general', 'type' => 'catid', 'item' => '2']);

        // The alias of a category is unique (the update of 7.2 makes it the primary key).
        $this->expectException(Exception::class);

        $this->insertRow('#__kunena_aliases', ['alias' => 'general', 'type' => 'catid', 'item' => '3']);
    }

    public function testTheStartValuesOfTheSchemaAreUsed(): void
    {
        $this->insertRow('#__kunena_announcement', ['title' => 'Title', 'sdescription' => '', 'description' => '']);

        $row = $this->fetchAll('SELECT showdate, published, publish_up FROM ' . $this->getDBDriver()->quoteName('#__kunena_announcement'))[0];

        $this->assertSame('1', (string) $row['showdate']);
        $this->assertSame('0', (string) $row['published']);
        // The date which Kunena uses for "no date" (the code compares with it).
        $this->assertStringStartsWith('1000-01-01', (string) $row['publish_up']);
    }

    /**
     * The tables which a plugin makes with its own install file (the inbox, the reports).
     *
     * @return  string[]
     */
    private static function pluginTables(): array
    {
        $tables = [];

        foreach (glob(\dirname(__DIR__, 5) . '/src/plugins/*/sql/install.mysql.utf8.sql') ?: [] as $file) {
            if (preg_match_all('/CREATE TABLE(?: IF NOT EXISTS)?\s+`?#__(kunena_\w+)`?/i', (string) file_get_contents($file), $matches)) {
                $tables = array_merge($tables, $matches[1]);
            }
        }

        return $tables;
    }

    public function testTheCodeAsksOnlyForTablesOfTheSchema(): void
    {
        $named = [];
        $root  = str_replace('\\', '/', \dirname(__DIR__, 5) . '/src');

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            // The updates of old versions name old tables, and the libraries of other projects are not ours.
            if (!str_ends_with($path, '.php') || str_contains($path, '/External/') || str_contains($path, '/install/sql/') || str_contains($path, '/Install/')) {
                continue;
            }

            foreach (explode("\n", (string) file_get_contents($path)) as $line) {
                // A comment can name a table (the docblock of a class): only the code counts.
                if (preg_match('/^\s*(\*|\/\/|\/\*|#)/', $line)) {
                    continue;
                }

                if (preg_match_all('/#__(kunena_\w+)/', $line, $matches)) {
                    foreach ($matches[1] as $table) {
                        $named[$table] = substr($path, \strlen($root) + 1);
                    }
                }
            }
        }

        $known   = array_merge(array_keys(SchemaGenerator::tables()), self::pluginTables(), self::KNOWN_NOT_IN_SCHEMA);
        $missing = array_diff(array_keys($named), $known);

        $this->assertNotEmpty($named);
        $this->assertSame([], array_values($missing), 'Tables which the code asks for and nothing makes: ' . implode(', ', $missing));
    }

    public function testTheListOfKnownMissingTablesIsStillTrue(): void
    {
        foreach (self::KNOWN_NOT_IN_SCHEMA as $table) {
            $this->assertArrayNotHasKey($table, SchemaGenerator::tables(), "$table is in the schema now: take it out of KNOWN_NOT_IN_SCHEMA.");
            $this->assertNotContains($table, self::pluginTables());
        }
    }
}
