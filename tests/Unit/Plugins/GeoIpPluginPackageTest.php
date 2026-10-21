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

namespace Kunena\Forum\Tests\Unit\Plugins;

use Kunena\Forum\Tests\Unit\Support\Files;
use PHPUnit\Framework\TestCase;

/**
 * The GeoIP plugin: the manifest, the files it names, its texts, and the places where it is listed for the package, the build
 * and the symlinks of the developers.
 */
class GeoIpPluginPackageTest extends TestCase
{
    private const FOLDER = 'src/plugins/plg_kunena_geoip';

    public function testTheManifestIsAKunenaPlugin(): void
    {
        $xml = simplexml_load_string(Files::read(self::FOLDER . '/geoip.xml'));

        $this->assertSame('plugin', (string) $xml['type']);
        $this->assertSame('kunena', (string) $xml['group']);
        $this->assertSame('Kunena\\Forum\\Plugin\\Kunena\\Geoip', (string) $xml->namespace);
        $this->assertSame('src', (string) $xml->namespace['path']);
        $this->assertSame(['geoip'], array_map('strval', $xml->xpath('//files/folder[@plugin]/@plugin')));
    }

    public function testTheFilesOfTheManifestAreThere(): void
    {
        $xml = simplexml_load_string(Files::read(self::FOLDER . '/geoip.xml'));

        foreach ($xml->files->folder as $folder) {
            $this->assertDirectoryExists(Files::path(self::FOLDER . '/' . $folder));
        }

        $this->assertFileExists(Files::path(self::FOLDER . '/services/provider.php'));
        $this->assertFileExists(Files::path(self::FOLDER . '/src/Extension/GeoIp.php'));
    }

    public function testTheProviderCreatesThePluginOfTheNamespace(): void
    {
        $provider = Files::read(self::FOLDER . '/services/provider.php');

        $this->assertStringContainsString('use Kunena\\Forum\\Plugin\\Kunena\\Geoip\\Extension\\GeoIp;', $provider);
        $this->assertStringContainsString("PluginHelper::getPlugin('kunena', 'geoip')", $provider);
        // The plugin does not use the database: the provider does not hand one over (the plugin has no such method).
        $this->assertStringNotContainsString('setDatabase', $provider);
    }

    public function testThePluginListensForTheEventOfTheForum(): void
    {
        $plugin = Files::read(self::FOLDER . '/src/Extension/GeoIp.php');

        $this->assertStringContainsString("'onKunenaGetIpLocation' => 'onKunenaGetIpLocation'", $plugin);
        $this->assertStringContainsString('namespace Kunena\\Forum\\Plugin\\Kunena\\Geoip\\Extension;', $plugin);
        $this->assertStringContainsString('class GeoIp extends CMSPlugin implements SubscriberInterface', $plugin);
        $this->assertStringContainsString('onKunenaGetIpLocation', Files::read('src/libraries/kunena/src/Html/KunenaIpLocation.php'));
    }

    public function testTheTextsOfTheManifestAreInTheLanguageFile(): void
    {
        $xml     = Files::read(self::FOLDER . '/geoip.xml');
        $strings = Files::language(self::FOLDER . '/language/en-GB/plg_kunena_geoip.sys.ini');

        preg_match_all('/(?:label|description|>)\s*=?\s*"?(PLG_KUNENA_GEOIP\w*)/', $xml, $used);

        $this->assertNotEmpty($used[1]);

        foreach (array_unique($used[1]) as $key) {
            $this->assertArrayHasKey($key, $strings, "$key is used in the manifest but is not in the language file.");
        }
    }

    public function testTheOptionOfTheDetailHasThreeLevels(): void
    {
        $xml    = simplexml_load_string(Files::read(self::FOLDER . '/geoip.xml'));
        $detail = $xml->xpath('//field[@name="detail"]')[0];

        $this->assertSame('city', (string) $detail['default']);
        $this->assertSame(['country', 'region', 'city'], array_map(static fn ($option) => (string) $option['value'], iterator_to_array($detail->option, false)));
    }

    public function testThePluginIsInThePackage(): void
    {
        $package = Files::read('src/pkg_kunena.xml');

        $this->assertMatchesRegularExpression('/group="kunena"\s+id="geoip">plg_kunena_geoip_v@kunenaversion@\.zip/', $package);
    }

    public function testThePluginIsBuilt(): void
    {
        $build = Files::read('build.xml');

        // It is copied for the package and for the xml files, and the folder is archived: three times in the file.
        $this->assertSame(3, substr_count($build, 'packages/plg_kunena_geoip"'));
        $this->assertStringContainsString('plg_kunena_geoip_v${xml.version}', $build);
    }

    public function testThePluginHasASymlinkForTheDevelopers(): void
    {
        $script = Files::read('scripts/kunena_sym_links.ps1');

        $this->assertStringContainsString('plg_kunena_geoip', $script);
        $this->assertStringContainsString('\\plugins\\kunena\\geoip', $script);
    }
}
