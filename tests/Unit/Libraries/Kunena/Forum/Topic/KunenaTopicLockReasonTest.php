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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicLockReason;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The reasons of a locked topic: the list of the configuration, the check of the reason which a request sends, who besides the
 * moderators may lock a topic with a reason, and the column lock_reason in every way a site gets its tables.
 */
class KunenaTopicLockReasonTest extends UnitTestCase
{
    private const array REASONS = ['Closed', 'Sold'];

    public function testTheLinesAreSplitTrimmedAndWithoutBlanksOrDuplicates(): void
    {
        $this->assertSame(['A', 'B', 'C'], KunenaTopicLockReason::parse("A\r\n\r\n B \nA\rC"));
    }

    public function testEveryKindOfLineBreakSplitsTheList(): void
    {
        $this->assertSame(['A', 'B', 'C', 'D'], KunenaTopicLockReason::parse("A\r\nB\nC\rD"));
    }

    public function testTheFirstOfTheDuplicatesIsKept(): void
    {
        $this->assertSame(['Sold', 'Closed'], KunenaTopicLockReason::parse("Sold\nClosed\n  Sold  \nClosed"));
    }

    public function testALongLineIsCutToTheLengthOfTheColumn(): void
    {
        $reasons = KunenaTopicLockReason::parse(str_repeat('é', 300) . "\nB");

        $this->assertSame(255, mb_strlen($reasons[0], 'UTF-8'));
        $this->assertSame(str_repeat('é', 255), $reasons[0]);
        $this->assertSame('B', $reasons[1]);
    }

    public function testNoSettingGivesNoReasons(): void
    {
        $this->assertSame([], KunenaTopicLockReason::parse(null));
        $this->assertSame([], KunenaTopicLockReason::parse(''));
        $this->assertSame([], KunenaTopicLockReason::parse(" \r\n\n  \r"));
    }

    public function testTheDefaultOfTheConfigurationIsThreeLanguageKeys(): void
    {
        $xml    = simplexml_load_file(Files::path('src/admin/config.xml'));
        $fields = $xml->xpath('//field[@name="lockReasons"]');

        $this->assertCount(1, $fields);
        $this->assertSame(
            ['COM_KUNENA_TOPIC_LOCK_REASON_CLOSED', 'COM_KUNENA_TOPIC_LOCK_REASON_SOLVED', 'COM_KUNENA_TOPIC_LOCK_REASON_SOLD'],
            KunenaTopicLockReason::parse((string) $fields[0]['default'])
        );
    }

    public function testAReasonOfTheListIsKept(): void
    {
        $this->assertSame('Sold', KunenaTopicLockReason::clean('Sold', self::REASONS));
    }

    public function testTheSpacesAroundAReasonAreRemoved(): void
    {
        $this->assertSame('Sold', KunenaTopicLockReason::clean("  Sold\n", self::REASONS));
    }

    public static function noReasons(): array
    {
        return [
            'not in the list' => ['Gone'],
            'other case'      => ['sold'],
            'empty'           => [''],
            'only spaces'     => ['   '],
            'null'            => [null],
            'number'          => [1],
            'array'           => [['Sold']],
        ];
    }

    #[DataProvider('noReasons')]
    public function testAnythingElseIsNoReason(mixed $value): void
    {
        $this->assertNull(KunenaTopicLockReason::clean($value, self::REASONS));
    }

    public function testNoListAcceptsNoReason(): void
    {
        $this->assertNull(KunenaTopicLockReason::clean('Sold', []));
    }

    /**
     * The logged-in author of an unlocked, approved topic.
     */
    private function author(array $topic = [], array $user = []): array
    {
        // The classes of the topic and of the user need the framework of Joomla.
        $this->requireJoomla();

        return [
            self::bare(KunenaTopic::class, $topic + ['first_post_userid' => 42, 'locked' => 0, 'hold' => 0]),
            self::bare(KunenaUser::class, $user + ['userid' => 42, '_exists' => true, 'banned' => null, 'blocked' => 0]),
        ];
    }

    public function testTheAuthorMayLockHisUnlockedTopic(): void
    {
        [$topic, $user] = $this->author();

        $this->assertTrue(KunenaTopicLockReason::authorMayLock($topic, $user, true, self::REASONS));
    }

    public function testNotWhenTheSettingIsOff(): void
    {
        [$topic, $user] = $this->author();

        $this->assertFalse(KunenaTopicLockReason::authorMayLock($topic, $user, false, self::REASONS));
    }

    public function testNotWithoutReasons(): void
    {
        [$topic, $user] = $this->author();

        $this->assertFalse(KunenaTopicLockReason::authorMayLock($topic, $user, true, []));
    }

