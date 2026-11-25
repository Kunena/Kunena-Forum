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

use Kunena\Forum\Libraries\Tools\KunenaAvatarFiles as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Avatar files" finds the avatars which are broken, gone or too big.
 */
class KunenaAvatarFilesTest extends UnitTestCase
{
    public function testTheProblemsOfTheAvatars(): void
    {
        $sizes = ['users/avatar2.jpg' => 900, 'users/avatar3.png' => 5000, 'gallery/cat.png' => 9000];
        $items = T::check([
            ['userid' => 1, 'username' => 'a', 'avatar' => ''],
            ['userid' => 2, 'username' => 'b', 'avatar' => 'users/avatar2.jpg'],
            ['userid' => 3, 'username' => 'c', 'avatar' => 'users/avatar3.png'],
            ['userid' => 4, 'username' => 'd', 'avatar' => 'users/avatar4.jpg'],
            ['userid' => 5, 'username' => 'e', 'avatar' => '../../configuration.php'],
            ['userid' => 6, 'username' => 'f', 'avatar' => 'gallery/cat.png'],
        ], static fn (string $avatar) => $sizes[$avatar] ?? null, 1024);

        // A big picture of the gallery is not an upload: the limit is for uploads.
        $this->assertSame([5 => 'invalid', 4 => 'missing', 3 => 'large'], array_column($items, 'problem', 'userid'));
        $this->assertSame(5000, $items[2]['size']);
    }

    public function testNoLimit(): void
    {
        $this->assertSame([], T::check([['userid' => 3, 'username' => 'c', 'avatar' => 'users/avatar3.png']], static fn () => 99999, 0));
    }
}
