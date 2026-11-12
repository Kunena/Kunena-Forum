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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Category;

use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The Access filter of the backend category manager: Joomla access levels and Joomla user groups.
 */
class KunenaCategoryAccessFilterTest extends UnitTestCase
{
    private static function level(int $access): KunenaCategory
    {
        return self::bare(KunenaCategory::class, ['accesstype' => 'joomla.level', 'access' => $access]);
    }

    private static function group(int $pubAccess, int $pubRecurse, int $adminAccess = 0, int $adminRecurse = 0): KunenaCategory
    {
        return self::bare(KunenaCategory::class, [
            'accesstype'   => 'joomla.group',
            'access'       => 0,
            'pubAccess'    => $pubAccess,
            'pubRecurse'   => $pubRecurse,
            'adminAccess'  => $adminAccess,
            'adminRecurse' => $adminRecurse,
        ]);
    }

    public static function cases(): array
    {
        // The default Joomla tree: Public (1) > Registered (2) > Author (3) > Editor (4), Registered (2) > Manager (6).
        return [
            'level match'                                  => [self::level(2), 'joomla.level.2', [], true],
            'level mismatch'                               => [self::level(2), 'joomla.level.3', [], false],
            'bare integer is a level'                      => [self::level(2), '2', [], true],
            'bare integer of another level'                => [self::level(2), '3', [], false],
            'group match on the primary group'             => [self::group(3, 0), 'joomla.group.3', [1, 2, 3], true],
            'group match on the secondary group'           => [self::group(6, 0, 3, 0), 'joomla.group.3', [1, 2, 3], true],
            'child group of the primary group, recurse'    => [self::group(2, 1), 'joomla.group.4', [1, 2, 3, 4], true],
            'child group of the secondary group, recurse'  => [self::group(6, 0, 2, 1), 'joomla.group.4', [1, 2, 3, 4], true],
            'child group of the primary group, no recurse' => [self::group(2, 0), 'joomla.group.4', [1, 2, 3, 4], false],
            'child group of the secondary, no recurse'     => [self::group(6, 0, 2, 0), 'joomla.group.4', [1, 2, 3, 4], false],
            'other group'                                  => [self::group(6, 1), 'joomla.group.3', [1, 2, 3], false],
            'unset secondary group never matches'          => [self::group(6, 0, 0, 1), 'joomla.group.0', [], false],
            'level filter on a group category'             => [self::group(2, 0), 'joomla.level.2', [], false],
            'bare integer on a group category'             => [self::group(2, 0), '2', [], false],
            'group filter on a level category'             => [self::level(2), 'joomla.group.2', [1, 2], false],
            'unknown group'                                => [self::group(2, 1), 'joomla.group.999', [], false],
            'unknown prefix'                               => [self::level(2), 'jomsocial.2', [], false],
            'non-numeric id'                               => [self::level(2), 'joomla.level.abc', [], false],
            'negative id'                                  => [self::level(2), '-2', [], false],
            'trailing text'                                => [self::level(2), 'joomla.level.2x', [], false],
            'empty value'                                  => [self::level(2), '', [], false],
        ];
    }

    #[DataProvider('cases')]
    public function testTheFilterMatchesTheCategory(KunenaCategory $category, string $filter, array $groupPath, bool $expected): void
    {
        $this->assertSame($expected, KunenaCategoryHelper::matchesAccessFilter($category, $filter, $groupPath));
    }

    public function testAPluginAccessTypeNeverMatches(): void
    {
        $category = self::bare(KunenaCategory::class, [
            'accesstype'   => 'jomsocial',
            'access'       => 2,
            'pubAccess'    => 2,
            'pubRecurse'   => 1,
            'adminAccess'  => 2,
            'adminRecurse' => 1,
        ]);

        foreach (['2', 'joomla.level.2', 'joomla.group.2', 'jomsocial.2'] as $filter) {
            $this->assertFalse(KunenaCategoryHelper::matchesAccessFilter($category, $filter, [1, 2]), $filter);
        }
    }
}
