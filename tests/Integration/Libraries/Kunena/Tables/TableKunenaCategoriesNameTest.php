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

namespace Kunena\Tests\Integration\Libraries\Kunena\Tables;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Tables\TableKunenaCategories;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The name of a category is a TINYTEXT (255 bytes) which the form does not limit: a longer name was an error of the database,
 * which Joomla runs in strict mode, and the category was not saved.
 */
class TableKunenaCategoriesNameTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver      = $this->getDBDriver();
        $userFactory = $this->createStub(UserFactoryInterface::class);
        $userFactory->method('loadUserById')->willReturnCallback(static function (int $id): User {
            $user     = new User();
            $user->id = $id;

            return $user;
        });
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(UserFactoryInterface::class, static fn () => $userFactory);

        // Loading the helper of the users makes the current user (a guest here), who reads the configuration and the avatars.
        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'config'      => $this->replace(KunenaConfig::class, 'instance', (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor()),
            'avatars'     => $this->replace(KunenaAvatar::class, 'instance', new KunenaAvatar()),
        ];

        $dispatcher  = new Dispatcher();
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);

        Factory::$container   = $container;
        Factory::$application = $application;
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAvatar::class, 'instance', $this->saved['avatars']);
        }

        // The row stored by the table itself (the rows of insertRow() are removed by the trait).
        $this->getDBDriver()->setQuery("DELETE FROM #__kunena_categories WHERE alias = 'long-name'")->execute();

        parent::tearDown();
    }

    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }

    public function testALongNameIsCutAtACharacter(): void
    {
        $driver = $this->getDBDriver();
        $mode   = (string) $driver->setQuery('SELECT @@SESSION.sql_mode')->loadResult();
        $driver->setQuery("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'")->execute();

        try {
            $table = new TableKunenaCategories($driver);
            $table->bind(['name' => str_repeat('Ü', 200), 'alias' => 'long-name', 'icon' => '', 'class_sfx' => '', 'description' => '', 'headerdesc' => '', 'topictemplate' => '', 'params' => '{}', 'published' => 1]);

            $this->assertTrue($table->check());
            $this->assertTrue($table->store());
        } finally {
            $driver->setQuery('SET SESSION sql_mode = ' . $driver->quote($mode))->execute();
        }

        $this->assertSame(str_repeat('Ü', 127), $this->fetchAll('SELECT name FROM #__kunena_categories')[0]['name']);
    }
}
