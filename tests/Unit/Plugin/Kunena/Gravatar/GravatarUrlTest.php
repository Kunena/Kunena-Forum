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

namespace Kunena\Tests\Unit\Plugin\Kunena\Gravatar;

use Kunena\Forum\Plugin\Kunena\Gravatar\Helper\GravatarUrl;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The address of a Gravatar image, made by Kunena itself (Kunena 8.0, instead of the library of 2011): a SHA-256 hash, https, any
 * size Gravatar gives (the library threw above 512, so the larger images of a srcset failed) and the default images of today.
 */
class GravatarUrlTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once Files::path('src/plugins/plg_kunena_gravatar/src/Helper/GravatarUrl.php');
    }

    public function testTheAddressOfAMember(): void
    {
        $this->assertSame(
            'https://www.gravatar.com/avatar/' . hash('sha256', 'jane.doe@example.com') . '?s=80&r=g&d=identicon',
            GravatarUrl::build('  Jane.Doe@Example.com ', 80, 'identicon')
        );
    }

    public function testWithoutAnAddressTheDefaultImageIsForced(): void
    {
        $this->assertSame('https://www.gravatar.com/avatar/' . str_repeat('0', 64) . '?s=36&r=g&f=y', GravatarUrl::build('', 36));
    }

    public static function sizes(): array
    {
        return [
            'a size of the layout'         => [90, '90'],
            'three times a profile (600)'  => [600, '600'],
            'the largest'                  => [2048, '2048'],
            'larger: the largest'          => [5000, '2048'],
            'zero: the smallest'           => [0, '1'],
        ];
    }

    #[DataProvider('sizes')]
    public function testEverySizeOfGravatar(int $size, string $expected): void
    {
        $this->assertStringContainsString('?s=' . $expected . '&', GravatarUrl::build('a@b.nl', $size));
    }

    public static function defaults(): array
    {
        return [
            'a built-in image'            => ['identicon', 'identicon'],
            'a newer built-in image'      => ['robohash', 'robohash'],
            'in capitals'                 => ['Retro', 'retro'],
            'the image of the site'       => ['https://example.org/img/a.png?v=2&x=1', rawurlencode('https://example.org/img/a.png?v=2&x=1')],
            'an address with a space'     => ['https://example.org/img/a b.png', ''],
            'empty: the one of Gravatar'  => ['', ''],
            'unknown: the one of Gravatar' => ['smiley', ''],
            'not an address of the web'   => ['javascript:alert(1)', ''],
            'a relative address'          => ['/images/a.png', ''],
        ];
    }

    #[DataProvider('defaults')]
    public function testTheDefaultImage(string $setting, string $expected): void
    {
        $this->assertSame($expected, GravatarUrl::defaultImage($setting));

        $url = GravatarUrl::build('a@b.nl', 40, $setting);
        $expected === '' ? $this->assertStringNotContainsString('&d=', $url) : $this->assertStringEndsWith('&d=' . $expected, $url);
    }

    public function testTheRating(): void
    {
        $this->assertStringContainsString('&r=pg', GravatarUrl::build('a@b.nl', 40, '', 'PG'));
        $this->assertStringContainsString('&r=g', GravatarUrl::build('a@b.nl', 40, '', 'nsfw'));
    }

    public function testTheLibraryIsGone(): void
    {
        $this->assertDirectoryDoesNotExist(Files::path('src/libraries/kunena/External/Emberlabs'));
        $this->assertStringNotContainsString('GravatarLib', Files::read('src/libraries/kunena/composer.json'));
        $this->assertStringNotContainsString('GravatarLib', Files::read('src/libraries/kunena/External/composer/autoload_psr4.php'));
    }
}
