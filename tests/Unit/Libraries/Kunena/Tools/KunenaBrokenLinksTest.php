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

use Kunena\Forum\Libraries\Tools\KunenaBrokenLinks as B;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Broken links in posts" finds the links of a post and reads the answers; it never asks an address of a private
 * network.
 */
class KunenaBrokenLinksTest extends UnitTestCase
{
    public function testTheLinksOfAPost(): void
    {
        $text = 'See [url=https://a.example/page]this[/url], [b]https://b.example/x?y=1[/b]. Also [Kunena](https://c.example/) '
            . 'and <a href="http://d.example/">d</a> and https://a.example/page again.';

        $this->assertSame(['https://a.example/page', 'https://b.example/x?y=1', 'https://c.example/', 'http://d.example/'], B::extract($text));
        $this->assertSame([], B::extract('No link, only www.example.org and ftp://files.example'));
    }

    public function testWhatAnAnswerSays(): void
    {
        $this->assertSame(B::OK, B::verdict(200));
        $this->assertSame(B::OK, B::verdict(301));
        $this->assertSame(B::BROKEN, B::verdict(404));
        $this->assertSame(B::BROKEN, B::verdict(410));
        $this->assertSame(B::BROKEN, B::verdict(B::NO_HOST));
        $this->assertSame(B::UNSURE, B::verdict(403));
        $this->assertSame(B::UNSURE, B::verdict(503));
        $this->assertSame(B::UNSURE, B::verdict(0));
        $this->assertSame(B::SKIPPED, B::verdict(B::NOT_READ));
    }

    /**
     * No request into the network of the server: a private address, the server itself, a port which is no web port.
     */
    public function testAnAddressOfThePrivateNetworkIsNotRead(): void
    {
        $this->assertSame(B::NOT_READ, B::status('http://127.0.0.1/admin'));
        $this->assertSame(B::NOT_READ, B::status('http://192.168.1.1/'));
        $this->assertSame(B::NOT_READ, B::status('http://[::1]/'));
        $this->assertSame(B::NOT_READ, B::status('http://169.254.169.254/latest/meta-data/'));
        $this->assertSame(B::NOT_READ, B::status('https://8.8.8.8:22/'));
        $this->assertSame(B::NOT_READ, B::status('file:///etc/passwd'));
        $this->assertSame(B::NOT_READ, B::status('http://user:pass@8.8.8.8/'));
    }
}
