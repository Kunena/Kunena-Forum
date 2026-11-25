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

use Kunena\Forum\Libraries\Tools\KunenaCategoryMerge;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Merge categories" merges a category into another one which is no section and not below it.
 */
class KunenaCategoryMergeTest extends UnitTestCase
{
    private static function tree(): array
    {
        return [
            ['id' => 1, 'parentid' => 0],
            ['id' => 2, 'parentid' => 1],
            ['id' => 3, 'parentid' => 2],
            ['id' => 4, 'parentid' => 1],
            ['id' => 5, 'parentid' => 0],
        ];
    }

    public function testACategoryMergesIntoAnotherOneWhichIsNoSectionAndNotBelowIt(): void
    {
        $this->assertNull(KunenaCategoryMerge::check(self::tree(), 2, 4));
        $this->assertNull(KunenaCategoryMerge::check(self::tree(), 3, 4));
        $this->assertNull(KunenaCategoryMerge::check(self::tree(), 5, 4));
        $this->assertSame('COM_KUNENA_TOOLS_CATMERGE_ERROR_SAME', KunenaCategoryMerge::check(self::tree(), 2, 2));
        $this->assertSame('COM_KUNENA_TOOLS_CATMERGE_ERROR_SECTION', KunenaCategoryMerge::check(self::tree(), 2, 5));
        $this->assertSame('COM_KUNENA_TOOLS_CATMERGE_ERROR_BELOW', KunenaCategoryMerge::check(self::tree(), 2, 3));
        $this->assertSame('COM_KUNENA_TOOLS_CATMERGE_ERROR_CHOOSE', KunenaCategoryMerge::check(self::tree(), 2, 99));
        $this->assertSame('COM_KUNENA_TOOLS_CATMERGE_ERROR_CHOOSE', KunenaCategoryMerge::check(self::tree(), 0, 4));
    }
}
