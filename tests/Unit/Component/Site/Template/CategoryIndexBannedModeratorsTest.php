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

namespace Kunena\Tests\Unit\Component\Site\Template;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The index of the categories names no banned moderator, as the other lists of moderators: the fix of the models did not reach the
 * index, whose layouts read the moderators of a category themselves. (The layouts need a whole site: their code is read.)
 */
class CategoryIndexBannedModeratorsTest extends UnitTestCase
{
    public static function templates(): array
    {
        return ['aurelia' => ['aurelia'], 'agora' => ['agora']];
    }

    #[DataProvider('templates')]
    public function testTheIndexLeavesOutBannedModerators(string $template): void
    {
        $layout = Files::read('src/site/template/' . $template . '/layouts/category/index/default.php');

        $this->assertStringContainsString('KunenaAccess::withoutBanned($category->getModerators())', $layout);
        $this->assertStringContainsString('foreach ($moderators as $moderator) {', $layout);
        $this->assertStringNotContainsString('foreach ($category->getmoderators() as $moderator)', $layout);
    }
}
