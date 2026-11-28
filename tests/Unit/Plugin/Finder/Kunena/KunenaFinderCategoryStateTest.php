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

namespace Kunena\Tests\Unit\Plugin\Finder\Kunena;

use Kunena\Forum\Libraries\Tables\TableKunenaCategories;
use Kunena\Forum\Plugin\Finder\Kunena\Extension\Kunena;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionMethod;

/**
 * The messages of a category are indexed again (Smart Search) when what the index takes from the category changes. The index takes
 * the child groups and the second group of a category of groups (its access) and the parent (a category under an unpublished parent
 * is hidden), which the comparison left out: the search kept the old access (the users of the child groups of a category closed to
 * them still found its messages) or the old state (a category moved into an unpublished section stayed in the results).
 */
class KunenaFinderCategoryStateTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();

        require_once Files::path('src/plugins/plg_finder_kunena/src/Extension/Kunena.php');
    }

    private static function category(array $values): TableKunenaCategories
    {
        $table = (new ReflectionClass(TableKunenaCategories::class))->newInstanceWithoutConstructor();

        foreach ($values + ['accesstype' => 'joomla.group', 'access' => 1, 'pubAccess' => 2, 'pubRecurse' => 1, 'adminAccess' => 0, 'adminRecurse' => 1, 'published' => 1, 'parentid' => 1, 'params' => '{}'] as $name => $value) {
            $table->$name = $value;
        }

        return $table;
    }

    public static function changes(): array
    {
        return [
            'the child groups of the group'     => [['pubRecurse' => 0]],
            'the second group'                  => [['adminAccess' => 6]],
            'the child groups of the second one' => [['adminRecurse' => 0]],
            'the parent'                        => [['parentid' => 5]],
        ];
    }

    #[DataProvider('changes')]
    public function testTheChangeIsSeen(array $change): void
    {
        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $state  = new ReflectionMethod($plugin, 'categoryIndexState');

        $this->assertNotSame($state->invoke($plugin, self::category([])), $state->invoke($plugin, self::category($change)));
    }

    public function testANewParentIndexesTheSubcategoriesToo(): void
    {
        $source = Files::read('src/plugins/plg_finder_kunena/src/Extension/Kunena.php');

        $this->assertStringContainsString("\$old['parentid'] !== \$new['parentid']", $source);
    }
}
