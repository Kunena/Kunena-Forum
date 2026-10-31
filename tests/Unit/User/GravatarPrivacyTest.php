<?php

/**
 * Kunena Component
 *
 * Regression test: the Gravatar address of an avatar contains the MD5 of the email address, in the html of every page that
 * shows the avatar and for every visitor. The hash is checked against guessed or leaked addresses, so a member who hides
 * the email address must get the default image, without the hash.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Kunena\Forum\Plugin\Kunena\Gravatar\Helper\GravatarPrivacy;
use Pedrollo\GravatarLib\Gravatar;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GravatarPrivacyTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once self::SRC . '/plugins/plg_kunena_gravatar/src/Helper/GravatarPrivacy.php';
        require_once self::SRC . '/libraries/kunena/External/Emberlabs/Gravatar.php';
    }

    #[DataProvider('settings')]
    public function testOnlyAPublicAddressIsGivenToGravatar(mixed $hideEmail, bool $given): void
    {
        $email = 'Jane.Doe@example.com';

        $this->assertSame($given ? $email : '', GravatarPrivacy::emailForHash($email, $hideEmail), var_export($hideEmail, true));
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
            'registered only as a string'        => ['2', false],
            'an unknown number'                  => [7, false],
            'a negative number'                  => [-1, false],
            'false'                              => [false, false],
            'true'                               => [true, false],
            'text'                               => ['no', false],
        ];
    }

    public function testAnEmptyAddressStaysEmpty(): void
    {
        foreach (['', '   ', null, false] as $email) {
            $this->assertSame('', GravatarPrivacy::emailForHash($email, 0), var_export($email, true));
        }
    }

    public function testTheAddressIsTrimmed(): void
    {
        $this->assertSame('a@b.nl', GravatarPrivacy::emailForHash("  a@b.nl \n", 0));
    }

    private function url(string $email, mixed $hideEmail, bool $secure = true): string
    {
        $gravatar = new Gravatar();
        $gravatar->setAvatarSize(64);
        $gravatar->setDefaultImage('identicon');
        $gravatar->setMaxRating('g');
        $secure ? $gravatar->enableSecureImages() : $gravatar->disableSecureImages();

        return $gravatar->buildGravatarURL(GravatarPrivacy::emailForHash($email, $hideEmail));
    }

    public function testMemberWhoHidesTheAddressHasNoHashInTheAddressOfTheAvatar(): void
    {
        $email = 'Jane.Doe@example.com';

        foreach ([$email, strtolower($email), strtoupper($email), " $email "] as $variant) {
            $hash = md5(strtolower(trim($variant)));

            foreach ([1, '1', 2, '2'] as $hide) {
                $url = $this->url($variant, $hide);

                $this->assertStringNotContainsString($hash, $url, "hash of $variant visible with hideEmail=" . var_export($hide, true));
                $this->assertStringNotContainsString('example.com', $url);
                $this->assertStringNotContainsString('Jane', $url);
            }
        }
    }

    public function testHiddenAddressGivesTheDefaultImage(): void
    {
        $url = $this->url('jane@example.com', 1);

        // The all-zero hash with "force default" (f=y) is how Gravatar is asked for the default image.
        $this->assertStringContainsString(str_repeat('0', 32), $url);
        $this->assertStringContainsString('f=y', $url);
        $this->assertStringContainsString('d=identicon', $url, 'The default image of the plugin is kept.');
        $this->assertStringContainsString('s=64', $url);
    }

    public function testAllHiddenMembersLookTheSame(): void
    {
        // Otherwise the address of one hidden member would still be told apart from another.
        $this->assertSame($this->url('one@example.com', 1), $this->url('two@example.org', 2));
    }

    public function testMemberWhoShowsTheAddressStillGetsTheirGravatar(): void
    {
        $email = 'Jane.Doe@example.com';
        $url   = $this->url($email, 0);

        $this->assertStringContainsString(md5('jane.doe@example.com'), $url, 'Gravatar is lower case and trimmed.');
        $this->assertStringNotContainsString('f=y', $url);
        $this->assertStringStartsWith('https://', $url);

        $this->assertStringStartsWith('http://', $this->url($email, null, false));
        $this->assertStringContainsString(md5('jane.doe@example.com'), $this->url($email, null, false), 'also when the setting was never saved');
    }

    // --- the plugin ---

    public function testThePluginAsksThePrivacyRuleBeforeBuildingTheAddress(): void
    {
        $source = file_get_contents(self::SRC . '/plugins/plg_kunena_gravatar/src/Helper/KunenaAvatarGravatar.php');

        $this->assertStringContainsString('buildGravatarURL(GravatarPrivacy::emailForHash($user->email, $user->hideEmail))', $source);
        $this->assertDoesNotMatchRegularExpression('/buildGravatarURL\(\s*\$user->email\s*\)/', $source, 'The address must not go to Gravatar directly.');
    }

    public function testNothingElseSendsAnEmailToGravatar(): void
    {
        $found = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::SRC, \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($path, '/External/')) {
                continue;
            }

            foreach (file($path) as $number => $line) {
                if (preg_match('/buildGravatarURL\(|->get\(\s*\$[a-z]*email|gravatar\.com\/avatar/i', $line) && !str_contains($line, 'GravatarPrivacy::emailForHash')) {
                    $found[] = substr($path, strpos($path, '/src/') + 1) . ':' . ($number + 1) . ' ' . trim($line);
                }
            }
        }

        $this->assertSame([], $found, "A Gravatar address is built without the privacy rule:\n" . implode("\n", $found));
    }
}
