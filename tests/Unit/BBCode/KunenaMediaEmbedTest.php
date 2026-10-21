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

namespace Kunena\Forum\Tests\Unit\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaMediaEmbed;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The Spotify and Deezer players of a link (the auto embed of the music services).
 */
class KunenaMediaEmbedTest extends TestCase
{
    public static function spotifyLinks(): array
    {
        return [
            'a track'              => ['open.spotify.com', '/track/4cOdK2wGLETKBW3PvgPWqT', 'https://open.spotify.com/embed/track/4cOdK2wGLETKBW3PvgPWqT', 152],
            'an episode'           => ['open.spotify.com', '/episode/0Q86acNRm6V9GYx55SXKwf', 'https://open.spotify.com/embed/episode/0Q86acNRm6V9GYx55SXKwf', 152],
            'an album'             => ['open.spotify.com', '/album/6dVIqQ8qmQ5GBnJ9shOYGE', 'https://open.spotify.com/embed/album/6dVIqQ8qmQ5GBnJ9shOYGE', 352],
            'a playlist'           => ['open.spotify.com', '/playlist/37i9dQZF1DXcBWIGoYBM5M', 'https://open.spotify.com/embed/playlist/37i9dQZF1DXcBWIGoYBM5M', 352],
            'an artist'            => ['open.spotify.com', '/artist/0TnOYISbd1XYRBk9myaseg', 'https://open.spotify.com/embed/artist/0TnOYISbd1XYRBk9myaseg', 352],
            'a show'               => ['open.spotify.com', '/show/2MAi0BvDc6GTFvKFPXnkCL', 'https://open.spotify.com/embed/show/2MAi0BvDc6GTFvKFPXnkCL', 352],
            'a link with a locale' => ['open.spotify.com', '/intl-nl/track/4cOdK2wGLETKBW3PvgPWqT', 'https://open.spotify.com/embed/track/4cOdK2wGLETKBW3PvgPWqT', 152],
        ];
    }

    #[DataProvider('spotifyLinks')]
    public function testSpotifyPlayer(string $host, string $path, string $source, int $height): void
    {
        $html = KunenaMediaEmbed::spotify($host, $path);

        $this->assertNotNull($html);
        $this->assertStringContainsString('src="' . $source . '"', $html);
        $this->assertStringContainsString('height="' . $height . '"', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
    }

    public static function notSpotify(): array
    {
        return [
            'another host'        => ['example.com', '/track/4cOdK2wGLETKBW3PvgPWqT'],
            'a lookalike host'    => ['open.spotify.com.evil.com', '/track/4cOdK2wGLETKBW3PvgPWqT'],
            'a subdomain'         => ['evil.open.spotify.com', '/track/4cOdK2wGLETKBW3PvgPWqT'],
            'the home page'       => ['open.spotify.com', '/'],
            'an unknown kind'     => ['open.spotify.com', '/user/someone'],
            'no id'               => ['open.spotify.com', '/track/'],
            'an empty path'       => ['open.spotify.com', ''],
        ];
    }

    #[DataProvider('notSpotify')]
    public function testNoSpotifyPlayerForOtherLinks(string $host, string $path): void
    {
        $this->assertNull(KunenaMediaEmbed::spotify($host, $path));
    }

    public function testNothingOfTheLinkButTheIdGoesInTheSpotifyPlayer(): void
    {
        $html = KunenaMediaEmbed::spotify('open.spotify.com', '/track/abc123"><script>alert(1)</script>');

        $this->assertNotNull($html);
        $this->assertStringContainsString('/embed/track/abc123"', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public static function deezerLinks(): array
    {
        return [
            'a track'                 => ['www.deezer.com', '/track/3135556', 'https://widget.deezer.com/widget/auto/track/3135556', 150],
            'a track without www'     => ['deezer.com', '/track/3135556', 'https://widget.deezer.com/widget/auto/track/3135556', 150],
            'an album'                => ['www.deezer.com', '/album/302127', 'https://widget.deezer.com/widget/auto/album/302127', 300],
            'a playlist'              => ['www.deezer.com', '/playlist/908622995', 'https://widget.deezer.com/widget/auto/playlist/908622995', 300],
            'an episode'              => ['www.deezer.com', '/episode/12345', 'https://widget.deezer.com/widget/auto/episode/12345', 150],
            'a link with a language'  => ['www.deezer.com', '/en/track/3135556', 'https://widget.deezer.com/widget/auto/track/3135556', 150],
            'a language and country'  => ['www.deezer.com', '/pt-br/album/302127', 'https://widget.deezer.com/widget/auto/album/302127', 300],
        ];
    }

    #[DataProvider('deezerLinks')]
    public function testDeezerPlayer(string $host, string $path, string $source, int $height): void
    {
        $html = KunenaMediaEmbed::deezer($host, $path);

        $this->assertNotNull($html);
        $this->assertStringContainsString('src="' . $source . '"', $html);
        $this->assertStringContainsString('height="' . $height . '"', $html);
    }

    public static function notDeezer(): array
    {
        return [
            'another host'     => ['example.com', '/track/3135556'],
            'a lookalike host' => ['deezer.com.evil.com', '/track/3135556'],
            'a letter id'      => ['www.deezer.com', '/track/abc'],
            'an artist page'   => ['www.deezer.com', '/artist/27'],
            'the home page'    => ['www.deezer.com', '/'],
        ];
    }

    #[DataProvider('notDeezer')]
    public function testNoDeezerPlayerForOtherLinks(string $host, string $path): void
    {
        $this->assertNull(KunenaMediaEmbed::deezer($host, $path));
    }

    public function testSpotifyAndDeezerDoNotAnswerForEachOther(): void
    {
        $this->assertNull(KunenaMediaEmbed::spotify('www.deezer.com', '/track/3135556'));
        $this->assertNull(KunenaMediaEmbed::deezer('open.spotify.com', '/track/4cOdK2wGLETKBW3PvgPWqT'));
    }
}
