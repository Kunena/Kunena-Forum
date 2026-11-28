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
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The prune of a category with "trash" (Tools > Prune, KunenaCategory::trash()) takes the posts of the trashed topics from the counts
 * of their authors, as the delete of a topic does. The count of a user is the sum of his posts in the topics (#__kunena_user_topics),
 * which nothing recounted: every author kept the posts of his trashed topics, and his rank, until a recount of the tools.
 */
class KunenaCategoryTrashUserPostsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    private const AUTHOR = 42;

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

        // As in KunenaTopicHelperTrashTest: the database of the container is the one of the tests, a command line application
        // without plugins, the settings which the code reads, caches and access which do nothing.
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

        $this->insertRow('#__kunena_categories', ['id' => self::CATEGORY, 'parentid' => 0, 'name' => 'Category', 'alias' => '2-category', 'published' => 1, 'numTopics' => 1, 'numPosts' => 2]);
        $this->insertRow('#__kunena_aliases', ['alias' => '2-category', 'type' => 'catid', 'item' => self::CATEGORY]);

        // An old topic of the author with two published posts: his count is 2.
        $this->insertRow('#__kunena_topics', [
            'id'              => 1,
            'category_id'     => self::CATEGORY,
            'subject'         => 'Old',
            'hold'            => 0,
            'posts'           => 2,
            'first_post_id'   => 1,
            'last_post_id'    => 2,
            'first_post_time' => 1000,
            'last_post_time'  => 1100,
        ]);
        $this->insertRows('#__kunena_messages', [
            ['id' => 1, 'parent' => 0, 'thread' => 1, 'catid' => self::CATEGORY, 'userid' => self::AUTHOR, 'subject' => 'Old', 'time' => 1000, 'hold' => 0],
            ['id' => 2, 'parent' => 1, 'thread' => 1, 'catid' => self::CATEGORY, 'userid' => self::AUTHOR, 'subject' => 'Old', 'time' => 1100, 'hold' => 0],
        ]);
        $this->insertRows('#__kunena_messages_text', [['mesid' => 1, 'message' => 'One'], ['mesid' => 2, 'message' => 'Two']]);
        $this->insertRow('#__kunena_user_topics', ['user_id' => self::AUTHOR, 'topic_id' => 1, 'category_id' => self::CATEGORY, 'posts' => 2, 'last_post_id' => 2, 'owner' => 1]);
        $this->insertRow('#__kunena_users', ['userid' => self::AUTHOR, 'posts' => 2]);
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

            // The helpers keep the topics and the users for the request: the next test has rows of its own.
            $this->replace(KunenaTopicHelper::class, '_instances', []);
            $this->replace(KunenaUserHelper::class, '_instances', []);
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

    public function testTheTrashedPostsLeaveTheCountOfTheirAuthor(): void
    {
        $this->assertSame(1, KunenaCategoryHelper::get(self::CATEGORY)->trash(time()));

        $this->assertSame(0, (int) $this->fetchAll('SELECT posts FROM #__kunena_users WHERE userid = ' . self::AUTHOR)[0]['posts']);
    }
}
