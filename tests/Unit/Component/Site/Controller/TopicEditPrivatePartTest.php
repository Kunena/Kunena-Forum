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
 * The edit of a message reads its private part from the field which the editors send (message_private, as post() reads it). It
 * read private_message, which no form sends: the private part was always empty, so an edit which emptied the public text and kept
 * the private part was refused ("no message"), or put the message in the trash for a user who may delete it. (The task needs a
 * whole site: its code is read.)
 */
class TopicEditPrivatePartTest extends UnitTestCase
{
    public function testTheEditReadsThePrivatePartWhichTheEditorsSend(): void
    {
        $source = str_replace("\r\n", "\n", Files::read('src/site/src/Controllers/TopicController.php'));

        $this->assertSame(1, preg_match('/public function edit\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $this->assertStringContainsString("'private'           => (string) \$this->input->getRaw('message_private'),", $m[1]);
        $this->assertStringNotContainsString("getRaw('private_message')", $m[1]);

        // The editors of both templates send this name.
        foreach (['sceditor', 'ckeditor'] as $editor) {
            $this->assertStringContainsString('name="message_private"', Files::read("src/site/template/aurelia/layouts/widget/editor/{$editor}.php"));
        }
    }
}
