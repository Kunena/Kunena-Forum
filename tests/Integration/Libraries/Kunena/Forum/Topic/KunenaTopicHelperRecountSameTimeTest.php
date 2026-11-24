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
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use RuntimeException;

/**
 * The recount of the topics takes the counts of the messages in the state of the topic. It grouped the messages by topic and state and
 * joined the groups by the topic only: the group of the deleted messages was taken too when its first or last time was the time of a
 * published message, and the topic got the number of posts and files of its deleted messages.
 */
class KunenaTopicHelperRecountSameTimeTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    private const TOPIC = 1;

    /**
     * The objects of Joomla which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        // The helper needs the framework of Joomla.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The helper reads the database of the container of Joomla: that is the database of the tests here. When a query fails,
        // the helper gives the error to the application, an administrator one, which throws it: the test fails with the error of
        // the database.
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'administrator');
        $application->method('enqueueMessage')->willReturnCallback(static function ($message) {
            throw new RuntimeException($message);
        });

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        $this->insertRow('#__kunena_categories', ['id' => self::CATEGORY, 'parentid' => 0, 'name' => 'Category', 'alias' => '2-category', 'published' => 1]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
        }

        parent::tearDown();
    }

    public function testThePostsAreTheMessagesInTheStateOfTheTopic(): void
    {
        // A published topic with two published messages, and a deleted reply written in the same second as the first one: the group of
        // the deleted messages has that time as its first and its last time.
        $this->insertRow('#__kunena_topics', ['id' => self::TOPIC, 'category_id' => self::CATEGORY, 'subject' => 'Topic', 'hold' => 0, 'posts' => 0]);
        $this->insertRows('#__kunena_messages', [
            ['id' => 3, 'parent' => 1, 'thread' => self::TOPIC, 'catid' => self::CATEGORY, 'subject' => 'Topic', 'time' => 1000, 'hold' => 2, 'userid' => 43],
            ['id' => 1, 'parent' => 0, 'thread' => self::TOPIC, 'catid' => self::CATEGORY, 'subject' => 'Topic', 'time' => 1000, 'hold' => 0, 'userid' => 42],
            ['id' => 2, 'parent' => 1, 'thread' => self::TOPIC, 'catid' => self::CATEGORY, 'subject' => 'Topic', 'time' => 1100, 'hold' => 0, 'userid' => 42],
        ]);
        $this->insertRows('#__kunena_messages_text', [
            ['mesid' => 1, 'message' => 'First'],
            ['mesid' => 2, 'message' => 'Reply'],
            ['mesid' => 3, 'message' => 'Deleted'],
        ]);

        KunenaTopicHelper::recount();

        $this->assertSame(
            [['hold' => 0, 'posts' => 2, 'first_post_id' => 1, 'last_post_id' => 2]],
            array_map(static fn ($row) => array_map('intval', $row), $this->fetchAll('SELECT hold, posts, first_post_id, last_post_id FROM #__kunena_topics WHERE id = ' . self::TOPIC))
        );
    }
}
