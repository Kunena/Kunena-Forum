<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\BBCode;

use Closure;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Throwable;

\defined('_JEXEC') or die();

/**
 * The players of a link to a music service, and the frame of a Bluesky post: only the known services, and only addresses of the
 * form which the service gives for its own players, so nothing of the link itself is put in the player.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaMediaEmbed
{
    /**
     * The Deezer player of a link to a track, album, playlist, episode or show.
     *
     * @param   string  $host  The host of the link.
     * @param   string  $path  The path of the link.
     *
     * @return  string|null  The html of the player, null when the link has none.
     *
     * @since   Kunena 8.0
     */
    public static function deezer(string $host, string $path): ?string
    {
        if (!\in_array($host, ['deezer.com', 'www.deezer.com'], true) || !preg_match('#^/(?:[a-z]{2}(?:-[a-z]{2})?/)?(track|album|playlist|episode|show)/(\d+)#', $path, $match)) {
            return null;
        }

        $height = $match[1] === 'track' || $match[1] === 'episode' ? 150 : 300;

        return '<iframe style="border: 0" width="100%" height="' . $height . '" src="https://widget.deezer.com/widget/auto/' . $match[1] . '/' . $match[2]
            . '" allow="encrypted-media; clipboard-write" loading="lazy"></iframe><br />';
    }

    /**
     * The Spotify player of a link to a track, album, playlist, artist, episode or show.
     *
     * @param   string  $host  The host of the link.
     * @param   string  $path  The path of the link.
     *
     * @return  string|null  The html of the player, null when the link has none.
     *
     * @since   Kunena 8.0
     */
    public static function spotify(string $host, string $path): ?string
    {
        if ($host !== 'open.spotify.com' || !preg_match('#^/(?:intl-[\w-]+/)?(track|album|playlist|artist|episode|show)/([A-Za-z0-9]+)#', $path, $match)) {
            return null;
        }

        $height = \in_array($match[1], ['track', 'episode'], true) ? 152 : 352;

        return '<iframe style="border-radius: 12px; border: 0" width="100%" height="' . $height . '" src="https://open.spotify.com/embed/' . $match[1] . '/' . $match[2]
            . '" allow="encrypted-media" loading="lazy"></iframe><br />';
    }

    /**
     * Finds the DID of a Bluesky handle, for the tests (a function of the handle which gives the DID or null).
     *
     * @var    Closure|null
     * @since  Kunena 8.0
     */
    public static ?Closure $blueskyResolver = null;

    /**
     * The Bluesky post of a link to it (https://bsky.app/profile/<handle or DID>/post/<key>), in the frame of embed.bsky.app.
     * The frame needs the DID of the author: the DID of a handle is asked once from the public API of Bluesky and kept for a week.
     * The frame gets an id, which it sends back with its height (media/kunena/core/js/bluesky-embed.js sets it).
     *
     * @param   string  $host  The host of the link.
     * @param   string  $path  The path of the link.
     *
     * @return  string|null  The frame, or null when the link is no post of Bluesky or its author is not found.
     *
     * @since   Kunena 8.0
     */
    public static function bluesky(string $host, string $path): ?string
    {
        if (
            !\in_array(strtolower($host), ['bsky.app', 'www.bsky.app'], true)
            || !preg_match('#^/profile/([A-Za-z0-9.:%_-]{1,253})/post/([A-Za-z0-9._~:-]{1,512})/?$#', $path, $match)
        ) {
            return null;
        }

        $did = str_starts_with($match[1], 'did:') ? $match[1] : self::blueskyDid(strtolower($match[1]));

        if ($did === null || !self::isDid($did)) {
            return null;
        }

        $id = bin2hex(random_bytes(6));

        return '<iframe class="kunena-bluesky" data-bluesky-id="' . $id . '" src="https://embed.bsky.app/embed/' . $did . '/app.bsky.feed.post/'
            . $match[2] . '?id=' . $id . '" title="Bluesky" width="100%" height="320" style="border: 0; max-width: 600px; display: block"'
            . ' loading="lazy"></iframe>';
    }

    /**
     * Is it a DID of the methods Bluesky uses (did:plc, did:web)? Only such a value is put in the address of the frame.
     *
     * @param   string  $did  The value.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected static function isDid(string $did): bool
    {
        return (bool) preg_match('#^did:(?:plc:[a-z2-7]{24}|web:[A-Za-z0-9.-]{1,253}(?:%3A\d{1,5})?)$#', $did);
    }

    /**
     * The DID of a handle: from the cache of a week, else from the public API of Bluesky (an unknown handle is kept as unknown too,
     * so that a post with a wrong link does not ask the API on every view).
     *
     * @param   string  $handle  The handle (name.bsky.social).
     *
     * @return  string|null
     *
     * @since   Kunena 8.0
     */
    protected static function blueskyDid(string $handle): ?string
    {
        if (!preg_match('#^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z][a-z0-9-]{0,62}$#', $handle)) {
            return null;
        }

        if (self::$blueskyResolver) {
            return (self::$blueskyResolver)($handle);
        }

        $cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
            ->createCacheController('output', ['defaultgroup' => 'com_kunena_bluesky', 'caching' => true, 'lifetime' => 10080]);
        $key   = 'did.' . md5($handle);
        $did   = $cache->get($key);

        if (\is_string($did)) {
            return $did !== '' ? $did : null;
        }

        $did = '';

        try {
            $response = HttpFactory::getHttp()->get(
                'https://public.api.bsky.app/xrpc/com.atproto.identity.resolveHandle?handle=' . rawurlencode($handle),
                [],
                5
            );

            if ($response->getStatusCode() === 200) {
                $data = json_decode((string) $response->getBody(), true);
                $did  = \is_string($data['did'] ?? null) && self::isDid($data['did']) ? $data['did'] : '';
            } elseif ($response->getStatusCode() !== 400) {
                // The service did not answer as it should (it answers 400 for an unknown handle): ask again next time.
                return null;
            }
        } catch (Throwable $e) {
            return null;
        }

        $cache->store($did, $key);

        return $did !== '' ? $did : null;
    }
}
