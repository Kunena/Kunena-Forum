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

use Kunena\Forum\Libraries\Tools\KunenaRankLadder as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Rank ladder check" finds the problems of the ranks.
 */
class KunenaRankLadderTest extends UnitTestCase
{
    private static function rank(int $id, int $min, int $special = 0, string $image = 'rank.png'): array
    {
        return ['rankId' => $id, 'rankTitle' => 'Rank ' . $id, 'rankMin' => $min, 'rankSpecial' => $special, 'rankImage' => $image];
    }

    public function testTheProblemsOfTheRanks(): void
    {
        $report = T::check([
            self::rank(1, 10),
            self::rank(2, 50),
            self::rank(3, 50, 0, 'gone.png'),
            self::rank(4, 0, 1),
            self::rank(5, 0, 1),
            self::rank(6, 0, 1, 'rankadmin.png'),
        ], [5 => 2, 9 => 3, 0 => 7], static fn (string $image) => $image !== 'gone.png');

        $this->assertSame([1, 2, 3], array_column($report['ladder'], 'rankId'));
        $this->assertSame([50 => [2, 3]], $report['same']);
        $this->assertTrue($report['noStart']);
        // Kunena gives the rank of the administrators by itself.
        $this->assertSame([4], $report['unused']);
        $this->assertSame([3], $report['images']);
        $this->assertSame([9 => 3], $report['unknown']);
    }

    public function testTheRanksKunenaGivesByItself(): void
    {
        $this->assertTrue(T::isAutomatic('rankadmin.png'));
        $this->assertTrue(T::isAutomatic('rankmod.svg'));
        $this->assertTrue(T::isAutomatic('rank_banned.gif'));
        $this->assertFalse(T::isAutomatic('rankspammer.png'));
    }

    public function testALadderFromZero(): void
    {
        $report = T::check([self::rank(1, 0), self::rank(2, 10, 0, '')], [], static fn () => true);

        $this->assertFalse($report['noStart']);
        $this->assertSame([], $report['same']);
        $this->assertSame([], $report['images']);
    }
}
