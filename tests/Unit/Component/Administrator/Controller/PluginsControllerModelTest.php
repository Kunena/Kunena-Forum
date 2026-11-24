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

namespace Kunena\Tests\Unit\Component\Administrator\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Kunena\Forum\Administrator\Controller\PluginsController;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Kunena > Plugins: the check-in (the button of the toolbar and the lock of a row) and the ordering of the plugins are done by the
 * model of the plugins of Joomla, as the publishing is. Kunena has no model of a plugin: the check-in stopped with the error
 * "Call to a member function checkIn() on false".
 */
class PluginsControllerModelTest extends UnitTestCase
{
    public function testTheModelIsThePluginModelOfJoomla(): void
    {
        $this->requireJoomla();

        $model = $this->createStub(BaseDatabaseModel::class);
        $asked = [];

        $factory = $this->createStub(MVCFactoryInterface::class);
        $factory->method('createModel')->willReturnCallback(
            static function (string $name, string $prefix = '', array $config = []) use ($model, &$asked) {
                $asked[] = [$name, $prefix, $config];

                return $model;
            }
        );

        $component = $this->createStub(MVCComponent::class);
        $component->method('getMVCFactory')->willReturn($factory);

        $booted = [];
        $app    = $this->createStub(CMSApplication::class);
        $app->method('bootComponent')->willReturnCallback(
            static function (string $name) use ($component, &$booted) {
                $booted[] = $name;

                return $component;
            }
        );

        $controller = (new ReflectionClass(PluginsController::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(PluginsController::class, 'app'))->setValue($controller, $app);

        $this->assertSame($model, $controller->getModel());
        $this->assertSame(['com_plugins'], $booted);
        $this->assertSame([['Plugin', 'Administrator', ['ignore_request' => true]]], $asked);
    }
}
