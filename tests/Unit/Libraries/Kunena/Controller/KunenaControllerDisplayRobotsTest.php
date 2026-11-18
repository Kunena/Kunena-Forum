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

namespace Kunena\Tests\Unit\Libraries\Kunena\Controller;

use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A thin page (an empty list, a profile without a post) is kept out of the index: its links are still followed, unless the robots
 * of the site or of the menu item say "nofollow".
 */
class KunenaControllerDisplayRobotsTest extends UnitTestCase
{
    public static function robots(): array
    {
        return [
            'no robots'         => ['', 'noindex, follow'],
            'index, follow'     => ['index, follow', 'noindex, follow'],
            'index, nofollow'   => ['index, nofollow', 'noindex, nofollow'],
            'noindex, follow'   => ['noindex, follow', 'noindex, follow'],
            'noindex, nofollow' => ['noindex, nofollow', 'noindex, nofollow'],
            'upper case'        => ['INDEX, NOFOLLOW', 'noindex, nofollow'],
        ];
    }

    #[DataProvider('robots')]
    public function testAThinPageIsOutOfTheIndex(string $robots, string $expected): void
    {
        $this->assertSame($expected, KunenaControllerDisplay::robotsOutOfIndex($robots));
    }
}
