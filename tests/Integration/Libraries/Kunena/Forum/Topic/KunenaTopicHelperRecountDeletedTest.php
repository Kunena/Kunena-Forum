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
 * The recount of the topics and a deleted topic with a reply which waits for approval: deleting the topic leaves the reply waiting
 * (it comes back with the topic). The recount made the deleted topic "waiting for approval" again, with that reply as its first post
 * (its author could open the topic again).
 */
class KunenaTopicHelperRecountDeletedTest extends IntegrationTestCase implements DBTestInterface
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

        // The topic was deleted by a moderator (its messages too); the reply of another member still waits for approval.
        $this->insertRow('#__kunena_topics', ['id' => self::TOPIC, 'category_id' => self::CATEGORY, 'subject' => 'Deleted', 'hold' => 3, 'posts' => 1,
            'first_post_id' => 1, 'first_post_userid' => 42, 'last_post_id' => 1, 'last_post_userid' => 42]);
        $this->insertRows('#__kunena_messages', [
            ['id' => 1, 'parent' => 0, 'thread' => self::TOPIC, 'catid' => self::CATEGORY, 'subject' => 'Deleted', 'time' => 1000, 'hold' => 3, 'userid' => 42],
            ['id' => 2, 'parent' => 1, 'thread' => self::TOPIC, 'catid' => self::CATEGORY, 'subject' => 'Deleted', 'time' => 1100, 'hold' => 1, 'userid' => 43],
        ]);
        $this->insertRows('#__kunena_messages_text', [
            ['mesid' => 1, 'message' => 'The first post.'],
            ['mesid' => 2, 'message' => 'A reply which waits.'],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
        }

        parent::tearDown();
    }

    public function testTheRecountLeavesTheDeletedTopicDeleted(): void
    {
        KunenaTopicHelper::recount();

        $this->assertSame([['hold' => 3, 'first_post_id' => 1]], $this->topic(self::TOPIC));
    }

    /**
     * The state and the first post of a topic, as they are in the database.
     */
    private function topic(int $id): array
    {
        return array_map(static fn ($row) => array_map('intval', $row), $this->fetchAll('SELECT hold, first_post_id FROM #__kunena_topics WHERE id = ' . $id));
    }
}
