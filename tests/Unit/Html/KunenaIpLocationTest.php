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

namespace Kunena\Forum\Tests\Unit\Html;

use Kunena\Forum\Libraries\Html\KunenaIpLocation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The place of an IP address: a local or reserved address has none, and no plugin is asked for it.
 */
class KunenaIpLocationTest extends TestCase
{
    public static function localAddresses(): array
    {
        return [
            'loopback'        => ['127.0.0.1'],
            'loopback v6'     => ['::1'],
            'private 10'      => ['10.0.0.5'],
            'private 172'     => ['172.16.4.1'],
            'private 192'     => ['192.168.1.20'],
            'link local'      => ['169.254.10.10'],
            'unique local v6' => ['fd12:3456:789a::1'],
            'not an address'  => ['not-an-ip'],
            'empty'           => [''],
        ];
    }

    #[DataProvider('localAddresses')]
    public function testALocalAddressHasNoPlace(string $ip): void
    {
        $this->assertNull(KunenaIpLocation::get($ip));
    }

    public function testSpacesAroundTheAddressAreIgnored(): void
    {
        $this->assertNull(KunenaIpLocation::get('  127.0.0.1  '));
    }

    public function testTheLinkOfALocalAddressHasNoPlace(): void
    {
        $html = KunenaIpLocation::html('192.168.1.20');

        $this->assertStringContainsString('https://search.dnslytics.com/ip/192.168.1.20', $html);
        $this->assertStringContainsString('IP: 192.168.1.20', $html);
        $this->assertStringNotContainsString('&middot;', $html);
        $this->assertStringContainsString('rel="nofollow noopener noreferrer"', $html);
    }

    public function testTheAddressIsEscapedInTheLink(): void
    {
        $html = KunenaIpLocation::html('"><script>alert(1)</script>');

        $this->assertStringNotContainsString('<script>', $html);
    }
}
