<?php

/**
 * Kunena Component
 *
 * Regression test for the class parameter of [url]. First it was printed into the class attribute without escaping, so a
 * quote broke out of the attribute (stored XSS). Escaping and a character whitelist stopped that, but the parameter still let a
 * member put any class of the site on a link: the utility classes of Bootstrap turn a link into one which covers the whole page
 * (every click goes to the site of the attacker), and scripts look for classes. Nothing needs the parameter, so it is ignored.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UrlClassXssTest extends TestCase
{
    private const LAYOUT = __DIR__ . '/../../../src/site/template/aurelia/layouts/bbcode/url/default.php';
    private const BBCODE = __DIR__ . '/../../../src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    private function render(array $props): string
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        // Same contract as KunenaLayout: variables are properties, escape() is htmlspecialchars.
        $view = new #[\AllowDynamicProperties] class () {
            public $internal = false;
            public $target   = '_blank';
            public $url      = 'http://example.com';
            public $content  = 'link';

            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }
        };

        foreach ($props as $name => $value) {
            $view->$name = $value;
        }

        $layout = self::LAYOUT;

        ob_start();
        (function () use ($layout) {
            include $layout;
        })->call($view);

        return ob_get_clean();
    }

    private function link(string $html): \DOMElement
    {
        $previous = libxml_use_internal_errors(true);
        $dom      = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $links = $dom->getElementsByTagName('a');

        $this->assertSame(1, $links->length, 'One link: ' . $html);

        return $links->item(0);
    }

    public static function classes(): array
    {
        return [
            'a quote and an event handler' => ['x" onmouseover="alert(1)'],
            'a page-covering link'         => ['position-fixed top-0 start-0 w-100 h-100'],
            'a class of a script'          => ['openmodal'],
            'a plain button look'          => ['btn btn-primary'],
            'a hidden link'                => ['d-none'],
            'one word'                     => ['red'],
            'empty'                        => [''],
        ];
    }

    #[DataProvider('classes')]
    public function testLayoutNeverPrintsAClassOfTheMember(string $class): void
    {
        $link = $this->link($this->render(['class' => $class]));

        $this->assertSame('bbcode_url', $link->getAttribute('class'), 'The class is always the one of the layout.');

        foreach ($link->attributes as $attribute) {
            $this->assertStringStartsNotWith('on', strtolower($attribute->name), 'An event handler was added.');
        }
    }

    public function testLayoutKeepsTheLinkItself(): void
    {
        $external = $this->link($this->render(['class' => 'btn', 'internal' => false, 'target' => '_blank', 'url' => 'http://example.com/a?b=1&c=2']));

        $this->assertSame('http://example.com/a?b=1&c=2', $external->getAttribute('href'));
        $this->assertSame('_blank', $external->getAttribute('target'));
        $this->assertSame('nofollow noopener noreferrer', $external->getAttribute('rel'));
        $this->assertSame('link', trim($external->textContent));

        $internal = $this->link($this->render(['class' => 'btn', 'internal' => true, 'url' => '/index.php?option=com_kunena']));

        $this->assertSame('/index.php?option=com_kunena', $internal->getAttribute('href'));
        $this->assertFalse($internal->hasAttribute('target'));
        $this->assertFalse($internal->hasAttribute('rel'));
    }

    public function testLayoutStillEscapesTheAddress(): void
    {
        $link = $this->link($this->render(['url' => 'http://example.com/"onmouseover="alert(1)']));

        foreach ($link->attributes as $attribute) {
            $this->assertStringStartsNotWith('on', strtolower($attribute->name));
        }
    }

    public function testLayoutDoesNotReadAClassAtAll(): void
    {
        $source = file_get_contents(self::LAYOUT);

        $this->assertDoesNotMatchRegularExpression('/\$this->class\b/', $source);
        $this->assertStringContainsString('class="bbcode_url"', $source);
    }

    public function testDoUrlIgnoresTheClassParameter(): void
    {
        $source = file_get_contents(self::BBCODE);

        $this->assertSame(1, preg_match('/public function DoURL\([^)]*\)\s*\{(.*?)\n    \}\n/s', $source, $m));
        $body = $m[1];

        $this->assertDoesNotMatchRegularExpression('/\$params\[\'class\'\]/', $body, 'DoURL must not read the class parameter.');
        $this->assertDoesNotMatchRegularExpression('/->set\(\'class\'/', $body, 'and must not hand a class to the layout.');
        $this->assertStringContainsString("\$target = isset(\$params['target']) ? \$bbcode->url_target : '';", $body, 'The member still chooses the target.');
    }

    public function testNoLayoutOfTheUrlTagPrintsAClass(): void
    {
        $found = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(__DIR__ . '/../../../src/site', \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() === 'php' && preg_match('#/layouts/bbcode/url/#i', $path) && preg_match('/\$this->class\b/', file_get_contents($path))) {
                $found[] = $path;
            }
        }

        $this->assertSame([], $found, 'A layout of the url tag prints a class of the member.');
    }
}
