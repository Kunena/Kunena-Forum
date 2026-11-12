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
}
