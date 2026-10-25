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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia\Config;

use FilesystemIterator;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SimpleXMLElement;

/**
 * The options of the template Aurelia in detail: the texts of the choices, the numbers and the colours, the place where each option
 * is read in the code, and the code which reads an option that the template does not have.
 */
class TemplateOptionDetailsTest extends UnitTestCase
{
    private const FILE = 'src/site/template/aurelia/config/config.xml';

    /**
     * Options which no code reads by their name (found by the first run of this test). They are listed here so that a new option
     * without code fails, and an option which gets code is taken out of the list.
     */
    private const KNOWN_UNUSED = ['profileIconset', 'editorIconset'];

    /**
     * Template parameters which the code reads (with a start value of its own, or without one) but which config.xml of the template
     * does not have: they can not be changed in the administration. They are listed so that a new read of an undefined option
     * fails; an option which gets a field in config.xml is taken out of the list.
     */
    private const KNOWN_UNDEFINED = [
        'maxPersonalText',
        'disableEmoticons',
        'enableThreadedLayouts',
        'menu_showall',
        'menu_levels',
        'editor',
        'socialsharetag',
    ];

    /**
     * Families of options which are read with a name that is made in the code: 'avatarSizeX' . $name.
     */
    private const FAMILIES = ['/^avatarSize[XY]\w+$/' => ["'avatarSizeX'", "'avatarSizeY'"]];

    /** @var array<string,SimpleXMLElement>|null */
    private static ?array $fields = null;

    private static ?string $code = null;

    /**
     * @return  array<string,SimpleXMLElement>
     */
    private static function fields(): array
    {
        if (self::$fields === null) {
            self::$fields = [];

            foreach (simplexml_load_string(Files::read(self::FILE))->xpath('//field') as $field) {
                $name = (string) $field['name'];

                if ($name !== '' && (string) $field['type'] !== 'spacer') {
                    self::$fields[$name] ??= $field;
                }
            }
        }

        return self::$fields;
    }

    /**
     * The code of the forum (php, js and scss) without the language files, the options and the updates of old versions.
     */
    private static function code(): string
    {
        if (self::$code === null) {
            $code  = '';
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Files::path('src'), FilesystemIterator::SKIP_DOTS));

            foreach ($files as $file) {
                $path = str_replace('\\', '/', $file->getPathname());

                // The template Agora reads its own options (config.xml of Agora, AgoraOptionsTest).
                if (
                    !preg_match('/\.(php|js|scss)$/', $path) || str_contains($path, '/language/') || str_contains($path, '/install/sql/')
                    || str_contains($path, '/site/template/agora/')
                ) {
                    continue;
                }

                $code .= file_get_contents($path) . "\n";
            }

