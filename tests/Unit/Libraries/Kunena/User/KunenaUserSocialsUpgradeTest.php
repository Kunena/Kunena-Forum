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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Kunena\Forum\Libraries\User\KunenaUserSocials;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The social websites which are shut down are removed from the stored profiles by every way a site is upgraded: the update file of
 * Joomla (sql/updates) and the upgrade file of the installer of Kunena (install/kunena.install.upgrade.xml, which script.php runs).
 */
class KunenaUserSocialsUpgradeTest extends UnitTestCase
{
    private function root(): string
    {
        return \dirname(__DIR__, 5) . '/src/admin/';
    }

    public function testTheRetiredProvidersAreListed(): void
    {
        $this->assertSame(['skype', 'yim', 'google'], KunenaUserSocials::RETIRED);
    }

    public function testTheUpdateFileOfJoomlaCleansEveryRetiredProvider(): void
    {
        $sql = (string) file_get_contents($this->root() . 'sql/updates/mysql/7.2.0-DEV.sql');

        $this->assertStringContainsString('JSON_REMOVE', $sql);

        foreach (KunenaUserSocials::RETIRED as $key) {
            $this->assertStringContainsString("'$.$key'", $sql, "The update file does not remove $key.");
        }
    }

    public function testTheUpgradeFileOfTheInstallerCleansEveryRetiredProvider(): void
    {
        $xml     = simplexml_load_file($this->root() . 'install/kunena.install.upgrade.xml');
        $queries = [];

        foreach ($xml->upgrade[0]->version as $version) {
            if ((string) $version['version'] === '7.2.0-DEV') {
                foreach ($version->query as $query) {
                    $queries[] = (string) $query;
                }
            }
        }

        $cleaning = array_values(array_filter($queries, static fn ($query) => str_contains($query, 'JSON_REMOVE')));

        $this->assertCount(1, $cleaning, 'The block 7.2.0-DEV has no query which cleans the stored socials.');

        foreach (KunenaUserSocials::RETIRED as $key) {
            $this->assertStringContainsString("'$.$key'", $cleaning[0], "The upgrade file does not remove $key.");
        }

        // All the rows: no condition on a user.
        $this->assertStringNotContainsString('userid', $cleaning[0]);
    }
}
