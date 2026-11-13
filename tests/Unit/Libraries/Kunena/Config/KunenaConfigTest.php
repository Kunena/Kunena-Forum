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

namespace Kunena\Tests\Unit\Libraries\Kunena\Config;

use Exception;
use Joomla\CMS\Cache\Controller\CallbackController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\ComponentRecord;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The cached list of options carries a mark of config.xml in its cache id: a list built from another config.xml (or stored
 * under the old fixed id 'config') is built again, a current one is used as it is, and an unreadable config.xml is never cached.
 */
class KunenaConfigTest extends UnitTestCase
{
    private const XML_OLD = '<?xml version="1.0" encoding="utf-8"?>
<config>
    <fieldset name="basic">
        <field name="boardTitle" type="text" default="Forum"/>
        <field name="ownDomains" type="text" default="old"/>
    </fieldset>
</config>
';

    private const XML_NEW = '<?xml version="1.0" encoding="utf-8"?>
<config>
    <fieldset name="basic">
        <field name="boardTitle" type="text" default="Forum"/>
        <field name="ownDomains" type="text" default="old"/>
        <field name="linkPreviewBlock" type="text" default="example.org"/>
        <field name="spacer1" type="spacer"/>
    </fieldset>
</config>
';

    private string $folder = '';

    private ?ReflectionProperty $components = null;

    private array $savedComponents = [];

    protected function setUp(): void
    {
        $this->folder = sys_get_temp_dir() . '/kunena-config-' . bin2hex(random_bytes(6));
        mkdir($this->folder);
    }

    protected function tearDown(): void
    {
        if ($this->components !== null) {
            $this->components->setValue(null, $this->savedComponents);
            $this->components = null;
        }

        foreach (glob($this->folder . '/*') ?: [] as $file) {
            unlink($file);
        }

        rmdir($this->folder);
    }

    private function xml(string $content, int $time = 1700000000): string
    {
        $path = $this->folder . '/config.xml';
        file_put_contents($path, $content);
        touch($path, $time);
        clearstatcache(true, $path);

        return $path;
    }

    /**
     * The params of com_kunena without the database: the stored options of the site.
     */
    private function seedParams(array $params): void
    {
        $this->requireJoomla();

        $this->components      = new ReflectionProperty(ComponentHelper::class, 'components');
        $this->savedComponents = $this->components->getValue() ?? [];
        $record                = new ComponentRecord(['option' => 'com_kunena', 'enabled' => 1]);
        $record->setParams(new Registry($params));
        $this->components->setValue(null, ['com_kunena' => $record] + $this->savedComponents);
    }

    /**
     * A cache controller which keeps the entries by id in an array and calls the callback on a miss, as CallbackController::get().
     *
     * @param   array  $entries  The entries by id, filled by the stores.
     * @param   int    $builds   The number of calls of the callback.
     */
    private function cache(array &$entries, int &$builds): CallbackController
    {
        $cache = $this->createStub(CallbackController::class);
        $cache->method('get')->willReturnCallback(
            function (callable $callback, array $args = [], $id = false) use (&$entries, &$builds) {
                if (\array_key_exists($id, $entries)) {
                    return $entries[$id];
                }

                $builds++;
                $entries[$id] = $callback(...$args);

                return $entries[$id];
            }
        );

        return $cache;
    }

    public function testTheMarkFollowsTheTimeAndTheSizeOfTheFile(): void
    {
        $path = $this->xml(self::XML_OLD);
        $mark = self::invoke(KunenaConfig::class, 'configCacheId', $path);

        $this->assertIsString($mark);
        $this->assertNotSame('config', $mark);
        $this->assertSame($mark, self::invoke(KunenaConfig::class, 'configCacheId', $path));

        touch($path, 1700000100);
        clearstatcache(true, $path);
        $touched = self::invoke(KunenaConfig::class, 'configCacheId', $path);
        $this->assertNotSame($mark, $touched);

        file_put_contents($path, self::XML_OLD . ' ');
        touch($path, 1700000100);
        clearstatcache(true, $path);
        $this->assertNotSame($touched, self::invoke(KunenaConfig::class, 'configCacheId', $path));
    }

