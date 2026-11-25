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

use Kunena\Forum\Libraries\Tools\KunenaAccessOverview as A;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Access overview" reads the groups of a category from its access level or from its groups (with their child
 * groups when asked), and knows when everybody can read.
 */
class KunenaAccessOverviewTest extends UnitTestCase
{
    private const LEVELS = [1 => [1], 2 => [2, 6], 9 => []];

    private const TREE = [1 => 0, 2 => 1, 6 => 2, 8 => 6];

    public function testTheGroupsOfAnAccessLevel(): void
    {
        $this->assertSame([2 => true, 6 => true], A::readers(['accesstype' => 'joomla.level', 'access' => 2], self::LEVELS));
        $this->assertSame([], A::readers(['accesstype' => 'joomla.level', 'access' => 9], self::LEVELS));
        $this->assertSame([], A::readers(['accesstype' => 'joomla.level', 'access' => 77], self::LEVELS));
    }

    public function testTheGroupsOfACategoryByGroup(): void
    {
        $this->assertSame(
            [6 => true, 8 => false],
            A::readers(['accesstype' => 'joomla.group', 'pubAccess' => 6, 'pubRecurse' => 1, 'adminAccess' => 8, 'adminRecurse' => 0], self::LEVELS)
        );
        $this->assertSame([6 => true], A::readers(['accesstype' => 'joomla.group', 'pubAccess' => 6, 'pubRecurse' => 0, 'adminAccess' => 6, 'adminRecurse' => 1], self::LEVELS));
        $this->assertSame([], A::readers(['accesstype' => 'communitybuilder', 'access' => 1], self::LEVELS));
    }

    public function testEverybodyIsTheTopGroupWithItsChildren(): void
    {
        $this->assertTrue(A::everybody(A::readers(['accesstype' => 'joomla.level', 'access' => 1], self::LEVELS), self::TREE));
        $this->assertFalse(A::everybody([1 => false], self::TREE));
        $this->assertFalse(A::everybody([2 => true], self::TREE));
    }
}
