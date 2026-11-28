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

namespace Kunena\Tests\Unit\Libraries\Kunena\Image;

use Kunena\Forum\Libraries\Image\KunenaImage;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The orientation of the camera is read from a JPEG only: on a PNG (a GIF, a WebP) the upload which resizes the picture gave the
 * warning "File not supported", which, shown, broke the answer of the upload. Without the extension exif of PHP (Joomla does not
 * need it) the picture is not turned, and not refused: every picture which the server had to resize was refused.
 */
class KunenaImageOrientationTest extends UnitTestCase
{
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    public function testAPngGivesNoWarning(): void
    {
        $this->requireJoomla();

        if (!\function_exists('exif_read_data')) {
            $this->markTestSkipped('PHP has no exif.');
        }

        $source        = sys_get_temp_dir() . '/kunena-orientation-' . bin2hex(random_bytes(4)) . '.png';
        $this->files[] = $source;
        imagepng(imagecreatetruecolor(20, 10), $source);

        $warnings = [];
        set_error_handler(static function (int $level, string $text) use (&$warnings): bool {
            $warnings[] = $text;

            return true;
        });

        try {
            (new KunenaImage($source))->correctImageOrientation();
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $warnings);
    }

    public function testWithoutExifThePictureIsNotRefused(): void
    {
        $source = Files::read('src/libraries/kunena/src/Image/KunenaImage.php');

        $this->assertStringContainsString("if (!\\function_exists('exif_read_data') || @exif_imagetype(\$this->getPath()) !== IMAGETYPE_JPEG) {\n            return;", str_replace("\r\n", "\n", $source));
        $this->assertStringNotContainsString('exif_read_data() method not available', $source);
    }
}
