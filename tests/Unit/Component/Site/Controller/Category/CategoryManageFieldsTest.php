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

namespace Kunena\Tests\Unit\Component\Site\Controller\Category;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The form of a category on the site (view=category&layout=manage) keeps what a save does not change:
 * - the channels are a list (channels[]) of "this", "children" and the categories, as in the backend: the field was one value
 *   (channels) with the first two only, so a save lost the channels of other categories, and with both chosen only the last was kept;
 * - the parents are all of the tree: the list of what the administrator may read left out the section of another language than the
 *   page, and the save took the first one of the list as the new parent.
 * (The form needs a whole site: its code is read.)
 */
class CategoryManageFieldsTest extends UnitTestCase
{
    public function testTheChannelsAndTheParentAreKept(): void
    {
        $source = Files::read('src/site/src/Controller/Category/Manage/CategoryManageDisplay.php');

        $this->assertStringContainsString("HTMLHelper::_('kunenaforum.categorylist', 'channels[]', 0, \$channelsOptions, ['catid' => \$category->id, 'action' => 'admin'],", $source);
        $this->assertStringNotContainsString("HTMLHelper::_('select.genericlist', \$channelsOptions, 'channels',", $source);
        $this->assertStringContainsString("\$catParams = ['sections' => 1, 'catid' => (int) \$category->id, 'unpublished' => 1, 'action' => 'none'];", $source);
    }
}
