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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Forum\Administrator\Controller\CategoryController;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The old aliases of a category in its form in the backend: a ticked alias is kept, an unticked one is removed. The ticked ones
 * were read with getArray(['aliases' => []]), which gives an empty list, so no alias was ever removed.
 */
class CategoryControllerAliasesTest extends UnitTestCase
{
    private function removed(string $all, mixed $ticked): array
    {
        $this->requireJoomla();

        return $this->invoke(CategoryController::class, 'removedAliases', $all, $ticked);
    }

    public function testTheUntickedAliasesAreRemoved(): void
    {
        $this->assertSame(['old-name', 'older-name'], $this->removed('news,old-name,older-name', ['news']));
    }

    public function testNoAliasTickedRemovesThemAll(): void
    {
        // No box ticked: the browser posts no field aliases[] (deleteAlias() keeps the alias of the category itself).
        $this->assertSame(['news', 'old-name'], $this->removed('news,old-name', []));
    }

    public function testAllTickedRemovesNone(): void
    {
        $this->assertSame([], $this->removed('news,old-name', ['news', 'old-name']));
    }

    public function testAFormWithoutTheListRemovesNone(): void
    {
        // The form of the forum posts the alias of a select list (aliases) and no list of all the aliases.
        $this->assertSame([], $this->removed('', 'news'));
    }

    public function testAListInATickedValueIsIgnored(): void
    {
        $this->assertSame(['old-name'], $this->removed('news,old-name', ['news', ['old-name']]));
    }
}
