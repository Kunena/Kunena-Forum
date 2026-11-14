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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Message;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Approving a message counts it once (issue #9619): the first post of an unapproved topic, which publishes the topic, and a reply in
 * a published topic add one post to the topic, the category, the user topic of the author and the author.
 */
class KunenaMessageApproveCountTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    private const TOPIC = 1;

    private const AUTHOR = 42;

    /**
     * The objects of Joomla and of Kunena which the test replaces, put back after the test.
     */
    private array $saved = [];

    /**
     * The columns which were added to the table of the users of Joomla for the test.
     */
    private array $userColumns = [];

    /**
     * The column deleted_time was added to the table of the messages for the test.
     */
    private bool $deletedTime = false;

    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // The messages and the topics need the framework of Joomla.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The library reads the database of the container of Joomla: that is the database of the tests here. The application
        // gives the dispatcher of the events and is a command line one, no plugin is loaded, the configuration has the settings
        // which the code reads, the caches and the access which a saved category clears do nothing, and the Joomla user of a saved
        // Kunena user is a user with its id.
        $driver      = $this->getDBDriver();
        $dispatcher  = new Dispatcher();
        $container   = new Container();
        $userFactory = $this->createStub(UserFactoryInterface::class);
        $userFactory->method('loadUserById')->willReturnCallback(static function (int $id): User {
            $user     = new User();
            $user->id = $id;

            return $user;
        });
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));
        $container->share(UserFactoryInterface::class, static fn () => $userFactory);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');
        $language = $this->createStub(Language::class);
        $language->method('transliterate')->willReturnArgument(0);
        $language->method('getTag')->willReturn('en-GB');
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, [
            'threadsPerPage'  => 20,
            'ratingEnabled'   => 0,
            'readOnly'        => 0,
            'askEmail'        => 0,
            'floodProtection' => 0,
            'attachmentLimit' => 0,
            'showNew'         => 1,
        ]);

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

        // The approval is made by a guest of the command line: the message loads its author, and the topic is not marked read. (Done
        // after the application is replaced: the helper finds the current user with it when it is loaded.)
        $this->saved['me']    = $this->replace(KunenaUserHelper::class, '_me', new KunenaUser(0));
        $this->saved['users'] = $this->replace(KunenaUserHelper::class, '_instances', []);

        // A Kunena user is loaded with the dates of the Joomla user: the columns are in the table of an installation of Joomla, not
        // in the dataset of the tests. They are there for the test only.
        $columns = array_column($this->fetchAll('SHOW COLUMNS FROM #__users'), 'Field');

        foreach (['registerDate' => 'datetime NOT NULL', 'lastvisitDate' => 'datetime NULL'] as $column => $type) {
            if (!\in_array($column, $columns, true)) {
                $driver->setQuery("ALTER TABLE #__users ADD $column $type")->execute();
                $this->userColumns[] = $column;
            }
        }

        // The message is saved with its deleted time: the column is in the tables of an installation (create_tables.utf8.sql and the
        // upgrade), not in the schema which makes the tables of the tests. It is there for the test only.
        $driver->setQuery("ALTER TABLE #__kunena_messages ADD deleted_time int(11) NOT NULL default '0' AFTER moved")->execute();
        $this->deletedTime = true;

        $this->insertRow('#__users', ['id' => self::AUTHOR, 'name' => 'Author', 'username' => 'author', 'params' => '', 'registerDate' => '2020-01-01 00:00:00']);

        // The approval writes the user topics (the recount makes the missing ones): they are emptied before the next test.
        DBTestHelper::markDirty('#__kunena_user_topics');
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
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['users']);
            $this->replace(KunenaUserHelper::class, '_me', $this->saved['me']);

            // The helpers keep the messages, the topics and the user topics for the request: the next test has rows of its own.
            $this->replace(KunenaTopicHelper::class, '_instances', []);
            $this->replace(KunenaMessageHelper::class, '_instances', []);
            $this->replace(KunenaTopicUserHelper::class, '_instances', []);
            $this->replace(KunenaTopicUserHelper::class, '_topics', []);
        }

        if ($this->deletedTime) {
            $this->getDBDriver()->setQuery('ALTER TABLE #__kunena_messages DROP deleted_time')->execute();
        }

        foreach ($this->userColumns as $column) {
            $this->getDBDriver()->setQuery("ALTER TABLE #__users DROP $column")->execute();
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
     * The category, the topic (its first and last posts are its messages in its state) and its messages by the author ([id, parent,
     * hold, time] each), and the author with the posts which are published.
     */
    private function seed(int $topicHold, int $topicPosts, int $numTopics, int $numPosts, array $messages): void
    {
        $this->insertRow('#__kunena_users', [
            'userid' => self::AUTHOR,
            'posts'  => \count(array_filter($messages, static fn ($message) => $message[2] == KunenaForum::PUBLISHED)),
        ]);
        $this->insertRow('#__kunena_categories', [
            'id'        => self::CATEGORY,
            'parentid'  => 0,
            'name'      => 'Category',
            'alias'     => '2-category',
            'published' => 1,
            'numTopics' => $numTopics,
            'numPosts'  => $numPosts,
        ]);
        $this->insertRow('#__kunena_aliases', ['alias' => '2-category', 'type' => 'catid', 'item' => self::CATEGORY]);

        $visible   = array_filter($messages, static fn ($message) => $message[2] == $topicHold);
        $first     = reset($visible);
        $last      = end($visible);

        $this->insertRow('#__kunena_topics', [
            'id'                => self::TOPIC,
            'category_id'       => self::CATEGORY,
            'subject'           => 'Topic',
            'hold'              => $topicHold,
            'posts'             => $topicPosts,
            'first_post_id'     => $first[0],
            'first_post_time'   => $first[3],
            'first_post_userid' => self::AUTHOR,
            'last_post_id'      => $last[0],
            'last_post_time'    => $last[3],
            'last_post_userid'  => self::AUTHOR,
        ]);

        foreach ($messages as [$id, $parent, $hold, $time]) {
            $this->insertRow('#__kunena_messages', [
                'id'      => $id,
                'parent'  => $parent,
                'thread'  => self::TOPIC,
                'catid'   => self::CATEGORY,
                'userid'  => self::AUTHOR,
                'subject' => 'Topic',
                'time'    => $time,
                'hold'    => $hold,
            ]);
            $this->insertRow('#__kunena_messages_text', ['mesid' => $id, 'message' => "Message $id"]);
        }
    }

    /**
     * The counters of the topic, the category, the user topic of the author and the author, as they are in the database.
     */
    private function counters(): array
    {
        return [
            'topic'     => $this->fetchAll('SELECT hold, posts FROM #__kunena_topics WHERE id = ' . self::TOPIC),
            'category'  => $this->fetchAll('SELECT numTopics, numPosts FROM #__kunena_categories WHERE id = ' . self::CATEGORY),
            'usertopic' => $this->fetchAll('SELECT posts FROM #__kunena_user_topics WHERE user_id = ' . self::AUTHOR . ' AND topic_id = ' . self::TOPIC),
            'user'      => $this->fetchAll('SELECT posts FROM #__kunena_users WHERE userid = ' . self::AUTHOR),
        ];
    }

    public function testApprovingTheFirstPostOfAnUnapprovedTopicCountsItOnce(): void
    {
        // An unapproved topic (hold 1) with its unapproved first post: the category and the author do not count it yet.
        $this->seed(KunenaForum::UNAPPROVED, 1, 3, 7, [[10, 0, KunenaForum::UNAPPROVED, 1000]]);

        $this->assertTrue(KunenaMessageHelper::get(10)->publish(KunenaForum::PUBLISHED));

        $this->assertEquals([
            'topic'     => [['hold' => 0, 'posts' => 1]],
            'category'  => [['numTopics' => 4, 'numPosts' => 8]],
            'usertopic' => [['posts' => 1]],
            'user'      => [['posts' => 1]],
        ], $this->counters());
    }

    public function testApprovingTheFirstPostOfAnUnapprovedTopicWithAnUnapprovedReplyCountsThePublishedPostOnly(): void
    {
        $this->seed(KunenaForum::UNAPPROVED, 2, 3, 7, [[10, 0, KunenaForum::UNAPPROVED, 1000], [11, 10, KunenaForum::UNAPPROVED, 1100]]);

        $this->assertTrue(KunenaMessageHelper::get(10)->publish(KunenaForum::PUBLISHED));

        $this->assertEquals([
            'topic'     => [['hold' => 0, 'posts' => 1]],
            'category'  => [['numTopics' => 4, 'numPosts' => 8]],
            'usertopic' => [['posts' => 1]],
            'user'      => [['posts' => 1]],
        ], $this->counters());
    }

    public function testApprovingAReplyInAPublishedTopicAddsOnePost(): void
    {
        // A published topic with its published first post (counted by the author's topic row) and an unapproved reply.
        $this->seed(KunenaForum::PUBLISHED, 1, 3, 7, [[10, 0, KunenaForum::PUBLISHED, 1000], [11, 10, KunenaForum::UNAPPROVED, 1100]]);
        $this->insertRow('#__kunena_user_topics', ['user_id' => self::AUTHOR, 'topic_id' => self::TOPIC, 'category_id' => self::CATEGORY, 'posts' => 1, 'last_post_id' => 10, 'owner' => 1]);

        $this->assertTrue(KunenaMessageHelper::get(11)->publish(KunenaForum::PUBLISHED));

        $this->assertEquals([
            'topic'     => [['hold' => 0, 'posts' => 2]],
            'category'  => [['numTopics' => 3, 'numPosts' => 8]],
            'usertopic' => [['posts' => 2]],
            'user'      => [['posts' => 2]],
        ], $this->counters());
    }
}
