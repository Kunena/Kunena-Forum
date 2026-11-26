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

use Kunena\Forum\Libraries\Tools\KunenaRecountProgress as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The progress of the recount goes over the chosen steps only, and never past the end.
 */
class KunenaRecountProgressTest extends UnitTestCase
{
    public function testTheProgressOverTheChosenSteps(): void
    {
        $all = ['topics' => true, 'usertopics' => true, 'categories' => true, 'users' => true, 'polls' => true];

        // 1000 topics: two steps of 1000, three of 100 (a tenth): 2300 in all.
        $this->assertSame(0.0, T::percent($all, 0, 0, 1000));
        $this->assertSame(21.7, T::percent($all, 0, 501, 1000));
        $this->assertSame(43.5, T::percent($all, 1, 0, 1000));
        $this->assertSame(95.7, T::percent($all, 4, 0, 1000));
        $this->assertSame(100.0, T::percent($all, 5, 0, 1000));
    }

    public function testAStepWhichWasNotChosenCountsForNothing(): void
    {
        $users = ['topics' => false, 'usertopics' => false, 'categories' => false, 'users' => true, 'polls' => false];

        $this->assertSame(0.0, T::percent($users, 0, 0, 1000));
        $this->assertSame(0.0, T::percent($users, 3, 0, 1000));
        $this->assertSame(100.0, T::percent($users, 4, 0, 1000));
        $this->assertSame(100.0, T::percent([], 0, 0, 1000));
    }

    public function testABatchStartPastTheLastTopicIsNotPastTheEnd(): void
    {
        $this->assertSame(50.0, T::percent(['topics' => true, 'usertopics' => true], 0, 99999, 1000));
    }

    public function testTheBatchesDoNotOverlap(): void
    {
        // BETWEEN includes both ends: the next batch starts after the end of this one.
        $this->assertSame(T::BATCH, T::batchEnd(0));
        $this->assertSame(T::BATCH, T::batchEnd(1));
        $this->assertSame(2 * T::BATCH, T::batchEnd(T::batchEnd(1) + 1));
    }
}
