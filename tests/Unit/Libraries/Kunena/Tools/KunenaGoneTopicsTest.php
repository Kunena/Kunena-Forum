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

use Kunena\Forum\Libraries\Tools\KunenaGoneTopics as G;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Deleted topic addresses" sends an address only to a web address or a path of the site.
 */
class KunenaGoneTopicsTest extends UnitTestCase
{
    public function testAPlaceToSendAVisitorTo(): void
    {
        $this->assertTrue(G::validTarget('https://www.example.org/forum/new-topic'));
        $this->assertTrue(G::validTarget('http://example.org'));
        $this->assertTrue(G::validTarget('/index.php/forum/general'));
    }

    public function testNoScriptAndNoOtherSiteThroughSlashes(): void
    {
        $this->assertFalse(G::validTarget('javascript:alert(1)'));
        $this->assertFalse(G::validTarget('data:text/html,x'));
        $this->assertFalse(G::validTarget('//evil.example/'));
        $this->assertFalse(G::validTarget('/\\evil.example/'));
        $this->assertFalse(G::validTarget("/forum\r\nSet-Cookie: x"));
        $this->assertFalse(G::validTarget('forum/general'));
        $this->assertFalse(G::validTarget(''));
        $this->assertFalse(G::validTarget('https:///nohost'));
    }
}
