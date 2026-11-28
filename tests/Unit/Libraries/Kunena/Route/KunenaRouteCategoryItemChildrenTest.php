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

namespace Kunena\Tests\Unit\Libraries\Kunena\Route;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A menu item of a category is the menu item of its subcategories too (after the item of the subcategory itself, before the home
 * page): its category was compared as every other variable, so it was never the item of a subcategory, whose pages got the menu item
 * of the home page (the wrong item highlighted, the wrong modules). (The menu items need the categories of a site: the code is read.)
 */
class KunenaRouteCategoryItemChildrenTest extends UnitTestCase
{
    public function testTheCategoryIsNotComparedAsAVariable(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Route/KunenaRoute.php'));
        $start  = strpos($source, '    protected static function checkCategory($item, Uri $uri)');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString("if (\$var !== 'catid' && \$value != \$uri->getVar(\$var)) {", $body);
        $this->assertStringContainsString("return isset(\$cache[\$item->id][\$catid]) ? 4 : 0;", $body);
        $this->assertStringContainsString("if (!empty(\$item->query['catid']) && (int) \$item->query['catid'] === \$catid) {\n            return 8;", $body);
        $this->assertStringNotContainsString('$check        = self::check($item, $uri);', $body);
    }
}
