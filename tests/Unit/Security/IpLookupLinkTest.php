<?php

/**
 * Kunena Component
 *
 * The link with the IP address of a message (for moderators) printed the stored value as it was. A new message holds a checked
 * address, but an older one of a site behind a load balancer can hold the text of the header X-Forwarded-For: markup for the
 * moderator who opens the topic. The value is encoded, and only a real address becomes a link.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use Kunena\Forum\Libraries\User\KunenaIpAddress;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if (!\defined('_JEXEC')) {
    \define('_JEXEC', 1);
}

require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaIpAddress.php';

class IpLookupLinkTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    public static function realAddresses(): array
    {
        return [
            'ipv4' => ['203.0.113.9', '203.0.113.9'],
            'ipv6' => ['2001:db8::1', '2001%3Adb8%3A%3A1'],
        ];
    }

    #[DataProvider('realAddresses')]
    public function testRealAddressIsALinkToTheLookup(string $ip, string $inAddress): void
    {
        $html = KunenaIpAddress::lookupLink($ip);

        $this->assertSame(
            '<a href="https://search.dnslytics.com/ip/' . $inAddress . '" target="_blank" rel="nofollow noopener noreferrer"> IP: ' . $ip . '</a>',
            $html
        );
    }

    public static function storedTexts(): array
    {
        return [
            'a script'            => ['<script>alert(1)</script>'],
            'out of the address'  => ['1.2.3.4" onmouseover="alert(1)'],
            'out of the link'     => ['1.2.3.4"><img src=x onerror=alert(1)>'],
            'single quotes'       => ["x' onclick='alert(1)"],
            'a list of a proxy'   => ['1.2.3.4, 5.6.7.8'],
            'a name'              => ['localhost'],
        ];
    }

    #[DataProvider('storedTexts')]
    public function testTextThatIsNoAddressIsShownAsTextWithoutALink(string $stored): void
    {
        $html = KunenaIpAddress::lookupLink($stored);

        $this->assertStringNotContainsString('<', $html);
        $this->assertStringNotContainsString('"', $html);
        $this->assertStringNotContainsString("'", $html);
        $this->assertSame(' IP: ' . htmlspecialchars($stored, ENT_QUOTES, 'UTF-8'), $html);
    }

    public static function nothing(): array
    {
        return [[''], ['   '], [null], [false], [[]], [5]];
    }

    #[DataProvider('nothing')]
    public function testNoAddressGivesAnEmptyPlace(mixed $stored): void
    {
        $this->assertSame('&nbsp;', KunenaIpAddress::lookupLink($stored));
    }

    public static function controllers(): array
    {
        return [
            'actions of a message' => ['site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php'],
            'message of a topic'   => ['site/src/Controller/Topic/Item/Message/TopicItemMessageDisplay.php'],
        ];
    }

    #[DataProvider('controllers')]
    public function testControllersBuildTheLinkWithTheHelper(string $file): void
    {
        $source = file_get_contents(self::SRC . '/' . $file);

        $this->assertStringContainsString('$this->ipLink = KunenaIpAddress::lookupLink($this->message->ip);', $source);
        $this->assertStringContainsString('use Kunena\Forum\Libraries\User\KunenaIpAddress;', $source);
        $this->assertStringNotContainsString("dnslytics.com/ip/' . \$this->message->ip", $source);
    }
}
