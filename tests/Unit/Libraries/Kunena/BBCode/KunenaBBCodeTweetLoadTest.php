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

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Forum\Site\Controllers\MiscController;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;
use RuntimeException;

/**
 * A tweet which the forum has not read from X yet is read after the page (a note: "use AJAX instead"): two requests to X of up to
 * ten seconds each held up the page. The page has a link to the tweet with the address which loads it; that address has a key made
 * with the secret of the site, so that nobody makes the forum ask X for other tweets.
 */
class KunenaBBCodeTweetLoadTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        class_exists(KunenaBBCode::class);

        $application = $this->createStub(CMSApplication::class);
        $application->method('get')->willReturnCallback(static fn ($name, $default = null) => $name === 'secret' ? 'the-secret' : $default);
        $application->method('getDocument')->willReturn($this->createStub(Document::class));
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        $root = new ReflectionProperty(Uri::class, 'root');

        $this->saved          = ['application' => Factory::$application, 'language' => Factory::$language, 'root' => $root->getValue()];
        Factory::$application = $application;
        Factory::$language    = $language;

        // The address of the site, which the command line has not.
        $root->setValue(null, ['prefix' => 'https://example.com', 'path' => '/forum']);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            (new ReflectionProperty(Uri::class, 'root'))->setValue(null, $this->saved['root']);
        }
    }

    public function testTheKeyBelongsToTheIdAndTheSecretOfTheSite(): void
    {
        $key = KunenaBBCodeLibrary::tweetKey('1234567890');

        $this->assertSame(hash_hmac('sha256', 'kunena-tweet:1234567890', 'the-secret'), $key);
        $this->assertNotSame($key, KunenaBBCodeLibrary::tweetKey('1234567891'));
    }

    public function testThePageHasALinkWithTheAddressWhichLoadsTheTweet(): void
    {
        $html = self::invoke(self::bare(KunenaBBCodeLibrary::class), 'tweetPlaceholder', '1234567890');

        $this->assertStringContainsString('class="kunena-tweet-load"', $html);
        $this->assertStringContainsString('data-tweet-url="https://example.com/forum/index.php?option=com_kunena&amp;view=misc&amp;', $html);
        $this->assertStringContainsString('href="https://x.com/i/status/1234567890"', $html);
        $this->assertStringContainsString(
            'task=tweet&amp;format=raw&amp;id=1234567890&amp;key=' . KunenaBBCodeLibrary::tweetKey('1234567890') . '"',
            $html
        );
    }

    public static function refused(): array
    {
        return [
            'a wrong key'        => ['1234567890', 'nope'],
            'the key of another' => ['1234567891', null],
            'no id'              => ['', null],
            'an id with letters' => ['12ab', null],
        ];
    }

    #[DataProvider('refused')]
    public function testTheTaskRefusesAnAddressWhichThePageDidNotGive(string $id, ?string $key): void
    {
        $controller = self::bare(MiscController::class, ['input' => new Input(['id' => $id, 'key' => $key ?? KunenaBBCodeLibrary::tweetKey('1234567890')])]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(403);

        $controller->tweet();
    }
}
