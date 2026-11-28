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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A section which has categories is not deleted: its categories go to its parent, the root, and became sections, which have no topics
 * (the list of a section shows none, a new topic is refused there), so their topics could not be reached any more. The categories are
 * moved or deleted first. (The task needs a whole site: its code is read.)
 */
class CategoriesDeleteSectionTest extends UnitTestCase
{
    public function testASectionWithCategoriesIsRefused(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Controller/CategoriesController.php'));

        $check  = strpos($source, '} elseif ($category->isSection() && $this->hasChildren((int) $category->id)) {');
        $delete = strpos($source, '$category->delete();', (int) $check);

        $this->assertNotFalse($check);
        $this->assertNotFalse($delete);
        $this->assertStringContainsString("Text::sprintf('COM_KUNENA_A_CATEGORY_SECTION_NOT_EMPTY'", substr($source, $check, 500));

        // The children are counted in the database: published or not.
        $this->assertSame(1, preg_match('/protected function hasChildren\(int \$id\): bool\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringNotContainsString('published', $m[1]);

        $this->assertStringContainsString('COM_KUNENA_A_CATEGORY_SECTION_NOT_EMPTY = ', Files::read('src/admin/language/en-GB/com_kunena.controllers.ini'));
    }
}
