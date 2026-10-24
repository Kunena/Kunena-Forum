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

namespace Kunena\Tests\Unit\Libraries\Kunena\Date;

use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The dates of the date fields of the forum (search at a date, birthdate): written in the format of the date picker and read
 * back from it. With the format given, no option of the forum is read.
 */
class KunenaDatePickerTest extends UnitTestCase
{
    public static function written(): array
    {
        return [
            'day first'                  => ['2026-10-23', 'dd/mm/yyyy', '23/10/2026'],
            'month first'                => ['2026-10-23', 'mm/dd/yyyy', '10/23/2026'],
            'year first with dashes'     => ['2026-10-23', 'yyyy-mm-dd', '2026-10-23'],
            'no leading zeros'           => ['2026-03-05', 'd.m.yyyy', '5.3.2026'],
            'two digit year'             => ['2026-03-05', 'dd/mm/yy', '05/03/26'],
            'a date with a time'         => ['2026-03-05 12:30:00', 'dd/mm/yyyy', '05/03/2026'],
            'the empty date of the base' => ['1000-01-01', 'dd/mm/yyyy', ''],
            'the zero date'              => ['0000-00-00 00:00:00', 'dd/mm/yyyy', ''],
            'no date'                    => ['', 'dd/mm/yyyy', ''],
            'not a date'                 => ['someday', 'dd/mm/yyyy', ''],
        ];
    }

    #[DataProvider('written')]
    public function testToPicker(string $date, string $format, string $expected): void
    {
        $this->assertSame($expected, KunenaDate::toPicker($date, $format));
    }

    public static function read(): array
    {
        return [
            'day first'                           => ['23/10/2026', 'dd/mm/yyyy', '2026-10-23'],
            'month first'                         => ['10/23/2026', 'mm/dd/yyyy', '2026-10-23'],
            'no leading zeros'                    => ['5.3.2026', 'd.m.yyyy', '2026-03-05'],
            'spaces around'                       => ['  23/10/2026 ', 'dd/mm/yyyy', '2026-10-23'],
            'a date PHP knows in another format'  => ['2026-10-23', 'dd/mm/yyyy', '2026-10-23'],
            'day and month swapped: read by PHP'  => ['10/23/2026', 'dd/mm/yyyy', '2026-10-23'],
            'the 31st of February'                => ['31/02/2026', 'dd/mm/yyyy', null],
            'not a date'                          => ['someday', 'dd/mm/yyyy', null],
            'empty'                               => ['', 'dd/mm/yyyy', null],
        ];
    }

    #[DataProvider('read')]
    public function testFromPicker(string $value, string $format, ?string $expected): void
    {
        $this->assertSame($expected, KunenaDate::fromPicker($value, $format));
    }

    public static function formats(): array
    {
        return [['dd/mm/yyyy'], ['mm/dd/yyyy'], ['yyyy-mm-dd'], ['d.m.yyyy'], ['dd M yyyy'], ['D, dd MM yyyy']];
    }

    /**
     * A date written for the field is read back the same: a form saved without a change keeps its date.
     */
    #[DataProvider('formats')]
    public function testRoundTrip(string $format): void
    {
        foreach (['2026-01-01', '2026-02-28', '2024-02-29', '2026-12-31', '1999-07-04'] as $date) {
            $this->assertSame($date, KunenaDate::fromPicker(KunenaDate::toPicker($date, $format), $format), "{$date} in {$format}");
        }
    }
}
