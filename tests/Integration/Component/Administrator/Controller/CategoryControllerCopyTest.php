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

namespace Kunena\Tests\Integration\Component\Administrator\Controller;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Administrator\Controller\CategoryController;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * The name and the alias of the copy of a category (Save as Copy in the backend). The alias was only compared with the alias of
 * the category which is copied: the second copy of General got general-2, the alias of the first copy.
 */
class CategoryControllerCopyTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The controller reads the database of the container of Joomla. Loading KunenaRoute runs its initialize(), which needs a
        // configuration and an application: an empty configuration and an application of the command line.
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $application = $this->createStub(CMSApplicationInterface::class);
        // Not the site: the languages of the categories (KunenaMultilanguage) are not read from the language filter of the site.
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client !== 'site');

        // The helper of the categories is loaded first: it loads all the categories when it is loaded, which the replaced
        // application could not do.
        $this->saved = [
            'categories'  => $this->replace(KunenaCategoryHelper::class, '_instances', []),
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'config'      => $this->replace(KunenaConfig::class, 'instance', (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor()),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        class_exists(KunenaRoute::class);

        $this->saved += [
            'rows' => $this->replace(KunenaRoute::class, 'aliasRows', []),
            'read' => $this->replace(KunenaRoute::class, 'aliasesRead', []),
        ];

        // General, and its first copy.
        $this->insertRows('#__kunena_categories', [
            ['id' => 2, 'name' => 'General', 'alias' => 'general'],
            ['id' => 3, 'name' => 'General (2)', 'alias' => 'general-2'],
        ]);

        $this->insertRows('#__kunena_aliases', [
            ['alias' => 'general', 'type' => 'catid', 'item' => '2', 'state' => 1],
            ['alias' => 'general-2', 'type' => 'catid', 'item' => '3', 'state' => 1],
            ['alias' => 'recent', 'type' => 'view', 'item' => 'topics', 'state' => 1],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);
            $this->replace(KunenaRoute::class, 'aliasRows', $this->saved['rows'] ?? []);
            $this->replace(KunenaRoute::class, 'aliasesRead', $this->saved['read'] ?? []);
        }

        parent::tearDown();
    }

    private function copy(int $categoryId, string $alias, string $name): array
    {
        // The controller without its constructor: that one needs the application of Joomla.
        $controller = (new ReflectionClass(CategoryController::class))->newInstanceWithoutConstructor();

        return (new ReflectionMethod(CategoryController::class, 'internalGenerateNewTitle'))->invoke($controller, $categoryId, $alias, $name);
    }

    public function testTheSecondCopyTakesTheNextFreeAlias(): void
    {
        $this->assertSame(['General (3)', 'general-3'], $this->copy(2, 'general', 'General'));
    }

    public function testAFreeAliasTypedInTheFormIsKept(): void
    {
        $this->assertSame(['News', 'news'], $this->copy(2, 'news', 'News'));
    }

    public function testAnAliasOfAViewIsNotGivenToTheCopy(): void
    {
        $this->assertSame(['Recent (2)', 'recent-2'], $this->copy(2, 'recent', 'Recent'));
    }

    /**
     * Put a value in a static property of a class, and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
