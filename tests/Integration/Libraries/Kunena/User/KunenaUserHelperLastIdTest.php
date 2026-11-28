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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The "latest member" of the statistics (the statistics page, the statistics of the index) is the newest account which is not
 * blocked: it was the last id of the users which the setting of the count takes, which by default counts the blocked accounts which
 * were activated, so a spammer whom the administrator blocked right after he registered was the latest member, with a link.
 */
class KunenaUserHelperLastIdTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        // The count of all the accounts (the test database of Joomla has no column of the activation).
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['userlistCountUsers' => '0', 'superAdminUserlist' => 0]);

        // Loading the helper makes the user of the request: a command line application without plugins (as in
        // KunenaCategoryTrashUserPostsTest).
        $dispatcher  = new Dispatcher();
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        $this->saved += [
            'total'  => $this->replace(KunenaUserHelper::class, '_total', null),
            'lastid' => $this->replace(KunenaUserHelper::class, '_lastid', null),
        ];

        // A member, and after him a spammer whom the administrator blocked (his account was activated).
        $this->insertRows('#__users', [
            ['id' => 60, 'name' => 'Member', 'username' => 'member', 'email' => 'member@example.com', 'params' => '{}', 'block' => 0],
            ['id' => 61, 'name' => 'Spammer', 'username' => 'spammer', 'email' => 'spammer@example.com', 'params' => '{}', 'block' => 1],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(PluginHelper::class, 'plugins', $this->saved['plugins']);
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaUserHelper::class, '_total', $this->saved['total']);
            $this->replace(KunenaUserHelper::class, '_lastid', $this->saved['lastid']);
        }

        parent::tearDown();
    }

    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }

    public function testTheLatestMemberIsNotABlockedAccount(): void
    {
        // The count is the one of the setting (the blocked spammer is counted, as before).
        $this->assertSame(2, KunenaUserHelper::getTotalCount());
        $this->assertSame(60, KunenaUserHelper::getLastId());
    }
}