    public function testNotForAGuest(): void
    {
        // A guest topic and a guest: both have the user id 0.
        [$topic, $user] = $this->author(['first_post_userid' => 0], ['userid' => 0, '_exists' => false]);

        $this->assertFalse(KunenaTopicLockReason::authorMayLock($topic, $user, true, self::REASONS));
    }

    public function testNotForTheUserIdZero(): void
    {
        [$topic, $user] = $this->author(['first_post_userid' => 0], ['userid' => 0]);

        $this->assertFalse(KunenaTopicLockReason::authorMayLock($topic, $user, true, self::REASONS));
    }

    public function testNotForABannedUser(): void
    {
        [$topic, $user] = $this->author([], ['banned' => '9999-12-31 23:59:59']);

        $this->assertFalse(KunenaTopicLockReason::authorMayLock($topic, $user, true, self::REASONS));
    }

    public function testNotForTheTopicOfAnotherUser(): void
    {
        [$topic, $user] = $this->author(['first_post_userid' => 7]);

        $this->assertFalse(KunenaTopicLockReason::authorMayLock($topic, $user, true, self::REASONS));
    }

    public function testNotForALockedTopic(): void
    {
        [$topic, $user] = $this->author(['locked' => 1]);

        $this->assertFalse(KunenaTopicLockReason::authorMayLock($topic, $user, true, self::REASONS));
    }

    public static function holds(): array
    {
        return ['unapproved' => [1], 'deleted' => [2], 'topic deleted' => [3]];
    }

    #[DataProvider('holds')]
    public function testNotForATopicWhichIsNotApproved(int $hold): void
    {
        [$topic, $user] = $this->author(['hold' => $hold]);

        $this->assertFalse(KunenaTopicLockReason::authorMayLock($topic, $user, true, self::REASONS));
    }

    public function testUnlockingClearsTheReason(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Topic/KunenaTopic.php');

        $this->assertSame(1, preg_match('/public function lock\(\$value = 1, string \$reason = \'\'\): bool\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString("\$this->lock_reason = \$this->locked ? \$reason : '';", $m[1]);
        $this->assertStringContainsString("'lock_reason'           => \$this->lock_reason,", $source, 'The reason is not stored.');
    }

    public function testTheTaskChecksTheReasonBeforeItLocks(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function lock\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $clean = strpos($m[1], 'KunenaTopicLockReason::clean(');

        $this->assertIsInt($clean);
        $this->assertLessThan(strpos($m[1], '->lock(1'), $clean);
        $this->assertStringContainsString('KunenaTopicLockReason::authorMayLock(', $m[1], 'The author is not checked.');
        $this->assertStringContainsString("['reason' => \$reason]", $m[1], 'The log has no reason.');

        // Only the moderators unlock.
        $this->assertSame(1, preg_match('/public function unlock\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString("\$topic->tryAuthorise('lock');", $m[1]);
        $this->assertStringNotContainsString('KunenaTopicLockReason', $m[1]);
    }

    public function testANewInstallCreatesTheColumn(): void
    {
        $this->assertMatchesRegularExpression(
            "/CREATE TABLE IF NOT EXISTS `#__kunena_topics`[^;]*`locked`\s+tinyint\(4\)\s+NOT NULL default '0',\s+`lock_reason`\s+varchar\(255\)\s+NOT NULL default ''/i",
            Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql')
        );

        $xml    = simplexml_load_file(Files::path('src/admin/sql/install/schema/install.xml'));
        $fields = $xml->xpath('//table[@name="kunena_topics"]/field[@name="lock_reason"]');

        $this->assertCount(1, $fields, 'The schema of the installer has no lock_reason on kunena_topics.');
        $this->assertSame('varchar(255)', (string) $fields[0]['type']);
        $this->assertSame('0', (string) $fields[0]['null']);
        $this->assertTrue(isset($fields[0]['default']));
        $this->assertSame('', (string) $fields[0]['default']);
    }

    public function testTheUpgradeFileOfTheInstallerAddsTheColumn(): void
    {
        $xml     = simplexml_load_file(Files::path('src/admin/install/kunena.install.upgrade.xml'));
        $queries = [];

        foreach ($xml->upgrade[0]->version as $version) {
            if ((string) $version['version'] === '8.0.0-DEV') {
                foreach ($version->query as $query) {
                    $queries[] = trim((string) $query);
                }
            }
        }

        $this->assertContains(
            "ALTER TABLE `#__kunena_topics` ADD `lock_reason` varchar(255) NOT NULL default '' AFTER `locked`",
            $queries,
            'The block 8.0.0-DEV does not add the reason of a lock.'
        );
    }

    public function testTheUpdateFileOfJoomlaAddsTheColumn(): void
    {
        $this->assertStringContainsString(
            "ALTER TABLE `#__kunena_topics` ADD `lock_reason` varchar(255) NOT NULL default '' AFTER `locked`;",
            Files::read('src/admin/sql/updates/mysql/7.2.0-DEV.sql')
        );
    }
}
