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
 * The filters of the list of the categories of the backend: each one has the name filter_* in the request, like the other lists of
 * Joomla. Two of them had another name (anonymous, level) and the depth of the category had no dot between "filter" and "levels" in
 * the session key: the two filters did not stay between two visits.
 */
class CategoriesModelFilterRequestVarsTest extends UnitTestCase
{
    public function testAnonymousAndLevelsUseTheNamesOfTheSearchTools(): void
    {
        $source = Files::read('src/admin/src/Model/CategoriesModel.php');

        $this->assertStringContainsString(
            "getUserStateFromRequest(\$this->context . '.filter.anonymous', 'filter_anonymous')",
            $source
        );
        $this->assertStringContainsString(
            "getUserStateFromRequest(\$this->context . '.filter.levels', 'filter_levels')",
            $source
        );
        $this->assertStringNotContainsString("'.filter.anonymous', 'anonymous')", $source);
        $this->assertStringNotContainsString("'.filter.level', 'level')", $source);
    }
}