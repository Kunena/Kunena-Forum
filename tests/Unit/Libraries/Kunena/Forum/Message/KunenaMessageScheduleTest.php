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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessageSchedule as Schedule;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A reply can be scheduled (issue #9830): the value of the datetime-local field of the reply form is read in the time zone of the
 * user, and only a date in the future schedules the reply.
 */
class KunenaMessageScheduleTest extends UnitTestCase
{
    /**
     * 2026-06-01 12:00:00 UTC.
     */
    private const NOW = 1780315200;

    public static function values(): array
    {
        return [
            'empty: no schedule'    => ['', 0],
            'only spaces'           => ['   ', 0],
            'a day later'           => ['2026-06-02T12:00', self::NOW + 86400],
            'a minute later'        => ['2026-06-01T12:01', self::NOW + 60],
            'with seconds'          => ['2026-06-01T12:00:30', self::NOW + 30],
            'equal to now'          => ['2026-06-01T12:00', Schedule::PAST],
            'a minute ago'          => ['2026-06-01T11:59', Schedule::PAST],
            'last year'             => ['2025-06-01T12:00', Schedule::PAST],
            'garbage'               => ['tomorrow at noon', Schedule::INVALID],
            'markup'                => ['<script>2026-06-02T12:00', Schedule::INVALID],
            'a date without a time' => ['2026-06-02', Schedule::INVALID],
            'the 30th of February'  => ['2026-02-30T10:00', Schedule::INVALID],
            'the hour 25'           => ['2026-06-02T25:00', Schedule::INVALID],
            'the month 13'          => ['2026-13-02T10:00', Schedule::INVALID],
        ];
    }

    #[DataProvider('values')]
    public function testValueOfTheFieldInUtc(string $value, int $expected): void
    {
        $this->assertSame($expected, Schedule::parse($value, 'UTC', self::NOW));
    }

    public function testSameWallClockInTwoTimeZonesGivesTwoTimes(): void
    {
        // Summer time: Amsterdam is UTC+2, New York UTC-4.
        $amsterdam = Schedule::parse('2026-06-02T09:00', 'Europe/Amsterdam', self::NOW);
        $newYork   = Schedule::parse('2026-06-02T09:00', 'America/New_York', self::NOW);

        $this->assertSame(gmmktime(7, 0, 0, 6, 2, 2026), $amsterdam);
        $this->assertSame(gmmktime(13, 0, 0, 6, 2, 2026), $newYork);
        $this->assertSame(6 * 3600, $newYork - $amsterdam);
    }

    public function testValueInThePastOfOneTimeZoneOnly(): void
    {
        // 13:00 in Amsterdam is 11:00 UTC: already past. In New York it is 17:00 UTC: in the future.
        $this->assertSame(Schedule::PAST, Schedule::parse('2026-06-01T13:00', 'Europe/Amsterdam', self::NOW));
        $this->assertSame(gmmktime(17, 0, 0, 6, 1, 2026), Schedule::parse('2026-06-01T13:00', 'America/New_York', self::NOW));
    }

    public function testUnknownTimeZoneIsUtc(): void
    {
        $this->assertSame(self::NOW + 3600, Schedule::parse('2026-06-01T13:00', 'Mars/Olympus_Mons', self::NOW));
        $this->assertSame(self::NOW + 3600, Schedule::parse('2026-06-01T13:00', '', self::NOW));
    }
}
