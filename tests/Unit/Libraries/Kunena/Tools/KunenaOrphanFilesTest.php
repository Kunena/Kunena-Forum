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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use FilesystemIterator;
use Kunena\Forum\Libraries\Tools\KunenaOrphanFiles as O;
use Kunena\Tests\Unit\UnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The tool "Orphaned files" lists and deletes the attachment files and the avatars which no row names, never a file which
 * a row names (with its thumbnail and raw copy), never a file which protects a folder, never a fresh upload.
 */
class KunenaOrphanFilesTest extends UnitTestCase
{
    private const NOW = 1_800_000_000;

    private string $root = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir() . '/kunena-orphans-' . bin2hex(random_bytes(4));

        foreach ([
            'media/kunena/attachments/index.html'          => self::NOW - 999999,
            'media/kunena/attachments/62/used.jpg'         => self::NOW - 999999,
            'media/kunena/attachments/62/thumb/used.jpg'   => self::NOW - 999999,
            'media/kunena/attachments/62/raw/used.jpg'     => self::NOW - 999999,
            'media/kunena/attachments/62/orphan.png'       => self::NOW - 999999,
            'media/kunena/attachments/62/thumb/orphan.png' => self::NOW - 999999,
            'media/kunena/attachments/62/fresh.png'        => self::NOW - 60,
            'media/kunena/attachments/62/index.html'       => self::NOW - 999999,
            'media/kunena/avatars/users/avatar62.png'      => self::NOW - 999999,
            'media/kunena/avatars/users/avatar99.png'      => self::NOW - 999999,
        ] as $file => $time) {
            @mkdir(\dirname($this->root . '/' . $file), 0777, true);
            file_put_contents($this->root . '/' . $file, 'xx');
            touch($this->root . '/' . $file, $time);
        }
    }

    protected function tearDown(): void
    {
        if (is_dir($this->root)) {
            $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($items as $item) {
                $item->isDir() && !$item->isLink() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
            }

            @rmdir($this->root);
        }

        parent::tearDown();
    }

    private static function used(): array
    {
        return O::attachmentPaths([['folder' => 'media/kunena/attachments/62', 'filename' => 'used.jpg']]);
    }

    public function testAnAttachmentNamesItsFileItsThumbnailAndItsRawCopy(): void
    {
        $this->assertSame(
            ['media/kunena/attachments/62/used.jpg', 'media/kunena/attachments/62/thumb/used.jpg', 'media/kunena/attachments/62/raw/used.jpg'],
            O::attachmentPaths([['folder' => 'media/kunena/attachments/62/', 'filename' => 'used.jpg'], ['folder' => '', 'filename' => 'x.jpg']])
        );
    }

    public function testOnlyTheUploadedAvatarsAreNamed(): void
    {
        $this->assertSame(
            ['media/kunena/avatars/users/avatar62.png'],
            O::avatarPaths(['users/avatar62.png', 'gallery/cat.png', '', 'nophoto.png'])
        );
    }

    public function testFindListsTheFilesWhichNoRowNames(): void
    {
        $found = O::find($this->root, O::ATTACHMENTS, self::used(), self::NOW);

        $this->assertSame(['media/kunena/attachments/62/orphan.png', 'media/kunena/attachments/62/thumb/orphan.png'], array_keys($found));
        $this->assertSame(2, $found['media/kunena/attachments/62/orphan.png']['size']);
    }

    public function testAFreshUploadIsNoOrphan(): void
    {
        $this->assertArrayNotHasKey('media/kunena/attachments/62/fresh.png', O::find($this->root, O::ATTACHMENTS, self::used(), self::NOW));
        $this->assertArrayHasKey('media/kunena/attachments/62/fresh.png', O::find($this->root, O::ATTACHMENTS, self::used(), self::NOW, 0));
    }

    public function testTheFilesWhichProtectAFolderAreKept(): void
    {
        foreach (array_keys(O::find($this->root, O::ATTACHMENTS, [], self::NOW)) as $path) {
            $this->assertNotSame('index.html', basename($path));
        }
    }

    public function testTheNamesAreComparedWithoutCase(): void
    {
        $used = O::attachmentPaths([['folder' => 'media/kunena/attachments/62', 'filename' => 'ORPHAN.PNG']]);

        $this->assertArrayNotHasKey('media/kunena/attachments/62/orphan.png', O::find($this->root, O::ATTACHMENTS, array_merge(self::used(), $used), self::NOW));
    }

    public function testAvatarsOfNoUserAreFound(): void
    {
        $used = O::avatarPaths(['users/avatar62.png']);

        $this->assertSame(['media/kunena/avatars/users/avatar99.png'], array_keys(O::find($this->root, O::AVATARS, $used, self::NOW)));
    }

    public function testAMissingFolderHasNoOrphan(): void
    {
        $this->assertSame([], O::find($this->root . '/nowhere', O::ATTACHMENTS, [], self::NOW));
    }

    public function testDeleteRemovesOnlyTheOrphansOfTheListMadeAgain(): void
    {
        $result = O::delete($this->root, O::ATTACHMENTS, self::used(), self::NOW);

        $this->assertSame(2, $result['deleted']);
        $this->assertSame(4, $result['bytes']);
        $this->assertFileDoesNotExist($this->root . '/media/kunena/attachments/62/orphan.png');
        $this->assertFileExists($this->root . '/media/kunena/attachments/62/used.jpg');
        $this->assertFileExists($this->root . '/media/kunena/attachments/62/thumb/used.jpg');
        $this->assertFileExists($this->root . '/media/kunena/attachments/62/fresh.png');
        $this->assertFileExists($this->root . '/media/kunena/attachments/62/index.html');
    }

    public function testFindMissingListsTheAttachmentsWithoutTheirFile(): void
    {
        $missing = O::findMissing($this->root, [
            ['id' => 1, 'folder' => 'media/kunena/attachments/62', 'filename' => 'used.jpg'],
            ['id' => 2, 'folder' => 'media/kunena/attachments/62', 'filename' => 'gone.jpg'],
            ['id' => 3, 'folder' => '', 'filename' => ''],
        ]);

        $this->assertSame([2, 3], array_keys($missing));
    }
}
