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

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * "Minimum number of posts to add links and images" is the number of posts which a new user needs, as the minimum of the captcha
 * and the one of the review of new users: with 10, a user of 10 posts was refused (he needed 11), and a minimum of 0 refused the
 * users without a post.
 */
class KunenaUserAllowedLinksImagesTest extends UnitTestCase
{
    public static function users(): array
    {
        return [
            'the minimum reached'    => [10, 10, true],
            'one post short'         => [9, 10, false],
            'no minimum, no post'    => [0, 0, true],
            'more than the minimum'  => [11, 10, true],
        ];
    }

    #[DataProvider('users')]
    public function testTheMinimumIsTheNumberOfPostsNeeded(int $posts, int $minimum, bool $allowed): void
    {
        $this->requireJoomla();

        $user = $this->createPartialMock(KunenaUser::class, ['isModerator', 'isAdmin']);
        $user->expects($this->once())->method('isModerator')->willReturn(false);
        $user->expects($this->once())->method('isAdmin')->willReturn(false);
        $user->posts = $posts;
        (new ReflectionProperty(KunenaUser::class, '_config'))->setValue(
            $user,
            self::bare(KunenaConfig::class, ['config' => ['newUsersPreventPostUrlImages' => 1, 'minimalUserPostsAddUrlImage' => $minimum]])
        );

        $this->assertSame($allowed, $user->checkUserAllowedLinksImages());
    }
}
