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
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * The card of a link (title, description and image of the page, from its Open Graph data), kept in the Joomla cache.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaLinkPreview
{
    /**
     * The card is kept for a week, a page without a card (or which did not answer) is asked again after a day.
     */
    protected const TTL           = 7 * 86400;
    protected const FAILED_TTL    = 86400;
    
    /**
     * Pages which are read in one request: the cards of the cache are free, a new page waits for the other site.
     */
    protected const MAX_FETCHES   = 3;
    
    /**
     * @var     integer
     * @since   Kunena 7.2
     */
    protected static $fetches = 0;
    
    /**
     * The card of a link: from the cache, or from the page (at most a few per request).
     *
     * @param   string  $url  The address of the page (http or https).
     *
     * @return  array|null  title, description, image and site; null when there is no card.
     *
     * @since   Kunena 7.2
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
     * @since   Kunena 7.2
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
     * Is the address one of the internet? Private, loopback, link-local (cloud metadata is 169.254.169.254), carrier-grade NAT,
     * benchmarking, documentation, multicast and reserved ranges are not. IPv6 is never used (see planRequest()).
     *
     * @param   string  $ip  An IPv4 address.
     *
     * @return  boolean
     *
     * @since   Kunena 7.2
     */
    public static function isPublicIp(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        $long = ip2long($ip);

        // Ranges which the filter flags of PHP do not all know (they differ by version).
        foreach (['100.64.0.0/10', '192.0.0.0/24', '192.0.2.0/24', '192.88.99.0/24', '198.18.0.0/15', '198.51.100.0/24', '203.0.113.0/24', '224.0.0.0/3'] as $range) {
            [$net, $bits] = explode('/', $range);
            $mask         = -1 << (32 - (int) $bits);

            if (($long & $mask) === (ip2long($net) & $mask)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Where to connect to. The name is resolved here, once, every answer has to be a public IPv4 address, and the request goes to
     * that address: when the connection looked the name up again, the other site could answer with an internal address the
     * second time (DNS rebinding) after the check was passed with a public one.
     *
     * @param   string         $url      The address.
     * @param   callable|null  $resolve  Name to the list of IPv4 addresses (gethostbynamel by default).
     *
     * @return  array|null  url (with the IP), host (for the Host header and the certificate) and ip; null when the page is not to be read.
     *
     * @since   Kunena 7.2
     */
    public static function planRequest(string $url, ?callable $resolve = null): ?array
    {
        $parts  = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');

        // Only fetch web pages (not file:, php:, ftp: ...), of a host, without a login in the address.
        if (!$parts || empty($parts['host']) || !\in_array($scheme, ['http', 'https'], true) || isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        // Web ports only: a link must not be a way to knock on other services of the internet.
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        if (!\in_array($port, [80, 443, 8080, 8443], true)) {
            return null;
        }

        $host = $parts['host'];
        $ips  = ($resolve ?? 'gethostbynamel')($host);

        if (!$ips) {
            return null;
        }

        foreach ($ips as $ip) {
            if (!self::isPublicIp((string) $ip)) {
                return null;
            }
        }

        $ip       = (string) $ips[0];
        $pinned   = $scheme . '://' . $ip . ':' . $port . ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');
        $hostName = $host . (isset($parts['port']) ? ':' . $parts['port'] : '');

        return ['url' => $pinned, 'host' => $host, 'host_header' => $hostName, 'ip' => $ip, 'scheme' => $scheme];
    }

    /**
     * The page as UTF-8 text. Only web pages are read, never an address of the own network (the checked address is the one connected to), there are no redirects (they
     * could lead to an internal address), and only the start of the page is taken.
     *
     * @param   string   $url      The address.
     * @param   integer  $timeout  Seconds to wait for the other site.
     *
     * @return  string|null  Null when the page can not be read.
     *
     * @since   Kunena 7.2
     */
    public static function fetchPage(string $url, int $timeout = 3): ?string
    {
        $plan = self::planRequest($url);

        if ($plan === null) {
            return null;
        }

        // Many sites refuse a request which does not say who asks. The connection goes to the address which was checked; the
        // name is only sent as Host and used to check the certificate.
        $agent   = 'Mozilla/5.0 (compatible; KunenaLinkPreview/7.2; +' . Uri::root() . ')';
        $options = ['http' => ['timeout' => $timeout, 'follow_location' => 0, 'ignore_errors' => true, 'header' => "Host: " . $plan['host_header'] . "\r\nUser-Agent: $agent\r\nAccept: text/html,application/xhtml+xml\r\nConnection: close\r\n"]];

        if ($plan['scheme'] === 'https') {
            $options['ssl'] = ['peer_name' => $plan['host'], 'SNI_enabled' => true, 'SNI_server_name' => $plan['host'], 'verify_peer' => true, 'verify_peer_name' => true];
        }

        $context = stream_context_create($options);
        $body    = @file_get_contents($plan['url'], false, $context, 0, 262144);

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
            } catch (\Throwable $e) {
                // Unknown character set: the text is read as it is.
            }
        }
        
        return $body;
    }
    
    /**
     * The card of a page.
     *
     * @param   string  $html  The page as UTF-8 text.
     * @param   string  $url   The address of the page.
     *
     * @return  array|null
     *
     * @since   Kunena 7.2
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
    
    /**
     * Plain text of the other site: no markup, one line, not too long (it is escaped when it is shown).
     *
     * @param   string   $text    The text.
     * @param   integer  $length  The most characters.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    protected static function clean(string $text, int $length): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($text)) ?? '');
        
        return mb_strimwidth($text, 0, $length, '…', 'UTF-8');
    }
    
    /**
     * The address of the picture of the page as an absolute http(s) address, or an empty text.
     *
     * @param   string  $image  The address in the page.
     * @param   string  $page   The address of the page.
     *
     * @return  string
     *
     * @since   Kunena 7.2
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
     * @return  \Joomla\CMS\Cache\Cache|null
     *
     * @since   Kunena 7.2
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
}
