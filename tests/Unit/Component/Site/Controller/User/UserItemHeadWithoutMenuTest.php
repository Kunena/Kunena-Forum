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

namespace Kunena\Tests\Unit\Component\Site\Controller\User;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The page of a profile has its title, its description and the tags og:title and og:description also without an active menu item
 * (a forum which has no menu item of Kunena, a link without an Itemid): they were set only inside "if ($menu_item)", so such a page
 * had the title of the site and no tag of Open Graph for the member. (The page needs a whole site: its code is read.)
 */
class UserItemHeadWithoutMenuTest extends UnitTestCase
{
    public function testTheHeadDoesNotNeedAMenuItem(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controller/User/Item/UserItemDisplay.php'));

        $start = strpos($source, '    protected function prepareDocument()');
        $this->assertNotFalse($start);
        $body = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringNotContainsString('if ($menu_item) {', $body);

        foreach (["\$this->setTitle(\$title);", "\$this->setMetaData('og:title', \$name, 'property');", "\$this->setMetaData('og:description', \$title, 'property');"] as $line) {
            // At the level of the method (8 spaces), not in a block of the menu item.
            $this->assertStringContainsString("\n        " . $line, $body, $line);
        }
    }
}
