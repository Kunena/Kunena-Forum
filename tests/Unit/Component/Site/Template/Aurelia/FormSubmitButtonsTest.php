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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The time filter and the forum jump do not submit on change: each form has a visible submit button, and each drop-down has
 * an accessible name built from a language key.
 */
class FormSubmitButtonsTest extends UnitTestCase
{
    private const FORUM_JUMPS = [
        'src/site/src/Controller/Category/Index/CategoryIndexDisplay.php',
        'src/site/src/Controller/Topic/Listing/ListDisplay.php',
        'src/site/src/Controller/Topic/Item/TopicItemDisplay.php',
        'src/site/src/View/Topic/HtmlView.php',
    ];

    private const TIME_FILTERS = [
        'src/site/src/Layout/Topic/TopicList.php',
        'src/site/src/Layout/Message/MessageList.php',
    ];

    /**
     * @return  array<string, array{string, string}>  The layout and the id of its time filter form.
     */
    public static function timeFilterForms(): array
    {
        return [
            'aurelia topic list'     => ['src/site/template/aurelia/layouts/topic/list/default.php', 'timeselect'],
            'aurelia message list'   => ['src/site/template/aurelia/layouts/message/list/default.php', 'timeselect'],
            'aurelia message mobile' => ['src/site/template/aurelia/layouts/message/list/default.php', 'timeselect-mobile'],
            'agora topic list'       => ['src/site/template/agora/layouts/topic/list/default.php', 'timeselect'],
        ];
    }

    public function testNothingSubmitsOnChange(): void
    {
        foreach (array_merge(self::FORUM_JUMPS, self::TIME_FILTERS) as $file) {
            $this->assertStringNotContainsString('this.form.submit()', Files::read($file), $file);
        }
    }

    #[DataProvider('timeFilterForms')]
    public function testTheTimeFilterFormHasASubmitButton(string $layout, string $id): void
    {
        $form = $this->form(Files::read($layout), $id, $layout);

        $this->assertStringContainsString('<button type="submit"', $form, $layout . '#' . $id);
        $this->assertStringContainsString("Text::_('COM_KUNENA_FILTER_APPLY')", $form, $layout . '#' . $id);
        $this->assertStringContainsString("HTMLHelper::_('form.token')", $form, $layout . '#' . $id);
    }

    public function testTheForumJumpHasASubmitButton(): void
    {
        $layout  = 'src/site/template/aurelia/layouts/widget/forumjump/default.php';
        $content = Files::read($layout);
        $form    = $this->form($content, 'jumpto', $layout);

        $this->assertStringContainsString('<button type="submit"', $form);
        $this->assertStringContainsString("Text::_('COM_KUNENA_GO')", $form);
        $this->assertStringContainsString('name="view" value="category"', $form);
        $this->assertStringContainsString('name="task" value="jump"', $form);
        $this->assertStringContainsString('use Joomla\CMS\Language\Text;', $content);
    }

    public function testTheDropDownsHaveAnAccessibleName(): void
    {
        foreach (self::TIME_FILTERS as $file) {
            $this->assertStringContainsString("aria-label=\"' . Text::_('COM_KUNENA_FILTER_TIME') . '\"", Files::read($file), $file);
            $this->assertStringContainsString('class="form-select form-select-sm filter"', Files::read($file), $file . ': the select is not as small as its btn-sm button.');
        }

        foreach (self::FORUM_JUMPS as $file) {
            $this->assertStringContainsString("aria-label=\"' . Text::_('COM_KUNENA_CATEGORY') . '\"", Files::read($file), $file);
        }

        $this->assertStringNotContainsString('class="class=', Files::read('src/site/src/Controller/Topic/Listing/ListDisplay.php'));
    }

    public function testTheNewKeyHasAnEnglishText(): void
    {
        $strings = Files::languageFolder('src/site/language/en-GB');

        $this->assertArrayHasKey('COM_KUNENA_FILTER_TIME', $strings);
        $this->assertNotSame('', $strings['COM_KUNENA_FILTER_TIME']);
    }

    /**
     * The markup of the form with the given id, from its opening tag to its closing tag.
     */
    private function form(string $content, string $id, string $layout): string
    {
        $position = strpos($content, 'id="' . $id . '"');

        $this->assertNotFalse($position, $layout . ' has no form #' . $id);

        $start = strrpos(substr($content, 0, $position), '<form');
        $end   = strpos($content, '</form>', $position);

        $this->assertNotFalse($start, $layout . ': #' . $id . ' is not in a form.');
        $this->assertNotFalse($end, $layout . ': the form #' . $id . ' is not closed.');

        return substr($content, $start, $end + \strlen('</form>') - $start);
    }
}
