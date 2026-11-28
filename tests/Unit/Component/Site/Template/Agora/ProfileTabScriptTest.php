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

namespace Kunena\Tests\Unit\Component\Site\Template\Agora;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Every layout of the profile of a user opens the tab which the address names (profile-tab.js): the link "New Ban" of the moderation
 * of a post goes to #banuser. The layout Profile of Agora had lost the script, and "New Ban" opened the profile on its first tab, the
 * form of the ban hidden in another one.
 */
class ProfileTabScriptTest extends UnitTestCase
{
    public function testEveryProfileLayoutOpensTheTabOfTheAddress(): void
    {
        $layouts = glob(Files::path('src/site/template') . '/*/{layouts,layoutsets/*/layouts}/user/item/default.php', GLOB_BRACE);

        $this->assertGreaterThanOrEqual(3, \count($layouts));

        foreach ($layouts as $layout) {
            $source = (string) file_get_contents($layout);

            $this->assertStringContainsString("'kunena.profile.tab'", $source, $layout);
            $this->assertStringContainsString("Uri::root() . 'media/kunena/core/js/profile-tab.js'", $source, $layout);
        }
    }
}
