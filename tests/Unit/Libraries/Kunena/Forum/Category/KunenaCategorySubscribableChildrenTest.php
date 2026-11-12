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

use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * Subscribing to a category also subscribes to the subcategories which the user may subscribe to.
 */
class KunenaCategorySubscribableChildrenTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        // Loading the helper loads the categories of the site.
        $this->requireJoomla();

        foreach (['_instances', '_tree'] as $name) {
            $this->saved[$name] = (new ReflectionProperty(KunenaCategoryHelper::class, $name))->getValue();
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->saved as $name => $value) {
            (new ReflectionProperty(KunenaCategoryHelper::class, $name))->setValue(null, $value);
        }
    }

    /**
     * A published category whose check of the action "subscribe" passes or fails.
     */
    private static function category(int $id, int $parent, bool $subscribable): KunenaCategory
    {
        $category = new class () extends KunenaCategory {
            public bool $subscribable = true;

            public function __construct()
            {
            }

            public function tryAuthorise($action = 'read', ?KunenaUser $user = null, $throw = true)
            {
                return $action == 'subscribe' && !$this->subscribable ? new KunenaExceptionAuthorise('No access', 403) : null;
            }
        };

        $category->id           = $id;
        $category->parentid     = $parent;
        $category->published    = 1;
        $category->name         = 'Category ' . $id;
        $category->subscribable = $subscribable;

        return $category;
    }

    /**
     * The tree of the helper:
     * 1 (subscribed) > 2 (may subscribe), 3 (may not read) > 4 (may subscribe) > 5 (may subscribe); 6 (outside) > 7.
     */
    private function forum(): KunenaCategory
    {
        $categories = [
            1 => self::category(1, 0, true),
            2 => self::category(2, 1, true),
            3 => self::category(3, 1, false),
            4 => self::category(4, 3, true),
            5 => self::category(5, 4, true),
            6 => self::category(6, 0, true),
            7 => self::category(7, 6, true),
        ];

        KunenaCategoryHelper::$_instances = $categories;
        self::invoke(KunenaCategoryHelper::class, 'buildTree', $categories);

        return $categories[1];
    }

    private function subscribable(): array
    {
        return array_keys($this->forum()->getSubscribableChildren(self::bare(KunenaUser::class, ['userid' => 42])));
    }

    public function testASubcategoryTheUserMaySubscribeToIsIncluded(): void
    {
        $this->assertContains(2, $this->subscribable());
    }

    public function testASubcategoryTheUserMayNotReadIsLeftOut(): void
    {
        $this->assertNotContains(3, $this->subscribable());
    }

    public function testTheSubcategoriesBelowALeftOutCategoryAreCheckedOnTheirOwnRights(): void
    {
        $ids = $this->subscribable();

        $this->assertContains(4, $ids);
        $this->assertContains(5, $ids);
    }

    public function testOnlyTheSubtreeOfTheCategoryIsIncluded(): void
    {
        $ids = $this->subscribable();

        sort($ids);
        $this->assertSame([2, 4, 5], $ids);
    }
}
