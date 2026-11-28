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
 * The [video] of SCEditor keeps the & of its address: the parser of SCEditor escapes the text of a tag already, and the handler
 * escaped it a second time, so [video]...?v=abc&t=30s[/video] was saved with &amp; at every edit of the message. (The script runs in
 * a browser: its code is read, with the escape of the parser.)
 */
class SceditorVideoEscapeTest extends UnitTestCase
{
    public function testTheContentIsEscapedOnce(): void
    {
        $script = Files::read('src/site/template/aurelia/assets/js/sceditor.js');

        $this->assertStringContainsString(".replace('{src}', content)", $script);
        $this->assertStringContainsString("return sceditor.escapeEntities(token.val, true) + content + sceditor.escapeEntities(token.closing ? token.closing.val : '', true);", $script);
        $this->assertStringContainsString('html           = escapeEntities(token.val, true);', Files::read('src/media/kunena/core/js/dev/sceditor/bbcode.js'));
    }
}
