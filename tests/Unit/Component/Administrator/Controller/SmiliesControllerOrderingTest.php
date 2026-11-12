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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Forum\Administrator\Controller\SmiliesController;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The manual order of the smilies: the positions which a drag in the backend list gives, and the column in every way a site gets
 * its tables (a new install, the upgrade of the installer of Kunena, the update file of Joomla, the sample data).
 */
class SmiliesControllerOrderingTest extends UnitTestCase
{
    private function root(): string
    {
        return \dirname(__DIR__, 5) . '/src/';
    }

    private function positions(array $current, array $posted): array
    {
        $this->requireJoomla();

        return $this->invoke(SmiliesController::class, 'orderPositions', $current, $posted);
    }

    public function testARowMovedDownComesAfterTheRowsItPassed(): void
    {
        // The list posts what the script of Joomla computes when the first row is dropped after the third one
        $positions = $this->positions([1 => 1, 2 => 2, 3 => 3, 4 => 4], [2 => 1, 3 => 2, 1 => 3, 4 => 4]);

        $this->assertSame([2 => 1, 3 => 2, 1 => 3, 4 => 4], $positions);
    }

    public function testARowMovedUpComesBeforeTheRowsItPassed(): void
    {
        $positions = $this->positions([1 => 1, 2 => 2, 3 => 3, 4 => 4], [1 => 1, 4 => 2, 2 => 3, 3 => 4]);

        $this->assertSame([1 => 1, 4 => 2, 2 => 3, 3 => 4], $positions);
    }

    public function testThePositionsWithGapsAreNumberedFromOne(): void
    {
        // The orderings of the upgrade are the ids, with the gaps of the smilies which were deleted
        $positions = $this->positions([3 => 3, 7 => 7, 12 => 12], [7 => 3, 3 => 4]);

        $this->assertSame([7 => 1, 3 => 2, 12 => 3], $positions);
    }

    public function testTheDuplicatesAreSortedById(): void
    {
        $positions = $this->positions([5 => 0, 2 => 0, 9 => 1, 4 => 1], []);

        $this->assertSame([2 => 1, 5 => 2, 4 => 3, 9 => 4], $positions);
    }

    public function testTheUnknownIdsAreIgnored(): void
    {
        $positions = $this->positions([1 => 1, 2 => 2], [99 => 0, 2 => 0]);

        $this->assertSame([2 => 1, 1 => 2], $positions);
    }

    public function testNoSmileyGivesNoPosition(): void
    {
        $this->assertSame([], $this->positions([], [3 => 1]));
    }

    public function testANewInstallCreatesTheColumn(): void
    {
        $sql = (string) file_get_contents($this->root() . 'admin/sql/install/mysql/create_tables.utf8.sql');

        $this->assertMatchesRegularExpression(
            "/CREATE TABLE IF NOT EXISTS `#__kunena_smileys`[^;]*`ordering`\s+int\(11\)\s+NOT NULL default '0'/i",
            $sql
        );

        $xml    = simplexml_load_file($this->root() . 'admin/sql/install/schema/install.xml');
        $fields = $xml->xpath('//table[@name="kunena_smileys"]/field[@name="ordering"]');

        $this->assertCount(1, $fields, 'The schema of the installer has no ordering on kunena_smileys.');
        $this->assertSame('int(11)', (string) $fields[0]['type']);
        $this->assertSame('0', (string) $fields[0]['null']);
        $this->assertSame('0', (string) $fields[0]['default']);
    }

    public function testTheUpgradeFileOfTheInstallerAddsAndFillsTheColumn(): void
    {
        $xml     = simplexml_load_file($this->root() . 'admin/install/kunena.install.upgrade.xml');
        $queries = [];

        foreach ($xml->upgrade[0]->version as $version) {
            if ((string) $version['version'] === '8.0.0-DEV') {
                foreach ($version->query as $query) {
                    $queries[] = (string) $query;
                }
            }
        }

        $add  = $this->indexOf($queries, "ALTER TABLE `#__kunena_smileys` ADD `ordering` int(11) NOT NULL default '0' AFTER `emoticonbar`");
        $fill = $this->indexOf($queries, 'UPDATE `#__kunena_smileys` SET `ordering` = `id` WHERE `ordering` = 0');

        $this->assertNotNull($add, 'The block 8.0.0-DEV does not add the ordering of the smilies.');
        $this->assertNotNull($fill, 'The block 8.0.0-DEV does not fill the ordering of the smilies.');
        $this->assertGreaterThan($add, $fill);
    }

    public function testTheUpdateFileOfJoomlaAddsAndFillsTheColumn(): void
    {
        $sql = (string) file_get_contents($this->root() . 'admin/sql/updates/mysql/7.2.0-DEV.sql');

        $add  = strpos($sql, "ALTER TABLE `#__kunena_smileys` ADD `ordering` int(11) NOT NULL default '0' AFTER `emoticonbar`;");
        $fill = strpos($sql, 'UPDATE `#__kunena_smileys` SET `ordering` = `id` WHERE `ordering` = 0;');

        $this->assertNotFalse($add, 'The update file does not add the ordering of the smilies.');
        $this->assertNotFalse($fill, 'The update file does not fill the ordering of the smilies.');
        $this->assertGreaterThan($add, $fill);
    }

    public function testTheSampleDataOrdersTheSmiliesByTheirId(): void
    {
        $php   = (string) file_get_contents($this->root() . 'libraries/kunena/src/Install/KunenaSampleData.php');
        $start = strpos($php, "->insert(\$db->quoteName('#__kunena_smileys'))");
        $end   = strpos($php, "\$queries[] = ['kunena_smileys', \$query];");

        $this->assertNotFalse($start);
        $this->assertNotFalse($end);

        $insert = substr($php, $start, $end - $start);

        $this->assertStringContainsString("\$db->quoteName('ordering')", $insert);
        $this->assertSame(34, preg_match_all("/->values\('(\d+), .*, [01], (\d+)'\)/", $insert, $rows));
        $this->assertSame($rows[1], $rows[2], 'The ordering of a smiley of the sample data is not its id.');
    }

    private function indexOf(array $queries, string $query): ?int
    {
        $index = array_search($query, array_map('trim', $queries), true);

        return $index === false ? null : $index;
    }
}
