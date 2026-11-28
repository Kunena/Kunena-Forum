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

namespace Kunena\Tests\Unit\Component\Site\Service;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The dashes of a list of categories under a parent count from that parent: the parent was turned into a list of parents before its
 * level was read, the empty category of an array has the level 0, so the categories under a parent kept the dashes of their whole
 * depth. (The list needs the categories of a site: its code is read.)
 */
class KunenaforumCategoryListIndentTest extends UnitTestCase
{
    public function testTheLevelOfTheParentIsRead(): void
    {
        $this->assertStringContainsString(
            '$toplevel = -KunenaCategoryHelper::get(\is_array($parent) ? (int) reset($parent) : (int) $parent)->level;',
            Files::read('src/site/src/Service/Html/Kunenaforum.php')
        );
    }
}
