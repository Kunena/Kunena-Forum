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

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The emoticons stored as an image path in messages are rebuilt with the address of the current site, so they keep working
 * after the site moved to or from a subfolder.
 */
class KunenaBBCodeEmoticonUrlTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    private string $root = '';

    protected function setUp(): void
    {
        // The parser is in the folder External of the library, and the library of the tags is in the file of KunenaBBCode.
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        // The file of KunenaBBCode loads the parser from the folder of the library.
        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCode::class, false)) {
            require_once Files::path(self::BBCODE);
        }

        // A site with one emoticon, and a picture of the same name just outside the folder of the emoticons.
        $this->root = str_replace('\\', '/', sys_get_temp_dir()) . '/kunena-phpunit-' . bin2hex(random_bytes(4));
        mkdir($this->root . '/media/kunena/emoticons', 0755, true);
        file_put_contents($this->root . '/media/kunena/emoticons/1.png', 'x');
        file_put_contents($this->root . '/media/kunena/1.png', 'x');
    }

    protected function tearDown(): void
    {
        if ($this->root !== '') {
            unlink($this->root . '/media/kunena/emoticons/1.png');
            unlink($this->root . '/media/kunena/1.png');
            rmdir($this->root . '/media/kunena/emoticons');
            rmdir($this->root . '/media/kunena');
            rmdir($this->root . '/media');
            rmdir($this->root);
        }
    }

    public static function emoticons(): array
    {
        return [
            'the site moved into a subfolder'   => ['/media/kunena/emoticons/1.png', '/newfolder', '/newfolder/media/kunena/emoticons/1.png'],
            'the site moved out of a subfolder' => ['/oldfolder/media/kunena/emoticons/1.png', '', '/media/kunena/emoticons/1.png'],
            'the site moved to another domain'  => [
                'https://example.com/oldfolder/media/kunena/emoticons/1.png',
                'https://site.test/forum',
                'https://site.test/forum/media/kunena/emoticons/1.png',
            ],
            'a folder in upper case'  => ['/OLD/Media/Kunena/Emoticons/1.png', '', '/media/kunena/emoticons/1.png'],
            'the address of the site' => ['/media/kunena/emoticons/1.png', '', '/media/kunena/emoticons/1.png'],
        ];
    }

    #[DataProvider('emoticons')]
    public function testEmoticonIsRebuilt(string $url, string $smileyUrl, string $expected): void
    {
        $this->assertSame($expected, KunenaBBCode::emoticonUrl($url, $smileyUrl, $this->root));
    }

    public static function otherAddresses(): array
    {
        return [
            'a file which does not exist' => ['/media/kunena/emoticons/missing.png'],
            'an extra subfolder'          => ['/media/kunena/emoticons/sub/1.png'],
            'a query string'              => ['/media/kunena/emoticons/1.png?x=1'],
            'a fragment'                  => ['/media/kunena/emoticons/1.png#a'],
            'a backslash out the folder'  => ['/media/kunena/emoticons/..\\1.png'],
            'the parent folder'           => ['/media/kunena/emoticons/..'],
            'the folder itself'           => ['/media/kunena/emoticons/.'],
            'an unrelated address'        => ['https://example.com/images/1.png'],
        ];
    }

    #[DataProvider('otherAddresses')]
    public function testOtherAddressIsKept(string $url): void
    {
        $this->assertNull(KunenaBBCode::emoticonUrl($url, '/newfolder', $this->root));
    }
}
