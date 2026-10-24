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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use SimpleXMLElement;

/**
 * Every option of the template Aurelia (src/site/template/aurelia/config/config.xml): one test case for each option.
 */
class AllTemplateOptionsTest extends UnitTestCase
{
    private const FILE = 'src/site/template/aurelia/config/config.xml';

    /** @var array<string,SimpleXMLElement>|null */
    private static ?array $fields = null;

    private static ?SimpleXMLElement $xml = null;

    /**
     * @return  array<string,SimpleXMLElement>
     */
    private static function fields(): array
    {
        if (self::$fields === null) {
            self::$xml    = simplexml_load_string(Files::read(self::FILE));
            self::$fields = [];

            foreach (self::$xml->xpath('//field') as $field) {
                $name = (string) $field['name'];

                if ($name !== '' && (string) $field['type'] !== 'spacer') {
                    self::$fields[$name] ??= $field;
                }
            }
        }

        return self::$fields;
    }

    public static function options(): array
    {
        $cases = [];

        foreach (array_keys(self::fields()) as $name) {
            $cases[$name] = [$name];
        }

        return $cases;
    }

    public function testTheTemplateHasOptions(): void
    {
        $this->assertGreaterThan(50, \count(self::fields()));
    }

    #[DataProvider('options')]
    public function testTheTextsOfTheOptionExist(string $name): void
    {
        $strings = Files::languageFolder('src/site/language/en-GB') + Files::languageFolder('src/admin/language/en-GB');
        $field   = self::fields()[$name];

        foreach (['label', 'description'] as $attribute) {
            $key = (string) $field[$attribute];

            if ($key === '' || preg_match('/^J[A-Z]+_/', $key)) {
                $this->assertTrue(true, 'No text, or a text of Joomla.');

                continue;
            }

            $this->assertArrayHasKey($key, $strings, "The $attribute $key of the option $name is not in the language files.");
        }
    }

    #[DataProvider('options')]
    public function testTheStartValueIsOneOfTheChoices(string $name): void
    {
        $field  = self::fields()[$name];
        $values = [];

        foreach ($field->option as $option) {
            $values[] = (string) $option['value'];
        }

        if ((string) $field['type'] !== 'list' || !$values) {
            $this->assertTrue(true, 'The field has no fixed choices.');

            return;
        }

        $this->assertContains((string) $field['default'], $values, "The start value of $name is not one of its choices.");
    }

    public function testEveryOptionIsDefinedOnce(): void
    {
        self::fields();

        $names = [];

        foreach (self::$xml->xpath('//field') as $field) {
            $name = (string) $field['name'];

            if ($name !== '' && (string) $field['type'] !== 'spacer') {
                $names[$name] = ($names[$name] ?? 0) + 1;
            }
        }

        $twice = array_keys(array_filter($names, static fn ($times) => $times > 1));

        $this->assertSame([], $twice, 'These options are defined twice: ' . implode(', ', $twice));
    }

    public function testTheTabsHaveTexts(): void
    {
        self::fields();

        $strings = Files::languageFolder('src/site/language/en-GB') + Files::languageFolder('src/admin/language/en-GB');

        foreach (self::$xml->xpath('//fieldset') as $fieldset) {
            $key = (string) $fieldset['label'] ?: (string) $fieldset['name'];

            if (str_starts_with($key, 'COM_KUNENA')) {
                $this->assertArrayHasKey($key, $strings, "The tab text $key is not in the language files.");
            }
        }
    }
}
