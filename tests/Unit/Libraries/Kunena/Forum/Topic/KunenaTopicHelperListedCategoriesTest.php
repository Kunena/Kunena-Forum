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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The categories of a list of topics: a list which excludes some categories shows the other readable ones, never the
 * categories which the user may not read.
 */
class KunenaTopicHelperListedCategoriesTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        // Loading the helper of the categories loads the categories of the site.
        $this->requireJoomla();

        $this->saved = [
            'categories' => (new ReflectionProperty(KunenaCategoryHelper::class, '_instances'))->getValue(),
            'access'     => (new ReflectionProperty(KunenaAccess::class, 'instance'))->getValue(),
        ];

        // Categories 1, 2 and 3; the user may read 1 and 2.
        $categories = [];

        foreach ([1, 2, 3] as $id) {
            $categories[$id] = self::bare(KunenaCategory::class, ['id' => $id, 'parentid' => 0, 'published' => 1, 'channels' => '', '_channels' => false]);
        }

        KunenaCategoryHelper::$_instances = $categories;

        $access = $this->createStub(KunenaAccess::class);
        $access->method('getAllowedCategories')->willReturn([1 => 1, 2 => 2]);
        (new ReflectionProperty(KunenaAccess::class, 'instance'))->setValue(null, $access);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            KunenaCategoryHelper::$_instances = $this->saved['categories'];
            (new ReflectionProperty(KunenaAccess::class, 'instance'))->setValue(null, $this->saved['access']);
        }
    }

    private static function listed($categories, bool $exclude): array
    {
        $ids = self::invoke(KunenaTopicHelper::class, 'listedCategoryIds', $categories, $exclude, 0);
        sort($ids);

        return $ids;
    }

    public function testExcludingACategoryKeepsOutTheUnreadableOnes(): void
    {
        // A NOT IN (1) also let category 3 through.
        $this->assertSame([2], self::listed([1], true));
    }

    public function testExcludingNothingListsTheReadableCategories(): void
    {
        $this->assertSame([1, 2], self::listed([], true));
    }

    public function testExcludingAnUnreadableCategoryChangesNothing(): void
    {
        $this->assertSame([1, 2], self::listed([3], true));
    }

    public function testAskedCategoriesAreTheReadableOnes(): void
    {
        $this->assertSame([2], self::listed([2, 3], false));
        $this->assertSame([], self::listed([3], false));
    }

    public function testNoCategoryAskedListsTheReadableCategories(): void
    {
        $this->assertSame([1, 2], self::listed(false, false));
    }
}
