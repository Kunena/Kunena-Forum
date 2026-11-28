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

namespace Kunena\Tests\Unit\Component\Site\Template\System;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The address of a [file] has the name of the file which the member wrote: it is escaped in the link, as the title is (it was put in
 * the attribute as it was). (The layout needs a whole site: its code is read.)
 */
class BBCodeFileEscapeTest extends UnitTestCase
{
    public function testTheAddressIsEscaped(): void
    {
        $layout = Files::read('src/site/template/system/layouts/bbcode/file/default.php');

        $this->assertStringContainsString('<a href="<?php echo $this->escape($url); ?>"', $layout);
        $this->assertStringNotContainsString('<a href="<?php echo $url; ?>"', $layout);
    }
}
