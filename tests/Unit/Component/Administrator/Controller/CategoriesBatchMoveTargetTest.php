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
 * The batch move of categories refuses a target which does not exist. isAdmin() of a missing category is the check of a global
 * administrator, so the move wrote the id of no category as the parent, and the categories were lost under no parent. (The task needs
 * a whole site: its code is read.)
 */
class CategoriesBatchMoveTargetTest extends UnitTestCase
{
    public function testAMissingTargetIsRefused(): void
    {
        $source = Files::read('src/admin/src/Controller/CategoriesController.php');

        $this->assertStringContainsString('if (!$target->exists() || !$this->me->isAdmin($target)) {', $source);
    }
}
