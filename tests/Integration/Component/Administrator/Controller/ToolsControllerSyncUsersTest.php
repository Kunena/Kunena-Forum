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

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Administrator\Controller\ToolsController;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserName;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Tools > Synchronize users > "Delete lifetime ban users": the Joomla accounts of the members banned for life which are blocked are
 * deleted, with their Kunena profile (by the delete of Joomla, whose clean-up removes the profile), for who may delete accounts. The profiles of all the members banned for life were deleted, also of those whose account
 * stays (a ban of the forum does not block the account): the ban is the column "banned" of the profile, so they were unbanned.
 */
class ToolsControllerSyncUsersTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const LIFETIME = '9999-12-31 23:59:59';

    private array $saved = [];

    private mixed $savedServer = null;

    /**
     * The accounts which were deleted (the delete of Joomla of each one).
     */
    private array $deletedIds = [];

    /**
     * Whether the administrator who runs the tool may delete the users (core.delete of com_users).
     */
    private bool $mayDelete = true;

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
        $userFactory = $this->createStub(UserFactoryInterface::class);
        // The accounts: their delete of Joomla is recorded (its clean-up is the work of the plugins, tested with them).
        $userFactory->method('loadUserById')->willReturnCallback(function (int $id): User {
            $user = new class () extends User {
                public ?\Closure $onDelete = null;

                public function authorise($action, $assetname = null)
                {
                    return false;
                }

                public function delete()
                {
                    return ($this->onDelete)((int) $this->id);
                }
            };

            $user->id       = $id;
            $user->onDelete = function (int $id): bool {
                $this->deletedIds[] = $id;

                return true;
            };

            return $user;
        });
        $container->share(UserFactoryInterface::class, static fn () => $userFactory);

        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        // The administrator application (no constructor) with a session, a user and the secret which make the form token.
        $session = $this->createStub(Session::class);
        $session->method('getToken')->willReturn('token');
        $application = (new ReflectionClass(AdministratorApplication::class))->newInstanceWithoutConstructor();
        $application->setDispatcher(new Dispatcher());
        $application->setSession($session);
        $application->setContainer($container);
        // The administrator 1, who may delete the users of Joomla (or not), and is no super user.
        $me = new class () extends User {
            public ?\Closure $may = null;

            public function authorise($action, $assetname = null)
            {
                return $action === 'core.delete' && $assetname === 'com_users' && ($this->may)();
            }
        };
        $me->id  = 1;
        $me->may = fn (): bool => $this->mayDelete;
        $application->loadIdentity($me);
        (new ReflectionProperty(AdministratorApplication::class, 'name'))->setValue($application, 'administrator');
        (new ReflectionProperty(AdministratorApplication::class, 'config'))->setValue($application, new Registry(['secret' => 'secret']));
        (new ReflectionProperty(AdministratorApplication::class, 'language'))->setValue($application, $language);

        // The redirect after the task: the router of the backend gives the address back.
        $router = $this->createStub(Router::class);
        $router->method('build')->willReturnCallback(static fn ($url) => new Uri($url));

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'config'      => $this->replace(KunenaConfig::class, 'instance', (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor()),
            // No user of the test is an administrator of the forum.
            'access'      => $this->replace(KunenaAccess::class, 'instance', $this->createStub(KunenaAccess::class)),
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        class_exists(KunenaRoute::class);
        // The users of the forum are loaded with the application of the test (loading the helper starts it).
        $this->saved['users'] = $this->replace(KunenaUserHelper::class, '_instances', []);
        $this->saved['adminApp'] = $this->replace(KunenaRoute::class, 'adminApp', true);
        $this->saved['routers']  = $this->replace(Route::class, '_router', ['administrator' => $router]);

        // The token of the form in its header.
        (new ReflectionProperty(AdministratorApplication::class, 'input'))->setValue($application, new Input(['userDelLife' => 1]));
        $this->savedServer                 = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        $_SERVER['HTTP_X_CSRF_TOKEN']      = Session::getFormToken();

        // 50: banned for life in the forum, his account is not blocked. 51: banned for life and blocked. 52: blocked, not banned.
        foreach ([50 => 0, 51 => 1, 52 => 1] as $id => $block) {
            $this->insertRow('#__users', ['id' => $id, 'name' => "User $id", 'username' => "user$id", 'block' => $block, 'params' => '']);
            $this->insertRow('#__user_usergroup_map', ['user_id' => $id, 'group_id' => 2]);
        }

        $this->insertRows('#__kunena_users', [
            ['userid' => 50, 'banned' => self::LIFETIME],
            ['userid' => 51, 'banned' => self::LIFETIME],
            ['userid' => 52],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            if ($this->savedServer === null) {
                unset($_SERVER['HTTP_X_CSRF_TOKEN']);
            } else {
                $_SERVER['HTTP_X_CSRF_TOKEN'] = $this->savedServer;
            }

            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAccess::class, 'instance', $this->saved['access']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['users']);
            $this->replace(PluginHelper::class, 'plugins', $this->saved['plugins']);
            $this->replace(KunenaRoute::class, 'adminApp', $this->saved['adminApp']);
            $this->replace(Route::class, '_router', $this->saved['routers']);
        }

        parent::tearDown();
    }

    private function syncUsers(): void
    {
        $controller = (new ReflectionClass(ToolsController::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(ToolsController::class, 'app'))->setValue($controller, Factory::$application);
        (new ReflectionProperty(ToolsController::class, 'input'))->setValue($controller, Factory::$application->getInput());
        (new ReflectionProperty(ToolsController::class, 'baseurl'))->setValue($controller, 'administrator/index.php?option=com_kunena&view=tools');

        $controller->syncUsers();
    }

    public function testOnlyTheBlockedAccountsOfTheLifetimeBannedAreDeletedAndTheOtherBansStay(): void
    {
        $this->syncUsers();

        $this->assertSame([51], $this->deletedIds);

        // The tool deletes no row itself: the profiles and the groups are left to the delete of Joomla (the ban of 50 stays).
        $this->assertSame([['userid' => 50, 'banned' => self::LIFETIME], ['userid' => 51, 'banned' => self::LIFETIME], ['userid' => 52, 'banned' => '1000-01-01 00:00:00']], $this->fetchAll('SELECT userid, banned FROM #__kunena_users ORDER BY userid'));
    }

    /**
     * Who may not delete the users of Joomla deletes none with the tool: any manager of the forum deleted the accounts with SQL.
     */
    public function testAnAdministratorWhoMayNotDeleteTheUsersDeletesNone(): void
    {
        $this->mayDelete = false;

        $this->syncUsers();

        $this->assertSame([], $this->deletedIds);
        $this->assertSame([['id' => 50], ['id' => 51], ['id' => 52]], $this->fetchAll('SELECT id FROM #__users ORDER BY id'));
    }

    /**
     * "Rename" with the names which the forum shows ("Show the name": 0 real name, 1 username, 2 nickname), as a new post stores
     * them: the tool wrote the username of every author for "nickname" (the logins which the site hides), and the real name, also an
     * empty one, for "real name".
     */
    private function rename(int $shown): array
    {
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['username' => $shown]);
        $this->replace(KunenaConfig::class, 'instance', $config);
        $field = $this->replace(KunenaUserName::class, 'displayNameField', null);

        // 50 has a real name, 52 has none; each wrote a message and its topic.
        $this->getDBDriver()->setQuery("UPDATE #__users SET name = '' WHERE id = 52")->execute();

        foreach ([50 => 1, 52 => 2] as $userid => $id) {
            $this->insertRow('#__kunena_messages', ['id' => $id, 'thread' => $id, 'catid' => 2, 'userid' => $userid, 'name' => 'old']);
            $this->insertRow('#__kunena_topics', ['id' => $id, 'category_id' => 2, 'first_post_userid' => $userid, 'first_post_guest_name' => 'old', 'last_post_userid' => $userid, 'last_post_guest_name' => 'old']);
        }

        (new ReflectionProperty(AdministratorApplication::class, 'input'))->setValue(Factory::$application, new Input(['userRename' => 1]));

        try {
            $this->syncUsers();
        } finally {
            $this->replace(KunenaUserName::class, 'displayNameField', $field);
        }

        return [
            'messages' => array_column($this->fetchAll('SELECT name FROM #__kunena_messages ORDER BY id'), 'name'),
            'topics'   => array_map(static fn ($row) => $row['first_post_guest_name'] . '/' . $row['last_post_guest_name'], $this->fetchAll('SELECT first_post_guest_name, last_post_guest_name FROM #__kunena_topics ORDER BY id')),
        ];
    }

    public function testTheNicknameSettingDoesNotShowTheUsernames(): void
    {
        // No nickname field: the real name, then the username, as the forum shows them.
        $this->assertSame(['messages' => ['User 50', 'user52'], 'topics' => ['User 50/User 50', 'user52/user52']], $this->rename(2));
    }

    public function testAnEmptyRealNameGivesTheUsername(): void
    {
        $this->assertSame(['messages' => ['User 50', 'user52'], 'topics' => ['User 50/User 50', 'user52/user52']], $this->rename(0));
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
