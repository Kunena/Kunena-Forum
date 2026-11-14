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

namespace Kunena\Tests\Unit\Libraries\Kunena\Integration;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * A user without an avatar with "Default avatar type = Icontype" (issue #9436): the person icon of the icon set of the template
 * (Font Awesome or SVG) at the size of the avatar, instead of the picture person.svg or an SVG of 1em.
 */
class KunenaAvatarTest extends UnitTestCase
{
    private const string ROOT = 'https://example.com/forum/';

    private const string PERSON = self::ROOT . 'media/kunena/core/svg/person.svg';

    private const string UPLOADED = self::ROOT . 'media/kunena/avatars/users/avatar62.png';

    /**
     * The values which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        // The avatar, the template and the language need the framework of Joomla.
        $this->requireJoomla();

        $site = $this->createStub(CMSApplicationInterface::class);
        $site->method('isClient')->willReturn(false);
        $site->method('getInput')->willReturn(new Input([]));

        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnCallback(
            static fn ($string) => $string === 'COM_KUNENA_LIB_AVATAR_TITLE' ? "%s's Avatar" : $string
        );

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'templates'   => (new ReflectionProperty(KunenaTemplate::class, '_instances'))->getValue(),
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'base'        => (new ReflectionProperty(Uri::class, 'base'))->getValue(),
            'root'        => (new ReflectionProperty(Uri::class, 'root'))->getValue(),
        ];

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, self::bare(KunenaConfig::class, ['config' => ['template' => 'aurelia']]));
        Factory::$application = $site;
        Factory::$language    = $language;

        // The forum is at https://example.com/forum/.
        (new ReflectionProperty(Uri::class, 'base'))->setValue(null, ['prefix' => 'https://example.com', 'path' => '/forum']);
        (new ReflectionProperty(Uri::class, 'root'))->setValue(null, ['prefix' => 'https://example.com', 'path' => '/forum']);
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, $this->saved['templates']);
        Factory::$application = $this->saved['application'];
        Factory::$language    = $this->saved['language'];
        (new ReflectionProperty(Uri::class, 'base'))->setValue(null, $this->saved['base']);
        (new ReflectionProperty(Uri::class, 'root'))->setValue(null, $this->saved['root']);
    }

    /**
     * The template Aurelia with the icon set given and the size "thumb" of 36 x 48.
     */
    private function template(string $topicicontype): void
    {
        $template         = $this->createStub(KunenaTemplate::class);
        $template->params = new Registry(['topicicontype' => $topicicontype, 'avatarSizeXThumb' => 36, 'avatarSizeYThumb' => 48]);

        (new ReflectionProperty(KunenaTemplate::class, '_instances'))->setValue(null, ['aurelia' => $template]);
    }

    /**
     * The link of the avatar of Jane Doe, whose avatar integration gives the address $url.
     */
    private function link(string $topicicontype, string $url, string $class, $sizex, $sizey, string $online = ''): string
    {
        $this->template($topicicontype);

        $user = $this->createStub(KunenaUser::class);
        $user->method('getName')->willReturn('Jane Doe');

        $avatar = new class ($url) extends KunenaAvatar {
            public function __construct(private string $url)
            {
            }

            protected function _getURL(KunenaUser $user, int $sizex, int $sizey): string
            {
                return $this->url;
            }
        };

        return $online === '' ? $avatar->getLink($user, $class, $sizex, $sizey) : $avatar->getLink($user, $class, $sizex, $sizey, $online);
    }

    /**
     * The SVG of the person is read from the media of the Joomla installation (JPATH_SITE).
     */
    private function requirePersonSvg(): void
    {
        if (!is_file(JPATH_SITE . '/media/kunena/core/svg/person.svg')) {
            $this->markTestSkipped('The Joomla installation of KUNENA_TEST_JOOMLA has no media/kunena/core/svg/person.svg.');
        }
    }

    public function testFontAwesomeGivesTheUserIconAtTheSizeOfTheAvatar(): void
    {
        $html = $this->link('fa', self::PERSON, 'kavatar', 20, 20);

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('<i class="fas fa-user-circle" style="display: block; width: 100%; height: 100%" aria-hidden="true"></i>', $html);
        $this->assertStringContainsString('style="display: inline-block; width: 20px; height: 20px; line-height: 1; font-size: 20px"', $html);
        $this->assertStringStartsWith('<span class="kavatar"', $html);
        $this->assertStringContainsString('title="Jane Doe\'s Avatar"', $html);
        $this->assertStringContainsString('role="img" aria-label="Jane Doe\'s Avatar"', $html);
    }

    public function testFontAwesomeWithANamedSizeUsesTheHeightOfTheTemplate(): void
    {
        $html = $this->link('fa', self::PERSON, 'none', 'thumb', 90);

        $this->assertStringContainsString('style="display: inline-block; width: 36px; height: 48px; line-height: 1; font-size: 48px"', $html);
        $this->assertStringStartsWith('<span class="kavatar"', $html);
    }

    public function testSvgGivesThePersonIconAtANumericSize(): void
    {
        $this->requirePersonSvg();

        $html = $this->link('svg', self::PERSON, 'rounded-circle', 20, 20);

        $this->assertStringNotContainsString('<img', $html);
        $this->assertMatchesRegularExpression('/<svg width="20" height="20" style="display: block; width: 100%; height: 100%"[^>]*class="bi bi-person"/', $html);
        $this->assertStringContainsString('style="display: inline-block; width: 20px; height: 20px; line-height: 1"', $html);
        $this->assertStringNotContainsString('1em', $html);
        $this->assertStringStartsWith('<span class="rounded-circle"', $html);
        $this->assertStringContainsString('title="Jane Doe\'s Avatar"', $html);
        $this->assertStringContainsString('role="img" aria-label="Jane Doe\'s Avatar"', $html);
    }

    public function testSvgGivesThePersonIconAtTheNamedSizeOfTheTemplate(): void
    {
        $this->requirePersonSvg();

        $html = $this->link('svg', self::PERSON, 'kavatar', 'thumb', 90);

        $this->assertMatchesRegularExpression('/<svg width="36" height="48"[^>]*>/', $html);
        $this->assertStringContainsString('style="display: inline-block; width: 36px; height: 48px; line-height: 1"', $html);
        $this->assertStringNotContainsString('1em', $html);
    }

    public function testTheStatusIsAClassOfTheIcon(): void
    {
        $this->assertStringStartsWith('<span class="rounded-circle green status-green"', $this->link('fa', self::PERSON, 'rounded-circle green', 20, 20, 'green'));
        $this->assertStringStartsWith('<span class="rounded-circle status-grey"', $this->link('svg', self::PERSON, 'rounded-circle ', 20, 20, 'grey'));
        $this->assertStringNotContainsString('status-', $this->link('fa', self::PERSON, 'rounded-circle', 20, 20));
        $this->assertStringNotContainsString('status-', $this->link('svg', self::PERSON, 'rounded-circle', 20, 20));
    }

    public function testImageIconSetKeepsThePictureOfThePerson(): void
    {
        $html = $this->link('image', self::PERSON, 'kavatar', 20, 20);

        $this->assertSame(
            '<img loading=lazy class="kavatar" src="' . self::PERSON . '" width="20" height="20"' . "\n\t\t\t  alt=\"Jane Doe's Avatar\" />",
            $html
        );
    }

    public function testAnUploadedAvatarKeepsThePicture(): void
    {
        $this->assertSame(
            '<img loading=lazy class="kavatar" src="' . self::UPLOADED . '" width="20" height="20"' . "\n\t\t\t  alt=\"Jane Doe's Avatar\" />",
            $this->link('fa', self::UPLOADED, 'kavatar', 20, 20)
        );
        $this->assertSame(
            '<span class="kavatar" data-bs-toggle="tooltip" title="Jane Doe\'s Avatar">' . "\n\t\t\t\t"
                . '<img alt="" loading=lazy src="' . self::UPLOADED . '" width="36" height="48"></span>',
            $this->link('svg', self::UPLOADED, 'kavatar', 'thumb', 90)
        );
    }
}
