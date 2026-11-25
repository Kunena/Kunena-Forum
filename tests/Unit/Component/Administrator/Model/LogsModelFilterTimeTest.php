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

namespace Kunena\Tests\Unit\Component\Administrator\Model;

use Kunena\Forum\Administrator\Model\LogsModel;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Logs of the backend with filter[time_start]=x in the address: the calendar field of the filter form stopped with "Failed to
 * parse time string". The filter is kept in the session, so the list ended in the error page on every visit until the session
 * ended.
 */
class LogsModelFilterTimeTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();
    }

    public function testATimeWhichIsNoDateIsNoFilter(): void
    {
        $this->assertSame('2026-11-25', LogsModel::filterTime('2026-11-25'));
        $this->assertSame('2026-11-25 10:30:00', LogsModel::filterTime('2026-11-25 10:30:00'));
        $this->assertSame('', LogsModel::filterTime(''));
        $this->assertSame('', LogsModel::filterTime('  '));
        $this->assertSame('', LogsModel::filterTime('x'));
        $this->assertSame('', LogsModel::filterTime("kx9'\")) OR 1=1 -- "));
        $this->assertSame('', LogsModel::filterTime('kx9"\'><svg/onload=kx9>'));
        $this->assertSame('', LogsModel::filterTime('2026-13-45'), 'A date which does not exist.');
        $this->assertSame('', LogsModel::filterTime("2026-11-25\n"));
        $this->assertSame('', LogsModel::filterTime(null));
        $this->assertSame('', LogsModel::filterTime(['2026-11-25']));
    }

    public function testTheStateAndTheSessionGetTheCheckedTime(): void
    {
        $source = Files::read('src/admin/src/Model/LogsModel.php');

        $this->assertSame(1, preg_match('/protected function populateState\(.*?\n    \}\n/s', $source, $m));

        $parent = strpos($m[0], 'parent::populateState($ordering, $direction);');
        $state  = strpos($m[0], "\$this->setState('filter.' . \$name, self::filterTime(");
        $stored = strpos($m[0], "\$app->setUserState(\$this->context . '.filter', \$stored);");

        $this->assertIsInt($parent);
        $this->assertIsInt($state, 'The state of the query has the checked time.');
        $this->assertIsInt($stored, 'The session, which fills the filter form, has it too.');
        $this->assertGreaterThan($parent, $state, 'After the filters of the form were read.');
    }
}
