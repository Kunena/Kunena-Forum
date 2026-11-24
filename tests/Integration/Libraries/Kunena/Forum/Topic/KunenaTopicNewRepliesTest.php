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

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicNewReplies;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The new replies of a topic on the real table of the messages: only the published replies of the topic after the given message,
 * not the ones of the user who reads, with the first one.
 */
class KunenaTopicNewRepliesTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // The container of Joomla gives the database of the tests to the class.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        // The topic 1: its first message 10 and the replies 11 to 15; the topic 2 has the message 16.
        $rows = [];

        foreach ([10 => [1, 7, 0], 11 => [1, 8, 0], 12 => [1, 42, 0], 13 => [1, 8, 1], 14 => [1, 9, 0], 15 => [1, 8, 2], 16 => [2, 8, 0]] as $id => [$thread, $userid, $hold]) {
            $rows[] = ['id' => $id, 'parent' => 0, 'thread' => $thread, 'catid' => 2, 'name' => 'U' . $userid, 'userid' => $userid,
                'email' => '', 'subject' => 'S', 'time' => $id, 'ip' => '', 'hold' => $hold];
        }

        $this->insertRows('#__kunena_messages', $rows);
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testThePublishedRepliesOfTheOtherUsersAfterTheMessageCount(): void
    {
        // 11 (user 8) and 14 (user 9): 12 is of the reader, 13 waits for approval, 15 is deleted, 16 is in another topic.
        $this->assertSame(['count' => 2, 'first' => 11], KunenaTopicNewReplies::find(1, 10, 42));
    }

    public function testOnlyTheRepliesAfterTheMessage(): void
    {
        $this->assertSame(['count' => 1, 'first' => 14], KunenaTopicNewReplies::find(1, 12, 42));
    }

    public function testForAGuestEveryPublishedReplyCounts(): void
    {
        $this->assertSame(['count' => 3, 'first' => 11], KunenaTopicNewReplies::find(1, 10, 0));
    }

    public function testNoNewReply(): void
    {
        $this->assertSame(['count' => 0, 'first' => 0], KunenaTopicNewReplies::find(1, 16, 42));
        $this->assertSame(['count' => 0, 'first' => 0], KunenaTopicNewReplies::find(0, 10, 42));
    }

    /**
     * A scheduled reply keeps the id it got when it was written and is published later, with the time of its publication: it came
     * after the last message of the page, but with a lower id, and was never announced.
     */
    public function testAScheduledReplyPublishedAfterTheMessageCounts(): void
    {
        $this->insertRow('#__kunena_messages', ['id' => 5, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'name' => 'U8', 'userid' => 8, 'email' => '', 'subject' => 'S', 'time' => 100, 'ip' => '', 'hold' => 0]);

        $this->assertSame(['count' => 1, 'first' => 5], KunenaTopicNewReplies::find(1, 14, 42));
    }
}
