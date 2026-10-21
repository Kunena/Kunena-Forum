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

use FilesystemIterator;
use Kunena\Tests\Unit\Support\Files;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A script of the template which needs a library: the layout which loads the script loads the library too (the user list loaded
 * search.js without tribute.js, so the page had the error "Tribute is not defined").
 */
class ScriptDependenciesTest extends UnitTestCase
{
    private const TEMPLATE = 'src/site/template/aurelia';

    /**
     * Script => the global which it uses => the file which gives it.
     */
    private const NEEDS = [
        'assets/js/search.js' => ['Tribute', "addScript('tribute.js')"],
    ];

    /**
     * @return  array<string,string>  Path (relative to the template) => code of the layouts.
     */
    private static function layouts(): array
    {
        $layouts = [];
        $root    = str_replace('\\', '/', Files::path(self::TEMPLATE . '/layouts'));

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if (str_ends_with($path, '.php')) {
                $layouts[substr($path, \strlen($root) + 1)] = (string) file_get_contents($path);
            }
        }

        return $layouts;
    }

    public function testTheScriptsReallyUseTheLibraries(): void
    {
        foreach (self::NEEDS as $script => [$global]) {
            $this->assertStringContainsString("new $global(", Files::read(self::TEMPLATE . '/' . $script), "$script does not use $global (anymore): update NEEDS.");
        }
    }

    public function testALayoutWhichLoadsAScriptLoadsItsLibrary(): void
    {
        $checked = 0;

        foreach (self::NEEDS as $script => [, $loader]) {
            foreach (self::layouts() as $path => $code) {
                if (str_contains($code, "addScript('$script')")) {
                    $checked++;

                    $this->assertStringContainsString($loader, $code, "The layout $path loads $script but not its library ($loader).");
                }
            }
        }

        $this->assertGreaterThanOrEqual(2, $checked, 'The user list and the search form load search.js.');
    }

    public function testTheLibraryOfTheScriptIsThere(): void
    {
        $this->assertFileExists(Files::path('src/media/kunena/core/js/tribute.js'));
    }
}
