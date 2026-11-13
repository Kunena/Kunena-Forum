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

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The shadow topics in the frontend list of the deleted topics (issue #6258): the list finds them, and restoring or deleting one
 * for good changes the shadow only, never the topic it points to.
 */
class KunenaTopicDeletedShadowTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    private const OTHER_CATEGORY = 3;

    /**
     * The topic which the shadows point to, with its messages.
     */
    private const TARGET = 1;

    /**
     * The objects of Joomla and of Kunena which the test replaces, put back after the test.
     */
    private array $saved = [];

    /**
     * The column deleted_time was added to the table of the messages for the test.
     */
    private bool $deletedTime = false;

    protected function setUp(): void
    {
        parent::setUp();

        // The finder and the topics need the framework of Joomla.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The library reads the database of the container of Joomla: that is the database of the tests here. The application
        // gives the dispatcher of the events and is a command line one (the router of Kunena needs no menu then), no plugin is
        // loaded, the configuration has the settings which the code reads, and the caches and the access which a saved category
        // clears do nothing.
        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');
        $language = $this->createStub(Language::class);
        $language->method('transliterate')->willReturnArgument(0);
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['threadsPerPage' => 20, 'ratingEnabled' => 0, 'readOnly' => 0]);

        // No category is cached: the topics load their category from the database. (Done first: the helper loads all the
        // categories when it is loaded, which the replaced application could not do.)
        $this->saved = [
            'categories'  => $this->replace(KunenaCategoryHelper::class, '_instances', []),
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'access'      => $this->replace(KunenaAccess::class, 'instance', $this->createStub(KunenaAccess::class)),
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        // KunenaTopic::publish() sets the deleted time of the messages: the column is in the tables of an installation
        // (create_tables.utf8.sql and the upgrade), not in the schema which makes the tables of the tests. It is there for the
        // test only: the other tests compare the tables with that schema.
        $driver->setQuery("ALTER TABLE #__kunena_messages ADD deleted_time int(11) NOT NULL default '0' AFTER moved")->execute();
        $this->deletedTime = true;

        $this->insertRows('#__kunena_categories', [
            ['id' => self::CATEGORY, 'parentid' => 0, 'name' => 'Category', 'alias' => '2-category', 'published' => 1, 'numTopics' => 1, 'numPosts' => 2],
            ['id' => self::OTHER_CATEGORY, 'parentid' => 0, 'name' => 'Other', 'alias' => '3-other', 'published' => 1],
        ]);
        $this->insertRow('#__kunena_aliases', ['alias' => '2-category', 'type' => 'catid', 'item' => self::CATEGORY]);

        $this->topic(self::TARGET, 0, 0, 2);
        $this->insertRows('#__kunena_messages', [
            ['id' => 1, 'parent' => 0, 'thread' => self::TARGET, 'catid' => self::CATEGORY, 'subject' => 'Target', 'time' => 1000, 'hold' => 0],
            ['id' => 2, 'parent' => 1, 'thread' => self::TARGET, 'catid' => self::CATEGORY, 'subject' => 'Target', 'time' => 1100, 'hold' => 0],
        ]);
        $this->insertRows('#__kunena_messages_text', [
            ['mesid' => 1, 'message' => 'First'],
            ['mesid' => 2, 'message' => 'Reply'],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAccess::class, 'instance', $this->saved['access']);
            $this->replace(PluginHelper::class, 'plugins', $this->saved['plugins']);
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);

            // The helpers keep the topics and the categories for the request: the next test has rows of its own.
            $this->replace(KunenaTopicHelper::class, '_instances', []);
        }

        if ($this->deletedTime) {
            $this->getDBDriver()->setQuery('ALTER TABLE #__kunena_messages DROP deleted_time')->execute();
        }

        parent::tearDown();
    }

    /**
     * Set a static property of a class and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }

    /**
     * A topic. A shadow (moved_id > 0) has no messages: they were moved to the topic it points to.
     */
    private function topic(int $id, int $hold, int $movedId = 0, int $posts = 0, int $category = self::CATEGORY): void
    {
        $this->insertRow('#__kunena_topics', [
            'id'             => $id,
            'category_id'    => $category,
            'subject'        => "Topic $id",
            'hold'           => $hold,
            'moved_id'       => $movedId,
            'posts'          => $posts,
            'first_post_id'  => $movedId ? 0 : 1,
            'last_post_id'   => $movedId ? 0 : 2,
            'last_post_time' => 1000 + $id,
        ]);
    }

    /**
     * The finder of the deleted list (TopicListingRecentDisplay, mode "deleted") on the category.
     */
    private function deletedList(): KunenaTopicFinder
    {
        return (new KunenaTopicFinder())
            ->filterByHold([2, 3])
            ->filterByCategories([self::CATEGORY])
            ->order('id', 1);
    }

    /**
     * The target topic and its messages, as they are in the database.
     */
    private function target(): array
    {
        return [
            'topic'    => $this->fetchAll('SELECT * FROM #__kunena_topics WHERE id = ' . self::TARGET),
            'messages' => $this->fetchAll('SELECT * FROM #__kunena_messages WHERE thread = ' . self::TARGET . ' ORDER BY id'),
            'texts'    => $this->fetchAll('SELECT * FROM #__kunena_messages_text ORDER BY mesid'),
        ];
    }

    public function testTheDeletedListHasTheDeletedShadowsOfTheCategory(): void
    {
        // Shadows deleted with the message state 2 and with the topic state 3, a deleted topic, a published shadow and a deleted
        // shadow of another category.
        $this->topic(10, 2, self::TARGET);
        $this->topic(11, 3, self::TARGET);
        $this->topic(12, 2);
        $this->topic(13, 0, self::TARGET);
        $this->topic(14, 2, self::TARGET, 0, self::OTHER_CATEGORY);

        $this->assertSame([10, 11, 12], array_keys($this->deletedList()->find('none')));
        $this->assertSame(3, $this->deletedList()->count());
    }

    public function testDeletingADeletedShadowForGoodRemovesTheShadowOnly(): void
    {
        $this->topic(10, 2, self::TARGET);
        $target = $this->target();

        $this->assertTrue(KunenaTopicHelper::get(10)->delete());

        $this->assertSame([], $this->fetchAll('SELECT id FROM #__kunena_topics WHERE id = 10'));
        $this->assertSame($target, $this->target());
    }

    public function testRestoringADeletedShadowPublishesTheShadowOnly(): void
    {
        $this->topic(10, 3, self::TARGET);
        $target = $this->target();

        $this->assertTrue(KunenaTopicHelper::get(10)->publish(KunenaForum::PUBLISHED));

        $this->assertSame([['hold' => 0, 'moved_id' => self::TARGET]], $this->fetchAll('SELECT hold, moved_id FROM #__kunena_topics WHERE id = 10'));
        $this->assertSame($target, $this->target());
    }
}
