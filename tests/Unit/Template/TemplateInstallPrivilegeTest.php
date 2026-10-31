<?php

/**
 * Kunena Component
 *
 * Regression test for the privilege needed to change a template. A template is PHP which the site runs, but installing one
 * (an uploaded archive), removing it, restoring its settings and editing its files only needed the right to manage Kunena, which
 * is given to people who run the forum. It needs the right to install extensions (or to administer Kunena). The package was
 * also written into the folder of the templates under the name the client sent, with its safety checks off, and the system
 * template was protected by a comparison which is case sensitive on a file system which is not.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Template;

use Kunena\Forum\Libraries\Template\KunenaTemplateHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TemplateInstallPrivilegeTest extends TestCase
{
    private const ADMIN = __DIR__ . '/../../../src/admin/src/Controller';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Template/KunenaTemplateHelper.php';
    }

    private function user(array $rights): object
    {
        return new class ($rights) {
            public function __construct(private array $rights)
            {
            }

            public function authorise($action, $asset = null)
            {
                return \in_array($action . '@' . $asset, $this->rights, true);
            }
        };
    }

    public static function users(): array
    {
        return [
            'can install extensions'      => [['core.manage@com_installer'], true],
            'administers Kunena'          => [['core.admin@com_kunena'], true],
            'both'                        => [['core.manage@com_installer', 'core.admin@com_kunena'], true],
            'only manages Kunena'         => [['core.manage@com_kunena'], false],
            'manages another component'   => [['core.manage@com_content'], false],
            'admin of another component'  => [['core.admin@com_content'], false],
            'manage installer is wrong'   => [['core.admin@com_installer'], false],
            'nothing'                     => [[], false],
        ];
    }

    #[DataProvider('users')]
    public function testWhoMayChangeTemplates(array $rights, bool $expected): void
    {
        $this->assertSame($expected, KunenaTemplateHelper::mayChangeTemplates($this->user($rights)));
    }

    public function testNoUserIsNoAccess(): void
    {
        $this->assertFalse(KunenaTemplateHelper::mayChangeTemplates(null));
        $this->assertFalse(KunenaTemplateHelper::mayChangeTemplates(new \stdClass()));
        $this->assertFalse(KunenaTemplateHelper::mayChangeTemplates('admin'));
    }

    public function testSystemTemplateIsLockedWhateverTheCase(): void
    {
        foreach (['aurelia', 'Aurelia', 'AURELIA', 'aUrElIa'] as $name) {
            $this->assertTrue(KunenaTemplateHelper::isLocked($name, ['aurelia']), $name);
        }

        foreach (['aurelia2', 'aurelia ', ' aurelia', '../aurelia', 'mytheme', '', null, ['aurelia']] as $name) {
            $this->assertFalse(KunenaTemplateHelper::isLocked($name, ['aurelia']), json_encode($name));
        }
    }

    public static function packages(): array
    {
        return [
            'zip'                  => ['template.zip', 'zip'],
            'upper case'           => ['Template.ZIP', 'zip'],
            'tar'                  => ['t.tar', 'tar'],
            'tgz'                  => ['t.tgz', 'tgz'],
            'tar.gz'               => ['t.tar.gz', 'tar.gz'],
            'tar.bz2'              => ['t.tar.bz2', 'tar.bz2'],
            'padded'               => ['  t.zip ', 'zip'],
            'path in the name'     => ['../../../../index.php.zip', 'zip'],
            'windows path'         => ['..\\..\\x.zip', 'zip'],
            'null byte'            => ["t.php\0.zip", 'zip'],
        ];
    }

    #[DataProvider('packages')]
    public function testPackageNameIsMadeHere(string $client, string $ext): void
    {
        $name = KunenaTemplateHelper::packageName($client);

        $this->assertMatchesRegularExpression('/^kunena-template-[0-9a-f]{16}\.' . preg_quote($ext, '/') . '\z/', $name);
        $this->assertStringNotContainsString('/', $name);
        $this->assertStringNotContainsString('..', $name);
        $this->assertNotSame($name, KunenaTemplateHelper::packageName($client), 'The name is not guessable.');
    }

    public static function notPackages(): array
    {
        return [
            'php'              => ['shell.php'],
            'php in a zip name' => ['shell.zip.php'],
            'phtml'            => ['x.phtml'],
            'htaccess'         => ['.htaccess'],
            'no extension'     => ['template'],
            'empty'            => [''],
            'double ext'       => ['x.zip.exe'],
            'image'            => ['x.png'],
            'trailing dot'     => ['x.zip.'],
            'newline'          => ["x.zip\n.php"],
        ];
    }

    #[DataProvider('notPackages')]
    public function testOtherFilesAreNotPackages(string $client): void
    {
        $this->assertNull(KunenaTemplateHelper::packageName($client));
    }

    public function testNotAStringIsNotAPackage(): void
    {
        $this->assertNull(KunenaTemplateHelper::packageName(null));
        $this->assertNull(KunenaTemplateHelper::packageName(['x.zip']));
    }

    private function body(string $file, string $method): string
    {
        $source = file_get_contents(self::ADMIN . '/' . $file);

        $this->assertSame(1, preg_match('/function ' . preg_quote($method, '/') . '\([^)]*\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m), $method);

        return $m[1];
    }

    public static function tasks(): array
    {
        return [
            'install'          => ['TemplatesController.php', 'install'],
            'uninstall'        => ['TemplateController.php', 'uninstall'],
            'restore'          => ['TemplateController.php', 'restore'],
            'save scss'        => ['TemplateController.php', 'internalSaveScss'],
            'save css'         => ['TemplateController.php', 'internalSaveCss'],
        ];
    }

    #[DataProvider('tasks')]
    public function testTaskAsksForThePrivilegeBeforeDoingAnything(string $file, string $method): void
    {
        $body = $this->body($file, $method);

        $this->assertIsInt(strpos($body, 'KunenaTemplateHelper::mayChangeTemplates($this->app->getIdentity())'), $method);
        $this->assertStringContainsString('JERROR_ALERTNOAUTHOR', $body);

        $check = strpos($body, 'mayChangeTemplates');

        foreach (['File::write', 'File::delete', 'File::upload', 'Folder::delete', 'Archive', 'parseXmlFile', 'isInstalled('] as $action) {
            $at = strpos($body, $action);

            if ($at !== false) {
                $this->assertLessThan($at, $check, "$method: the privilege is checked before $action");
            }
        }
    }

    public function testInstallDoesNotUseTheNameOfTheClientAsAPath(): void
    {
        $body = $this->body('TemplatesController.php', 'install');

        $this->assertDoesNotMatchRegularExpression('/\$dest\s*\.\s*\$file\s*\[\s*\'name\'\s*\]/', $body, 'The folder of the templates is not where the package is kept.');
        $this->assertStringContainsString('KunenaTemplateHelper::packageName(', $body);
        $this->assertStringContainsString("KunenaPath::tmpdir() . '/' . \$package", $body);
        $this->assertStringContainsString('KunenaTemplateHelper::isLocked($template->directory, $this->locked)', $body);
        $this->assertDoesNotMatchRegularExpression('/in_array\(\$template->directory,\s*\$this->locked\)/', $body);
    }
}
