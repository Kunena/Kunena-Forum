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

    public function testAGroupGetsTheNarrowestLevelOfIt(): void
    {
        // Registered (2) is read by the level Registered; Public (1) by two levels of it alone: the lowest id.
        $this->assertSame(2, KunenaFinderAccess::viewLevel('joomla.group', 0, 2, self::LEVELS));
        $this->assertSame(1, KunenaFinderAccess::viewLevel('joomla.group', 0, 1, self::LEVELS));
        // Author (3) is in Special (3 groups) and 6 (4 groups): the narrower one.
        $this->assertSame(3, KunenaFinderAccess::viewLevel('joomla.group', 0, 3, self::LEVELS));
        $this->assertSame(6, KunenaFinderAccess::viewLevel('joomla.group', 0, 4, self::LEVELS));
        // The groups of the rules are numbers or strings (JSON).
        $this->assertSame(7, KunenaFinderAccess::viewLevel('joomla.group', 0, 2, [7 => ['2']]));
    }

    public function testAGroupWithoutALevelAndOtherAccessAreShownToNobody(): void
    {
        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.group', 0, 42, self::LEVELS));

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
