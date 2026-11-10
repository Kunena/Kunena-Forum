<?php

/**
 * Kunena Component
 *
 * [tableau] printed two parts of the address into attributes with single quotes, and a single quote is not always encoded by
 * the parser. The parts are plain names now, and the layout encodes single quotes too.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if (!\defined('_JEXEC')) {
    \define('_JEXEC', 1);
}

class TableauEmbedTest extends TestCase
{
    private const SRC    = __DIR__ . '/../../../src';
    private const LAYOUT = self::SRC . '/site/template/aurelia/layouts/bbcode/tableau/default.php';

    private static function loadBBCode(): void
    {
        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', realpath(self::SRC . '/libraries/kunena'));
        }

        if (!class_exists(KunenaMessageHelper::class, false)) {
            require_once __DIR__ . '/../BBCode/fixtures/quote_stubs.php';
        }

        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = KPATH_FRAMEWORK . '/External/Nbbc/src/' . substr($class, 5) . '.php';

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        require_once self::SRC . '/libraries/kunena/src/BBCode/KunenaBBCode.php';
    }

    public function testAddressOfAViewGivesTheWorkbookAndTheSheet(): void
    {
        self::loadBBCode();

        $this->assertSame(
            ['VGContest_InteractiveFiscalCalendar_ShinichiroMurakami', 'FYCalendar'],
            KunenaBBCodeLibrary::tableauNames('https://public.tableau.com/views/VGContest_InteractiveFiscalCalendar_ShinichiroMurakami/FYCalendar?:embed=y&amp;:display_count=y&amp;publish=yes')
        );
        $this->assertSame(['Book-1.2', 'Sheet_1'], KunenaBBCodeLibrary::tableauNames(' https://public.tableau.com/views/Book-1.2/Sheet_1 '));
    }

    public static function noViews(): array
    {
        return [
            'single quote'           => ["https://public.tableau.com/views/x' onerror='alert(1)/y?z"],
            'single quote, sheet'    => ["https://public.tableau.com/views/x/y' autofocus onfocus='alert(1)?z"],
            'encoded quote'          => ['https://public.tableau.com/views/x&#039;y/z?a'],
            'space'                  => ['https://public.tableau.com/views/x y/z?a'],
            'a third part'           => ['https://public.tableau.com/views/a/b/c?d'],
            'up the path'            => ['https://public.tableau.com/views/../x?d'],
            'another host'           => ['https://publicXtableau.com/views/a/b?c'],
            'host as a part'         => ['https://evil.example/https://public.tableau.com/views/a/b?c'],
            'http'                   => ['http://public.tableau.com/views/a/b?c'],
            'a line after it'        => ["https://public.tableau.com/views/a/b\n' onerror='x"],
            'nothing'                => [''],
        ];
    }

    #[DataProvider('noViews')]
    public function testAnythingElseIsNoView(string $content): void
    {
        self::loadBBCode();

        $this->assertNull(KunenaBBCodeLibrary::tableauNames($content));
    }

    public function testLayoutEncodesSingleQuotesToo(): void
    {
        // Same contract as KunenaLayout: the variables are properties.
        $view = new class () {
            public $vizualization    = "x' onerror='alert(1)";
            public $vizualizationTab = 'y"z<b>';
        };

        $layout = self::LAYOUT;

        ob_start();
        (function () use ($layout) {
            include $layout;
        })->call($view);
        $html = ob_get_clean();

        $this->assertStringNotContainsString("' onerror='", $html);
        $this->assertStringNotContainsString('<b>', $html);
        $this->assertSame(3, substr_count($html, 'x&#039; onerror=&#039;alert(1)/y&quot;z&lt;b&gt;'));
    }

    public function testTagUsesTheCheckedNames(): void
    {
        $source = file_get_contents(self::SRC . '/libraries/kunena/src/BBCode/KunenaBBCode.php');

        $this->assertSame(1, preg_match('/public function doTableau\([^)]*\)\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));
        $this->assertStringContainsString('self::tableauNames(', $m[1]);
        $this->assertStringNotContainsString('preg_match(', $m[1], 'One place reads the address.');
    }
}
