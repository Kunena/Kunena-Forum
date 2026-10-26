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

namespace Kunena\Tests\Unit\Libraries\Kunena\Html;

use Kunena\Forum\Libraries\Html\KunenaLinkPreview;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The card of a link is made from the Open Graph data of a page: the rules for the text and the picture.
 */
class KunenaLinkPreviewTest extends UnitTestCase
{
    /**
     * The protected methods of the class are the rules which are tested here.
     */
    private function call(string $method, mixed ...$arguments): mixed
    {
        return self::invoke(KunenaLinkPreview::class, $method, ...$arguments);
    }

    public function testTheOpenGraphDataMakesTheCard(): void
    {
        $html = '<html><head><title>Fallback</title>'
            . '<meta property="og:title" content="The title">'
            . '<meta property="og:description" content="A short description">'
            . '<meta property="og:image" content="https://cdn.example.com/a.jpg">'
            . '<meta property="og:site_name" content="Example Site">'
            . '</head><body></body></html>';

        $card = $this->call('parse', $html, 'https://example.com/page');

        $this->assertSame('The title', $card['title']);
        $this->assertSame('A short description', $card['description']);
        $this->assertSame('https://cdn.example.com/a.jpg', $card['image']);
        $this->assertSame('Example Site', $card['site']);
    }

    public function testThePageTitleAndTheHostAreTheFallback(): void
    {
        $card = $this->call('parse', '<html><head><title> My  page </title></head></html>', 'https://example.com/x');

        $this->assertSame('My page', $card['title']);
        $this->assertSame('', $card['description']);
        $this->assertSame('', $card['image']);
        $this->assertSame('example.com', $card['site']);
    }

    public function testTwitterDataIsUsedWhenThereIsNoOpenGraphData(): void
    {
        $html = '<head><meta name="twitter:title" content="Tweet title"><meta name="twitter:image" content="https://i.example.com/t.png"></head>';

        $card = $this->call('parse', $html, 'https://example.com/');

        $this->assertSame('Tweet title', $card['title']);
        $this->assertSame('https://i.example.com/t.png', $card['image']);
    }

    public function testAPageWithoutATitleHasNoCard(): void
    {
        $this->assertNull($this->call('parse', '<html><head></head><body>text</body></html>', 'https://example.com/'));
        $this->assertNull($this->call('parse', '', 'https://example.com/'));
    }

    public function testMarkupInTheTextOfAnotherSiteIsRemoved(): void
    {
        $html = '<head><meta property="og:title" content="&lt;b&gt;Bold&lt;/b&gt; &lt;script&gt;alert(1)&lt;/script&gt;"></head>';

        $card = $this->call('parse', $html, 'https://example.com/');

        $this->assertStringNotContainsString('<', $card['title']);
        $this->assertStringContainsString('Bold', $card['title']);
    }

    public function testALongDescriptionIsShortened(): void
    {
        $html = '<head><meta property="og:title" content="T"><meta property="og:description" content="' . str_repeat('word ', 200) . '"></head>';

        $card = $this->call('parse', $html, 'https://example.com/');

        $this->assertLessThanOrEqual(220, mb_strlen($card['description']));
        $this->assertStringEndsWith('…', $card['description']);
    }

    public static function pictures(): array
    {
        return [
            'an absolute address'       => ['https://cdn.example.com/a.jpg', 'https://example.com/dir/page', 'https://cdn.example.com/a.jpg'],
            'a path'                    => ['/img/a.jpg', 'https://example.com/dir/page', 'https://example.com/img/a.jpg'],
            'the protocol of the page'  => ['//cdn.example.com/a.jpg', 'https://example.com/dir/page', 'https://cdn.example.com/a.jpg'],
            'a port is kept'            => ['/a.jpg', 'http://example.com:8080/x', 'http://example.com:8080/a.jpg'],
            'javascript'                => ['javascript:alert(1)', 'https://example.com/', ''],
            'data'                      => ['data:image/png;base64,AAAA', 'https://example.com/', ''],
            'a relative name'           => ['a.jpg', 'https://example.com/dir/page', ''],
            'empty'                     => ['', 'https://example.com/', ''],
            'a quote in the address'    => ['https://x.com/a".jpg', 'https://example.com/', ''],
        ];
    }

    #[DataProvider('pictures')]
    public function testThePictureAddress(string $image, string $page, string $expected): void
    {
        $this->assertSame($expected, $this->call('absolute', $image, $page));
    }

    public function testCleanMakesOneLineOfPlainText(): void
    {
        $this->assertSame('One two three', $this->call('clean', "  <i>One</i>\n two\t three ", 100));
    }

    public function testCleanCutsTheText(): void
    {
        $text = $this->call('clean', 'abcdefghijklmnopqrstuvwxyz', 10);

        $this->assertSame(10, mb_strlen($text));
        $this->assertStringEndsWith('…', $text);
    }

    /**
     * The addresses the server may read a page from: only public ones, never one of its own network, also when the address
     * is written as IPv6 or reached through a gateway which leads to IPv4 (NAT64, 6to4, Teredo).
     */
    public static function addresses(): array
    {
        return [
            'a public IPv4 address'            => ['93.184.215.14', true],
            'a public IPv6 address'            => ['2606:2800:21f:cb07:6820:80da:af6b:8b2c', true],
            'the loopback'                     => ['127.0.0.1', false],
            'another loopback'                 => ['127.1.2.3', false],
            'a private network'                => ['10.0.0.5', false],
            'a private network (192.168)'      => ['192.168.1.1', false],
            'a private network (172.16)'       => ['172.16.0.1', false],
            'the cloud metadata service'       => ['169.254.169.254', false],
            'the shared address space'         => ['100.64.0.1', false],
            'no address'                       => ['0.0.0.0', false],
            'the IPv6 loopback'                => ['::1', false],
            'the IPv6 unspecified address'     => ['::', false],
            'a unique local IPv6 address'      => ['fd00::1', false],
            'a link local IPv6 address'        => ['fe80::1', false],
            'IPv4 in IPv6 (mapped)'            => ['::ffff:127.0.0.1', false],
            'IPv4 in IPv6 (mapped, hex)'       => ['::ffff:7f00:1', false],
            'NAT64 to the loopback'            => ['64:ff9b::7f00:1', false],
            'NAT64 to a public address'        => ['64:ff9b::5db8:d70e', false],
            '6to4 to the loopback'             => ['2002:7f00:1::1', false],
            'Teredo'                           => ['2001:0:4136:e378:8000:63bf:3fff:fdd2', false],
            'the documentation range'          => ['2001:db8::1', false],
            'not an address'                   => ['example.com', false],
        ];
    }

    #[DataProvider('addresses')]
    public function testOnlyPublicAddressesAreRead(string $ip, bool $public): void
    {
        $this->assertSame($public, $this->call('isPublicAddress', $ip));
    }

    /**
     * The page is read from the address which was checked, not from a second lookup of the name (which could give another
     * address): the address goes into the URL, the name stays in the Host header and for the certificate.
     */
    public function testThePageIsReadFromTheCheckedAddress(): void
    {
        $this->assertSame(
            ['https://93.184.215.14/a/b?c=1', 'example.com'],
            $this->call('pinned', parse_url('https://example.com/a/b?c=1#frag'), '93.184.215.14')
        );
        $this->assertSame(
            ['http://[2606:2800:21f:cb07:6820:80da:af6b:8b2c]:8080/', 'example.com:8080'],
            $this->call('pinned', parse_url('http://user:pass@example.com:8080'), '2606:2800:21f:cb07:6820:80da:af6b:8b2c')
        );
    }
}
