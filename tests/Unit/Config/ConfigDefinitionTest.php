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

use Kunena\Forum\Tests\Unit\Support\Files;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

/**
 * The options of the features of 7.2: each one is in the configuration with its type and start value, has its texts in the
 * language files and is listed in the properties of the configuration class.
 */
class ConfigDefinitionTest extends TestCase
{
    private static ?SimpleXMLElement $xml = null;

    private static function field(string $name): ?SimpleXMLElement
    {
        self::$xml ??= simplexml_load_string(Files::read('src/admin/config.xml'));

        $found = self::$xml->xpath('//field[@name="' . $name . '"]');

        return $found ? $found[0] : null;
    }

    /**
     * name => type, start value
     */
    public static function options(): array
    {
        return [
            'ban button'                 => ['quickBan', 'radio', '1'],
            'ban from'                   => ['quickBanLevel', 'list', '0'],
            'ban days'                   => ['quickBanDays', 'number', '7'],
            'ban deletes the posts'      => ['quickBanDeletePosts', 'radio', '0'],
            'map provider'               => ['mapProvider', 'list', 'auto'],
            'google maps key'            => ['googleMapApiKey', 'text', ''],
            'default announcement'       => ['bannerAnnouncement', 'hidden', '0'],
            'category id in the alias'   => ['categoryAliasId', 'radio', '0'],
            'joomla mail templates'      => ['useMailTemplates', 'radio', '0'],
            'popup notifications'        => ['popupNotifications', 'radio', '1'],
            'popup interval'             => ['popupNotificationsInterval', 'number', '30'],
            'link cards'                 => ['linkPreview', 'radio', '1'],
            'link cards in a message'    => ['linkPreviewMax', 'number', '3'],
            'sites without link cards'   => ['linkPreviewBlock', 'textarea', ''],
            'spotify player'             => ['autoEmbedSpotify', 'radio', '1'],
            'deezer player'              => ['autoEmbedDeezer', 'radio', '1'],
        ];
    }

    #[DataProvider('options')]
    public function testTheOptionIsInTheConfiguration(string $name, string $type, string $default): void
    {
        $field = self::field($name);

        $this->assertNotNull($field, "The option $name is not in config.xml.");
        $this->assertSame($type, (string) $field['type']);
        $this->assertSame($default, (string) $field['default']);
    }

    #[DataProvider('options')]
    public function testTheOptionHasItsTexts(string $name, string ...$unused): void
    {
        $field   = self::field($name);
        $strings = Files::languageFolder('src/admin/language/en-GB');

        foreach (['label', 'description'] as $attribute) {
            $key = (string) $field[$attribute];

            // The option of the default announcement is hidden: it is set in the list of the announcements.
            if ($key === '' && (string) $field['type'] === 'hidden') {
                $this->assertSame('bannerAnnouncement', $name);

                continue;
            }

            if ($key === '' && $attribute === 'description') {
                continue;
            }

            $this->assertArrayHasKey($key, $strings, "The text $key of the option $name is missing in the language files.");
            $this->assertNotSame('', $strings[$key]);
        }
    }

    #[DataProvider('options')]
    public function testTheOptionIsAPropertyOfTheConfiguration(string $name, string ...$unused): void
    {
        $this->assertStringContainsString('$' . $name . ' ', Files::read('src/libraries/kunena/src/Config/KunenaConfig.php'));
    }

    public static function numbers(): array
    {
        return [
            'popup interval'          => ['popupNotificationsInterval', '10', '300'],
            'link cards in a message' => ['linkPreviewMax', '1', '20'],
        ];
    }

    #[DataProvider('numbers')]
    public function testTheNumberOptionsHaveLimits(string $name, string $min, string $max): void
    {
        $field = self::field($name);

        $this->assertSame($min, (string) $field['min']);
        $this->assertSame($max, (string) $field['max']);
    }

    public static function dependents(): array
    {
        return [
            'popup interval'        => ['popupNotificationsInterval', 'popupNotifications:1'],
            'link cards in message' => ['linkPreviewMax', 'linkPreview:1'],
            'sites without cards'   => ['linkPreviewBlock', 'linkPreview:1'],
            'ban from'              => ['quickBanLevel', 'quickBan:1'],
            'ban days'              => ['quickBanDays', 'quickBan:1'],
        ];
    }

    #[DataProvider('dependents')]
    public function testTheOptionsOfASwitchAreShownWithTheSwitch(string $name, string $showon): void
    {
        $this->assertSame($showon, (string) self::field($name)['showon']);
    }

    public function testEveryOptionIsDefinedOnce(): void
    {
        foreach (array_keys(array_column(self::options(), 0, 0)) as $name) {
            self::$xml ??= simplexml_load_string(Files::read('src/admin/config.xml'));

            $this->assertCount(1, self::$xml->xpath('//field[@name="' . $name . '"]'), "The option $name is defined twice.");
        }
    }
}
