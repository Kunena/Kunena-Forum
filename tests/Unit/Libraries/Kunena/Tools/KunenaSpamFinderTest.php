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

use Kunena\Forum\Libraries\Tools\KunenaSpamFinder as S;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Find spam" gives points for the signs of spam and lists the messages with enough of them, the worst first.
 */
class KunenaSpamFinderTest extends UnitTestCase
{
    private const TIME = 1_800_000_000;

    private static function row(array $values): array
    {
        return $values + ['id' => 1, 'subject' => 'Hello', 'message' => 'Hi', 'time' => self::TIME, 'hold' => 0, 'userid' => 5, 'posts' => 40, 'registered' => self::TIME - 999999, 'ip' => '1.2.3.4'];
    }

    public function testCountLinksCountsTheBbcodeAndTheAddressesOnce(): void
    {
        $this->assertSame(0, S::countLinks('no link here'));
        $this->assertSame(1, S::countLinks('[url=https://a.example]a[/url]'));
        $this->assertSame(1, S::countLinks('[url]https://a.example[/url]'));
        $this->assertSame(3, S::countLinks('https://a.example www.b.example [URL=http://c.example]c[/URL]'));
    }

    public function testAnOrdinaryMessageHasNoPoint(): void
    {
        $this->assertSame(['score' => 0, 'signs' => []], S::score(self::row([])));
    }

    public function testAFreshAccountPostingLinksIsSuspect(): void
    {
        $result = S::score(self::row(['message' => 'http://a.example http://b.example http://c.example', 'registered' => self::TIME - 120, 'posts' => 1]));

        $this->assertSame(['links_many', 'new_minutes', 'first_post'], $result['signs']);
        $this->assertSame(7, $result['score']);
    }

    public function testAnAccountOfTheDayGetsLessThanOneOfMinutes(): void
    {
        $this->assertSame(['new_day'], S::score(self::row(['registered' => self::TIME - 3600]))['signs']);
    }

    public function testAGuestCountsOnlyWithALink(): void
    {
        $this->assertSame([], S::score(self::row(['userid' => 0, 'registered' => 0]))['signs']);
        $this->assertSame(['links', 'guest_link'], S::score(self::row(['userid' => 0, 'registered' => 0, 'message' => 'www.a.example']))['signs']);
    }

    public function testASharedAddressAndAnUnapprovedMessageCount(): void
    {
        $this->assertSame(['shared_ip', 'unapproved'], S::score(self::row(['hold' => 1, 'ip' => '8.8.4.4']), 3)['signs']);
    }

    public function testAPrivateOrLocalAddressSharedTellsNothing(): void
    {
        foreach (['::1', '127.0.0.1', '192.168.1.5', '10.0.0.2', 'fd00::1', ''] as $ip) {
            $this->assertSame([], S::score(self::row(['ip' => $ip]), 5)['signs'], $ip);
        }

        $this->assertTrue(S::isPublicIp('2001:4860:4860::8888'));
    }

    public function testSuspectsAreTheMessagesAtTheThresholdTheWorstFirst(): void
    {
        $rows = [
            self::row(['id' => 1]),
            self::row(['id' => 2, 'hold' => 1, 'ip' => '8.8.8.8']),
            self::row(['id' => 3, 'message' => 'http://a http://b http://c', 'posts' => 0, 'registered' => self::TIME - 60]),
            self::row(['id' => 4, 'hold' => 1, 'ip' => '8.8.8.8', 'time' => self::TIME + 10]),
        ];

        $suspects = S::suspects($rows, ['8.8.8.8' => 2]);

        $this->assertSame([3, 4, 2], array_column($suspects, 'id'));
        $this->assertSame(3, $suspects[2]['score']);
        $this->assertCount(1, S::suspects($rows, ['8.8.8.8' => 2], 1));
    }
}
