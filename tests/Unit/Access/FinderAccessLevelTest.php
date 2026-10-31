<?php

/**
 * Kunena Component
 *
 * Regression test for the access level which the Smart Search plugin stores. Smart Search compares it with the view levels of the
 * visitor, so it has to be the id of a view level. The plugin stored 1 (Public) when guests could read the category and 0 (a
 * level nobody has) when not: the messages of a category for Registered users were hidden from the registered users.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Access;

use Kunena\Forum\Libraries\Access\KunenaFinderAccess;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FinderAccessLevelTest extends TestCase
{
    private const PLUGIN = __DIR__ . '/../../../src/plugins/plg_finder_kunena/src/Extension/Kunena.php';

    /** The default view levels of Joomla: id => groups. */
    private const LEVELS = [
        1 => [1],           // Public: the group Public
        2 => [6, 2, 8],     // Registered: Manager, Registered, Super Users
        3 => [6, 3, 8],     // Special: Manager, Author, Super Users
        5 => [1],           // Guest: Public
        6 => [3, 4, 5, 8],
    ];

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Access/KunenaFinderAccess.php';
    }

    public static function levelCategories(): array
    {
        return [
            'public'       => [1, 1],
            'registered'   => [2, 2],
            'special'      => [3, 3],
            'custom'       => [6, 6],
        ];
    }

    #[DataProvider('levelCategories')]
    public function testCategoryOfAViewLevelStoresThatLevel(int $access, int $expected): void
    {
        $this->assertSame($expected, KunenaFinderAccess::viewLevel('joomla.level', $access, 0, self::LEVELS));
    }

    public function testRegisteredCategoryIsNotStoredAsPublicOrNobody(): void
    {
        $level = KunenaFinderAccess::viewLevel('joomla.level', 2, 0, self::LEVELS);

        $this->assertNotSame(1, $level, 'Not Public: guests must not find it.');
        $this->assertNotSame(0, $level, 'Not 0: registered users must find it.');
    }

    public function testLevelWhichDoesNotExistIsShownToNobody(): void
    {
        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.level', 99, 0, self::LEVELS));
        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.level', 0, 0, self::LEVELS));
        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.level', 2, 0, []));
    }

    public function testGroupGetsALevelWhichItBelongsTo(): void
    {
        // The group Registered (2) is read by the level Registered.
        $this->assertSame(2, KunenaFinderAccess::viewLevel('joomla.group', 0, 2, self::LEVELS));
        // The group Public (1): two levels, both of the group alone; the lowest id.
        $this->assertSame(1, KunenaFinderAccess::viewLevel('joomla.group', 0, 1, self::LEVELS));
    }

    public function testGroupGetsTheNarrowestLevel(): void
    {
        // Group 3 (Author) is in the levels Special (3 groups) and 6 (4 groups): the narrower one.
        $this->assertSame(3, KunenaFinderAccess::viewLevel('joomla.group', 0, 3, self::LEVELS));
        // Group 4 is only in the level 6.
        $this->assertSame(6, KunenaFinderAccess::viewLevel('joomla.group', 0, 4, self::LEVELS));
    }

    public function testGroupWithoutALevelIsShownToNobody(): void
    {
        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.group', 0, 42, self::LEVELS));
        $this->assertSame(0, KunenaFinderAccess::viewLevel('joomla.group', 0, 0, self::LEVELS));
    }

    public function testGroupsAreComparedAsNumbers(): void
    {
        $this->assertSame(7, KunenaFinderAccess::viewLevel('joomla.group', 0, 2, [7 => ['2']]));
    }

    public function testOtherTypesOfAccessAreShownToNobody(): void
    {
        foreach (['none', 'communitybuilder', 'jomsocial', '', 'JOOMLA.LEVEL'] as $type) {
            $this->assertSame(0, KunenaFinderAccess::viewLevel($type, 2, 2, self::LEVELS), $type);
        }
    }

    private function plugin(): string
    {
        return file_get_contents(self::PLUGIN);
    }

    public function testPluginUsesThePolicyAndNoLongerReturnsZeroOrOne(): void
    {
        $source = $this->plugin();

        $this->assertSame(1, preg_match('/protected function getAccessLevel\(\$catid\): int\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));
        $this->assertStringContainsString('KunenaFinderAccess::viewLevel(', $m[1]);
        $this->assertStringContainsString("'#__viewlevels'", $m[1], 'The view levels of the site are read.');
        $this->assertDoesNotMatchRegularExpression('/return\s+[01]\s*;/', $m[1], 'A level is not a yes or no.');
        $this->assertDoesNotMatchRegularExpression('/loadUserById\(0\)/', $m[1], 'The guest is not the measure of the level.');
    }

    public function testChangingTheGroupOfACategoryReindexesItsMessages(): void
    {
        $source = $this->plugin();

        $this->assertStringContainsString('$this->old_catpubaccess  = $old_table->pubAccess;', $source);
        $this->assertStringContainsString('$row->pubAccess == $this->old_catpubaccess', $source);
        $this->assertStringContainsString("'joomla.group'", $source);
    }
}
