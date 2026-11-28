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
 * Moving a topic which waits for approval (or is deleted) changes no count of the categories: they count the published topics only.
 * The move of a whole topic took its topic and posts from the old category and gave them to the new one, and the merge into another
 * topic added its posts to that topic, with its last message (waiting for approval) as the last post of the topic.
 */
class KunenaTopicMoveUnpublishedTest extends IntegrationTestCase implements DBTestInterface
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
        // The refusals of the move are texts with their values (Text::sprintf()).
        $language->method('_')->willReturnArgument(0);
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

    /**
     * The counts of the categories: [numTopics, numPosts] of each.
     */
    private function counts(): array
    {
        return array_map(
            static fn ($row) => array_map('intval', array_values($row)),
            $this->fetchAll('SELECT numTopics, numPosts FROM #__kunena_categories WHERE id IN (2, 3) ORDER BY id')
        );
    }

    public function testMovingAnUnapprovedTopicToAnotherCategoryChangesNoCount(): void
    {
        KunenaTopicHelper::get(self::UNAPPROVED)->move(KunenaCategoryHelper::get(self::OTHER_CATEGORY));

        $this->assertSame([[1, 2], [0, 0]], $this->counts());
    }

    /**
     * A topic is not merged into the shadow of a moved topic (the move with "targetid" takes any topic): the shadow shows the topic it
     * points to, so the messages moved into it were seen nowhere.
     */
    public function testATopicIsNotMergedIntoAShadow(): void
    {
        $this->insertRow('#__kunena_topics', ['id' => 7, 'category_id' => self::CATEGORY, 'subject' => 'Shadow', 'hold' => 0, 'moved_id' => self::TARGET]);

        try {
            KunenaTopicHelper::get(self::UNAPPROVED)->move(KunenaTopicHelper::get(7));
            $refused = false;
        } catch (\Exception $e) {
            $refused = true;
        }

        $this->assertSame(
            ['refused' => true, 'thread' => self::UNAPPROVED],
            ['refused' => $refused, 'thread' => (int) $this->fetchAll('SELECT thread FROM #__kunena_messages WHERE id = 10')[0]['thread']]
        );
    }

    public function testMergingAnUnapprovedTopicChangesNoPostsOfTheTopicNorOfTheCategory(): void
    {
        KunenaTopicHelper::get(self::UNAPPROVED)->move(KunenaTopicHelper::get(self::TARGET));

        $this->assertSame(
            [['posts' => 2, 'last_post_id' => 2]],
            array_map(static fn ($row) => array_map('intval', $row), $this->fetchAll('SELECT posts, last_post_id FROM #__kunena_topics WHERE id = ' . self::TARGET))
        );
        $this->assertSame([[1, 2], [0, 0]], $this->counts());
    }

    /**
     * A topic moved into a category of one language takes that language (as a new topic of it does): it kept its own, and the
     * lists of the category, which show the topics of the language of the page, left it out on every page.
     */
    public function testATopicMovedIntoACategoryOfOneLanguageTakesIt(): void
    {
        $category           = KunenaCategoryHelper::get(self::OTHER_CATEGORY);
        $category->language = 'fr-FR';

        KunenaTopicHelper::get(self::TARGET)->move($category);

        $this->assertSame('fr-FR', $this->fetchAll('SELECT language FROM #__kunena_topics WHERE id = ' . self::TARGET)[0]['language']);
    }

    /**
     * A published topic is not merged into a topic which waits for approval: its messages were hidden with that topic, while the
     * category counted them (and counted them again when the topic was approved).
     */
    public function testAPublishedTopicIsNotMergedIntoATopicWhichWaitsForApproval(): void
    {
        try {
            KunenaTopicHelper::get(self::TARGET)->move(KunenaTopicHelper::get(self::UNAPPROVED));
            $refused = false;
        } catch (\Exception $e) {
            $refused = true;
        }

        $this->assertSame(
            ['refused' => true, 'threads' => [self::TARGET, self::TARGET]],
            ['refused' => $refused, 'threads' => array_map('intval', array_column($this->fetchAll('SELECT thread FROM #__kunena_messages WHERE id IN (1, 2) ORDER BY id'), 'thread'))]
        );
        $this->assertSame([[1, 2], [0, 0]], $this->counts());
    }

    /**
     * The read marks of the users follow a topic which moves to another category: they kept the old category, so the topic was not
     * in the unread topics of the new category (Diagnostics listed them as "user read in the wrong category").
     */
    public function testTheReadMarksOfAMovedTopicGetItsNewCategory(): void
    {
        $this->insertRows('#__kunena_user_read', [
            ['user_id' => 42, 'topic_id' => self::TARGET, 'category_id' => self::CATEGORY, 'message_id' => 2, 'time' => 1100],
            ['user_id' => 43, 'topic_id' => self::TARGET, 'category_id' => self::CATEGORY, 'message_id' => 1, 'time' => 1000],
        ]);

        KunenaTopicHelper::get(self::TARGET)->move(KunenaCategoryHelper::get(self::OTHER_CATEGORY));

        $this->assertSame(
            [[42, self::TARGET, self::OTHER_CATEGORY], [43, self::TARGET, self::OTHER_CATEGORY]],
            $this->readMarks()
        );
    }

    /**
     * The read marks of a topic which is merged into another one go to that topic: they stayed on the shadow. A user who read both
     * keeps the earlier of his two marks. A user who read only the merged topic has not read the messages of the target: his mark
     * is before its first message (1000).
     */
    public function testTheReadMarksOfAMergedTopicGoToTheTarget(): void
    {
        $this->insertRows('#__kunena_user_read', [
            ['user_id' => 42, 'topic_id' => self::UNAPPROVED, 'category_id' => self::CATEGORY, 'message_id' => 10, 'time' => 1500],
            ['user_id' => 43, 'topic_id' => self::UNAPPROVED, 'category_id' => self::CATEGORY, 'message_id' => 10, 'time' => 1500],
            ['user_id' => 43, 'topic_id' => self::TARGET, 'category_id' => self::CATEGORY, 'message_id' => 2, 'time' => 1100],
        ]);

        KunenaTopicHelper::get(self::UNAPPROVED)->move(KunenaTopicHelper::get(self::TARGET));

        $this->assertSame(
            [[42, self::TARGET, self::CATEGORY, 10, 999], [43, self::TARGET, self::CATEGORY, 2, 1100]],
            $this->readMarks(true)
        );
    }

    /**
     * The read marks: user, topic, category (and the message and the time).
     */
    private function readMarks(bool $all = false): array
    {
        return array_map(
            static fn ($row) => array_map('intval', array_values($row)),
            $this->fetchAll('SELECT user_id, topic_id, category_id' . ($all ? ', message_id, time' : '') . ' FROM #__kunena_user_read ORDER BY user_id, topic_id')
        );
    }
}
