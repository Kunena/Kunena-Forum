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

namespace Kunena\Tests\Unit\Component\Administrator\Model;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The batch "move to" of the backend list of the categories offers every category which the user may administer: it offered the
 * categories of the list as it was shown, with its filters and its page (20 by default), so a category on another page or left out by
 * a filter could not be the target. (The model needs a whole site: its code is read.)
 */
class CategoriesBatchTargetsTest extends UnitTestCase
{
    public function testTheTargetsAreAllTheCategories(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/admin/src/Model/CategoriesModel.php'));
        $start  = strpos($source, '    public function getBatchCategories(): string');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString("KunenaCategoryHelper::getChildren(0, 100, ['action' => 'admin', 'unpublished' => 1])", $body);
        $this->assertStringNotContainsString('$this->getAdminCategories()', $body);
    }
}
