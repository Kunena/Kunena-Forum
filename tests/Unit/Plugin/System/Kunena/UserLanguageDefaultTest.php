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

namespace Kunena\Tests\Unit\Plugin\System\Kunena;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A user who sets his language of Joomla back to "Use default" (an empty language) gets the mails of the forum in the default
 * language: only a language which was not empty was stored, so the forum kept the old one, and his mails stayed in it. Parameters
 * without the language change nothing. (The event needs a whole site: its code is read.)
 */
class UserLanguageDefaultTest extends UnitTestCase
{
    public function testTheDefaultLanguageIsStoredToo(): void
    {
        $source = Files::read('src/plugins/plg_system_kunena/src/Extension/Kunena.php');

        $this->assertStringNotContainsString("if (\$language !== '' && \\intval(\$user['id'] ?? 0) > 0) {", $source);
        $this->assertStringContainsString("property_exists(\$params, 'language')", $source);
    }
}
