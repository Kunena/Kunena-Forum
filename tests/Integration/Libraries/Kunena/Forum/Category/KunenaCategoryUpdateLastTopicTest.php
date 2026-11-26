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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Category;

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
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The last topic of a category after its last topic was deleted or moved away: the topic with the newest post by time. The category
 * took the topic with the highest id of its last post, and a scheduled reply keeps its id when it is published later, so the category
 * showed an older post as its last one.
 */
class KunenaCategoryUpdateLastTopicTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    /**
     * The objects of Joomla and of Kunena which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        // The categories and the topics need the framework of Joomla.
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
            ['id' => 1, 'parentid' => 0, 'name' => 'Section', 'alias' => '1-section', 'published' => 1],
            ['id' => self::CATEGORY, 'parentid' => 1, 'name' => 'Category', 'alias' => '2-category', 'published' => 1, 'numTopics' => 3, 'numPosts' => 3,
                'last_topic_id' => 1, 'last_post_id' => 30, 'last_post_time' => 3000],
        ]);
        $this->insertRow('#__kunena_aliases', ['alias' => '2-category', 'type' => 'catid', 'item' => self::CATEGORY]);

        // The topic 1 has the last post of the category. The topic 2 has the post 20 of the time 1000; the topic 3 the scheduled
        // reply 10, published at 2000: it is newer than the post 20.
        foreach ([1 => [30, 3000], 2 => [20, 1000], 3 => [10, 2000]] as $id => [$post, $time]) {
            $this->insertRow('#__kunena_topics', [
                'id' => $id, 'category_id' => self::CATEGORY, 'subject' => "Topic $id", 'hold' => 0, 'posts' => 1,
                'first_post_id' => $post, 'first_post_time' => $time, 'last_post_id' => $post, 'last_post_time' => $time,
            ]);
        }
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

    public function testTheNewestPostByTimeIsTheLastOneAfterTheLastTopicIsDeleted(): void
    {
        $topic       = KunenaTopicHelper::get(1);
        $topic->hold = 2;
        $this->getDBDriver()->setQuery('UPDATE #__kunena_topics SET hold = 2 WHERE id = 1')->execute();

        KunenaCategoryHelper::get(self::CATEGORY)->update($topic);

        $this->assertSame(
            [['last_topic_id' => 3, 'last_post_id' => 10]],
            array_map(static fn ($row) => array_map('intval', $row), $this->fetchAll('SELECT last_topic_id, last_post_id FROM #__kunena_categories WHERE id = ' . self::CATEGORY))
        );
    }

    /**
     * The save of the counts (update()) does not order the categories again, the next save of the category does: the mark of
     * update() stayed on the object (a note asked to remove that mark), and a category moved after a post kept a wrong place in its list.
     */
    public function testTheNextSaveAfterTheCountsOrdersTheCategoriesAgain(): void
    {
        $category = KunenaCategoryHelper::get(self::CATEGORY);
        $category->update(KunenaTopicHelper::get(1), 0, 1);

        $this->assertTrue((new ReflectionProperty(KunenaCategory::class, 'reorderOnSave'))->getValue($category));
    }
}
