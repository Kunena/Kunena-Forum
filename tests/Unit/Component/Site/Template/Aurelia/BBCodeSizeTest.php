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
 * [size=N] has the sizes 1 to 7, as the editor. The layout knew six: the largest size of the editor (7), and any other number,
 * was written as "font-size:7", which is no size for a browser, so the text was shown in the normal size.
 */
class BBCodeSizeTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();

        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }
    }

    private function render(mixed $size): string
    {
        $layout = new class () {
            public mixed $size = null;

            public string $content = 'Text';

            public function render(string $file): string
            {
                ob_start();

                include $file;

                return trim((string) ob_get_clean());
            }
        };
        $layout->size = $size;

        return $layout->render(Files::path('src/site/template/aurelia/layouts/bbcode/size/default.php'));
    }

    public static function sizes(): array
    {
        return [
            'smallest'          => [1, '0.6em'],
            'normal'            => [3, '1em'],
            'sixth'             => [6, '1.8em'],
            'largest of editor' => [7, '2.2em'],
            'above the scale'   => [40, '2.2em'],
            'as text'           => ['7', '2.2em'],
            'between two sizes' => ['4.5', '1.2em'],
            'none'              => [0, '1em'],
        ];
    }

    #[DataProvider('sizes')]
    public function testTheSizeIsOneOfTheScale(mixed $size, string $css): void
    {
        $this->assertSame('<span style="font-size:' . $css . '">Text</span>', $this->render($size));
    }
}
