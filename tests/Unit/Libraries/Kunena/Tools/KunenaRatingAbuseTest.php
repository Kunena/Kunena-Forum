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

use Kunena\Forum\Libraries\Tools\KunenaRatingAbuse as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Rating abuse" finds the users who give every topic the same score or rate one author only down or up.
 */
class KunenaRatingAbuseTest extends UnitTestCase
{
    public function testTheUsersWhoGiveTheSameScore(): void
    {
        $result = T::sameScore([
            ['userid' => 2, 'rate' => 1, 'count' => 19],
            ['userid' => 2, 'rate' => 3, 'count' => 1],
            ['userid' => 3, 'rate' => 5, 'count' => 15],
            ['userid' => 3, 'rate' => 4, 'count' => 5],
            ['userid' => 4, 'rate' => 5, 'count' => 10],
            ['userid' => 0, 'rate' => 5, 'count' => 50],
        ]);

        // User 3: 75% the same; user 4: too few ratings; a guest is not checked.
        $this->assertCount(1, $result);
        $this->assertSame(['user' => 2, 'ratings' => 20, 'score' => 1, 'share' => 0.95], $result[0]);
    }

    public function testTheUsersWhoTargetAnAuthor(): void
    {
        $result = T::targeted([
            ['userid' => 2, 'author' => 9, 'ratings' => 6, 'low' => 6, 'high' => 0],
            ['userid' => 3, 'author' => 9, 'ratings' => 8, 'low' => 0, 'high' => 8],
            ['userid' => 4, 'author' => 9, 'ratings' => 6, 'low' => 5, 'high' => 0],
            ['userid' => 5, 'author' => 9, 'ratings' => 4, 'low' => 4, 'high' => 0],
            ['userid' => 9, 'author' => 9, 'ratings' => 9, 'low' => 0, 'high' => 9],
        ]);

        $this->assertSame([[3, 'up'], [2, 'down']], array_map(static fn ($row) => [$row['user'], $row['kind']], $result));
    }
}
