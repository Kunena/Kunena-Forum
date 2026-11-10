<?php

/**
 * Kunena Component
 *
 * The list of the pictures of a gallery of avatars put the name of the request behind the path of the galleries as it came, for
 * everybody: "../../.." was the list of the files of any folder of the server. The views are not reachable at the moment (the raw
 * views are not found by Joomla), but the name is one folder now and the list is for members.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Security;

use Kunena\Forum\Libraries\User\KunenaAvatarGalleryName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

if (!\defined('_JEXEC')) {
    \define('_JEXEC', 1);
}

require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaAvatarGalleryName.php';

class AvatarGalleryNameTest extends TestCase
{
    private const VIEWS = __DIR__ . '/../../../src/site/src/View/User';

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
        $this->assertSame('animals', KunenaAvatarGalleryName::clean("animals\0"), 'A null byte at the end is removed, not kept.');
    }

    public static function noGalleries(): array
    {
        return [
            'up'                   => ['..'],
            'up the server'        => ['../../../..'],
            'up, windows'          => ['..\\..\\..'],
            'up after a folder'    => ['animals/../../..'],
            'a folder below'       => ['animals/cats'],
            'absolute'             => ['/etc'],
            'a drive'              => ['C:\\Windows'],
            'this folder'          => ['.'],
            'hidden'               => ['.git'],
            'two points inside'    => ['a..b'],
            'encoded'              => ['%2e%2e%2f'],
            'null byte'            => ["ani\0mals"],
            'null byte, a path'    => ["animals\0/../.."],
            'a line'               => ["ani\nmals"],
            'markup'               => ['<b>'],
            'empty'                => [''],
            'spaces'               => ['   '],
            'too long'             => [str_repeat('a', 101)],
            'null'                 => [null],
            'a list'               => [['animals']],
            'a number'             => [5],
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
        $source = file_get_contents(self::VIEWS . '/' . $file);

        $this->assertSame(1, preg_match('/public function displayGalleryimages\(\)\s*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));
        $body = $m[1];

        $this->assertStringContainsString("KunenaAvatarGalleryName::clean(\$this->app->getInput()->get('gallery_name'", $body);
        $this->assertStringContainsString('$this->me->exists() && $gallery_name !== null && is_dir($folder) ? Folder::files($folder) : []', $body);
        $this->assertSame(1, substr_count($body, 'Folder::files('), 'One place lists files.');
        $this->assertStringNotContainsString("gallery/' . \$gallery_name)", $body, 'The name of the request is not put into the path as it came.');
        $this->assertStringContainsString('use Kunena\Forum\Libraries\User\KunenaAvatarGalleryName;', $source);
    }
}
