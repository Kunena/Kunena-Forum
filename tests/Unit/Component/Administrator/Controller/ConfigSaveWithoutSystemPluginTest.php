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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The save of the configuration keeps the badges, the social websites and the groups of the ranks (they have their own views, the
 * form does not have them) and empties the cache of the settings itself: both were done by the plugin "System - Kunena" only, so
 * without it a save deleted them, and the forum kept the old settings while the page said "saved". (The save needs a whole site:
 * its code is read.)
 */
class ConfigSaveWithoutSystemPluginTest extends UnitTestCase
{
    private static function store(): string
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/ConfigController.php'));
        $save   = strpos($source, '$model->save([');

        return $save === false ? '' : $source;
    }

    public function testTheOwnParametersAreKept(): void
    {
        $source = self::store();
        $keep   = strpos($source, "foreach (['badges', 'socials', 'rankgroups'] as \$own) {");

        $this->assertNotFalse($keep);
        $this->assertLessThan(strpos($source, '$model->save(['), $keep);
        $this->assertStringContainsString('$valid[$own] = $current->get($own);', $source);
    }

    public function testTheCacheOfTheSettingsIsEmptied(): void
    {
        $source = self::store();
        $reset  = strpos($source, 'KunenaConfig::getInstance()->reset();');

        $this->assertNotFalse($reset);
        $this->assertGreaterThan(strpos($source, '$model->save(['), $reset);
    }
}
