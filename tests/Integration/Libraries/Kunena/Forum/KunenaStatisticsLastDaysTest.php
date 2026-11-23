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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum;

use DateTimeImmutable;
use DateTimeZone;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The topics and replies of today and of yesterday in the statistics are counted by the days of the site (its time zone). The days
 * started at midnight of the server (UTC): east of UTC a topic of yesterday evening was counted as one of today.
 */
class KunenaStatisticsLastDaysTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['showStats' => 1, 'showGenStats' => 1, 'showPopUserStats' => 0, 'showPopSubjectStats' => 0, 'showPopPollStats' => 0, 'showPopThankYouStats' => 0]);

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'zone'        => date_default_timezone_get(),
        ];

        // The server runs in UTC, as Joomla sets it.
        date_default_timezone_set('UTC');
        Factory::$container = $container;
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            date_default_timezone_set($this->saved['zone']);
        }

        parent::tearDown();
    }

    /**
     * A topic of the last two days whose day in the time zone of the site is not its day in UTC: today for the site and yesterday
     * for the server, or the other way. At any hour of the day one of the zones from UTC-12 to UTC+14 has one.
     */
    public function testATopicCountsForItsDayInTheTimeZoneOfTheSite(): void
    {
        [$zone, $time, $day] = $this->topicOfAnotherDayInUtc();

        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('get')->willReturnCallback(static fn ($key, $default = null) => $key === 'offset' ? $zone : $default);
        Factory::$application = $application;

        $this->insertRows('#__kunena_messages', [
            ['id' => 1, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'name' => 'U', 'userid' => 42, 'email' => '', 'subject' => 'S',
                'time' => $time, 'ip' => '', 'hold' => 0],
        ]);

        $statistics = new KunenaStatistics();
        $statistics->loadLastDays();

        $expected = $day === 'today' ? [1, 0] : [0, 1];
        $this->assertSame($expected, [$statistics->todayTopicCount, $statistics->yesterdayTopicCount], "$zone, $day");
    }

    /**
     * A time zone, a time of the last two days and its day there ('today' or 'yesterday'), where the day in UTC is the other one.
     *
     * @return  array{string, int, string}
     */
    private function topicOfAnotherDayInUtc(): array
    {
        $now = time();

        foreach (range(-12, 14) as $hours) {
            $zone = new DateTimeZone(\sprintf('%+03d:00', $hours));

            for ($step = 1; $step <= 96; $step++) {
                $time  = $now - $step * 900;
                $local = $this->day($time, $now, $zone);
                $utc   = $this->day($time, $now, new DateTimeZone('UTC'));

                if ($local && $utc && $local !== $utc) {
                    return [$zone->getName(), $time, $local];
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
            $today->format('Y-m-d')                     => 'today',
            $today->modify('-1 day')->format('Y-m-d') => 'yesterday',
            default                                     => null,
        };
    }

    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
