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

namespace Kunena\Forum\Tests\Unit\Config;

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Tests\Unit\Support\Files;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SimpleXMLElement;

/**
 * Every option of the configuration of the forum (src/admin/config.xml): one test case for each option. A new option which has
 * no text, a start value which the field does not offer, a rule which shows it with a switch that is not there, or no property
 * in the configuration class, fails here.
 */
class AllConfigOptionsTest extends TestCase
{
    /**
     * Types of fields which the configuration may use: the standard ones of Joomla and the fields of Kunena.
     */
    private const TYPES = [
        'hidden', 'text', 'radio', 'textarea', 'number', 'list', 'postDateFormat', 'categoryList', 'sql',
        'protectAttachments', 'fileSize', 'ebayLanguage', 'rules',
    ];

    /**
     * Options whose label is a text and not a language key (the label of a list of templates and of a list of pages).
     */
    private const LITERAL_LABELS = ['template', 'defaultPage'];

    /**
     * Options whose label is a language key of Joomla.
     */
    private const JOOMLA_LABELS = ['rules'];

    private static ?SimpleXMLElement $xml = null;

    /** @var array<string,SimpleXMLElement>|null */
    private static ?array $fields = null;

    /** @var array<string,string>|null */
    private static ?array $strings = null;

    /**
     * @return  array<string,SimpleXMLElement>
     */
    private static function fields(): array
    {
        if (self::$fields === null) {
            self::$xml    = simplexml_load_string(Files::read('src/admin/config.xml'));
            self::$fields = [];

            foreach (self::$xml->xpath('//field') as $field) {
                $name = (string) $field['name'];

                if ($name === '' || $name[0] === '@' || (string) $field['type'] === 'spacer' || (string) $field['config'] === 'false') {
                    continue;
                }

                self::$fields[$name] ??= $field;
            }
        }

        return self::$fields;
    }

    /**
     * The texts of the English language files of the administrator and of the site.
     *
     * @return  array<string,string>
     */
    private static function strings(): array
    {
        return self::$strings ??= Files::languageFolder('src/admin/language/en-GB') + Files::languageFolder('src/site/language/en-GB');
    }

    /**
     * @return  string[]
     */
    private static function values(SimpleXMLElement $field): array
    {
        $values = [];

        foreach ($field->option as $option) {
            $values[] = (string) $option['value'];
        }

        return $values;
    }

    public static function options(): array
    {
        $cases = [];

        foreach (array_keys(self::fields()) as $name) {
            $cases[$name] = [$name];
        }

        return $cases;
    }

    public function testTheConfigurationHasManyOptions(): void
    {
        $this->assertGreaterThan(200, \count(self::fields()));
    }

    #[DataProvider('options')]
    public function testTheNameIsAName(string $name): void
    {
        $this->assertMatchesRegularExpression('/^[A-Za-z][A-Za-z0-9]*$/', $name);
    }

    #[DataProvider('options')]
    public function testTheTypeIsKnown(string $name): void
    {
        $this->assertContains((string) self::fields()[$name]['type'], self::TYPES, "The option $name has a type which the tests do not know.");
    }

    #[DataProvider('options')]
    public function testTheLabelIsAnExistingText(string $name): void
    {
        $field = self::fields()[$name];
        $label = (string) $field['label'];

        if ((string) $field['type'] === 'hidden') {
            $this->assertTrue(true, 'A hidden option has no label.');

            return;
        }

        $this->assertNotSame('', $label, "The option $name has no label.");

        if (\in_array($name, self::LITERAL_LABELS, true) || \in_array($name, self::JOOMLA_LABELS, true)) {
            $this->assertTrue(true);

            return;
        }

        $this->assertArrayHasKey($label, self::strings(), "The label $label of the option $name is not in the language files.");
        $this->assertNotSame('', trim(self::strings()[$label]));
    }

    #[DataProvider('options')]
    public function testTheDescriptionIsAnExistingText(string $name): void
    {
        $description = (string) self::fields()[$name]['description'];

        if ($description === '') {
            $this->assertTrue(true, 'The option has no description.');

            return;
        }

        $this->assertArrayHasKey($description, self::strings(), "The description $description of the option $name is not in the language files.");
        $this->assertNotSame('', trim(self::strings()[$description]));
    }

    #[DataProvider('options')]
    public function testTheStartValueIsOneOfTheChoices(string $name): void
    {
        $field  = self::fields()[$name];
        $type   = (string) $field['type'];
        $values = self::values($field);

        if (!\in_array($type, ['radio', 'list'], true) || !$values) {
            $this->assertTrue(true, 'The field has no fixed choices.');

            return;
        }

        $this->assertContains((string) $field['default'], $values, "The start value of $name is not one of its choices.");
    }

    #[DataProvider('options')]
    public function testASwitchHasTheChoicesYesAndNo(string $name): void
    {
        $field = self::fields()[$name];

        if ((string) $field['layout'] !== 'joomla.form.field.radio.switcher') {
            $this->assertTrue(true, 'The option is no switch.');

            return;
        }

        $this->assertSame(['0', '1'], self::values($field), "The switch $name has other choices than 0 and 1.");
    }

