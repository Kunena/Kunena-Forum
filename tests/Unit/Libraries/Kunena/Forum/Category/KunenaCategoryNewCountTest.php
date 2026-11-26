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

/**
 * The count of the new topics of a category (a note: "Currently new topics needs to be calculated manually, make it automatic"):
 * the lists count them for their categories at once, a category which no list counted counts its own the first time (it gave
 * nothing), and a count of no topic is 0 for every category of the list (else each one counted its own again).
 */
class KunenaCategoryNewCountTest extends UnitTestCase
{
    public function testACountWhichWasGivenIsKept(): void
    {
        $this->requireJoomla();

        $category = self::bare(KunenaCategory::class, ['_new' => null, '_exists' => false]);

        $this->assertSame(0, $category->getNewCount(), 'a category which is not in the database has no new topic, and no query');
        $this->assertSame(4, $category->getNewCount(4));
        $this->assertSame(4, $category->getNewCount());
    }

    public function testACategoryWhichNoListCountedCountsItsOwn(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Category/KunenaCategory.php');

        $this->assertSame(1, preg_match('/public function getNewCount\(\$count = null\)\s*\{(.*?)\n    \}/s', $source, $m));
        $this->assertStringContainsString('$this->_new = 0;', $m[1]);
        $this->assertStringContainsString('KunenaCategoryHelper::getNewTopics([(int) $this->id]);', $m[1]);
    }

    public function testTheCountOfNoNewTopicIsZeroForEveryCategory(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/Category/KunenaCategoryHelper.php');

        $this->assertSame(1, preg_match('/public static function getNewTopics\(\$catids\): void\s*\{(.*?)\n    \}/s', $source, $m));
        $this->assertStringContainsString('$category->getNewCount(0);', $m[1]);
        $this->assertStringNotContainsString("if (empty(\$newlist)) {\n            return;", $m[1]);
    }

    public function testTheTopicListsHaveTheCategoriesInTheirForumJump(): void
    {
        $source = Files::read('src/site/src/Controller/Topic/Listing/ListDisplay.php');

        $this->assertStringContainsString("'kunenaforum.categorylist',", $source);
        $this->assertStringNotContainsString("HTMLHelper::_('select.genericlist', \$options, 'catid'", $source);
    }
}
