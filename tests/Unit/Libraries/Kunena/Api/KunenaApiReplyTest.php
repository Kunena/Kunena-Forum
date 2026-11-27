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

namespace Kunena\Tests\Unit\Libraries\Kunena\Api;

use Kunena\Forum\Libraries\Api\KunenaApiReply;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * The rules of a reply posted through the REST API: its fields, the flood protection, the identical messages, the links and the
 * moderation of the new users.
 */
class KunenaApiReplyTest extends UnitTestCase
{
    public function testTheFieldsAreReadFlat(): void
    {
        $this->assertSame(
            ['topic_id' => 12, 'parent_id' => 0, 'message' => 'Hello', 'subscribe' => true],
            KunenaApiReply::fields(['topic_id' => 12, 'message' => '  Hello ', 'subscribe' => 1])
        );
    }

    public function testTheFieldsAreReadAsJsonApi(): void
    {
        $this->assertSame(
            ['topic_id' => 0, 'parent_id' => 7, 'message' => 'Hi', 'subscribe' => false],
            KunenaApiReply::fields(['data' => ['type' => 'posts', 'attributes' => ['parent_id' => '7', 'message' => 'Hi']]])
        );
    }

    public static function badBodies(): array
    {
        return [
            'no body'                 => [null],
            'no topic'                => [['message' => 'Hello']],
            'a topic which is no id'  => [['topic_id' => '12abc', 'message' => 'Hello']],
            'a negative topic'        => [['topic_id' => -3, 'message' => 'Hello']],
            'no message'              => [['topic_id' => 12]],
            'a message which is no text' => [['topic_id' => 12, 'message' => ['Hello']]],
            'only tags'               => [['topic_id' => 12, 'message' => '[b] [/b]']],
        ];
    }

    #[DataProvider('badBodies')]
    public function testABadBodyIsABadRequest(mixed $body): void
    {
        try {
            KunenaApiReply::fields($body);
            $this->fail('no exception');
        } catch (RuntimeException $e) {
            $this->assertSame(400, $e->getCode());
        }
    }

    public static function texts(): array
    {
        return [
            'text'                 => ['Hello', true],
            'text in tags'         => ['[b]Hello[/b]', true],
            'an image only'        => ['[img]https://example.com/a.png[/img]', true],
            'empty tags and space' => ["[i]\n [/i]  ", false],
            'nothing'              => ['', false],
        ];
    }

    #[DataProvider('texts')]
    public function testAMessageMustHaveText(string $message, bool $has): void
    {
        $this->assertSame($has, KunenaApiReply::hasText($message));
    }

    public static function floods(): array
    {
        // last post, now, option, moderator, flood
        return [
            'within the time'         => [995, 1000, 10, false, true],
            'after the time'          => [990, 1000, 10, false, false],
            'the option is off'       => [999, 1000, 0, false, false],
            'a moderator'             => [999, 1000, 10, true, false],
            'the first post'          => [0, 1000, 10, false, false],
        ];
    }

    #[DataProvider('floods')]
    public function testTheFloodProtection(int $last, int $now, int $seconds, bool $moderator, bool $flood): void
    {
        $this->assertSame($flood, KunenaApiReply::isFlood($last, $now, $seconds, $moderator));
    }

    public static function duplicates(): array
    {
        $now = 10000;

        // last author, last text, last time, user, text, duplicate
        return [
            'the same reply again'      => [5, 'Hello', $now - 10, 5, ' Hello ', true],
            'later'                     => [5, 'Hello', $now - KunenaApiReply::DUPLICATE_SECONDS - 1, 5, 'Hello', false],
            'another text'              => [5, 'Hello', $now - 10, 5, 'Hello!', false],
            'another user'              => [6, 'Hello', $now - 10, 5, 'Hello', false],
        ];
    }

    #[DataProvider('duplicates')]
    public function testAnIdenticalReplyIsNotPostedTwice(int $lastUser, string $lastText, int $lastTime, int $user, string $text, bool $duplicate): void
    {
        $this->assertSame($duplicate, KunenaApiReply::isDuplicate($lastUser, $lastText, $lastTime, $user, $text, 10000));
    }