    #[DataProvider('options')]
    public function testANumberIsInsideItsLimits(string $name): void
    {
        $field = self::fields()[$name];

        if ((string) $field['type'] !== 'number') {
            $this->assertTrue(true, 'The option is no number.');

            return;
        }

        $default = (string) $field['default'];

        if ((string) $field['min'] !== '' && (string) $field['max'] !== '') {
            $this->assertLessThanOrEqual((float) $field['max'], (float) $field['min'], "The limits of $name are the wrong way round.");
        }

        if ($default !== '') {
            $this->assertIsNumeric($default, "The start value of $name is no number.");

            if ((string) $field['min'] !== '') {
                $this->assertGreaterThanOrEqual((float) $field['min'], (float) $default, "The start value of $name is under its minimum.");
            }

            if ((string) $field['max'] !== '') {
                $this->assertLessThanOrEqual((float) $field['max'], (float) $default, "The start value of $name is over its maximum.");
            }
        }

        if ((string) $field['step'] !== '') {
            $this->assertGreaterThan(0, (float) $field['step']);
        }
    }

    #[DataProvider('options')]
    public function testTheRuleToShowTheOptionPointsToAnOptionWithThatChoice(string $name): void
    {
        $showon = (string) self::fields()[$name]['showon'];

        if ($showon === '') {
            $this->assertTrue(true, 'The option is always shown.');

            return;
        }

        preg_match_all('/([A-Za-z0-9_]+)!?:([^\[\]]*)/', $showon, $conditions, PREG_SET_ORDER);

        $this->assertNotEmpty($conditions, "The rule $showon of $name is not a rule.");

        foreach ($conditions as [, $target, $value]) {
            $this->assertArrayHasKey($target, self::fields(), "The option $name is shown by $target, which is no option.");

            $choices = self::values(self::fields()[$target]);

            if ($choices) {
                foreach (explode(',', $value) as $one) {
                    $this->assertContains($one, $choices, "The option $name is shown for $target=$one, which is no choice of $target.");
                }
            }

            $this->assertNotSame($name, $target, "The option $name is shown by itself.");
        }
    }

    #[DataProvider('options')]
    public function testTheOptionIsAPropertyOfTheConfigurationClass(string $name): void
    {
        $this->assertMatchesRegularExpression(
            '/@property\s+\S+\s+\$' . preg_quote($name, '/') . '\s/',
            Files::read('src/libraries/kunena/src/Config/KunenaConfig.php'),
            "The option $name has no @property line in KunenaConfig."
        );
    }

    #[DataProvider('options')]
    public function testTheStartValueIsReadFromTheFile(string $name): void
    {
        $method = new ReflectionMethod(KunenaConfig::class, 'getXmlDefaults');
        $values = $method->invoke(null, Files::path('src/admin/config.xml'));

        $this->assertArrayHasKey($name, $values);
        $this->assertSame((string) self::fields()[$name]['default'], (string) $values[$name]);
    }

    #[DataProvider('options')]
    public function testTheStartValueHasNoSpacesAroundIt(string $name): void
    {
        $default = (string) self::fields()[$name]['default'];

        $this->assertSame(trim($default), $default);
    }

    public function testEveryPropertyOfTheConfigurationClassIsAnOption(): void
    {
        preg_match_all('/@property\s+\S+\s+\$(\w+)/', Files::read('src/libraries/kunena/src/Config/KunenaConfig.php'), $matches);

        $stale = array_values(array_diff($matches[1], array_keys(self::fields())));

        $this->assertNotEmpty($matches[1]);
        $this->assertSame([], $stale, 'These properties are no option of config.xml (anymore): ' . implode(', ', $stale));
    }

    public function testEveryOptionIsDefinedOnce(): void
    {
        self::fields();

        $count = 0;
        $names = [];

        foreach (self::$xml->xpath('//field') as $field) {
            $name = (string) $field['name'];

            if ($name === '' || $name[0] === '@' || (string) $field['type'] === 'spacer' || (string) $field['config'] === 'false') {
                continue;
            }

            $count++;
            $names[$name] = ($names[$name] ?? 0) + 1;
        }

        $twice = array_keys(array_filter($names, static fn ($times) => $times > 1));

        $this->assertSame([], $twice, 'These options are defined twice: ' . implode(', ', $twice));
        $this->assertSame($count, \count(self::fields()));
    }

    public function testTheTabsHaveTextsToo(): void
    {
        self::fields();

        foreach (self::$xml->xpath('//fieldset') as $fieldset) {
            $label = (string) $fieldset['label'];

            // The strings of Joomla itself (JGLOBAL_..., JCONFIG_...) are not in the language files of the forum.
            if ($label === '' || preg_match('/^J[A-Z]+_/', $label)) {
                continue;
            }

            $this->assertArrayHasKey($label, self::strings(), 'The tab ' . $fieldset['name'] . " has the label $label, which is not in the language files.");
        }
    }

    public function testASqlFieldUsesThePrefixOfTheTables(): void
    {
        self::fields();

        foreach (self::$xml->xpath('//field[@type="sql"]') as $field) {
            $this->assertStringContainsString('#__', (string) $field['query'], 'The query of ' . $field['name'] . ' has no #__ prefix.');
        }
    }
}
