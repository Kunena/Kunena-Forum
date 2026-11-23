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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Tools > Synchronize users > "Delete lifetime ban users": the Joomla accounts of the members banned for life which are blocked are
 * deleted, with their Kunena profile. The profiles of all the members banned for life were deleted, also of those whose account
 * stays (a ban of the forum does not block the account): the ban is the column "banned" of the profile, so they were unbanned.
 */
class ToolsControllerSyncUsersTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const LIFETIME = '9999-12-31 23:59:59';

    private array $saved = [];

    private mixed $savedServer = null;

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
        $userFactory->method('loadUserById')->willReturnCallback(static fn () => new User());
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
        $application->loadIdentity(new User());
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
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        class_exists(KunenaRoute::class);
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
            $this->replace(KunenaRoute::class, 'adminApp', $this->saved['adminApp']);
            $this->replace(Route::class, '_router', $this->saved['routers']);
        }

        parent::tearDown();
    }

    public function testOnlyTheBlockedAccountsOfTheLifetimeBannedAreDeletedAndTheOtherBansStay(): void
    {
        $controller = (new ReflectionClass(ToolsController::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(ToolsController::class, 'app'))->setValue($controller, Factory::$application);
        (new ReflectionProperty(ToolsController::class, 'input'))->setValue($controller, Factory::$application->getInput());
        (new ReflectionProperty(ToolsController::class, 'baseurl'))->setValue($controller, 'administrator/index.php?option=com_kunena&view=tools');

        $controller->syncUsers();

        $this->assertSame([['id' => 50], ['id' => 52]], $this->fetchAll('SELECT id FROM #__users ORDER BY id'));
        $this->assertSame([['userid' => 50, 'banned' => self::LIFETIME], ['userid' => 52, 'banned' => '1000-01-01 00:00:00']], $this->fetchAll('SELECT userid, banned FROM #__kunena_users ORDER BY userid'));
        $this->assertSame([['user_id' => 50], ['user_id' => 52]], $this->fetchAll('SELECT user_id FROM #__user_usergroup_map ORDER BY user_id'));
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
