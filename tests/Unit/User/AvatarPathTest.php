<?php

/**
 * Kunena Component
 *
 * Regression test: the value of the avatar of a user is a path which is used to read, resize and delete files. A member
 * could store users/../../../../configuration.php there (avatar_gallery was taken as it was), and the next removal of
 * the avatar, an upload, or a ban with "delete avatar" deleted that file. Only the two forms which Kunena makes are
 * accepted now, and only the own upload of the user is ever deleted.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Kunena\Forum\Libraries\User\KunenaAvatarPath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AvatarPathTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    private string $dir = '';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once self::SRC . '/libraries/kunena/src/User/KunenaAvatarPath.php';
    }

    protected function tearDown(): void
    {
        if ($this->dir === '') {
            return;
        }

        foreach (['users', 'gallery/set', 'gallery', ''] as $sub) {
            $folder = $this->dir . ($sub === '' ? '' : DIRECTORY_SEPARATOR . $sub);

            foreach (glob($folder . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
                if (is_file($file) || is_link($file)) {
                    @unlink($file);
                }
            }

            @rmdir($folder);
        }
    }

    #[DataProvider('valid')]
    public function testValidValues(string $avatar): void
    {
        $this->assertTrue(KunenaAvatarPath::isValid($avatar), $avatar);
    }

    public static function valid(): array
    {
        return [
            'upload'                  => ['users/avatar62.jpg'],
            'upload png'              => ['users/avatar1.png'],
            'upload upper case'       => ['users/avatar12.JPG'],
            'upload webp'             => ['users/avatar7.webp'],
            'gallery file'            => ['gallery/smile.png'],
            'gallery folder file'     => ['gallery/animals/cat.gif'],
            'gallery with spaces'     => ['gallery/my set/the cat (1).jpg'],
            'gallery with hyphens'    => ['gallery/set-2/a_b-c.jpeg'],
            'gallery unicode'         => ['gallery/dieren/kat-é.png'],
        ];
    }

    #[DataProvider('invalid')]
    public function testInvalidValues(mixed $avatar): void
    {
        $this->assertFalse(KunenaAvatarPath::isValid($avatar), var_export($avatar, true));
        $this->assertNull(KunenaAvatarPath::getPath(sys_get_temp_dir(), $avatar));
    }

    public static function invalid(): array
    {
        return [
            'the traversal which was found' => ['users/../../../../configuration.php'],
            'traversal after gallery'       => ['gallery/../../../configuration.php'],
            'traversal with image'          => ['gallery/../../../x.png'],
            'traversal in upload'           => ['users/../avatar1.jpg'],
            'upload traversal'              => ['users/avatar1/../../x.jpg'],
            'two dots in a name'            => ['gallery/a..b.png'],
            'absolute unix'                 => ['/etc/passwd'],
            'absolute windows'              => ['C:\\Windows\\win.ini'],
            'backslashes'                   => ['users\\avatar1.jpg'],
            'backslash traversal'           => ['gallery\\..\\x.png'],
            'other folder'                  => ['resized/size20/users/avatar1.jpg'],
            'configuration'                 => ['configuration.php'],
            'script in gallery'             => ['gallery/shell.php'],
            'script behind image name'      => ['gallery/shell.php.txt'],
            'svg'                           => ['gallery/x.svg'],
            'html'                          => ['users/avatar1.html'],
            'upload not a number'           => ['users/avatarabc.jpg'],
            'upload with name'              => ['users/other.jpg'],
            'deep gallery'                  => ['gallery/a/b/c.png'],
            'hidden file'                   => ['gallery/.htaccess.png'],
            'hidden folder'                 => ['gallery/.git/x.png'],
            'null byte'                     => ["users/avatar1.jpg\0.php"],
            'control character'             => ["gallery/a\n.png"],
            'trailing newline'              => ["users/avatar1.jpg\n"],
            'empty'                         => [''],
            'null'                          => [null],
            'array'                         => [['users/avatar1.jpg']],
            'integer'                       => [5],
            'too long'                      => ['gallery/' . str_repeat('a', 300) . '.png'],
        ];
    }

    public function testOnlyTheOwnUploadMayBeDeleted(): void
    {
        $this->assertTrue(KunenaAvatarPath::isUploadOf('users/avatar62.jpg', 62));

        // The picture of somebody else: the attack was to choose it as avatar and then remove it.
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/avatar5.jpg', 62));
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/avatar620.jpg', 62), 'a number which only starts with the id');
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/avatar6.jpg', 62));

        // The gallery is shared.
        $this->assertFalse(KunenaAvatarPath::isUploadOf('gallery/smile.png', 62));

        // Nothing for a guest or an unknown user.
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/avatar0.jpg', 0));
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/avatar62.jpg', 0));
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/avatar62.jpg', -62));

        // And never a path that leaves the folder.
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/../../../../configuration.php', 62));
    }

    public function testGalleryAndUploadAreDifferentKinds(): void
    {
        $this->assertTrue(KunenaAvatarPath::isGallery('gallery/cat.png'));
        $this->assertFalse(KunenaAvatarPath::isGallery('users/avatar1.png'), 'the choice from the gallery can not be the upload of somebody else');
        $this->assertTrue(KunenaAvatarPath::isUpload('users/avatar1.png'));
        $this->assertFalse(KunenaAvatarPath::isUpload('gallery/cat.png'));
    }

    private function folder(): string
    {
        $this->dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kunena-avatars-' . bin2hex(random_bytes(4));
        mkdir($this->dir . '/users', 0777, true);
        mkdir($this->dir . '/gallery/set', 0777, true);
        file_put_contents($this->dir . '/users/avatar62.jpg', 'x');
        file_put_contents($this->dir . '/gallery/set/cat.png', 'x');
        file_put_contents($this->dir . '/outside.png', 'x');

        return $this->dir;
    }

    public function testPathInsideTheFolder(): void
    {
        $base = $this->folder();

        $this->assertSame(realpath($base) . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar62.jpg', KunenaAvatarPath::getPath($base, 'users/avatar62.jpg'));
        $this->assertSame(realpath($base) . DIRECTORY_SEPARATOR . 'gallery' . DIRECTORY_SEPARATOR . 'set' . DIRECTORY_SEPARATOR . 'cat.png', KunenaAvatarPath::getPath($base, 'gallery/set/cat.png'));
    }

    public function testFileWhichDoesNotExistYetStillGetsASafePath(): void
    {
        $base = $this->folder();
        $path = KunenaAvatarPath::getPath($base, 'users/avatar99.png');

        $this->assertNotNull($path);
        $this->assertStringStartsWith(realpath($base) . DIRECTORY_SEPARATOR, $path);
        $this->assertFileDoesNotExist($path);
    }

    public function testTheFileOutsideTheFolderIsNeverReachedWithDots(): void
    {
        $base = $this->folder();

        $this->assertFileExists($base . '/outside.png');
        $this->assertNull(KunenaAvatarPath::getPath($base, 'users/../outside.png'));
        $this->assertNull(KunenaAvatarPath::getPath($base . '/users', 'gallery/../../outside.png'));
    }

    public function testALinkWhichLeavesTheFolderIsRefused(): void
    {
        $base = $this->folder();

        if (!@symlink($base . '/outside.png', $base . '/users/avatar7.png')) {
            $this->markTestSkipped('Symbolic links can not be created here.');
        }

        $this->assertNull(KunenaAvatarPath::getPath($base, 'users/avatar7.png'));
    }

    // The code which uses the value.

    private function source(string $file): string
    {
        return file_get_contents(self::SRC . '/' . $file);
    }

    private function method(string $source, string $name): string
    {
        $this->assertSame(1, preg_match('/function ' . preg_quote($name, '/') . '\([^)]*\)[^{]*\{(.*?)\n    \}\n/s', $source, $m), $name);

        return $m[1];
    }

    public function testTheGalleryChoiceIsValidated(): void
    {
        $body = $this->method($this->source('site/src/Controllers/UserController.php'), 'saveProfile');

        $this->assertStringContainsString('KunenaAvatarPath::isGallery($avatar_gallery)', $body);
        $this->assertStringContainsString('is_file($galleryFile)', $body);
        // The assignment sits inside the checks.
        $this->assertLessThan(strpos($body, '$user->avatar = $avatar_gallery;'), strpos($body, 'KunenaAvatarPath::isGallery('));
    }

    public function testDeleteOldAvatarsOnlyDeletesTheOwnUpload(): void
    {
        $body = $this->method($this->source('site/src/Controllers/UserController.php'), 'deleteOldAvatars');

        $this->assertStringContainsString('KunenaAvatarPath::isUploadOf($user->avatar, (int) $user->userid)', $body);
        $this->assertStringContainsString('KunenaAvatarPath::getPath(', $body);
        $this->assertDoesNotMatchRegularExpression('/File::delete\([^;\n]*->avatar/', $body, 'the value of the avatar must not be glued to a path');
    }

    public function testBanDeletesOnlyTheOwnUpload(): void
    {
        $source = $this->source('site/src/Controllers/UserController.php');

        $this->assertStringContainsString('KunenaAvatarPath::isUploadOf($user->avatar, (int) $user->userid)', $source);
        $this->assertDoesNotMatchRegularExpression("/is_file\\(JPATH_ROOT \\. '\\/media\\/kunena\\/avatars\\/' \\. \\\$user->avatar\\)/", $source);
    }

    public function testNoDeleteIsBuiltFromTheAvatarValue(): void
    {
        $problems = [];
        $files    = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($path, '/External/')) {
                continue;
            }

            foreach (file($path) as $number => $line) {
                if (preg_match('/(File::delete|unlink)\([^;]*->avatar\b/', $line)) {
                    $problems[] = substr($path, strpos($path, '/src/') + 1) . ':' . ($number + 1) . ' ' . trim($line);
                }
            }
        }

        $this->assertSame([], $problems, "A file is deleted with a path made from the avatar value:\n" . implode("\n", $problems));
    }

    public function testResizerIgnoresAnInvalidValue(): void
    {
        $body = $this->source('plugins/plg_kunena_kunena/src/Helper/KunenaAvatarKunena.php');

        $this->assertStringContainsString('!empty($avatar) && KunenaAvatarPath::isValid($avatar)', $body, 'The resizer writes files from this value.');
    }

    public function testDisplaysCheckTheValueBeforeUsingIt(): void
    {
        $this->assertStringContainsString('KunenaAvatarPath::isValid(', $this->source('site/src/Controller/User/Item/UserItemDisplay.php'));
        $this->assertStringContainsString('KunenaAvatarPath::isValid(', $this->source('site/src/Controller/Topic/Item/TopicItemDisplay.php'));
    }
}
