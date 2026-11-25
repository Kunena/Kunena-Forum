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

use Kunena\Forum\Libraries\Tools\KunenaReactionsOverview as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Reactions overview" finds the users who react far more than they post.
 */
class KunenaReactionsOverviewTest extends UnitTestCase
{
    public function testTheUsersWhoReactFarMoreThanTheyPost(): void
    {
        $suspects = T::suspects([
            ['user_id' => 2, 'reactions' => 60, 'posts' => 0, 'username' => 'bot'],
            ['user_id' => 3, 'reactions' => 200, 'posts' => 19, 'username' => 'fan'],
            ['user_id' => 4, 'reactions' => 200, 'posts' => 21, 'username' => 'writer'],
            ['user_id' => 5, 'reactions' => 49, 'posts' => 0, 'username' => 'new'],
            ['user_id' => 0, 'reactions' => 500, 'posts' => 0, 'username' => null],
        ]);

        $this->assertSame([3, 2], array_column($suspects, 'user'));
        $this->assertEqualsWithDelta(200 / 19, $suspects[0]['ratio'], 0.001);
    }
}
