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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic\Rate;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionProperty;

/**
 * The numbers of ratings of the topics of several lists of a page (the tabs of a profile) are loaded with one query, when the
 * first number is shown: each list of topics only notes its topics.
 */
class KunenaRateHelperDeferCountsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The helper reads the database of the container of Joomla.
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $this->saved = [
            'container' => Factory::$container,
            'counts'    => $this->replace('_counts', []),
            'pending'   => $this->replace('_pending', []),
        ];

        Factory::$container = $container;

        // Topic 1 has two ratings, topic 2 one, topic 3 none.
        $this->insertRows('#__kunena_rate', [
            ['topic_id' => 1, 'userid' => 42, 'rate' => 4, 'time' => '2026-01-01 00:00:00'],
            ['topic_id' => 1, 'userid' => 43, 'rate' => 2, 'time' => '2026-01-01 00:00:00'],
            ['topic_id' => 2, 'userid' => 42, 'rate' => 5, 'time' => '2026-01-01 00:00:00'],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            $this->replace('_counts', $this->saved['counts']);
            $this->replace('_pending', $this->saved['pending']);
        }

        parent::tearDown();
    }

    public function testTheTopicsOfAllTheListsAreCountedWithOneQuery(): void
    {
        // Two lists of topics: each one notes its topics, nothing is read yet.
        KunenaRateHelper::deferCounts([1, 3]);
        KunenaRateHelper::deferCounts([2, 0, -4]);

        // The first number which is shown reads the numbers of all the noted topics.
        $this->assertSame(2.0, KunenaRateHelper::getCount(1));

        $this->getDBDriver()->setQuery('DELETE FROM ' . $this->getDBDriver()->quoteName('#__kunena_rate'))->execute();

        $this->assertSame(1.0, KunenaRateHelper::getCount(2));
        $this->assertSame(0.0, KunenaRateHelper::getCount(3));
    }

    public function testATopicWhichWasNotNotedIsCountedToo(): void
    {
        KunenaRateHelper::deferCounts([2]);

        // A topic which no list noted is read with the noted ones.
        $this->assertSame(2.0, KunenaRateHelper::getCount(1));

        $this->getDBDriver()->setQuery('DELETE FROM ' . $this->getDBDriver()->quoteName('#__kunena_rate'))->execute();

        $this->assertSame(1.0, KunenaRateHelper::getCount(2));
    }

    private function replace(string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty(KunenaRateHelper::class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
