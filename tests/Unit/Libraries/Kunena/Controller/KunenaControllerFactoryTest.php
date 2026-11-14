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

namespace Kunena\Tests\Unit\Libraries\Kunena\Controller;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\LegacyFactory;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Kunena\Forum\Libraries\Controller\KunenaController;
use Kunena\Forum\Site\Controllers\CreditsController;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;
use RuntimeException;

/**
 * The old controllers of the site get the MVC factory of Kunena, so that they find the namespaced views and models instead of
 * failing with "View not found" through the LegacyFactory of Joomla (issue #10257).
 */
class KunenaControllerFactoryTest extends UnitTestCase
{
    private mixed $application;

    private MVCFactory $factory;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->application = Factory::$application;
        $this->factory     = new MVCFactory('\\Kunena\\Forum');

        $component = $this->createStub(MVCComponent::class);
        $component->method('getMVCFactory')->willReturn($this->factory);

        $app = $this->createStub(CMSApplicationInterface::class);
        $app->method('bootComponent')->willReturnCallback(
            fn (string $name) => $name === 'com_kunena' ? $component : throw new RuntimeException("Unexpected component {$name}")
        );

        Factory::$application = $app;
    }

    protected function tearDown(): void
    {
        if (\defined('JVERSION')) {
            Factory::$application = $this->application;
        }
    }

    private function controllerFactory(): mixed
    {
        $controller = $this->bare(CreditsController::class);

        $this->invoke(KunenaController::class, 'useComponentFactory', $controller);

        return (new ReflectionProperty($controller, 'factory'))->getValue($controller);
    }

    public function testTheControllerGetsTheFactoryOfTheComponent(): void
    {
        $factory = $this->controllerFactory();

        $this->assertSame($this->factory, $factory);
        $this->assertNotInstanceOf(LegacyFactory::class, $factory);
    }

    public function testTheFactoryFindsTheNamespacedViewAndModel(): void
    {
        $factory = $this->controllerFactory();

        $this->assertSame(
            'Kunena\\Forum\\Site\\View\\Category\\HtmlView',
            $this->invoke($factory, 'getClassName', 'View\\Category\\HtmlView', 'site')
        );
        $this->assertSame(
            'Kunena\\Forum\\Site\\Model\\CategoryModel',
            $this->invoke($factory, 'getClassName', 'Model\\CategoryModel', 'site')
        );
    }
}
