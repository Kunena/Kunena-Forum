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
 * The scheduled task "Purge the trash": every message which is in the trash for longer than the age of the task is deleted for good.
 * The task took the trashed messages from the list of the backend, one page of it (the list limit of the site, 20 by default, in
 * the order of the ids, with the search of the session which runs the task): the other messages of the trash were left, and a
 * message trashed lately on that page kept the older ones after it in the trash.
 */
class PurgeTrashTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    private array $userColumns = [];

    private bool $deletedTime = false;

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

        $driver->setQuery("ALTER TABLE #__kunena_messages ADD deleted_time int(11) NOT NULL default '0' AFTER moved")->execute();
        $this->deletedTime = true;

        DBTestHelper::markDirty('#__kunena_user_topics');

        // A published topic with its first post, and three replies in the trash since long ago (one of them trashed later than the
        // others, but long ago too), and one reply trashed now, which stays.
        $this->insertRow('#__users', ['id' => 42, 'name' => 'Author', 'username' => 'author', 'params' => '', 'registerDate' => '2020-01-01 00:00:00']);
        $this->insertRow('#__kunena_users', ['userid' => 42, 'posts' => 1]);
        $this->insertRow('#__kunena_categories', ['id' => 2, 'parentid' => 0, 'name' => 'Category', 'alias' => '2-category', 'published' => 1,
            'numTopics' => 1, 'numPosts' => 1]);
        $this->insertRow('#__kunena_aliases', ['alias' => '2-category', 'type' => 'catid', 'item' => 2]);
        $this->insertRow('#__kunena_topics', ['id' => 1, 'category_id' => 2, 'subject' => 'Topic', 'hold' => 0, 'posts' => 1,
            'first_post_id' => 1, 'first_post_time' => 100, 'first_post_userid' => 42, 'last_post_id' => 1, 'last_post_time' => 100,
            'last_post_userid' => 42]);

        foreach ([[1, 0, 0], [2, 2, 1000], [3, 2, 1000], [4, 2, 5000], [5, 2, time()]] as [$id, $hold, $deleted]) {
            $this->insertRow('#__kunena_messages', ['id' => $id, 'parent' => $id === 1 ? 0 : 1, 'thread' => 1, 'catid' => 2, 'userid' => 42,
                'subject' => 'Topic', 'time' => 100 + $id, 'hold' => $hold, 'deleted_time' => $deleted]);
            $this->insertRow('#__kunena_messages_text', ['mesid' => $id, 'message' => "Message $id"]);
        }
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
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['users']);
            $this->replace(KunenaUserHelper::class, '_me', $this->saved['me']);
            $this->replace(KunenaTopicHelper::class, '_instances', []);
            $this->replace(KunenaMessageHelper::class, '_instances', []);
            $this->replace(KunenaTopicUserHelper::class, '_instances', []);
            $this->replace(KunenaTopicUserHelper::class, '_topics', []);
        }

        if ($this->deletedTime) {
            $this->getDBDriver()->setQuery('ALTER TABLE #__kunena_messages DROP deleted_time')->execute();
        }

        foreach ($this->userColumns as $column) {
            $this->getDBDriver()->setQuery("ALTER TABLE #__users DROP $column")->execute();
        }

        parent::tearDown();
    }

    public function testEveryMessageOfTheTrashOlderThanTheAgeIsPurged(): void
    {
        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());

        $event = $this->createStub(ExecuteTaskEvent::class);
        $event->method('getArgument')->willReturnCallback(static fn ($name, $default = null) => $name === 'params' ? (object) ['age' => 31] : $default);

        (new ReflectionMethod(Kunena::class, 'purgeTrash'))->invoke($plugin, $event);

        $this->assertSame([['id' => 1], ['id' => 5]], $this->fetchAll('SELECT id FROM #__kunena_messages ORDER BY id'));
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
