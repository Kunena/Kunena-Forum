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

use Kunena\Forum\Libraries\Tools\KunenaHealthSummary as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Forum health summary" puts the checks of the other tools in order, the worst first.
 */
class KunenaHealthSummaryTest extends UnitTestCase
{
    public function testTheLevelOfTheChecksOfAnAudit(): void
    {
        $this->assertSame([0, 'ok'], T::fromChecks([['a', 'ok'], ['b', 'info']]));
        $this->assertSame([2, 'warning'], T::fromChecks([['a', 'warning'], ['b', 'ok'], ['c', 'warning']]));
        $this->assertSame([2, 'problem'], T::fromChecks([['a', 'warning'], ['b', 'problem']]));
    }

    public function testAnEntryWithoutFindingsIsInOrder(): void
    {
        $this->assertSame(['tool' => 'avatarfiles', 'count' => 0, 'level' => 'ok'], T::entry('avatarfiles', 0, 'warning'));
        $this->assertSame('warning', T::entry('avatarfiles', 3, 'warning')['level']);
    }

    public function testTheWorstFirst(): void
    {
        $summary = T::summarize([
            T::entry('unanswered', 9, 'info'),
            T::entry('banreview', 2, 'warning'),
            ['tool' => 'overrides', 'count' => 0, 'level' => 'error', 'error' => 'Broken'],
            T::entry('security', 1, 'problem'),
            T::entry('missingfiles', 5, 'warning'),
            T::entry('rankladder', 0, 'info'),
        ]);

        $this->assertSame('error', $summary['level']);
        $this->assertSame(['overrides', 'security', 'missingfiles', 'banreview', 'unanswered', 'rankladder'], array_column($summary['entries'], 'tool'));
        $this->assertSame(['error' => 1, 'problem' => 1, 'warning' => 2, 'info' => 1, 'ok' => 1], $summary['counts']);
    }

    public function testOnlyInformationIsInOrder(): void
    {
        $this->assertSame('ok', T::summarize([T::entry('unanswered', 9, 'info'), T::entry('banreview', 0, 'warning')])['level']);
    }

    public function testEveryCheckHasATitle(): void
    {
        $this->assertSame([], array_diff(array_keys(T::checks()), array_keys(T::TOOLS)));
    }
}
