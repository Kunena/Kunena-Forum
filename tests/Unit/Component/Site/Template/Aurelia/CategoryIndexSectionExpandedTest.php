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
 * The button which folds a section of the index tells that the section is open, as it is when the page opens ("collapse show"), and
 * as Agora tells it: screen readers were told "collapsed" until the first click.
 */
class CategoryIndexSectionExpandedTest extends UnitTestCase
{
    public function testTheButtonTellsThatTheSectionIsOpen(): void
    {
        $layout = Files::read('src/site/template/aurelia/layouts/category/index/default.php');

        $this->assertStringContainsString('collapse show', $layout);
        $this->assertStringContainsString('aria-expanded="true" aria-controls="section', $layout);
        $this->assertStringNotContainsString('aria-expanded="false" aria-controls="section', $layout);
    }
}
