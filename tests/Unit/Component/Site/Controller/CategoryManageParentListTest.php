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

/**
 * The parent list of the category manage form of the site leaves out the category and its subcategories, as the backend does: the
 * list got the id 0, so a category was offered as its own parent and as the child of its own children (the save refused them).
 * (The form needs a whole site: its code is read.)
 */
class CategoryManageParentListTest extends UnitTestCase
{
    public function testTheBranchOfTheCategoryIsLeftOut(): void
    {
        $source = Files::read('src/site/src/Controller/Category/Manage/CategoryManageDisplay.php');

        $this->assertStringContainsString("\$catParams = ['sections' => 1, 'catid' => (int) \$category->id, 'unpublished' => 1, 'action' => 'none'];", $source);
        $this->assertStringContainsString("if (\$id == \$params['selected'])", Files::read('src/libraries/kunena/src/Forum/Category/KunenaCategoryHelper.php'));
    }
}
