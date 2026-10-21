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

namespace Kunena\Tests\Unit\Codebase;

use FilesystemIterator;
use Kunena\Tests\Unit\Support\Files;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Every PHP file of the source (one test case for each file): it can be parsed, a file which Joomla loads refuses to run on its
 * own, and a class is in the file and the folder which its namespace says (the case of the names matters on Linux).
 */
class AllPhpFilesTest extends UnitTestCase
{
    /**
     * Folders of other projects which Kunena delivers: they are not checked for the rules of Kunena.
     */
    private const EXTERNAL = 'src/libraries/kunena/External/';

    /**
     * Files which are not loaded by Joomla and so have no guard: the file which serves the attachments is a standalone entry
     * point and defines _JEXEC itself.
     */
    private const NO_GUARD = ['src/site/src/Application/attachment.php'];

    /**
     * The namespaces of the code of Kunena and the folders they are in.
     */
    private const ROOTS = [
        'Kunena\\Forum\\Libraries\\' => 'src/libraries/kunena/src/',
        'Kunena\\Forum\\Site\\'      => 'src/site/src/',
        'Kunena\\Forum\\Administrator\\' => 'src/admin/src/',
    ];

    /**
     * Files whose class is not where its namespace says (found by the first run of this test). A new file like this fails; a file
     * which is put right is taken out of the list. Why they are different:
     * - the views for a format (view.raw.php, view.json.php) are found by Joomla by the name of the file;
     * - attachment.php is a standalone entry point, and KunenaInstaller is loaded by the installer;
     * - View/Default is a folder with the name of a keyword;
     * - Inbox/HtmlView.php declares the namespace of the Credits view (a copy of that file: the same class twice);
     * - the other ones differ in the case of a letter (item/Item, ForumJump/Forumjump), which an autoloader on Linux does not accept.
     */
    private const KNOWN_MISMATCHES = [
        'src/admin/src/View/Categories/HtmlViewRaw.php',
        'src/libraries/kunena/src/Installer/KunenaInstaller.php',
        'src/site/src/Application/attachment.php',
        'src/site/src/Controller/User/Item/UserItemDisplay.php',
        'src/site/src/Layout/Widget/WidgetForumjump.php',
        'src/site/src/View/Category/view.raw.php',
        'src/site/src/View/Default/HtmlView.php',
        'src/site/src/View/Inbox/HtmlView.php',
        'src/site/src/View/Topic/view.json.php',
        'src/site/src/View/Topic/view.raw.php',
        'src/site/src/View/Topics/view.json.php',
        'src/site/src/View/User/view.raw.php',
    ];

    /**
     * Classes which two files declare.
     */
    private const KNOWN_DOUBLES = ['Kunena' . "\x5c" . 'Forum' . "\x5c" . 'Site' . "\x5c" . 'View' . "\x5c" . 'Credits' . "\x5c" . 'HtmlView'];

    /** @var string[]|null */
    private static ?array $files = null;

    /**
     * @return  string[]  The paths of the files, relative to the root of the project.
     */
    private static function files(): array
    {
        if (self::$files === null) {
            self::$files = [];
            $root        = str_replace('\\', '/', Files::path('src'));
            $iterator    = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));

            foreach ($iterator as $file) {
                $path = str_replace('\\', '/', $file->getPathname());

                if (str_ends_with($path, '.php')) {
                    self::$files[] = 'src/' . substr($path, \strlen($root) + 1);
                }
            }

            sort(self::$files);
        }

        return self::$files;
    }

    public static function files_(): array
    {
        $cases = [];

        foreach (self::files() as $file) {
            $cases[$file] = [$file];
        }

        return $cases;
    }

    public static function firstParty(): array
    {
        return array_filter(self::files_(), static fn ($case) => !str_starts_with($case[0], self::EXTERNAL));
    }

    public function testTheSourceHasManyFiles(): void
    {
        $this->assertGreaterThan(800, \count(self::files()));
    }

    #[DataProvider('files_')]
    public function testTheFileCanBeParsed(string $file): void
    {
        try {
            $tokens = token_get_all(Files::read($file), TOKEN_PARSE);
        } catch (Throwable $e) {
            $this->fail("$file can not be parsed: " . $e->getMessage());
        }

        $this->assertNotEmpty($tokens);
    }

    #[DataProvider('firstParty')]
    public function testTheFileRefusesToRunOnItsOwn(string $file): void
    {
        if (\in_array($file, self::NO_GUARD, true)) {
            $this->assertStringContainsString("define('_JEXEC'", str_replace('\\define', 'define', Files::read($file)));

            return;
        }

        $this->assertMatchesRegularExpression(
            '/defined\(\s*[\'"](?:_JEXEC|JPATH_BASE)[\'"]\s*\)/',
            Files::read($file),
            "$file has no guard against direct access (defined('_JEXEC') or die)."
        );
    }

    /**
     * The place of the file for the class which it declares, null when the file has no class or is not part of the code of the forum.
     *
     * @return  array{0:string,1:string}|null  The class and the expected path.
     */
    private static function expectedPath(string $code): ?array
    {
        $slash = \chr(92);

        if (!preg_match('/^(?:abstract\s+|final\s+)?(?:class|interface|trait|enum)\s+(\w+)/m', $code, $class) || !preg_match('/^namespace\s+([\w' . $slash . $slash . ']+);/m', $code, $namespace)) {
            return null;
        }

        $name = $namespace[1] . $slash . $class[1];

        foreach (self::ROOTS as $prefix => $folder) {
            if (str_starts_with($name . $slash, $prefix)) {
                return [$name, $folder . str_replace($slash, '/', substr($name, \strlen($prefix))) . '.php'];
            }
        }

        return null;
    }

    #[DataProvider('firstParty')]
    public function testTheClassIsInTheFileOfItsNamespace(string $file): void
    {
        $expected = self::expectedPath(Files::read($file));

        // The templates and the pages have no class, and a plugin or a template has a folder of its own.
        if ($expected === null) {
            $this->assertTrue(true, 'The file has no class of the forum.');

            return;
        }

        if (\in_array($file, self::KNOWN_MISMATCHES, true)) {
            $this->assertNotSame($expected[1], $file, "$file is where its class says now: take it out of KNOWN_MISMATCHES.");

            return;
        }

        $this->assertSame($expected[1], $file, "The class {$expected[0]} must be in {$expected[1]}.");
    }

    public function testNoClassIsDeclaredInTwoFiles(): void
    {
        $declared = [];

        foreach (self::files() as $file) {
            if (str_starts_with($file, self::EXTERNAL)) {
                continue;
            }

            $code = Files::read($file);

            if (preg_match('/^(?:abstract\s+|final\s+)?(?:class|interface|trait|enum)\s+(\w+)/m', $code, $class) && preg_match('/^namespace\s+([\w' . \chr(92) . \chr(92) . ']+);/m', $code, $namespace)) {
                $declared[strtolower($namespace[1] . \chr(92) . $class[1])][] = $file;
            }
        }

        $doubles = array_keys(array_filter($declared, static fn ($files) => \count($files) > 1));
        $known   = array_map('strtolower', self::KNOWN_DOUBLES);

        $this->assertSame([], array_values(array_diff($doubles, $known)), 'These classes are declared in two files: ' . implode(', ', array_diff($doubles, $known)));
        $this->assertSame([], array_values(array_diff($known, $doubles)), 'A class of KNOWN_DOUBLES is declared once now: take it out of the list.');
    }
}
