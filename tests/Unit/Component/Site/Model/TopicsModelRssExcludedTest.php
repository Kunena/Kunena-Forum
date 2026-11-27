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

namespace Kunena\Tests\Unit\Component\Site\Model;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The categories which the RSS leaves out are left out of the feeds only. On a page, a list of one such category (the filter of the
 * recent topics, the link "N pending" of a category) was the list of all the other categories, without that one. (The state is
 * made from the request, the menu item and the document of a site: its code is read.)
 */
class TopicsModelRssExcludedTest extends UnitTestCase
{
    public function testTheExcludedCategoriesOfTheRssAreOfTheFeedsOnly(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Model/TopicsModel.php'));

        $this->assertSame(1, preg_match('/\n(\s*)if \(((?:(?!if \().)*?)\) \{\n\s*if \(\\\\in_array\(\$catid, \$this->config->rssExcludedCategories\)\)/s', $source, $m));
        $this->assertStringContainsString("Factory::getApplication()->getDocument()->getType() == 'feed'", $m[2]);
    }
}
