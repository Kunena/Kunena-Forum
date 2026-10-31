<?php

/**
 * Kunena Component
 *
 * Regression test:
 *
 *  - The template name of the cookie kunena_template (a value the visitor chooses) became part of file paths, a class name
 *    and a require_once. Only a plain name is accepted now.
 *  - Tasks which change the site were started by a plain link: choosing a template, deleting the pending uploads of the
 *    user, recreating the menu, changing tables, the settings of the socials and the settings of a template. They need the
 *    token of the form now (and the forms of the administrator send it).
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TokenAndTemplateNameTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once self::SRC . '/libraries/kunena/src/Template/KunenaTemplateHelper.php';
    }

    private function source(string $file): string
    {
        return file_get_contents(self::SRC . '/' . $file);
    }

    private function method(string $source, string $name): string
    {
        $this->assertSame(1, preg_match('/function ' . preg_quote($name, '/') . '\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m), $name);

        return $m[1];
    }

    // --- the cookie ---

    #[DataProvider('cookieValues')]
    public function testOnlyAPlainTemplateNameIsAccepted(mixed $value, bool $valid): void
    {
        $this->assertSame($valid, KunenaTemplateHelper::isValidName($value), var_export($value, true));
    }

    public static function cookieValues(): array
    {
        return [
            'aurelia'                  => ['aurelia', true],
            'custom template'          => ['my-template_2', true],
            'one level up'             => ['../template/aurelia', false],
            'many levels up'           => ['../../../../etc', false],
            'dots only'                => ['..', false],
            'absolute path'            => ['/etc/passwd', false],
            'windows path'             => ['C:\\Windows', false],
            'backslash traversal'      => ['..\\aurelia', false],
            'encoded dots'             => ['%2e%2e/aurelia', false],
            'double slash'             => ['aurelia//x', false],
            'null byte'                => ["aurelia\0", false],
            'new line'                 => ["aurelia\n", false],
            'space'                    => ['aurelia x', false],
            'a class name with slash'  => ['Foo\\Bar', false],
            'empty'                    => ['', false],
            'null'                     => [null, false],
            'array from the cookie'    => [['aurelia'], false],
        ];
    }

    public function testTemplateInstanceChecksTheNameFirst(): void
    {
        $body = $this->method($this->source('libraries/kunena/src/Template/KunenaTemplate.php'), 'getInstance');

        $check = strpos($body, 'KunenaTemplateHelper::isValidName($name)');

        $this->assertNotFalse($check, 'getInstance() must validate the name.');

        foreach (['KunenaPath::clean($name)', 'is_file(KPATH_SITE', 'require_once $file', '"KunenaTemplate{$templatename}"'] as $use) {
            $at = strpos($body, $use);

            $this->assertNotFalse($at, $use);
            $this->assertLessThan($at, $check, "The name has to be checked before: $use");
        }

        $this->assertStringContainsString("'aurelia'", $body, 'There has to be a safe fallback.');
    }

    public function testChoosingATemplateNeedsATokenAndAnInstalledTemplate(): void
    {
        $body = $this->method($this->source('site/src/Controllers/MiscController.php'), 'template');

        $this->assertLessThan(strpos($body, 'getString('), strpos($body, "Session::checkToken('request')"), 'Token before the input is read.');
        $this->assertStringContainsString('KunenaTemplateHelper::isInstalled($name)', $body);
        $this->assertDoesNotMatchRegularExpression('/setcookie\([^;]*,\s*true\s*\)/', $body, 'Use the options array.');
        $this->assertStringContainsString("'httponly' => true", $body);
        $this->assertStringContainsString("'samesite' => 'Lax'", $body);
        $this->assertDoesNotMatchRegularExpression('/setcookie\(\'kunena_template\',\s*null/', $body, 'null is not a cookie value');
    }

    // --- the tasks ---

    public function testPendingUploadsCanNotBeDeletedByALink(): void
    {
        $source = $this->source('site/src/Controllers/TopicController.php');

        $this->assertMatchesRegularExpression('/protected function removeOrphanedAttachments\(\)/', $source);
        $this->assertDoesNotMatchRegularExpression('/public function removeOrphanedAttachments\(\)/', $source, 'A public method of a controller is a task.');

        // It is still used by the actions which check their token.
        $this->assertSame(2, substr_count($source, '$this->removeOrphanedAttachments();'));
    }

    public function testAdminToolsStartWithTheTokenCheck(): void
    {
        $source = $this->source('admin/src/Controller/ToolsController.php');

        foreach (['trashMenu', 'setupcolumnlanguage', 'setupsocials', 'modifysocials', 'deletesocials'] as $task) {
            $body = ltrim($this->method($source, $task));

            $this->assertStringStartsWith('if (!$this->hasValidToken()) {', $body, "$task must check the token before anything else");
            $this->assertMatchesRegularExpression('/^if \(!\$this->hasValidToken\(\)\) \{\s*return;\s*\}/', $body, "$task must stop on a wrong token");
        }

        $helper = $this->method($source, 'hasValidToken');

        $this->assertStringContainsString('Session::checkToken()', $helper);
        $this->assertStringContainsString('COM_KUNENA_ERROR_TOKEN', $helper);
        $this->assertMatchesRegularExpression('/protected function hasValidToken\(\)/', $source, 'The helper must not be a task itself.');
    }

    public function testRestoreOfTheSettingsOfATemplate(): void
    {
        $body = ltrim($this->method($this->source('admin/src/Controller/TemplateController.php'), 'restore'));

        $this->assertStringStartsWith('if (!Session::checkToken()) {', $body);
        $this->assertLessThan(strpos($body, 'File::delete('), strpos($body, 'KunenaTemplateHelper::isInstalled($template)'), 'The name is checked before a file is deleted.');
        $this->assertLessThan(strpos($body, 'File::delete('), strpos($body, 'Session::checkToken()'));
    }

    public function testNoOtherAdminToolChangesTheSiteWithoutAToken(): void
    {
        $source = $this->source('admin/src/Controller/ToolsController.php');

        preg_match_all('/public function (\w+)\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m, PREG_SET_ORDER);

        $untouched = [];

        foreach ($m as [, $name, $body]) {
            if (in_array($name, ['__construct', 'cancel', 'systemReport', 'diagnostics'], true)) {
                continue;
            }

            if (!str_contains($body, 'checkToken(') && !str_contains($body, 'hasValidToken(')) {
                $untouched[] = $name;
            }
        }

        $this->assertSame([], $untouched, 'Public tasks without a token check: ' . implode(', ', $untouched));
    }

    // --- the forms still send the token ---

    #[DataProvider('forms')]
    public function testTheFormOfTheTaskSendsTheToken(string $file): void
    {
        $this->assertMatchesRegularExpression('/form\.token|getFormToken/', $this->source($file), "$file has no token in its form");
    }

    public static function forms(): array
    {
        return [
            'menu (trash menu)'    => ['admin/tmpl/tools/menu.php'],
            'setup socials'        => ['admin/tmpl/tools/setupsocials.php'],
            'modify socials'       => ['admin/tmpl/tools/modifysocials.php'],
            'delete socials'       => ['admin/tmpl/tools/deletesocials.php'],
            'template (restore)'   => ['admin/tmpl/template/edit.php'],
        ];
    }
}
