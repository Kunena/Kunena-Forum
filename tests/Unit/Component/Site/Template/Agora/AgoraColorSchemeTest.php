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

namespace Kunena\Tests\Unit\Component\Site\Template\Agora;

use Joomla\Registry\Registry;
use Kunena\Tests\Unit\UnitTestCase;
use KunenaTemplateAgora;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The colour scheme of Agora: the attributes of #kunena and, for the scheme auto, the script which takes the colours of the site
 * template around the forum (its data-bs-theme, else its background), not the colours of the device.
 */
class AgoraColorSchemeTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();

        if (!\defined('KPATH_SITE')) {
            \define('KPATH_SITE', \dirname(__DIR__, 6) . '/src/site');
        }

        require_once \dirname(__DIR__, 6) . '/src/site/template/agora/template.php';
    }

    /**
     * Agora with the colour scheme of its options.
     */
    private static function agora(string $scheme): KunenaTemplateAgora
    {
        return self::bare(KunenaTemplateAgora::class, ['params' => new Registry(['colorScheme' => $scheme])]);
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function attributes(): array
    {
        return [
            'auto is light without JavaScript' => ['auto', ' data-agora-scheme="auto" data-bs-theme="light"'],
            'light'                            => ['light', ' data-agora-scheme="light" data-bs-theme="light"'],
            'dark'                             => ['dark', ' data-agora-scheme="dark" data-bs-theme="dark"'],
            'an unknown value is auto'         => ['purple', ' data-agora-scheme="auto" data-bs-theme="light"'],
        ];
    }

    #[DataProvider('attributes')]
    public function testSchemeAttributes(string $scheme, string $expected): void
    {
        $this->assertSame($expected, self::agora($scheme)->getSchemeAttributes());
    }

    public function testNoScriptForLightAndDark(): void
    {
        $this->assertSame('', self::agora('light')->getSchemeScript());
        $this->assertSame('', self::agora('dark')->getSchemeScript());
    }

    public function testAutoScriptFollowsTheSiteTemplateNotTheDevice(): void
    {
        $script = self::agora('auto')->getSchemeScript();

        $this->assertStringStartsWith('<script>', $script);
        $this->assertStringEndsWith('</script>', $script);
        $this->assertStringContainsString('document.currentScript.parentElement', $script);
        $this->assertStringContainsString('window.kunenaAgoraTheme', $script);
        $this->assertStringContainsString('node = forum.parentElement; node; node = node.parentElement', $script);
        $this->assertStringContainsString('getAttribute("data-bs-theme")', $script);
        $this->assertStringContainsString('getComputedStyle(node).backgroundColor', $script);
        $this->assertStringContainsString('lum < 0.5 ? "dark" : "light"', $script);
        $this->assertStringContainsString('return "light"', $script);
        $this->assertStringContainsString('catch (e) {}', $script);
        $this->assertStringNotContainsString('prefers-color-scheme', $script);
    }
}
