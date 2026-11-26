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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The repairs of the diagnostics of the backend on the real tables:
 * - an upload which waits for its message (mesid 0) is no orphaned attachment: it was listed and deleted, and the post lost its file;
 * - a topic with a wrong poll is repaired, not deleted: deleting it left its messages without a topic (and the next repair deleted
 *   them).
 */
class KunenaDiagnosticsRepairsTest extends IntegrationTestCase implements DBTestInterface
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

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    private function attachment(int $id, int $mesid): void
    {
        $this->insertRow('#__kunena_attachments', ['id' => $id, 'mesid' => $mesid, 'userid' => 42, 'folder' => 'media/kunena/attachments/42',
            'filetype' => 'text/plain', 'filename' => "f$id.txt"]);
    }

    public function testAnUploadWhichWaitsForItsMessageIsNoOrphan(): void
    {
        // The upload 1 waits for its message, the attachment 2 is of the message 5 which is gone.
        $this->attachment(1, 0);
        $this->attachment(2, 5);

        $this->assertSame(1, KunenaDiagnostics::count('attachmentOrphaned'));

        KunenaDiagnostics::delete('attachmentOrphaned');

        $this->assertSame([['id' => 1]], $this->fetchAll('SELECT id FROM #__kunena_attachments ORDER BY id'));
    }

    public function testATopicWhosePollIsMissingForgetsThePollAndStays(): void
    {
        $this->insertRow('#__kunena_topics', ['id' => 10, 'category_id' => 2, 'subject' => 'Poll gone', 'poll_id' => 3]);

        $this->assertFalse(KunenaDiagnostics::canDelete('topicMissingPoll'));
        $this->assertTrue(KunenaDiagnostics::fix('topicMissingPoll'));

        $this->assertSame([['id' => 10, 'poll_id' => 0]], $this->fetchAll('SELECT id, poll_id FROM #__kunena_topics'));
    }

    public function testAPollWhichItsTopicDoesNotUseGoesToTheTopicWhichUsesIt(): void
    {
        // A split gave the poll 4 to the new topic 21; the poll still names the topic 20, which has no poll any more.
        $this->insertRows('#__kunena_topics', [
            ['id' => 20, 'category_id' => 2, 'subject' => 'Source', 'poll_id' => 0],
            ['id' => 21, 'category_id' => 2, 'subject' => 'New', 'poll_id' => 4],
        ]);
        $this->insertRow('#__kunena_polls', ['id' => 4, 'title' => 'Poll', 'threadid' => 20]);

        $this->assertFalse(KunenaDiagnostics::canDelete('topicPollMismatch'));
        $this->assertTrue(KunenaDiagnostics::fix('topicPollMismatch'));

        $this->assertSame([['threadid' => 21]], $this->fetchAll('SELECT threadid FROM #__kunena_polls'));
        $this->assertSame([['id' => 20, 'poll_id' => 0], ['id' => 21, 'poll_id' => 4]], $this->fetchAll('SELECT id, poll_id FROM #__kunena_topics ORDER BY id'));
        $this->assertSame(0, KunenaDiagnostics::count('topicPollMismatch'));
    }

    public function testATopicWhichNamesThePollOfAnotherTopicForgetsIt(): void
    {
        // The poll 5 is of the topic 30, which uses it; the topic 31 names it too.
        $this->insertRows('#__kunena_topics', [
            ['id' => 30, 'category_id' => 2, 'subject' => 'Owner', 'poll_id' => 5],
            ['id' => 31, 'category_id' => 2, 'subject' => 'Other', 'poll_id' => 5],
        ]);
        $this->insertRow('#__kunena_polls', ['id' => 5, 'title' => 'Poll', 'threadid' => 30]);

        KunenaDiagnostics::fix('topicPollMismatch');

        $this->assertSame([['threadid' => 30]], $this->fetchAll('SELECT threadid FROM #__kunena_polls'));
        $this->assertSame([['id' => 30, 'poll_id' => 5], ['id' => 31, 'poll_id' => 0]], $this->fetchAll('SELECT id, poll_id FROM #__kunena_topics ORDER BY id'));
    }

    /**
     * Three private posts: the first of the post 5 which exists, the second of the post 9 which is gone, the third without a post.
     */
    private function privatePosts(): void
    {
        $this->insertRow('#__kunena_messages', ['id' => 5, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'subject' => 'Post', 'time' => 1000]);

        foreach ([1 => 5, 2 => 9, 3 => 0] as $id => $message) {
            $this->insertRow('#__kunena_private', ['id' => $id, 'author_id' => 42, 'created_at' => '2026-01-01 00:00:00', 'subject' => 'Private', 'body' => "Secret $id", 'params' => '']);
            $this->insertRow('#__kunena_private_user_map', ['private_id' => $id, 'user_id' => 42]);
            $this->insertRow('#__kunena_private_attachment_map', ['private_id' => $id, 'attachment_id' => 70 + $id]);

            if ($message) {
                $this->insertRow('#__kunena_private_post_map', ['private_id' => $id, 'message_id' => $message]);
            }
        }
    }

    /**
     * The private posts which are left, in each of their tables.
     */
    private function privateLeft(): array
    {
        $ids = fn (string $table, string $column): array => array_map('intval', array_column($this->fetchAll("SELECT $column FROM $table ORDER BY $column"), $column));

        return [
            'private'     => $ids('#__kunena_private', 'id'),
            'posts'       => $ids('#__kunena_private_post_map', 'private_id'),
            'users'       => $ids('#__kunena_private_user_map', 'private_id'),
            'attachments' => $ids('#__kunena_private_attachment_map', 'private_id'),
        ];
    }

    /**
     * A private post whose post of the forum is gone is found and deleted (a post deleted for good left its private post, with
     * its text, behind until Kunena 8.0; nothing found it). Its recipients and links are for the checks which follow it.
     */
    public function testAPrivatePostWhosePostIsGoneIsDeleted(): void
    {
        $this->privatePosts();

        $this->assertContains('privateOrphaned', KunenaDiagnostics::getList());
        $this->assertSame(2, KunenaDiagnostics::count('privateOrphaned'));

        KunenaDiagnostics::delete('privateOrphaned');

        $this->assertSame([1], $this->privateLeft()['private']);
        $this->assertSame(0, KunenaDiagnostics::count('privateOrphaned'));
    }

    /**
     * The upgrade to Kunena 8.0 removes them, with their recipients and links.
     */
    public function testTheUpgradeRemovesThePrivatePostsWhosePostIsGone(): void
    {
        $this->privatePosts();

        $queries = array_filter(
            array_map('trim', preg_split('/\R/', file_get_contents(\dirname(__DIR__, 5) . '/src/admin/sql/updates/mysql/8.0.0-DEV.sql'))),
            static fn (string $line): bool => str_starts_with($line, 'DELETE a FROM `#__kunena_private')
        );

        $this->assertCount(4, $queries);

        foreach ($queries as $query) {
            $this->getDBDriver()->setQuery(rtrim($query, ';'))->execute();
        }

        $this->assertSame(['private' => [1], 'posts' => [1], 'users' => [1], 'attachments' => [1]], $this->privateLeft());
    }
}
