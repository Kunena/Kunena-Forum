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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The optional extraClass of a pagination item is appended to the class of its list item; without it the item renders as before.
 */
class WidgetPaginationItemTest extends UnitTestCase
{
    private const LAYOUT = 'src/site/template/aurelia/layouts/widget/pagination/item/default.php';

    public static function items(): array
    {
        return [
            'active'   => [true, '<li class="page-item active"><a class="page-link">3</a></li>', '<li class="page-item active d-none d-sm-block"><a class="page-link">3</a></li>'],
            'disabled' => [false, '<li class="page-item disabled"><a class="page-link">3</a></li>', '<li class="page-item disabled d-none d-sm-block"><a class="page-link">3</a></li>'],
        ];
    }

    #[DataProvider('items')]
    public function testWithoutExtraClassTheItemIsUnchanged(bool $active, string $expected, string $withExtra): void
    {
        $this->assertSame($expected, $this->render($active));
    }

    #[DataProvider('items')]
    public function testTheExtraClassIsAppended(bool $active, string $expected, string $withExtra): void
    {
        $this->assertSame($withExtra, $this->render($active, 'd-none d-sm-block'));
    }

    #[DataProvider('items')]
    public function testAnEmptyExtraClassChangesNothing(bool $active, string $expected, string $withExtra): void
    {
        $this->assertSame($expected, $this->render($active, ''));
    }

    private function render(bool $active, ?string $extraClass = null): string
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        $layout = new class ((object) ['base' => null, 'active' => $active, 'text' => '3']) {
            // Left uninitialised unless the test sets it, so isset() is false as for a caller which doesn't pass it.
            public string $extraClass;

            public function __construct(public object $item)
            {
            }
        };

        if ($extraClass !== null) {
            $layout->extraClass = $extraClass;
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
