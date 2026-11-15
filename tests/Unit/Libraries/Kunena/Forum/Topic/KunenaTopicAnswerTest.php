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

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicAnswer;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The accepted answer of a topic: which message can be the answer, who may choose it, what accepting and removing it change, and the
 * column answer_id in every way a site gets its tables.
 */
class KunenaTopicAnswerTest extends UnitTestCase
{
    /**
     * A published topic 10 of the user 42 with its first post 100, a published reply 101 of the topic and its author.
     *
     * @return  array{0: KunenaTopic, 1: KunenaMessage, 2: KunenaUser}
     */
    private function topic(array $topic = [], array $message = [], array $user = []): array
    {
        // The classes of the topic, the message and the user need the framework of Joomla.
        $this->requireJoomla();

        return [
            self::bare(KunenaTopic::class, $topic + [
                'id' => 10, 'first_post_id' => 100, 'first_post_userid' => 42, 'locked' => 0, 'hold' => 0, 'moved_id' => 0,
                'answer_id' => 0, 'icon_id' => 0,
            ]),
            self::bare(KunenaMessage::class, $message + ['id' => 101, 'thread' => 10, 'hold' => 0]),
            self::bare(KunenaUser::class, $user + ['userid' => 42, '_exists' => true, 'banned' => null, 'blocked' => 0]),
        ];
    }

    public function testAPublishedReplyCanBeTheAnswer(): void
    {
        [$topic, $message] = $this->topic();

        $this->assertTrue(KunenaTopicAnswer::canBeAnswer($topic, $message));
    }

    public static function noAnswers(): array
    {
        return [
            'the first post'         => [[], ['id' => 100]],
            'a reply of another topic' => [[], ['thread' => 11]],
            'no message'             => [[], ['id' => 0]],
            'an unapproved reply'    => [[], ['hold' => 1]],
            'a deleted reply'        => [[], ['hold' => 2]],
            'an unapproved topic'    => [['hold' => 1], []],
            'a deleted topic'        => [['hold' => 2], []],
            'a moved topic'          => [['moved_id' => 12], []],
        ];
    }

    #[DataProvider('noAnswers')]
    public function testAnythingElseCannotBeTheAnswer(array $topic, array $message): void
    {
        [$topic, $message] = $this->topic($topic, $message);

        $this->assertFalse(KunenaTopicAnswer::canBeAnswer($topic, $message));
    }

    public function testTheAnswerIsTheMessageOfTheTopic(): void
    {
        [$topic, $message] = $this->topic(['answer_id' => 101]);

        $this->assertTrue(KunenaTopicAnswer::isAnswer($topic, $message));
    }

    public function testAnotherMessageIsNotTheAnswer(): void
    {
        [$topic, $message] = $this->topic(['answer_id' => 102]);

        $this->assertFalse(KunenaTopicAnswer::isAnswer($topic, $message));
    }

    public function testATopicWithoutAnswerHasNoAnswerForANewMessage(): void
    {
        // A message which is not saved has the id 0, as a topic without answer.
        [$topic, $message] = $this->topic([], ['id' => 0]);

        $this->assertFalse(KunenaTopicAnswer::isAnswer($topic, $message));
    }

    public function testTheAuthorOfTheTopicMayChooseTheAnswer(): void
    {
        [$topic, $message, $user] = $this->topic();

        $this->assertTrue(KunenaTopicAnswer::mayChoose($topic, $message, $user, false, true));
    }

    public function testAModeratorMayChooseTheAnswerOfATopicOfAnotherUser(): void
    {
        [$topic, $message, $user] = $this->topic(['first_post_userid' => 7]);

        $this->assertTrue(KunenaTopicAnswer::mayChoose($topic, $message, $user, true, true));
    }

    public function testAModeratorMayChooseTheAnswerOfALockedTopic(): void
    {
        [$topic, $message, $user] = $this->topic(['locked' => 1, 'first_post_userid' => 7]);

        $this->assertTrue(KunenaTopicAnswer::mayChoose($topic, $message, $user, true, true));
    }

    public function testNobodyWhenTheSettingIsOff(): void
    {
        [$topic, $message, $user] = $this->topic();

        $this->assertFalse(KunenaTopicAnswer::mayChoose($topic, $message, $user, false, false));
        $this->assertFalse(KunenaTopicAnswer::mayChoose($topic, $message, $user, true, false));
    }

    public function testNotEvenAModeratorForTheFirstPost(): void
    {
        [$topic, $message, $user] = $this->topic([], ['id' => 100]);

        $this->assertFalse(KunenaTopicAnswer::mayChoose($topic, $message, $user, true, true));
    }

    public function testNotForTheTopicOfAnotherUser(): void
    {
        [$topic, $message, $user] = $this->topic(['first_post_userid' => 7]);

        $this->assertFalse(KunenaTopicAnswer::mayChoose($topic, $message, $user, false, true));
    }

    public function testNotForTheAuthorOfALockedTopic(): void
    {
        [$topic, $message, $user] = $this->topic(['locked' => 1]);

        $this->assertFalse(KunenaTopicAnswer::mayChoose($topic, $message, $user, false, true));
    }

    public function testNotForAGuest(): void
    {
        // A guest topic and a guest: both have the user id 0.
        [$topic, $message, $user] = $this->topic(['first_post_userid' => 0], [], ['userid' => 0, '_exists' => false]);

        $this->assertFalse(KunenaTopicAnswer::mayChoose($topic, $message, $user, false, true));
    }

