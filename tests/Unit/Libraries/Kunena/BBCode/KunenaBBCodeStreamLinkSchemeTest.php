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

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;

/**
 * In an activity stream (forceMinimal: the stream of JomSocial) [soundcloud] and [instagram] are a link only to an address of the web.
 * The address was put into the link as it was typed: [soundcloud]javascript:alert(document.cookie)[/soundcloud] (and [instagram] with
 * an address which parse_url() does not read) ran its script for every reader of the stream.
 */
class KunenaBBCodeStreamLinkSchemeTest extends UnitTestCase
{
    protected function setUp(): void
    {
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

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path('src/libraries/kunena/src/BBCode/KunenaBBCode.php');
        }
    }

    private static function stream(): object
    {
        return (object) ['autoLink_disable' => 0, 'parent' => (object) ['forceMinimal' => true], 'context' => ''];
    }

    public function testAScriptAddressIsNoLinkAndAnAddressOfTheWebIs(): void
    {
        $library = self::bare(KunenaBBCodeLibrary::class);

        $soundcloud = $library->DoSoundcloud(self::stream(), BBCode::BBCODE_OUTPUT, 'soundcloud', null, [], 'javascript:alert(document.cookie)');
        $instagram  = KunenaBBCodeLibrary::DoInstagram(self::stream(), BBCode::BBCODE_OUTPUT, 'instagram', null, [], 'javascript://x:a%0aalert(1)');

        foreach (['soundcloud' => $soundcloud, 'instagram' => $instagram] as $tag => $html) {
            $this->assertStringNotContainsString('<a ', $html, $tag);
        }

        // An address of the web stays a link.
        $this->assertSame(
            '<a href="https://soundcloud.com/x" rel="nofollow noopener noreferrer" target="_blank">https://soundcloud.com/x</a>',
            $library->DoSoundcloud(self::stream(), BBCode::BBCODE_OUTPUT, 'soundcloud', null, [], 'https://soundcloud.com/x')
        );
    }
}
