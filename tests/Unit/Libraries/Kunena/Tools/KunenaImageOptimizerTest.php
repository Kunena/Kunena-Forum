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

use Kunena\Forum\Libraries\Tools\KunenaImageOptimizer as I;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Optimize images" fits an image into the size of the settings, never makes it bigger, and renames a file for
 * WebP without touching a private file (which has no extension).
 */
class KunenaImageOptimizerTest extends UnitTestCase
{
    public function testAnImageFitsIntoTheBox(): void
    {
        $this->assertSame([800, 600], I::fit(4000, 3000, 800, 800));
        $this->assertSame([600, 800], I::fit(3000, 4000, 800, 800));
        $this->assertSame([800, 200], I::fit(1600, 400, 800, 800));
    }

    public function testASmallImageKeepsItsSize(): void
    {
        $this->assertSame([640, 480], I::fit(640, 480, 800, 800));
        $this->assertSame([0, 0], I::fit(0, 0, 800, 800));
    }

    public function testTheNameForWebp(): void
    {
        $this->assertSame('photo.webp', I::rename('photo.jpg', 'webp'));
        $this->assertSame('my.holiday.webp', I::rename('my.holiday.png', 'webp'));
        $this->assertSame('0123456789abcdef0123456789abcdef', I::rename('0123456789abcdef0123456789abcdef', 'webp'));
    }
}
