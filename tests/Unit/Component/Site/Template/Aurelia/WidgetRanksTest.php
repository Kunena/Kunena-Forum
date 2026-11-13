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
 * With "Ranks by CSS" the rank dots come from the widget layout: they are wrapped in an inline span, never in a list item, so
 * they sit where a rank image sits in the profile and in the profile box of a message.
 */
class WidgetRanksTest extends UnitTestCase
{
    private const LAYOUT = 'src/site/template/aurelia/layouts/widget/ranks/default.php';

    public static function iconSets(): array
    {
        return [
            'fa, not special'        => ['fa', 0, 'fa-circle', 5],
            'fa, special'            => ['fa', 1, 'fa-circle', 6],
            'glyphicon, not special' => ['svg', 0, 'glyphicon-one-fine-dot', 5],
            'glyphicon, special'     => ['svg', 1, 'glyphicon-one-fine-dot', 6],
        ];
    }

    #[DataProvider('iconSets')]
    public function testTheDotsAreWrappedInAnInlineSpan(string $topicicontype, int $rankSpecial, string $dotClass, int $dots): void
    {
        $output = trim($this->render($topicicontype, $rankSpecial));

        $this->assertStringNotContainsString('<li', $output);
        $this->assertStringNotContainsString('list-group-item', $output);
        $this->assertStringStartsWith('<span class="kwho-admin">', $output);
        $this->assertStringEndsWith('</span>', $output);
        $this->assertSame($dots, substr_count($output, $dotClass));
        $this->assertStringContainsString('style="color:#e8f7ff;"', $output);
    }

    private function render(string $topicicontype, int $rankSpecial): string
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        $layout = (object) [
            'type'          => 'admin',
            'topicicontype' => $topicicontype,
            'rank'          => (object) ['rankSpecial' => $rankSpecial],
            'stylesecond'   => '',
            'stylethird'    => '',
            'stylefourth'   => '',
            'stylelast'     => 'style="color:#e8f7ff;"',
        ];

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
