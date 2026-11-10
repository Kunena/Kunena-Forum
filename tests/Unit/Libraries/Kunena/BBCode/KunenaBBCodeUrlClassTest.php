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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * [url class=...]: the parameter was printed into the class attribute as the member wrote it, so a quote left the attribute
 * (stored XSS). A class name alone also lets a member restyle the link with the classes of the site. Nothing needs the
 * parameter, so it is ignored.
 */
class KunenaBBCodeUrlClassTest extends UnitTestCase
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

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    private function links(string $html): \DOMNodeList
    {
        $previous = libxml_use_internal_errors(true);
        $dom      = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $dom->getElementsByTagName('a');
    }

    /**
     * A layout of the template with its variables (the contract of KunenaLayout: properties, and escape()).
     */
    private function layout(string $file, array $properties): string
    {
        $view = new #[\AllowDynamicProperties] class () {
            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }
        };

        foreach ($properties as $name => $value) {
            $view->$name = $value;
        }

        $path = Files::path($file);

        ob_start();
        (function () use ($path) {
            include $path;
        })->call($view);

        return (string) ob_get_clean();
    }

    public static function classes(): array
    {
        return [
            'a quote and a handler' => ['x" onmouseover="alert(1)'],
            'markup'                => ['"><script>alert(1)</script>'],
            'a class of the site'   => ['stretched-link position-fixed'],
        ];
    }

    #[DataProvider('classes')]
    public function testClassOfTheLinkIsNeverTheOneOfTheMember(string $class): void
    {
        $html = $this->layout(
            'src/site/template/aurelia/layouts/bbcode/url/default.php',
            ['internal' => false, 'target' => '_blank', 'url' => 'http://example.com', 'content' => 'link', 'class' => $class]
        );

        $links = $this->links($html);

        $this->assertSame(1, $links->length, $html);
        $this->assertSame('bbcode_url', $links->item(0)->getAttribute('class'));
        $this->assertFalse($links->item(0)->hasAttribute('onmouseover'));
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function testClassParameterOfTheTagIsIgnored(): void
    {
        $this->assertSame(1, preg_match('/public function DoURL\([^)]*\)\s*\{(.*?)\n    \}\n/s', Files::read(self::BBCODE), $m));
        $this->assertStringNotContainsString("\$params['class']", $m[1]);
        $this->assertStringNotContainsString("->set('class'", $m[1]);
        $this->assertStringNotContainsString('$this->class', Files::read('src/site/template/aurelia/layouts/bbcode/url/default.php'));
    }
}
