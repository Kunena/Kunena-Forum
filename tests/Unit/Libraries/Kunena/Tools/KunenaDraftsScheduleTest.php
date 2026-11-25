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

use Kunena\Forum\Libraries\Tools\KunenaDraftsSchedule as D;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Drafts and scheduled replies" finds the drafts by their last change: a draft saved again is not old.
 */
class KunenaDraftsScheduleTest extends UnitTestCase
{
    public function testTheLastChangeOfADraft(): void
    {
        $this->assertSame(500, D::lastChange(['created_time' => 100, 'modified_time' => 500]));
        $this->assertSame(100, D::lastChange(['created_time' => 100, 'modified_time' => 0]));
        $this->assertSame(0, D::lastChange([]));
    }

    public function testTheAgesOfTheFilter(): void
    {
        $this->assertSame(0, D::AGES[0], 'The first choice shows all the drafts.');
        $this->assertSame(D::AGES, array_values(array_unique(D::AGES)));
    }
}
