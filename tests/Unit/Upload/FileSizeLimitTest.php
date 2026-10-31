<?php

/**
 * Kunena Component
 *
 * Regression test for the size limits of KunenaUpload::upload(). The limit for files was applied to everything that is not an
 * image with "stripos($type, 'image/') !== true". stripos() returns a position or false, never true, so the comparison was always
 * true: images were checked against the limit for files too, and an image of 3 MB was refused when the limit for images was
 * 5 MB and the one for files 2 MB.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Upload;

use PHPUnit\Framework\TestCase;

class FileSizeLimitTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    private function upload(): string
    {
        $source = file_get_contents(self::SRC . '/libraries/kunena/src/Upload/KunenaUpload.php');

        $this->assertSame(1, preg_match('/public function upload\([^)]*\)\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));

        return $m[1];
    }

    public function testLimitForFilesIsOnlyAppliedToFilesWhichAreNotImages(): void
    {
        $body = $this->upload();

        $this->assertSame(1, preg_match('/if \(!\$file->isAvatar && stripos\(\$type, \'image\/\'\) === false\) \{\s*if \(!\$this->checkFileSizeFileAttachment\(/', $body), 'Files which are not images are checked against the limit for files.');
        $this->assertSame(1, preg_match('/if \(!\$file->isAvatar && stripos\(\$type, \'image\/\'\) !== false\) \{\s*if \(!\$this->checkFileSizeImageAttachment\(/', $body), 'Images are checked against the limit for images.');
    }

    public function testNoPositionIsComparedWithABoolean(): void
    {
        $found = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($path, '/External/') || str_contains($path, '/vendor/')) {
                continue;
            }

            // strpos() and its relatives give a position or false: a comparison with true is always the same answer.
            if (preg_match('/\b(?:mb_)?(?:str|stri)(?:pos|rpos)\([^;]*?\)\s*[!=]==\s*true\b/', file_get_contents($path))) {
                $found[] = substr($path, strpos($path, '/src/') + 1);
            }
        }

        $this->assertSame([], $found, 'A position can never be identical to true.');
    }
}
