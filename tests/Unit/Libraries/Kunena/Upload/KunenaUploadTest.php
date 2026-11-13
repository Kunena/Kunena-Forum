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

namespace Kunena\Tests\Unit\Libraries\Kunena\Upload;

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Upload\KunenaUpload;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * SVG stays forbidden for every upload: the configuration cannot allow it through addExtensions(), only the avatar upload
 * allows it with allowSvgAvatar() (the file is cleaned before it is stored).
 *
 * The temporary name of an upload is an HMAC-SHA256 keyed with the site secret over the file name, user id, session token and
 * extension, each with its length in front (issue #7459).
 */
class KunenaUploadTest extends UnitTestCase
{
    private const string SECRET = 'site-secret';

    private const string TOKEN = '0123456789abcdef0123456789abcdef';

    /**
     * The application which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$application = $this->saved['application'];
            $this->saved          = [];
        }
    }

    private static function validExtensions(KunenaUpload $upload): array
    {
        return (new ReflectionProperty(KunenaUpload::class, 'validExtensions'))->getValue($upload);
    }

    /**
     * Replace the application with one which has the given secret, user id and session token.
     */
    private function application(string $secret, int $userId, string $token): void
    {
        // Factory is a class of Joomla.
        $this->requireJoomla();

        if (!$this->saved) {
            $this->saved = ['application' => Factory::$application];
        }

        Factory::$application = new class ($secret, $userId, $token) {
            public function __construct(private string $secret, private int $userId, private string $token)
            {
            }

            public function get($name, $default = null)
            {
                return $name === 'secret' ? $this->secret : $default;
            }

            public function getIdentity(): object
            {
                return (object) ['id' => $this->userId];
            }

            public function getSession(): object
            {
                return new class ($this->token) {
                    public function __construct(private string $token)
                    {
                    }

                    public function getToken(): string
                    {
                        return $this->token;
                    }
                };
            }
        };
    }

    private function protectedName(string $filename, string $secret = self::SECRET, int $userId = 42, string $token = self::TOKEN): string
    {
        $this->application($secret, $userId, $token);

        $upload = self::bare(KunenaUpload::class);
        $upload->addExtensions(['png']);

        return $upload->getProtectedFilename($filename);
    }

    public function testAddExtensionsNeverAllowsSvg(): void
    {
        $upload = self::bare(KunenaUpload::class);
        $upload->addExtensions(['png', 'svg', '.SVG']);

        $extensions = self::validExtensions($upload);

        $this->assertContains('.png', $extensions);
        $this->assertNotContains('.svg', array_map('strtolower', $extensions));
    }

    public function testAllowSvgAvatarAllowsSvg(): void
    {
        $upload = self::bare(KunenaUpload::class);
        $upload->addExtensions(['png', 'svg']);

        $this->assertSame($upload, $upload->allowSvgAvatar());

        $extensions = self::validExtensions($upload);

        $this->assertContains('.png', $extensions);
        $this->assertContains('.svg', $extensions);
    }

    public function testProtectedFilenameIsStable(): void
    {
        $this->assertSame($this->protectedName('file.png'), $this->protectedName('file.png'));
    }

    public function testProtectedFilenameChangesWithEachInput(): void
    {
        $name = $this->protectedName('file.png');

        $this->assertNotSame($name, $this->protectedName('other.png'));
        $this->assertNotSame($name, $this->protectedName('file.png', userId: 43));
        $this->assertNotSame($name, $this->protectedName('file.png', token: 'fedcba9876543210fedcba9876543210'));
        $this->assertNotSame($name, $this->protectedName('file.png', secret: 'other-secret'));
    }

    public function testProtectedFilenameIsLowercaseHexOf64Characters(): void
    {
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $this->protectedName('File.PNG'));
    }

    public function testProtectedFilenameIsHmacKeyedWithTheSecret(): void
    {
        $message = 'kunena-upload|4:file|2:42|32:' . self::TOKEN . '|3:png';

        $this->assertSame(hash_hmac('sha256', $message, self::SECRET), $this->protectedName('file.png'));
    }

    public function testProtectedFilenameIsNoLongerMd5(): void
    {
        $old = md5('file.' . self::SECRET . '42' . self::TOKEN . '.png');

        $this->assertNotSame($old, $this->protectedName('file.png'));
    }

    public function testProtectedFilenameEncodingIsUnambiguous(): void
    {
        // Name "a|42|b" with token "c", and name "a" with token "b|42|c": joined with a plain "|" both give "a|42|b|42|c".
        $this->assertSame(implode('|', ['a|42|b', 42, 'c']), implode('|', ['a', 42, 'b|42|c']));

        $this->assertNotSame(
            $this->protectedName('a|42|b.png', token: 'c'),
            $this->protectedName('a.png', token: 'b|42|c')
        );
    }
}
