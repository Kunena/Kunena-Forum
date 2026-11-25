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

use Kunena\Forum\Libraries\Tools\KunenaProfileSpam as P;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Signature and profile spam" counts the links of the fields of a profile, each link once.
 */
class KunenaProfileSpamTest extends UnitTestCase
{
    public function testEachLinkCountsOnce(): void
    {
        $this->assertSame(0, P::links('Hello, I like this forum.'));
        $this->assertSame(1, P::links('Buy at https://shop.example'));
        $this->assertSame(1, P::links('[url]https://shop.example[/url]'));
        $this->assertSame(1, P::links('[url=https://shop.example]cheap[/url]'));
        $this->assertSame(1, P::links('<a href="http://shop.example">cheap</a>'));
        $this->assertSame(1, P::links('see www.shop.example now'));
        $this->assertSame(1, P::links('see http://www.shop.example now'));
        $this->assertSame(1, P::links('[url]shop.example[/url]'));
        $this->assertSame(2, P::links('https://a.example and [url=b.example]b[/url]'));
    }

    public function testAnAddressWhichIsNoLink(): void
    {
        $this->assertSame(0, P::links('mail me at jane@www.example'));
        $this->assertSame(0, P::links('the www. prefix'));
    }

    public function testTheLinksOfAProfile(): void
    {
        $this->assertSame(
            ['signature' => 2, 'websiteurl' => 1],
            P::linksOf(['signature' => 'https://a.example https://b.example', 'personalText' => 'Hi', 'websiteurl' => 'https://c.example', 'location' => 'Paris'])
        );
    }
}
