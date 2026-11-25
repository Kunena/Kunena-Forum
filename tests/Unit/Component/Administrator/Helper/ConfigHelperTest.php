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

namespace Kunena\Tests\Unit\Component\Administrator\Helper;

use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The sections of the configuration view and of the dashboard are the tabs of config.xml: a tab added to config.xml
 * without a section would still be shown, but without its icon and its description.
 */
class ConfigHelperTest extends UnitTestCase
{
    /**
     * @return  string[]  The names of the tabs of config.xml, in their order.
     */
    private static function tabs(): array
    {
        $xml = simplexml_load_string(Files::read('src/admin/config.xml'));

        return array_map(static fn ($fieldset) => (string) $fieldset['name'], $xml->xpath('/config/fieldset'));
    }

    public function testEveryTabOfTheConfigurationHasASection(): void
    {
        $this->assertSame(self::tabs(), array_keys(ConfigHelper::SECTIONS));
    }

    public function testTheTextsOfTheSectionsExist(): void
    {
        // The administration loads the language of the site too (the label of the subscriptions is one of the site).
        $strings = Files::languageFolder('src/admin/language/en-GB') + Files::language('src/site/language/en-GB/com_kunena.ini');

        foreach (ConfigHelper::SECTIONS as $name => [$label, $icon, $description]) {
            foreach ([$label, $description] as $key) {
                // The texts of Joomla (JGLOBAL_..., JCONFIG_...) are in the language files of Joomla.
                if ($key[0] === 'J') {
                    continue;
                }

                $this->assertArrayHasKey($key, $strings, "The text $key of the section $name is missing in the language files.");
            }

            $this->assertMatchesRegularExpression('/^[a-z-]+$/', $icon);
        }
    }

    public function testTheLinkOpensAKnownSectionOnly(): void
    {
        $this->assertSame('index.php?option=com_kunena&view=config', ConfigHelper::link());
        $this->assertSame('index.php?option=com_kunena&view=config#security', ConfigHelper::link('security'));
        $this->assertSame('index.php?option=com_kunena&view=config', ConfigHelper::link('"><script>'));
    }
}
