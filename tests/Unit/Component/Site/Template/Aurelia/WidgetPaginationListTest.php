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

use Closure;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The pagination is one list: the page numbers carry no responsive display class, so a blocker which hides `.d-sm-block` can't
 * hide them. Only Start, Prev, Next, End and the "..." gaps are hidden on a small screen.
 */
class WidgetPaginationListTest extends UnitTestCase
{
    private const LAYOUT = 'src/site/template/aurelia/layouts/widget/pagination/list/default.php';

    public function testOneNavWithOneListAndNoResponsiveClassOnTheNav(): void
    {
        $output = $this->render([1, 2, 3, 7]);

        $this->assertSame(1, substr_count($output, '<nav'));
        $this->assertSame(1, substr_count($output, '<ul class="pagination'));
        $this->assertStringNotContainsString('d-sm-none', $output);
        $this->assertMatchesRegularExpression('/<nav>/', $output);
        $this->assertDoesNotMatchRegularExpression('/<nav[^>]*\bd-/', $output);
    }

    public function testEachPageAppearsOnceWithoutAResponsiveClass(): void
    {
        $output = $this->render([1, 2, 3, 7]);

        foreach (['page 1', 'page 2', 'page 3', 'page 7'] as $page) {
            $this->assertSame(1, substr_count($output, '>' . $page . '<'), $page);
            $this->assertStringContainsString('<li class="page-item">' . $page . '</li>', $output);
        }
    }

    public function testTheExtrasAreHiddenBelowSm(): void
    {
        $output = $this->render([1, 2, 3, 7]);

        foreach (['Start', 'Prev', 'Next', 'End'] as $text) {
            $this->assertStringContainsString('<li class="page-item d-none d-sm-block">' . $text . '</li>', $output);
        }

        $this->assertSame(1, substr_count($output, '...'));
        $this->assertStringContainsString('<li class="page-item disabled d-none d-sm-block"><a class="">...</a></li>', $output);
    }

    public function testTheOrderIsStartPrevPagesNextEnd(): void
    {
        $output = $this->render([1, 2, 3, 7]);

        preg_match_all('/<li[^>]*>(.*?)<\/li>/', $output, $matches);

        $this->assertSame(
            ['Start', 'Prev', 'page 1', 'page 2', 'page 3', '<a class="">...</a>', 'page 7', 'Next', 'End'],
            $matches[1]
        );
    }

    public function testNothingIsPrintedForNoPages(): void
    {
        $this->assertSame('', trim($this->render([])));
    }

    public function testNothingIsPrintedForOnePageWithoutDisplay(): void
    {
        $this->assertSame('', trim($this->render([1])));
    }

    public function testOnePageIsPrintedWithDisplay(): void
    {
        $output = $this->render([1], true);

        $this->assertSame(1, substr_count($output, '<nav'));
        $this->assertStringContainsString('<li class="page-item">page 1</li>', $output);
    }

    private function render(array $pageNumbers, bool $display = false): string
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        $pages = [];

        foreach ($pageNumbers as $number) {
            $pages[$number] = (object) ['text' => 'page ' . $number];
        }

        $data = (object) [
            'start'    => (object) ['text' => 'Start'],
            'previous' => (object) ['text' => 'Prev'],
            'next'     => (object) ['text' => 'Next'],
            'end'      => (object) ['text' => 'End'],
            'pages'    => $pages,
        ];

        $layout = new class ($data) {
            public object $pagination;

            // Left uninitialised unless the test sets it, so isset() is false as for a layout without display.
            public bool $display;

            public function __construct(object $data)
            {
                $this->pagination = new class ($data) {
                    public function __construct(private object $data)
                    {
                    }

                    public function getData(): object
                    {
                        return $this->data;
                    }
                };
            }

            public function subLayout(string $path): object
            {
                return new class () {
                    private array $properties = [];

                    public function set(string $key, $value): static
                    {
                        $this->properties[$key] = $value;

                        return $this;
                    }

                    public function __toString(): string
                    {
                        $extra = isset($this->properties['extraClass']) ? ' ' . $this->properties['extraClass'] : '';

                        return '<li class="page-item' . $extra . '">' . $this->properties['item']->text . '</li>';
                    }
                };
            }
        };

        if ($display) {
            $layout->display = true;
        }

        $file   = Files::path(self::LAYOUT);
        $render = function () use ($file): void {
            include $file;
        };

        ob_start();

        try {
            Closure::bind($render, $layout)();
        } finally {
            $output = ob_get_clean();
        }

        return $output;
    }
}
