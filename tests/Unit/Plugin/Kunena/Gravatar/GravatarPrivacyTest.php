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

use FilesystemIterator;
use Kunena\Forum\Plugin\Kunena\Gravatar\Helper\GravatarPrivacy;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Pedrollo\GravatarLib\Gravatar;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Gravatar and a hidden e-mail address (Kunena 7.1): the address of a Gravatar has the MD5 of the e-mail address in it, in the html
 * of every page with the avatar. That hash is checked against guessed or leaked addresses, so a member who hides the address gets
 * the default image instead.
 */
class GravatarPrivacyTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once Files::path('src/plugins/plg_kunena_gravatar/src/Helper/GravatarPrivacy.php');
        require_once Files::path('src/libraries/kunena/External/Emberlabs/Gravatar.php');
    }

    public static function settings(): array
    {
        return [
            'show (0)'                           => [0, true],
            'show as a string from the database' => ['0', true],
            'never set (null)'                   => [null, true],
            'never set (empty string)'           => ['', true],
            'hide (1)'                           => [1, false],
            'hide as a string'                   => ['1', false],
            'registered users only (2)'          => [2, false],
            'an unknown number'                  => [7, false],
            'false'                              => [false, false],
            'true'                               => [true, false],
            'text'                               => ['no', false],
        ];
    }

    #[DataProvider('settings')]
    public function testOnlyAPublicAddressIsGivenToGravatar(mixed $hideEmail, bool $given): void
    {
        $email = 'Jane.Doe@example.com';

        $this->assertSame($given ? $email : '', GravatarPrivacy::emailForHash($email, $hideEmail));
    }

    public function testAnEmptyAddressStaysEmptyAndAnAddressIsTrimmed(): void
    {
        foreach (['', '   ', null, false] as $email) {
            $this->assertSame('', GravatarPrivacy::emailForHash($email, 0));
        }

        $this->assertSame('a@b.nl', GravatarPrivacy::emailForHash("  a@b.nl \n", 0));
    }

    private function url(string $email, mixed $hideEmail): string
    {
        $gravatar = new Gravatar();
        $gravatar->setAvatarSize(64);
        $gravatar->setDefaultImage('identicon');
        $gravatar->setMaxRating('g');
        $gravatar->enableSecureImages();

        return $gravatar->buildGravatarURL(GravatarPrivacy::emailForHash($email, $hideEmail));
    }

    public function testAHiddenAddressHasNoHashInTheAvatar(): void
    {
        $url = $this->url('Jane.Doe@example.com', 1);

        $this->assertStringNotContainsString(md5('jane.doe@example.com'), $url);
        $this->assertStringContainsString(str_repeat('0', 32), $url);
        $this->assertStringContainsString('d=identicon', $url);

        // All the hidden members look the same: one could not be told apart from another.
        $this->assertSame($url, $this->url('two@example.org', 2));
    }

    public function testAMemberWhoShowsTheAddressStillGetsTheirGravatar(): void
    {
        $this->assertStringContainsString(md5('jane.doe@example.com'), $this->url('Jane.Doe@example.com', 0));
    }

    public function testNothingSendsAnAddressToGravatarWithoutTheRule(): void
    {
        $found = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Files::path('src'), FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($path, '/External/')) {
                continue;
            }

            foreach (file($path) as $number => $line) {
                if (preg_match('/buildGravatarURL\(|gravatar\.com\/avatar/i', $line) && !str_contains($line, 'GravatarPrivacy::emailForHash')) {
                    $found[] = substr($path, strpos($path, '/src/') + 1) . ':' . ($number + 1) . ' ' . trim($line);
                }
            }
        }

        $this->assertSame([], $found, implode("\n", $found));
    }
}
