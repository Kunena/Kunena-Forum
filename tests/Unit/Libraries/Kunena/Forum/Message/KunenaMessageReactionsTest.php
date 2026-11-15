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
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageReactions;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The reactions on the messages: the list of the setting, the check of the reaction which a request sends, who may react, the order
 * of the pills, the table in every way a site gets its tables, and the clean-ups which remove the reactions with their messages and
 * users.
 */
class KunenaMessageReactionsTest extends UnitTestCase
{
    private const array LIST = ['👍', '❤️', '😂'];

    public function testTheDefaultOfTheSettingIsSixEmoji(): void
    {
        $xml    = simplexml_load_file(Files::path('src/admin/config.xml'));
        $fields = $xml->xpath('//field[@name="reactions"]');

        $this->assertCount(1, $fields);
        $this->assertSame(['👍', '❤️', '😂', '😮', '😢', '🎉'], KunenaMessageReactions::parse((string) $fields[0]['default']));
    }

    public function testSpacesAndLinesSeparateTheReactions(): void
    {
        $this->assertSame(['👍', '❤️', '+1'], KunenaMessageReactions::parse("  👍 \r\n❤️\t +1 "));
    }

    public function testTheDuplicatesAreLeftOut(): void
    {
        $this->assertSame(['👍', '😂'], KunenaMessageReactions::parse('👍 😂 👍'));
    }

    public function testAReactionWithACharacterOfHtmlIsLeftOut(): void
    {
        $this->assertSame(['👍'], KunenaMessageReactions::parse('<b> 👍 a&b "x" \'y\''));
    }

    public function testATooLongReactionIsLeftOut(): void
    {
        $this->assertSame(['ok'], KunenaMessageReactions::parse(str_repeat('a', KunenaMessageReactions::MAX_LENGTH + 1) . ' ok'));
    }

    public function testTheListHasAtMostEightReactions(): void
    {
        $this->assertSame(['1', '2', '3', '4', '5', '6', '7', '8'], KunenaMessageReactions::parse('1 2 3 4 5 6 7 8 9 10'));
    }

    public function testNoSettingGivesNoReactions(): void
    {
        $this->assertSame([], KunenaMessageReactions::parse(null));
        $this->assertSame([], KunenaMessageReactions::parse(''));
        $this->assertSame([], KunenaMessageReactions::parse(" \r\n "));
    }

    public function testAReactionOfTheListIsKept(): void
    {
        $this->assertSame('❤️', KunenaMessageReactions::clean(' ❤️ ', self::LIST));
    }

    public static function noReactions(): array
    {
        return [
            'not in the list' => ['🎉'],
            'the heart without its variation selector' => ["\u{2764}"],
            'empty'           => [''],
            'null'            => [null],
            'array'           => [['👍']],
        ];
    }

    #[DataProvider('noReactions')]
    public function testAnythingElseIsNoReaction(mixed $value): void
    {
        $this->assertNull(KunenaMessageReactions::clean($value, self::LIST));
    }

    /**
     * A published reply 101 of the user 7 in a published, unlocked topic, and the logged-in user 42.
     *
     * @return  array{0: KunenaTopic, 1: KunenaMessage, 2: KunenaUser}
     */
    private function message(array $topic = [], array $message = [], array $user = []): array
    {
        // The classes of the topic, the message and the user need the framework of Joomla.
        $this->requireJoomla();

        return [
            self::bare(KunenaTopic::class, $topic + ['id' => 10, 'locked' => 0, 'hold' => 0]),
            self::bare(KunenaMessage::class, $message + ['id' => 101, 'thread' => 10, 'userid' => 7, 'hold' => 0]),
            self::bare(KunenaUser::class, $user + ['userid' => 42, '_exists' => true, 'banned' => null, 'blocked' => 0]),
        ];
    }

    public function testAUserMayReactToTheMessageOfAnotherUser(): void
    {
        [$topic, $message, $user] = $this->message();

        $this->assertTrue(KunenaMessageReactions::mayReact($topic, $message, $user, self::LIST));
    }

