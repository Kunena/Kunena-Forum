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

namespace Kunena\Tests\Unit\Libraries\Kunena\Template;

use FilesystemIterator;
use Kunena\Forum\Libraries\Template\KunenaTemplateChecksums;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The list of checksums of a template (KunenaTemplateChecksums): the list written by the build, and the modified and missing
 * files of a template compared with it. The templates are in a temporary folder of the test, removed again.
 */
class KunenaTemplateChecksumsTest extends UnitTestCase
{
    private string $templates = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->templates = str_replace('\\', '/', sys_get_temp_dir()) . '/kunena-phpunit-' . bin2hex(random_bytes(4)) . '/templates';
        $this->put('demo/template.php', '<?php echo "demo";');
        $this->put('demo/layouts/topic/default.php', 'topic');
        $this->put('demo/config/template.xml', '<extension/>');
        $this->put('demo/config/params.ini', 'setting=1');
    }

    protected function tearDown(): void
    {
        if ($this->templates !== '') {
            $root  = \dirname($this->templates);
            $items = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($items as $item) {
                $item->isDir() && !$item->isLink() ? rmdir($item->getPathname()) : unlink($item->getPathname());
            }

            rmdir($root);
        }

        parent::tearDown();
    }

    private function put(string $path, string $content): void
    {
        $file = $this->templates . '/' . $path;

        if (!is_dir(\dirname($file))) {
            mkdir(\dirname($file), 0755, true);
        }

        file_put_contents($file, $content);
    }

    private function writeList(array $checksums): void
    {
        file_put_contents($this->templates . '/demo/checksums.json', json_encode((object) $checksums));
    }

    public function testTheListHasTheFilesByTheirRelativePathWithoutTheSettingsAndItself(): void
    {
        file_put_contents($this->templates . '/demo/checksums.json', '{"old": "list"}');

        $written = KunenaTemplateChecksums::write($this->templates . '/demo');
        $list    = json_decode(file_get_contents($this->templates . '/demo/checksums.json'), true);

        $this->assertSame($written, $list);
        $this->assertSame(['config/template.xml', 'layouts/topic/default.php', 'template.php'], array_keys($list));
        $this->assertSame(hash('sha256', 'topic'), $list['layouts/topic/default.php']);
    }

    public function testAnUnchangedTemplateHasNoModifiedOrMissingFile(): void
    {
        KunenaTemplateChecksums::write($this->templates . '/demo');

        $this->assertSame(['modified' => [], 'missing' => []], KunenaTemplateChecksums::compare($this->templates, 'demo'));
    }

    public function testTheSettingsOfTheSiteAreNotAModification(): void
    {
        KunenaTemplateChecksums::write($this->templates . '/demo');
        $this->put('demo/config/params.ini', 'setting=2');

        $this->assertSame(['modified' => [], 'missing' => []], KunenaTemplateChecksums::compare($this->templates, 'demo'));
    }

    public function testAChangedFileIsModified(): void
    {
        KunenaTemplateChecksums::write($this->templates . '/demo');
        $this->put('demo/layouts/topic/default.php', 'changed topic');

        $this->assertSame(
            ['modified' => ['layouts/topic/default.php'], 'missing' => []],
            KunenaTemplateChecksums::compare($this->templates, 'demo')
        );
    }

    public function testADeletedFileIsMissing(): void
    {
        KunenaTemplateChecksums::write($this->templates . '/demo');
        unlink($this->templates . '/demo/template.php');

        $this->assertSame(['modified' => [], 'missing' => ['template.php']], KunenaTemplateChecksums::compare($this->templates, 'demo'));
    }

    public function testATemplateWithoutAListHasNoResult(): void
    {
        $this->assertNull(KunenaTemplateChecksums::compare($this->templates, 'demo'));
        $this->assertNull(KunenaTemplateChecksums::compare($this->templates, 'absent'));
    }

    public static function invalidLists(): array
    {
        return [
            'not JSON'             => ['{not json'],
            'a JSON list'          => ['["template.php"]'],
            'a JSON string'        => ['"template.php"'],
            'a value not a string' => ['{"template.php": 1}'],
        ];
    }

    #[DataProvider('invalidLists')]
    public function testAnInvalidListHasNoResult(string $content): void
    {
        file_put_contents($this->templates . '/demo/checksums.json', $content);

        $this->assertNull(KunenaTemplateChecksums::compare($this->templates, 'demo'));
    }

    public static function unsafeNames(): array
    {
        return [
            'the parent folder' => ['..'],
            'a sub folder'      => ['demo/layouts'],
            'a slash'           => ['a/b'],
            'a dot'             => ['a.b'],
            'empty'             => [''],
        ];
    }

    #[DataProvider('unsafeNames')]
    public function testAnUnsafeTemplateNameHasNoResult(string $name): void
    {
        KunenaTemplateChecksums::write($this->templates . '/demo');
        mkdir($this->templates . '/a.b');
        file_put_contents($this->templates . '/a.b/checksums.json', '{}');

        $this->assertNull(KunenaTemplateChecksums::compare($this->templates, $name));
    }

    public function testUnsafePathsOfTheListAreNeitherReadNorReported(): void
    {
        // Files which exist, with a wrong checksum: reading them would report them as modified.
        file_put_contents(\dirname($this->templates) . '/outside.txt', 'outside');
        $this->put('demo/a\\b', 'backslash');
        $this->put('demo/x', 'x');

        $this->writeList([
            '../outside.txt'          => str_repeat('0', 64),
            '../../outside.txt'       => str_repeat('0', 64),
            '/etc/passwd'             => str_repeat('0', 64),
            'C:/x'                    => str_repeat('0', 64),
            'C:\\Windows\\win.ini'    => str_repeat('0', 64),
            'a\\b'                    => str_repeat('0', 64),
            './template.php'          => str_repeat('0', 64),
            'layouts/../template.php' => str_repeat('0', 64),
            "template.php\0.txt"      => str_repeat('0', 64),
            ''                        => str_repeat('0', 64),
            'template.php'            => hash('sha256', '<?php echo "demo";'),
        ]);

        $this->assertSame(['modified' => [], 'missing' => []], KunenaTemplateChecksums::compare($this->templates, 'demo'));
    }

    public function testAFileOutsideTheTemplateThroughALinkIsSkipped(): void
    {
        file_put_contents(\dirname($this->templates) . '/outside.txt', 'outside');

        if (!@symlink(\dirname($this->templates) . '/outside.txt', $this->templates . '/demo/link.txt')) {
            $this->markTestSkipped('This system cannot create a symbolic link.');
        }

        $this->writeList(['link.txt' => str_repeat('0', 64)]);

        $this->assertSame(['modified' => [], 'missing' => []], KunenaTemplateChecksums::compare($this->templates, 'demo'));
    }

    /**
     * The list of a folder of Kunena (the component, the library): only the folders and files which the manifest installs,
     * the list in a subfolder, the settings of the templates in it left out.
     */
    public function testTheListOfAFolderOfKunena(): void
    {
        $this->put('component/src/Controller.php', 'controller');
        $this->put('component/install/sql.txt', 'sql');
        $this->put('component/template/demo/config/params.ini', 'setting=1');
        $this->put('component/template/demo/layout.php', 'layout');
        $this->put('component/not-installed.txt', 'not in the manifest');

        $folder  = $this->templates . '/component';
        $written = KunenaTemplateChecksums::writeList($folder, 'install/checksums.json', ['src', 'install', 'template']);

        $this->assertSame(['install/sql.txt', 'src/Controller.php', 'template/demo/layout.php'], array_keys($written));
        $this->assertFileExists($folder . '/install/checksums.json');
        $this->assertSame(['modified' => [], 'missing' => []], KunenaTemplateChecksums::compareList($folder, 'install/checksums.json'));

        // A file changed by hand, a file deleted.
        file_put_contents($folder . '/src/Controller.php', 'hacked');
        unlink($folder . '/install/sql.txt');

        $this->assertSame(['modified' => ['src/Controller.php'], 'missing' => ['install/sql.txt']], KunenaTemplateChecksums::compareList($folder, 'install/checksums.json'));
    }

    /**
     * The list of SHA-512 next to the one of SHA-256: its own name, hashes of 128 characters, each list leaves the other out,
     * and a changed file is found by both.
     */
    public function testTheListOfSha512(): void
    {
        $this->assertSame('install/checksums.json', KunenaTemplateChecksums::listName('install/checksums.json', 'sha256'));
        $this->assertSame('install/checksums.sha512.json', KunenaTemplateChecksums::listName('install/checksums.json', 'sha512'));

        $this->put('component/src/Controller.php', 'controller');
        $folder = $this->templates . '/component';

        $sha256 = KunenaTemplateChecksums::writeList($folder, 'src/checksums.json', [], 'sha256');
        $sha512 = KunenaTemplateChecksums::writeList($folder, 'src/checksums.json', [], 'sha512');

        $this->assertSame(['src/Controller.php'], array_keys($sha256));
        $this->assertSame(['src/Controller.php'], array_keys($sha512), 'The list of SHA-256 is no file of the list of SHA-512.');
        $this->assertSame(hash('sha512', 'controller'), $sha512['src/Controller.php']);
        $this->assertSame(128, \strlen($sha512['src/Controller.php']));
        $this->assertFileExists($folder . '/src/checksums.sha512.json');

        file_put_contents($folder . '/src/Controller.php', 'hacked');

        foreach (KunenaTemplateChecksums::ALGORITHMS as $algorithm) {
            $this->assertSame(['src/Controller.php'], KunenaTemplateChecksums::compareList($folder, 'src/checksums.json', $algorithm)['modified'], $algorithm);
        }

        $this->assertNull(KunenaTemplateChecksums::compareList($folder, 'src/checksums.json', 'md5'));
    }

    /**
     * A copy of the code which was not built (a development site) has no list: no result, not "everything is missing".
     */
    public function testAFolderWithoutItsListHasNoResult(): void
    {
        $this->put('component/src/Controller.php', 'controller');

        $this->assertNull(KunenaTemplateChecksums::compareList($this->templates . '/component', 'install/checksums.json'));
    }
}
