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

use Kunena\Forum\Libraries\Tools\KunenaPollsOverview as P;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Polls overview" knows which polls are open and which ended; an empty date of the database is no end.
 */
class KunenaPollsOverviewTest extends UnitTestCase
{
    public function testTheEndOfAPoll(): void
    {
        $this->assertSame(gmmktime(12, 0, 0, 6, 1, 2026), P::endsAt('2026-06-01 12:00:00'));
        $this->assertSame(0, P::endsAt('1000-01-01 00:00:00'));
        $this->assertSame(0, P::endsAt('0000-00-00 00:00:00'));
        $this->assertSame(0, P::endsAt(''));
        $this->assertSame(0, P::endsAt(null));
    }

    public function testTheFilters(): void
    {
        $now = 1_000_000;

        $this->assertTrue(P::matches('open', 0, 3, $now));
        $this->assertTrue(P::matches('open', $now + 60, 3, $now));
        $this->assertFalse(P::matches('open', $now - 60, 3, $now));
        $this->assertTrue(P::matches('ended', $now - 60, 3, $now));
        $this->assertFalse(P::matches('ended', 0, 3, $now));
        $this->assertTrue(P::matches('novotes', 0, 0, $now));
        $this->assertFalse(P::matches('novotes', 0, 1, $now));
        $this->assertTrue(P::matches('all', $now - 60, 0, $now));
    }
}
