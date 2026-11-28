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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The search box of a category searches its subcategories too: the layout worked out the choice (with them, by default) and did not
 * send it, and a search form sent without the box means "without them", so a search from a category found nothing in its
 * subcategories. (The layout needs a whole site: its code is read.)
 */
class SearchBoxChildForumsTest extends UnitTestCase
{
    public function testTheBoxSendsTheSubcategories(): void
    {
        $layout = str_replace("\r\n", "\n", Files::read('src/site/template/aurelia/layouts/widget/search/topic.php'));
        $field  = strpos($layout, '<input type="hidden" name="childforums" value="<?php echo $childforums; ?>" />');

        $this->assertNotFalse($field);
        $this->assertGreaterThan(strpos($layout, 'name="catids[]"'), $field);
        $this->assertStringContainsString("getInt('childforums', \$posted ? 0 : 1)", Files::read('src/site/src/Model/SearchModel.php'));
    }
}
