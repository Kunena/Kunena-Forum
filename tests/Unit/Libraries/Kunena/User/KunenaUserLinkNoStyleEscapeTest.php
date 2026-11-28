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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The link of the profile with the username or the real name (the option "optional_username" of the template) escapes the name, as
 * getLink() does with getName(): it went into the HTML as it was stored (a name written by an integration, an import or the
 * database was HTML). (The method reads the template of the site: its code is read.)
 */
class KunenaUserLinkNoStyleEscapeTest extends UnitTestCase
{
    public function testTheNamesAreEscaped(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/User/KunenaUser.php'));
        $start  = strpos($source, '    public function getLinkNoStyle(');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString('$name = $this->escape((string) $this->username);', $body);
        $this->assertStringContainsString('$name = $this->escape((string) $this->name);', $body);
        $this->assertStringNotContainsString('$name = $this->username;', $body);
    }
}
