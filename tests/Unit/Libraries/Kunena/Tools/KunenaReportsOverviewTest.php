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

use Kunena\Forum\Libraries\Tools\KunenaReportsOverview as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Reports overview" counts the reports per user and reads a report of the log.
 */
class KunenaReportsOverviewTest extends UnitTestCase
{
    public function testTheUsersWithTheMostReports(): void
    {
        $rows = [
            ['user_id' => 5, 'target_user' => 9],
            ['user_id' => 5, 'target_user' => 8],
            ['user_id' => 6, 'target_user' => 9],
            ['user_id' => 0, 'target_user' => 0],
        ];

        $this->assertSame([5 => 2, 6 => 1], T::top($rows, 'user_id'));
        $this->assertSame([9 => 2, 8 => 1], T::top($rows, 'target_user'));
        $this->assertSame([5 => 2], T::top($rows, 'user_id', 1));
    }

    public function testAReportOfTheLog(): void
    {
        $entry = T::entry(['id' => 3, 'time' => 100, 'user_id' => 5, 'target_user' => 9, 'topic_id' => 7,
            'data'              => json_encode(['mesid' => 12, 'reason' => ' Spam ', 'message' => 'Selling shoes'])]);

        $this->assertSame(['id' => 3, 'time' => 100, 'reporter' => 5, 'author' => 9, 'topic' => 7, 'message' => 12, 'reason' => 'Spam', 'text' => 'Selling shoes'], $entry);
        $this->assertSame(0, T::entry(['id' => 4, 'time' => 1, 'user_id' => 1, 'target_user' => 2, 'topic_id' => 3, 'data' => 'broken'])['message']);
    }
}
