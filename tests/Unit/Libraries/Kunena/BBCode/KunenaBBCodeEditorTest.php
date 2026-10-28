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

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeEditor;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeEditorSeparator;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;

/**
 * A plugin adds a button to the editor with insertElement() (in the event onKunenaBBCodeEditorInit). The method checked the
 * class of the element with its name without namespace: no element was ever a subclass of it, and nothing was added.
 */
class KunenaBBCodeEditorTest extends UnitTestCase
{
    private function editor(): KunenaBBCodeEditor
    {
        // The constructor asks for the template of the forum: not needed to change the list of the elements.
        $editor                  = (new ReflectionClass(KunenaBBCodeEditor::class))->newInstanceWithoutConstructor();
        $editor->editor_elements = [];

        return $editor;
    }

    public function testInsertElementAddsAnElementOfTheEditor(): void
    {
        $editor = $this->editor();
        $editor->insertElement(new KunenaBBCodeEditorSeparator('cypressseparator'));

        $this->assertArrayHasKey('cypressseparator', $editor->editor_elements);
    }
}
