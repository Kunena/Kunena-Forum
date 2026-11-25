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
use Kunena\Forum\Libraries\Tools\KunenaCacheFolders as C;
use Kunena\Tests\Unit\UnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The tool "Empty the caches" measures and empties the cache folders: everything but the files which protect the folder.
 */
class KunenaCacheFoldersTest extends UnitTestCase
{
    private string $dir = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir() . '/kunena-cache-' . bin2hex(random_bytes(4));

        foreach (['index.html' => 'i', '.htaccess' => 'h', 'size108/users/avatar62.png' => 'abc', 'size108/users/index.html' => 'i', 'a.css' => 'abcd'] as $file => $content) {
            @mkdir(\dirname($this->dir . '/' . $file), 0777, true);
            file_put_contents($this->dir . '/' . $file, $content);
        }
    }

    protected function tearDown(): void
    {
        if (is_dir($this->dir)) {
            $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($items as $item) {
                $item->isDir() && !$item->isLink() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
            }

            @rmdir($this->dir);
        }

        parent::tearDown();
    }

    public function testMeasureCountsTheFilesButTheProtectionAtTheTop(): void
    {
        // avatar62.png (3), the index.html of a subfolder (1), a.css (4).
        $this->assertSame(['files' => 3, 'bytes' => 8], C::measure($this->dir));
    }

    public function testEmptyFolderKeepsOnlyTheProtectionAtTheTop(): void
    {
        $this->assertSame(['files' => 3, 'bytes' => 8], C::emptyFolder($this->dir));

        $this->assertFileExists($this->dir . '/index.html');
        $this->assertFileExists($this->dir . '/.htaccess');
        $this->assertFileDoesNotExist($this->dir . '/a.css');
        $this->assertDirectoryDoesNotExist($this->dir . '/size108');
        $this->assertSame(['files' => 0, 'bytes' => 0], C::measure($this->dir));
    }

    public function testAMissingFolderIsEmpty(): void
    {
        $this->assertSame(['files' => 0, 'bytes' => 0], C::measure($this->dir . '/nowhere'));
        $this->assertSame(['files' => 0, 'bytes' => 0], C::emptyFolder($this->dir . '/nowhere'));
    }

    public function testTheFoldersAreKunenaOnes(): void
    {
        foreach (C::folders() as $path) {
            $this->assertMatchesRegularExpression('#(^|/)(cache/kunena|media/kunena/)#', $path);
        }
    }
}
