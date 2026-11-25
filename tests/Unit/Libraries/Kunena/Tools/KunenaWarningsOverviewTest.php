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

use Kunena\Forum\Libraries\Tools\KunenaWarningsOverview as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Warnings overview" sums the active points of each user and finds who reached a ban.
 */
class KunenaWarningsOverviewTest extends UnitTestCase
{
    private const NOW = 1_800_000_000;

    public function testTheActivePointsPerUser(): void
    {
        $users = T::users([
            ['user_id' => 3, 'points' => 2, 'expires_time' => 0, 'created_time' => 100],
            ['user_id' => 3, 'points' => 3, 'expires_time' => self::NOW + 10, 'created_time' => 200],
            ['user_id' => 4, 'points' => 4, 'expires_time' => self::NOW - 10, 'created_time' => 300],
            ['user_id' => 5, 'points' => 5, 'expires_time' => 0, 'created_time' => 400],
            ['user_id' => 6, 'points' => 1, 'expires_time' => 0, 'created_time' => 500],
        ], [5], 5, self::NOW);

        // User 4: his warning expired. User 5 is banned already; the same points: the newest warning first.
        $this->assertSame([5, 3, 6], array_keys($users));
        $this->assertSame(['points' => 5, 'warnings' => 2, 'last' => 200, 'reaches' => true], $users[3]);
        $this->assertFalse($users[5]['reaches']);
        $this->assertFalse($users[6]['reaches']);
    }

    public function testNoBanForPoints(): void
    {
        $users = T::users([['user_id' => 3, 'points' => 9, 'expires_time' => 0, 'created_time' => 1]], [], 0, self::NOW);

        $this->assertFalse($users[3]['reaches']);
    }
}
