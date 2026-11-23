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
     * Yesterday 23:30 in the time zone of the user, in a zone where that moment and now are the same day in UTC (so that the days of
     * the server called it today). There is always one among the zones from UTC-11 to UTC+14.
     */
    public function testAPostOfLateYesterdayIsYesterdayInTheTimeZoneOfTheUser(): void
    {
        $utc = new DateTimeZone('UTC');

        foreach (['Pacific/Kiritimati', 'Pacific/Auckland', 'Asia/Tokyo', 'Asia/Kolkata', 'Europe/Amsterdam', 'America/New_York', 'Pacific/Honolulu', 'Pacific/Pago_Pago'] as $name) {
            $zone = new DateTimeZone($name);
            $now  = new DateTimeImmutable('now', $zone);
            $post = $now->setTime(0, 0)->modify('-30 minutes');

            if ($post->setTimezone($utc)->format('Y-m-d') === $now->setTimezone($utc)->format('Y-m-d')) {
                $date = KunenaDate::getInstance($post->setTimezone($utc)->format('Y-m-d H:i:s'));

                $this->assertSame('Yesterday 23:30', $date->toKunena('datetime_today', $name), $name);

                return;
            }
        }

        $this->fail('No time zone where yesterday 23:30 is today in UTC.');
    }
}
