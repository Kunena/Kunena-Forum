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

use Kunena\Forum\Libraries\Tools\KunenaBanReview as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Ban review" lists the active bans which need a look, with what to check.
 */
class KunenaBanReviewTest extends UnitTestCase
{
    private const NOW = 1_800_000_000;

    private static function ban(int $id, int $user, string $expiration, string $reason = 'spam'): array
    {
        return ['id' => $id, 'userid' => $user, 'ip' => '', 'expiration' => $expiration, 'reason_private' => $reason, 'reason_public' => ''];
    }

    private static function at(int $time): string
    {
        return gmdate('Y-m-d H:i:s', $time);
    }

    public function testTheProblemsOfTheActiveBans(): void
    {
        $review = T::review([
            self::ban(1, 10, self::at(self::NOW + 2 * 86400)),
            self::ban(2, 11, T::LIFETIME, ''),
            self::ban(3, 12, self::at(self::NOW + 90 * 86400)),
            self::ban(4, 13, self::at(self::NOW + 90 * 86400)),
            self::ban(5, 14, self::at(self::NOW - 86400)),
            self::ban(6, 15, self::at(self::NOW + 90 * 86400)),
        ], [12, 14], [13, 12], self::NOW);

        // Ban 5 ended, ban 6 has nothing to check.
        $this->assertSame(5, $review['active']);
        $this->assertSame([1, 3, 4, 2], array_map(static fn ($item) => (int) $item['ban']['id'], $review['items']));
        $this->assertSame(['soon'], $review['items'][0]['problems']);
        $this->assertSame(['moderator', 'subscribed'], $review['items'][1]['problems']);
        $this->assertSame(['subscribed'], $review['items'][2]['problems']);
        $this->assertSame(['noreason'], $review['items'][3]['problems']);
        $this->assertSame(0, $review['items'][3]['end']);
    }

    public function testABanOfAnIpAddressIsNoModerator(): void
    {
        $ban    = ['id' => 7, 'userid' => 0, 'ip' => '192.0.2.1', 'expiration' => T::LIFETIME, 'reason_private' => 'bot', 'reason_public' => ''];
        $review = T::review([$ban], [0], [0], self::NOW);

        $this->assertSame(1, $review['active']);
        $this->assertSame([], $review['items']);
    }
}
