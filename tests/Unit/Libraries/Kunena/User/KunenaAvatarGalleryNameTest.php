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

use Kunena\Forum\Libraries\User\KunenaAvatarGalleryName;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The list of the pictures of a gallery of avatars put the name of the request behind the path of the galleries as it came, for
 * everybody: "../../.." was the list of the files of any folder of the server.
 */
class KunenaAvatarGalleryNameTest extends UnitTestCase
{
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

    public static function views(): array
    {
        return [['HtmlViewRaw.php'], ['view.raw.php']];
    }

    #[DataProvider('views')]
    public function testViewListsOneFolderOfTheGalleriesForMembers(string $file): void
    {
        $source = Files::read('src/site/src/View/User/' . $file);

        $this->assertSame(1, preg_match('/public function displayGalleryimages\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $this->assertStringContainsString("KunenaAvatarGalleryName::clean(\$this->app->getInput()->get('gallery_name'", $m[1]);
        $this->assertStringContainsString('$this->me->exists() && $gallery_name !== null && is_dir($folder) ? Folder::files($folder) : []', $m[1]);
        $this->assertSame(1, substr_count($m[1], 'Folder::files('), 'One place lists files.');
        $this->assertStringContainsString('use Kunena\Forum\Libraries\User\KunenaAvatarGalleryName;', $source);
    }
}