    public static function noReacting(): array
    {
        return [
            'his own message'      => [[], ['userid' => 42], []],
            'an unapproved message' => [[], ['hold' => 1], []],
            'a deleted message'    => [[], ['hold' => 2], []],
            'a message not saved'  => [[], ['id' => 0], []],
            'a locked topic'       => [['locked' => 1], [], []],
            'a deleted topic'      => [['hold' => 2], [], []],
            'a guest'              => [[], [], ['userid' => 0, '_exists' => false]],
            'the user id 0'        => [[], [], ['userid' => 0]],
            'a banned user'        => [[], [], ['banned' => '9999-12-31 23:59:59']],
        ];
    }

    #[DataProvider('noReacting')]
    public function testNotOn(array $topic, array $message, array $user): void
    {
        [$topic, $message, $user] = $this->message($topic, $message, $user);

        $this->assertFalse(KunenaMessageReactions::mayReact($topic, $message, $user, self::LIST));
    }

    public function testNobodyWithoutReactionsInTheList(): void
    {
        [$topic, $message, $user] = $this->message();

        $this->assertFalse(KunenaMessageReactions::mayReact($topic, $message, $user, []));
    }

    public function testThePillsFollowTheOrderOfTheList(): void
    {
        $this->assertSame(['👍' => 1, '😂' => 3], KunenaMessageReactions::sort(['😂' => 3, '👍' => 1, '❤️' => 0], self::LIST));
    }

    public function testAReactionWhichIsNoLongerInTheListComesLast(): void
    {
        $this->assertSame(['❤️' => 2, '🎉' => 5], KunenaMessageReactions::sort(['🎉' => 5, '❤️' => 2], self::LIST));
    }

    public function testTheTaskChecksTheReactionAndTheUserBeforeItSaves(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function react\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $token  = strpos($m[1], "Session::checkToken('get')");
        $clean  = strpos($m[1], 'KunenaMessageReactions::clean(');
        $check  = strpos($m[1], 'KunenaMessageReactions::mayReact(');
        $toggle = strpos($m[1], 'KunenaMessageReactions::toggle(');

        $this->assertIsInt($token, 'The token is not checked.');
        $this->assertIsInt($clean, 'The reaction is not checked.');
        $this->assertIsInt($check, 'The user is not checked.');
        $this->assertIsInt($toggle);
        $this->assertLessThan($toggle, $clean);
        $this->assertLessThan($toggle, $check);
        $this->assertLessThan($clean, $token);
    }

    public function testANewInstallCreatesTheTableWithABinaryReaction(): void
    {
        $this->assertMatchesRegularExpression(
            '/CREATE TABLE IF NOT EXISTS `#__kunena_reactions`\s*\(\s*`message_id`\s+int\(11\)\s+NOT NULL,\s*`user_id`\s+int\(11\)\s+NOT NULL,'
            . '\s*`reaction`\s+varbinary\(64\)\s+NOT NULL,[^;]*PRIMARY KEY \(`message_id`, `user_id`\)/i',
            Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql')
        );

        $xml    = simplexml_load_file(Files::path('src/admin/sql/install/schema/install.xml'));
        $fields = $xml->xpath('//table[@name="kunena_reactions"]/field[@name="reaction"]');

        $this->assertCount(1, $fields, 'The schema of the installer has no table kunena_reactions.');
        $this->assertSame('varbinary(64)', (string) $fields[0]['type']);
        $this->assertCount(1, $xml->xpath('//table[@name="kunena_reactions"]/key[@name="PRIMARY"][@columns="message_id,user_id"]'));
    }

    public function testTheUpdateAndTheUninstallKnowTheTable(): void
    {
        $this->assertStringContainsString(
            'CREATE TABLE IF NOT EXISTS `#__kunena_reactions`',
            Files::read('src/admin/sql/updates/mysql/8.0.0-DEV.sql')
        );
        $this->assertStringContainsString(
            'DROP TABLE IF EXISTS `#__kunena_reactions`;',
            Files::read('src/admin/sql/uninstall/mysql/uninstall.mysql.utf8.sql')
        );
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
    public function testTheReactionsAreRemovedWith(string $file): void
    {
        $this->assertStringContainsString('#__kunena_reactions', Files::read($file));
    }
}
