<?php

/**
 * Kunena Component
 *
 * Regression test: the template editor may only write .css and .scss files inside its own folders. It used to accept
 * any file name (shell.php) and a template name with path parts.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Template;

use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EditablePathTest extends TestCase
{
    private const CONTROLLER = __DIR__ . '/../../../src/admin/src/Controller/TemplateController.php';
    private const MODEL      = __DIR__ . '/../../../src/admin/src/Model/TemplateModel.php';

    private string $dir;

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Template/KunenaTemplateHelper.php';
    }

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kunena-editable-' . bin2hex(random_bytes(4));
        mkdir($this->dir . DIRECTORY_SEPARATOR . 'css', 0777, true);
        file_put_contents($this->dir . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'custom.css', 'a{}');
        file_put_contents($this->dir . DIRECTORY_SEPARATOR . 'secret.css', 'b{}');
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            @unlink($file);
        }

        @unlink($this->dir . DIRECTORY_SEPARATOR . 'secret.css');
        @rmdir($this->dir . DIRECTORY_SEPARATOR . 'css');
        @rmdir($this->dir);
    }

    private function css(): string
    {
        return $this->dir . DIRECTORY_SEPARATOR . 'css';
    }

    public function testAcceptsAPlainFileWithTheRightExtension(): void
    {
        $path = KunenaTemplateHelper::getEditablePath($this->css(), 'custom.css', 'css');

        $this->assertSame(realpath($this->css()) . DIRECTORY_SEPARATOR . 'custom.css', $path);
    }

    public function testAcceptsANewFileInsideTheFolder(): void
    {
        $this->assertNotNull(KunenaTemplateHelper::getEditablePath($this->css(), 'brand-new_file.css', 'css'));
        $this->assertNotNull(KunenaTemplateHelper::getEditablePath($this->css(), 'kunena.min.css', 'css'));
    }

    public function testExtensionIsCaseInsensitive(): void
    {
        $this->assertNotNull(KunenaTemplateHelper::getEditablePath($this->css(), 'CUSTOM.CSS', 'css'));
    }

    #[DataProvider('scriptNames')]
    public function testRejectsScriptsAndWrongExtensions(string $name): void
    {
        $this->assertNull(KunenaTemplateHelper::getEditablePath($this->css(), $name, 'css'), $name);
    }

    public static function scriptNames(): array
    {
        return [
            'php'              => ['shell.php'],
            'php in the middle' => ['shell.php.css'],
            'phtml in middle'  => ['x.phtml.css'],
            'phar in middle'   => ['x.PHAR.css'],
            'scss for css'     => ['custom.scss'],
            'htaccess'         => ['.htaccess'],
            'hidden css'       => ['.css'],
            'no extension'     => ['custom'],
            'empty'            => [''],
            'trailing dot'     => ['custom.css.'],
        ];
    }

    #[DataProvider('pathNames')]
    public function testRejectsPathParts(string $name): void
    {
        $this->assertNull(KunenaTemplateHelper::getEditablePath($this->css(), $name, 'css'), $name);
    }

    public static function pathNames(): array
    {
        return [
            'parent'           => ['../secret.css'],
            'deep parent'      => ['../../../../etc/x.css'],
            'subfolder'        => ['sub/custom.css'],
            'absolute'         => ['/etc/passwd.css'],
            'windows parent'   => ['..\\secret.css'],
            'windows absolute' => ['C:\\x.css'],
        ];
    }

    public function testRejectsNonStrings(): void
    {
        $this->assertNull(KunenaTemplateHelper::getEditablePath($this->css(), null, 'css'));
        $this->assertNull(KunenaTemplateHelper::getEditablePath($this->css(), ['custom.css'], 'css'));
    }

    public function testRejectsAMissingFolder(): void
    {
        $this->assertNull(KunenaTemplateHelper::getEditablePath($this->dir . DIRECTORY_SEPARATOR . 'nope', 'custom.css', 'css'));
    }

    public function testRejectsALinkWhichLeavesTheFolder(): void
    {
        $link = $this->css() . DIRECTORY_SEPARATOR . 'link.css';

        if (!@symlink($this->dir . DIRECTORY_SEPARATOR . 'secret.css', $link)) {
            $this->markTestSkipped('Symbolic links can not be created here.');
        }

        $this->assertNull(KunenaTemplateHelper::getEditablePath($this->css(), 'link.css', 'css'));
    }

    #[DataProvider('templateNames')]
    public function testTemplateNameValidation(mixed $name, bool $valid): void
    {
        $this->assertSame($valid, KunenaTemplateHelper::isValidName($name));
    }

    public static function templateNames(): array
    {
        return [
            'aurelia'      => ['aurelia', true],
            'with hyphen'  => ['my-template_2', true],
            'parent'       => ['..', false],
            'traversal'    => ['../../..', false],
            'slash'        => ['a/b', false],
            'backslash'    => ['a\\b', false],
            'dot'          => ['a.b', false],
            'empty'        => ['', false],
            'null'         => [null, false],
            'array'        => [['aurelia'], false],
            'newline'      => ["aurelia\n", false],
        ];
    }

    // The controller and the model have to use these checks.

    public function testControllerUsesTheChecksBeforeWriting(): void
    {
        $source = file_get_contents(self::CONTROLLER);

        foreach (['internalSaveScss', 'internalSaveCss'] as $method) {
            preg_match('/function ' . $method . '\(\)[^{]*\{(.*?)\n    \}/s', $source, $m);
            $body = $m[1] ?? '';

            $this->assertStringContainsString('KunenaTemplateHelper::isInstalled(', $body, $method);
            $this->assertStringContainsString('KunenaTemplateHelper::getEditablePath(', $body, $method);
            $this->assertLessThan(strpos($body, 'File::write('), strpos($body, 'getEditablePath('), "$method: check before the write");
            $this->assertDoesNotMatchRegularExpression("/KPATH_(SITE|MEDIA) \\. '[^']*' \\. \\\$filename/", $body, "$method: no path built from the file name");
        }
    }

    public function testChooseAndEditStepsStopOnInvalidInput(): void
    {
        $source = file_get_contents(self::CONTROLLER);

        foreach (['editscss', 'editCss'] as $method) {
            preg_match('/function ' . $method . '\(\)[^{]*\{(.*?)\n    \}/s', $source, $m);
            $body = $m[1] ?? '';

            $this->assertMatchesRegularExpression('/setRedirect\([^;]*;\s*return;/s', $body, "$method must return after the warning");
            $this->assertStringContainsString('getEditablePath(', $body);
        }
    }

    public function testModelReadsOnlyThroughTheCheck(): void
    {
        $source = file_get_contents(self::MODEL);

        $this->assertSame(2, substr_count($source, 'KunenaTemplateHelper::getEditablePath('));
        $this->assertDoesNotMatchRegularExpression("/file_get_contents\\(KPATH_/", $source);
    }
}
