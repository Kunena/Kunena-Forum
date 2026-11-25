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

use Kunena\Forum\Libraries\Tools\KunenaInactiveUsers;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Inactive accounts" takes only known criteria from the request.
 */
class KunenaInactiveUsersTest extends UnitTestCase
{
    public function testTheCriteriaOfTheInactiveAccountsTakeOnlyKnownValues(): void
    {
        $this->assertSame(['kind' => 'never_posted', 'months' => 12], KunenaInactiveUsers::criteria([]));
        $this->assertSame(['kind' => 'not_seen', 'months' => 36], KunenaInactiveUsers::criteria(['kind' => 'not_seen', 'months' => '36']));
        $this->assertSame(['kind' => 'never_posted', 'months' => 12], KunenaInactiveUsers::criteria(['kind' => 'all', 'months' => 0]));
    }
}
