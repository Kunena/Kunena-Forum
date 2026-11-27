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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * "Mark Topics Read" of a category with channels: the list of the category shows the topics of its channels (getChannels()),
 * and only the category itself was marked, so they stayed new after the click (and in the count of the new topics of the category
 * in the index). The channels which the user reads are marked with it. (The task needs a whole site: its code is read.)
 */
class CategoryMarkReadChannelsTest extends UnitTestCase
{
    public function testTheChannelsOfTheCategoryAreMarkedWithIt(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/CategoryController.php'));

        $this->assertSame(1, preg_match('/public function markread\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString('$categories = [$category->id => $category] + $category->getChannels();', $m[1]);
        $this->assertStringContainsString('if ($children && \count($categories) > 1) {', $m[1]);
    }
}
