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

use Kunena\Forum\Libraries\User\KunenaAvatarPath;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The stored value of an avatar is a path which is used to delete files (Kunena 7.1): only the forms which Kunena makes are
 * accepted, and a delete removes the upload of the user himself, never a file outside the folder of the avatars.
 */
class KunenaAvatarPathTest extends UnitTestCase
{
    public static function valid(): array
    {
        return [
            'an upload'               => ['users/avatar42.jpg'],
            'an uploaded svg (8.0)'   => ['users/avatar42.svg'],
            'a gallery picture'       => ['gallery/cat.png'],
            'in a gallery folder'     => ['gallery/animals/cat 2.webp'],
        ];
    }

    #[DataProvider('valid')]
    public function testTheFormsOfKunenaAreValid(string $avatar): void
    {
        $this->assertTrue(KunenaAvatarPath::isValid($avatar));
    }

    public static function invalid(): array
    {
        return [
            'up out of the folder'     => ['users/../../configuration.php'],
            'up in the gallery'        => ['gallery/../users/avatar42.jpg'],
            'an absolute path'         => ['/etc/passwd'],
            'a backslash'              => ['users\\avatar42.jpg'],
            'another folder'           => ['resized/avatar42.jpg'],
            'a php file'               => ['users/avatar42.php'],
            'a control character'      => ["users/avatar42.jpg\0.php"],
            'empty'                    => [''],
            'not a string'             => [42],
        ];
    }

    #[DataProvider('invalid')]
    public function testAnythingElseIsRefused(mixed $avatar): void
    {
        $this->assertFalse(KunenaAvatarPath::isValid($avatar));
        $this->assertNull(KunenaAvatarPath::getPath(sys_get_temp_dir(), $avatar));
    }

    public function testOnlyTheOwnUploadIsDeleted(): void
    {
        $this->assertTrue(KunenaAvatarPath::isUploadOf('users/avatar42.png', 42));
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/avatar43.png', 42));
        $this->assertFalse(KunenaAvatarPath::isUploadOf('gallery/cat.png', 42));
        $this->assertFalse(KunenaAvatarPath::isUploadOf('users/avatar0.png', 0));
    }

    public function testThePathIsInsideTheFolder(): void
    {
        $base = realpath(sys_get_temp_dir());

        $this->assertSame($base . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar42.jpg', KunenaAvatarPath::getPath($base, 'users/avatar42.jpg'));
    }

    public function testTheDeletesOfTheControllerCheckTheValue(): void
    {
        $source = Files::read('src/site/src/Controllers/UserController.php');

        $this->assertSame(1, preg_match('/protected function deleteOldAvatars\(KunenaUser \$user\)\s*\{(.*?)\n    \}/s', $source, $m));
        $this->assertStringContainsString('KunenaAvatarPath::isUploadOf($user->avatar, (int) $user->userid)', $m[1]);
        $this->assertStringNotContainsString("preg_match('|^users/|'", $m[1]);

        // No file is deleted with the stored value glued to a folder.
        $this->assertDoesNotMatchRegularExpression('#File::delete\([^)]*\.\s*\$user->avatar\)#', $source);
    }
}
