<?php

/**
 * Kunena Component
 *
 * Regression test: a few kilobytes of PNG can describe an image of billions of pixels. Kunena read only the header to
 * decide to resize it, and then loaded the whole image into memory: the request died with a fatal error (which no
 * try/catch can stop). For an avatar this happened at every page that showed it. Now the size is checked from the header
 * first, at the uploads and in the resizer.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Image;

use Kunena\Forum\Libraries\Image\KunenaImageHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImageBombGuardTest extends TestCase
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

    private function tmp(string $content, string $ext = 'png'): string
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kunena-img-' . bin2hex(random_bytes(4)) . '.' . $ext;
        file_put_contents($file, $content);
        $this->files[] = $file;

        return $file;
    }

    /**
     * A valid 1-bit grey PNG of the given size. The pixels are all zero, so it compresses to almost nothing: this is the
     * file of the attack. It is written, never loaded into memory.
     */
    private function png(int $width, int $height): string
    {
        $rowBytes = intdiv($width + 7, 8) + 1;
        $ctx      = deflate_init(ZLIB_ENCODING_DEFLATE, ['level' => 9]);
        $idat     = '';

        for ($done = 0; $done < $height; $done += 1000) {
            $rows  = min(1000, $height - $done);
            $idat .= deflate_add($ctx, str_repeat("\0", $rowBytes * $rows), ZLIB_NO_FLUSH);
        }

        $idat .= deflate_add($ctx, '', ZLIB_FINISH);

        $chunk = static fn (string $type, string $data) => pack('N', \strlen($data)) . $type . $data . pack('N', crc32($type . $data));

        return "\x89PNG\r\n\x1a\n" . $chunk('IHDR', pack('NNCCCCC', $width, $height, 1, 0, 0, 0, 0)) . $chunk('IDAT', $idat) . $chunk('IEND', '');
    }

    // --- the size rule ---

    #[DataProvider('sizes')]
    public function testIsSafeSize(string $what, int $w, int $h, ?int $limit, int $inUse, bool $expected): void
    {
        $this->assertSame($expected, KunenaImageHelper::isSafeSize($w, $h, $limit, $inUse), $what);
    }

    public static function sizes(): array
    {
        $M = 1048576;

        return [
            ['avatar 200x200, small limit',            200,   200,   64 * $M,  20 * $M, true],
            ['photo 4000x3000 (12 MP), 256M',           4000,  3000,  256 * $M, 30 * $M, true],
            ['photo 4000x3000 (12 MP), 64M used 30M',   4000,  3000,  64 * $M,  30 * $M, false],
            ['photo 6000x4000 (24 MP), 128M',           6000,  4000,  128 * $M, 20 * $M, false],
            ['photo 6000x4000 (24 MP), 512M',           6000,  4000,  512 * $M, 20 * $M, true],
            ['no memory limit, normal photo',           4000,  3000,  null,     0,       true],
            ['no memory limit, 40 MP exactly',          8000,  5000,  null,     0,       true],
            ['no memory limit, over the hard cap',      8001,  5000,  null,     0,       false],
            ['the bomb of the report: 20000x20000',     20000, 20000, null,     0,       false],
            ['the bomb with a gigabyte of memory',      20000, 20000, 1024 * $M, 0,      false],
            ['one pixel',                               1,     1,     128 * $M, 0,       true],
            ['zero width',                              0,     100,   null,     0,       false],
            ['zero height',                             100,   0,     null,     0,       false],
            ['negative width',                          -1,    100,   null,     0,       false],
            ['very long and thin',                      100000, 100,  null,     0,       true],
            ['very long and thin, too many pixels',     100000, 1000, null,     0,       false],
            ['memory already used up',                  100,   100,   10 * $M,  10 * $M, false],
        ];
    }

    #[DataProvider('limits')]
    public function testParseBytes(mixed $value, ?int $expected): void
    {
        $this->assertSame($expected, KunenaImageHelper::parseBytes($value), var_export($value, true));
    }

    public static function limits(): array
    {
        return [
            ['128M', 134217728],
            ['128m', 134217728],
            ['1G', 1073741824],
            ['512K', 524288],
            ['1048576', 1048576],
            [' 256M ', 268435456],
            ['256MB', 268435456],
            ['-1', null],
            ['', null],
            ['0', null],
            ['lots', null],
            [null, null],
            [false, null],
        ];
    }

    // --- real files: only the header is read ---

    public function testTheBombFileIsSmallAndRefused(): void
    {
        $bomb = $this->tmp($this->png(20000, 20000));

        // This is what made it dangerous: it is tiny, and it passes every limit on the size of a file ...
        $this->assertLessThan(200 * 1024, filesize($bomb), 'The attack file is only a few kilobytes.');
        $this->assertSame([20000, 20000], \array_slice(getimagesize($bomb), 0, 2), 'and the header describes 400 megapixels.');

        // ... and it is refused without being loaded.
        $before = memory_get_peak_usage(true);

        $this->assertFalse(KunenaImageHelper::isSafeFile($bomb));
        $this->assertLessThan($before + 8 * 1048576, memory_get_peak_usage(true), 'Checking must not load the image.');
    }

    public function testAssertSafeFileThrowsForTheBomb(): void
    {
        $this->expectException(\RuntimeException::class);

        KunenaImageHelper::assertSafeFile($this->tmp($this->png(30000, 30000)));
    }

    public function testNormalImagesAreAccepted(): void
    {
        $this->assertTrue(KunenaImageHelper::isSafeFile($this->tmp($this->png(64, 64))), 'png');

        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $this->assertTrue(KunenaImageHelper::isSafeFile($this->tmp($gif, 'gif')), 'gif');

        if (\function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor(50, 50);
            ob_start();
            imagejpeg($im);
            $jpg = ob_get_clean();

            $this->assertTrue(KunenaImageHelper::isSafeFile($this->tmp($jpg, 'jpg')), 'jpeg');
        }
    }

    #[DataProvider('notImages')]
    public function testSomethingWhichIsNotAnImageIsRefused(string $content, string $ext): void
    {
        $this->assertFalse(KunenaImageHelper::isSafeFile($this->tmp($content, $ext)));
    }

    public static function notImages(): array
    {
        return [
            'php code with a png name'  => ['<?php echo 1;', 'png'],
            'html with a jpg name'      => ['<html><script>1</script></html>', 'jpg'],
            'text'                      => ['hello', 'gif'],
            'empty'                     => ['', 'png'],
            'png signature only'        => ["\x89PNG\r\n\x1a\n", 'png'],
            'cut in the header'         => [substr("\x89PNG\r\n\x1a\n\0\0\0\x0dIHDR\0\0", 0, 18), 'png'],
        ];
    }

    public function testAFileWhichDoesNotExistIsRefused(): void
    {
        $this->assertFalse(KunenaImageHelper::isSafeFile(sys_get_temp_dir() . '/kunena-missing-' . bin2hex(random_bytes(4)) . '.png'));
    }

    // --- the code which loads images ---

    private function source(string $file): string
    {
        return file_get_contents(self::SRC . '/' . $file);
    }

    public function testResizerThrowsBeforeLoadingTheImage(): void
    {
        $source = $this->source('libraries/kunena/src/Image/KunenaImageHelper.php');

        $this->assertSame(1, preg_match('/public static function version\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));

        $guard = strpos($m[1], 'self::isSafeSize(');
        $load  = strpos($m[1], 'new KunenaImage($file)');

        $this->assertNotFalse($guard);
        $this->assertNotFalse($load);
        $this->assertLessThan($load, $guard, 'The size is checked before the image is loaded.');
        $this->assertMatchesRegularExpression('/isSafeSize\(.*?\)\)\s*\{\s*throw new \\\\RuntimeException/s', $m[1]);
    }

    public function testAvatarResizerShowsTheDefaultAvatarForAnUnsafeImage(): void
    {
        $body = $this->source('plugins/plg_kunena_kunena/src/Helper/KunenaAvatarKunena.php');

        $guard = strpos($body, 'KunenaImageHelper::isSafeFile($origPath)');
        $call  = strpos($body, 'KunenaImageHelper::version(');

        $this->assertNotFalse($guard);
        $this->assertLessThan($call, $guard, 'Check before the resize is started.');
        $this->assertMatchesRegularExpression("/isSafeFile\\(\\\$origPath\\)\\) \\{.*?return \\\$config->avatarType\\s*\\?\\s*KURL_MEDIA \\. 'core\\/svg\\/person.svg'\\s*:\\s*KURL_MEDIA \\. 'avatars\\/' \\. \\\$config->defaultAvatar;/s", $body);
    }

    public function testAvatarUploadChecksTheImageBeforeChangingAnything(): void
    {
        $source = $this->source('site/src/Controllers/UserController.php');

        $this->assertSame(1, preg_match('/public function upload\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $check  = strpos($m[1], 'KunenaImageHelper::assertSafeFile($uploadFile)');
        $delete = strpos($m[1], '$this->deleteOldAvatars($user)');
        $copy   = strpos($m[1], 'File::copy($uploadFile');

        $this->assertNotFalse($check);
        $this->assertLessThan($delete, $check, 'The old avatar is only deleted when the new file is good.');
        $this->assertLessThan($copy, $check);
        $this->assertStringContainsString('@unlink($uploadFile);', $m[1], 'A refused file is removed.');
    }

    public function testAttachmentUploadChecksTheImageBeforeLoadingIt(): void
    {
        $body = $this->source('site/src/Controllers/TopicController.php');

        $check = strpos($body, 'KunenaImageHelper::assertSafeFile($uploadFile)');
        $load  = strpos($body, 'new KunenaImage($uploadFile)');

        $this->assertNotFalse($check);
        $this->assertNotFalse($load);
        $this->assertLessThan($load, $check);
        $this->assertLessThan(strpos($body, 'KunenaImage::getImageFileProperties($uploadFile)'), $check);
    }

    public function testClassicAttachmentUploadChecksTheImageAndRemovesIt(): void
    {
        $source = $this->source('libraries/kunena/src/Attachment/KunenaAttachment.php');

        $this->assertSame(1, preg_match('/public function upload\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m));

        $guard = strpos($m[1], 'KunenaImageHelper::isSafeFile($uploadBasePath . $fileNameWithExt)');
        $load  = strpos($m[1], 'new KunenaImage($uploadBasePath . $fileNameWithExt)');

        $this->assertNotFalse($guard);
        $this->assertLessThan($load, $guard);
        $this->assertMatchesRegularExpression('/isSafeFile\([^)]*\)\) \{\s*File::delete\([^;]*\);\s*return false;/s', $m[1]);
    }

    public function testEveryPlaceWhichLoadsAnImageIsGuarded(): void
    {
        $unguarded = [];
        $files     = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($path, '/External/') || str_ends_with($path, '/Image/KunenaImage.php')) {
                continue;
            }

            $source = file_get_contents($path);
            $load   = strpos($source, 'new KunenaImage(');

            if ($load === false) {
                continue;
            }

            $guard = false;

            foreach (['KunenaImageHelper::isSafeFile(', 'KunenaImageHelper::assertSafeFile(', 'self::isSafeSize(', 'KunenaImageHelper::isSafeSize('] as $call) {
                $at = strpos($source, $call);

                if ($at !== false && $at < $load) {
                    $guard = true;
                }
            }

            if (!$guard) {
                $unguarded[] = substr($path, strpos($path, '/src/') + 1);
            }
        }

        $this->assertSame([], $unguarded, "These load an image into memory without checking its size first:\n" . implode("\n", $unguarded));
    }
}
