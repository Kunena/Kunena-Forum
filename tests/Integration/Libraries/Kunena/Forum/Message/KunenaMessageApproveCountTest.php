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
use Kunena\Forum\Libraries\Integration\KunenaActivity;
use Kunena\Forum\Libraries\Tools\KunenaModerationQueue;
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

    /**
     * A published topic with three published posts of the author, who has them in the row of the topic.
     */
    private function seedThreeShownPosts(): void
    {
        $this->seed(KunenaForum::PUBLISHED, 3, 3, 7, [
            [10, 0, KunenaForum::PUBLISHED, 1000],
            [11, 10, KunenaForum::PUBLISHED, 1100],
            [12, 10, KunenaForum::PUBLISHED, 1200],
        ]);
        $this->insertRow('#__kunena_user_topics', ['user_id' => self::AUTHOR, 'topic_id' => self::TOPIC, 'category_id' => self::CATEGORY, 'posts' => 3, 'last_post_id' => 12, 'owner' => 1]);
    }

    /**
     * Messages read as a list (the posts a moderator ticked, the posts of a user) are made from the rows of one query. Such a
     * message did not know the state of its row: putting it in the trash took nothing off the counters.
     */
    public function testTrashingMessagesReadAsAListTakesThemOffTheCounters(): void
    {
        $this->seedThreeShownPosts();

        foreach (KunenaMessageHelper::getMessages([11, 12], 'none') as $message) {
            $this->assertTrue($message->publish(KunenaForum::DELETED));
        }

        $this->assertEquals([
            'topic'     => [['hold' => 0, 'posts' => 1]],
            'category'  => [['numTopics' => 3, 'numPosts' => 5]],
            'usertopic' => [['posts' => 1]],
            'user'      => [['posts' => 1]],
        ], $this->counters());
    }

    public function testTrashingTheMessagesOfATopicReadAsAListTakesThemOffTheCounters(): void
    {
        $this->seedThreeShownPosts();

        $messages = KunenaMessageHelper::getMessagesByTopic(KunenaTopicHelper::get(self::TOPIC), 0, 50, 'ASC', 0);

        $this->assertCount(3, $messages);

        // Every post but the first one.
        foreach (\array_slice(array_values($messages), 1) as $message) {
            $this->assertTrue($message->publish(KunenaForum::DELETED));
        }

        $this->assertEquals([
            'topic'     => [['hold' => 0, 'posts' => 1]],
            'category'  => [['numTopics' => 3, 'numPosts' => 5]],
            'usertopic' => [['posts' => 1]],
            'user'      => [['posts' => 1]],
        ], $this->counters());
    }

    /**
     * The bulk action "delete permanently" of the moderators reads the ticked posts as a list too.
     */
    public function testDeletingMessagesReadAsAListForGoodTakesThemOffTheCounters(): void
    {
        $this->seedThreeShownPosts();

        foreach (KunenaMessageHelper::getMessages([11, 12], 'none') as $message) {
            $this->assertTrue($message->delete());
        }

        $this->assertEquals([
            'topic'     => [['hold' => 0, 'posts' => 1]],
            'category'  => [['numTopics' => 3, 'numPosts' => 5]],
            'usertopic' => [['posts' => 1]],
            'user'      => [['posts' => 1]],
        ], $this->counters());
    }

    /**
     * The tool "Moderation queue" of the backend (here: the publish needs this setup). A reply which waits for approval in a
     * topic which went to the trash afterwards: approving it published the topic again, with that reply only, while its first
     * post and the other replies stayed in the trash.
     */
    public function testTheModerationQueueLeavesTheWaitingReplyOfATrashedTopic(): void
    {
        $this->seed(KunenaForum::TOPIC_DELETED, 1, 3, 7, [[10, 0, KunenaForum::TOPIC_DELETED, 1000], [11, 10, KunenaForum::UNAPPROVED, 1100]]);

        $this->assertSame(0, KunenaModerationQueue::unapprovedOnSite()['total']);
        $this->assertSame(['done' => 0, 'failed' => 1], KunenaModerationQueue::apply([11], KunenaModerationQueue::APPROVE));

        $this->assertEquals([['hold' => 3, 'posts' => 1]], $this->counters()['topic']);
        $this->assertEquals([['numTopics' => 3, 'numPosts' => 7]], $this->counters()['category']);
    }

    /**
     * The moderation queue and a reply which waits for its date (scheduled, not held for review): it is for no moderator, and
     * approving it published it at once, before the date which its author chose. One held for review stays in the queue.
     */
    public function testTheModerationQueueLeavesAScheduledReply(): void
    {
        $this->seed(KunenaForum::PUBLISHED, 1, 3, 7, [[10, 0, KunenaForum::PUBLISHED, 1000], [11, 10, KunenaForum::UNAPPROVED, 1100], [12, 10, KunenaForum::UNAPPROVED, 1200]]);
        $later = time() + 7 * 86400;
        $this->getDBDriver()->setQuery("UPDATE #__kunena_messages SET scheduled_time = $later, scheduled_review = 0 WHERE id = 11")->execute();
        $this->getDBDriver()->setQuery("UPDATE #__kunena_messages SET scheduled_time = $later, scheduled_review = 1 WHERE id = 12")->execute();

        $this->assertSame([12], array_map('intval', array_column(KunenaModerationQueue::unapprovedOnSite()['items'], 'id')));
        $this->assertSame(['done' => 0, 'failed' => 1], KunenaModerationQueue::apply([11], KunenaModerationQueue::APPROVE));
        $this->assertEquals([['hold' => 1]], $this->fetchAll('SELECT hold FROM #__kunena_messages WHERE id = 11'));
    }

    /**
     * A split (here: the test needs the setup of the messages) makes a new topic with the state of the old one: of an unapproved
     * topic, an unapproved one, which the category does not count. It counted it as a shown topic.
     */
    public function testASplitOfAnUnapprovedTopicAddsNoTopicToTheTargetCategory(): void
    {
        $this->seed(KunenaForum::UNAPPROVED, 2, 3, 7, [[10, 0, KunenaForum::UNAPPROVED, 1000], [11, 10, KunenaForum::UNAPPROVED, 1100]]);

        // A section with the category of the topic and a second category, which is empty.
        $this->insertRow('#__kunena_categories', ['id' => 1, 'parentid' => 0, 'name' => 'Section', 'alias' => '1-section', 'published' => 1]);
        $this->insertRow('#__kunena_categories', ['id' => 9, 'parentid' => 1, 'name' => 'Other', 'alias' => '9-other', 'published' => 1]);
        $this->getDBDriver()->setQuery('UPDATE #__kunena_categories SET parentid = 1 WHERE id = ' . self::CATEGORY)->execute();

        $new = KunenaTopicHelper::get(self::TOPIC)->move(KunenaCategoryHelper::get(9), [11]);

        $this->assertSame(KunenaForum::UNAPPROVED, (int) $new->hold);
        $this->assertEquals([['numTopics' => 0, 'numPosts' => 0]], $this->fetchAll('SELECT numTopics, numPosts FROM #__kunena_categories WHERE id = 9'));
    }

    /**
     * A merge into a topic in the trash: the messages were hidden with it, while the category counted them (and again when the
     * topic was restored). It is refused, and nothing changes.
     */
    public function testATopicIsNotMergedIntoATopicInTheTrash(): void
    {
        $this->seed(KunenaForum::PUBLISHED, 2, 3, 7, [[10, 0, KunenaForum::PUBLISHED, 1000], [11, 10, KunenaForum::PUBLISHED, 1100]]);
        $this->insertRow('#__kunena_topics', [
            'id' => 2, 'category_id' => self::CATEGORY, 'subject' => 'Trashed', 'hold' => KunenaForum::DELETED, 'posts' => 1,
            'first_post_id' => 20, 'first_post_time' => 2000, 'first_post_userid' => self::AUTHOR,
            'last_post_id' => 20, 'last_post_time' => 2000, 'last_post_userid' => self::AUTHOR,
        ]);
        $this->insertRow('#__kunena_messages', ['id' => 20, 'parent' => 0, 'thread' => 2, 'catid' => self::CATEGORY, 'userid' => self::AUTHOR, 'subject' => 'Trashed', 'time' => 2000, 'hold' => KunenaForum::DELETED]);
        $this->insertRow('#__kunena_messages_text', ['mesid' => 20, 'message' => 'Message 20']);

        $refused = false;

        try {
            KunenaTopicHelper::get(self::TOPIC)->move(KunenaTopicHelper::get(2));
        } catch (\Exception $e) {
            $refused = true;
        }

        $this->assertTrue($refused, 'The merge into a topic in the trash was made.');
        $this->assertEquals([['numTopics' => 3, 'numPosts' => 7]], $this->counters()['category']);
        $this->assertEquals([['thread' => 1], ['thread' => 1]], $this->fetchAll('SELECT thread FROM #__kunena_messages WHERE id IN (10, 11)'));
    }

    /**
     * A shown message read as a list and saved as it is (an edit) is not a new post.
     */
    public function testSavingAShownMessageReadAsAListAddsNoPost(): void
    {
        $this->seedThreeShownPosts();

        foreach (KunenaMessageHelper::getMessages([11, 12], 'none') as $message) {
            $this->assertTrue($message->save());
        }

        $this->assertEquals([
            'topic'     => [['hold' => 0, 'posts' => 3]],
            'category'  => [['numTopics' => 3, 'numPosts' => 7]],
            'usertopic' => [['posts' => 3]],
            'user'      => [['posts' => 3]],
        ], $this->counters());
    }

    /**
     * The time of the delete of the messages, as it is in the database.
     */
    private function deletedTimes(): array
    {
        return array_column($this->fetchAll('SELECT id, deleted_time FROM #__kunena_messages ORDER BY id'), 'deleted_time', 'id');
    }

    /**
     * A message in the trash keeps the time of its delete when it is saved again (an edit of a moderator, a move). The save took
     * the time away from a message deleted with its topic (state 3), and from a deleted one (state 2) read from the database (its
     * state is text then): the task which empties the trash removed such a message for good at its next run, whatever the age.
     */
    public function testASavedMessageOfTheTrashKeepsTheTimeOfItsDelete(): void
    {
        $this->seed(KunenaForum::PUBLISHED, 1, 3, 7, [[10, 0, KunenaForum::PUBLISHED, 1000], [11, 10, KunenaForum::DELETED, 1100], [12, 10, KunenaForum::TOPIC_DELETED, 1200]]);
        $this->getDBDriver()->setQuery('UPDATE #__kunena_messages SET deleted_time = 5000 WHERE id IN (11, 12)')->execute();

        foreach ([11, 12] as $id) {
            $message          = KunenaMessageHelper::get($id);
            $message->subject = 'Changed';
            $this->assertTrue($message->save());
        }

        $this->assertEquals([10 => 0, 11 => 5000, 12 => 5000], $this->deletedTimes());
    }

    /**
     * The name of the author which a message keeps is his name as it is. It was kept escaped for HTML ("Tom &amp; Jerry"), and
     * escaped again where the kept name is shown: the post of a user who was deleted, the API, a feed.
     */
    public function testTheNameKeptOnAMessageIsNotEscaped(): void
    {
        $name = $this->getDBDriver()->quote('Tom & "Jerry"');
        $this->getDBDriver()->setQuery("UPDATE #__users SET name = $name, username = $name WHERE id = " . self::AUTHOR)->execute();
        $this->seed(KunenaForum::PUBLISHED, 1, 3, 7, [[10, 0, KunenaForum::PUBLISHED, 1000]]);

        $message          = KunenaMessageHelper::get(10);
        $message->subject = 'Changed';
        $this->assertTrue($message->save());

        $this->assertSame('Tom & "Jerry"', $this->fetchAll('SELECT name FROM #__kunena_messages WHERE id = 10')[0]['name']);
    }

    /**
     * An anonymous post of a member does not keep his name. The name of a post without an author which is the name of a user is
     * replaced, but the form gave the name of the member escaped for HTML ("Tom &amp; Jerry"): it was the name of no user, so it
     * stayed on the anonymous post and named its author. (The controller gives the name as it is now: its code is read.)
     */
    public function testAnAnonymousPostDoesNotKeepTheNameOfItsAuthor(): void
    {
        $name = $this->getDBDriver()->quote('Tom & Jerry');
        $this->getDBDriver()->setQuery("UPDATE #__users SET name = $name WHERE id = " . self::AUTHOR)->execute();
        $this->seed(KunenaForum::PUBLISHED, 1, 3, 7, [[10, 0, KunenaForum::PUBLISHED, 1000]]);

        // The post of the member, made anonymous, with his name as the form gives it now.
        $message         = KunenaMessageHelper::get(10);
        $message->userid = 0;
        $message->name   = 'Tom & Jerry';
        $message->check();

        $this->assertNotSame('Tom & Jerry', $message->name);

        // What the form gave before: the escaped name, which is the name of no user and stayed.
        $message->name = 'Tom &amp; Jerry';
        $message->check();

        $this->assertSame('Tom &amp; Jerry', $message->name);

        $controller = (string) file_get_contents(\dirname(__DIR__, 6) . '/src/site/src/Controllers/TopicController.php');

        $this->assertSame(2, substr_count($controller, "getString('authorname', \$this->me->getName('', false))"));
        $this->assertStringNotContainsString("getString('authorname', \$this->me->getName())", $controller);
    }

    /**
     * A reply which comes back from the trash tells the integrations (points, activity streams) about a restore: it told them about
     * a new reply again, so its author got the points and its subscribers the notification once more at every restore.
     */
    public function testARestoredReplyIsNoNewReplyForTheIntegrations(): void
    {
        $this->seedThreeShownPosts();

        $activity = new class () extends KunenaActivity {
            public array $calls = [];

            public function __construct()
            {
            }

            public function __call(string $method, array $arguments)
            {
                $this->calls[] = $method;

                return null;
            }
        };
        $saved = $this->replace(KunenaActivity::class, 'instance', $activity);

        try {
            $this->assertTrue(KunenaMessageHelper::get(11)->publish(KunenaForum::DELETED));
            $this->replace(KunenaMessageHelper::class, '_instances', []);
            $this->assertTrue(KunenaMessageHelper::get(11)->publish(KunenaForum::PUBLISHED));
        } finally {
            $this->replace(KunenaActivity::class, 'instance', $saved);
        }

        $this->assertSame(['onAfterDelete', 'onAfterUndelete'], $activity->calls);
    }
}