    public function testAMissingFileHasNoMark(): void
    {
        $this->assertNull(self::invoke(KunenaConfig::class, 'configCacheId', $this->folder . '/missing.xml'));
    }

    public function testAStaleListIsBuiltAgainWithTheNewOption(): void
    {
        $this->seedParams(['boardTitle' => 'My forum']);

        $oldPath = $this->xml(self::XML_OLD);
        $oldMark = self::invoke(KunenaConfig::class, 'configCacheId', $oldPath);
        $stale   = ['boardTitle' => 'My forum', 'ownDomains' => 'old'];
        $entries = ['config' => $stale, $oldMark => $stale];
        $builds  = 0;
        $cache   = $this->cache($entries, $builds);

        $path   = $this->xml(self::XML_NEW, 1700000200);
        $mark   = self::invoke(KunenaConfig::class, 'configCacheId', $path);
        $config = self::invoke(KunenaConfig::class, 'getCachedConfig', $path, $cache);

        $this->assertNotSame($oldMark, $mark);
        $this->assertSame(1, $builds);
        $this->assertSame(['boardTitle' => 'My forum', 'ownDomains' => 'old', 'linkPreviewBlock' => 'example.org'], $config);
        $this->assertSame($config, $entries[$mark]);
    }

    public function testTheLegacyConfigEntryIsIgnored(): void
    {
        $this->seedParams([]);

        $entries = ['config' => ['boardTitle' => 'Legacy']];
        $builds  = 0;
        $cache   = $this->cache($entries, $builds);
        $path    = $this->xml(self::XML_NEW);
        $config  = self::invoke(KunenaConfig::class, 'getCachedConfig', $path, $cache);

        $this->assertSame(1, $builds);
        $this->assertSame('Forum', $config['boardTitle']);
        $this->assertArrayHasKey('linkPreviewBlock', $config);
        $this->assertSame(['boardTitle' => 'Legacy'], $entries['config']);
    }

    public function testACurrentListIsUsedWithoutBuilding(): void
    {
        $this->requireJoomla();

        $path    = $this->xml(self::XML_NEW);
        $mark    = self::invoke(KunenaConfig::class, 'configCacheId', $path);
        $current = ['boardTitle' => 'Cached', 'ownDomains' => '', 'linkPreviewBlock' => ''];
        $entries = [$mark => $current];
        $builds  = 0;
        $cache   = $this->cache($entries, $builds);

        $this->assertSame($current, self::invoke(KunenaConfig::class, 'getCachedConfig', $path, $cache));
        $this->assertSame(0, $builds);
    }

    public function testAMissingConfigXmlIsNotCached(): void
    {
        $this->seedParams(['boardTitle' => 'My forum']);

        $cache = $this->createMock(CallbackController::class);
        $cache->expects($this->never())->method('get');
        $cache->expects($this->never())->method('store');

        $this->assertSame([], self::invoke(KunenaConfig::class, 'getCachedConfig', $this->folder . '/missing.xml', $cache));
    }

    public function testAnUnknownKeyThrowsOnGet(): void
    {
        $config = self::bare(KunenaConfig::class, ['config' => ['boardTitle' => 'Forum']]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Trying to get non-existing property: ownDomains');

        $config->ownDomains;
    }

    public function testAnUnknownKeyThrowsOnSet(): void
    {
        $config = self::bare(KunenaConfig::class, ['config' => ['boardTitle' => 'Forum']]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Trying to set non-existing property: ownDomains');

        $config->ownDomains = 'example.com';
    }

    public function testAKnownKeyReadsAndWrites(): void
    {
        $config = self::bare(KunenaConfig::class, ['config' => ['boardTitle' => 'Forum']]);

        $this->assertSame('Forum', $config->boardTitle);

        $config->boardTitle = 'Other';

        $this->assertSame('Other', $config->boardTitle);
    }
}
