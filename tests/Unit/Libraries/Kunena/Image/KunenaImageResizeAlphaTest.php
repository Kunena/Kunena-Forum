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
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The resize of a WebP with transparent parts keeps them transparent, as the one of a PNG does: the new picture kept its alpha
 * channel for a PNG only, so a transparent WebP (a resized attachment, the sizes of an avatar) got a black background.
 */
class KunenaImageResizeAlphaTest extends UnitTestCase
{
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    public function testATransparentWebpStaysTransparent(): void
    {
        $this->requireJoomla();

        if (!\function_exists('imagewebp')) {
            $this->markTestSkipped('GD has no WebP.');
        }

        // 200 x 100, transparent, with an opaque red box from (20, 20) to (180, 80).
        $source = sys_get_temp_dir() . '/kunena-alpha-' . bin2hex(random_bytes(4)) . '.webp';
        $result = $source . '.resized.webp';
        $this->files = [$source, $result];

        $picture = imagecreatetruecolor(200, 100);
        imagealphablending($picture, false);
        imagesavealpha($picture, true);
        imagefill($picture, 0, 0, imagecolorallocatealpha($picture, 255, 255, 255, 127));
        imagefilledrectangle($picture, 20, 20, 180, 80, imagecolorallocatealpha($picture, 255, 0, 0, 0));
        imagewebp($picture, $source, 101);

        $image = new KunenaImage($source);
        $image->resize(100, 50, false);
        $image->toFile($result, IMAGETYPE_WEBP, ['quality' => 101]);

        // Half the size: (2, 2) is in the transparent border, (50, 25) in the box.
        $resized = imagecreatefromwebp($result);
        $alpha   = static fn (int $x, int $y): int => (imagecolorat($resized, $x, $y) >> 24) & 0x7F;

        $this->assertSame(['border' => 127, 'box' => 0], ['border' => $alpha(2, 2), 'box' => $alpha(50, 25)]);
    }
}
