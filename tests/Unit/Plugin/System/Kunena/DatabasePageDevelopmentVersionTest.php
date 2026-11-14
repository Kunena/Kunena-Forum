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

namespace Kunena\Tests\Unit\Plugin\System\Kunena;

use Kunena\Forum\Plugin\System\Kunena\Extension\Kunena;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A development site (a git checkout) has the placeholder of the source as the version of its manifest. The Database page of the
 * Joomla installer then warns that the version of Kunena does not match it. That warning is taken out of the page: the line of
 * the list, one problem less in the badge (green when none is left) and the message above the page when no problem is left.
 *
 * The strings are those of Joomla, given to the method (the unit tests have no application, so no language).
 */
class DatabasePageDevelopmentVersionTest extends UnitTestCase
{
    private const ERRORS = 'There are tables not up to date!';

    private const OK = 'All database table structures are up to date.';

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();
    }

    public function testTheWarningIsTakenOutAndTheBadgeTurnsGreenWhenItWasTheOnlyProblem(): void
    {
        $out = $this->hide($this->page($this->row(1, 'warning', true)));

        $this->assertStringNotContainsString($this->placeholder(), $out);
        $this->assertMatchesRegularExpression('/badge bg-success" tabindex="0">\s*No Problems/', $out);
        $this->assertStringContainsString('<strong>No Problems</strong>', $out);

        // The other lines of the list stay.
        $this->assertStringContainsString('<li>3 changes were checked</li>', $out);
    }

    public function testTheMessageAboveThePageSaysThatAllIsUpToDateWhenNoProblemIsLeft(): void
    {
        $out = $this->hide($this->page($this->row(1, 'warning', true)));

        $this->assertStringNotContainsString('{"warning":[', $out);
        $this->assertStringContainsString('{"info":[' . json_encode(self::OK) . ']}', $out);
    }

    public function testARealProblemStaysAndTheBadgeCountsOneLess(): void
    {
        $out = $this->hide($this->page($this->row(2, 'danger', true)));

        $this->assertStringNotContainsString($this->placeholder(), $out);
        $this->assertMatchesRegularExpression('/badge bg-danger" tabindex="0">\s*One Problem/', $out);
        $this->assertStringContainsString('<li>The table is wrong.</li>', $out);

        // A problem is left: the message above the page stays.
        $this->assertStringContainsString('{"warning":[' . json_encode(self::ERRORS) . ']}', $out);
    }

    public function testAPageWithoutThePlaceholderIsNotChanged(): void
    {
        $page = $this->page($this->row(1, 'warning', false));

        $this->assertSame($page, $this->hide($page));
    }

    public function testAnotherExtensionWithAProblemKeepsTheMessageAboveThePage(): void
    {
        $out = $this->hide($this->page($this->row(1, 'warning', true) . $this->row(1, 'danger', false)));

        $this->assertStringContainsString('{"warning":[' . json_encode(self::ERRORS) . ']}', $out);
    }

    private function hide(string $page): string
    {
        $plugin = self::bare(Kunena::class);

        return $plugin->hidePlaceholderWarning($page, [self::class, 'plural'], self::ERRORS, self::OK);
    }

    public static function plural(int $count): string
    {
        return match ($count) {
            0       => 'No Problems',
            1       => 'One Problem',
            default => $count . ' Problems',
        };
    }

    private function placeholder(): string
    {
        return '@' . 'kunenaversion' . '@';
    }

    /**
     * A row of the table of the Database page, as the template of Joomla makes it.
     */
    private function row(int $problems, string $class, bool $withPlaceholder): string
    {
        $text = self::plural($problems);
        $list = '';

        if ($withPlaceholder) {
            $list .= '<li>Database update version (<strong>8.0.0-ALPHA1-DEV</strong>) does not match <strong>com_kunena</strong> version (<strong>' . $this->placeholder() . '</strong>).</li>';
        }

        if ($problems > ($withPlaceholder ? 1 : 0)) {
            $list .= '<li>The table is wrong.</li>';
        }

        $list .= '<li>3 changes were checked</li>';

        return '<tr class="row0"><td class="text-center"><input type="checkbox" name="cid[]" value="249"></td>'
            . '<th scope="row">com_kunena<div class="small">Kunena</div></th>'
            . '<td><span class="badge bg-' . $class . '" tabindex="0">' . "\n                                                " . $text . "\n                                            " . '</span>'
            . '<div role="tooltip" id="tip0"><strong>' . $text . '</strong><ul>' . $list . '</ul></div></td></tr>';
    }

    private function page(string $rows): string
    {
        $messages = '{"warning":[' . json_encode(self::ERRORS) . ']}';

        return '<script type="application/json" class="joomla-script-options new">{"joomla.messages":[' . $messages . ']}</script>'
            . '<table class="table"><tbody>' . $rows . '</tbody></table>';
    }
}
