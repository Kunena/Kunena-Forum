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
use SimpleXMLElement;

/**
 * The upgrade of an installed forum runs the version blocks of kunena.install.upgrade.xml (script.php, upgradeDatabase()), not the
 * update files of Joomla (the manifest has no schemas). So:
 * - the new tables and the mail templates of the update file of 8.0 must be in the block of 8.0 too: a forum upgraded from 7.x had
 *   none of the tables of 8.0 (drafts, reactions, bookmarks...), and deleting a message for good stopped halfway;
 * - every version block is a child of the upgrade: the block of 6.4.7 was inside the one of 6.4.5, so it never ran (the polls
 *   without an end date stayed NULL) and the upgrade reported its block as a failed action;
 * - the uninstall drops every table which the install makes.
 */
class UpgradeXmlTest extends UnitTestCase
{
    private const UPGRADE = 'src/admin/install/kunena.install.upgrade.xml';

    private const UPDATE = 'src/admin/sql/updates/mysql/8.0.0-DEV.sql';

    private const INSTALL = 'src/admin/sql/install/mysql/create_tables.utf8.sql';

    private const UNINSTALL = 'src/admin/sql/uninstall/mysql/uninstall.mysql.utf8.sql';

    private static function normalise(string $sql): string
    {
        return rtrim(trim((string) preg_replace('/\s+/', ' ', $sql)), ';');
    }

    public function testTheNewTablesAndMailTemplatesOfTheUpdateFileAreInTheUpgradeOf8(): void
    {
        $xml    = new SimpleXMLElement(Files::read(self::UPGRADE));
        $blocks = $xml->xpath("//upgrade/version[@version='8.0.0-DEV']");

        $this->assertCount(1, $blocks);

        $queries = array_map(static fn ($query) => self::normalise((string) $query), $blocks[0]->xpath('query'));
        $missing = [];

        foreach (explode(';', (string) preg_replace('/--[^\n]*/', '', Files::read(self::UPDATE))) as $statement) {
            $statement = self::normalise($statement);

            if (preg_match('/^(CREATE TABLE|INSERT)/i', $statement) && !\in_array($statement, $queries, true)) {
                $missing[] = substr($statement, 0, 80);
            }
        }

        $this->assertSame([], $missing);
    }

    /**
     * The columns which the install of 8.0 has and an upgraded forum lacked: the language of a category and of a topic (in the
     * install only: on a multilingual site the lists ended in an error, and the language of a topic was never saved), and the choice
     * of the age of a profile (the install of 7.1 missed it, and the step of 7.1.0-BETA2 runs only for older versions: every save
     * of a profile was an error). The block of 8.0 adds them (it runs on every upgrade to 8.0), and the update file too (the page
     * Database of Joomla and the update of a Git checkout read it).
     */
    public function testTheColumnsOfTheInstallWhichAnUpgradeLackedAreAdded(): void
    {
        $xml     = new SimpleXMLElement(Files::read(self::UPGRADE));
        $queries = implode("\n", array_map(static fn ($query) => self::normalise((string) $query), $xml->xpath("//upgrade/version[@version='8.0.0-DEV']/query")));
        $update  = Files::read(self::UPDATE);
        $install = Files::read(self::INSTALL);

        foreach (['kunena_categories' => 'language', 'kunena_topics' => 'language', 'kunena_users' => 'displayAgeBirthdate'] as $table => $column) {
            $this->assertMatchesRegularExpression('/`' . $column . '`\s/', $install, "$table.$column is in the install");
            $this->assertMatchesRegularExpression('/ALTER TABLE `#__' . $table . '` ADD `' . $column . '` /', $queries, "$table.$column in the block of 8.0");
            $this->assertMatchesRegularExpression('/ALTER TABLE `#__' . $table . '` ADD COLUMN `' . $column . '` /', $update, "$table.$column in the update file");
        }

        $this->assertStringContainsString('ADD KEY `language` (`language`)', $queries);
        $this->assertStringContainsString('ALTER TABLE `#__kunena_topics` ADD INDEX `language` (`language`);', $update);
    }

    /**
     * A new forum has the mail templates of the forum too, as the upgrade adds them: they were only in the update file and the block
     * of 8.0, so a new forum had none (Users > Mail Templates), and the option to use the mail templates did nothing.
     */
    public function testTheInstallHasTheMailTemplatesOfTheUpdate(): void
    {
        $rows = static function (string $file): array {
            preg_match_all('/INSERT IGNORE INTO `#__mail_templates`[^;]*;/s', Files::read($file), $matches);

            return array_map([self::class, 'normalise'], $matches[0]);
        };

        $this->assertCount(4, $rows(self::UPDATE));
        $this->assertSame($rows(self::UPDATE), $rows(self::INSTALL));
    }

    public function testEveryVersionBlockIsAChildOfTheUpgrade(): void
    {
        $xml = new SimpleXMLElement(Files::read(self::UPGRADE));

        $this->assertSame([], array_map(static fn ($version) => (string) $version['version'], $xml->xpath('//version/version')));
    }

    public function testTheUninstallDropsEveryTableOfTheInstall(): void
    {
        preg_match_all('/CREATE TABLE IF NOT EXISTS `#__(\w+)`/', Files::read(self::INSTALL), $created);
        preg_match_all('/DROP TABLE IF EXISTS `#__(\w+)`/', Files::read(self::UNINSTALL), $dropped);

        $this->assertSame([], array_values(array_diff($created[1], $dropped[1])));
    }
}
