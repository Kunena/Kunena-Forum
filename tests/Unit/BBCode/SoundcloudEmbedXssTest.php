<?php

/**
 * Kunena Component
 *
 * Regression test: a SoundCloud URL must not break out of the iframe src attribute.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use PHPUnit\Framework\TestCase;

class SoundcloudEmbedXssTest extends TestCase
{
    private const BBCODE = __DIR__ . '/../../../src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    private static function load(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', realpath(__DIR__ . '/../../../src/libraries/kunena'));
        }

        require_once self::BBCODE;
    }

    public function testSoundcloudUrlCannotInjectAnIframeAttribute(): void
    {
        self::load();

        $parser         = (new \ReflectionClass(KunenaBBCode::class))->newInstanceWithoutConstructor();
        $parser->config = new class () {
            public $autoLink             = false;
            public $autoEmbedSoundcloud  = true;
        };
        $parser->parent = (object) ['forceMinimal' => false];

        $html = $parser->parseUrl([
            'url'  => 'https://soundcloud.com/x"onload=alert(1)//',
            'text' => 'soundcloud.com/x',
            'host' => 'soundcloud.com',
            'path' => '/x"onload=alert(1)//',
            'query' => '',
        ]);

        $previous = libxml_use_internal_errors(true);
        $dom      = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $iframes = $dom->getElementsByTagName('iframe');

        $this->assertSame(1, $iframes->length);
        $iframe = $iframes->item(0);

        $this->assertSame(
            'https://w.soundcloud.com/player/?url=https://soundcloud.com/x"onload=alert(1)//&auto_play=false&visual=true',
            $iframe->getAttribute('src')
        );
        $this->assertFalse($iframe->hasAttribute('onload'));
    }
}
