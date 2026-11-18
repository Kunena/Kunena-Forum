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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A link to a profile around an avatar gets the class kunena-imagelink: a box around the whole image, so that its tooltip is above
 * the image (inline, the tooltip came over the image under the pointer and flickered). A name, also in a span, is text.
 */
class KunenaUserImageLinkTest extends UnitTestCase
{
    public static function contents(): array
    {
        return [
            'an avatar'                 => ['<img loading=lazy class="rounded-circle" src="a.png" alt="" />', true],
            'an avatar after spaces'    => ["\n\t<img src=\"a.png\" alt=\"\">", true],
            'a picture'                 => ['<picture><img src="a.png" alt=""></picture>', true],
            'the icon of no avatar'     => ['<span class="kavatar d-inline-block lh-1" title="x"><svg></svg></span>', true],
            'the svg avatar of a set'   => ['<span class="rounded kavatar" data-bs-toggle="tooltip"><img src="a.svg" alt=""></span>', true],
            'an avatar in a span'       => ["<span class=\"rounded\" data-bs-toggle=\"tooltip\" title=\"x\">\n\t\t<img alt=\"\" src=\"a.jpg\"></span>", true],
            'a name'                    => ['Boss Jelle', false],
            'a name in a span'          => ['<span class="profile" data-bs-toggle="tooltip">Boss Jelle</span>', false],
            'a name before an image'    => ['Boss Jelle <img src="a.png" alt="">', false],
            'a class which only starts' => ['<span class="kavatars-list">x</span>', false],
        ];
    }

    #[DataProvider('contents')]
    public function testAnImageIsKnown(string $content, bool $expected): void
    {
        $this->assertSame($expected, KunenaUser::isImageContent($content));
    }
}