    public function testTheNewUsersWaitForAModerator(): void
    {
        $this->assertTrue(KunenaApiReply::holdsNewUser(false, 2, 5));
        $this->assertFalse(KunenaApiReply::holdsNewUser(false, 5, 5));
        $this->assertFalse(KunenaApiReply::holdsNewUser(true, 0, 5));
        $this->assertFalse(KunenaApiReply::holdsNewUser(false, 0, 0));
    }

    public static function links(): array
    {
        return [
            'a url tag'    => ['See [url=https://example.com]this[/url]', true],
            'an image'     => ['[img]a.png[/img]', true],
            'a bare link'  => ['See https://example.com', true],
            'www'          => ['See www.example.com', true],
            'plain text'   => ['See the topic about www', false],
            // The site makes links of these too, and the form removes them.
            'ftp'          => ['Get ftp://files.example.com/a', true],
            'www2'         => ['See www2.example.com', true],
            'an e-mail'    => ['Write to spam@example.com', true],
        ];
    }

    /**
     * The links are counted as the site shows them: the form of the site counts the links of the shown text. Only the addresses
     * with a scheme were counted, so a reply of the API passed the limit with [url=example.com] or www.example.com, and an address
     * written as the text of its own [url] tag, or one in a block of code, was counted where the site shows no second link.
     */
    public static function counted(): array
    {
        return [
            'url tags without a scheme' => [str_repeat('[url=spam.example/x]x[/url] ', 3), 3],
            'www without a scheme'      => ['www.spam.example and www3.other.example', 2],
            'an address without scheme' => ['[url=//spam.example/a]a[/url]', 1],
            'a tag with its address'    => ['[url=https://a.example]https://a.example[/url]', 1],
            'a url tag with the link'   => ['[url]https://a.example/b[/url]', 1],
            'in a block of code'        => ['[code]https://a.example[/code]', 0],
            'a path of the site'        => ['[url=/forum/topic]here[/url]', 0],
            'the site without scheme'   => ['[url=forum.example.org/a]a[/url] www.forum.example.org/b', 0],
        ];
    }

    #[DataProvider('counted')]
    public function testTheLinksAreCountedAsTheSiteShowsThem(string $message, int $count): void
    {
        $this->assertSame($count, KunenaApiReply::externalLinks($message, 'forum.example.org'));
    }

    #[DataProvider('links')]
    public function testTheLinksAndImagesOfANewUser(string $message, bool $has): void
    {
        $this->assertSame($has, KunenaApiReply::hasLinksOrImages($message));
    }

    public function testTheLinksToTheSiteDoNotCount(): void
    {
        $message = 'https://forum.example.org/a https://www.forum.example.org/b [url=http://other.com/x]x[/url] https://third.net';

        $this->assertSame(2, KunenaApiReply::externalLinks($message, 'forum.example.org'));
        $this->assertSame(0, KunenaApiReply::externalLinks('no links', 'forum.example.org'));
    }

    public function testNoReplyInReadOnlyMode(): void
    {
        $this->requireJoomla();

        // The site hides the reply buttons in read-only mode (isAuthorised()), but reply() checked tryAuthorise() only, which does
        // not know the mode: a member with an API token posted his reply. It is refused before anything is read or saved.
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['readOnly' => 1]);
        $instance = new ReflectionProperty(KunenaConfig::class, 'instance');
        $saved    = $instance->getValue();
        $instance->setValue(null, $config);

        try {
            KunenaApiReply::reply(self::bare(KunenaUser::class, ['userid' => 50, '_exists' => true]), [
                'topic_id' => 99999999, 'parent_id' => 0, 'message' => 'Hello', 'subscribe' => false,
            ]);
            $this->fail('no exception');
        } catch (RuntimeException $e) {
            $this->assertSame(403, $e->getCode());
        } finally {
            $instance->setValue(null, $saved);
        }
    }
}
