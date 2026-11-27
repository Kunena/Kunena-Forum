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

namespace Kunena\Tests\Integration\Plugin\Task\Kunena;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Plugin\Task\Kunena\Extension\Kunena;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * The scheduled task "Remove the expired bans": a ban which has ended unblocks the account of Joomla and clears the ban of the
 * forum profile, unless the user is banned again. The task did not look at the other bans of the user: an old ban which it had
 * not handled yet (a forum upgraded from a version without the task, the task switched off for a while) unblocked a user who was
 * banned again since.
 */
class RemoveExpiredBansTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    private array $userColumns = [];

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

        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_task_kunena/src/Extension/Kunena.php';

        // The library reads the database of the container of Joomla: that is the database of the tests here. The application of
        // the backend has a list limit of 2, no user state, no plugin; the caches and the access do nothing.
        $driver      = $this->getDBDriver();
        $dispatcher  = new Dispatcher();
        $container   = new Container();
        $userFactory = $this->createStub(UserFactoryInterface::class);
        $userFactory->method('loadUserById')->willReturnCallback(static function (int $id): User {
            $user     = new User();
            $user->id = $id;

            return $user;
        });
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));
        $container->share(UserFactoryInterface::class, static fn () => $userFactory);

        $language = $this->createStub(Language::class);
        $language->method('transliterate')->willReturnArgument(0);
        $language->method('getTag')->willReturn('en-GB');
        $language->method('_')->willReturnArgument(0);

        $application = (new ReflectionClass(AdministratorApplication::class))->newInstanceWithoutConstructor();
        $application->setDispatcher($dispatcher);
        $application->setContainer($container);
        $application->setSession($this->createStub(Session::class));
        (new ReflectionProperty(AdministratorApplication::class, 'name'))->setValue($application, 'administrator');
        (new ReflectionProperty(AdministratorApplication::class, 'config'))->setValue($application, new Registry(['list_limit' => 2, 'access' => 1]));
        (new ReflectionProperty(AdministratorApplication::class, 'input'))->setValue($application, new Input([]));
        (new ReflectionProperty(AdministratorApplication::class, 'language'))->setValue($application, $language);

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, [
            'threadsPerPage' => 20, 'messagesPerPage' => 20, 'ratingEnabled' => 0, 'readOnly' => 0, 'showNew' => 1,
        ]);

        $this->saved = [
            'categories'  => $this->replace(KunenaCategoryHelper::class, '_instances', []),
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'access'      => $this->replace(KunenaAccess::class, 'instance', $this->createStub(KunenaAccess::class)),
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
            'components'  => $this->replace(ComponentHelper::class, 'components', ['com_kunena' => (object) ['id' => 1, 'enabled' => 1]]),
            // The model of the trash is of com_kunena, and a model of Joomla boots its component: the booted one of the test,
            // which has nothing (the real one needs the whole container of a site).
            'extensions' => $this->replace(ExtensionHelper::class, 'extensions', [
                ComponentInterface::class => ['kunena' => $this->createStub(ComponentInterface::class)],
            ]),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        $this->saved['me']    = $this->replace(KunenaUserHelper::class, '_me', new KunenaUser(0));
        $this->saved['users'] = $this->replace(KunenaUserHelper::class, '_instances', []);

        // The columns of the tables of an installation which the schema of the tests does not have, for the test only.
        $columns = array_column($this->fetchAll('SHOW COLUMNS FROM #__users'), 'Field');

        foreach (['registerDate' => 'datetime NOT NULL', 'lastvisitDate' => 'datetime NULL'] as $column => $type) {
            if (!\in_array($column, $columns, true)) {
                $driver->setQuery("ALTER TABLE #__users ADD $column $type")->execute();
                $this->userColumns[] = $column;
            }
        }

        $this->insertRow('#__users', ['id' => 42, 'name' => 'Member', 'username' => 'member', 'params' => '', 'block' => 1, 'registerDate' => '2020-01-01 00:00:00']);
        $this->insertRow('#__kunena_users', ['userid' => 42, 'banned' => '2021-01-01 00:00:00']);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAccess::class, 'instance', $this->saved['access']);
            $this->replace(PluginHelper::class, 'plugins', $this->saved['plugins']);
            $this->replace(ComponentHelper::class, 'components', $this->saved['components']);
            $this->replace(ExtensionHelper::class, 'extensions', $this->saved['extensions']);
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['users']);
            $this->replace(KunenaUserHelper::class, '_me', $this->saved['me']);
            $this->replace(KunenaTopicHelper::class, '_instances', []);
            $this->replace(KunenaMessageHelper::class, '_instances', []);
            $this->replace(KunenaTopicUserHelper::class, '_instances', []);
            $this->replace(KunenaTopicUserHelper::class, '_topics', []);
        }

        foreach ($this->userColumns as $column) {
            $this->getDBDriver()->setQuery("ALTER TABLE #__users DROP $column")->execute();
        }

        parent::tearDown();
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

    /**
     * The accounts of Joomla which the task unblocked.
     */
    private array $unblocked = [];

    /**
     * A ban of the user 42 which blocks the account.
     */
    private function ban(int $id, string $expiration): void
    {
        $this->insertRow('#__kunena_users_banned', [
            'id' => $id, 'userid' => 42, 'blocked' => 1, 'expiration' => $expiration, 'created_by' => 1, 'created_time' => '2020-01-01 00:00:00',
        ]);
    }

    /**
     * Run the task, with the user 42 blocked in Joomla.
     */
    private function run42(): void
    {
        $test        = $this;
        $userFactory = $this->createStub(UserFactoryInterface::class);
        $userFactory->method('loadUserById')->willReturnCallback(static function (int $id) use ($test): User {
            $user = new class () extends User {
                public ?\Closure $saved = null;

                public function save($updateOnly = false)
                {
                    ($this->saved)($this);

                    return true;
                }
            };
            $user->id    = $id;
            $user->block = 1;
            $user->saved = static function (User $user) use ($test): void {
                if (!$user->block) {
                    $test->unblocked[] = (int) $user->id;
                }
            };

            return $user;
        });
        Factory::$container->share(UserFactoryInterface::class, static fn () => $userFactory);

        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());
        (new ReflectionProperty(Kunena::class, 'snapshot'))->setValue($plugin, ['logCategory' => 'task']);

        $event = $this->createStub(ExecuteTaskEvent::class);
        $event->method('getRoutineId')->willReturn('remove.expiredbans');

        (new ReflectionMethod(Kunena::class, 'removeExpiredBans'))->invoke($plugin, $event);
    }

    /**
     * The end of the last ban in the forum profile (the save of the profile leaves it, it is in the past), and the bans which
     * the task marked as handled.
     */
    private function state(): array
    {
        return [
            'banned'  => $this->fetchAll('SELECT banned FROM #__kunena_users WHERE userid = 42')[0]['banned'],
            'handled' => array_map('intval', array_column($this->fetchAll('SELECT id FROM #__kunena_users_banned WHERE modified_time IS NOT NULL ORDER BY id'), 'id')),
        ];
    }

    public function testAUserWhoIsBannedAgainStaysBlocked(): void
    {
        $this->ban(1, '2021-01-01 00:00:00');
        $this->ban(2, '9999-12-31 23:59:59');

        $this->run42();

        $this->assertSame([], $this->unblocked);
        $this->assertSame(['banned' => '2021-01-01 00:00:00', 'handled' => [1]], $this->state());
    }
}
