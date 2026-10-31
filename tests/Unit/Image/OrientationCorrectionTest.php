<?php

/**
 * Kunena Component
 *
 * Regression test for KunenaUpload::upload(): it called KunenaImage::correctImageOrientation($file->tmp_name) as a static method.
 * The method needs an image object (it is not static, and takes no argument), so every upload through this path ended in
 * "Non-static method cannot be called statically", a fatal error. And it ran for every file, also for documents.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Image;

use Kunena\Forum\Libraries\Image\KunenaImageHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrientationCorrectionTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    /** @var string[] */
    private array $files = [];

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once self::SRC . '/libraries/kunena/src/Image/KunenaImageHelper.php';
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }
    }

    private function tmp(string $content, string $ext): string
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kunena-orient-' . bin2hex(random_bytes(4)) . '.' . $ext;
        file_put_contents($file, $content);
        $this->files[] = $file;

        return $file;
    }

    /**
     * A small JPEG, with an EXIF block which holds the orientation when one is given.
     */
    private function jpeg(?int $orientation): string
    {
        if (!\function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD is not available.');
        }

        $image = imagecreatetruecolor(8, 4);
        ob_start();
        imagejpeg($image);
        $jpeg = ob_get_clean();

        if ($orientation === null) {
            return $jpeg;
        }

        // TIFF header (big endian), one entry of the IFD: tag 0x0112 (orientation), type SHORT, count 1.
        $tiff = "MM\x00\x2A\x00\x00\x00\x08" . "\x00\x01" . "\x01\x12\x00\x03\x00\x00\x00\x01" . pack('n', $orientation) . "\x00\x00" . "\x00\x00\x00\x00";
        $app1 = "Exif\x00\x00" . $tiff;

        return substr($jpeg, 0, 2) . "\xFF\xE1" . pack('n', \strlen($app1) + 2) . $app1 . substr($jpeg, 2);
    }

    public static function orientations(): array
    {
        return [
            'mirrored'          => [2, true],
            'upside down'       => [3, true],
            'turned right'      => [6, true],
            'turned left'       => [8, true],
            'as it is'          => [1, false],
            'nonsense value'    => [9, false],
            'zero'              => [0, false],
        ];
    }

    #[DataProvider('orientations')]
    public function testJpegWithOrientation(int $orientation, bool $expected): void
    {
        if (!\function_exists('exif_read_data')) {
            $this->markTestSkipped('exif is not available.');
        }

        $this->assertSame($expected, KunenaImageHelper::needsOrientationFix($this->tmp($this->jpeg($orientation), 'jpg')));
    }

    public function testJpegWithoutExifIsLeftAlone(): void
    {
        $this->assertFalse(KunenaImageHelper::needsOrientationFix($this->tmp($this->jpeg(null), 'jpg')));
    }

    public function testOnlyJpegIsTurned(): void
    {
        if (!\function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD is not available.');
        }

        $image = imagecreatetruecolor(8, 4);
        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        ob_start();
        imagegif($image);
        $gif = ob_get_clean();

        $this->assertFalse(KunenaImageHelper::needsOrientationFix($this->tmp($png, 'png')));
        $this->assertFalse(KunenaImageHelper::needsOrientationFix($this->tmp($gif, 'gif')), 'A GIF may be an animation.');
    }

    public function testDocumentsAndMissingFilesAreLeftAlone(): void
    {
        $this->assertFalse(KunenaImageHelper::needsOrientationFix($this->tmp("%PDF-1.4 not an image", 'pdf')));
        $this->assertFalse(KunenaImageHelper::needsOrientationFix($this->tmp('PK' . str_repeat("\0", 64), 'zip')));
        $this->assertFalse(KunenaImageHelper::needsOrientationFix(sys_get_temp_dir() . '/kunena-does-not-exist.jpg'));
    }

    public function testJpegWithBrokenExifDoesNotRaiseAnything(): void
    {
        $jpeg = $this->jpeg(6);
        $file = $this->tmp(substr($jpeg, 0, 40), 'jpg');

        $this->assertFalse(KunenaImageHelper::needsOrientationFix($file));
    }

    private function upload(): string
    {
        $source = file_get_contents(self::SRC . '/libraries/kunena/src/Upload/KunenaUpload.php');

        $this->assertSame(1, preg_match('/public function upload\([^)]*\)\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));

        return $m[1];
    }

    public function testUploadNoLongerCallsAnInstanceMethodStatically(): void
    {
        $body = $this->upload();

        $this->assertDoesNotMatchRegularExpression('/KunenaImage::correctImageOrientation\(/', $body);
        $this->assertStringContainsString('$this->correctOrientation($file->tmp_name);', $body);
    }

    public function testNoCallStaticallyAnywhere(): void
    {
        $found = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            if ($file->getExtension() === 'php' && preg_match('/\b\w*Image::correctImageOrientation\(/', file_get_contents($file->getPathname()))) {
                $found[] = $file->getPathname();
            }
        }

        $this->assertSame([], $found, 'correctImageOrientation() is not static.');
    }

    public function testTurningNeverBlocksTheUploadAndIsOnlyDoneWhenNeeded(): void
    {
        $source = file_get_contents(self::SRC . '/libraries/kunena/src/Upload/KunenaUpload.php');

        $this->assertSame(1, preg_match('/function correctOrientation\(string \$file\): void\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));
        $body = $m[1];

        $this->assertLessThan(strpos($body, 'new KunenaImage('), strpos($body, 'needsOrientationFix('), 'Only photos which are stored turned are loaded.');
        $this->assertStringContainsString('catch (\Throwable $e)', $body, 'A failure keeps the file as uploaded.');
        $this->assertStringContainsString('IMAGETYPE_JPEG', $body);
    }
}
