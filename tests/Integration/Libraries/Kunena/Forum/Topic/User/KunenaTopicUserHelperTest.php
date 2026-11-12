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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic\User;

use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The recount of the user topics after a split (issue #2336): the reply of user B was split from topic 1 into topic 2, and his row
 * of topic 1 kept its posts, because its last post still existed (in topic 2). The post counter of B counted his reply twice.
 */
class KunenaTopicUserHelperTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const USER_A = 10;

    private const USER_B = 20;

    private function topic(int $id, int $firstPostId): void
    {
        $this->insertRow('#__kunena_topics', ['id' => $id, 'category_id' => 2, 'subject' => "Topic $id", 'first_post_id' => $firstPostId]);
    }

    private function message(int $id, int $thread, int $parent, int $userid): void
    {
        $this->insertRow('#__kunena_messages', ['id' => $id, 'parent' => $parent, 'thread' => $thread, 'catid' => 2, 'userid' => $userid, 'hold' => 0]);
    }

    /**
     * The state after the split of the reply of B into topic 2, before the recount: the row of B for topic 1 is the one of before
     * the split.
     */
    private function splitReply(array $staleRow = []): void
    {
        $this->topic(1, 1);
        $this->topic(2, 2);
        $this->message(1, 1, 0, self::USER_A);
        $this->message(2, 2, 0, self::USER_B);

        $this->insertRow('#__kunena_user_topics', ['user_id' => self::USER_A, 'topic_id' => 1, 'category_id' => 2, 'posts' => 1, 'last_post_id' => 1, 'owner' => 1, 'params' => '']);
        $this->insertRow('#__kunena_user_topics', $staleRow + ['user_id' => self::USER_B, 'topic_id' => 1, 'category_id' => 2, 'posts' => 1, 'last_post_id' => 2, 'owner' => 0, 'params' => '']);
    }

    /**
     * @return  array  The rows of the user, by topic.
     */
    private function rows(int $user): array
    {
        $rows = [];

        foreach ($this->fetchAll('SELECT * FROM #__kunena_user_topics WHERE user_id = ' . $user) as $row) {
            $rows[(int) $row['topic_id']] = $row;
        }

        return $rows;
    }

    private function assertRepaired(): void
    {
        $b = $this->rows(self::USER_B);

        $this->assertSame([2], array_keys(array_filter($b, static fn ($row) => (int) $row['posts'] > 0)));
        $this->assertSame(1, (int) $b[2]['posts']);
        $this->assertSame(1, array_sum(array_map(static fn ($row) => (int) $row['posts'], $b)));

        $a = $this->rows(self::USER_A);

        $this->assertSame(1, (int) $a[1]['posts']);
        $this->assertSame(1, (int) $a[1]['owner']);
    }

    public function testTheRowOfTheOldTopicIsDeletedAfterASplit(): void
    {
        $this->splitReply();

        KunenaTopicUserHelper::recountRows($this->getDBDriver(), [1, 2]);

        $this->assertRepaired();
        $this->assertArrayNotHasKey(1, $this->rows(self::USER_B));
    }

    public function testASubscriptionKeepsTheResetRow(): void
    {
        $this->splitReply(['subscribed' => 1]);

        KunenaTopicUserHelper::recountRows($this->getDBDriver(), [1, 2]);

        $this->assertRepaired();

        $row = $this->rows(self::USER_B)[1];

        $this->assertSame([0, 0, 0, 1, 0], [(int) $row['posts'], (int) $row['last_post_id'], (int) $row['owner'], (int) $row['subscribed'], (int) $row['favorite']]);
    }

    public function testAFavoriteKeepsTheResetRow(): void
    {
        $this->splitReply(['favorite' => 1]);

        KunenaTopicUserHelper::recountRows($this->getDBDriver(), [1, 2]);

        $this->assertRepaired();

        $row = $this->rows(self::USER_B)[1];

        $this->assertSame([0, 0, 0, 0, 1], [(int) $row['posts'], (int) $row['last_post_id'], (int) $row['owner'], (int) $row['subscribed'], (int) $row['favorite']]);
    }

    public function testTheFullRecountRepairsIt(): void
    {
        $this->splitReply();

        // Tools > Recount statistics: no topic ids.
        KunenaTopicUserHelper::recountRows($this->getDBDriver(), false);

        $this->assertRepaired();
        $this->assertArrayNotHasKey(1, $this->rows(self::USER_B));
    }
}
