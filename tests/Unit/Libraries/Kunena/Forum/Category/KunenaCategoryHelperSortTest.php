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
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionMethod;
use ReflectionProperty;

/**
 * The list of the categories of the backend is sorted by the column which was clicked: the id, the state, the access, locked,
 * review, polls and anonymous only turned the ordering of the categories around (the helper knew the title and "catid" only),
 * and the sort list asked for "p.id", which the model did not accept.
 */
class KunenaCategoryHelperSortTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        foreach (['_instances', '_tree'] as $name) {
            $this->saved[$name] = (new ReflectionProperty(KunenaCategoryHelper::class, $name))->getValue();
        }

        // Three categories of the section 1, in the ordering 30, 10, 20.
        $categories = [];

        foreach ([[1, 0, 1, 0], [30, 1, 1, 1], [10, 1, 0, 0], [20, 1, 1, 0]] as [$id, $parent, $published, $locked]) {
            $categories[$id] = self::bare(KunenaCategory::class, [
                'id' => $id, 'parentid' => $parent, 'name' => "Category $id", 'alias' => "c$id", 'published' => $published,
                'locked' => $locked, 'access' => 1, 'review' => 0, 'allowPolls' => 0, 'allowAnonymous' => 0,
            ]);
        }

        (new ReflectionProperty(KunenaCategoryHelper::class, '_instances'))->setValue(null, $categories);
        (new ReflectionProperty(KunenaCategoryHelper::class, '_tree'))->setValue(null, [0 => [1 => []], 1 => [30 => [], 10 => [], 20 => []]]);
    }

    protected function tearDown(): void
    {
        foreach ($this->saved as $name => $value) {
            (new ReflectionProperty(KunenaCategoryHelper::class, $name))->setValue(null, $value);
        }
    }

    private function order(string $ordering, int $direction): array
    {
        $params = [
            'ordering' => $ordering, 'direction' => $direction, 'selected' => 0, 'action' => 'none', 'search' => '', 'parents' => true,
        ];

        return array_keys((new ReflectionMethod(KunenaCategoryHelper::class, '_getChildren'))->invoke(null, [1], 0, $params, false));
    }

    public function testTheListIsSortedByTheId(): void
    {
        $this->assertSame([10, 20, 30], $this->order('id', 1));
        $this->assertSame([30, 20, 10], $this->order('p.id', -1));
    }

    public function testTheListIsSortedByAColumnAndKeepsTheOrderingForTheSameValue(): void
    {
        // Published: 10 is not, 30 and 20 are (in their ordering).
        $this->assertSame([10, 30, 20], $this->order('p.published', 1));
        $this->assertSame([30, 20, 10], $this->order('p.published', -1));
        // Locked: 30 only.
        $this->assertSame([10, 20, 30], $this->order('p.locked', 1));
    }

    public function testTheSortListAsksForColumnsWhichTheModelAccepts(): void
    {
        $this->assertStringContainsString("'p.id',", Files::read('src/admin/src/Model/CategoriesModel.php'));
        $this->assertStringContainsString('default="ordering ASC"', Files::read('src/admin/forms/filter_categories.xml'));
    }
}
