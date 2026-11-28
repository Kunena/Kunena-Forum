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
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * A split which moves the first message of a topic with a poll into a new topic: the poll goes with it, and the poll names the new
 * topic. It still named the old topic, so the diagnostics found a topic whose poll belongs to another one (and their delete removed
 * the new topic).
 */
class KunenaTopicSplitPollTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    private const OTHER_CATEGORY = 3;

    /**
     * The topic which the shadows point to, with its messages.
     */
    private const TARGET = 1;

    /**
     * The topic which waits for approval.
     */
    private const UNAPPROVED = 5;

    /**
     * The objects of Joomla and of Kunena which the test replaces, put back after the test.
     */
    private array $saved = [];

    private ?Dispatcher $dispatcher = null;

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
        $this->dispatcher = $dispatcher;
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
            ['id' => 1, 'parentid' => 0, 'name' => 'Section', 'alias' => '1-section', 'published' => 1],
            ['id' => self::CATEGORY, 'parentid' => 1, 'name' => 'Category', 'alias' => '2-category', 'published' => 1, 'numTopics' => 1, 'numPosts' => 2],
            ['id' => self::OTHER_CATEGORY, 'parentid' => 1, 'name' => 'Other', 'alias' => '3-other', 'published' => 1, 'numTopics' => 0, 'numPosts' => 0],
        ]);
        $this->insertRows('#__kunena_aliases', [
            ['alias' => '2-category', 'type' => 'catid', 'item' => self::CATEGORY],
            ['alias' => '3-other', 'type' => 'catid', 'item' => self::OTHER_CATEGORY],
        ]);

        $this->insertRow('#__kunena_topics', [
            'id' => self::TARGET, 'category_id' => self::CATEGORY, 'subject' => 'Target', 'hold' => 0, 'posts' => 2,
            'first_post_id' => 1, 'first_post_time' => 1000, 'last_post_id' => 2, 'last_post_time' => 1100,
        ]);
        $this->insertRows('#__kunena_messages', [
            ['id' => 1, 'parent' => 0, 'thread' => self::TARGET, 'catid' => self::CATEGORY, 'subject' => 'Target', 'time' => 1000, 'hold' => 0],
            ['id' => 2, 'parent' => 1, 'thread' => self::TARGET, 'catid' => self::CATEGORY, 'subject' => 'Target', 'time' => 1100, 'hold' => 0],
        ]);
        $this->insertRows('#__kunena_messages_text', [
            ['mesid' => 1, 'message' => 'First'],
            ['mesid' => 2, 'message' => 'Reply'],
            ['mesid' => 10, 'message' => 'Waits'],
        ]);

        // The topic 5 waits for approval: its message 10 too. The category does not count it.
        $this->insertRow('#__kunena_topics', [
            'id' => self::UNAPPROVED, 'category_id' => self::CATEGORY, 'subject' => 'Waits', 'hold' => 1, 'posts' => 1,
            'first_post_id' => 10, 'first_post_time' => 2000, 'last_post_id' => 10, 'last_post_time' => 2000,
        ]);
        $this->insertRow('#__kunena_messages', [
            'id' => 10, 'parent' => 0, 'thread' => self::UNAPPROVED, 'catid' => self::CATEGORY, 'subject' => 'Waits', 'time' => 2000, 'hold' => 1,
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

    public function testThePollOfASplitFirstMessageNamesTheNewTopic(): void
    {
        $this->insertRow('#__kunena_polls', ['id' => 4, 'title' => 'Poll', 'threadid' => self::TARGET]);
        $this->getDBDriver()->setQuery('UPDATE #__kunena_topics SET poll_id = 4 WHERE id = ' . self::TARGET)->execute();

        $new = KunenaTopicHelper::get(self::TARGET)->move(KunenaCategoryHelper::get(self::OTHER_CATEGORY), [1]);

        $this->assertNotSame(self::TARGET, (int) $new->id);
        $this->assertSame([['threadid' => (int) $new->id]], array_map(static fn ($row) => ['threadid' => (int) $row['threadid']], $this->fetchAll('SELECT threadid FROM #__kunena_polls')));
        $this->assertSame(
            [self::TARGET => 0, (int) $new->id => 4],
            array_map('intval', array_column($this->fetchAll('SELECT id, poll_id FROM #__kunena_topics WHERE id IN (' . self::TARGET . ', ' . (int) $new->id . ')'), 'poll_id', 'id'))
        );
    }

    /**
     * The messages of a split are given to the search of the site again (onKunenaAfterSave, which the plugin Kunena - Finder turns
     * into the update of the index), with their new topic and category: the split moves them with SQL, and only the move of a whole
     * topic indexed its messages again, so a reply split into a closed category stayed in the results of the search of everybody.
     */
    public function testTheMessagesOfASplitAreIndexedAgainWithTheirNewCategory(): void
    {
        $saved = [];
        $this->dispatcher->addListener('onKunenaAfterSave', static function ($event) use (&$saved): void {
            [$context, $table] = [$event->getArgument(0), $event->getArgument(1)];

            if ($context === 'com_kunena.message') {
                $saved[(int) $table->id] = (int) $table->catid;
            }
        });

        KunenaTopicHelper::get(self::TARGET)->move(KunenaCategoryHelper::get(self::OTHER_CATEGORY), [2]);

        $this->assertSame([2 => self::OTHER_CATEGORY], $saved);
    }
}
