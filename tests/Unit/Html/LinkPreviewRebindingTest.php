<?php

/**
 * Kunena Component
 *
 * Regression test for the page reader of the link preview. The name of the host was resolved and checked, and then
 * file_get_contents() resolved it again: a site which answers with a public address the first time and with an internal one
 * (127.0.0.1, the metadata service of the cloud, ...) the second time (DNS rebinding) was read by the server. IPv6 answers
 * (never checked) and the ranges the filter flags of PHP do not know (100.64.0.0/10 ...) were open too.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Html;

use Kunena\Forum\Libraries\Html\KunenaLinkPreview;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LinkPreviewRebindingTest extends TestCase
{
    private const SOURCE = __DIR__ . '/../../../src/libraries/kunena/src/Html/KunenaLinkPreview.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once self::SOURCE;
    }

    public static function addresses(): array
    {
        return [
            'public'             => ['93.184.216.34', true],
            'public 2'           => ['8.8.8.8', true],
            'loopback'           => ['127.0.0.1', false],
            'loopback 2'         => ['127.255.255.254', false],
            'private 10'         => ['10.1.2.3', false],
            'private 172'        => ['172.16.0.1', false],
            'private 192'        => ['192.168.1.1', false],
            'metadata'           => ['169.254.169.254', false],
            'this network'       => ['0.0.0.0', false],
            'carrier grade nat'  => ['100.64.0.1', false],
            'benchmarking'       => ['198.18.0.1', false],
            'documentation'      => ['203.0.113.7', false],
            'multicast'          => ['224.0.0.1', false],
            'reserved'           => ['240.0.0.1', false],
            'broadcast'          => ['255.255.255.255', false],
            'ipv6 loopback'      => ['::1', false],
            'ipv6 mapped'        => ['::ffff:127.0.0.1', false],
            'not an address'     => ['localhost', false],
            'empty'              => ['', false],
        ];
    }

    #[DataProvider('addresses')]
    public function testIsPublicIp(string $ip, bool $expected): void
    {
        $this->assertSame($expected, KunenaLinkPreview::isPublicIp($ip));
    }

    public function testConnectionGoesToTheCheckedAddress(): void
    {
        $calls = 0;
        $plan  = KunenaLinkPreview::planRequest('https://example.org/a/b?c=1', function ($host) use (&$calls) {
            $calls++;

            return $host === 'example.org' ? ['93.184.216.34'] : false;
        });

        $this->assertSame(1, $calls, 'The name is resolved once; a second lookup is what rebinding needs.');
        $this->assertSame('https://93.184.216.34:443/a/b?c=1', $plan['url'], 'The request goes to the address which was checked.');
        $this->assertStringNotContainsString('example.org', $plan['url']);
        $this->assertSame('example.org', $plan['host'], 'The name is kept for the certificate.');
        $this->assertSame('example.org', $plan['host_header']);
    }

    public function testPortOfTheLinkIsKeptInTheHostHeader(): void
    {
        $plan = KunenaLinkPreview::planRequest('http://example.org:8080/x', fn () => ['93.184.216.34']);

        $this->assertSame('http://93.184.216.34:8080/x', $plan['url']);
        $this->assertSame('example.org:8080', $plan['host_header']);
    }

    public function testOneInternalAnswerRefusesTheHost(): void
    {
        $this->assertNull(KunenaLinkPreview::planRequest('http://rebind.example/', fn () => ['93.184.216.34', '127.0.0.1']));
        $this->assertNull(KunenaLinkPreview::planRequest('http://rebind.example/', fn () => ['169.254.169.254']));
        $this->assertNull(KunenaLinkPreview::planRequest('http://rebind.example/', fn () => ['100.64.1.1']));
    }

    public function testNoAnswerRefusesTheHost(): void
    {
        $this->assertNull(KunenaLinkPreview::planRequest('http://nowhere.example/', fn () => false));
        $this->assertNull(KunenaLinkPreview::planRequest('http://nowhere.example/', fn () => []));
    }

    public static function refused(): array
    {
        return [
            'file'            => ['file:///etc/passwd'],
            'php'             => ['php://filter/resource=http://example.org/'],
            'ftp'             => ['ftp://example.org/'],
            'gopher'          => ['gopher://example.org:80/_x'],
            'no scheme'       => ['//example.org/'],
            'login in link'   => ['http://user:pass@example.org/'],
            'user only'       => ['http://admin@example.org/'],
            'redis port'      => ['http://example.org:6379/'],
            'ssh port'        => ['http://example.org:22/'],
            'mysql port'      => ['http://example.org:3306/'],
            'loopback'        => ['http://127.0.0.1/'],
            'metadata'        => ['http://169.254.169.254/latest/meta-data/'],
            'decimal ip'      => ['http://2130706433/'],
            'octal ip'        => ['http://0177.0.0.1/'],
            'hex ip'          => ['http://0x7f.0.0.1/'],
            'ipv6 loopback'   => ['http://[::1]/'],
            'ipv6 mapped'     => ['http://[::ffff:127.0.0.1]/'],
            'localhost'       => ['http://localhost/'],
        ];
    }

    #[DataProvider('refused')]
    public function testRefusedWithTheRealResolver(string $url): void
    {
        // Only literals and localhost: nothing here asks the internet.
        $this->assertNull(KunenaLinkPreview::planRequest($url));
    }

    public function testFetchPageConnectsToThePlanAndResolvesNothingElse(): void
    {
        $source = file_get_contents(self::SOURCE);

        $this->assertSame(1, preg_match('/public static function fetchPage\([^)]*\)[^{]*\{(.*?)\r?\n    \}\r?\n/s', $source, $m));
        $body = $m[1];

        $this->assertStringContainsString('self::planRequest($url)', $body);
        $this->assertMatchesRegularExpression('/file_get_contents\(\$plan\[\'url\'\]/', $body, 'The pinned address is what is read.');
        $this->assertDoesNotMatchRegularExpression('/file_get_contents\(\s*\$url\b/', $body, 'The name must not be looked up a second time.');
        $this->assertStringNotContainsString('gethostbyname', $body, 'Resolving belongs to planRequest().');
        $this->assertStringContainsString("'follow_location' => 0", $body, 'No redirects.');
        $this->assertStringContainsString("'verify_peer_name' => true", $body);
        $this->assertStringContainsString("'peer_name' => \$plan['host']", $body, 'The certificate is checked against the name of the link.');
    }
}
