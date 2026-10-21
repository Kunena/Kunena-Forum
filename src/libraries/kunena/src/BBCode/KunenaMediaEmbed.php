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

\defined('_JEXEC') or die();

/**
 * The players of a link to a music service: only the known services, and only addresses of the form which the service gives
 * for its own players, so nothing of the link itself is put in the player.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaMediaEmbed
{
    /**
     * The Spotify player of a link to a track, album, playlist, artist, episode or show.
     *
     * @param   string  $host  The host of the link.
     * @param   string  $path  The path of the link.
     *
     * @return  string|null  The html of the player, null when the link has none.
     *
     * @since   Kunena 7.2
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
     * The Deezer player of a link to a track, album, playlist, episode or show.
     *
     * @param   string  $host  The host of the link.
     * @param   string  $path  The path of the link.
     *
     * @return  string|null  The html of the player, null when the link has none.
     *
     * @since   Kunena 7.2
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
}
