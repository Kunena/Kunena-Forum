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

namespace Kunena\Tests\Unit\Libraries\Kunena\Install;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A new install of Kunena 8.0 has the template Agora; a forum which is upgraded keeps the template it has (Aurelia when it never
 * stored one, the former default).
 */
class KunenaModelInstallDefaultTemplateTest extends UnitTestCase
{
    public function testANewInstallHasAgora(): void
    {
        $xml = simplexml_load_file(Files::path('src/admin/config.xml'));

        $this->assertSame(['agora'], array_map('strval', $xml->xpath('//field[@name="template"]/@default')));
        $this->assertDirectoryExists(Files::path('src/site/template/agora'));
    }

    public function testAnUpgradedForumKeepsItsTemplate(): void
    {
        // The step of the upgrade of 8.0 which gives Aurelia to a forum from Kunena 7 without a stored template (it was in
        // migrateConfig() of the old web installer, which nothing ran: an upgraded forum got Agora). Its behaviour is tested in
        // Integration/Component/Administrator/Install/KeepTemplateOfUpgradedForumTest.php.
        $xml  = simplexml_load_file(Files::path('src/admin/install/kunena.install.upgrade.xml'));
        $step = '8.0.0-2026-11-26_keep_template_of_upgraded_forum';

        $this->assertCount(1, $xml->xpath('//version[@version="8.0.0-DEV"]/phpfile[@name="' . $step . '"]'));
        $this->assertFileExists(Files::path('src/admin/install/sql/updates/php/' . $step . '.php'));
    }
}
