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

use Kunena\Forum\Libraries\Tools\KunenaDataRetention as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Data retention" deletes old rows, and never a read marker of a topic which can still be new.
 */
class KunenaDataRetentionTest extends UnitTestCase
{
    private const NOW = 1_800_000_000;

    public function testTheCutoff(): void
    {
        $this->assertSame(self::NOW - 365 * 86400, T::cutoff('logs', 365, self::NOW));
        $this->assertSame(self::NOW - 90 * 86400, T::cutoff('history', 90, self::NOW));
    }

    public function testAReadMarkerOfATopicWhichCanBeNewStays(): void
    {
        // 90 days is shorter than the time Kunena shows a topic as new.
        $this->assertSame(self::NOW - (T::NEW_DAYS + 1) * 86400, T::cutoff('read', 90, self::NOW));
        $this->assertSame(self::NOW - 365 * 86400, T::cutoff('read', 365, self::NOW));
    }
}
