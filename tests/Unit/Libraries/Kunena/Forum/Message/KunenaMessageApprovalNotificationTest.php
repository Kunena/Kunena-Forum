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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Message;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The approval of a message: the subscribers get the normal heading, the author alone gets one "approved" e-mail.
 */
class KunenaMessageApprovalNotificationTest extends UnitTestCase
{
    public static function headings(): array
    {
        return [
            'a new topic for a subscriber'         => [false, false, 'COM_KUNENA_POST_EMAIL_NOTIFICATION1_CAT'],
            'a reply for a subscriber'             => [true, false, 'COM_KUNENA_POST_EMAIL_NOTIFICATION1'],
            'an approved new topic for the author' => [false, true, 'COM_KUNENA_POST_EMAIL_NOTIFICATION_APPROVED_MESSAGE'],
            'an approved reply for the author'     => [true, true, 'COM_KUNENA_POST_EMAIL_NOTIFICATION_APPROVED_MESSAGE'],
        ];
    }

    #[DataProvider('headings')]
    public function testSubscriptionHeadingKey(bool $isReply, bool $approvedAuthor, string $expected): void
    {
        $this->assertSame($expected, KunenaMessage::subscriptionHeadingKey($isReply, $approvedAuthor));
    }

    public static function authors(): array
    {
        return [
            'a valid author'                   => [5, 2, true, false, 'author@example.org', true, true],
            'a guest'                          => [0, 2, true, false, 'author@example.org', true, false],
            'a missing user'                   => [5, 2, false, false, 'author@example.org', true, false],
            'a blocked user'                   => [5, 2, true, true, 'author@example.org', true, false],
            'an empty address'                 => [5, 2, true, false, '', true, false],
            'an invalid address'               => [5, 2, true, false, 'not-an-address', true, false],
            'the author is the approver'       => [5, 5, true, false, 'author@example.org', true, false],
            'the author may not read the post' => [5, 2, true, false, 'author@example.org', false, false],
        ];
    }

    #[DataProvider('authors')]
    public function testAuthorGetsApprovalEmail(int $authorId, int $approverId, bool $exists, bool $blocked, string $email, bool $mayRead, bool $expected): void
    {
        $this->requireJoomla();

        $this->assertSame($expected, KunenaMessage::authorGetsApprovalEmail($authorId, $approverId, $exists, $blocked, $email, $mayRead));
    }

    public function testTheAuthorIsAddedToTheSubscribers(): void
    {
        $first  = $this->subscriber(7, 'first@example.org');
        $second = $this->subscriber(8, 'second@example.org');

        $list = KunenaMessage::addApprovedAuthor([$first, $second], $this->author());

        $this->assertCount(3, $list);
        $this->assertSame([$first, $second], \array_slice($list, 0, 2));
        $this->assertEquals($this->subscriber(7, 'first@example.org'), $list[0]);
        $this->assertEquals($this->subscriber(8, 'second@example.org'), $list[1]);
        $this->assertFlaggedAuthor($list[2]);
        $this->assertCount(1, array_filter($list, static fn ($emailTo) => !empty($emailTo->approvedAuthor)));
    }

    public function testAnAuthorWhoIsASubscriberIsAddedOnce(): void
    {
        $list = KunenaMessage::addApprovedAuthor([$this->subscriber(5, 'author@example.org'), $this->subscriber(7, 'first@example.org')], $this->author());

        $authorEntries = array_values(array_filter($list, static fn ($emailTo) => (int) $emailTo->id === 5));

        $this->assertCount(2, $list);
        $this->assertCount(1, $authorEntries);
        $this->assertFlaggedAuthor($authorEntries[0]);
    }

    public function testTheAuthorAloneOnAnEmptyList(): void
    {
        $list = KunenaMessage::addApprovedAuthor([], $this->author());

        $this->assertCount(1, $list);
        $this->assertFlaggedAuthor($list[0]);
    }

    public function testTheFlagSurvivesTheQueue(): void
    {
        $list = KunenaMessage::addApprovedAuthor([$this->subscriber(7, 'first@example.org')], $this->author());

        $queued = json_decode(json_encode($list));

        $this->assertCount(2, $queued);
        $this->assertTrue(empty($queued[0]->approvedAuthor));
        $this->assertFlaggedAuthor($queued[1]);
    }

    private function subscriber(int $id, string $email): object
    {
        return (object) ['id' => $id, 'email' => $email, 'subscription' => 1, 'moderator' => 0, 'admin' => 0];
    }

    private function author(): object
    {
        return (object) ['userid' => 5, 'email' => 'author@example.org'];
    }

    private function assertFlaggedAuthor(object $entry): void
    {
        $this->assertSame(5, $entry->id);
        $this->assertSame('author@example.org', $entry->email);
        $this->assertSame(0, $entry->subscription);
        $this->assertSame(0, $entry->moderator);
        $this->assertSame(0, $entry->admin);
        $this->assertSame(1, $entry->approvedAuthor);
    }
}
