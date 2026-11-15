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

namespace Kunena\Tests\Unit\Libraries\Kunena\Email;

use Kunena\Forum\Libraries\Email\KunenaDigest;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The digests of the subscriptions: the choices, who gets a digest instead of an e-mail, when a digest is due, the table in every
 * way a site gets its tables, where the posts are collected and sent, and the clean-ups.
 */
class KunenaDigestTest extends UnitTestCase
{
    public function testTheIntervalsOfTheChoices(): void
    {
        $this->assertSame(0, KunenaDigest::interval(KunenaDigest::EACH));
        $this->assertSame(86400, KunenaDigest::interval(KunenaDigest::DAILY));
        $this->assertSame(604800, KunenaDigest::interval(KunenaDigest::WEEKLY));
        $this->assertSame(0, KunenaDigest::interval(9));
    }

    public static function choices(): array
    {
        return [
            'daily'          => [1, KunenaDigest::DAILY],
            'weekly as text' => ['2', KunenaDigest::WEEKLY],
            'each'           => [0, KunenaDigest::EACH],
            'unknown'        => [5, KunenaDigest::EACH],
            'negative'       => [-1, KunenaDigest::EACH],
            'text'           => ['daily', KunenaDigest::EACH],
            'nothing'        => [null, KunenaDigest::EACH],
        ];
    }

    #[DataProvider('choices')]
    public function testTheChoiceOfARequest(mixed $value, int $choice): void
    {
        $this->assertSame($choice, KunenaDigest::clean($value));
    }

    private static function recipient(int $id, array $flags = []): object
    {
        return (object) ($flags + ['id' => $id, 'subscription' => 1, 'moderator' => 0, 'admin' => 0]);
    }

    public function testASubscriberWithADigestGetsNoEmailNow(): void
    {
        [$now, $digest] = KunenaDigest::split([self::recipient(5), self::recipient(6)], [5 => KunenaDigest::DAILY]);

        $this->assertSame([6], array_map(static fn ($r): int => $r->id, $now));
        $this->assertSame([5], $digest);
    }

    public static function alwaysNow(): array
    {
        return [
            'a moderator'           => [['moderator' => 1]],
            'an administrator'      => [['admin' => 1]],
            'a follower of a user'  => [['follower' => 1]],
            'not a subscription'    => [['subscription' => 0]],
            'no id'                 => [['id' => 0]],
        ];
    }

    #[DataProvider('alwaysNow')]
    public function testTheEmailIsSentNowTo(array $flags): void
    {
        $recipient = self::recipient(5, $flags);

        [$now, $digest] = KunenaDigest::split([$recipient], [5 => KunenaDigest::WEEKLY, 0 => KunenaDigest::WEEKLY]);

        $this->assertSame([$recipient], $now);
        $this->assertSame([], $digest);
    }

    public function testAUserWithoutAChoiceGetsTheEmailNow(): void
    {
        [$now, $digest] = KunenaDigest::split([self::recipient(5)], []);

        $this->assertCount(1, $now);
        $this->assertSame([], $digest);
    }

    public static function due(): array
    {
        $day = 86400;

        // choice, last digest, oldest post, now, due
        return [
            'daily, a day after the last one'          => [KunenaDigest::DAILY, 1000, 5000, 1000 + $day, true],
            'daily, before a day'                      => [KunenaDigest::DAILY, 1000, 5000, 1000 + $day - 1, false],
            'daily, never sent: from the oldest post'  => [KunenaDigest::DAILY, 0, 5000, 5000 + $day, true],
            'daily, never sent, too early'             => [KunenaDigest::DAILY, 0, 5000, 5000 + $day - 1, false],
            'weekly, six days after'                   => [KunenaDigest::WEEKLY, 1000, 2000, 1000 + 6 * $day, false],
            'weekly, a week after'                     => [KunenaDigest::WEEKLY, 1000, 2000, 1000 + 7 * $day, true],
            'back to each post: at once'               => [KunenaDigest::EACH, 1000, 2000, 1001, true],
        ];
    }

