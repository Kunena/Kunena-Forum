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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use FilesystemIterator;
use Kunena\Forum\Libraries\User\KunenaAvatarGalleryName;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The list of the pictures of a gallery of avatars put the name of the request behind the path of the galleries as it came, for
 * everybody: "../../.." was the list of the files of any folder of the server.
 */
class KunenaAvatarGalleryNameTest extends UnitTestCase
{
    /**
     * A temporary folder of galleries, removed after the test.
     */
    private string $galleries = '';

    public static function galleries(): array
    {
        return [['animals'], ['Sci-Fi'], ['my_pictures'], ['Level 1'], ['v1.2'], ['Tiere-Übersicht'], ['2024']];
    }

    #[DataProvider('galleries')]
    public function testNameOfAFolderIsKept(string $name): void
    {
        $this->assertSame($name, KunenaAvatarGalleryName::clean($name));
    }

    public function testSpacesAroundTheNameAreRemoved(): void
    {
        $this->assertSame('animals', KunenaAvatarGalleryName::clean('  animals '));
        $this->assertSame('animals', KunenaAvatarGalleryName::clean("animals\n"));
    }

    public static function noGalleries(): array
    {
        return [
            'up'                => ['..'],
            'up the server'     => ['../../../..'],
            'up, windows'       => ['..\\..\\..'],
            'up after a folder' => ['animals/../../..'],
            'a folder below'    => ['animals/cats'],
            'absolute'          => ['/etc'],
            'a drive'           => ['C:\\Windows'],
            'this folder'       => ['.'],
            'hidden'            => ['.git'],
            'two points inside' => ['a..b'],
            'encoded'           => ['%2e%2e%2f'],
            'null byte'         => ["ani\0mals"],
            'null byte, a path' => ["animals\0/../.."],
            'a line'            => ["ani\nmals"],
            'markup'            => ['<b>'],
            'empty'             => [''],
            'spaces'            => ['   '],
            'too long'          => [str_repeat('a', 101)],
            'null'              => [null],
            'a list'            => [['animals']],
            'a number'          => [5],
        ];
    }

    #[DataProvider('noGalleries')]
    public function testAnythingElseIsNoGallery(mixed $name): void
    {
        $this->assertNull(KunenaAvatarGalleryName::clean($name));
    }

    public static function pictures(): array
    {
        return [['a.png'], ['b.PNG'], ['c.gif'], ['d.jpg'], ['e.JPEG'], ['f.webp'], ['G.WebP'], ['my cat-1_v2.jpg'], ['Übersicht.png']];
    }

    #[DataProvider('pictures')]
    public function testPictureIsAPicture(string $name): void
    {
        $this->assertTrue(KunenaAvatarGalleryName::isPicture($name));
    }

    public static function noPictures(): array
    {
        return [
            'index'          => ['index.html'],
            'hidden'         => ['.hidden.png'],
            'a folder'       => ['a/b.png'],
            'windows folder' => ['a\\b.png'],
            'two points'     => ['..png'],
            'svg'            => ['x.svg'],
            'double'         => ['x.php.txt'],
            'no name'        => ['.png'],
            'space around'   => [' x.png'],
            'null byte'      => ["x.php\0.png"],
            'null'           => [null],
        ];
    }

    #[DataProvider('noPictures')]
    public function testAnythingElseIsNoPicture(mixed $name): void
    {
        $this->assertFalse(KunenaAvatarGalleryName::isPicture($name));
    }

    public static function avatars(): array
    {
        return [['gallery/a.png'], ['gallery/animals/cat.JPG'], ['gallery/Tiere-Übersicht/katze.webp'], ['gallery/Level 1/x.gif']];
    }

    #[DataProvider('avatars')]
    public function testAvatarOfAGalleryIsAccepted(string $value): void
    {
        $this->assertTrue(KunenaAvatarGalleryName::isAvatar($value));
    }

    public static function noAvatars(): array
    {
        return [
            'empty folder'  => ['gallery//x.png'],
            'two folders'   => ['gallery/a/b/x.png'],
            'up'            => ['gallery/../x.png'],
            'other folder'  => ['users/x.png'],
            'hidden folder' => ['gallery/.git/x.png'],
            'no picture'    => ['gallery/a/index.html'],
            'only a folder' => ['gallery/a'],
            'no gallery'    => ['x.png'],
            'leading slash' => ['/gallery/x.png'],
            'empty'         => [''],
            'null'          => [null],
        ];
    }

    #[DataProvider('noAvatars')]
    public function testAnythingElseIsNoAvatar(mixed $value): void
    {
        $this->assertFalse(KunenaAvatarGalleryName::isAvatar($value));
    }

    private function makeGalleries(): string
    {
        $this->galleries = str_replace('\\', '/', sys_get_temp_dir()) . '/kunena-phpunit-' . bin2hex(random_bytes(4));

        mkdir($this->galleries . '/animals/deeper', 0755, true);

        foreach (['b.png', 'a.JPG', 'index.html', '.hidden.png', 'animals/cat.png', 'animals/index.html', 'animals/deeper/x.png'] as $file) {
            file_put_contents($this->galleries . '/' . $file, 'x');
        }

        // A folder whose name looks like a picture is no picture.
        mkdir($this->galleries . '/folder.png');

        return $this->galleries;
    }

    protected function tearDown(): void
    {
        if ($this->galleries !== '') {
            $items = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->galleries, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($items as $item) {
                $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
            }

            rmdir($this->galleries);
        }

        parent::tearDown();
    }

    public function testRootListsOnlyItsPicturesSorted(): void
    {
        $this->assertSame(['a.JPG', 'b.png'], KunenaAvatarGalleryName::pictures($this->makeGalleries(), ''));
    }

    public function testFolderListsItsPicturesWithTheFolder(): void
    {
        $this->assertSame(['animals/cat.png'], KunenaAvatarGalleryName::pictures($this->makeGalleries(), 'animals'));
    }

    public function testRefusedOrMissingGalleryListsNothing(): void
    {
        $path = $this->makeGalleries();

        $this->assertSame([], KunenaAvatarGalleryName::pictures($path, '..'));
        $this->assertSame([], KunenaAvatarGalleryName::pictures($path, 'animals/deeper'));
        $this->assertSame([], KunenaAvatarGalleryName::pictures($path, 'missing'));
    }

    public function testTaskListsOneGalleryForMembers(): void
    {
        $source = Files::read('src/site/src/Controllers/UserController.php');

        $this->assertSame(1, preg_match('/public function galleryimages\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $this->assertStringContainsString("if (!Session::checkToken('get')) {\n            throw new RuntimeException(Text::_('Forbidden'), 403);", $m[1]);
        $this->assertStringContainsString('if ($this->me->exists()) {', $m[1]);
        $this->assertStringContainsString("KunenaAvatarGalleryName::pictures(JPATH_ROOT . '/media/kunena/avatars/gallery', \$name)", $m[1]);
        $this->assertStringContainsString('use Kunena\Forum\Libraries\User\KunenaAvatarGalleryName;', $source);
    }
}
