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

namespace Kunena\Tests\Unit\Codebase;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The update file of the version in development is named after the newest version block of the upgrade of the installer, so
 * that its name follows the version of the branch (8.0.0-DEV.sql on Kunena 8.0, not the name of 7.2 which it had before).
 */
class UpdateSqlFilesTest extends UnitTestCase
{
    private const UPGRADE = 'src/admin/install/kunena.install.upgrade.xml';

    private const UPDATES = 'src/admin/sql/updates/mysql/';

    /**
     * The version after which the update file of Kunena 8.0 was named first.
     */
    private const OLD_VERSION = '7.2.0';

    public function testTheNewestVersionOfTheUpgradeHasAnUpdateFile(): void
    {
        preg_match_all('/<version\s+version="([^"@]+)"/', Files::read(self::UPGRADE), $matches);
        $this->assertNotEmpty($matches[1], 'The upgrade of the installer has no named version block.');

        $newest = array_reduce(
            $matches[1],
            static fn (?string $carry, string $version): string => $carry === null || version_compare(strtolower($version), strtolower($carry), '>') ? $version : $carry
        );

        $this->assertFileExists(Files::path(self::UPDATES . $newest . '.sql'), 'The newest version of the upgrade is ' . $newest . ': its update file is missing.');
    }

    public function testTheUpdateFileOfTheOldVersionNameIsGone(): void
    {
        $this->assertFileDoesNotExist(Files::path(self::UPDATES . self::OLD_VERSION . '-DEV.sql'));
    }

    /**
     * The Database page of the Joomla installer checks the tables with these files. It reads the table name of a CREATE TABLE up to
     * the next space, so with the bracket on the next line the name is "table(" and the page reports a table which is there as
     * missing.
     */
    public function testTheBracketOfACreateTableIsOnTheLineOfTheTableName(): void
    {
        $wrong = [];

        foreach (glob(Files::path(self::UPDATES) . '*.sql') ?: [] as $file) {
            $sql = str_replace("\r\n", "\n", (string) file_get_contents($file));

            if (preg_match('/CREATE TABLE[^;(]*\n\s*\(/i', $sql)) {
                $wrong[] = basename($file);
            }
        }

        $this->assertSame([], $wrong, 'A CREATE TABLE has its bracket on a new line in: ' . implode(', ', $wrong));
    }

    /**
     * Kunena runs its own updates: the manifest must not make Joomla run the update files as well. Joomla lists Kunena on its Database
     * page through the row of #__schemas which the installer script writes.
     */
    public function testJoomlaDoesNotRunTheUpdateFilesButTheInstallerScriptRecordsTheSchema(): void
    {
        $manifest = Files::read('src/admin/kunena.xml');
        $script   = Files::read('src/admin/script.php');

        $this->assertStringNotContainsString('<schemas>', $manifest);
        $this->assertStringContainsString('$this->setSchemaVersion();', $script);
        $this->assertStringContainsString("'#__schemas'", $script);
    }
}
