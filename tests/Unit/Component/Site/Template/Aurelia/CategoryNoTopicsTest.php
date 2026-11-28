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
 * A category without a topic says "No Topics in This Category" (the key was there, no layout used it): the page had its buttons
 * only, and a "</table>" which nothing opened (the table opens for a list with topics). Agora uses this layout. (The layout needs a
 * whole site: its code is read.)
 */
class CategoryNoTopicsTest extends UnitTestCase
{
    public function testAnEmptyCategorySaysSo(): void
    {
        $layout = str_replace("\r\n", "\n", Files::read('src/site/template/aurelia/layouts/category/item/default.php'));

        $this->assertStringContainsString("            </table>\n        <?php else : ?>", $layout);
        $this->assertStringContainsString("Text::_('COM_KUNENA_VIEW_CATEGORY_NO_TOPICS')", $layout);
        $this->assertSame(substr_count($layout, '<table'), substr_count($layout, '</table>'));
        $this->assertStringContainsString("\nCOM_KUNENA_VIEW_CATEGORY_NO_TOPICS = \"", Files::read('src/site/language/en-GB/com_kunena.templates.ini'));
    }
}