            self::$code = $code;
        }

        return self::$code;
    }

    public static function options(): array
    {
        $cases = [];

        foreach (array_keys(self::fields()) as $name) {
            $cases[$name] = [$name];
        }

        return $cases;
    }

    #[DataProvider('options')]
    public function testTheNameIsAName(string $name): void
    {
        $this->assertMatchesRegularExpression('/^[A-Za-z][A-Za-z0-9_]*$/', $name);
    }

    #[DataProvider('options')]
    public function testTheTextsOfTheChoicesExist(string $name): void
    {
        $strings = Files::languageFolder('src/site/language/en-GB') + Files::languageFolder('src/admin/language/en-GB');

        $this->assertGreaterThanOrEqual(0, \count(self::fields()[$name]->option));

        foreach (self::fields()[$name]->option as $option) {
            $text = trim((string) $option);

            // The strings of Joomla (JYES, JNO, JGLOBAL_...) and texts which are written out are not in the language files.
            if ($text === '' || !preg_match('/^COM_KUNENA[A-Z0-9_]*$/', $text)) {
                $this->assertTrue(true, 'A text of Joomla or a written text.');

                continue;
            }

            $this->assertArrayHasKey($text, $strings, "The choice $text of the option $name is not in the language files.");
        }
    }

    #[DataProvider('options')]
    public function testTheChoicesAreDifferent(string $name): void
    {
        $values = [];

        foreach (self::fields()[$name]->option as $option) {
            $values[] = (string) $option['value'];
        }

        $this->assertSame($values, array_values(array_unique($values)), "The option $name has two choices with the same value.");
    }

    #[DataProvider('options')]
    public function testANumberIsANumberInsideItsLimits(string $name): void
    {
        $field = self::fields()[$name];

        if ((string) $field['type'] !== 'number') {
            $this->assertTrue(true, 'The option is no number.');

            return;
        }

        $default = (string) $field['default'];

        $this->assertIsNumeric($default);

        if ((string) $field['min'] !== '') {
            $this->assertGreaterThanOrEqual((float) $field['min'], (float) $default);
        }

        if ((string) $field['max'] !== '') {
            $this->assertLessThanOrEqual((float) $field['max'], (float) $default);
        }
    }

    #[DataProvider('options')]
    public function testAColourIsAColour(string $name): void
    {
        $field = self::fields()[$name];

        if ((string) $field['type'] !== 'color') {
            $this->assertTrue(true, 'The option is no colour.');

            return;
        }

        $this->assertMatchesRegularExpression(
            '/^(#[0-9a-fA-F]{3,8}|inherit|transparent|currentcolor|[a-z]+)$/',
            (string) $field['default'],
            "The start value of the colour $name is no colour."
        );
    }

    #[DataProvider('options')]
    public function testTheOptionIsReadByTheCode(string $name): void
    {
        if (\in_array($name, self::KNOWN_UNUSED, true)) {
            $this->assertDoesNotMatchRegularExpression(
                '/[\'"]' . preg_quote($name, '/') . '[\'"]/',
                self::code(),
                "The option $name is read by the code now: take it out of KNOWN_UNUSED."
            );

            return;
        }

        foreach (self::FAMILIES as $pattern => $prefixes) {
            if (preg_match($pattern, $name)) {
                foreach ($prefixes as $prefix) {
                    $this->assertStringContainsString($prefix, self::code());
                }

                return;
            }
        }

        $this->assertMatchesRegularExpression('/[\'"]' . preg_quote($name, '/') . '[\'"]/', self::code(), "No code reads the option $name.");
    }

    public function testTheCodeReadsOnlyOptionsWhichTheTemplateHas(): void
    {
        preg_match_all(
            '/(?:getTemplate\(\)|KunenaTemplate::getInstance\(\)|ktemplate|templateParams|\$template)->(?:params->)?get\(\s*[\'"](\w+)[\'"]/',
            self::code(),
            $matches
        );

        $missing = array_values(array_diff(array_unique($matches[1]), array_keys(self::fields())));
        $new     = array_values(array_diff($missing, self::KNOWN_UNDEFINED));

        $this->assertNotEmpty($matches[1], 'The pattern finds no reading of an option.');
        $this->assertSame([], $new, 'The code reads template options which config.xml does not have: ' . implode(', ', $new));
    }

    public function testTheListOfKnownUndefinedOptionsIsStillTrue(): void
    {
        preg_match_all(
            '/(?:getTemplate\(\)|KunenaTemplate::getInstance\(\)|ktemplate|templateParams|\$template)->(?:params->)?get\(\s*[\'"](\w+)[\'"]/',
            self::code(),
            $matches
        );

        foreach (self::KNOWN_UNDEFINED as $name) {
            $this->assertContains($name, $matches[1], "The code does not read $name anymore: take it out of KNOWN_UNDEFINED.");
            $this->assertArrayNotHasKey($name, self::fields(), "$name has a field in config.xml now: take it out of KNOWN_UNDEFINED.");
        }
    }

    public function testTheAvatarSizesComeInPairs(): void
    {
        $x = array_map(static fn ($name) => substr($name, \strlen('avatarSizeX')), array_filter(array_keys(self::fields()), static fn ($name) => str_starts_with($name, 'avatarSizeX')));
        $y = array_map(static fn ($name) => substr($name, \strlen('avatarSizeY')), array_filter(array_keys(self::fields()), static fn ($name) => str_starts_with($name, 'avatarSizeY')));

        $this->assertNotEmpty($x);
        $this->assertEqualsCanonicalizing(array_values($x), array_values($y), 'Each avatar size has a width and a height.');
    }

    public function testTheButtonsOfTheEditorAreSwitches(): void
    {
        foreach (['Bold', 'Italic', 'Underline', 'Link_Unlink', 'Image', 'Code', 'Spoiler'] as $button) {
            $this->assertArrayHasKey($button, self::fields());
            $this->assertSame(['0', '1'], array_map(static fn ($option) => (string) $option['value'], iterator_to_array(self::fields()[$button]->option, false)));
        }
    }
}