    #[DataProvider('due')]
    public function testWhenTheDigestIsDue(int $frequency, int $lastSent, int $firstPending, int $now, bool $isDue): void
    {
        $this->assertSame($isDue, KunenaDigest::isDue($frequency, $lastSent, $firstPending, $now));
    }

    public function testTheSettingIsOnByDefault(): void
    {
        $xml    = simplexml_load_file(Files::path('src/admin/config.xml'));
        $fields = $xml->xpath('//field[@name="digestEmails"]');

        $this->assertCount(1, $fields);
        $this->assertSame('1', (string) $fields[0]['default']);
    }

    public function testThePostsAreCollectedBeforeTheEmailsAreQueuedOrSent(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php');

        $this->assertSame(1, preg_match('/public function processNotification\(.*?\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $split = strpos($m[1], 'KunenaDigest::split(');

        $this->assertIsInt($split, 'The recipients are not split.');
        $this->assertIsInt(strpos($m[1], 'KunenaDigest::collect('));
        $this->assertLessThan(strpos($m[1], 'sendNotificationNow('), $split);
        $this->assertLessThan(strpos($m[1], '#__kunena_notifications_mailsqueue'), $split);
    }

    public function testTheSchedulerHasARoutineWhichSendsTheDigests(): void
    {
        $source = Files::read('src/plugins/plg_task_kunena/src/Extension/Kunena.php');

        $this->assertStringContainsString("'send.digests' => [", $source);
        $this->assertStringContainsString('KunenaDigest::sendDue(', $source);
        $this->assertStringContainsString(
            'PLG_TASK_KUNENA_SENDDIGESTS_TITLE=',
            Files::read('src/plugins/plg_task_kunena/language/en-GB/plg_task_kunena.ini')
        );
    }

    public function testTheProfileSavesTheChoice(): void
    {
        $this->assertStringContainsString(
            'KunenaUserHelper::setJoomlaParam((int) $this->user->userid, KunenaDigest::PARAM,',
            Files::read('src/site/src/Controllers/UserController.php')
        );
    }

    public function testANewInstallCreatesTheTable(): void
    {
        $this->assertMatchesRegularExpression(
            '/CREATE TABLE IF NOT EXISTS `#__kunena_digest`\s*\(\s*`user_id`\s+int\(11\)\s+NOT NULL,\s*`message_id`\s+int\(11\)\s+NOT NULL,[^;]*PRIMARY KEY \(`user_id`, `message_id`\)/i',
            Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql')
        );

        $xml = simplexml_load_file(Files::path('src/admin/sql/install/schema/install.xml'));

        $this->assertCount(1, $xml->xpath('//table[@name="kunena_digest"]/key[@name="PRIMARY"][@columns="user_id,message_id"]'));
    }

    public function testTheUpdateAndTheUninstallKnowTheTable(): void
    {
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `#__kunena_digest`', Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql'));
        $this->assertStringContainsString('DROP TABLE IF EXISTS `#__kunena_digest`;', Files::read('src/admin/sql/uninstall/mysql/uninstall.mysql.utf8.sql'));
    }

    public static function cleanUps(): array
    {
        return [
            'a deleted message'             => ['src/libraries/kunena/src/Forum/Message/KunenaMessage.php'],
            'a deleted topic'               => ['src/libraries/kunena/src/Forum/Topic/KunenaTopic.php'],
            'the topics deleted at once'    => ['src/libraries/kunena/src/Forum/Topic/KunenaTopicHelper.php'],
            'a purged category'             => ['src/libraries/kunena/src/Forum/Category/KunenaCategory.php'],
            'a deleted Joomla user'         => ['src/plugins/plg_system_kunena/src/Extension/Kunena.php'],
            'the privacy export and remove' => ['src/plugins/plg_privacy_kunena/src/Extension/Kunena.php'],
            'the diagnostics'               => ['src/libraries/kunena/src/Forum/KunenaDiagnostics.php'],
        ];
    }

    #[DataProvider('cleanUps')]
    public function testTheCollectedPostsAreRemovedWith(string $file): void
    {
        $this->assertMatchesRegularExpression('/#__kunena_digest\b/', Files::read($file));
    }
}
