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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The forum jump offers the whole forum and chooses no first category: the page of a category gave its category as the parent of the
 * list, so its jump offered that category and its subcategories only; and every jump chose the first category of the forum (the
 * list chooses the first one when nothing is chosen), so "Go" without a choice jumped there. The page of a category chooses itself.
 * (The pages need a whole site: their code is read.)
 */
class ForumJumpListTest extends UnitTestCase
{
    public static function jumps(): array
    {
        return [
            'the page of a topic'  => ['src/site/src/Controller/Topic/Item/TopicItemDisplay.php'],
            'the lists of topics'  => ['src/site/src/Controller/Topic/Listing/ListDisplay.php'],
            'the older topic view' => ['src/site/src/View/Topic/HtmlView.php'],
            'the page of a category' => ['src/site/src/Controller/Category/Index/CategoryIndexDisplay.php'],
        ];
    }

    #[DataProvider('jumps')]
    public function testTheJumpChoosesNoFirstCategory(string $file): void
    {
        $this->assertMatchesRegularExpression("/\\['sections' => 1, 'catid' => 0, 'preselect' => false\\]/", Files::read($file));
    }

    public function testTheJumpOfACategoryHasTheWholeForumWithTheCategoryChosen(): void
    {
        $source = Files::read('src/site/src/Controller/Category/Index/CategoryIndexDisplay.php');

        $this->assertStringContainsString("HTMLHelper::_('kunenaforum.categorylist', 'catid', 0, \$options, \$catParams,", $source);
        $this->assertStringContainsString("'value', 'text', \$catid);", $source);
    }
}
