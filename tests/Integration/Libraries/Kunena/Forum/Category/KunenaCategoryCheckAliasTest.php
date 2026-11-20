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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Category;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Exception\KunenaException;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * A category may not take the alias of another category nor a reserved word (the name of a view): the check of the category
 * asked whether the alias was free, but let the save go on when it was not. The category was saved with the alias, its address
 * opened the other category (or the view), and the table of the aliases still named the other one. Only the form of the
 * backend stopped it, with a script.
 */
class KunenaCategoryCheckAliasTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The aliases are read from the database of the container of Joomla. Loading KunenaRoute runs its initialize(), which needs
        // a configuration and an application: an empty configuration and an application of the command line (it stops at once).
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        // The aliases with the Unicode filter of Joomla: the other one reads the settings of the languages (the cache of Joomla).
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('isClient')->willReturn(true);
        $application->method('get')->willReturnCallback(static fn ($name, $default = null) => $name === 'unicodeslugs' ? 1 : $default);

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'config'      => $this->replace(KunenaConfig::class, 'instance', (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor()),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $this->createStub(Language::class);
        Factory::$language->method('_')->willReturnArgument(0);

        class_exists(KunenaRoute::class);

        $this->saved += [
            'rows' => $this->replace(KunenaRoute::class, 'aliasRows', []),
            'read' => $this->replace(KunenaRoute::class, 'aliasesRead', []),
        ];

        $this->insertRows('#__kunena_aliases', [
            ['alias' => 'general', 'type' => 'catid', 'item' => '1', 'state' => 1],
            ['alias' => 'search', 'type' => 'view', 'item' => 'search', 'state' => 1],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaRoute::class, 'aliasRows', $this->saved['rows'] ?? []);
            $this->replace(KunenaRoute::class, 'aliasesRead', $this->saved['read'] ?? []);
        }

        parent::tearDown();
    }

    public function testTheAliasOfAnotherCategoryIsRefused(): void
    {
        $this->expectException(KunenaException::class);

        $this->category(2, 'general')->check();
    }

    public function testAReservedWordIsRefused(): void
    {
        $this->expectException(KunenaException::class);

        $this->category(2, 'search')->check();
    }

    public function testAFreeAliasAndTheOwnAliasAreTaken(): void
    {
        $this->assertTrue($this->category(2, 'news')->check());
        $this->assertTrue($this->category(1, 'general', 'old-general')->check(), 'the alias which the category has in the table');
    }

    /**
     * A category which exists, with a new alias (the alias it had: $old).
     */
    private function category(int $id, string $alias, string $old = ''): KunenaCategory
    {
        $category        = (new ReflectionClass(KunenaCategory::class))->newInstanceWithoutConstructor();
        $category->id    = $id;
        $category->name  = 'A category';
        $category->alias = $alias;

        foreach (['_alias' => $old, '_exists' => true] as $name => $value) {
            (new ReflectionProperty(KunenaCategory::class, $name))->setValue($category, $value);
        }

        return $category;
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
