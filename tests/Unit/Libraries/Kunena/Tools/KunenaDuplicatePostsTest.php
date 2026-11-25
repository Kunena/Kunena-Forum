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

use Kunena\Forum\Libraries\Tools\KunenaDuplicatePosts as D;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Duplicate posts" groups the messages with the same text (without BBCode, case and spaces), keeps the first of
 * each group, and never takes a short text for a duplicate.
 */
class KunenaDuplicatePostsTest extends UnitTestCase
{
    private const SPAM = 'Buy the best watches in our shop today, cheap and fast.';

    public function testTheTextIsComparedWithoutItsForm(): void
    {
        $this->assertSame(D::normalize(self::SPAM), D::normalize('[b]BUY the best   watches[/b] in our shop today, cheap and fast.'));
        $this->assertSame('', D::normalize('Thanks!'));
    }

    public function testTheGroupsKeepTheFirstMessage(): void
    {
        $rows = [
            ['id' => 3, 'time' => 300, 'message' => self::SPAM],
            ['id' => 1, 'time' => 100, 'message' => self::SPAM],
            ['id' => 2, 'time' => 200, 'message' => 'Something else entirely, long enough to be compared.'],
            ['id' => 4, 'time' => 400, 'message' => strtoupper(self::SPAM)],
            ['id' => 5, 'time' => 500, 'message' => '+1'],
            ['id' => 6, 'time' => 600, 'message' => '+1'],
        ];

        $groups = D::groups($rows);

        $this->assertCount(1, $groups);
        $this->assertSame([1, 3, 4], array_column($groups[0], 'id'));
        $this->assertSame([3, 4], D::copies($groups));
    }

    public function testTheBiggestGroupComesFirst(): void
    {
        $other = 'Another message which was posted twice by mistake, sorry.';
        $rows  = [
            ['id' => 1, 'time' => 1, 'message' => $other],
            ['id' => 2, 'time' => 2, 'message' => $other],
            ['id' => 3, 'time' => 3, 'message' => self::SPAM],
            ['id' => 4, 'time' => 4, 'message' => self::SPAM],
            ['id' => 5, 'time' => 5, 'message' => self::SPAM],
        ];

        $this->assertSame([3, 4, 5], array_column(D::groups($rows)[0], 'id'));
    }
}
