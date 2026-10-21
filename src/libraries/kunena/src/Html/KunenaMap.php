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

use Exception;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Http\HttpFactory;
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * The maps of the forum: the provider (Google Maps or OpenStreetMap), the links to a location and the coordinates of an
 * address for the maps of OpenStreetMap (Nominatim, the answer is kept in the database).
 *
 * @since   Kunena 7.2
 */
abstract class KunenaMap
{
    /**
     * The number of addresses which are looked up in one request: a page with many maps does not wait for many requests.
     *
     * @since   Kunena 7.2
     */
    protected const MAX_LOOKUPS = 3;

    /**
     * How long an address which was not found is kept (seconds), before it is looked up again.
     *
     * @since   Kunena 7.2
     */
    protected const NOT_FOUND_TTL = 604800;

    /**
     * @var     int
     * @since   Kunena 7.2
     */
    protected static $lookups = 0;

    /**
     * The provider of the maps: google or osm. Automatic uses Google Maps when an API key is filled in.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function provider(): string
    {
        $config = KunenaConfig::getInstance();
        $value  = (string) ($config->mapProvider ?? 'auto');

        if ($value === 'google' || $value === 'osm') {
            return $value;
        }

        return !empty($config->googleMapApiKey) ? 'google' : 'osm';
    }

    /**
     * The address of a page with the location on a map.
     *
     * @param   string  $address  The address or the place.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function linkUrl(string $address): string
    {
        if (self::provider() === 'google') {
            return 'https://maps.google.com/?q=' . urlencode($address);
        }

        return 'https://www.openstreetmap.org/search?query=' . urlencode($address);
    }

    /**
     * The address of a page with the coordinates on a map.
     *
     * @param   float  $latitude   The latitude.
     * @param   float  $longitude  The longitude.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function coordinatesUrl(float $latitude, float $longitude): string
    {
        if (self::provider() === 'google') {
            return 'https://maps.google.com/?q=' . $latitude . ',' . $longitude;
        }

        return self::osmPageUrl($latitude, $longitude, 10);
    }

    /**
     * The address of the embedded map of OpenStreetMap for a location.
     *
     * @param   float  $latitude   The latitude.
     * @param   float  $longitude  The longitude.
     * @param   int    $zoom       The zoom level (1 to 18).
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function osmEmbedUrl(float $latitude, float $longitude, int $zoom = 10): string
    {
        $zoom   = max(1, min(18, $zoom));
        $lonSpan = 360 / (2 ** $zoom) * 2.4;
        $latSpan = $lonSpan / 2.4;
        $bbox   = [$longitude - $lonSpan / 2, $latitude - $latSpan / 2, $longitude + $lonSpan / 2, $latitude + $latSpan / 2];

        return 'https://www.openstreetmap.org/export/embed.html?bbox=' . implode(',', array_map(static fn ($value) => rtrim(rtrim(number_format($value, 6, '.', ''), '0'), '.'), $bbox))
            . '&layer=mapnik&marker=' . rtrim(rtrim(number_format($latitude, 6, '.', ''), '0'), '.') . ',' . rtrim(rtrim(number_format($longitude, 6, '.', ''), '0'), '.');
    }

    /**
     * The address of the page of OpenStreetMap with the location (a larger map).
     *
     * @param   float  $latitude   The latitude.
     * @param   float  $longitude  The longitude.
     * @param   int    $zoom       The zoom level.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function osmPageUrl(float $latitude, float $longitude, int $zoom = 10): string
    {
        return 'https://www.openstreetmap.org/?mlat=' . $latitude . '&mlon=' . $longitude . '#map=' . max(1, min(18, $zoom)) . '/' . $latitude . '/' . $longitude;
    }

    /**
     * The coordinates of an address: from the cache, or from the search service (at most a few per request).
     *
     * @param   string  $address  The address or the place.
     *
     * @return  float[]|null  [latitude, longitude], null when the address is not known.
     *
     * @since   Kunena 7.2
     */
    public static function geocode(string $address): ?array
    {
        $address = trim(preg_replace('/\s+/', ' ', $address));

        if ($address === '' || mb_strlen($address) > 255) {
            return null;
        }

        $hash  = sha1(mb_strtolower($address));
        $cache = self::cache();
        $row   = $cache ? json_decode((string) $cache->get($hash), true) : false;

        if (\is_array($row) && \array_key_exists('coords', $row)) {
            if ($row['coords'] !== null) {
                return $row['coords'];
            }

            // Not found: it is looked up again after some time.
            if ((int) $row['time'] > time() - self::NOT_FOUND_TTL) {
                return null;
            }
        }

        if (self::$lookups >= self::MAX_LOOKUPS) {
            return null;
        }

        self::$lookups++;

        $found = self::lookup($address);

        // The service did not answer: nothing is kept, it is looked up again later.
        if ($found === false) {
            return null;
        }

        if ($cache) {
            // The cache keeps text.
            $cache->store(json_encode(['coords' => $found, 'time' => time()]), $hash);
        }

        return $found;
    }

    /**
     * The Joomla cache with the coordinates of the addresses: it is kept for a long time (also when the cache of the site is
     * switched off), and it is emptied with the cache of the site.
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
                ['defaultgroup' => 'com_kunena_geocode', 'caching' => true, 'lifetime' => 60 * 24 * 365]
            );

            return $controller->cache;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Ask the search service (Nominatim) for the coordinates of an address.
     *
     * @param   string  $address  The address or the place.
     *
     * @return  float[]|null|false  The coordinates, null when the service does not know the address, false when it did not answer.
     *
     * @since   Kunena 7.2
     */
    protected static function lookup(string $address)
    {
        $config = KunenaConfig::getInstance();
        $url    = trim((string) ($config->geocoderUrl ?? ''));
        $url    = $url !== '' ? $url : 'https://nominatim.openstreetmap.org/search';

        // The usage policy of the public service asks for a user agent which tells who asks.
        $contact = filter_var((string) $config->email, FILTER_VALIDATE_EMAIL) ? ' ' . $config->email : '';
        $agent   = 'Kunena Forum (' . Uri::root() . ')' . $contact;

        try {
            $http     = (new HttpFactory())->getHttp([], ['curl', 'socket']);
            $response = $http->get(
                $url . (str_contains($url, '?') ? '&' : '?') . http_build_query(['q' => $address, 'format' => 'jsonv2', 'limit' => 1]),
                ['User-Agent' => $agent, 'Accept' => 'application/json'],
                4
            );

            if ($response->getStatusCode() !== 200) {
                return false;
            }

            $list = json_decode((string) $response->getBody(), true);

            if (!\is_array($list)) {
                return false;
            }

            if (isset($list[0]['lat'], $list[0]['lon']) && is_numeric($list[0]['lat']) && is_numeric($list[0]['lon'])) {
                return [(float) $list[0]['lat'], (float) $list[0]['lon']];
            }

            return null;
        } catch (Exception $e) {
            // The service does not answer: the map is not shown, the link is.
            return false;
        }
    }
}
