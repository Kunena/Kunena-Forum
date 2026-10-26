<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Html
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Html;

\defined('_JEXEC') or die();

use DOMDocument;
use Exception;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Throwable;

/**
 * The card of a link (title, description and image of the page, from its Open Graph data), kept in the Joomla cache.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaLinkPreview
{
    /**
     * The card is kept for a week, a page without a card (or which did not answer) is asked again after a day.
     */
    protected const int TTL           = 7 * 86400;
    protected const int FAILED_TTL    = 86400;

    /**
     * Pages which are read in one request: the cards of the cache are free, a new page waits for the other site.
     */
    protected const int MAX_FETCHES   = 3;

    /**
     * @var     integer
     * @since   Kunena 8.0
     */
    protected static mixed $fetches = 0;

    /**
     * The address of the picture of the page as an absolute http(s) address, or an empty text.
     *
     * @param   string  $image  The address in the page.
     * @param   string  $page   The address of the page.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function absolute(string $image, string $page): string
    {
        $image = trim($image);

        if ($image === '' || \strlen($image) > 2000) {
            return '';
        }

        $parts = parse_url($page);
        $root  = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '') . (isset($parts['port']) ? ':' . $parts['port'] : '');

        if (str_starts_with($image, '//')) {
            $image = ($parts['scheme'] ?? 'https') . ':' . $image;
        } elseif (str_starts_with($image, '/')) {
            $image = $root . $image;
        }

        return preg_match('#^https?://[^\s"<>]+$#i', $image) ? $image : '';
    }

    /**
     * The Joomla cache with the cards: it is kept (also when the cache of the site is switched off), and it is emptied with
     * the cache of the site.
     *
     * @return  Cache|null
     *
     * @since   Kunena 8.0
     */
    protected static function cache()
    {
        try {
            $controller = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController(
                'output',
                ['defaultgroup' => 'com_kunena_linkpreview', 'caching' => true, 'lifetime' => 60 * 24 * 30]
            );

            return $controller->cache;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Plain text of the other site: no markup, one line, not too long (it is escaped when it is shown).
     *
     * @param   string   $text    The text.
     * @param   integer  $length  The most characters.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function clean(string $text, int $length): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');

        return mb_strimwidth($text, 0, $length, '…', 'UTF-8');
    }

    /**
     * The page as UTF-8 text. Only web pages are read, never an address of the own network, there are no redirects (they
     * could lead to an internal address), and only the start of the page is taken.
     *
     * @param   string   $url      The address.
     * @param   integer  $timeout  Seconds to wait for the other site.
     *
     * @return  string|null  Null when the page can not be read.
     *
     * @since   Kunena 8.0
     */
    public static function fetchPage(string $url, int $timeout = 3): ?string
    {
        $parts = parse_url($url);

        // Only fetch web pages. Other schemes (file:, php:, ftp: ...) are never read.
        if (!$parts || empty($parts['host']) || !\in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return null;
        }

        // Do not let the server read addresses of its own network: every address of the name (IPv4 and IPv6) must be public.
        $host = trim($parts['host'], '[]');

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips = [$host];
        } else {
            $ips = gethostbynamel($host) ?: [];

            foreach (@dns_get_record($host, DNS_AAAA) ?: [] as $record) {
                if (!empty($record['ipv6'])) {
                    $ips[] = $record['ipv6'];
                }
            }
        }

        if (!$ips) {
            return null;
        }

        foreach ($ips as $ip) {
            if (!static::isPublicAddress($ip)) {
                return null;
            }
        }

        // The page is read from the address which was checked: a second lookup of the name could give another one (an
        // address of the own network, given by the DNS of the other site after the check).
        [$target, $hostHeader] = static::pinned($parts, $ips[0]);

        // Many sites refuse a request which does not say who asks.
        $agent   = 'Mozilla/5.0 (compatible; KunenaLinkPreview/8.0; +' . Uri::root() . ')';
        $context = stream_context_create([
            'http' => ['timeout' => $timeout, 'follow_location' => 0, 'ignore_errors' => true, 'header' => "Host: $hostHeader\r\nUser-Agent: $agent\r\nAccept: text/html,application/xhtml+xml\r\n"],
            'ssl'  => ['peer_name' => $host, 'SNI_enabled' => true, 'verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $body = @file_get_contents($target, false, $context, 0, 262144);

        if ($body === false || $body === '') {
            return null;
        }

        $charset = '';

        foreach ($http_response_header ?? [] as $header) {
            if (stripos($header, 'content-type:') === 0) {
                $type = strtolower(trim(substr($header, 13)));

                // A picture, a download... is no page.
                if ($type !== '' && !str_contains($type, 'html')) {
                    return null;
                }

                if (preg_match('/charset=["\']?([\w\-]+)/', $type, $m)) {
                    $charset = $m[1];
                }
            }
        }

        if ($charset === '' && preg_match('/<meta[^>]+charset=["\']?([\w\-]+)/i', substr($body, 0, 4096), $m)) {
            $charset = $m[1];
        }

        if ($charset !== '' && strcasecmp($charset, 'utf-8') !== 0 && strcasecmp($charset, 'utf8') !== 0) {
            try {
                $body = mb_convert_encoding($body, 'UTF-8', $charset);
            } catch (Throwable $e) {
                // Unknown character set: the text is read as it is.
            }
        }

        return $body;
    }

    /**
     * Is the address one of the public internet? Not the own computer, not a private or reserved network, and not an IPv6
     * address which leads to IPv4 (mapped, NAT64, 6to4, Teredo), where the IPv4 address could be one of the own network.
     *
     * @param   string  $ip  The address.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected static function isPublicAddress(string $ip): bool
    {
        $bytes = @inet_pton($ip);

        if ($bytes === false) {
            return false;
        }

        if (\strlen($bytes) === 16) {
            // IPv4 written as IPv6 (::ffff:a.b.c.d): the IPv4 address decides
            if (str_starts_with($bytes, str_repeat("\0", 10) . "\xff\xff")) {
                return static::isPublicAddress(inet_ntop(substr($bytes, 12)));
            }

            $hex = bin2hex($bytes);

            // NAT64 (64:ff9b::/96, 64:ff9b:1::/48), 6to4 (2002::/16), Teredo (2001::/32), documentation (2001:db8::/32)
            if (
                str_starts_with($hex, '0064ff9b0000000000000000') || str_starts_with($hex, '0064ff9b0001')
                || str_starts_with($hex, '2002') || str_starts_with($hex, '20010000') || str_starts_with($hex, '20010db8')
            ) {
                return false;
            }
        } else {
            $long = ip2long($ip);

            // The shared address space of the providers (100.64.0.0/10), the IETF protocol assignments (192.0.0.0/24) and
            // the networks for benchmarks (198.18.0.0/15), which filter_var lets through.
            if (
                ($long & 0xFFC00000) === (100 << 24 | 64 << 16) || ($long & 0xFFFFFF00) === (192 << 24)
                || ($long & 0xFFFE0000) === (198 << 24 | 18 << 16)
            ) {
                return false;
            }
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    /**
     * The address of the page with the checked IP address in place of the name, and the Host header with the name.
     *
     * @param   array   $parts  The parts of the address of the page (parse_url).
     * @param   string  $ip     The checked IP address of the name.
     *
     * @return  array  The address to read, the Host header.
     *
     * @since   Kunena 8.0
     */
    protected static function pinned(array $parts, string $ip): array
    {
        $port = isset($parts['port']) ? ':' . (int) $parts['port'] : '';
        $ip   = str_contains($ip, ':') ? '[' . $ip . ']' : $ip;
        $url  = strtolower($parts['scheme']) . '://' . $ip . $port . ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');

        return [$url, trim($parts['host'], '[]') . $port];
    }

    /**
     * The card of a link: from the cache, or from the page (at most a few per request).
     *
     * @param   string  $url  The address of the page (http or https).
     *
     * @return  array|null  title, description, image and site; null when there is no card.
     *
     * @since   Kunena 8.0
     */
    public static function get(string $url): ?array
    {
        $url = trim($url);

        if (\strlen($url) > 2000 || self::isBlocked($url)) {
            return null;
        }

        $hash  = sha1($url);
        $cache = self::cache();
        $row   = $cache ? json_decode((string) $cache->get($hash), true) : false;

        if (\is_array($row) && \array_key_exists('card', $row)) {
            $age = time() - (int) $row['time'];

            if ($row['card'] !== null ? $age < self::TTL : $age < self::FAILED_TTL) {
                return $row['card'];
            }
        }

        if (self::$fetches >= self::MAX_FETCHES) {
            return null;
        }

        self::$fetches++;

        $card = null;
        $html = self::fetchPage($url, 2);

        if ($html !== null) {
            $card = self::parse($html, $url);
        }

        if ($cache) {
            // The cache keeps text.
            $cache->store(json_encode(['card' => $card, 'time' => time()]), $hash);
        }

        return $card;
    }

    /**
     * Does the site (or the host of the link) not get a card: the forum itself, and the sites of the list in the configuration.
     *
     * @param   string  $url  The address.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isBlocked(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($host === '') {
            return true;
        }

        if ($host === strtolower((string) parse_url(Uri::root(), PHP_URL_HOST))) {
            return true;
        }

        $list = (string) KunenaConfig::getInstance()->linkPreviewBlock;

        foreach (preg_split('/[\s,;]+/', strtolower($list), -1, PREG_SPLIT_NO_EMPTY) as $domain) {
            $domain = ltrim($domain, '.');

            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The card of a page.
     *
     * @param   string  $html  The page as UTF-8 text.
     * @param   string  $url   The address of the page.
     *
     * @return  array|null
     *
     * @since   Kunena 8.0
     */
    protected static function parse(string $html, string $url): ?array
    {
        $meta = [];

        try {
            $previous = libxml_use_internal_errors(true);
            $dom      = new DOMDocument();
            $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_NONET);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            foreach ($dom->getElementsByTagName('meta') as $tag) {
                $key     = strtolower($tag->getAttribute('property') ?: $tag->getAttribute('name'));
                $content = trim($tag->getAttribute('content'));

                if ($key !== '' && $content !== '' && !isset($meta[$key])) {
                    $meta[$key] = $content;
                }
            }

            $titleTag = $dom->getElementsByTagName('title')->item(0);
        } catch (Exception $e) {
            return null;
        }

        $title = $meta['og:title'] ?? $meta['twitter:title'] ?? ($titleTag ? $titleTag->textContent : '');
        $title = self::clean($title, 150);

        if ($title === '') {
            return null;
        }

        $host = (string) parse_url($url, PHP_URL_HOST);

        return [
            'title'       => $title,
            'description' => self::clean($meta['og:description'] ?? $meta['twitter:description'] ?? $meta['description'] ?? '', 220),
            'image'       => self::absolute($meta['og:image'] ?? $meta['twitter:image'] ?? '', $url),
            'site'        => self::clean($meta['og:site_name'] ?? $host, 60),
        ];
    }
}
