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
 * An absolute address of the site itself is valid for [img] and [url], also on a host without a domain (localhost, a name of an
 * intranet), which the pattern of IsValidURL() refuses: the images of the forum stayed raw BBCode on such a site.
 */
class KunenaBBCodeSiteUrlTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

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

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCode::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    public static function addresses(): array
    {
        $root = 'http://localhost/tests/';

        return [
            'an image of the site on localhost'      => ['http://localhost/tests/media/kunena/ranks/rank1.gif', $root, true],
            'the root without a trailing slash'      => ['http://localhost/tests/media/x.gif', 'http://localhost/tests', true],
            'the scheme and host in upper case'      => ['HTTP://LOCALHOST/tests/media/x.gif', $root, true],
            'a site at the root of an intranet host' => ['http://intranet/images/logo.png', 'http://intranet/', true],
            'a site with a port'                     => ['http://localhost:8080/forum/x.png', 'http://localhost:8080/forum/', true],
            'another folder which starts the same'   => ['http://localhost/testsx/media/x.gif', $root, false],
            'another host which starts the same'     => ['http://localhost.example.com/tests/x.gif', $root, false],
            'another port'                           => ['http://localhost:8080/tests/x.gif', $root, false],
            'the root inside another address'        => ['http://example.com/http://localhost/tests/x.gif', $root, false],
            'another scheme'                         => ['javascript:alert(1)//http://localhost/tests/', $root, false],
            'an address with a space'                => ['http://localhost/tests/a b.gif', $root, false],
            'no root (a script outside of a site)'   => ['http://localhost/tests/x.gif', '', false],
            'a root without a scheme'                => ['localhost/tests/x.gif', 'localhost/tests/', false],
        ];
    }

    #[DataProvider('addresses')]
    public function testIsSiteUrl(string $url, string $root, bool $expected): void
    {
        $this->assertSame($expected, KunenaBBCode::isSiteUrl($url, $root));
    }
}
