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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Kunena\Forum\Libraries\User\KunenaUserSocials;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The links of the social websites of a profile:
 * - a value which is the address copied from the website: only its path was put in the address of the website, which doubled the
 *   part of the address in it (https://www.linkedin.com/in/in/joe) or lost a name in the host (https://.blogspot.com/);
 * - Bluesky: the profiles are at https://bsky.app/profile/<handle>, the link was https://bsky.app/<handle>, a page which is not.
 */
class KunenaUserSocialsHrefTest extends UnitTestCase
{
    public static function values(): array
    {
        return [
            'a name'                         => ['https://www.linkedin.com/in/##VALUE##', 'johndoe', 'https://www.linkedin.com/in/johndoe'],
            'the address of the profile'     => ['https://www.linkedin.com/in/##VALUE##', 'https://www.linkedin.com/in/johndoe', 'https://www.linkedin.com/in/johndoe'],
            'the address with a slash'       => ['https://www.linkedin.com/in/##VALUE##', 'https://linkedin.com/in/johndoe/', 'https://www.linkedin.com/in/johndoe'],
            'a company'                      => ['https://www.linkedin.com/company/##VALUE##', 'https://www.linkedin.com/company/acme', 'https://www.linkedin.com/company/acme'],
            'Reddit'                         => ['https://www.reddit.com/user/##VALUE##', 'https://www.reddit.com/user/joe', 'https://www.reddit.com/user/joe'],
            'a name in the host'             => ['https://##VALUE##.blogspot.com/', 'https://joe.blogspot.com/', 'https://joe.blogspot.com/'],
            'a name for the host'            => ['https://##VALUE##.blogspot.com/', 'joe', 'https://joe.blogspot.com/'],
            'Threads with an @'              => ['https://www.threads.net/@##VALUE##', '@joe', 'https://www.threads.net/@joe'],
            'another address of the website' => ['https://x.com/##VALUE##', 'https://twitter.com/joe', 'https://x.com/joe'],
            'a YouTube handle keeps its @'   => ['https://www.youtube.com/##VALUE##', 'https://www.youtube.com/@joe', 'https://www.youtube.com/@joe'],
        ];
    }

    #[DataProvider('values')]
    public function testTheLinkOfTheValue(string $template, string $value, string $href): void
    {
        $this->assertSame($href, KunenaUserSocials::href($template, $value));
    }

    public function testTheLayoutUsesIt(): void
    {
        $layout = Files::read('src/site/template/aurelia/layouts/user/item/socials/default.php');

        $this->assertStringContainsString('KunenaUserSocials::href((string) $social->url, (string) $social->value)', $layout);
        $this->assertStringNotContainsString('parse_url(', $layout);
    }

    public function testBlueskyLinksToTheProfile(): void
    {
        $providers = json_decode(KunenaUserSocials::getDefaultSocialsJson());

        $this->assertSame('https://bsky.app/profile/##VALUE##', $providers->bluesky_app->url);
    }
}
