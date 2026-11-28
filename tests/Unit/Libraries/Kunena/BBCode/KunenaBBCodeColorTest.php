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

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * [color] takes the colours of rgb() and hsl() (CKEditor writes the colours of its palette as hsl(0, 75%, 60%)): the tag of such a
 * colour was left as text in the post. Nothing which could leave the style (a quote, a ";") is taken.
 */
class KunenaBBCodeColorTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    protected function setUp(): void
    {
        // The parser is in the folder External of the library, and the library of the tags is in the file of KunenaBBCode.
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        // The file of KunenaBBCode loads the parser from the folder of the library.
        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    public static function colours(): array
    {
        return [
            'hsl'        => ['hsl(0, 75%, 60%)'],
            'rgb'        => ['rgb(255,0,0)'],
            'rgba'       => ['rgba(255, 0, 0, 0.5)'],
            'a name'     => ['red'],
            'a hex value' => ['#ff0000'],
        ];
    }

    #[DataProvider('colours')]
    public function testTheColourIsTaken(string $colour): void
    {
        $html = (new BBCode(self::bare(KunenaBBCodeLibrary::class)))->Parse("[color={$colour}]x[/color]");

        $this->assertStringStartsWith('<span style="color: ', $html);
        $this->assertStringNotContainsString('[color', $html);
    }

    public static function refused(): array
    {
        return [
            'another property'  => ['red;background:url(x)'],
            'a quote'           => ['red" onmouseover="x'],
            'a function of CSS' => ['expression(alert(1))'],
        ];
    }

    #[DataProvider('refused')]
    public function testAnythingElseIsRefused(string $colour): void
    {
        $html = (new BBCode(self::bare(KunenaBBCodeLibrary::class)))->Parse("[color={$colour}]x[/color]");

        $this->assertStringNotContainsString('<span', $html);
    }
}
