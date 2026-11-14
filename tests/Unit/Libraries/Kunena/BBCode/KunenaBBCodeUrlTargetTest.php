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

use AllowDynamicProperties;
use DOMDocument;
use DOMElement;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * [url=... target=_blank]: the link dialog of CKEditor writes the parameter for "New window". Only whether the parameter is
 * there matters: without it the link opens in the same window, with it in a new window, and the value the member wrote never
 * reaches the HTML.
 */
class KunenaBBCodeUrlTargetTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    private const LAYOUT = 'src/site/template/aurelia/layouts/bbcode/url/default.php';

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

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    private function link(string $html): DOMElement
    {
        $previous = libxml_use_internal_errors(true);
        $dom      = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $links = $dom->getElementsByTagName('a');
        $this->assertSame(1, $links->length, $html);

        return $links->item(0);
    }

    /**
     * The layout of the template with its variables (the contract of KunenaLayout: properties, and escape()).
     */
    private function layout(array $properties): string
    {
        $view = new #[AllowDynamicProperties] class () {
            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }
        };

        foreach ($properties as $name => $value) {
            $view->$name = $value;
        }

        $path = Files::path(self::LAYOUT);

        ob_start();
        (function () use ($path) {
            include $path;
        })->call($view);

        return (string) ob_get_clean();
    }

    public function testExternalLinkWithoutTheParameterOpensInTheSameWindow(): void
    {
        $link = $this->link($this->layout(['internal' => false, 'target' => '', 'url' => 'https://example.com', 'content' => 'x']));

        $this->assertNotSame('_blank', $link->getAttribute('target'));
        $this->assertSame('nofollow noopener noreferrer', $link->getAttribute('rel'));
    }

    public function testExternalLinkWithTheParameterOpensInANewWindow(): void
    {
        $link = $this->link($this->layout(['internal' => false, 'target' => '_blank', 'url' => 'https://example.com', 'content' => 'x']));

        $this->assertSame('_blank', $link->getAttribute('target'));
        $this->assertSame('nofollow noopener noreferrer', $link->getAttribute('rel'));
    }

    public function testValueOfTheParameterNeverReachesTheHtml(): void
    {
        $this->assertSame(1, preg_match('/public function DoURL\([^)]*\)\s*\{(.*?)\n    \}\n/s', Files::read(self::BBCODE), $m));
        $body = $m[1];

        // The parameter is only tested for presence.
        $this->assertGreaterThan(0, substr_count($body, "\$params['target']"));
        $this->assertSame(substr_count($body, "\$params['target']"), substr_count($body, "isset(\$params['target'])"));

        // The target given to the layout is the fixed one of the parser.
        $this->assertMatchesRegularExpression('/\$target\s*=\s*isset\(\$params\[\'target\'\]\)\s*\?\s*\$bbcode->url_target\s*:\s*\'\';/', $body);
        $this->assertSame(1, substr_count($body, '$target ='));
        $this->assertStringContainsString("->set('target', \$target)", $body);
    }
}
