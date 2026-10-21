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

namespace Kunena\Tests\Integration\Libraries\Kunena\Notification;

use Kunena\Forum\Libraries\Notification\KunenaPopupNotification;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The query which finds the messages a user gets a popup for, run on the real tables of the schema.
 */
class PopupCandidatesTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const USER = 42;

    private const NOW = 1800000000;

    private function topic(int $id, int $firstUser): void
    {
        $this->insertRow('#__kunena_topics', ['id' => $id, 'category_id' => 2, 'subject' => "Topic $id", 'first_post_userid' => $firstUser]);
    }

    private function message(int $id, int $thread, int $parent, int $userid, string $text = 'text', int $hold = 0, int $age = 60): void
    {
        $this->insertRow('#__kunena_messages', ['id' => $id, 'parent' => $parent, 'thread' => $thread, 'catid' => 2, 'userid' => $userid, 'time' => self::NOW - $age, 'hold' => $hold]);
        $this->insertRow('#__kunena_messages_text', ['mesid' => $id, 'message' => $text]);
    }

    /**
     * @return  string[]  The id of each message found, with its reason.
     */
    private function found(int $after = 0, int $cursor = 100): array
    {
        $found = [];

        foreach (KunenaPopupNotification::candidates($this->getDBDriver(), self::USER, $after, $cursor, self::NOW) as $row) {
            $found[(int) $row->id] = KunenaPopupNotification::reason($row);
        }

        return $found;
    }

    public function testAReplyInATopicOfTheUserIsFound(): void
    {
        $this->topic(1, self::USER);
        $this->message(1, 1, 0, self::USER);
        $this->message(2, 1, 1, 7);

        $this->assertSame([2 => 'reply'], $this->found());
    }

    public function testAMentionIsFoundAndBeatsTheOtherReasons(): void
    {
        $this->topic(1, self::USER);
        $this->message(1, 1, 0, self::USER);
        $this->message(2, 1, 1, 7, 'hello [mention userid=' . self::USER . ']x[/mention]');

        $this->assertSame([2 => 'mention'], $this->found());
    }

    public function testAMentionOfAnotherUserIsNotFound(): void
    {
        $this->topic(1, 9);
        $this->message(1, 1, 0, 9);
        $this->message(2, 1, 1, 7, '[mention userid=' . self::USER . '0]x[/mention]');

        $this->assertSame([], $this->found());
    }

    public function testAReplyInASubscribedTopicIsFound(): void
    {
        $this->topic(1, 9);
        $this->message(1, 1, 0, 9);
        $this->message(2, 1, 1, 7);
        $this->insertRow('#__kunena_user_topics', ['user_id' => self::USER, 'topic_id' => 1, 'subscribed' => 1]);

        // Every message of a followed topic counts, the first one too.
        $this->assertSame([1 => 'subscribed', 2 => 'subscribed'], $this->found());
    }

    public function testANewTopicInASubscribedCategoryIsFoundButItsRepliesAreNot(): void
    {
        $this->topic(1, 9);
        $this->message(1, 1, 0, 9);
        $this->message(2, 1, 1, 7);
        $this->insertRow('#__kunena_user_categories', ['user_id' => self::USER, 'category_id' => 2, 'subscribed' => 1]);

        $this->assertSame([1 => 'topic'], $this->found());
    }

    public function testOwnHeldAndOldMessagesAreSkippedAndTheCursorLimitsTheRange(): void
    {
        $this->topic(1, self::USER);
        $this->message(1, 1, 0, self::USER);
        $this->message(2, 1, 1, self::USER);          // His own message.
        $this->message(3, 1, 1, 7, 'text', 1);        // Waiting for approval.
        $this->message(4, 1, 1, 7, 'text', 0, 90000); // More than a day old.
        $this->message(5, 1, 1, 7);                   // A real reply.
        $this->message(6, 1, 1, 7);                   // Another one.

        // Between the last message the browser saw (4) and the cursor (5) only message 5 is new.
        $this->assertSame([5 => 'reply'], $this->found(4, 5));
        // Messages 2, 3 and 4 never count; 1 is his own.
        $this->assertSame([5 => 'reply', 6 => 'reply'], $this->found());
    }
}
