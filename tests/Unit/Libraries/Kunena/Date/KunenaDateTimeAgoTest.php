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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Forum\Announcement\KunenaAnnouncement;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The relative time of a date: "3 days ago" for the past, "in 3 days" for a time which is still to come (a scheduled reply, the
 * start of an announcement). The future was "3 days ago" too. And the start and the end of an announcement are shown as
 * themselves: displayField() gave the date of the creation for both.
 */
class KunenaDateTimeAgoTest extends UnitTestCase
{
    private mixed $language = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $texts = [
            'COM_KUNENA_LIB_TIME_AGO' => '%s ago', 'COM_KUNENA_LIB_TIME_IN' => 'in %s', 'COM_KUNENA_DATE_DAYS' => 'days',
            'COM_KUNENA_DATE_DAY'     => 'day', 'COM_KUNENA_DATE_WEEK' => 'week', 'COM_KUNENA_DATE_WEEKS' => 'weeks',
            'COM_KUNENA_DATE_HOURS'   => 'hours', 'COM_KUNENA_DATE_HOUR' => 'hour', 'COM_KUNENA_DATE_YEARS' => 'years',
        ];
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

    private function date(string $modify): string
    {
        return gmdate('Y-m-d H:i:s', strtotime($modify));
    }

    public function testATimeToComeIsInAndAPastTimeIsAgo(): void
    {
        $this->assertSame('3 days 2 hours ago', KunenaDate::getInstance($this->date('-3 days -2 hours -5 minutes'))->toKunena('ago'));
        $this->assertSame('in 3 days 2 hours', KunenaDate::getInstance($this->date('+3 days +2 hours +5 minutes'))->toKunena('ago'));
    }
}
