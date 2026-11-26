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

use Kunena\Forum\Libraries\BBCode\KunenaMediaEmbed;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A video of YouTube is shown for every address of it: the page of the video, the short link (youtu.be), a Short, a live stream
 * and the address of the player. [video] showed nothing for all but the page of the video, and the automatic player of a pasted
 * address left a Short and a live stream as plain links.
 */
class KunenaMediaEmbedYoutubeTest extends UnitTestCase
{
    private const ID = 'dQw4w9WgXcQ';

    public static function addresses(): array
    {
        return [
            'page of the video'      => ['https://www.youtube.com/watch?v=' . self::ID, true],
            'page with more values'  => ['https://www.youtube.com/watch?feature=share&v=' . self::ID . '&t=10', true],
            'short link'             => ['https://youtu.be/' . self::ID, true],
            'short link with a time' => ['https://youtu.be/' . self::ID . '?t=42', true],
            'Short'                  => ['https://www.youtube.com/shorts/' . self::ID, true],
            'live stream'            => ['https://youtube.com/live/' . self::ID, true],
            'player'                 => ['https://www.youtube-nocookie.com/embed/' . self::ID, true],
            'mobile site'            => ['https://m.youtube.com/watch?v=' . self::ID, true],
            'a channel'              => ['https://www.youtube.com/@somebody', false],
            'the home page'          => ['https://www.youtube.com/', false],
            'an id with a quote'     => ['https://youtu.be/abc"onload="x', false],
            'another site'           => ['https://notyoutube.example/watch?v=' . self::ID, false],
        ];
    }

    #[DataProvider('addresses')]
    public function testTheIdOfTheVideoIsReadFromTheAddress(string $url, bool $video): void
    {
        $parts = parse_url($url);

        $this->assertSame(
            $video ? self::ID : null,
            KunenaMediaEmbed::youtube($parts['host'], $parts['path'] ?? '', $parts['query'] ?? '')
        );
    }

    /**
     * The tag [video] and the automatic player of a pasted address both read the address with it. (They need the template of the
     * forum to write the player: their code is read.)
     */
    public function testTheVideoTagAndTheAutomaticPlayerReadTheAddressWithIt(): void
    {
        $source = Files::read('src/libraries/kunena/src/BBCode/KunenaBBCode.php');

        $this->assertSame(2, substr_count($source, 'KunenaMediaEmbed::youtube('));
        $this->assertStringContainsString("case 'youtu':", $source, 'The short link (youtu.be) is a video of YouTube.');
        $this->assertStringContainsString('$content     = $vid_code;', $source, 'The tag shows the video which was read.');
    }
}
