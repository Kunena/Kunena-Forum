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
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\ComponentRecord;
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
use Kunena\Tests\Integration\SchemaGenerator;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * Tools > "Set up socials" repairs a forum whose update to 6.4 failed: it makes the column socials, moves the social websites of the
 * users from their own columns into it, and removes those columns. Only the users who were never banned were moved, the value of
 * the column twitter was read as $value->twiter (nothing), and a value with a quote made the text of the column no JSON: those
 * social websites were lost with the columns.
 */
class ToolsControllerSetupSocialsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    /**
     * The columns of the social websites of a forum before 6.4 (the tool reads all of them).
     */
    private const OLD_COLUMNS = [
        'twitter', 'facebook', 'myspace', 'linkedin', 'linkedin_company', 'digg', 'skype', 'yim', 'google', 'github', 'microsoft',
        'blogspot', 'flickr', 'bebo', 'instagram', 'qqsocial', 'qzone', 'weibo', 'wechat', 'vk', 'telegram', 'apple', 'vimeo',
        'whatsapp', 'youtube', 'ok', 'pinterest', 'reddit', 'icq',
    ];

    private array $saved = [];

    private bool $altered = false;

    private mixed $savedServer = null;

    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));
        $userFactory = $this->createStub(UserFactoryInterface::class);
        $userFactory->method('loadUserById')->willReturnCallback(static fn () => new User());
        $container->share(UserFactoryInterface::class, static fn () => $userFactory);

        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        // The administrator application (no constructor) with a session and the secret which make the form token. The tool ends
        // with a redirect, which stops the test where the request would end.
        $session = $this->createStub(Session::class);
        $session->method('getToken')->willReturn('token');
        $application = $this->getMockBuilder(AdministratorApplication::class)->disableOriginalConstructor()->onlyMethods(['redirect'])->getMock();
        $application->expects($this->once())->method('redirect')->willThrowException(new RuntimeException('redirect'));
        $application->setDispatcher(new Dispatcher());
        $application->setSession($session);
        $application->setContainer($container);
        $application->loadIdentity(new User());
        (new ReflectionProperty(AdministratorApplication::class, 'name'))->setValue($application, 'administrator');
        (new ReflectionProperty(AdministratorApplication::class, 'config'))->setValue($application, new Registry(['secret' => 'secret']));
        (new ReflectionProperty(AdministratorApplication::class, 'language'))->setValue($application, $language);
        (new ReflectionProperty(AdministratorApplication::class, 'input'))->setValue($application, new Input([]));

        $router = $this->createStub(Router::class);
        $router->method('build')->willReturnCallback(static fn ($url) => new Uri($url));

        // The socials read the providers of the admin from the parameters of the component: none here.
        $record         = new ComponentRecord(['option' => 'com_kunena', 'enabled' => 1]);
        $record->params = new Registry();
        $components     = new ReflectionProperty(ComponentHelper::class, 'components');

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'components'  => $components->getValue(),
            'config'      => $this->replace(KunenaConfig::class, 'instance', (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor()),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;
        $components->setValue(null, ['com_kunena' => $record]);

        class_exists(KunenaRoute::class);
        $this->saved['adminApp'] = $this->replace(KunenaRoute::class, 'adminApp', true);
        $this->saved['routers']  = $this->replace(Route::class, '_router', ['administrator' => $router]);

        // The token of the form in its header.
        $this->savedServer            = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        $_SERVER['HTTP_X_CSRF_TOKEN'] = Session::getFormToken();

        // The table of the users as a forum before 6.4 has it: no column socials, a column for each social website.
        $this->altered = true;
        $driver->setQuery('ALTER TABLE #__kunena_users DROP socials')->execute();

        foreach (self::OLD_COLUMNS as $column) {
            $driver->setQuery('ALTER TABLE #__kunena_users ADD ' . $driver->quoteName($column) . ' varchar(75) NULL')->execute();
        }
    }

    protected function tearDown(): void
    {
        if ($this->altered) {
            // The table of the schema again, for the next tests.
            $driver = $this->getDBDriver();
            $driver->setQuery('DROP TABLE IF EXISTS #__kunena_users')->execute();
            $driver->setQuery(SchemaGenerator::createStatement(SchemaGenerator::tables()['kunena_users']))->execute();
        }

        if ($this->saved) {
            if ($this->savedServer === null) {
                unset($_SERVER['HTTP_X_CSRF_TOKEN']);
            } else {
                $_SERVER['HTTP_X_CSRF_TOKEN'] = $this->savedServer;
            }

            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            (new ReflectionProperty(ComponentHelper::class, 'components'))->setValue(null, $this->saved['components']);
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaRoute::class, 'adminApp', $this->saved['adminApp']);
            $this->replace(Route::class, '_router', $this->saved['routers']);
        }

        parent::tearDown();
    }

    public function testTheSocialsOfEveryUserAreMovedIntoTheColumnSocials(): void
    {
        // 70: never banned. 71: banned once (his ban is over). 72: his X account in the column twitter, a quote in a value.
        $this->insertRows('#__kunena_users', [
            ['userid' => 70, 'banned' => '1000-01-01 00:00:00', 'facebook' => 'fb70'],
            ['userid' => 71, 'banned' => '2020-01-01 00:00:00', 'facebook' => 'fb71', 'github' => 'gh71'],
            ['userid' => 72, 'banned' => '1000-01-01 00:00:00', 'twitter' => 'tw72', 'facebook' => 'say "hi"'],
        ]);

        $controller = (new ReflectionClass(ToolsController::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(ToolsController::class, 'app'))->setValue($controller, Factory::$application);
        (new ReflectionProperty(ToolsController::class, 'input'))->setValue($controller, Factory::$application->getInput());
        (new ReflectionProperty(ToolsController::class, 'baseurl'))->setValue($controller, 'administrator/index.php?option=com_kunena&view=tools');

        try {
            $controller->setupsocials();
        } catch (RuntimeException $e) {
            $this->assertSame('redirect', $e->getMessage());
        }

        $socials = [];

        foreach ($this->fetchAll('SELECT userid, socials FROM #__kunena_users ORDER BY userid') as $row) {
            $socials[$row['userid']] = json_decode((string) $row['socials'], true);
        }

        $this->assertSame('fb70', $socials[70]['facebook']['value'] ?? null);
        $this->assertSame('fb71', $socials[71]['facebook']['value'] ?? null, 'The user who was banned once lost his socials.');
        $this->assertSame('gh71', $socials[71]['github']['value'] ?? null);
        $this->assertSame('tw72', $socials[72]['x_social']['value'] ?? null, 'The column twitter was not moved.');
        $this->assertSame('say "hi"', $socials[72]['facebook']['value'] ?? null, 'A quote in a value broke the JSON.');

        // The old columns are removed.
        $columns = array_column($this->fetchAll('SHOW COLUMNS FROM #__kunena_users'), 'Field');

        $this->assertSame([], array_values(array_intersect(self::OLD_COLUMNS, $columns)));
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
