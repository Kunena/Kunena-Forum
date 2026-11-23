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
