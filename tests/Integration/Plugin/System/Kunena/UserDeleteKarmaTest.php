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

namespace Kunena\Tests\Integration\Plugin\System\Kunena;

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
use Joomla\CMS\Event\User\AfterDeleteEvent;
use Joomla\CMS\Event\User\AfterSaveEvent;
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
use Kunena\Forum\Plugin\System\Kunena\Extension\Kunena;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * A user who is deleted in Joomla loses the karma he gave and the karma he got. The karma which other users gave him stayed, as
 * rows of a user who is gone (Diagnostics lists them).
 */
class UserDeleteKarmaTest extends IntegrationTestCase implements DBTestInterface
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

        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_system_kunena/src/Extension/Kunena.php';

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
     * The user of the test: a number no user of a site has (the plugin also removes the avatar files of the user).
     */
    private const USER = 999001;

    /**
     * The topics which he rated get the rating of the ratings which are left: the rating is stored with the topic, and kept his.
     */
    public function testTheTopicsWhichTheDeletedUserRatedAreRatedAgain(): void
    {
        $this->insertRows('#__kunena_topics', [
            ['id' => 1, 'category_id' => 2, 'subject' => 'Rated by him only', 'rating' => 1],
            ['id' => 2, 'category_id' => 2, 'subject' => 'Rated by him and 7', 'rating' => 3],
        ]);
        $this->insertRows('#__kunena_rate', [
            ['topic_id' => 1, 'userid' => self::USER, 'rate' => 1, 'time' => '2026-01-01 00:00:00'],
            ['topic_id' => 2, 'userid' => self::USER, 'rate' => 1, 'time' => '2026-01-01 00:00:00'],
            ['topic_id' => 2, 'userid' => 7, 'rate' => 5, 'time' => '2026-01-01 00:00:00'],
        ]);

        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());

        $event = $this->createStub(AfterDeleteEvent::class);
        $event->method('getDeletingResult')->willReturn(true);
        $event->method('getUser')->willReturn(['id' => self::USER]);

        $plugin->onUserAfterDelete($event);

        $this->assertSame(
            ['1' => 0, '2' => 5],
            array_map('intval', array_column($this->fetchAll('SELECT id, rating FROM #__kunena_topics ORDER BY id'), 'rating', 'id'))
        );
    }

    public function testTheKarmaGivenByAndToTheDeletedUserIsRemoved(): void
    {
        foreach ([[self::USER, 7], [8, self::USER], [7, 8]] as [$from, $to]) {
            $this->insertRow('#__kunena_karma', ['userid' => $from, 'target_userid' => $to, 'time' => 1000]);
        }

        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());

        $event = $this->createStub(AfterDeleteEvent::class);
        $event->method('getDeletingResult')->willReturn(true);
        $event->method('getUser')->willReturn(['id' => self::USER]);

        $plugin->onUserAfterDelete($event);

        $this->assertEquals(
            [['userid' => 7, 'target_userid' => 8]],
            $this->fetchAll('SELECT userid, target_userid FROM #__kunena_karma ORDER BY id')
        );
    }

    /**
     * His votes are taken off the options of the polls: the results had a vote more than the voters.
     */
    public function testTheVotesOfTheDeletedUserAreTakenOffThePolls(): void
    {
        $this->insertRows('#__kunena_polls_options', [
            ['id' => 1, 'pollid' => 1, 'text' => 'Yes', 'votes' => 2],
            ['id' => 2, 'pollid' => 1, 'text' => 'No', 'votes' => 1],
        ]);
        $this->insertRows('#__kunena_polls_users', [
            ['pollid' => 1, 'userid' => self::USER, 'votes' => 1, 'lasttime' => '2026-01-01 00:00:00', 'lastvote' => 1],
            ['pollid' => 1, 'userid' => 7, 'votes' => 1, 'lasttime' => '2026-01-01 00:00:00', 'lastvote' => 1],
            ['pollid' => 1, 'userid' => 8, 'votes' => 1, 'lasttime' => '2026-01-01 00:00:00', 'lastvote' => 2],
        ]);

        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());

        $event = $this->createStub(AfterDeleteEvent::class);
        $event->method('getDeletingResult')->willReturn(true);
        $event->method('getUser')->willReturn(['id' => self::USER]);

        $plugin->onUserAfterDelete($event);

        $this->assertSame(
            ['1' => 1, '2' => 1],
            array_map('intval', array_column($this->fetchAll('SELECT id, votes FROM #__kunena_polls_options ORDER BY id'), 'votes', 'id'))
        );
    }

    /**
     * A language chosen in Joomla (by the user or an administrator) is the language of the forum profile at once: it was stored only
     * at the login, so the mails of the subscriptions stayed in the old language until the next login. (The save of a user, next to
     * his delete: both are the user events of the plugin.)
     */
    public function testTheLanguageChosenInJoomlaIsTheLanguageOfTheProfile(): void
    {
        $this->insertRow('#__users', ['id' => 7, 'name' => 'User 7', 'username' => 'user7', 'registerDate' => '2026-01-01 00:00:00', 'params' => '']);
        $this->insertRow('#__kunena_users', ['userid' => 7, 'language' => 'en-GB']);

        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());

        $event = $this->createStub(AfterSaveEvent::class);
        $event->method('getSavingResult')->willReturn(true);
        $event->method('getIsNew')->willReturn(false);
        $event->method('getUser')->willReturn(['id' => 7, 'params' => '{"language":"de-DE"}']);

        $plugin->onUserAfterSave($event);

        $this->assertSame('de-DE', $this->fetchAll('SELECT language FROM #__kunena_users WHERE userid = 7')[0]['language']);
    }

    /**
     * The thanks which he gave are removed and the users whom he thanked are counted again: the post counted his thanks without
     * naming him, and the user kept them in his count. The thanks to his posts, which stay, are kept.
     */
    public function testTheThanksGivenByTheDeletedUserAreRemovedAndRecounted(): void
    {
        $this->insertRows('#__users', [
            ['id' => 7, 'name' => 'User 7', 'username' => 'user7', 'registerDate' => '2026-01-01 00:00:00', 'params' => ''],
            ['id' => 8, 'name' => 'User 8', 'username' => 'user8', 'registerDate' => '2026-01-01 00:00:00', 'params' => ''],
        ]);
        $this->insertRow('#__kunena_users', ['userid' => 7, 'thankyou' => 2]);

        foreach ([[1, self::USER, 7], [2, 8, 7], [3, 8, self::USER]] as [$post, $from, $to]) {
            $this->insertRow('#__kunena_thankyou', ['postid' => $post, 'userid' => $from, 'targetuserid' => $to, 'time' => '2026-01-01 00:00:00']);
        }

        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());

        $event = $this->createStub(AfterDeleteEvent::class);
        $event->method('getDeletingResult')->willReturn(true);
        $event->method('getUser')->willReturn(['id' => self::USER]);

        $plugin->onUserAfterDelete($event);

        $this->assertEquals(
            [['postid' => 2, 'userid' => 8], ['postid' => 3, 'userid' => 8]],
            $this->fetchAll('SELECT postid, userid FROM #__kunena_thankyou ORDER BY postid')
        );
        $this->assertSame(1, (int) $this->fetchAll('SELECT thankyou FROM #__kunena_users WHERE userid = 7')[0]['thankyou']);
    }
}
