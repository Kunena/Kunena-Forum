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
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionMethod;

/**
 * On a multilingual site a category of the page has the translation of its name, description and header (the original values are
 * kept for its save, so that a save of its counters does not write the translation). The form of the category in the forum:
 * - saved what was typed: the original values were saved again, so a change of the name in the form was lost;
 * - shows the original values: a save without a change would write the translation over them.
 */
class KunenaCategoryTranslationSaveTest extends UnitTestCase
{
    private function translated(): KunenaCategory
    {
        $this->requireJoomla();

        $category = self::bare(KunenaCategory::class, ['id' => 5, 'name' => 'General', 'description' => 'About all', 'headerdesc' => '', 'params' => '{}', 'iconset' => 'default']);
        $category->applyTranslation((object) ['name' => 'Allgemein', 'description' => 'Über alles']);

        return $category;
    }

    private static function stored(KunenaCategory $category): array
    {
        return (new ReflectionMethod($category, 'getTableProperties'))->invoke($category);
    }

    public function testAValueGivenToTheCategoryIsSaved(): void
    {
        $category = $this->translated();
        $category->bind(['name' => 'Allgemeines Forum']);

        $properties = self::stored($category);

        $this->assertSame('Allgemeines Forum', $properties['name']);
        // A field which was not given keeps its original value, not the translation.
        $this->assertSame('About all', $properties['description']);
    }

    public function testTheFormShowsTheOriginalValues(): void
    {
        $category = $this->translated();
        $category->removeTranslation();

        $this->assertSame('General', $category->name);
        $this->assertSame('About all', $category->description);
        $this->assertSame('General', self::stored($category)['name']);

        // The form of the forum gets a copy without the translation.
        $display = str_replace("\r\n", "\n", Files::read('src/site/src/Controller/Category/Manage/CategoryManageDisplay.php'));
        $this->assertMatchesRegularExpression('/\$this->category = clone \$this->category;\n\s*\$this->category->removeTranslation\(\);/', $display);
    }
}
