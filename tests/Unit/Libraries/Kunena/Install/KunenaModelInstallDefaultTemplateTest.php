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
        $source = Files::read('src/libraries/kunena/src/Install/KunenaModelInstall.php');

        // migrateConfig() runs on an upgrade only, and saves the configuration with the defaults of config.xml: before the save, a
        // forum without a stored template gets Aurelia, which it had.
        $this->assertSame(1, preg_match('/public function migrateConfig\(\): void\s*\{(.*?)\n    \}/s', $source, $m));
        $guard = strpos($m[1], "if (!ComponentHelper::getParams('com_kunena')->exists('template')) {\n            \$config->template = 'aurelia';");
        $save  = strrpos($m[1], '$config->save();');

        $this->assertNotFalse($guard);
        $this->assertNotFalse($save);
        $this->assertLessThan($save, $guard);
    }
}
