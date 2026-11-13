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

use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The uploads which the text of a message names with [attachment=ID]name[/attachment], and which of them the saving user
 * may put into his message.
 */
class KunenaMessageAttachmentsInTextTest extends UnitTestCase
{
    public static function texts(): array
    {
        return [
            'no tag'                  => ['Hello world', []],
            'empty text'              => ['', []],
            'one tag'                 => ['See [attachment=5]a.jpg[/attachment]', [5]],
            'two tags'                => ['[attachment=5]a.jpg[/attachment] [attachment=12]b.png[/attachment]', [5, 12]],
            'the same id twice'       => ['[attachment=5]a[/attachment] [attachment=5]a[/attachment]', [5]],
            'tag in capitals'         => ['[ATTACHMENT=7]G[/ATTACHMENT]', [7]],
            'a temporary id'          => ['[attachment:5]a.jpg[/attachment]', []],
            'a name without an id'    => ['[attachment]x[/attachment]', []],
            'a name is not an id'     => ['[attachment=abc]x[/attachment]', []],
            'id 0'                    => ['[attachment=0]x[/attachment]', []],
            'ids keep the text order' => ['[attachment=9]I[/attachment][attachment=3]C[/attachment]', [9, 3]],
        ];
    }

    #[DataProvider('texts')]
    public function testGetAttachmentIdsInText(string $text, array $expected): void
    {
        $this->assertSame($expected, KunenaMessage::getAttachmentIdsInText($text));
    }

    private static function attachment(int $id, int $userid, int $mesid = 0, bool $exists = true): KunenaAttachment
    {
        return self::bare(KunenaAttachment::class, ['id' => $id, 'userid' => $userid, 'mesid' => $mesid, '_exists' => $exists]);
    }

    public function testAnOwnPendingUploadIsKept(): void
    {
        $own = self::attachment(5, 42);

        $this->assertSame([5 => $own], KunenaMessage::selectOwnPendingAttachments([5 => $own], 42, []));
    }

    public function testTheUploadOfAnotherUserIsDropped(): void
    {
        $this->assertSame([], KunenaMessage::selectOwnPendingAttachments([5 => self::attachment(5, 43)], 42, []));
    }

    public function testAFileInAMessageIsDropped(): void
    {
        $this->assertSame([], KunenaMessage::selectOwnPendingAttachments([5 => self::attachment(5, 42, 10)], 42, []));
    }

    public function testAMissingFileIsDropped(): void
    {
        $this->assertSame([], KunenaMessage::selectOwnPendingAttachments([5 => self::attachment(5, 42, 0, false)], 42, []));
    }

    public function testASkippedIdIsDropped(): void
    {
        $kept = self::attachment(6, 42);
        $list = [5 => self::attachment(5, 42), 6 => $kept];

        $this->assertSame([6 => $kept], KunenaMessage::selectOwnPendingAttachments($list, 42, [5]));
    }

    public function testAGuestGetsNothing(): void
    {
        $this->assertSame([], KunenaMessage::selectOwnPendingAttachments([5 => self::attachment(5, 0)], 0, []));
    }
}
