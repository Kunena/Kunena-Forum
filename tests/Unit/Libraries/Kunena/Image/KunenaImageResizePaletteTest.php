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
 * The resize of a PNG with a palette and a transparent colour (as pngquant and TinyPNG make them) keeps the whole picture. A
 * transparent rectangle of the size of the new picture was drawn on the original before the copy, so the top-left part of the
 * picture was erased in a resized attachment and in the sizes of an avatar.
 */
class KunenaImageResizePaletteTest extends UnitTestCase
{
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    public function testThePictureKeepsItsTopLeftPart(): void
    {
        $this->requireJoomla();

        // 200 x 100, a transparent background with a red box from (20, 20) to (180, 80).
        $source = sys_get_temp_dir() . '/kunena-palette-' . bin2hex(random_bytes(4)) . '.png';
        $result = $source . '.resized.png';
        $this->files = [$source, $result];

        $picture    = imagecreate(200, 100);
        $background = imagecolorallocate($picture, 0, 255, 0);
        imagecolortransparent($picture, $background);
        imagefilledrectangle($picture, 20, 20, 180, 80, imagecolorallocate($picture, 255, 0, 0));
        imagepng($picture, $source);

        $image = new KunenaImage($source);
        $image->resize(100, 50, false);
        $image->toFile($result, IMAGETYPE_PNG);

        // Half the size: (25, 20) is (50, 40) of the original, inside the box and inside the erased rectangle; (75, 35) is outside it.
        $resized = imagecreatefrompng($result);
        $pixel   = static function (int $x, int $y) use ($resized): array {
            $colour = imagecolorsforindex($resized, imagecolorat($resized, $x, $y));

            return ['red' => $colour['red'], 'green' => $colour['green'], 'opaque' => $colour['alpha'] === 0];
        };

        $this->assertSame(
            ['top left' => ['red' => 255, 'green' => 0, 'opaque' => true], 'right' => ['red' => 255, 'green' => 0, 'opaque' => true]],
            ['top left' => $pixel(25, 20), 'right' => $pixel(75, 35)]
        );
    }
}
