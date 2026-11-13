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

namespace Kunena\Tests\Unit\Libraries\Kunena\Log;

use Kunena\Forum\Libraries\Log\KunenaActionLog;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The messages which the moderation entries of the Kunena log get in the User Actions Log of Joomla: only the allow-list of
 * the entry (actor, operation, topic, category, target user), with a language key for each context and a label for the
 * operation which has an English text.
 */
class KunenaActionLogTest extends UnitTestCase
{
    private const KEYS = [
        'id', 'type', 'userid', 'accountlink', 'topicid', 'categoryid', 'targetuserid', 'targetlink', 'username', 'title', 'category',
        'targetusername',
    ];

    /**
     * The data of an entry, as KunenaEntry::getData() returns it.
     */
    private static function data(array $values = []): array
    {
        return $values + [
            'type'        => KunenaLog::TYPE_MODERATION,
            'user_id'     => 42,
            'category_id' => 7,
            'topic_id'    => 123,
            'target_user' => 0,
            'ip'          => '1.2.3.4',
            'time'        => 1700000000,
            'operation'   => KunenaLog::LOG_TOPIC_LOCK,
            'data'        => json_encode(['reason_private' => 'secret']),
        ];
    }

    private static function names(array $values = []): array
    {
        return $values + ['user' => 'moderator', 'topic' => 'A topic', 'category' => 'A category', 'target_user' => ''];
    }

    public static function otherTypes(): array
    {
        return [
            'administration' => [KunenaLog::TYPE_ADMINISTRATION],
            'action'         => [KunenaLog::TYPE_ACTION],
            'error'          => [KunenaLog::TYPE_ERROR],
            'report'         => [KunenaLog::TYPE_REPORT],
        ];
    }

    #[DataProvider('otherTypes')]
    public function testAnEntryWhichIsNoModerationGivesNoMessage(int $type): void
    {
        $this->assertNull(KunenaActionLog::buildMessage(self::data(['type' => $type]), self::names()));
    }

    public static function contexts(): array
    {
        return [
            'nothing'           => [0, 0, 0, 'COM_KUNENA_ACTIONLOG_MODERATION'],
            'topic'             => [7, 123, 0, 'COM_KUNENA_ACTIONLOG_MODERATION_TOPIC'],
            'category'          => [7, 0, 0, 'COM_KUNENA_ACTIONLOG_MODERATION_CATEGORY'],
            'user'              => [0, 0, 9, 'COM_KUNENA_ACTIONLOG_MODERATION_USER'],
            'topic and user'    => [7, 123, 9, 'COM_KUNENA_ACTIONLOG_MODERATION_TOPIC_USER'],
            'category and user' => [7, 0, 9, 'COM_KUNENA_ACTIONLOG_MODERATION_CATEGORY_USER'],
        ];
    }

    #[DataProvider('contexts')]
    public function testTheKeyFollowsTheContext(int $categoryId, int $topicId, int $targetUser, string $key): void
    {
        $result = KunenaActionLog::buildMessage(
            self::data(['category_id' => $categoryId, 'topic_id' => $topicId, 'target_user' => $targetUser]),
            self::names()
        );

        $this->assertSame($key, $result['key']);
    }

    public function testTheMessageHasExactlyTheKeysOfTheAllowList(): void
    {
        $result = KunenaActionLog::buildMessage(self::data(['target_user' => 9]), self::names(['target_user' => 'member']));

        $this->assertSame(['key', 'message'], array_keys($result));
        $this->assertSame(self::KEYS, array_keys($result['message']));
        $this->assertSame(
            [
                'id'             => '123',
                'type'           => 'COM_KUNENA_ACTIONLOG_OPERATION_TOPIC_LOCK',
                'userid'         => '42',
                'accountlink'    => 'index.php?option=com_users&task=user.edit&id=42',
                'topicid'        => '123',
                'categoryid'     => '7',
                'targetuserid'   => '9',
                'targetlink'     => 'index.php?option=com_users&task=user.edit&id=9',
                'username'       => 'moderator',
                'title'          => 'A topic',
                'category'       => 'A category',
                'targetusername' => 'member',
            ],
            $result['message']
        );
    }

