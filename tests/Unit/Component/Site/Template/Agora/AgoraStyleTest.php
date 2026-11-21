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

use FilesystemIterator;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Path\KunenaPath;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use KunenaTemplateAgora;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * The styles and the layouts of Agora, two options apart. The style (the look) and the layout (the HTML) are attributes of
 * #kunena; Midnight is always dark; the accent of a style stays until the options choose another one; the folder of a layout is
 * searched before the one of Agora; each style and each layout has its option and its style sheet, and the rules of a layout only
 * use the tokens, so every layout goes with every style.
 */
class AgoraStyleTest extends UnitTestCase
{
    private const AGORA = 'src/site/template/agora';

    protected function setUp(): void
    {
        $this->requireJoomla();

        if (!\defined('KPATH_SITE')) {
            \define('KPATH_SITE', \dirname(__DIR__, 6) . '/src/site');
        }

        require_once \dirname(__DIR__, 6) . '/src/site/template/agora/template.php';
    }

    /**
     * Agora with these options.
     *
     * @param   array<string,string>  $params  the options
     */
    private static function agora(array $params): KunenaTemplateAgora
    {
        $agora       = self::bare(KunenaTemplateAgora::class, ['params' => new Registry($params)]);
        $agora->name = 'agora';

        return $agora;
    }

    /**
     * The values of the list option $name of config.xml.
     *
     * @return string[]
     */
    private static function optionValues(string $name): array
    {
        $options = simplexml_load_string(Files::read(self::AGORA . '/config/config.xml'))->xpath('//field[@name="' . $name . '"]/option');

        return array_map(static fn ($option) => (string) $option['value'], $options);
    }

    /**
     * @return array<string,array{array<string,string>,string,string}>
     */
    public static function choices(): array
    {
        return [
            'no option is default'       => [[], 'default', 'default'],
            'a style and a layout'       => [['style' => 'conversation', 'layoutSet' => 'chat'], 'conversation', 'chat'],
            'any style with any layout'  => [['style' => 'midnight', 'layoutSet' => 'cards'], 'midnight', 'cards'],
            'unknown values are default' => [['style' => 'neon', 'layoutSet' => 'compact'], 'default', 'default'],
        ];
    }

    #[DataProvider('choices')]
    public function testStyleAndLayoutAreChosenApart(array $params, string $style, string $layout): void
    {
        $agora = self::agora($params);

        $this->assertSame($style, $agora->getStyle());
        $this->assertSame($layout, $agora->getLayoutSet());
    }

    public function testTheStyleAndTheLayoutAreAttributesOfTheForum(): void
    {
        $this->assertSame(
            ' data-agora-style="soft" data-agora-layout="dense" data-agora-scheme="light" data-bs-theme="light"',
            self::agora(['style' => 'soft', 'layoutSet' => 'dense', 'colorScheme' => 'light'])->getSchemeAttributes()
        );
    }

    public function testMidnightIsAlwaysDark(): void
    {
        $agora = self::agora(['style' => 'midnight', 'colorScheme' => 'light']);

        $this->assertSame('dark', $agora->getColorScheme());
        $this->assertSame(
            ' data-agora-style="midnight" data-agora-layout="default" data-agora-scheme="dark" data-bs-theme="dark"',
            $agora->getSchemeAttributes()
        );
        $this->assertSame('', self::agora(['style' => 'midnight', 'colorScheme' => 'auto'])->getSchemeScript());
    }

    /**
     * @return array<string,array{array<string,string>,string}>
     */
    public static function accents(): array
    {
        $rule = '#kunena.layout.agora[data-agora-style][data-agora-scheme] { --agora-accent: %s; }';

        return [
            'default style, default accent' => [['accentColor' => '#3d5afe'], \sprintf($rule, '#3d5afe')],
            'default style, own accent'     => [['accentColor' => '#ff0066'], \sprintf($rule, '#ff0066')],
            'a style keeps its accent'      => [['style' => 'tonal', 'accentColor' => '#3D5AFE'], ''],
            'a style with an own accent'    => [['style' => 'tonal', 'accentColor' => '#ff0066'], \sprintf($rule, '#ff0066')],
            'a layout does not matter'      => [['layoutSet' => 'app', 'accentColor' => '#3d5afe'], \sprintf($rule, '#3d5afe')],
            'not a colour'                  => [['accentColor' => 'red; } body { display: none'], ''],
        ];
    }

