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
 * The names of the lists of the mentions are sent as text (the JSON encodes them): the scripts of the editors put them in as text, so
 * an escaped name was shown in the list as "Tom &amp; Jerry" and posted so in the mention. (The tasks need a whole site: their code
 * is read, with the scripts which use the names.)
 */
class UserMentionNamesTest extends UnitTestCase
{
    public function testTheNamesAreSentAsText(): void
    {
        $source = Files::read('src/site/src/Controllers/UserController.php');

        $this->assertStringNotContainsString("\$obj->username = \$this->escape(", $source);
        $this->assertStringContainsString("\$obj->username = \$found->getName('', false);", $source);
        $this->assertStringContainsString("\$obj->username = \$user->getName('', false);", $source);

        // The editors put the names in as text.
        $this->assertStringContainsString("name.textContent = ' ' + item.username;", Files::read('editor/ckeditor5/src/kunena-editor.js'));
        $this->assertStringContainsString('name.textContent = user.username;', Files::read('src/site/template/aurelia/assets/js/sceditor.js'));
    }
}
