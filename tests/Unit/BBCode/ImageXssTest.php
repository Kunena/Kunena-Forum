<?php

/**
 * Kunena Component
 *
 * Regression test: [img] parameters and URLs must never inject markup into the image layout.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/fixtures/generic_config.php';

class ImageXssTest extends TestCase
{
    private const LAYOUT = __DIR__ . '/../../../src/site/template/aurelia/layouts/bbcode/image/default.php';
    private const BBCODE = __DIR__ . '/../../../src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    protected function setUp(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        if (!class_exists('Kunena\Forum\Libraries\Config\KunenaConfig', false)) {
            class_alias(\Kunena\Tests\Unit\BBCode\Fixtures\GenericConfig::class, 'Kunena\Forum\Libraries\Config\KunenaConfig');
        }
    }

    /**
     * Render the real layout. The layout must not open a connection to the address: a test which does would hang or fail
     * loudly, because the address points at a closed local port.
     */
    private function render(string $url, $float = 0, $alt = 0): string
    {
        $view = new class ($url, $float, $alt) {
            public $title   = 'File attachment';
            public $size    = 0;
            public $canLink = true;

            public function __construct(public $url, public $float, public $alt)
            {
            }

            public function subLayout($name)
            {
                return '';
            }

            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }
        };

        $layout = self::LAYOUT;

        ob_start();
        (function () use ($layout) {
            include $layout;
        })->call($view);

        return ob_get_clean();
    }

    public function testHostileUrlIsEscapedInTheLinkAndTheImage(): void
    {
        $html = $this->render('http://127.0.0.1:1/<svg/onload=alert`1`>/x.png');

        $this->assertStringContainsString('<img ', $html, 'The image is always rendered, the browser loads it.');
        $this->assertStringNotContainsString('<svg', $html);
        $this->assertStringContainsString('&lt;svg', $html);
        $this->assertStringNotContainsString('image missing', $html);
    }

    public function testFloatIsAppliedToTheRenderedImage(): void
    {
        $this->assertStringContainsString('float:left;', $this->render('http://127.0.0.1:1/a.png', 'left'));
        $this->assertStringNotContainsString('float', str_replace('class="kmsgimage"', '', $this->render('http://127.0.0.1:1/a.png', 'x" onerror="alert(1)')));
    }

    public function testFloatCannotBreakOutOfStyleAttribute(): void
    {
        // The layout only reaches the <img> for a reachable image, so check the computed attribute through the source
        // of the layout: a hostile value must be dropped, not concatenated.
        $source = file_get_contents(self::LAYOUT);

        $this->assertStringNotContainsString('$this->float.";"', $source);
        $this->assertMatchesRegularExpression('/in_array\(\$this->float, \[\'left\', \'right\', \'none\'\], true\)/', $source);
    }

    public function testDoImageWhitelistsFloat(): void
    {
        $source = file_get_contents(self::BBCODE);

        $this->assertMatchesRegularExpression(
            '/->set\(\'float\', isset\(\$params\[\'float\'\]\) && \\\\in_array\(\$params\[\'float\'\], \[\'left\', \'right\', \'none\'\], true\)/',
            $source,
            'DoImage must only pass left, right or none on to the layout.'
        );
    }

    public function testNoBbcodeLayoutRequestsAnAddressOfAMember(): void
    {
        $found = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../../../src/site', \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || !preg_match('#/layouts/bbcode/#i', $path)) {
                continue;
            }

            // Comments may talk about it; code may not.
            $code = preg_replace('~/\*.*?\*/|(?<!:)//[^\n]*~s', '', file_get_contents($path));

            if (preg_match('/\b(get_headers|file_get_contents|fopen|curl_\w+|getimagesize|fsockopen|stream_socket_client|readfile|copy)\s*\(/i', $code)) {
                $found[] = $path;
            }
        }

        $this->assertSame([], $found, 'The server must not open the address of an image or a link written by a member.');
    }
}