    public function testNotForTheUserIdZero(): void
    {
        [$topic, $message, $user] = $this->topic(['first_post_userid' => 0], [], ['userid' => 0]);

        $this->assertFalse(KunenaTopicAnswer::mayChoose($topic, $message, $user, false, true));
    }

    public function testNotForABannedAuthor(): void
    {
        [$topic, $message, $user] = $this->topic([], [], ['banned' => '9999-12-31 23:59:59']);

        $this->assertFalse(KunenaTopicAnswer::mayChoose($topic, $message, $user, false, true));
    }

    public function testAcceptingKeepsTheReplyAndMarksTheTopicSolved(): void
    {
        [$topic, $message] = $this->topic();

        $this->assertTrue(KunenaTopicAnswer::toggle($topic, $message, true));
        $this->assertSame(101, $topic->answer_id);
        $this->assertSame(KunenaTopic::ICON_SOLVED, $topic->icon_id);
    }

    public function testAcceptingWithoutTheSolvedIconKeepsTheIcon(): void
    {
        [$topic, $message] = $this->topic(['icon_id' => 3]);

        $this->assertTrue(KunenaTopicAnswer::toggle($topic, $message, false));
        $this->assertSame(101, $topic->answer_id);
        $this->assertSame(3, $topic->icon_id);
    }

    public function testAcceptingAnotherReplyReplacesTheAnswer(): void
    {
        [$topic, $message] = $this->topic(['answer_id' => 105, 'icon_id' => KunenaTopic::ICON_SOLVED]);

        $this->assertTrue(KunenaTopicAnswer::toggle($topic, $message, true));
        $this->assertSame(101, $topic->answer_id);
        $this->assertSame(KunenaTopic::ICON_SOLVED, $topic->icon_id);
    }

    public function testRemovingTheAnswerMarksTheTopicNotSolved(): void
    {
        [$topic, $message] = $this->topic(['answer_id' => 101, 'icon_id' => KunenaTopic::ICON_SOLVED]);

        $this->assertFalse(KunenaTopicAnswer::toggle($topic, $message, true));
        $this->assertSame(0, $topic->answer_id);
        $this->assertSame(0, $topic->icon_id);
    }

    public function testRemovingTheAnswerKeepsAnIconWhichIsNotSolved(): void
    {
        [$topic, $message] = $this->topic(['answer_id' => 101, 'icon_id' => 3]);

        $this->assertFalse(KunenaTopicAnswer::toggle($topic, $message, true));
        $this->assertSame(0, $topic->answer_id);
        $this->assertSame(3, $topic->icon_id);
    }

    public function testTheSettingIsOnByDefault(): void
    {
        $xml    = simplexml_load_file(Files::path('src/admin/config.xml'));
        $fields = $xml->xpath('//field[@name="acceptedAnswers"]');

        $this->assertCount(1, $fields);
        $this->assertSame('1', (string) $fields[0]['default']);
    }

    public function testTheTaskChecksTheUserBeforeItChangesTheTopic(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function answer\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $token  = strpos($m[1], "Session::checkToken('get')");
        $check  = strpos($m[1], 'KunenaTopicAnswer::mayChoose(');
        $toggle = strpos($m[1], 'KunenaTopicAnswer::toggle(');

        $this->assertIsInt($token, 'The token is not checked.');
        $this->assertIsInt($check, 'The user is not checked.');
        $this->assertIsInt($toggle);
        $this->assertLessThan($check, $token);
        $this->assertLessThan($toggle, $check);
        $this->assertLessThan(strpos($m[1], '$topic->save()'), $toggle);
    }

    public function testANewInstallCreatesTheColumn(): void
    {
        $this->assertMatchesRegularExpression(
            "/CREATE TABLE IF NOT EXISTS `#__kunena_topics`[^;]*`lock_reason`\s+varchar\(255\)\s+NOT NULL default '',\s+`answer_id`\s+int\(11\)\s+NOT NULL default '0'/i",
            Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql')
        );

        $xml    = simplexml_load_file(Files::path('src/admin/sql/install/schema/install.xml'));
        $fields = $xml->xpath('//table[@name="kunena_topics"]/field[@name="answer_id"]');

        $this->assertCount(1, $fields, 'The schema of the installer has no answer_id on kunena_topics.');
        $this->assertSame('int(11)', (string) $fields[0]['type']);
        $this->assertSame('0', (string) $fields[0]['null']);
        $this->assertSame('0', (string) $fields[0]['default']);
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
            "ALTER TABLE `#__kunena_topics` ADD `answer_id` int(11) NOT NULL default '0' AFTER `lock_reason`",
            $queries,
            'The block 8.0.0-DEV does not add the accepted answer.'
        );
    }

    public function testTheUpdateFileOfJoomlaAddsTheColumn(): void
    {
        $this->assertStringContainsString(
            "ALTER TABLE `#__kunena_topics` ADD COLUMN `answer_id` int(11) NOT NULL default '0' AFTER `lock_reason`;",
            Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql')
        );
    }

    public function testTheTopicSavesTheColumn(): void
    {
        $this->assertStringContainsString(
            "'answer_id'             => \$this->answer_id,",
            Files::read('src/libraries/kunena/src/Forum/Topic/KunenaTopic.php'),
            'The answer is not stored.'
        );
    }
}