    public function testAnEntryWithoutContextHasZerosAndNoTargetLink(): void
    {
        $result = KunenaActionLog::buildMessage(
            self::data(['category_id' => 0, 'topic_id' => 0]),
            self::names(['topic' => '', 'category' => ''])
        );

        $this->assertSame('0', $result['message']['id']);
        $this->assertSame('0', $result['message']['topicid']);
        $this->assertSame('0', $result['message']['categoryid']);
        $this->assertSame('0', $result['message']['targetuserid']);
        $this->assertSame('', $result['message']['targetlink']);

        foreach ($result['message'] as $value) {
            $this->assertIsString($value);
        }
    }

    public function testNeitherTheDataNorTheIpNorTheTimeLeaveKunena(): void
    {
        $data   = self::data([
            'target_user' => 9,
            'data'        => json_encode(['reason_private' => 'secret', 'reason_public' => 'shown', 'comment' => 'remark', 'email' => 'a@b.example']),
        ]);
        $result = json_encode(KunenaActionLog::buildMessage($data, self::names(['target_user' => 'member'])));

        foreach (['secret', 'reason_private', 'shown', 'remark', 'a@b.example', '1.2.3.4', '1700000000'] as $value) {
            $this->assertStringNotContainsString($value, $result);
        }
    }

    public function testBracesOfUserWrittenValuesCannotBePlaceholdersAndNothingIsEscaped(): void
    {
        $result = KunenaActionLog::buildMessage(
            self::data(['target_user' => 9]),
            [
                'user'        => '{targetlink}',
                'topic'       => '{accountlink}<b>',
                'category'    => '<script>alert(1)</script>{x}',
                'target_user' => 'a}b{c',
            ]
        );

        $this->assertSame('(targetlink)', $result['message']['username']);
        $this->assertSame('(accountlink)<b>', $result['message']['title']);
        $this->assertSame('<script>alert(1)</script>(x)', $result['message']['category']);
        $this->assertSame('a)b(c', $result['message']['targetusername']);
    }

    public function testAKnownOperationGetsALanguageKey(): void
    {
        $result = KunenaActionLog::buildMessage(self::data(['operation' => KunenaLog::LOG_TOPIC_LOCK]), self::names());

        $this->assertSame('COM_KUNENA_ACTIONLOG_OPERATION_TOPIC_LOCK', $result['message']['type']);
    }

    public function testAnUnknownOperationGetsAReadableLabel(): void
    {
        $result = KunenaActionLog::buildMessage(self::data(['operation' => 'LOG_MY_PLUGIN_THING']), self::names());

        $this->assertSame('My plugin thing', $result['message']['type']);
    }

    public function testTheErrorsAndTheNotificationsAreNoKnownOperation(): void
    {
        $operations = KunenaActionLog::knownOperations();

        $this->assertContains(KunenaLog::LOG_USER_BAN, $operations);
        $this->assertNotContains(KunenaLog::LOG_ERROR_FATAL, $operations);
        $this->assertNotContains(KunenaLog::LOG_EMAIL_NOTIFICATION_SENT, $operations);
    }

    public function testEveryKeyTheClassCanReturnHasAnEnglishText(): void
    {
        $strings = Files::language('src/admin/language/en-GB/com_kunena.ini');
        $keys    = array_column(self::contexts(), 3);

        foreach (KunenaActionLog::knownOperations() as $operation) {
            $result = KunenaActionLog::buildMessage(self::data(['operation' => $operation]), self::names());
            $keys[] = $result['message']['type'];
        }

        $this->assertGreaterThan(30, \count($keys));

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $strings, "$key is not in the administrator com_kunena.ini.");
            $this->assertNotSame('', trim($strings[$key]));
        }
    }

    public function testTheOperationTextsHaveNoHtmlAndTheMessagesNameOnlyTheirPlaceholders(): void
    {
        $strings = Files::language('src/admin/language/en-GB/com_kunena.ini');

        foreach (KunenaActionLog::knownOperations() as $operation) {
            $text = $strings['COM_KUNENA_ACTIONLOG_OPERATION_' . substr($operation, 4)];

            $this->assertSame(strip_tags($text), $text);
        }

        foreach (self::contexts() as [$categoryId, $topicId, $targetUser, $key]) {
            $text = $strings[$key];

            $this->assertStringStartsWith("User <a href='{accountlink}'>{username}</a>", $text);
            $this->assertStringContainsString('{type}', $text);
            $this->assertSame($topicId > 0, str_contains($text, '{title}'), $key);
            $this->assertSame($targetUser > 0, str_contains($text, '{targetusername}'), $key);
            $this->assertSame($categoryId > 0, str_contains($text, '{category}'), $key);
        }
    }
}
