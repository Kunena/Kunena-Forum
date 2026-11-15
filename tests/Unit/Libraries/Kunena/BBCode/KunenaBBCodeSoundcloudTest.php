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

use DOMDocument;
use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;

/**
 * A SoundCloud address does not break out of the src of the frame (Kunena 7.1, #10323): a quote in it added attributes (onload...)
 * to the iframe.
 */
class KunenaBBCodeSoundcloudTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();

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

    public function testASoundcloudAddressCannotAddAttributesToTheFrame(): void
    {
        $parser         = (new ReflectionClass(KunenaBBCode::class))->newInstanceWithoutConstructor();
        $parser->config = (object) ['autoLink' => 0, 'autoEmbedSpotify' => 0, 'autoEmbedDeezer' => 0, 'autoEmbedSoundcloud' => 1];
        $parser->parent = (object) ['forceMinimal' => false];

        $html = $parser->parseUrl([
            'url'   => 'https://soundcloud.com/x"onload=alert(1)//',
            'text'  => 'soundcloud.com/x',
            'host'  => 'soundcloud.com',
            'path'  => '/x"onload=alert(1)//',
            'query' => '',
        ]);

        $previous = libxml_use_internal_errors(true);
        $dom      = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $iframes = $dom->getElementsByTagName('iframe');

        $this->assertSame(1, $iframes->length);
        $this->assertFalse($iframes->item(0)->hasAttribute('onload'));
        $this->assertSame(
            'https://w.soundcloud.com/player/?url=https://soundcloud.com/x"onload=alert(1)//&auto_play=false&visual=true',
            $iframes->item(0)->getAttribute('src')
        );
    }
}
