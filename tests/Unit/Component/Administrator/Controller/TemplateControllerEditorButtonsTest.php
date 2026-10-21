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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Kunena\Forum\Administrator\Controller\TemplateController;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The buttons of the editor which the form of the template gives: a checkbox which is not checked is not sent, so the keys of the
 * buttons can be missing (this was a PHP warning for every button).
 */
class TemplateControllerEditorButtonsTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    private function buttons(array $params): array
    {
        return $this->invoke($this->bare(TemplateController::class), 'SaveSCeditorParamFile', $params);
    }

    public function testAFormWithoutKeysGivesTheButtonsWhichAreNotSelectable(): void
    {
        $buttons = $this->buttons([]);

        $this->assertContains('cut', $buttons);
        $this->assertContains('source', $buttons);
        $this->assertContains('undo,redo', $buttons);
        $this->assertNotContains('quote', $buttons);
    }

    public function testAChosenButtonIsAdded(): void
    {
        $buttons = $this->buttons(['Blockquote' => '1', 'Code' => '1', 'Cut' => '1']);

        $this->assertContains('quote', $buttons);
        $this->assertContains('code', $buttons);
        $this->assertNotContains('cut', $buttons);
    }
}
