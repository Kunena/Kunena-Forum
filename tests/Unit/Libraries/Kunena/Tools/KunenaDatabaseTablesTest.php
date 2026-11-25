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

use Kunena\Forum\Libraries\Tools\KunenaDatabaseTables;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Database tables" puts only the names of the tables of Kunena in its SQL.
 */
class KunenaDatabaseTablesTest extends UnitTestCase
{
    public function testOnlyTheKnownTablesReachTheSql(): void
    {
        $known = ['jos_kunena_messages' => [], 'jos_kunena_logs' => []];

        $this->assertSame(
            ['jos_kunena_logs'],
            KunenaDatabaseTables::filterNames(['jos_kunena_logs', 'jos_users', 'jos_kunena_logs`; DROP TABLE x; --', 5, 'jos_kunena_logs'], $known)
        );
    }
}
