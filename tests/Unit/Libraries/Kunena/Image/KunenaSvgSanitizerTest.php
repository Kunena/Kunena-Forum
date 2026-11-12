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

namespace Kunena\Tests\Unit\Libraries\Kunena\Image;

use enshrined\svgSanitize\Sanitizer;
use Kunena\Forum\Libraries\Image\KunenaSvgSanitizer;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

/**
 * An SVG avatar is stored only after it is cleaned: no script, no event attribute, no javascript: link, no foreignObject and no
 * remote reference survive, and a file which is not an SVG document (or declares entities) is refused.
 */
class KunenaSvgSanitizerTest extends UnitTestCase
{
    private const SVG_OPEN = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100" height="100">';

    protected function setUp(): void
    {
        // The sanitiser is bundled in the folder External of the library. Joomla has an older copy in its vendor folder: the loader
        // of the bundled copy comes first, as the autoloader of External does on the site (it is registered with prepend).
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'enshrined\\svgSanitize\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/enshrined/svg-sanitize/src/'
                    . str_replace('\\', '/', substr($class, \strlen('enshrined\\svgSanitize\\'))) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        }, true, true);
    }

    public function testTheBundledSanitizerIsUsed(): void
    {
        $file = str_replace('\\', '/', (string) (new ReflectionClass(Sanitizer::class))->getFileName());

        $this->assertStringEndsWith('src/libraries/kunena/External/enshrined/svg-sanitize/src/Sanitizer.php', $file);
    }

    public function testAValidSvgKeepsItsRootAndShapes(): void
    {
        $clean = KunenaSvgSanitizer::clean(self::SVG_OPEN . '<circle cx="50" cy="50" r="40" fill="red"/></svg>');

        $this->assertNotNull($clean);
        $this->assertMatchesRegularExpression('/<svg\b/', $clean);
        $this->assertStringContainsString('<circle', $clean);
        $this->assertStringContainsString('r="40"', $clean);
    }

    public function testAPublicDoctypeIsAcceptedAndDropped(): void
    {
        $clean = KunenaSvgSanitizer::clean('<?xml version="1.0"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" '
            . '"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . self::SVG_OPEN . '<circle cx="50" cy="50" r="40"/></svg>');

        $this->assertNotNull($clean);
        $this->assertStringNotContainsStringIgnoringCase('<!DOCTYPE', $clean);
        $this->assertStringContainsString('<circle', $clean);
    }

    public function testAScriptIsRemoved(): void
    {
        $clean = KunenaSvgSanitizer::clean(self::SVG_OPEN . '<script>alert(1)</script><circle cx="50" cy="50" r="40"/></svg>');

        $this->assertNotNull($clean);
        $this->assertStringNotContainsStringIgnoringCase('<script', $clean);
        $this->assertStringNotContainsString('alert', $clean);
        $this->assertStringContainsString('<circle', $clean);
    }

    public function testEventAttributesAreRemoved(): void
    {
        $clean = KunenaSvgSanitizer::clean(
            '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><circle cx="50" cy="50" r="40" onclick="alert(2)"/></svg>'
        );

        $this->assertNotNull($clean);
        $this->assertStringNotContainsStringIgnoringCase('onload', $clean);
        $this->assertStringNotContainsStringIgnoringCase('onclick', $clean);
        $this->assertStringNotContainsString('alert', $clean);
    }

    public function testAJavascriptLinkIsRemoved(): void
    {
        $clean = KunenaSvgSanitizer::clean(
            self::SVG_OPEN . '<a href="javascript:alert(1)"><circle cx="50" cy="50" r="40"/></a>'
            . '<a xlink:href="javascript:alert(2)"><rect width="10" height="10"/></a></svg>'
        );

        $this->assertNotNull($clean);
        $this->assertStringNotContainsStringIgnoringCase('javascript:', $clean);
    }

    public function testAForeignObjectIsRemoved(): void
    {
        $clean = KunenaSvgSanitizer::clean(
            self::SVG_OPEN . '<foreignObject width="100" height="100"><body xmlns="http://www.w3.org/1999/xhtml">'
            . '<iframe src="https://example.org/"></iframe></body></foreignObject><circle cx="50" cy="50" r="40"/></svg>'
        );

        $this->assertNotNull($clean);
        $this->assertStringNotContainsStringIgnoringCase('foreignObject', $clean);
        $this->assertStringNotContainsStringIgnoringCase('iframe', $clean);
    }

    public function testRemoteReferencesAreRemoved(): void
    {
        $clean = KunenaSvgSanitizer::clean(
            self::SVG_OPEN . '<image href="https://example.com/x.png" width="10" height="10"/>'
            . '<use xlink:href="http://example.com/a.svg#b"/><circle cx="50" cy="50" r="40"/></svg>'
        );

        $this->assertNotNull($clean);
        $this->assertStringNotContainsString('example.com', $clean);
    }

    public function testAnEntityDeclarationIsRefused(): void
    {
        $this->assertNull(KunenaSvgSanitizer::clean(
            '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>'
            . '<svg xmlns="http://www.w3.org/2000/svg"><text>&xxe;</text></svg>'
        ));
        $this->assertNull(KunenaSvgSanitizer::clean(
            '<!DOCTYPE svg [<!entity a "aaaaaaaaaa">]><svg xmlns="http://www.w3.org/2000/svg"><text>&a;</text></svg>'
        ));
    }

    public static function notSvgProvider(): array
    {
        return [
            'PNG bytes'      => [base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==')],
            'plain text'     => ['This is not a picture, only some text.'],
            'HTML document'  => ['<!DOCTYPE html><html><head><title>x</title></head><body><p>Hello</p><script>alert(1)</script></body></html>'],
            'XHTML document' => ['<html xmlns="http://www.w3.org/1999/xhtml"><body><p>Hello</p></body></html>'],
        ];
    }

    #[DataProvider('notSvgProvider')]
    public function testANonSvgFileIsRefused(string $content): void
    {
        $this->assertNull(KunenaSvgSanitizer::clean($content));
    }
}
