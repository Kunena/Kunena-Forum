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

namespace Kunena\Tests\Integration\Libraries\Kunena\User;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserName;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The author of a message whose user was deleted (the message keeps the id of the user and his name): the lists of topics load the
 * authors first (loadUsers), which keeps the deleted user in memory without a name, and the author was then given back without the
 * name of the message, so the list showed no author.
 */
class KunenaUserHelperGetAuthorDeletedTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const FIELD = 7;

    /**
     * The values of the static properties which the test replaces, put back after the test.
     */
    private array $saved = [];


    public function getSchemasToLoad(): array
    {
        return ['kunena', 'fields', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The names read the database of the container of Joomla, and the field of the nickname from the configuration. Loading
        // the helper makes the current user, a guest here: his table needs the dispatcher, and he reads the avatars, the avatars
        // of Kunena itself (no plugin).
        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['displayNameField' => self::FIELD]);

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'avatars'     => $this->replace(KunenaAvatar::class, 'instance', new KunenaAvatar()),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        $this->saved += [
            'me'        => $this->replace(KunenaUserHelper::class, '_me', null),
            'instances' => $this->replace(KunenaUserHelper::class, '_instances', []),
            'byname'    => $this->replace(KunenaUserHelper::class, '_instances_name', []),
            'names'     => $this->replace(KunenaUserName::class, 'displayNames', []),
            'field'     => $this->replace(KunenaUserName::class, 'displayNameField', null),
        ];

    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAvatar::class, 'instance', $this->saved['avatars']);
            $this->replace(KunenaUserHelper::class, '_me', $this->saved['me']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['instances']);
            $this->replace(KunenaUserHelper::class, '_instances_name', $this->saved['byname']);
            $this->replace(KunenaUserName::class, 'displayNames', $this->saved['names']);
            $this->replace(KunenaUserName::class, 'displayNameField', $this->saved['field']);
        }

        parent::tearDown();
    }

    public function testTheAuthorOfAMessageOfADeletedUserHasTheNameOfTheMessage(): void
    {
        // The list loads its authors: the user 999999 is not there any more.
        KunenaUserHelper::loadUsers([999999]);

        $this->assertSame('Carol', KunenaUserHelper::getAuthor(999999, 'Carol')->username);
    }

    /**
     * Set a static property of a class and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
