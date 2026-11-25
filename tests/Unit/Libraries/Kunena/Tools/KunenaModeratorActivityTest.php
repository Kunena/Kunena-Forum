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

use Kunena\Forum\Libraries\Tools\KunenaModeratorActivity as M;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Moderator activity" sums the actions of the log per moderator, the most active first.
 */
class KunenaModeratorActivityTest extends UnitTestCase
{
    public function testTheActionsArePerModerator(): void
    {
        $users = M::summarize([
            ['user_id' => 7, 'operation' => 'LOG_POST_APPROVE', 'count' => 2, 'last' => 100],
            ['user_id' => 9, 'operation' => 'LOG_TOPIC_LOCK', 'count' => 1, 'last' => 300],
            ['user_id' => 7, 'operation' => 'LOG_POST_DELETE', 'count' => 5, 'last' => 200],
        ]);

        $this->assertSame([7, 9], array_keys($users));
        $this->assertSame(7, $users[7]['total']);
        $this->assertSame(200, $users[7]['last']);
        $this->assertSame(['LOG_POST_DELETE' => 5, 'LOG_POST_APPROVE' => 2], $users[7]['operations']);
    }

    public function testNoActions(): void
    {
        $this->assertSame([], M::summarize([]));
    }
}
