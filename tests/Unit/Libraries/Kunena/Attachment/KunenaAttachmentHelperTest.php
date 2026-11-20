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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * The parts of the attachment helper which need no Joomla and no database.
 */
class KunenaAttachmentHelperTest extends UnitTestCase
{
    /**
     * An attachment with only what the sort needs.
     */
    private function attachment(int $id, string $name, int $size, string $type): object
    {
        return new class ($id, $name, $size, $type) {
            public function __construct(public int $id, private string $name, public int $size, public string $filetype)
            {
            }

            public function getFilename($escape = true): string
            {
                return $this->name;
            }

            public function getPath($thumb = false)
            {
                return false;
            }
        };
    }

    public static function names(): array
    {
        return [
            'a short name is kept'        => ['photo.png', 'photo.png'],
            'the limit is kept'           => ['abcdefghij...abcdefgh', 'abcdefghij...abcdefgh'],
            'a long name is shortened'    => ['a-very-long-file-name-of-a-holiday.jpg', 'a-very-lon...iday.jpg'],
            'an empty name'               => ['', ''],
        ];
    }

    /**
     * The name of an uploaded file as it is shown: never a folder which the browser has sent, no control characters.
     */
    public static function clientNames(): array
    {
        return [
            'a plain name'           => ['photo.png', 'photo.png'],
            'a folder of Windows'    => ['C:\\Users\\me\\photo.png', 'photo.png'],
            'a folder'               => ['/var/www/photo.png', 'photo.png'],
            'up a folder'            => ['../../config.php', 'config.php'],
            'control characters'     => ["pho\x00to\x1f.png", 'photo.png'],
            'only dots'              => ['..', ''],
            'not a text'             => [['photo.png'], ''],
            'too long'               => [str_repeat('a', 300) . '.png', str_repeat('a', 190)],
        ];
    }

    #[DataProvider('clientNames')]
    public function testGetClientFilename(mixed $name, string $expected): void
    {
        $this->assertSame($expected, KunenaAttachmentHelper::getClientFilename($name));
    }

    #[DataProvider('names')]
    public function testShortenFilename(string $name, string $expected): void
    {
        $this->assertSame($expected, KunenaAttachmentHelper::shortenFilename($name));
    }

    public function testShortenFilenameWithOwnLengths(): void
    {
        $this->assertSame('abc~xyz', KunenaAttachmentHelper::shortenFilename('abcdefghijklmnopqrstuvwxyz', 3, 3, '~'));
    }

    public static function mimeTypes(): array
    {
        return [
            'png'         => ['image/png', true],
            'jpeg'        => ['image/jpeg', true],
            'capitals'    => ['IMAGE/GIF', true],
            'pdf'         => ['application/pdf', false],
            'text'        => ['text/plain', false],
            'video'       => ['video/mp4', false],
            'empty'       => ['', false],
        ];
    }

    #[DataProvider('mimeTypes')]
    public function testIsImageMime(string $mime, bool $expected): void
    {
        $helper = new class () extends KunenaAttachmentHelper {
        };

        $this->assertSame($expected, $helper->isImageMime($mime));
    }

    public function testSortByNameIsNatural(): void
    {
        $list = [
            1 => $this->attachment(1, 'file10.txt', 5, 'text/plain'),
            2 => $this->attachment(2, 'File2.txt', 5, 'text/plain'),
            3 => $this->attachment(3, 'file1.txt', 5, 'text/plain'),
        ];

        $sorted = KunenaAttachmentHelper::sort($list, 'name');

        $this->assertSame([3, 2, 1], array_keys($sorted));
    }

    public function testSortBySizeDescendingKeepsTheKeys(): void
    {
        $list = [
            10 => $this->attachment(10, 'a', 300, 'text/plain'),
            11 => $this->attachment(11, 'b', 100, 'text/plain'),
            12 => $this->attachment(12, 'c', 200, 'text/plain'),
        ];

        $sorted = KunenaAttachmentHelper::sort($list, 'size', 'desc');

        $this->assertSame([10, 12, 11], array_keys($sorted));
    }

    public function testSortByTypeGroupsTheTypesAndSortsTheNamesInside(): void
    {
        $list = [
            1 => $this->attachment(1, 'b.pdf', 1, 'application/pdf'),
            2 => $this->attachment(2, 'z.png', 1, 'image/png'),
            3 => $this->attachment(3, 'a.pdf', 1, 'application/pdf'),
            4 => $this->attachment(4, 'a.png', 1, 'image/png'),
        ];

        $sorted = KunenaAttachmentHelper::sort($list, 'type');

        $this->assertSame([3, 1, 4, 2], array_keys($sorted));
    }

    public function testSortByIdIsTheOrderOfUploading(): void
    {
        $list = [
            2 => $this->attachment(2, 'b', 1, 'x'),
            1 => $this->attachment(1, 'a', 1, 'x'),
        ];

        $this->assertSame([1, 2], array_keys(KunenaAttachmentHelper::sort($list)));
    }

    public function testSortOfOneOrNoAttachmentChangesNothing(): void
    {
        $this->assertSame([], KunenaAttachmentHelper::sort([]));

        $one = [5 => $this->attachment(5, 'a', 1, 'x')];

        $this->assertSame($one, KunenaAttachmentHelper::sort($one, 'name', 'desc'));
    }

    /**
     * A finder reads whole rows: they become the attachments (with one query for the list, not one more per attachment). An
     * attachment which is already loaded is kept, a row without id is no attachment.
     */
    public function testTheRowsOfAFinderBecomeTheAttachments(): void
    {
        $instances = new ReflectionProperty(KunenaAttachmentHelper::class, '_instances');
        $messages  = new ReflectionProperty(KunenaAttachmentHelper::class, '_messages');
        $saved     = [$instances->getValue(), $messages->getValue()];

        try {
            $loaded = static::bare(KunenaAttachment::class, ['id' => 7, 'mesid' => 3, 'filename' => 'kept.png']);
            $instances->setValue(null, [7 => $loaded]);
            $messages->setValue(null, []);

            $list = KunenaAttachmentHelper::getFromRows([
                12 => (object) ['id' => 12, 'mesid' => 5, 'userid' => 46, 'filename' => 'photo.jpg'],
                7  => ['id' => 7, 'mesid' => 3, 'filename' => 'read again.png'],
                0  => ['id' => 0, 'filename' => 'no id'],
            ]);

            $this->assertSame([12, 7], array_keys($list));
            $this->assertInstanceOf(KunenaAttachment::class, $list[12]);
            $this->assertSame('photo.jpg', $list[12]->filename);
            $this->assertSame(46, (int) $list[12]->userid);
            $this->assertTrue($list[12]->exists());
            $this->assertSame($loaded, $list[7]);

            // The helper knows the new attachment: by its id and by its message.
            $this->assertSame($list[12], $instances->getValue()[12]);
            $this->assertSame($list[12], $messages->getValue()[5][12]);
        } finally {
            $instances->setValue(null, $saved[0]);
            $messages->setValue(null, $saved[1]);
        }
    }
}
