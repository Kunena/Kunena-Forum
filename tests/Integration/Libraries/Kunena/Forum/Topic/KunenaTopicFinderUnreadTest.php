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

use Joomla\Database\QueryInterface;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;

/**
 * The filter of the Unread list (issue #6036), run on the real tables of the topics, the messages, the read times of the
 * topics and the categories of the users: it follows the rule of the "new" indicator (KunenaTopicHelper::fetchNewStatus()).
 */
class KunenaTopicFinderUnreadTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const USER = 42;

    private const OTHER_USER = 43;

    /**
     * The "mark all read" time of the session.
     */
    private const ALL_READ = 1000;

    private int $messageId = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // The finder and the user need the framework of Joomla.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }
    }

    /**
     * A topic of the category with its messages (time => hold, moved), the last post time being the time of the last message.
     *
     * @param   int    $id        The topic.
     * @param   array  $messages  time => [hold, moved].
     * @param   int    $category  The category.
     *
     * @return  void
     */
    private function topic(int $id, array $messages, int $category = 2): void
    {
        $this->insertRow('#__kunena_topics', [
            'id'             => $id,
            'category_id'    => $category,
            'subject'        => "Topic $id",
            'last_post_time' => max(array_keys($messages)),
        ]);

        foreach ($messages as $time => [$hold, $moved]) {
            $this->insertRow('#__kunena_messages', [
                'id'     => ++$this->messageId,
                'thread' => $id,
                'catid'  => $category,
                'time'   => $time,
                'hold'   => $hold,
                'moved'  => $moved,
            ]);
        }
    }

    private function read(int $topic, int $time, int $user = self::USER): void
    {
        $this->insertRow('#__kunena_user_read', [
            'user_id'     => $user,
            'topic_id'    => $topic,
            'category_id' => 2,
            'message_id'  => 0,
            'time'        => $time,
        ]);
    }

    private static function user(int $userid, bool $exists = true): KunenaUser
    {
        $reflection   = new ReflectionClass(KunenaUser::class);
        $user         = $reflection->newInstanceWithoutConstructor();
        $user->userid = $userid;
        $reflection->getProperty('_exists')->setValue($user, $exists);

        return $user;
    }

    /**
     * The query of a finder on the topics, after the unread filter.
     */
    private function query(KunenaUser $user): QueryInterface
    {
        $db         = $this->getDBDriver();
        $reflection = new ReflectionClass(KunenaTopicFinder::class);
        $finder     = $reflection->newInstanceWithoutConstructor();
        $query      = $db->createQuery()->from($db->quoteName('#__kunena_topics', 'a'));

        $reflection->getProperty('db')->setValue($finder, $db);
        $reflection->getProperty('query')->setValue($finder, $query);

        $this->assertSame($finder, $finder->filterByUserUnread($user, self::ALL_READ));

        return $reflection->getProperty('query')->getValue($finder);
    }

    /**
     * The ids of the unread topics, sorted.
     *
     * @return  int[]
     */
    private function unread(?KunenaUser $user = null, int $limit = 0, int $start = 0): array
    {
        $query = clone $this->query($user ?? self::user(self::USER));
        $query->select('a.id')->order('a.id')->setLimit($limit, $start);

        return array_map('intval', $this->getDBDriver()->setQuery($query)->loadColumn());
    }

    public function testATopicNeverOpenedWithANewMessageIsUnread(): void
    {
        $this->topic(1, [2000 => [0, 0]]);

        $this->assertSame([1], $this->unread());
    }

    public function testATopicReadAfterAllItsMessagesIsNotUnread(): void
    {
        $this->topic(1, [2000 => [0, 0], 2100 => [0, 0]]);
        $this->read(1, 2200);

        $this->assertSame([], $this->unread());
    }

    public function testANewMessageAfterTheLastVisitMakesTheTopicUnreadAgain(): void
    {
        $this->topic(1, [2000 => [0, 0], 2300 => [0, 0]]);
        $this->read(1, 2200);

        $this->assertSame([1], $this->unread());
    }

    public function testTheVisitOfAnotherUserDoesNotCount(): void
    {
        $this->topic(1, [2000 => [0, 0]]);
        $this->read(1, 2200, self::OTHER_USER);

        $this->assertSame([1], $this->unread());
    }

    public function testNewMessagesWhichAreUnapprovedOrMovedDoNotCount(): void
    {
        $this->topic(1, [500 => [0, 0], 2000 => [1, 0]]);
        $this->topic(2, [500 => [0, 0], 2000 => [0, 1]]);

        $this->assertSame([], $this->unread());
    }

    public function testATopicOlderThanTheAllReadTimeIsNotUnread(): void
    {
        $this->topic(1, [500 => [0, 0], 900 => [0, 0]]);

        $this->assertSame([], $this->unread());
    }

    public function testTheAllReadTimeOfTheCategoryHidesTheOlderTopics(): void
    {
        $this->topic(1, [2000 => [0, 0]]);
        $this->topic(2, [3000 => [0, 0]]);
        $this->insertRow('#__kunena_user_categories', ['user_id' => self::USER, 'category_id' => 2, 'allreadtime' => 2500]);

        $this->assertSame([2], $this->unread());
    }

    public function testTheAllReadTimeOfTheCategoryOfAnotherUserHidesNothing(): void
    {
        $this->topic(1, [2000 => [0, 0]]);
        $this->insertRows('#__kunena_user_categories', [
            ['user_id' => self::OTHER_USER, 'category_id' => 2, 'allreadtime' => 2500],
            ['user_id' => self::USER, 'category_id' => 2, 'allreadtime' => 0],
        ]);

        $this->assertSame([1], $this->unread());
    }

    public function testAGuestHasNoUnreadTopic(): void
    {
        $this->topic(1, [2000 => [0, 0]]);

        $this->assertSame([], $this->unread(self::user(0, false)));
    }

    public function testThePagesAndTheCountAreOnTheUnreadTopicsOnly(): void
    {
        // Topics 1 to 10, of which the even ones are read.
        for ($id = 1; $id <= 10; $id++) {
            $this->topic($id, [2000 + $id => [0, 0]]);

            if ($id % 2 === 0) {
                $this->read($id, 3000);
            }
        }

        $this->assertSame([1, 3], $this->unread(null, 2));
        $this->assertSame([5, 7], $this->unread(null, 2, 2));
        $this->assertSame([9], $this->unread(null, 2, 4));

        // The count of KunenaFinder::count(): the same query without a group, its select replaced by COUNT(*).
        $query = clone $this->query(self::user(self::USER));
        $query->select('COUNT(*)');

        $this->assertSame(5, (int) $this->getDBDriver()->setQuery($query)->loadResult());
    }
}
