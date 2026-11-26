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
 * A deleted category (the categories of the backend) takes the files of its messages with it. KunenaCategory::delete() removed the
 * topics, messages, texts, polls... but not the files (a note: "delete attachments"): their rows stayed without a message, and the
 * files on the disk, reachable by their address without the protection.
 */
class KunenaCategoryDeleteAttachmentsTest extends IntegrationTestCase implements DBTestInterface
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

        // KunenaTopic::publish() sets the deleted time of the messages: the column is in the tables of an installation
        // (create_tables.utf8.sql and the upgrade), not in the schema which makes the tables of the tests. It is there for the
        // test only: the other tests compare the tables with that schema.
        $driver->setQuery("ALTER TABLE #__kunena_messages ADD deleted_time int(11) NOT NULL default '0' AFTER moved")->execute();
        $this->deletedTime = true;

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

        if ($this->deletedTime) {
            $this->getDBDriver()->setQuery('ALTER TABLE #__kunena_messages DROP deleted_time')->execute();
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

    public function testDeletingACategoryRemovesTheFilesOfItsMessages(): void
    {
        $this->insertRows('#__kunena_attachments', [
            ['id' => 7, 'mesid' => 1, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'a.txt', 'filename_real' => 'a.txt'],
            ['id' => 8, 'mesid' => 3, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'b.txt', 'filename_real' => 'b.txt'],
            // A file of a message of another category, and an upload which waits for its message.
            ['id' => 9, 'mesid' => 99, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'c.txt', 'filename_real' => 'c.txt'],
            ['id' => 10, 'mesid' => 0, 'userid' => 42, 'folder' => 'media/kunena/attachments/42', 'filename' => 'd.txt', 'filename_real' => 'd.txt'],
        ]);

        $this->insertRow('#__kunena_messages', ['id' => 99, 'parent' => 0, 'thread' => 50, 'catid' => self::OTHER_CATEGORY, 'subject' => 'Other', 'time' => 1000, 'hold' => 0]);

        // The delete removes the Joomla tags of the topics: the table is in an installation of Joomla, not in the schema of the
        // tests. It is there for this test only.
        $this->getDBDriver()->setQuery("CREATE TABLE IF NOT EXISTS #__contentitem_tag_map (type_alias varchar(255) NOT NULL DEFAULT '', content_item_id int NOT NULL DEFAULT 0, tag_id int NOT NULL DEFAULT 0)")->execute();

        try {
            $this->assertTrue(KunenaCategoryHelper::get(self::CATEGORY)->delete());
        } finally {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS #__contentitem_tag_map')->execute();
        }

        $this->assertSame([9, 10], array_map('intval', array_column($this->fetchAll('SELECT id FROM #__kunena_attachments ORDER BY id'), 'id')));
    }

    /**
     * Delete the category (with the table of the Joomla tags, which is not in the schema of the tests).
     */
    private function deleteCategory(): void
    {
        $this->getDBDriver()->setQuery("CREATE TABLE IF NOT EXISTS #__contentitem_tag_map (type_alias varchar(255) NOT NULL DEFAULT '', content_item_id int NOT NULL DEFAULT 0, tag_id int NOT NULL DEFAULT 0)")->execute();

        try {
            $this->assertTrue(KunenaCategoryHelper::get(self::CATEGORY)->delete());
        } finally {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS #__contentitem_tag_map')->execute();
        }
    }

    /**
     * A deleted category takes the private messages of its posts with it. They stayed in the database, with their text, their
     * recipients and their links, shown nowhere.
     */
    public function testDeletingACategoryTakesThePrivateMessagesOfItsPosts(): void
    {
        $this->insertRow('#__kunena_messages', ['id' => 99, 'parent' => 0, 'thread' => 50, 'catid' => self::OTHER_CATEGORY, 'subject' => 'Other', 'time' => 1000, 'hold' => 0]);

        foreach ([1 => 1, 2 => 99] as $id => $message) {
            $this->insertRow('#__kunena_private', ['id' => $id, 'author_id' => 42, 'created_at' => '2026-01-01 00:00:00', 'subject' => 'Private', 'body' => "Secret $id", 'params' => '']);
            $this->insertRow('#__kunena_private_post_map', ['private_id' => $id, 'message_id' => $message]);
            $this->insertRow('#__kunena_private_user_map', ['private_id' => $id, 'user_id' => 42]);
        }

        $this->deleteCategory();

        $ids = fn (string $table, string $column): array => array_map('intval', array_column($this->fetchAll("SELECT $column FROM $table ORDER BY $column"), $column));

        $this->assertSame([2], $ids('#__kunena_private', 'id'));
        $this->assertSame([2], $ids('#__kunena_private_post_map', 'private_id'));
        $this->assertSame([2], $ids('#__kunena_private_user_map', 'private_id'));
    }

    /**
     * The subcategories of a deleted category go to its parent. They kept the deleted category as their parent: no list of the
     * forum showed them any more, and a post in one of them ended in an error.
     */
    public function testTheSubcategoriesOfADeletedCategoryGoToItsParent(): void
    {
        $this->getDBDriver()->setQuery('UPDATE #__kunena_categories SET parentid = ' . self::OTHER_CATEGORY . ' WHERE id = ' . self::CATEGORY)->execute();
        $this->insertRows('#__kunena_categories', [
            ['id' => 8, 'parentid' => self::CATEGORY, 'name' => 'Child', 'alias' => '8-child', 'published' => 1],
            ['id' => 9, 'parentid' => 8, 'name' => 'Grandchild', 'alias' => '9-grandchild', 'published' => 1],
        ]);

        $this->deleteCategory();

        $this->assertEquals(
            [self::OTHER_CATEGORY => 0, 8 => self::OTHER_CATEGORY, 9 => 8],
            array_column($this->fetchAll('SELECT id, parentid FROM #__kunena_categories ORDER BY id'), 'parentid', 'id')
        );
    }
}
