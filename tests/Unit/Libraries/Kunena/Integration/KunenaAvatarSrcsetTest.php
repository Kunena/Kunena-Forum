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

namespace Kunena\Tests\Unit\Libraries\Kunena\Integration;

use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Plugin\Kunena\Kunena\Helper\KunenaAvatarKunena;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The avatars on the screens with more pixels for each point (most phones and laptops): the image of the size of the layout was
 * stretched there, and the small default avatar (48 pixels) was used up to 90 pixels. An avatar has a srcset with the images for 2
 * and 3 times the pixels, and the small default avatar is used only when it is large enough.
 */
class KunenaAvatarSrcsetTest extends UnitTestCase
{
    public function testThreeDensities(): void
    {
        $this->assertSame(
            ' srcset="a/36.png?1 1x, a/72.png?1 2x, a/108.png?1 3x"',
            KunenaAvatar::srcset([1 => 'a/36.png?1', 2 => 'a/72.png?1', 3 => 'a/108.png?1'])
        );
    }

    public static function single(): array
    {
        return [
            'one address for every size (svg, an integration with one size)' => [[1 => 'a.svg', 2 => 'a.svg', 3 => 'a.svg']],
            'only the first address'                                          => [[1 => 'a.png', 2 => '', 3 => false]],
            'nothing'                                                         => [[]],
        ];
    }

    #[DataProvider('single')]
    public function testOneAddressIsNoSrcset(array $urls): void
    {
        $this->assertSame('', KunenaAvatar::srcset($urls));
    }

    public function testTheSameAddressTwiceIsGivenOnce(): void
    {
        // An image which is not made larger than its original (100 pixels) for 2x and 3x.
        $this->assertSame(' srcset="s.png 1x, n.png 2x"', KunenaAvatar::srcset([3 => 'n.png', 1 => 's.png', 2 => 'n.png']));
    }

    public function testTheAddressesAreEncoded(): void
    {
        $srcset = KunenaAvatar::srcset([1 => 'a.png?x=1&y="2"', 2 => 'b c,d.png']);

        $this->assertSame(' srcset="a.png?x=1&amp;y=&quot;2&quot; 1x, b%20c%2Cd.png 2x"', $srcset);
    }

    public function testTheLinkHasTheSrcset(): void
    {
        $source = Files::read('src/libraries/kunena/src/Integration/KunenaAvatar.php');

        $this->assertStringContainsString('2 => $this->getURL($user, $size->x * 2, $size->y * 2)', $source);
        $this->assertStringContainsString('3 => $this->getURL($user, $size->x * 3, $size->y * 3)', $source);
        $this->assertSame(2, substr_count($source, "src=\"' . \$avatar . '\"' . \$srcset . '"));
    }

    public static function defaults(): array
    {
        // width asked for, width of the small default avatar, small used
        return [
            'smaller than the small avatar'  => [36, 48, true],
            'as large as the small avatar'   => [48, 48, true],
            'larger: the large avatar'       => [72, 48, false],
            'the width is not known: 90'     => [90, 0, true],
            'the width is not known: above'  => [91, 0, false],
        ];
    }

    #[DataProvider('defaults')]
    public function testTheSmallDefaultAvatarIsNotStretched(int $sizex, int $smallWidth, bool $small): void
    {
        require_once Files::path('src/plugins/plg_kunena_kunena/src/Helper/KunenaAvatarKunena.php');

        $this->assertSame($small, KunenaAvatarKunena::usesSmallDefault($sizex, $smallWidth));
    }
}
