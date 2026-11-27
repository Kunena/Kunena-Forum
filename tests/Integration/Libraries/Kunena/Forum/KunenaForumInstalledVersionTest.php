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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use Kunena\Tests\Unit\Support\Files;
use ReflectionMethod;

/**
 * The version of a forum without the manifest of the package is read from the table of the versions: the newest row. The first row
 * was read (the version of the first installation), so an upgraded forum gave the version it once started with. And its name is
 * the one of that row: every version without a manifest was called "Stable Release".
 */
class KunenaForumInstalledVersionTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testTheNewestRowIsTheInstalledVersion(): void
    {
        $this->insertRows('#__kunena_version', [
            ['id' => 1, 'version' => '6.0.0', 'versiondate' => '2022-01-01', 'installdate' => '2022-01-02', 'build' => '', 'versionname' => 'First', 'state' => ''],
            ['id' => 2, 'version' => '8.0.0', 'versiondate' => '2026-11-01', 'installdate' => '2026-11-02', 'build' => '', 'versionname' => 'Release candidate', 'state' => ''],
        ]);

        $version = (new ReflectionMethod(KunenaForum::class, 'installedVersion'))->invoke(null);

        $this->assertSame('8.0.0', $version->version);
        $this->assertSame('Release candidate', $version->versionname);
    }

    public function testNoRowIsNoVersion(): void
    {
        $this->assertNull((new ReflectionMethod(KunenaForum::class, 'installedVersion'))->invoke(null));
    }

    public function testTheNameOfTheVersionIsKeptAndOnlyAnEmptyOneIsAStableRelease(): void
    {
        $source = Files::read('src/libraries/kunena/src/Forum/KunenaForum.php');

        $this->assertStringContainsString("} elseif ((string) self::\$version_name === '') {", $source);
        $this->assertStringNotContainsString("(\$manifest->versionname ?? 'Stable Release')", $source);
    }
}
