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
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The address of an avatar (Kunena 8.0): a page shows the avatar of a user many times, at three pixel densities, and every
 * address looked on the disk for the picture and its resized copy. It is made once in a request for a user, a file and a size.
 */
class KunenaAvatarUrlCacheTest extends UnitTestCase
{
    /**
     * An integration which counts the addresses it makes.
     */
    private function integration(): KunenaAvatar
    {
        return new class () extends KunenaAvatar {
            public int $made = 0;

            protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
            {
                $this->made++;

                return "https://example.com/{$user->avatar}/{$sizex}x{$sizey}";
            }
        };
    }

    private function user(string $avatar = 'users/avatar1.png', int $timestamp = 100): KunenaUser
    {
        return self::bare(KunenaUser::class, ['userid' => 62, 'avatar' => $avatar, 'timestamp' => $timestamp]);
    }

    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    public function testTheAddressOfAUserAndASizeIsMadeOnce(): void
    {
        $avatars = $this->integration();
        $user    = $this->user();

        $this->assertSame('https://example.com/users/avatar1.png/36x36', $avatars->getURL($user, 36, 36));
        $this->assertSame('https://example.com/users/avatar1.png/36x36', $avatars->getURL($user, 36, 36));
        $this->assertSame(1, $avatars->made);
    }

    public function testEachSizeHasItsOwnAddress(): void
    {
        $avatars = $this->integration();
        $user    = $this->user();

        $this->assertSame('https://example.com/users/avatar1.png/36x36', $avatars->getURL($user, 36, 36));
        $this->assertSame('https://example.com/users/avatar1.png/72x72', $avatars->getURL($user, 72, 72));
        $this->assertSame(2, $avatars->made);
    }

    public function testANewAvatarOfTheUserGetsANewAddress(): void
    {
        $avatars = $this->integration();
        $user    = $this->user();

        $avatars->getURL($user, 36, 36);

        $user->avatar    = 'users/avatar2.png';
        $user->timestamp = 200;

        $this->assertSame('https://example.com/users/avatar2.png/36x36', $avatars->getURL($user, 36, 36));
        $this->assertSame(2, $avatars->made);
    }
}
