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

use Joomla\CMS\Application\ConsoleApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageSchedule;
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
 * A scheduled reply (issue #9830) is published by the scheduler task when its time has come, as if its author posted it then: it is
 * counted once and becomes the last post of the topic. A reply held for review, in a locked topic or by a banned author is not
 * published: its schedule is cleared, it waits for a moderator.
 */
class KunenaMessageScheduleTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    private const TOPIC = 1;

    private const AUTHOR = 42;

    private const FIRST = 10;

    private const REPLY = 11;

    /**
     * The time of the run of the task (in the past of the real clock: a message is not saved with a time in the future).
     */
    private const NOW = 1_700_000_000;

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

        // As in KunenaMessageApproveCountTest: the library reads the database of the tests through the container of Joomla, the
        // application is a command line one without plugins and without a logged-in user, the configuration has the settings which
        // the code reads. The e-mails are off (mailonline): the notification stops before it queues anything.
        $driver      = $this->getDBDriver();
        $dispatcher  = new Dispatcher();
        $container   = new Container();
        $userFactory = $this->createStub(UserFactoryInterface::class);
        $userFactory->method('loadUserById')->willReturnCallback(static function (int $id): User {
            $user     = new User();
            $user->id = $id;

            return $user;
        });
        $joomlaConfig = new Registry(['live_site' => 'https://forum.example', 'mailonline' => 0]);
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));
        $container->share(UserFactoryInterface::class, static fn () => $userFactory);
        $container->share('config', static fn () => $joomlaConfig);
        $application = $this->createStub(ConsoleApplication::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');
        $application->method('getConfig')->willReturn($joomlaConfig);
        $language = $this->createStub(Language::class);
        $language->method('transliterate')->willReturnArgument(0);
        $language->method('getTag')->willReturn('en-GB');

        // askEmail is on: nobody is logged in on the command line, the publication must not ask for the e-mail address.
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, [
            'threadsPerPage'        => 20,
            'ratingEnabled'         => 0,
            'readOnly'              => 0,
            'askEmail'              => 1,
            'floodProtection'       => 0,
            'attachmentLimit'       => 0,
            'showNew'               => 1,
            'pubWrite'              => 0,
            'sendEmails'            => 1,
            'allowSubscriptions'    => 1,
            'categorySubscriptions' => 'post',
            'topicSubscriptions'    => 'every',
            'mailModerators'        => 0,
            'mailAdministrators'    => 0,
        ]);

        // The author may read the category; he is no moderator.
        $access = $this->createStub(KunenaAccess::class);
        $access->method('getAllowedCategories')->willReturn([self::CATEGORY => self::CATEGORY]);
        $access->method('getAllowedHold')->willReturn([0]);
        $access->method('getSubscribers')->willReturn([]);

        $this->saved = [
            'categories'  => $this->replace(KunenaCategoryHelper::class, '_instances', []),
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'access'      => $this->replace(KunenaAccess::class, 'instance', $access),
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
            'uriBase'     => $this->replace(Uri::class, 'base', []),
            'uriRoot'     => $this->replace(Uri::class, 'root', []),
            'uris'        => $this->replace(Uri::class, 'instances', []),
            'host'        => $_SERVER['HTTP_HOST'] ?? null,
        ];

        // The console application of Joomla gives the host of the site to the request (populateHttpHost()).
        $_SERVER['HTTP_HOST'] = 'forum.example';

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        // The task runs as a guest of the command line.
        $this->saved['me']    = $this->replace(KunenaUserHelper::class, '_me', new KunenaUser(0));
        $this->saved['users'] = $this->replace(KunenaUserHelper::class, '_instances', []);

        $columns = array_column($this->fetchAll('SHOW COLUMNS FROM #__users'), 'Field');

        foreach (['registerDate' => 'datetime NOT NULL', 'lastvisitDate' => 'datetime NULL'] as $column => $type) {
            if (!\in_array($column, $columns, true)) {
                $driver->setQuery("ALTER TABLE #__users ADD $column $type")->execute();
                $this->userColumns[] = $column;
            }
        }

        $driver->setQuery("ALTER TABLE #__kunena_messages ADD deleted_time int(11) NOT NULL default '0' AFTER moved")->execute();
        $this->deletedTime = true;

        $this->insertRow('#__users', ['id' => self::AUTHOR, 'name' => 'Author', 'username' => 'author', 'params' => '', 'registerDate' => '2020-01-01 00:00:00']);

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
            $this->replace(Uri::class, 'base', $this->saved['uriBase']);
            $this->replace(Uri::class, 'root', $this->saved['uriRoot']);
            $this->replace(Uri::class, 'instances', $this->saved['uris']);

            if ($this->saved['host'] === null) {
                unset($_SERVER['HTTP_HOST']);
            } else {
                $_SERVER['HTTP_HOST'] = $this->saved['host'];
            }

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
     * A published topic (in a category, not a section) with its published first post (time 1000) by the author, and his scheduled
     * reply (posted at 1100).
     */
    private function seed(int $scheduledTime, int $scheduledReview = 0, int $locked = 0, ?string $banned = null): void
    {
        $this->insertRow('#__kunena_users', ['userid' => self::AUTHOR, 'posts' => 1, 'banned' => $banned]);
        $this->insertRow('#__kunena_categories', ['id' => 1, 'parentid' => 0, 'name' => 'Section', 'alias' => '1-section', 'published' => 1]);
        $this->insertRow('#__kunena_categories', [
            'id'        => self::CATEGORY,
            'parentid'  => 1,
            'name'      => 'Category',
            'alias'     => '2-category',
            'published' => 1,
            'numTopics' => 1,
            'numPosts'  => 1,
        ]);
        $this->insertRow('#__kunena_aliases', ['alias' => '2-category', 'type' => 'catid', 'item' => self::CATEGORY]);
        $this->insertRow('#__kunena_topics', [
            'id'                => self::TOPIC,
            'category_id'       => self::CATEGORY,
            'subject'           => 'Topic',
            'hold'              => 0,
            'locked'            => $locked,
            'posts'             => 1,
            'first_post_id'     => self::FIRST,
            'first_post_time'   => 1000,
            'first_post_userid' => self::AUTHOR,
            'last_post_id'      => self::FIRST,
            'last_post_time'    => 1000,
            'last_post_userid'  => self::AUTHOR,
        ]);
        $this->insertRow('#__kunena_user_topics', ['user_id' => self::AUTHOR, 'topic_id' => self::TOPIC, 'category_id' => self::CATEGORY, 'posts' => 1, 'last_post_id' => self::FIRST, 'owner' => 1]);

        foreach ([[self::FIRST, 0, KunenaForum::PUBLISHED, 1000, 0, 0], [self::REPLY, self::FIRST, KunenaForum::UNAPPROVED, 1100, $scheduledTime, $scheduledReview]] as [$id, $parent, $hold, $time, $scheduled, $review]) {
            $this->insertRow('#__kunena_messages', [
                'id'               => $id,
                'parent'           => $parent,
                'thread'           => self::TOPIC,
                'catid'            => self::CATEGORY,
                'userid'           => self::AUTHOR,
                'subject'          => 'Topic',
                'time'             => $time,
                'hold'             => $hold,
                'scheduled_time'   => $scheduled,
                'scheduled_review' => $review,
            ]);
            $this->insertRow('#__kunena_messages_text', ['mesid' => $id, 'message' => "Message $id"]);
        }
    }

    /**
     * The reply, the counters and the last post of the topic, as they are in the database.
     */
    private function state(): array
    {
        return [
            'reply'     => $this->fetchAll('SELECT hold, time, scheduled_time, scheduled_review FROM #__kunena_messages WHERE id = ' . self::REPLY),
            'topic'     => $this->fetchAll('SELECT posts, last_post_id, last_post_time FROM #__kunena_topics WHERE id = ' . self::TOPIC),
            'category'  => $this->fetchAll('SELECT numPosts FROM #__kunena_categories WHERE id = ' . self::CATEGORY),
            'usertopic' => $this->fetchAll('SELECT posts FROM #__kunena_user_topics WHERE user_id = ' . self::AUTHOR . ' AND topic_id = ' . self::TOPIC),
            'user'      => $this->fetchAll('SELECT posts FROM #__kunena_users WHERE userid = ' . self::AUTHOR),
        ];
    }

    /**
     * The state of the seed: nothing was published.
     */
    private function unpublished(int $scheduledTime, int $scheduledReview): array
    {
        return [
            'reply'     => [['hold' => 1, 'time' => 1100, 'scheduled_time' => $scheduledTime, 'scheduled_review' => $scheduledReview]],
            'topic'     => [['posts' => 1, 'last_post_id' => self::FIRST, 'last_post_time' => 1000]],
            'category'  => [['numPosts' => 1]],
            'usertopic' => [['posts' => 1]],
            'user'      => [['posts' => 1]],
        ];
    }

    public function testADueReplyIsPublishedAtItsTimeAndCountedOnce(): void
    {
        $scheduled = self::NOW - 60;
        $this->seed($scheduled);
        $me = KunenaUserHelper::getMyself();

        $this->assertSame(['published' => 1, 'skipped' => 0], KunenaMessageSchedule::publishDue(self::NOW));

        $this->assertEquals([
            'reply'     => [['hold' => 0, 'time' => $scheduled, 'scheduled_time' => 0, 'scheduled_review' => 0]],
            'topic'     => [['posts' => 2, 'last_post_id' => self::REPLY, 'last_post_time' => $scheduled]],
            'category'  => [['numPosts' => 2]],
            'usertopic' => [['posts' => 2]],
            'user'      => [['posts' => 2]],
        ], $this->state());

        // The user who ran the task is the current user again.
        $this->assertSame($me, KunenaUserHelper::getMyself());
    }

    public function testAReplyWhichIsNotDueIsLeftAlone(): void
    {
        $this->seed(self::NOW + 3600);

        $this->assertSame(['published' => 0, 'skipped' => 0], KunenaMessageSchedule::publishDue(self::NOW));

        $this->assertEquals($this->unpublished(self::NOW + 3600, 0), $this->state());
    }

    public function testAReplyHeldForReviewStaysForTheModerators(): void
    {
        $this->seed(self::NOW - 60, 1);

        $this->assertSame(['published' => 0, 'skipped' => 1], KunenaMessageSchedule::publishDue(self::NOW));

        $this->assertEquals($this->unpublished(0, 0), $this->state());
    }

    public function testAReplyInALockedTopicIsNotPublished(): void
    {
        $this->seed(self::NOW - 60, 0, 1);

        $this->assertSame(['published' => 0, 'skipped' => 1], KunenaMessageSchedule::publishDue(self::NOW));

        $this->assertEquals($this->unpublished(0, 0), $this->state());
    }

    public function testAReplyOfABannedAuthorIsNotPublished(): void
    {
        $this->seed(self::NOW - 60, 0, 0, '9999-12-31 23:59:59');

        $this->assertSame(['published' => 0, 'skipped' => 1], KunenaMessageSchedule::publishDue(self::NOW));

        $this->assertEquals($this->unpublished(0, 0), $this->state());
    }

    public function testApprovingAScheduledReplyEarlyClearsItsSchedule(): void
    {
        $this->seed(self::NOW + 3600);

        // A logged-in user approves it (only a guest is asked for his e-mail address).
        KunenaUserHelper::setMyself(KunenaUserHelper::get(self::AUTHOR));

        $this->assertTrue(KunenaMessageHelper::get(self::REPLY)->publish(KunenaForum::PUBLISHED));

        $this->assertEquals([['hold' => 0, 'time' => 1100, 'scheduled_time' => 0, 'scheduled_review' => 0]], $this->state()['reply']);

        // Nothing is left for the task.
        $this->assertSame(['published' => 0, 'skipped' => 0], KunenaMessageSchedule::publishDue(self::NOW + 7200));
    }
}
