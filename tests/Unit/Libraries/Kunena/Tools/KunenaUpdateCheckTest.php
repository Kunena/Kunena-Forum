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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaUpdateCheck as U;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Before you update" compares the versions with the minimums of the package script, and finds the newest update
 * file of the database.
 */
class KunenaUpdateCheckTest extends UnitTestCase
{
    public function testTheVersions(): void
    {
        $this->assertSame(U::OK, U::versionStatus('8.4.3', '8.4.1'));
        $this->assertSame(U::OK, U::versionStatus('8.4.1-1+ubuntu24.04', '8.4.1'));
        $this->assertSame(U::PROBLEM, U::versionStatus('8.3.9', '8.4.1'));
        $this->assertSame(U::OK, U::versionStatus('10.11.6-MariaDB-log', '10.6'));
        $this->assertSame('10.11.6', U::clean('10.11.6-MariaDB'));
    }

    public function testTheNewestUpdateFile(): void
    {
        $this->assertSame('8.0.0-DEV', U::latestSchema(['/x/6.0.0-BETA1-DEV.sql', '/x/7.1.0-BETA3-DEV.sql', '/x/8.0.0-DEV.sql', '/x/README.md', '/x/7.10.0.sql']));
        $this->assertSame('', U::latestSchema([]));
    }

    /**
     * The plugins of the package, whoever wrote them, are no plugins of other developers.
     */
    public function testThePluginsOfThePackage(): void
    {
        $shipped = U::shippedPlugins(Files::read('src/pkg_kunena.xml'));

        $this->assertArrayHasKey('kunena/easyblog', $shipped);
        $this->assertArrayHasKey('kunena/easysocial', $shipped);
        $this->assertArrayHasKey('finder/kunena', $shipped);
        $this->assertArrayNotHasKey('kunena/someoneelse', $shipped);
        $this->assertNull(U::shippedPlugins(''));
    }

    /**
     * The minimums are those of the package script, which refuses to install below them.
     */
    public function testTheMinimumsAreThoseOfThePackage(): void
    {
        $script = Files::read('src/script.php');

        $this->assertStringContainsString("\$minimumJoomla = '" . U::MIN_JOOMLA . "'", $script);
        $this->assertMatchesRegularExpression("/'8\\.4'\\s*=>\\s*'" . preg_quote(U::MIN_PHP, '/') . "'/", $script);
        $this->assertMatchesRegularExpression("/'8\\.0'\\s*=>\\s*'" . preg_quote(U::MIN_MYSQL, '/') . "'/", $script);
        $this->assertMatchesRegularExpression("/'" . preg_quote(U::MIN_MARIADB, '/') . "'\\s*=>\\s*'" . preg_quote(U::MIN_MARIADB, '/') . "',\\s*'0'/", $script);
        $this->assertStringContainsString("\$extensions = ['" . implode("', '", U::EXTENSIONS) . "']", $script);
    }
}
