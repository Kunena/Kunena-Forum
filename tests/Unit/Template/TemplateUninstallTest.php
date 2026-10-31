<?php

/**
 * Kunena Component
 *
 * Regression test for the uninstall of a template. The system and the default template were refused by comparing the name from
 * the request with in_array() / ==, but the folder is built from the same name: "../template/aurelia" (and "Aurelia" on a file
 * system which does not care about case) is another spelling of the system template, it passed both checks, and the folder of the
 * system template was deleted. And a folder which is a link was deleted through the link.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Template;

use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TemplateUninstallTest extends TestCase
{
    private const CONTROLLER = __DIR__ . '/../../../src/admin/src/Controller/TemplateController.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Template/KunenaTemplateHelper.php';
    }

    public static function names(): array
    {
        return [
            'path to the system template'    => ['../template/aurelia', 'invalid'],
            'path with a dot'                => ['./aurelia', 'invalid'],
            'dot dot'                        => ['..', 'invalid'],
            'dot'                            => ['.', 'invalid'],
            'deeper path'                    => ['../../media', 'invalid'],
            'backslashes'                    => ['..\\template\\aurelia', 'invalid'],
            'trailing slash'                 => ['aurelia/', 'invalid'],
            'trailing newline'               => ["aurelia\n", 'invalid'],
            'trailing dot'                   => ['aurelia.', 'invalid'],
            'trailing space'                 => ['aurelia ', 'invalid'],
            'null byte'                      => ["aurelia\0x", 'invalid'],
            'empty'                          => ['', 'invalid'],
            'an array'                       => [['aurelia'], 'invalid'],
            'null'                           => [null, 'invalid'],
            'system template'                => ['aurelia', 'locked'],
            'system template, other case'    => ['Aurelia', 'locked'],
            'system template, upper case'    => ['AURELIA', 'locked'],
            'default template'               => ['mytheme', 'default'],
            'default template, other case'   => ['MyTheme', 'default'],
            'another template'               => ['other', null],
            'name with hyphen and digits'    => ['my-theme_2', null],
        ];
    }

    #[DataProvider('names')]
    public function testUninstallBlock($name, ?string $expected): void
    {
        $this->assertSame($expected, KunenaTemplateHelper::uninstallBlock($name, ['aurelia'], 'mytheme'));
    }

    public function testNoDefaultSet(): void
    {
        $this->assertNull(KunenaTemplateHelper::uninstallBlock('other', ['aurelia'], null));
        $this->assertSame('locked', KunenaTemplateHelper::uninstallBlock('aurelia', ['aurelia'], null));
    }

    private function uninstall(): string
    {
        $source = file_get_contents(self::CONTROLLER);

        $this->assertSame(1, preg_match('/public function uninstall\([^)]*\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));

        return $m[1];
    }

    public function testControllerAsksThePolicyBeforeReadingAnything(): void
    {
        $body = $this->uninstall();

        $this->assertStringContainsString('KunenaTemplateHelper::uninstallBlock($id, $this->locked,', $body);
        $this->assertLessThan(strpos($body, 'parseXmlFile'), strpos($body, 'uninstallBlock'), 'The name is checked before it becomes a path.');
        $this->assertStringContainsString("\$block === 'locked'", $body);
        $this->assertStringContainsString("\$block === 'default'", $body);
        $this->assertDoesNotMatchRegularExpression('/in_array\(\$id,\s*\$this->locked\)/', $body, 'Case-sensitive comparison of a spelling.');
        $this->assertDoesNotMatchRegularExpression('/KunenaTemplateHelper::isDefault\(/', $body);
    }

    public function testFolderIsOnlyDeletedWhenItIsNotALink(): void
    {
        $body = $this->uninstall();

        $this->assertIsInt(strpos($body, 'is_link($tpl)'), 'A link has to be checked.');
        $this->assertLessThan(strpos($body, 'Folder::delete'), strpos($body, 'is_link($tpl)'), 'A link is refused before the delete.');
    }
}
