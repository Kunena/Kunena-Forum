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
 * The private part of the form of SCEditor starts empty, as the one of CKEditor: it had the template of the topics of the category,
 * so every new topic of a member had a private message with the template, and a topic whose text was emptied was not refused
 * (it had a private part). (The layout needs a whole site: its code is read.)
 */
class SceditorPrivateTemplateTest extends UnitTestCase
{
    public function testThePrivatePartHasNoTemplate(): void
    {
        $source = Files::read('src/site/template/aurelia/layouts/widget/editor/sceditor.php');

        $start = strpos($source, 'name="message_private"');
        $this->assertNotFalse($start);
        $field = substr($source, $start, (int) strpos($source, '</textarea>', $start) - $start);

        $this->assertStringNotContainsString('topictemplate', $field);
        $this->assertStringContainsString('$this->privateMessage->body', $field);
    }
}
