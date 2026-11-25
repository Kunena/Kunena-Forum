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

use Kunena\Forum\Libraries\Tools\KunenaUnansweredTopics as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Unanswered topics" sums the unanswered topics per category, the most first.
 */
class KunenaUnansweredTopicsTest extends UnitTestCase
{
    public function testTheCategoriesWithTheMostFirst(): void
    {
        $report = T::summarize([
            ['category_id' => 2, 'count' => 1, 'oldest' => 500],
            ['category_id' => 3, 'count' => 4, 'oldest' => 300],
            ['category_id' => 4, 'count' => 1, 'oldest' => 100],
            ['category_id' => 5, 'count' => 0, 'oldest' => 0],
        ], [2 => 'News', 3 => 'Help']);

        $this->assertSame(6, $report['total']);
        $this->assertSame(100, $report['oldest']);
        $this->assertSame([3, 4, 2], array_column($report['categories'], 'id'));
        $this->assertSame(['Help', '#4', 'News'], array_column($report['categories'], 'name'));
    }

    public function testNoUnansweredTopics(): void
    {
        $this->assertSame(['total' => 0, 'oldest' => 0, 'categories' => []], T::summarize([], []));
    }
}
