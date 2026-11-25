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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaPermissionCheck;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Permission checker" checks actions which the categories know.
 */
class KunenaPermissionCheckTest extends UnitTestCase
{
    public function testTheActionsOfTheCheckerAreActionsOfTheCategories(): void
    {
        $this->requireJoomla();

        $rules = KunenaPermissionCheck::rules();

        foreach (array_keys(KunenaPermissionCheck::ACTIONS) as $action) {
            $this->assertArrayHasKey($action, $rules, $action);
        }
    }
}
