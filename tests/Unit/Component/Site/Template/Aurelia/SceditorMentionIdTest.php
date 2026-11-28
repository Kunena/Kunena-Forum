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
 * The id of a [mention] in the HTML of SCEditor is a number: it was put into the attribute as it was written, and
 * [mention userid=1"style="...]x[/mention] of another post added attributes to the span of a moderator who edited the post.
 * (The script runs in a browser: its code is read.)
 */
class SceditorMentionIdTest extends UnitTestCase
{
    public function testTheIdOfAMentionIsANumber(): void
    {
        $script = Files::read('src/site/template/aurelia/assets/js/sceditor.js');

        $this->assertStringContainsString("data-kmention-userid=\"' + (parseInt(attrs.userid, 10) || 0) + '\"", $script);
        $this->assertStringNotContainsString("data-kmention-userid=\"' + attrs.userid + '\"", $script);
    }
}
