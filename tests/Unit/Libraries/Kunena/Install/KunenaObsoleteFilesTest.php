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

namespace Kunena\Tests\Unit\Libraries\Kunena\Install;

use FilesystemIterator;
use Kunena\Forum\Libraries\Install\KunenaObsoleteFiles as O;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The tool "Prune old files" deletes the files which Kunena no longer ships: only the ones of the list, not one which the
 * forum uses, not one reached through a symbolic link (a site which runs a git clone), and never a folder of Kunena itself.
 */
class KunenaObsoleteFilesTest extends UnitTestCase
{
    private string $root = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir() . '/kunena-obsolete-' . bin2hex(random_bytes(4));

        foreach ([
            'media/kunena/kunena/core/images/spacer.gif',
            'media/kunena/kunena/core/images/icons/top_arrow.gif',
            'media/kunena/ranks/rank1.gif',
            'media/kunena/ranks/rank2.gif',
            'media/kunena/ranks/rank1.svg',
            'administrator/components/com_kunena/media/css/dashboard.css',
            'media/kunena/core/css/kunena.css',
        ] as $file) {
            @mkdir(\dirname($this->root . '/' . $file), 0777, true);
            file_put_contents($this->root . '/' . $file, 'x');
        }
    }

    protected function tearDown(): void
    {
        if (is_dir($this->root)) {
            $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($items as $item) {
                $item->isDir() && !$item->isLink() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
            }

            @rmdir($this->root);
        }

        parent::tearDown();
    }

    private const LIST = [
        'media/kunena/kunena/core/images/spacer.gif',
        'media/kunena/kunena/core/images/icons/top_arrow.gif',
        'media/kunena/ranks/rank1.gif',
        'media/kunena/ranks/rank2.gif',
        'administrator/components/com_kunena/media/css/dashboard.css',
        'media/kunena/core/images/not-on-this-site.gif',
    ];

    private static function rankOneInUse(): callable
    {
        return static fn (string $path) => $path === 'media/kunena/ranks/rank1.gif';
    }

    public function testFindListsTheFilesOfTheListWhichAreThere(): void
    {
        $found = O::find($this->root, self::LIST, self::rankOneInUse());

        $this->assertSame([
            'media/kunena/kunena/core/images/spacer.gif',
            'media/kunena/kunena/core/images/icons/top_arrow.gif',
            'media/kunena/ranks/rank1.gif',
            'media/kunena/ranks/rank2.gif',
            'administrator/components/com_kunena/media/css/dashboard.css',
        ], array_keys($found));
        $this->assertSame(O::IN_USE, $found['media/kunena/ranks/rank1.gif']['status']);
        $this->assertSame(O::OBSOLETE, $found['media/kunena/ranks/rank2.gif']['status']);
        $this->assertSame(1, $found['media/kunena/ranks/rank2.gif']['size']);
    }

    public function testDeleteRemovesTheObsoleteFilesAndTheFoldersTheyLeaveEmpty(): void
    {
        $result = O::delete($this->root, [
            'media/kunena/kunena/core/images/spacer.gif',
            'media/kunena/kunena/core/images/icons/top_arrow.gif',
            'administrator/components/com_kunena/media/css/dashboard.css',
        ], self::LIST);

        $this->assertCount(3, $result['deleted']);
        $this->assertSame([], $result['skipped']);
        $this->assertDirectoryDoesNotExist($this->root . '/media/kunena/kunena', 'The nested folder is left empty: it goes.');
        $this->assertDirectoryExists($this->root . '/media/kunena', 'A folder of Kunena itself stays, even when it would be empty.');
        $this->assertDirectoryDoesNotExist($this->root . '/administrator/components/com_kunena/media');
        $this->assertDirectoryExists($this->root . '/administrator/components/com_kunena');
        $this->assertFileExists($this->root . '/media/kunena/core/css/kunena.css', 'A file which is not on the list stays.');
    }

    public function testDeleteKeepsAFileInUseAndAFolderWithOtherFiles(): void
    {
        $result = O::delete($this->root, ['media/kunena/ranks/rank1.gif', 'media/kunena/ranks/rank2.gif'], self::LIST, self::rankOneInUse());

        $this->assertSame(['media/kunena/ranks/rank2.gif'], $result['deleted']);
        $this->assertSame(['media/kunena/ranks/rank1.gif'], $result['skipped']);
        $this->assertFileExists($this->root . '/media/kunena/ranks/rank1.gif');
        $this->assertFileExists($this->root . '/media/kunena/ranks/rank1.svg');
    }

    public static function refused(): array
    {
        return [
            'not on the list'       => ['media/kunena/core/css/kunena.css'],
            'a step up'             => ['media/kunena/ranks/../core/css/kunena.css'],
            'outside of kunena'     => ['configuration.php'],
            'a folder of kunena'    => ['media/kunena/'],
            'a backslash'           => ['media\\kunena\\core\\css\\kunena.css'],
        ];
    }

    #[DataProvider('refused')]
    public function testDeleteRefusesAnyOtherPath(string $path): void
    {
        $list   = array_merge(self::LIST, [$path]);
        $result = O::delete($this->root, [$path], $path === 'media/kunena/core/css/kunena.css' ? self::LIST : $list);

        $this->assertSame([], $result['deleted']);
        $this->assertFileExists($this->root . '/media/kunena/core/css/kunena.css');
    }

    public function testAFileReachedThroughALinkIsNotDeleted(): void
    {
        $target = $this->root . '/clone';
        mkdir($target . '/core/images', 0777, true);
        file_put_contents($target . '/core/images/old.gif', 'x');

        rename($this->root . '/media/kunena', $this->root . '/media/kunena-real');

        if (!@symlink($target, $this->root . '/media/kunena')) {
            rename($this->root . '/media/kunena-real', $this->root . '/media/kunena');
            $this->markTestSkipped('This system does not make symbolic links.');
        }

        $found  = O::find($this->root, ['media/kunena/core/images/old.gif']);
        $result = O::delete($this->root, ['media/kunena/core/images/old.gif'], ['media/kunena/core/images/old.gif']);

        $this->assertSame(O::LINKED, $found['media/kunena/core/images/old.gif']['status']);
        $this->assertSame([], $result['deleted']);
        $this->assertFileExists($target . '/core/images/old.gif', 'The file of the clone stays.');

        unlink($this->root . '/media/kunena');
    }

    public function testTheListHasNoFileWhichKunenaShipsAndNoFileOfTheKindsWhichStay(): void
    {
        $list = require Files::path('src/admin/install/obsolete-files.php');

        $this->assertNotEmpty($list);

        $installed = [
            'src/admin/'            => 'administrator/components/com_kunena/',
            'src/site/'             => 'components/com_kunena/',
            'src/media/kunena/'     => 'media/kunena/',
            'src/libraries/kunena/' => 'libraries/kunena/',
        ];
        $shipped = [];

        foreach ($installed as $source => $target) {
            $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Files::path($source), FilesystemIterator::SKIP_DOTS));

            foreach ($items as $item) {
                $relative = substr(str_replace('\\', '/', $item->getPathname()), \strlen(str_replace('\\', '/', Files::path($source))));
                $shipped[strtolower($target . ltrim($relative, '/'))] = true;
            }
        }

        foreach ($list as $path) {
            $this->assertTrue(O::isSafePath($path), "$path is a file inside a folder of Kunena.");
            $this->assertArrayNotHasKey(strtolower($path), $shipped, "$path is shipped.");
            $this->assertDoesNotMatchRegularExpression('#/language/|/sql/|^media/kunena/(emoticons|avatars|topic_?icons)/#i', $path);
        }
    }

    public static function generatorPaths(): array
    {
        return [
            'media'                   => ['src/media/kunena/ranks/rank1.gif', 'media/kunena/ranks/rank1.gif'],
            'admin'                   => ['src/admin/media/css/dashboard.css', 'administrator/components/com_kunena/media/css/dashboard.css'],
            'library'                 => ['src/libraries/kunena/src/Old.php', 'libraries/kunena/src/Old.php'],
            'shipped template'        => ['src/site/template/aurelia/old.php', 'components/com_kunena/template/aurelia/old.php'],
            'template no more there'  => ['src/site/template/crypsis/old.php', null],
            'language'                => ['src/admin/language/en-GB/com_kunena.old.ini', null],
            'sql'                     => ['src/admin/sql/updates/mysql/6.0.0.sql', null],
            'emoticon'                => ['src/media/kunena/emoticons/old.gif', null],
            'not installed'           => ['tests/Unit/OldTest.php', null],
        ];
    }

    #[DataProvider('generatorPaths')]
    public function testTheGeneratorMapsAPathOfTheCloneToTheSite(string $path, ?string $expected): void
    {
        require_once Files::path('scripts/obsoletefiles.php');

        $this->assertSame($expected, kunenaObsoletePath($path, ['agora', 'aurelia', 'system']));
    }
}
