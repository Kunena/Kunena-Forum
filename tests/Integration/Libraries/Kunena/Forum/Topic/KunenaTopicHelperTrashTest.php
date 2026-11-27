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
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The prune of a category with "trash" (KunenaCategory::trash() -> KunenaTopicHelper::trash()) deletes the old topics as the delete of
 * a topic does: their published messages are deleted with the topic, the others keep their state. Every message was set to deleted,
 * so the restore of the topic (which brings back the messages deleted with it) published the replies which waited for approval and
 * brought back the replies which a moderator had deleted.
 */
class KunenaTopicHelperTrashTest extends IntegrationTestCase implements DBTestInterface
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

        $this->insertRows('#__kunena_categories', [
            ['id' => self::CATEGORY, 'parentid' => 0, 'name' => 'Category', 'alias' => '2-category', 'published' => 1, 'numTopics' => 1, 'numPosts' => 1],
            ['id' => self::OTHER_CATEGORY, 'parentid' => 0, 'name' => 'Other', 'alias' => '3-other', 'published' => 1],
        ]);
        $this->insertRow('#__kunena_aliases', ['alias' => '2-category', 'type' => 'catid', 'item' => self::CATEGORY]);

        $this->topic(self::TARGET, 0, 0, 1);
        $this->insertRows('#__kunena_messages', [
            ['id' => 1, 'parent' => 0, 'thread' => self::TARGET, 'catid' => self::CATEGORY, 'subject' => 'Target', 'time' => 1000, 'hold' => 0],
            ['id' => 2, 'parent' => 1, 'thread' => self::TARGET, 'catid' => self::CATEGORY, 'subject' => 'Target', 'time' => 1100, 'hold' => 1],
            ['id' => 3, 'parent' => 1, 'thread' => self::TARGET, 'catid' => self::CATEGORY, 'subject' => 'Target', 'time' => 1200, 'hold' => 2],
        ]);
        $this->insertRows('#__kunena_messages_text', [
            ['mesid' => 1, 'message' => 'First'],
            ['mesid' => 2, 'message' => 'Waiting'],
            ['mesid' => 3, 'message' => 'Deleted'],
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

    public function testRestoringATrashedTopicBringsBackItsPublishedMessagesOnly(): void
    {
        KunenaTopicHelper::trash([self::TARGET]);
        $this->replace(KunenaTopicHelper::class, '_instances', []);

        $this->assertTrue(KunenaTopicHelper::get(self::TARGET)->publish(KunenaForum::PUBLISHED));

        $this->assertSame(
            [['id' => 1, 'hold' => 0], ['id' => 2, 'hold' => 1], ['id' => 3, 'hold' => 2]],
            array_map(static fn ($row) => ['id' => (int) $row['id'], 'hold' => (int) $row['hold']], $this->fetchAll('SELECT id, hold FROM #__kunena_messages WHERE thread = ' . self::TARGET . ' ORDER BY id'))
        );
    }
}
