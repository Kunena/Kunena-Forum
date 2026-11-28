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
 * The layout of CKEditor loads the modal of Bootstrap, which its dialogs use (dialogs.js: window.bootstrap.Modal). Each script of
 * Bootstrap adds only its own part: the layout loaded the tabs only, and on a page which loads no other modal every dialog (map,
 * link, poll, video...) was an error of the editor (bbcode-map-params.cy.js).
 */
class CkeditorBootstrapModalTest extends UnitTestCase
{
    public function testTheModalOfTheDialogsIsLoaded(): void
    {
        $this->assertStringContainsString('window.bootstrap.Modal', Files::read('editor/ckeditor5/src/dialogs.js'));
        $this->assertStringContainsString("HTMLHelper::_('bootstrap.modal');", Files::read('src/site/template/aurelia/layouts/widget/editor/ckeditor.php'));
    }
}
