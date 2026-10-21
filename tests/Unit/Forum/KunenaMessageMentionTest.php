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

namespace Kunena\Forum\Tests\Unit\Forum;

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The users which are mentioned in a message with [mention userid=1]name[/mention].
 */
class KunenaMessageMentionTest extends TestCase
{
    public static function texts(): array
    {
        return [
            'no mention'              => ['Hello world', []],
            'empty text'              => ['', []],
            'one mention'             => ['Hi [mention userid=5]Anna[/mention]!', [5]],
            'two mentions'            => ['[mention userid=5]A[/mention] and [mention userid=12]B[/mention]', [5, 12]],
            'the same user twice'     => ['[mention userid=5]A[/mention] [mention userid=5]A[/mention]', [5]],
            'tag in capitals'         => ['[MENTION USERID=7]G[/MENTION]', [7]],
            'a name is not an id'     => ['[mention userid=abc]x[/mention]', []],
            'a missing id'            => ['[mention]x[/mention]', []],
            'a plain at sign'         => ['mail me @anna', []],
            'ids keep the text order' => ['[mention userid=9]I[/mention][mention userid=3]C[/mention]', [9, 3]],
        ];
    }

    #[DataProvider('texts')]
    public function testGetMentionedUserIds(string $text, array $expected): void
    {
        $this->assertSame($expected, KunenaMessage::getMentionedUserIds($text));
    }

    public function testTheIdsAreIntegers(): void
    {
        $ids = KunenaMessage::getMentionedUserIds('[mention userid=042]x[/mention]');

        $this->assertSame([42], $ids);
    }
}
