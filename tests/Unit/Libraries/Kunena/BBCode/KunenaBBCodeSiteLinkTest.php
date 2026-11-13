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
 * Only a non-SEF address of the site (index.php?...) in the URL tag goes through the router: a SEF address (/index.php/forum/...)
 * is kept as written, so the router does not add the alias of the menu at its end (issue #6121).
 */
class KunenaBBCodeSiteLinkTest extends UnitTestCase
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

        // The file of KunenaBBCode loads the parser from the folder of the library.
        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCode::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    /**
     * The cases with the same result at the root of the domain and in a subfolder.
     */
    private static function sameForEveryBase(): array
    {
        $topic = 'index.php?option=com_kunena&view=topic&catid=5&id=11&Itemid=8940&limitstart=24#259';

        return [
            'a non-SEF address'                    => [$topic, $topic, true],
            'a non-SEF address in upper case'      => ['INDEX.PHP?option=com_kunena', 'INDEX.PHP?option=com_kunena', true],
            'a non-SEF address with a slash'       => ['/index.php?option=com_kunena&view=topic&id=11', 'index.php?option=com_kunena&view=topic&id=11', true],
            'a SEF address with a slash'           => ['/index.php/forum/welcome-mat/2-relative-links#2', '/index.php/forum/welcome-mat/2-relative-links#2', false],
            'a SEF address with a query'           => ['/index.php/forum/x?start=24#259', '/index.php/forum/x?start=24#259', false],
            'the home with a slash'                => ['/index.php', '/index.php', false],
            'the home with a slash and an anchor'  => ['/index.php#top', '/index.php#top', false],
            'a name which starts with index.php'   => ['index.phpfoo', 'index.phpfoo', false],
            'another extension'                    => ['/index.phtml', '/index.phtml', false],
            'another character instead of the dot' => ['indexXphp?a=1', 'indexXphp?a=1', false],
            'a root-relative address'              => ['/forum/test-category/11-link-test?start=24#259', '/forum/test-category/11-link-test?start=24#259', false],
            'a full address'                       => ['https://example.com/index.php?x=1', 'https://example.com/index.php?x=1', false],
            'index.php? after a newline'           => ["foo\nindex.php?x=1", "foo\nindex.php?x=1", false],
        ];
    }

    public static function links(): array
    {
        $cases = [];

        foreach (['' => 'at the root', '/foldername' => 'in a subfolder'] as $base => $where) {
            foreach (self::sameForEveryBase() as $name => [$url, $expected, $route]) {
                $cases[$name . ' ' . $where] = [$url, (string) $base, $expected, $route];
            }
        }

        return $cases + [
            'a SEF address without a slash at the root'           => ['index.php/forum/x#2', '', '/index.php/forum/x#2', false],
            'a SEF address without a slash in a subfolder'        => ['index.php/forum/x#2', '/foldername', '/foldername/index.php/forum/x#2', false],
            'the home without a slash at the root'                => ['index.php', '', '/index.php', false],
            'the home without a slash in a subfolder'             => ['index.php', '/foldername', '/foldername/index.php', false],
            'the home without a slash with an anchor at the root' => ['index.php#top', '', '/index.php#top', false],
        ];
    }

    #[DataProvider('links')]
    public function testSiteLink(string $url, string $base, string $expected, bool $route): void
    {
        $this->assertSame(['url' => $expected, 'route' => $route], KunenaBBCode::siteLink($url, $base));
    }

    public function testSefAddressGetsNothingAppended(): void
    {
        $url = '/index.php/forum/welcome-mat/2-relative-links#2';

        $this->assertSame(['url' => $url, 'route' => false], KunenaBBCode::siteLink($url, ''));
    }

    public function testNonSefTopicIsStillRouted(): void
    {
        $url = 'index.php?option=com_kunena&view=topic&catid=5&id=11&Itemid=8940&limitstart=24#259';

        $this->assertSame(['url' => $url, 'route' => true], KunenaBBCode::siteLink($url, '/foldername'));
    }
}
