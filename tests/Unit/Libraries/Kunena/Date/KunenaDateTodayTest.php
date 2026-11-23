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

use DateTimeImmutable;
use DateTimeZone;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A date of the lists is "Today 10:15" or "Yesterday 23:30" in the time zone of the user. The days were the days of the server (UTC):
 * around midnight a post of yesterday evening was "Today 23:30" for a user east of UTC (and a post of today "Yesterday" west of it).
 */
class KunenaDateTodayTest extends UnitTestCase
{
    private mixed $language = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        // The texts of the dates: the formats, and Today / Yesterday with the time.
        $texts    = ['COM_KUNENA_LIB_TIME_FMT' => 'H:i', 'COM_KUNENA_LIB_DATE_TODAY' => 'Today %s', 'COM_KUNENA_LIB_DATE_YESTERDAY' => 'Yesterday %s'];
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnCallback(static fn ($key) => $texts[$key] ?? $key);
        $language->method('getTag')->willReturn('en-GB');

        $property       = new ReflectionProperty(Factory::class, 'language');
        $this->language = $property->getValue();
        $property->setValue(null, $language);
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(Factory::class, 'language'))->setValue(null, $this->language);
    }

    /**
     * A post of the last two days whose day in the time zone of the user is not its day in UTC: today for the user and yesterday
     * for the server, or the other way. At any hour of the day one of the zones from UTC-12 to UTC+14 has one (a list of a few named
     * zones had none at some hours, so the test depended on the time it ran).
     */
    public function testAPostIsTodayOrYesterdayInTheTimeZoneOfTheUser(): void
    {
        $now = time();
        $utc = new DateTimeZone('UTC');

        foreach (range(-12, 14) as $hours) {
            $zone = new DateTimeZone(\sprintf('%+03d:00', $hours));

            for ($step = 1; $step <= 96; $step++) {
                $time  = $now - $step * 900;
                $local = $this->day($time, $now, $zone);
                $day   = $this->day($time, $now, $utc);

                if ($local && $day && $local !== $day) {
                    $post = new DateTimeImmutable('@' . $time);
                    $date = KunenaDate::getInstance($post->format('Y-m-d H:i:s'));

                    $expected = ($local === 'today' ? 'Today ' : 'Yesterday ') . $post->setTimezone($zone)->format('H:i');
                    $this->assertSame($expected, $date->toKunena('datetime_today', $zone->getName()), $zone->getName());

                    return;
                }
            }
        }

        $this->fail('No time of the last two days with another day in UTC.');
    }

    private function day(int $time, int $now, DateTimeZone $zone): ?string
    {
        $then  = (new DateTimeImmutable('@' . $time))->setTimezone($zone)->format('Y-m-d');
        $today = (new DateTimeImmutable('@' . $now))->setTimezone($zone);

        return match ($then) {
            $today->format('Y-m-d')                   => 'today',
            $today->modify('-1 day')->format('Y-m-d') => 'yesterday',
            default                                   => null,
        };
    }
}
