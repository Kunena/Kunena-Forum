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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * A page which loads messages (and with them their topics) and other topics too (the tabs of a profile) loads all the topics
 * with one query: the other topics are loaded with the next topics which are loaded.
 */
class KunenaTopicHelperLoadAlsoTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The helper reads the database of the container of Joomla, and the configuration (no ratings here).
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['ratingEnabled' => 0, 'readOnly' => 0]);

        $this->saved = [
            'container' => Factory::$container,
            'config'    => $this->replace(KunenaConfig::class, 'instance', $config),
            'instances' => $this->replace(KunenaTopicHelper::class, '_instances', []),
            'also'      => $this->replace(KunenaTopicHelper::class, '_also', []),
        ];

        Factory::$container = $container;

        $this->insertRows('#__kunena_topics', [
            ['id' => 1, 'category_id' => 2, 'subject' => 'Topic of a message'],
            ['id' => 2, 'category_id' => 2, 'subject' => 'Topic of a list'],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaTopicHelper::class, '_instances', $this->saved['instances']);
            $this->replace(KunenaTopicHelper::class, '_also', $this->saved['also']);
        }

        parent::tearDown();
    }

    public function testTheOtherTopicsComeWithTheNextTopicsWhichAreLoaded(): void
    {
        KunenaTopicHelper::loadAlso([2, 0]);

        $this->assertSame([1], array_keys(KunenaTopicHelper::getTopics([1], 'none')));

        // Topic 2 came with topic 1: it is known without the database.
        $this->getDBDriver()->setQuery('DELETE FROM ' . $this->getDBDriver()->quoteName('#__kunena_topics'))->execute();

        $topics = KunenaTopicHelper::getTopics([2], 'none');
        $this->assertSame([2], array_keys($topics));
        $this->assertSame('Topic of a list', $topics[2]->subject);
    }

    public function testATopicWhichIsLoadedAlreadyIsNotLoadedAgain(): void
    {
        KunenaTopicHelper::getTopics([2], 'none');

        // The topic is known: it is no topic to load with the next ones.
        KunenaTopicHelper::loadAlso([2]);

        $this->assertSame([], (new ReflectionProperty(KunenaTopicHelper::class, '_also'))->getValue());
    }

    /**
     * Put a value in a static property of a class, and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
