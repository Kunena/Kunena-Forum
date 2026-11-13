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
use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The links in messages to the own domains of the site (configuration ownDomains) get no rel="nofollow noopener noreferrer".
 */
class KunenaBBCodeOwnDomainTest extends UnitTestCase
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

        if (!class_exists(KunenaBBCode::class, false)) {
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

        $properties += ['target' => '_blank', 'url' => 'https://example.com/page', 'content' => 'link'];

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

    public static function ownDomains(): array
    {
        return [
            'the domain'                  => ['https://example.com/page', 'example.com'],
            'a subdomain'                 => ['https://sub.example.com/page', 'example.com'],
            'a host in upper case'        => ['https://SHOP.Example.COM/', 'example.com'],
            'an entry in upper case'      => ['https://shop.example.com/', 'EXAMPLE.com'],
            'an entry written as address' => ['https://example.com/', 'https://example.com/forum'],
            'an entry with a port'        => ['https://example.com/', 'example.com:8080'],
            'an entry with blanks'        => ['https://example.com/', "  example.com \t "],
            'an entry with a leading dot' => ['https://shop.example.com/', '.example.com'],
            'a list of several lines'     => ['https://blog.example.org/', "example.com\r\nexample.org\nexample.net"],
            'a list with commas'          => ['https://example.net/', 'example.com, example.org;example.net'],
        ];
    }

    #[DataProvider('ownDomains')]
    public function testOwnDomain(string $url, string $list): void
    {
        $this->assertTrue(KunenaBBCode::isOwnDomain($url, $list));
    }

    public static function otherDomains(): array
    {
        return [
            'another domain with the same end' => ['https://notexample.com/', 'example.com'],
            'the domain as a subdomain'        => ['https://example.com.evil.org/', 'example.com'],
            'the domain as the user'           => ['http://example.com@evil.org/', 'example.com'],
            'an empty list'                    => ['https://example.com/', ''],
            'a list of only blanks'            => ['https://example.com/', " \r\n\t "],
            'a star'                           => ['https://example.com/', '*'],
            'a star as subdomain'              => ['https://shop.example.com/', '*.example.com'],
            'an entry with a blank'            => ['https://example.com/', 'exa mple'],
            'an entry of dots'                 => ['https://example.com/', '..'],
            'an entry with an empty label'     => ['https://example..com/', 'example..com'],
            'a url without a host'             => ['/index.php?option=com_kunena', 'example.com'],
            'a url of mail'                    => ['mailto:info@example.com', 'example.com'],
            'a backslash before the user'      => ['http://evil.org\\@example.com/', 'example.com'],
            'two backslashes before the user'  => ['http://evil.org\\\\@example.com/', 'example.com'],
            'a user on the domain'             => ['http://user@example.com/', 'example.com'],
            'a user and a password'            => ['http://user:pass@example.com/', 'example.com'],
        ];
    }

    #[DataProvider('otherDomains')]
    public function testOtherDomain(string $url, string $list): void
    {
        $this->assertFalse(KunenaBBCode::isOwnDomain($url, $list));
    }

    public function testLinkToAnOwnDomainHasNoRel(): void
    {
        $link = $this->link($this->layout(['internal' => false, 'ownDomain' => true]));

        $this->assertFalse($link->hasAttribute('rel'));
        $this->assertSame('_blank', $link->getAttribute('target'));
    }

    public function testLinkToAnotherDomainKeepsRel(): void
    {
        $link = $this->link($this->layout(['internal' => false, 'ownDomain' => false]));

        $this->assertSame('nofollow noopener noreferrer', $link->getAttribute('rel'));
        $this->assertSame('_blank', $link->getAttribute('target'));
    }

    public function testInternalLinkHasNeitherRelNorTarget(): void
    {
        $link = $this->link($this->layout(['internal' => true, 'url' => '/index.php?option=com_kunena']));

        $this->assertFalse($link->hasAttribute('rel'));
        $this->assertFalse($link->hasAttribute('target'));
    }
}