    #[DataProvider('accents')]
    public function testAccentDeclaration(array $params, string $expected): void
    {
        $this->assertSame($expected, self::agora($params)->getAccentDeclaration());
    }

    public function testTheFolderOfTheLayoutIsSearchedBeforeAgora(): void
    {
        $path  = KunenaPath::clean('/layouts/topic/row');
        $paths = [
            '/root/system' . $path,
            '/site/html/com_kunena' . $path,
            '/root/aurelia' . $path,
            '/site/html/com_kunena' . $path,
            '/root/agora' . $path,
            '/site/html/com_kunena' . $path,
        ];

        // The last folder is searched first: the site template, then the layout, then Agora. The style does not change the paths.
        $this->assertSame(
            array_merge(\array_slice($paths, 0, 5), ['/root/agora/layoutsets/dense' . $path], \array_slice($paths, 5)),
            self::agora(['style' => 'soft', 'layoutSet' => 'dense'])->addLayoutSetPath($paths, 'layouts/topic/row', '/root')
        );
        $this->assertSame($paths, self::agora(['style' => 'compact'])->addLayoutSetPath($paths, 'layouts/topic/row', '/root'));
    }

    public function testEachFileOfALayoutReplacesALayoutOfAgoraOrAurelia(): void
    {
        $root = str_replace('\\', '/', Files::path(self::AGORA . '/layoutsets'));

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
            $relative        = substr(str_replace('\\', '/', $file->getPathname()), \strlen($root) + 1);
            [$layout, $path] = explode('/', $relative, 2);

            $this->assertContains($layout, KunenaTemplateAgora::LAYOUTS, "The folder layoutsets/$layout is not a layout of KunenaTemplateAgora.");
            $this->assertMatchesRegularExpression('#^(layouts|pages)/.+\.php$#', $path, "layoutsets/$relative is not a layout or a page.");
            $this->assertTrue(
                is_file(Files::path(self::AGORA . "/$path")) || is_file(Files::path("src/site/template/aurelia/$path")),
                "The file layoutsets/$relative has no layout of Agora or Aurelia to replace (a wrong name?)."
            );
        }
    }

    public function testEachStyleHasAnOptionAndAStyleSheetWithItsLookOnly(): void
    {
        $this->assertSame(KunenaTemplateAgora::STYLES, self::optionValues('style'), 'The option style does not list the styles of KunenaTemplateAgora.');

        $scss = Files::read(self::AGORA . '/assets/scss/agora.scss');

        foreach (array_diff(KunenaTemplateAgora::STYLES, ['default']) as $style) {
            $file = self::AGORA . "/assets/scss/styles/_{$style}.scss";

            $this->assertFileExists(Files::path($file));
            $this->assertStringContainsString("@import \"styles/{$style}\";", $scss);
            $this->assertStringContainsString("#kunena.layout.agora[data-agora-style=\"{$style}\"]", Files::read($file));
            $this->assertStringNotContainsString('data-agora-layout', Files::read($file), "The style $style has rules of a layout.");
        }
    }

    public function testEachLayoutHasAnOptionAndAStyleSheetWithTokensOnly(): void
    {
        $this->assertSame(KunenaTemplateAgora::LAYOUTS, self::optionValues('layoutSet'), 'The option layoutSet does not list the layouts of KunenaTemplateAgora.');

        $scss = Files::read(self::AGORA . '/assets/scss/agora.scss');

        foreach (array_diff(KunenaTemplateAgora::LAYOUTS, ['default']) as $layout) {
            $file = self::AGORA . "/assets/scss/layoutsets/_{$layout}.scss";

            $this->assertFileExists(Files::path($file));
            $this->assertDirectoryExists(Files::path(self::AGORA . "/layoutsets/{$layout}"));
            $this->assertStringContainsString("@import \"layoutsets/{$layout}\";", $scss);

            $rules = Files::read($file);

            $this->assertStringContainsString("#kunena.layout.agora[data-agora-layout=\"{$layout}\"]", $rules);
            $this->assertStringNotContainsString('data-agora-style', $rules, "The layout $layout depends on a style.");
            $this->assertDoesNotMatchRegularExpression('/#[0-9a-f]{3,8}\b|\brgba?\(/i', $rules, "The layout $layout has its own colours: use the tokens.");
        }
    }
}
