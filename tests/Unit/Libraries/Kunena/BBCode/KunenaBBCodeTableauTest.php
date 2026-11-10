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
 * [tableau]: two parts of the address were printed into attributes with single quotes, and a single quote is not always encoded
 * by the parser. The parts are plain names now, and the layout encodes single quotes too.
 */
class KunenaBBCodeTableauTest extends UnitTestCase
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

    private function links(string $html): \DOMNodeList
    {
        $previous = libxml_use_internal_errors(true);
        $dom      = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $dom->getElementsByTagName('a');
    }

    /**
     * A layout of the template with its variables (the contract of KunenaLayout: properties, and escape()).
     */
    private function layout(string $file, array $properties): string
    {
        $view = new #[\AllowDynamicProperties] class () {
            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }
        };

        foreach ($properties as $name => $value) {
            $view->$name = $value;
        }

        $path = Files::path($file);

        ob_start();
        (function () use ($path) {
            include $path;
        })->call($view);

        return (string) ob_get_clean();
    }

    public function testAddressOfAViewGivesTheWorkbookAndTheSheet(): void
    {
        $this->assertSame(
            ['VGContest_InteractiveFiscalCalendar', 'FYCalendar'],
            KunenaBBCodeLibrary::tableauNames('https://public.tableau.com/views/VGContest_InteractiveFiscalCalendar/FYCalendar?:embed=y&amp;publish=yes')
        );
        $this->assertSame(['Book-1.2', 'Sheet_1'], KunenaBBCodeLibrary::tableauNames(' https://public.tableau.com/views/Book-1.2/Sheet_1 '));
    }

    public static function noViews(): array
    {
        return [
            'single quote'        => ["https://public.tableau.com/views/x' onerror='alert(1)/y?z"],
            'single quote, sheet' => ["https://public.tableau.com/views/x/y' autofocus onfocus='alert(1)?z"],
            'encoded quote'       => ['https://public.tableau.com/views/x&#039;y/z?a'],
            'space'               => ['https://public.tableau.com/views/x y/z?a'],
            'a third part'        => ['https://public.tableau.com/views/a/b/c?d'],
            'up the path'         => ['https://public.tableau.com/views/../x?d'],
            'another host'        => ['https://publicXtableau.com/views/a/b?c'],
            'host as a part'      => ['https://evil.example/https://public.tableau.com/views/a/b?c'],
            'http'                => ['http://public.tableau.com/views/a/b?c'],
            'a line after it'     => ["https://public.tableau.com/views/a/b\n' onerror='x"],
            'nothing'             => [''],
        ];
    }

    #[DataProvider('noViews')]
    public function testAnythingElseIsNoView(string $content): void
    {
        $this->assertNull(KunenaBBCodeLibrary::tableauNames($content));
    }

    public function testLayoutOfTableauEncodesSingleQuotesToo(): void
    {
        $html = $this->layout(
            'src/site/template/aurelia/layouts/bbcode/tableau/default.php',
            ['vizualization' => "x' onerror='alert(1)", 'vizualizationTab' => 'y"z<b>']
        );

        $this->assertStringNotContainsString("' onerror='", $html);
        $this->assertSame(3, substr_count($html, 'x&#039; onerror=&#039;alert(1)/y&quot;z&lt;b&gt;'));
    }

    public function testTagOfTableauUsesTheCheckedNames(): void
    {
        $this->assertSame(1, preg_match('/public function doTableau\([^)]*\)\s*\{(.*?)\n    \}\n/s', Files::read(self::BBCODE), $m));
        $this->assertStringContainsString('self::tableauNames(', $m[1]);
        $this->assertStringNotContainsString('preg_match(', $m[1], 'One place reads the address.');
    }
}
