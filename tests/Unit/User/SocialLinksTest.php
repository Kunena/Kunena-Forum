<?php

/**
 * Kunena Component
 *
 * Regression test: a social network link may only point to a safe address. A value like javascript://%0aalert(1)
 * in a network with the bare ##VALUE## template used to become the href of the link.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Kunena\Forum\Libraries\User\KunenaUserSocials;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SocialTextStub
{
    public static function _($text)
    {
        return (string) $text;
    }
}

class SocialLinksTest extends TestCase
{
    private const LAYOUT = __DIR__ . '/../../../src/site/template/aurelia/layouts/user/item/socials/default.php';

    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        if (!class_exists('Joomla\CMS\Language\Text', false)) {
            class_alias(SocialTextStub::class, 'Joomla\CMS\Language\Text');
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaUserSocials.php';
    }

    public function testTheFoundPayloadIsRefused(): void
    {
        // The bare template is the one of Google, Yahoo, Weibo, Apple... in the default networks.
        $this->assertNull(KunenaUserSocials::getSafeHref('##VALUE##', 'javascript://%0aalert(1)'));
        $this->assertNull(KunenaUserSocials::getSafeHref('##VALUE##', 'JAVASCRIPT://%0aalert(1)'));
        $this->assertNull(KunenaUserSocials::getSafeHref('##VALUE##', '   javascript://%0aalert(1)'));
    }

    /**
     * Whatever comes out must not be something a browser runs or opens as a document. Some inputs are turned into a
     * harmless relative address by parse_url (a control character becomes an underscore, the scheme is dropped), others
     * are refused; both are fine.
     */
    #[DataProvider('dangerous')]
    public function testDangerousValuesNeverBecomeAnExecutableLink(string $value): void
    {
        $href = KunenaUserSocials::getSafeHref('##VALUE##', $value);

        if ($href === null) {
            $this->addToAssertionCount(1);

            return;
        }

        $seen = strtolower(preg_replace('/[\x00-\x20\x7f]+/', '', $href));

        $this->assertDoesNotMatchRegularExpression('/^(javascript|data|vbscript|file|blob):/', $seen, $value);
        $this->assertTrue(KunenaUserSocials::isSafeUrl($href), $value);
    }

    public static function dangerous(): array
    {
        return [
            'the payload which was found'  => ['javascript://%0aalert(1)'],
            'javascript'                   => ['javascript://x%0aalert(document.domain)'],
            'upper case'                   => ['JAVASCRIPT://%0aalert(1)'],
            'tab inside the scheme'        => ["java\tscript://%0aalert(1)"],
            'line break inside the scheme' => ["java\nscript://%0aalert(1)"],
            'leading spaces'               => ["   javascript://%0aalert(1)"],
            'leading control character'    => ["\x01javascript://%0aalert(1)"],
            'data'                         => ['data://text/html;base64,PHNjcmlwdD4='],
            'vbscript'                     => ['vbscript://x'],
            'file'                         => ['file:///etc/passwd'],
            'blob'                         => ['blob://x'],
        ];
    }

    public function testDangerousSchemeAlsoFailsInsideAPrefixedTemplateOnlyWhenItIsTheStart(): void
    {
        // With a prefix the user value can not choose the scheme.
        $href = KunenaUserSocials::getSafeHref('https://x.com/##VALUE##', 'javascript://%0aalert(1)');

        $this->assertNotNull($href);
        $this->assertStringStartsWith('https://x.com/', $href);
    }

    #[DataProvider('safe')]
    public function testSafeValuesStillWork(string $template, string $value, string $expected): void
    {
        $this->assertSame($expected, KunenaUserSocials::getSafeHref($template, $value));
    }

    public static function safe(): array
    {
        return [
            'user name with prefix'   => ['https://www.github.com/##VALUE##', 'octocat', 'https://www.github.com/octocat'],
            'url with prefix'         => ['https://www.instagram.com/##VALUE##/', 'https://www.instagram.com/someone', 'https://www.instagram.com/someone/'],
            'skype'                   => ['skype:##VALUE##', 'live.name', 'skype:live.name'],
            'blog host'               => ['https://##VALUE##.blogspot.com/', 'myblog', 'https://myblog.blogspot.com/'],
            'plain name, bare'        => ['##VALUE##', 'someone', 'someone'],
            'https in the template'   => ['https://t.me/##VALUE##', '@name', 'https://t.me/@name'],
        ];
    }

    public function testEmptyResultIsNotALink(): void
    {
        $this->assertNull(KunenaUserSocials::getSafeHref('', ''));
        $this->assertNull(KunenaUserSocials::getSafeHref('##VALUE##', '///'));
    }

    #[DataProvider('schemes')]
    public function testIsSafeUrl(string $url, bool $safe): void
    {
        $this->assertSame($safe, KunenaUserSocials::isSafeUrl($url), $url);
    }

    public static function schemes(): array
    {
        return [
            ['https://example.com', true],
            ['HTTP://example.com', true],
            ['mailto:someone@example.com', true],
            ['tel:+3112345', true],
            ['skype:live.name', true],
            ['/relative/path', true],
            ['relative', true],
            ['alert(1)//:80', true],
            ['javascript:alert(1)', false],
            ['jAvAsCrIpT:alert(1)', false],
            ["java\0script:alert(1)", false],
            ['data:text/html,x', false],
            ['vbscript:x', false],
            ['ftp://example.com', false],
        ];
    }

    private function render(array $socials): string
    {
        $view = new class ($socials) {
            public $showAll = false;

            public function __construct(public array $socials)
            {
            }
        };

        $layout = self::LAYOUT;

        ob_start();
        (function () use ($layout) {
            include $layout;
        })->call($view);

        return ob_get_clean();
    }

    private function social(string $value, string $url = '##VALUE##'): object
    {
        return (object) ['fa' => 'fa-brands fa-google', 'value' => $value, 'url' => $url, 'title' => 'Google'];
    }

    public function testLayoutDoesNotRenderADangerousLink(): void
    {
        $html = $this->render([$this->social('javascript://%0aalert(1)')]);

        $this->assertStringNotContainsString('javascript', strtolower($html));
        $this->assertStringNotContainsString('<a ', $html);
    }

    public function testLayoutKeepsTheSafeLinksNextToADroppedOne(): void
    {
        $html = $this->render([
            $this->social('javascript://%0aalert(1)'),
            $this->social('octocat', 'https://www.github.com/##VALUE##'),
        ]);

        $this->assertSame(1, substr_count($html, '<a '));
        $this->assertStringContainsString('href="https://www.github.com/octocat"', $html);
    }

    public function testLayoutStillEscapesTheAddress(): void
    {
        $html = $this->render([$this->social('a"onmouseover="x', 'https://example.com/##VALUE##')]);

        $this->assertStringNotContainsString('onmouseover="x"', $html);
    }

    public function testLayoutUsesTheSafeBuilder(): void
    {
        $this->assertStringContainsString('KunenaUserSocials::getSafeHref(', file_get_contents(self::LAYOUT));
    }
}
