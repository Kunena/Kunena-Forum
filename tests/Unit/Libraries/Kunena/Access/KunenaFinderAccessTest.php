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

namespace Kunena\Tests\Unit\Libraries\Kunena\Access;

use Kunena\Forum\Libraries\Access\KunenaFinderAccess;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The access level which Smart Search stores for a message (Kunena 7.1): the id of a view level. A category of a user group got 0,
 * a level which nobody has, so its messages were not found by the users who may read them either.
 */
class KunenaFinderAccessTest extends UnitTestCase
{
    /**
     * The default view levels of Joomla: id => groups.
     */
    private const LEVELS = [
        1 => [1],
        2 => [6, 2, 8],
        3 => [6, 3, 8],
        5 => [1],
        6 => [3, 4, 5, 8],
    ];

    public static function levels(): array
    {
        return ['public' => [1], 'registered' => [2], 'special' => [3], 'custom' => [6]];
    }

    #[DataProvider('levels')]
    public function testACategoryOfAViewLevelHasThatLevel(int $access): void
    {
        $this->assertSame($access, KunenaFinderAccess::viewLevel('joomla.level', $access, 0, self::LEVELS));
    }

    public function testALevelWhichDoesNotExistIsShownToNobody(): void
    {
        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.level', 99, 0, self::LEVELS));
        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.level', 2, 0, []));
    }

    /**
     * The user groups of a new Joomla site: id => parent. Super Users (8) are the administrators of the site.
     */
    private const PARENTS = [1 => 0, 9 => 1, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 2, 7 => 6, 8 => 1];

    /**
     * The view levels of a new Joomla site: Public, Registered, Special, Guest and Super Users.
     */
    private const JOOMLA_LEVELS = [1 => [1], 2 => [6, 2, 8], 3 => [6, 3, 8], 5 => [9], 6 => [8]];

    private static function group(int $group, bool $recurse = true, int $admin = 0, bool $adminRecurse = true): int
    {
        return KunenaFinderAccess::viewLevel('joomla.group', 0, $group, self::JOOMLA_LEVELS, [
            'parents'      => self::PARENTS,
            'always'       => [8],
            'pubRecurse'   => $recurse,
            'adminAccess'  => $admin,
            'adminRecurse' => $adminRecurse,
        ]);
    }

    public static function groupCategories(): array
    {
        return [
            // Everybody reads it: the level Public.
            'public and the groups under it' => [1, true, 0, true, 1],
            // Registered with the groups under it (Manager, Administrator) and the Super Users: the level Registered.
            'registered and the groups under it' => [2, true, 0, true, 2],
            // Registered only: every level with Registered gives it to Author, Manager...: only the level of the Super Users.
            'registered only' => [2, false, 0, true, 6],
            // Author and the groups under it: Special has Manager, who may not read it (it was given Special before).
            'author and the groups under it' => [3, true, 0, true, 6],
            // Author, and Manager as the admin group: Special (Manager, Author, Super Users) fits now.
            'author with manager as the admin group' => [3, true, 6, true, 3],
        ];
    }

    #[DataProvider('groupCategories')]
    public function testAGroupGetsALevelWhichOnlyItsReadersHave(int $group, bool $recurse, int $admin, bool $adminRecurse, int $level): void
    {
        $this->assertSame($level, self::group($group, $recurse, $admin, $adminRecurse));
    }

    public function testWithoutALevelForItsReadersAGroupIsShownToNobody(): void
    {
        // A site without the level of the Super Users: Registered only fits no level.
        $levels = self::JOOMLA_LEVELS;
        unset($levels[6]);

        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.group', 0, 2, $levels, ['parents' => self::PARENTS, 'always' => [8], 'pubRecurse' => false]));
    }

    public function testTheGroupsOfTheRulesAreNumbersOrStrings(): void
    {
        $this->assertSame(7, KunenaFinderAccess::viewLevel('joomla.group', 0, 2, [7 => ['2']]));
    }

    public function testAGroupWithoutALevelAndOtherAccessAreShownToNobody(): void
    {
        // A group which the site has not: only the administrators, who read every category.
        $this->assertSame(6, self::group(42));

        foreach (['none', 'communitybuilder', ''] as $type) {
            $this->assertSame(0, KunenaFinderAccess::viewLevel($type, 2, 2, self::LEVELS), $type);
        }
    }

    public function testThePluginUsesTheRule(): void
    {
        $source = Files::read('src/plugins/plg_finder_kunena/src/Extension/Kunena.php');

        $this->assertSame(1, preg_match('/protected function getAccessLevel\(\$catid\): int\s*\{(.*?)\n    \}/s', $source, $m));
        $this->assertStringContainsString('KunenaFinderAccess::viewLevel(', $m[1]);
        $this->assertStringContainsString("from(\$db->quoteName('#__viewlevels'))", $m[1]);
        $this->assertStringNotContainsString('return 0;', $m[1]);
    }
}
