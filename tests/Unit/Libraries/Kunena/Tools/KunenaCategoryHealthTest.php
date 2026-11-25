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

use Kunena\Forum\Libraries\Tools\KunenaCategoryHealth as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Category health" finds the categories which are no longer used.
 */
class KunenaCategoryHealthTest extends UnitTestCase
{
    private const NOW = 1_800_000_000;

    private static function row(int $id, int $parent, string $name, int $published, int $topics, int $last): array
    {
        return ['id' => $id, 'parentid' => $parent, 'name' => $name, 'published' => $published, 'numTopics' => $topics, 'last_post_time' => $last];
    }

    public function testTheProblemsOfTheCategories(): void
    {
        $items = T::check([
            self::row(1, 0, 'Section', 1, 0, 0),
            self::row(2, 1, 'Empty', 1, 0, 0),
            self::row(3, 1, 'Quiet', 1, 4, self::NOW - 400 * 86400),
            self::row(4, 1, 'Hidden', 0, 2, self::NOW),
            self::row(5, 1, 'Busy', 1, 9, self::NOW - 86400),
            self::row(6, 1, 'Parent', 1, 0, 0),
            self::row(7, 6, 'Hidden empty', 0, 0, 0),
        ], 365, self::NOW);

        $this->assertSame([2 => 'empty', 3 => 'quiet', 4 => 'unpublished'], array_column($items, 'problem', 'id'));
        $this->assertSame('Section', $items[0]['path']);
    }

    public function testThePathOfACategory(): void
    {
        $byId = [1 => self::row(1, 0, 'A', 1, 0, 0), 2 => self::row(2, 1, 'B', 1, 0, 0), 3 => self::row(3, 2, 'C', 1, 0, 0)];

        $this->assertSame('A › B', T::path($byId, 3));
        $this->assertSame('', T::path($byId, 1));
    }

    public function testALoopInTheParentsEnds(): void
    {
        $byId = [1 => self::row(1, 2, 'A', 1, 0, 0), 2 => self::row(2, 1, 'B', 1, 0, 0)];

        $this->assertSame('A › B', T::path($byId, 1));
    }
}
