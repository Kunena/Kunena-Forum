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

namespace Kunena\Tests\Integration\Libraries\Kunena\Route;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The router asks for the alias of each segment of an address, one after the other: the aliases of all the segments are read
 * with one query before (preloadAliases), each segment is then found without a query of its own.
 */
class KunenaRoutePreloadAliasesTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The route reads the database of the container of Joomla. Loading KunenaRoute runs its initialize(), which needs a
        // configuration and an application: an empty configuration and an application of the command line (it stops at once).
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('isClient')->willReturn(true);

        $this->saved = [
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

        $this->insertRows('#__kunena_aliases', [
            ['alias' => 'test3', 'type' => 'catid', 'item' => '3', 'state' => 1],
            ['alias' => 'test3-more', 'type' => 'catid', 'item' => '9', 'state' => 1],
            ['alias' => 'recent', 'type' => 'view', 'item' => 'topics', 'state' => 1],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaRoute::class, 'aliasRows', $this->saved['rows'] ?? []);
            $this->replace(KunenaRoute::class, 'aliasesRead', $this->saved['read'] ?? []);
        }

        parent::tearDown();
    }

    public function testTheSegmentsOfAnAddressAreFoundWithOneQuery(): void
    {
        KunenaRoute::preloadAliases(['test3', '58-s', '']);

        // The aliases were read: without them in the database, the segments are still found.
        $this->getDBDriver()->setQuery('DELETE FROM ' . $this->getDBDriver()->quoteName('#__kunena_aliases'))->execute();

        // Only the alias which is equal counts (not test3-more), whatever its case.
        $this->assertSame(['catid' => '3', 'view' => 'category'], KunenaRoute::resolveAlias('Test3'));
        $this->assertSame([], KunenaRoute::resolveAlias('58-s'));
    }

    public function testASegmentWhichWasNotReadIsReadOnItsOwn(): void
    {
        KunenaRoute::preloadAliases(['test3']);

        $this->assertSame(['view' => 'topics'], KunenaRoute::resolveAlias('recent'));
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
