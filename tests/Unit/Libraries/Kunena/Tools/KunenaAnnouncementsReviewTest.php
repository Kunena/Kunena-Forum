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

use Kunena\Forum\Libraries\Tools\KunenaAnnouncementsReview as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Announcements review" finds the announcements which are past their end, old, waiting or unpublished.
 */
class KunenaAnnouncementsReviewTest extends UnitTestCase
{
    private const NOW = 1_800_000_000;

    private static function row(int $id, int $published, int $created, string $up = '1000-01-01 00:00:00', string $down = '1000-01-01 00:00:00'): array
    {
        return ['id' => $id, 'title' => 'A' . $id, 'published' => $published, 'created' => gmdate('Y-m-d H:i:s', $created), 'publish_up' => $up, 'publish_down' => $down];
    }

    public function testADateOfTheDatabase(): void
    {
        $this->assertSame(0, T::time('1000-01-01 00:00:00'));
        $this->assertSame(0, T::time('0000-00-00 00:00:00'));
        $this->assertSame(0, T::time(null));
        $this->assertSame(self::NOW, T::time(gmdate('Y-m-d H:i:s', self::NOW)));
    }

    public function testTheStateOfTheAnnouncements(): void
    {
        $day   = 86400;
        $items = T::review([
            self::row(1, 1, self::NOW - 10 * $day),
            self::row(2, 1, self::NOW - 10 * $day, '1000-01-01 00:00:00', gmdate('Y-m-d H:i:s', self::NOW - $day)),
            self::row(3, 1, self::NOW - 400 * $day),
            self::row(4, 1, self::NOW - $day, gmdate('Y-m-d H:i:s', self::NOW + $day)),
            self::row(5, 0, self::NOW - $day),
        ], self::NOW);

        // Announcement 1 is shown and recent.
        $this->assertSame([2 => 'expired', 3 => 'old', 4 => 'waiting', 5 => 'unpublished'], array_column($items, 'state', 'id'));
    }
}
