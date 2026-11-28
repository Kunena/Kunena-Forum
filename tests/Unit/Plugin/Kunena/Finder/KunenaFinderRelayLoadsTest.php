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

namespace Kunena\Tests\Unit\Plugin\Kunena\Finder;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Plugin\Kunena\Finder\Extension\Finder;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The plugin "Kunena - Finder" (which tells Smart Search about the saved and deleted messages) is loaded by Joomla: it had no service
 * provider, and its class was not in the namespace of its folder, so Joomla loaded an empty plugin in its place (it looked for the
 * file of a plugin of Joomla 3). New, edited and deleted messages reached the search only at the next indexing, and a deleted message
 * or one moved into a hidden category stayed in the results until then.
 */
class KunenaFinderRelayLoadsTest extends UnitTestCase
{
    private mixed $application = null;

    private mixed $plugins = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->application    = (new ReflectionProperty(Factory::class, 'application'))->getValue();
        Factory::$application = $this->createStub(CMSApplicationInterface::class);

        $plugins       = new ReflectionProperty(PluginHelper::class, 'plugins');
        $this->plugins = $plugins->getValue();
        $plugins->setValue(null, []);

        require_once Files::path('src/plugins/plg_kunena_finder/src/Extension/Finder.php');
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->application;
        (new ReflectionProperty(PluginHelper::class, 'plugins'))->setValue(null, $this->plugins);

        parent::tearDown();
    }

    public function testTheServiceProviderGivesThePlugin(): void
    {
        $this->assertFileExists(Files::path('src/plugins/plg_kunena_finder/services/provider.php'));

        $container = new Container();
        $container->share(DispatcherInterface::class, static fn () => new Dispatcher());
        (require Files::path('src/plugins/plg_kunena_finder/services/provider.php'))->register($container);

        $this->assertInstanceOf(Finder::class, $container->get(PluginInterface::class));
    }

    public function testTheManifestInstallsTheProvider(): void
    {
        $this->assertStringContainsString('<folder>services</folder>', Files::read('src/plugins/plg_kunena_finder/finder.xml'));
    }
}
