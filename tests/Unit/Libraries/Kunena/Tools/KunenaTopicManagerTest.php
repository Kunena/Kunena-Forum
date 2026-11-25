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

use Kunena\Forum\Libraries\Tools\KunenaTopicManager as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Topic manager" takes only known filters from the request, and no unknown action.
 */
class KunenaTopicManagerTest extends UnitTestCase
{
    public function testTheDefaultFilters(): void
    {
        $this->assertSame(
            ['category' => 0, 'state' => 'all', 'age' => 0, 'author' => '', 'noreplies' => false, 'search' => '', 'page' => 1, 'limit' => 50],
            T::filters([])
        );
    }

    public function testOnlyAKnownNumberOfTopicsPerPage(): void
    {
        $this->assertSame(100, T::filters(['limit' => '100'])['limit']);
        $this->assertSame(T::PAGE_SIZE, T::filters(['limit' => '100000'])['limit']);
        $this->assertSame(T::PAGE_SIZE, T::filters(['limit' => '-1'])['limit']);

        // The default is left out of the address, another number is kept.
        $this->assertSame([], T::active(T::filters(['limit' => '50'])));
        $this->assertSame(['limit' => 20], T::active(T::filters(['limit' => '20'])));
    }

    public function testThePagesOfThePagination(): void
    {
        $this->assertSame([1], T::pageWindow(1, 1));
        $this->assertSame([1, 2, 3, 4, 5], T::pageWindow(1, 5));
        $this->assertSame([1, 2, 3, null, 10], T::pageWindow(1, 10));
        $this->assertSame([1, null, 4, 5, 6, 7, 8, null, 20], T::pageWindow(6, 20));
        $this->assertSame([1, null, 8, 9, 10], T::pageWindow(10, 10));

        // One page between the first one and the window is shown, not a gap.
        $this->assertSame([1, 2, 3, 4, 5, 6, null, 10], T::pageWindow(4, 10));
    }

    public function testOnlyKnownValues(): void
    {
        $filters = T::filters(['category' => '-4', 'state' => 'everything', 'age' => '45', 'page' => '0', 'author' => ' jane ', 'noreplies' => '1']);

        $this->assertSame(0, $filters['category']);
        $this->assertSame('all', $filters['state']);
        $this->assertSame(0, $filters['age']);
        $this->assertSame(1, $filters['page']);
        $this->assertSame('jane', $filters['author']);
        $this->assertTrue($filters['noreplies']);
        $this->assertSame(365, T::filters(['age' => '365'])['age']);
        $this->assertSame('sticky', T::filters(['state' => 'sticky'])['state']);
    }

    public function testTheAddressKeepsOnlyTheFiltersWhichAreSet(): void
    {
        $this->assertSame([], T::active(T::filters([])));
        $this->assertSame(
            ['category' => 4, 'state' => 'locked', 'noreplies' => true, 'page' => 3],
            T::active(T::filters(['category' => '4', 'state' => 'locked', 'noreplies' => '1', 'page' => '3']))
        );
    }

    public function testAnUnknownActionChangesNothing(): void
    {
        $this->assertSame(['done' => 0, 'failed' => 0], T::apply([1, 2], 'purge'));
    }

    public function testTheModerationActions(): void
    {
        // Approve and delete are actions of the list; delete only takes topics of the trash (KunenaTopicManager::apply()).
        $this->assertSame(['approve', 'lock', 'unlock', 'stick', 'unstick', 'move', 'trash', 'restore', 'delete'], T::ACTIONS);
    }

    public function testAMoveWithoutACategoryChangesNothing(): void
    {
        $this->assertSame(['done' => 0, 'failed' => 2], T::apply([1, 2], 'move', 0));
    }
}
