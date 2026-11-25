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

namespace Kunena\Tests\Unit\Component\Administrator\View;

use Kunena\Forum\Administrator\View\Template\HtmlView;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Templates > Edit CSS (and Edit SCSS): the page made an empty custom.css (custom.scss) when it was opened, so a page which is
 * only viewed wrote a file. The file of the own styles is in the list before it exists, and it is made when it is saved.
 */
class TemplateViewOwnFileTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();
    }

    public function testTheOwnFileIsInTheListAlsoWhenItDoesNotExist(): void
    {
        $this->assertSame(['custom.css'], HtmlView::withOwnFile([], 'custom.css'));
        $this->assertSame(['a.css', 'custom.css', 'z.css'], HtmlView::withOwnFile(['z.css', 'a.css'], 'custom.css'));
        $this->assertSame(['z.css', 'custom.css'], HtmlView::withOwnFile(['z.css', 'custom.css'], 'custom.css'), 'A list which has it stays as it is.');
    }

    public function testOpeningThePageWritesNoFile(): void
    {
        $source = Files::read('src/admin/src/View/Template/HtmlView.php');

        // (The page of the template settings still makes its params.ini: another file, not the own styles.)
        $this->assertSame(0, preg_match('/custom\.s?css.{0,300}fopen\(/s', $source));
        $this->assertStringNotContainsString('fwrite(', $source);
        $this->assertStringNotContainsString('Folder::create(', $source);
        $this->assertStringContainsString("'custom.css')", $source);
        $this->assertStringContainsString("'custom.scss')", $source);
    }

    public function testAFileWhichIsNotThereYetIsWritableWhenItsFolderIs(): void
    {
        foreach (['choosecss', 'choosescss'] as $layout) {
            $source = Files::read("src/admin/tmpl/template/{$layout}.php");

            $this->assertStringContainsString('is_writable(is_file($this->dir . \'/\' . $file) ? $this->dir . \'/\' . $file : $this->dir)', $source, $layout);
        }
    }
}
