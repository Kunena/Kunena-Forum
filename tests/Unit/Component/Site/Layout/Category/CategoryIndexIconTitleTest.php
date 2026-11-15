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

namespace Kunena\Tests\Unit\Component\Site\Layout\Category;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Kunena\Forum\Site\Layout\Category\CategoryIndex;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tooltip of the icon of a category on the index: the icon shows whether the category has new posts, so the tooltip says it
 * (for a user; a guest has no new posts, so the name only). The name next to it was the whole tooltip before.
 */
class CategoryIndexIconTitleTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        // A language which gives back the key, and the name in place of the %s.
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getLanguage')->willReturn($language);

        $this->saved = ['application' => Factory::$application, 'language' => Factory::$language];

        Factory::$application = $application;
        Factory::$language    = $language;
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->saved['application'];
        Factory::$language    = $this->saved['language'];
    }

    public function testAUserSeesWhetherTheCategoryHasNewPosts(): void
    {
        $this->assertStringContainsString('COM_KUNENA_CATEGORY_ICON_NEW_POSTS', CategoryIndex::categoryIconTitle('News', true, 3));
        $this->assertStringContainsString('COM_KUNENA_CATEGORY_ICON_NO_NEW_POSTS', CategoryIndex::categoryIconTitle('News', true, 0));
    }

    public function testAGuestSeesTheName(): void
    {
        $this->assertSame('News', CategoryIndex::categoryIconTitle('News', false, 0));
        $this->assertSame('News', CategoryIndex::categoryIconTitle('News', false, 5));
    }

    public function testTheIconsOfBothTemplatesHaveTheTooltip(): void
    {
        foreach (['aurelia', 'agora'] as $template) {
            $layout = Files::read('src/site/template/' . $template . '/layouts/category/index/default.php');

            $this->assertStringContainsString('$this->getCategoryIcon($category), $this->getCategoryIconTitle($category), $this->ktemplate->tooltips()', $layout);
            $this->assertStringContainsString('$this->getSmallCategoryIcon($subcategory), $this->getCategoryIconTitle($subcategory), $this->ktemplate->tooltips()', $layout);
        }

        $ini = Files::read('src/site/language/en-GB/com_kunena.templates.ini');

        $this->assertStringContainsString('COM_KUNENA_CATEGORY_ICON_NEW_POSTS = "%s: new posts"', $ini);
        $this->assertStringContainsString('COM_KUNENA_CATEGORY_ICON_NO_NEW_POSTS = "%s: no new posts"', $ini);
    }
}
