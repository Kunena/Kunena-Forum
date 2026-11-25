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

use Kunena\Forum\Libraries\Tools\KunenaThankyouRings as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Thank-you rings" finds two users who thank each other often and mostly each other.
 */
class KunenaThankyouRingsTest extends UnitTestCase
{
    public function testAPairWhichMostlyThanksEachOther(): void
    {
        $pairs = T::pairs([
            ['userid' => 2, 'targetuserid' => 3, 'count' => 5],
            ['userid' => 3, 'targetuserid' => 2, 'count' => 4],
            ['userid' => 2, 'targetuserid' => 9, 'count' => 1],
            // Each thanks the other often, but most of their thanks go to others.
            ['userid' => 4, 'targetuserid' => 5, 'count' => 3],
            ['userid' => 5, 'targetuserid' => 4, 'count' => 3],
            ['userid' => 4, 'targetuserid' => 9, 'count' => 20],
            // Only one way.
            ['userid' => 6, 'targetuserid' => 7, 'count' => 10],
            ['userid' => 7, 'targetuserid' => 6, 'count' => 1],
        ]);

        $this->assertCount(1, $pairs);
        $this->assertSame(['a' => 2, 'b' => 3, 'ab' => 5, 'ba' => 4, 'givenA' => 6, 'givenB' => 4], array_diff_key($pairs[0], ['share' => 0]));
        $this->assertEqualsWithDelta(0.9, $pairs[0]['share'], 0.001);
    }

    public function testAGuestIsNoPair(): void
    {
        $this->assertSame([], T::pairs([['userid' => 0, 'targetuserid' => 3, 'count' => 9], ['userid' => 3, 'targetuserid' => 0, 'count' => 9]]));
    }

    public function testThePairsOfAForm(): void
    {
        $this->assertSame([[2, 3], [4, 5]], T::parse(['2-3', '3-2', '5-4', '7-7', '0-3', 'x', '1-2-3']));
    }
}
