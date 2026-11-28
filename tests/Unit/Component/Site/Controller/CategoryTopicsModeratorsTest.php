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

use Kunena\Forum\Site\Controller\Category\Topics\CategoryTopicsDisplay;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The page of a category names its moderators (not a banned one) when "Show moderators" is on: its layout has the block, nothing gave
 * it the moderators, so the moderators of a category were named nowhere on its page. (The page needs a whole site: its code is read.)
 */
class CategoryTopicsModeratorsTest extends UnitTestCase
{
    public function testThePageHasTheModerators(): void
    {
        $this->requireJoomla();

        $this->assertTrue(property_exists(CategoryTopicsDisplay::class, 'moderators'));

        $source = Files::read('src/site/src/Controller/Category/Topics/CategoryTopicsDisplay.php');

        $this->assertStringContainsString('$this->moderators = KunenaAccess::withoutBanned($this->category->getModerators(false));', $source);
        $this->assertStringContainsString("->set('moderators', \$this->moderators)", Files::read('src/site/template/aurelia/layouts/category/item/default.php'));
    }
}
