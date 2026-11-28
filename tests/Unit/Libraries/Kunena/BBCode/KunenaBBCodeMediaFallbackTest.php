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
 * A media tag which cannot be embedded keeps its address as a link: a [soundcloud] of another address (a short link, www.) and a
 * [video] of a provider which is not embedded (DivX, QuickTime... which the dialog of the editor still offers) gave nothing, and the
 * post lost the address.
 */
class KunenaBBCodeMediaFallbackTest extends UnitTestCase
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

    public function testASoundcloudAddressOfAnotherHostIsALink(): void
    {
        $bbcode = (object) ['parent' => (object) []];
        $out    = self::bare(KunenaBBCodeLibrary::class)->DoSoundcloud($bbcode, BBCode::BBCODE_OUTPUT, 'soundcloud', '', [], 'https://on.soundcloud.com/abc');

        $this->assertStringContainsString('href="https://on.soundcloud.com/abc"', (string) $out);
    }

    public function testAWwwSoundcloudAddressIsEmbedded(): void
    {
        $bbcode = (object) ['parent' => (object) []];
        $out    = self::bare(KunenaBBCodeLibrary::class)->DoSoundcloud($bbcode, BBCode::BBCODE_OUTPUT, 'soundcloud', '', [], 'https://www.soundcloud.com/a/b');

        $this->assertStringContainsString('<iframe', (string) $out);
    }

    public function testAVideoOfAnotherProviderIsALink(): void
    {
        $source = Files::read('src/libraries/kunena/src/BBCode/KunenaBBCode.php');

        $this->assertMatchesRegularExpression('/\} else \{\s*\/\/ A provider which is not embedded[^\n]*\n[^\n]*\n\s*return self::minimalLink\(\$content\);/', $source);
    }
}
