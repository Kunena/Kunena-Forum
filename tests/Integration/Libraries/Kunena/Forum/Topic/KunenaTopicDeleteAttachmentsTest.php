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
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
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
 * A topic deleted for good (the purge of the trash in the backend, TrashsModel::purgeTopics(), and the frontend) takes the files of its
 * messages with it. KunenaTopic::delete() removed the texts, the thank yous, the history... but not the files (a note: "delete
 * attachments"): their rows stayed without a message, and the files on the disk, reachable by their address without the protection.
 */
class KunenaTopicDeleteAttachmentsTest extends IntegrationTestCase implements DBTestInterface
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

        // The helper of the attachments keeps them for the request: each test has rows of its own (another test used the same ids).
        foreach (['_instances', '_messages', '_messagesLoaded'] as $cache) {
            (new ReflectionProperty(KunenaAttachmentHelper::class, $cache))->setValue(null, []);
        }

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

        // The helper of the attachments keeps them for the request: each test has rows of its own (another test used the same ids).
        foreach (['_instances', '_messages', '_messagesLoaded'] as $cache) {
            (new ReflectionProperty(KunenaAttachmentHelper::class, $cache))->setValue(null, []);
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

    public function testDeletingATopicForGoodRemovesTheFilesOfItsMessages(): void
    {
        $this->insertRows('#__kunena_attachments', [
            ['id' => 7, 'mesid' => 1, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'a.txt', 'filename_real' => 'a.txt'],
            ['id' => 8, 'mesid' => 3, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'b.txt', 'filename_real' => 'b.txt'],
            // A file of another topic, and an upload which waits for its message.
            ['id' => 9, 'mesid' => 99, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'c.txt', 'filename_real' => 'c.txt'],
            ['id' => 10, 'mesid' => 0, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'd.txt', 'filename_real' => 'd.txt'],
        ]);

        $this->assertTrue(KunenaTopicHelper::get(self::TARGET)->delete());

        $this->assertSame([9, 10], array_map('intval', array_column($this->fetchAll('SELECT id FROM #__kunena_attachments ORDER BY id'), 'id')));
    }

    /**
     * The topics deleted together (KunenaTopicHelper::delete(), the prune of a category) take the files of their messages too: the
     * helper deleted the texts, the polls, the ratings... and a note asked to "delete attachments". The clean-up after the prune
     * removed 50 rows of them at most, never their files.
     */
    public function testDeletingSeveralTopicsRemovesTheFilesOfTheirMessages(): void
    {
        $this->topic(5, 0, 0, 1);
        $this->insertRow('#__kunena_messages', ['id' => 50, 'parent' => 0, 'thread' => 5, 'catid' => self::CATEGORY, 'subject' => 'Five', 'time' => 1300, 'hold' => 0]);
        $this->insertRows('#__kunena_attachments', [
            ['id' => 7, 'mesid' => 1, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'a.txt', 'filename_real' => 'a.txt'],
            ['id' => 8, 'mesid' => 50, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'b.txt', 'filename_real' => 'b.txt'],
            // A file of another topic, and an upload which waits for its message.
            ['id' => 9, 'mesid' => 99, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'c.txt', 'filename_real' => 'c.txt'],
            ['id' => 10, 'mesid' => 0, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'd.txt', 'filename_real' => 'd.txt'],
        ]);

        // The helper removes the Joomla tags of the topics: the table is in an installation of Joomla, not in the schema of the
        // tests. It is there for this test only.
        $this->getDBDriver()->setQuery("CREATE TABLE IF NOT EXISTS #__contentitem_tag_map (type_alias varchar(255) NOT NULL DEFAULT '', content_item_id int NOT NULL DEFAULT 0, tag_id int NOT NULL DEFAULT 0)")->execute();

        try {
            $this->assertNotFalse(KunenaTopicHelper::delete([self::TARGET, 5]));
        } finally {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS #__contentitem_tag_map')->execute();
        }

        $this->assertSame([9, 10], array_map('intval', array_column($this->fetchAll('SELECT id FROM #__kunena_attachments ORDER BY id'), 'id')));
        $this->assertSame(0, $this->countRows('#__kunena_topics'));
    }

    /**
     * A private message of each of the two posts, with its recipient and its attachment link.
     */
    private function privateMessages(int $first, int $second): void
    {
        foreach ([1 => $first, 2 => $second] as $id => $message) {
            $this->insertRow('#__kunena_private', ['id' => $id, 'author_id' => 42, 'created_at' => '2026-01-01 00:00:00', 'subject' => 'Private', 'body' => "Secret $id", 'params' => '']);
            $this->insertRow('#__kunena_private_post_map', ['private_id' => $id, 'message_id' => $message]);
            $this->insertRow('#__kunena_private_user_map', ['private_id' => $id, 'user_id' => 42]);
            $this->insertRow('#__kunena_private_attachment_map', ['private_id' => $id, 'attachment_id' => 70 + $id]);
        }
    }

    /**
     * The private messages which are left, in each of their tables.
     */
    private function privateLeft(): array
    {
        $ids = fn (string $table, string $column): array => array_map('intval', array_column($this->fetchAll("SELECT $column FROM $table ORDER BY $column"), $column));

        return [
            'private'     => $ids('#__kunena_private', 'id'),
            'posts'       => $ids('#__kunena_private_post_map', 'private_id'),
            'users'       => $ids('#__kunena_private_user_map', 'private_id'),
            'attachments' => $ids('#__kunena_private_attachment_map', 'private_id'),
        ];
    }

    private const ONLY_THE_SECOND = ['private' => [2], 'posts' => [2], 'users' => [2], 'attachments' => [2]];

    /**
     * A topic deleted for good takes the private messages of its posts with it (one topic, and several topics at once). They
     * stayed in the database, with their text, their recipients and their links, shown nowhere and never removed.
     */
    public function testATopicDeletedForGoodTakesThePrivateMessagesOfItsPosts(): void
    {
        $this->privateMessages(1, 99);

        $this->assertTrue(KunenaTopicHelper::get(self::TARGET)->delete());

        $this->assertSame(self::ONLY_THE_SECOND, $this->privateLeft());
    }

    public function testSeveralTopicsDeletedForGoodTakeThePrivateMessagesOfTheirPosts(): void
    {
        $this->privateMessages(1, 99);
        $this->getDBDriver()->setQuery("CREATE TABLE IF NOT EXISTS #__contentitem_tag_map (type_alias varchar(255) NOT NULL DEFAULT '', content_item_id int NOT NULL DEFAULT 0, tag_id int NOT NULL DEFAULT 0)")->execute();

        try {
            $this->assertNotFalse(KunenaTopicHelper::delete([self::TARGET]));
        } finally {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS #__contentitem_tag_map')->execute();
        }

        $this->assertSame(self::ONLY_THE_SECOND, $this->privateLeft());
    }

    /**
     * A shown topic which is deleted for good leaves the counters of its category: the topics, the posts and the last post. They
     * kept it (only the delete of several topics counted the category again): the category still counted the topic and named it
     * as its last topic, a link to a page which is gone.
     */
    public function testAShownTopicDeletedForGoodLeavesTheCountersOfItsCategory(): void
    {
        $this->getDBDriver()->setQuery(
            'UPDATE #__kunena_categories SET numTopics = 1, numPosts = 1, last_topic_id = ' . self::TARGET . ', last_post_id = 1, last_post_time = 1000 WHERE id = ' . self::CATEGORY
        )->execute();

        $this->assertTrue(KunenaTopicHelper::get(self::TARGET)->delete());

        $this->assertEquals(
            [['numTopics' => 0, 'numPosts' => 0, 'last_topic_id' => 0, 'last_post_id' => 0]],
            $this->fetchAll('SELECT numTopics, numPosts, last_topic_id, last_post_id FROM #__kunena_categories WHERE id = ' . self::CATEGORY)
        );
    }
}
