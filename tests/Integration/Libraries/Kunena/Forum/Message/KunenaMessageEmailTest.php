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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Message;

use Joomla\CMS\Application\ConsoleApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Kunena\Forum\Libraries\Email\KunenaEmailUnsubscribe;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageSchedule;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * The e-mails of the messages: the e-mail to a user who is mentioned in a post ([mention userid=43]name[/mention]) and the headers of
 * the notifications. The test keeps the e-mails instead of sending them (the mailer of the container gives a Mail which only records).
 */
class KunenaMessageEmailTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    private const TOPIC = 1;

    private const AUTHOR = 42;

    private const MENTIONED = 43;

    private const FIRST = 10;

    private const REPLY = 11;

    private const NOW = 1_700_000_000;

    private array $saved = [];

    private array $userColumns = [];

    private bool $deletedTime = false;

    private bool $userLanguage = false;

    /**
     * The e-mails which were sent: [address, subject, body].
     */
    private array $mails = [];

    /**
     * The languages which were loaded: [file, language].
     */
    private array $loaded = [];

    /**
     * The users of Joomla, as the user factory gives them.
     */
    private array $joomlaUsers = [];

    private array $options = [];

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

        // As in KunenaMessageScheduleTest, with the e-mails on: the mailer keeps them.
        $driver      = $this->getDBDriver();
        $dispatcher  = new Dispatcher();
        $container   = new Container();
        $userFactory = $this->createStub(UserFactoryInterface::class);
        $userFactory->method('loadUserById')->willReturnCallback(function (int $id): User {
            if (!isset($this->joomlaUsers[$id])) {
                $user            = new User();
                $user->id        = $id;
                $user->sendEmail = 1;
                $this->joomlaUsers[$id] = $user;
            }

            return $this->joomlaUsers[$id];
        });
        $test  = $this;
        $mails = $this->createStub(MailerFactoryInterface::class);
        $mails->method('createMailer')->willReturnCallback(static fn () => new class ($test) extends Mail {
            public function __construct(private $test)
            {
                parent::__construct();
            }

            public function send()
            {
                $this->test->sent($this->getToAddresses(), $this->Subject, $this->Body);

                return true;
            }
        });
        $joomlaConfig = new Registry(['live_site' => 'https://forum.example', 'mailonline' => 1, 'sitename' => 'Forum', 'secret' => 'test-secret']);
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));
        $container->share(UserFactoryInterface::class, static fn () => $userFactory);
        $container->share(MailerFactoryInterface::class, static fn () => $mails);
        $container->share('config', static fn () => $joomlaConfig);
        $language = $this->createStub(Language::class);
        $language->method('transliterate')->willReturnArgument(0);
        $language->method('getTag')->willReturn('en-GB');
        $language->method('_')->willReturnArgument(0);
        $language->method('load')->willReturnCallback(function ($file, $path = '', $lang = null) {
            $this->loaded[] = [$file, $lang];

            return true;
        });
        $application = $this->createStub(ConsoleApplication::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');
        $application->method('getConfig')->willReturn($joomlaConfig);
        $application->method('get')->willReturnCallback(static fn ($name, $default = null) => $joomlaConfig->get($name, $default));
        $application->method('getLanguage')->willReturn($language);

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, [
            'threadsPerPage'        => 20,
            'ratingEnabled'         => 0,
            'readOnly'              => 0,
            'askEmail'              => 1,
            'floodProtection'       => 0,
            'attachmentLimit'       => 0,
            'showNew'               => 1,
            'pubWrite'              => 0,
            'sendEmails'            => 1,
            'mentionEmail'          => 1,
            'fallbackEnglish'       => 0,
            'useMailTemplates'      => 0,
            'useSystemEmails'       => 0,
            'emailSenderName'       => '',
            'email'                 => 'forum@forum.example',
            'boardTitle'            => 'Forum',
            'allowSubscriptions'    => 0,
            'mailModerators'        => 0,
            'mailAdministrators'    => 0,
        ]);
        $this->options = ['config' => $config];

        $access = $this->createStub(KunenaAccess::class);
        $access->method('getAllowedCategories')->willReturn([self::CATEGORY => self::CATEGORY]);
        $access->method('getAllowedHold')->willReturn([0]);
        $access->method('getSubscribers')->willReturn([]);

        $this->saved = [
            'categories'  => $this->replace(KunenaCategoryHelper::class, '_instances', []),
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'access'      => $this->replace(KunenaAccess::class, 'instance', $access),
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
            'uriBase'     => $this->replace(Uri::class, 'base', []),
            'uriRoot'     => $this->replace(Uri::class, 'root', []),
            'uris'        => $this->replace(Uri::class, 'instances', []),
            'host'        => $_SERVER['HTTP_HOST'] ?? null,
        ];

        $_SERVER['HTTP_HOST'] = 'forum.example';

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        $this->saved['me']    = $this->replace(KunenaUserHelper::class, '_me', new KunenaUser(0));
        $this->saved['users'] = $this->replace(KunenaUserHelper::class, '_instances', []);
        $this->saved['params'] = $this->replace(KunenaUserHelper::class, 'joomlaParams', []);

        $columns = array_column($this->fetchAll('SHOW COLUMNS FROM #__users'), 'Field');

        foreach (['registerDate' => 'datetime NOT NULL', 'lastvisitDate' => 'datetime NULL'] as $column => $type) {
            if (!\in_array($column, $columns, true)) {
                $driver->setQuery("ALTER TABLE #__users ADD $column $type")->execute();
                $this->userColumns[] = $column;
            }
        }

        // The language of a user is a column which the upgrade of the installation adds (kunena.install.upgrade.xml).
        if (!\in_array('language', array_column($this->fetchAll('SHOW COLUMNS FROM #__kunena_users'), 'Field'), true)) {
            $driver->setQuery('ALTER TABLE #__kunena_users ADD language text NULL')->execute();
            $this->userLanguage = true;
        }

        // The language files of the e-mail are read from the folder of the site of Kunena.
        if (!\defined('KPATH_SITE')) {
            \define('KPATH_SITE', \dirname(__DIR__, 6) . '/src/site');
        }

        $driver->setQuery("ALTER TABLE #__kunena_messages ADD deleted_time int(11) NOT NULL default '0' AFTER moved")->execute();
        $this->deletedTime = true;

        $this->insertRow('#__users', ['id' => self::AUTHOR, 'name' => 'Author', 'username' => 'author', 'email' => 'author@forum.example', 'params' => '', 'registerDate' => '2020-01-01 00:00:00']);
        $this->insertRow('#__users', ['id' => self::MENTIONED, 'name' => 'Anna', 'username' => 'anna', 'email' => 'anna@forum.example', 'params' => '', 'registerDate' => '2020-01-01 00:00:00']);

        DBTestHelper::markDirty('#__kunena_user_topics');
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
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['users']);
            $this->replace(KunenaUserHelper::class, '_me', $this->saved['me']);
            $this->replace(KunenaUserHelper::class, 'joomlaParams', $this->saved['params']);
            $this->replace(Uri::class, 'base', $this->saved['uriBase']);
            $this->replace(Uri::class, 'root', $this->saved['uriRoot']);
            $this->replace(Uri::class, 'instances', $this->saved['uris']);

            if ($this->saved['host'] === null) {
                unset($_SERVER['HTTP_HOST']);
            } else {
                $_SERVER['HTTP_HOST'] = $this->saved['host'];
            }

            $this->replace(KunenaTopicHelper::class, '_instances', []);
            $this->replace(KunenaMessageHelper::class, '_instances', []);
            $this->replace(KunenaTopicUserHelper::class, '_instances', []);
            $this->replace(KunenaTopicUserHelper::class, '_topics', []);
        }

        if ($this->userLanguage) {
            $this->getDBDriver()->setQuery('ALTER TABLE #__kunena_users DROP language')->execute();
        }

        if ($this->deletedTime) {
            $this->getDBDriver()->setQuery('ALTER TABLE #__kunena_messages DROP deleted_time')->execute();
        }

        foreach ($this->userColumns as $column) {
            $this->getDBDriver()->setQuery("ALTER TABLE #__users DROP $column")->execute();
        }

        parent::tearDown();
    }

    /**
     * Called by the mailer of the test for each e-mail.
     */
    public function sent(array $to, string $subject, string $body): void
    {
        $this->mails[] = [implode(',', array_column($to, 0)), $subject, $body];
    }

    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }

    private function option(string $name, mixed $value): void
    {
        $property = new ReflectionProperty(KunenaConfig::class, 'config');
        $values   = $property->getValue($this->options['config']);
        $values[$name] = $value;
        $property->setValue($this->options['config'], $values);
    }

    /**
     * The topic of the author with its first post, and his reply with the given text.
     */
    private function seed(string $replyText, int $replyHold = KunenaForum::PUBLISHED, int $scheduledTime = 0): void
    {
        $this->insertRow('#__kunena_users', ['userid' => self::AUTHOR, 'posts' => 1]);
        $this->insertRow('#__kunena_users', ['userid' => self::MENTIONED, 'posts' => 0]);
        $this->insertRow('#__kunena_categories', ['id' => 1, 'parentid' => 0, 'name' => 'Section', 'alias' => '1-section', 'published' => 1]);
        $this->insertRow('#__kunena_categories', ['id' => self::CATEGORY, 'parentid' => 1, 'name' => 'Category', 'alias' => '2-category', 'published' => 1, 'numTopics' => 1, 'numPosts' => 1]);
        $this->insertRow('#__kunena_aliases', ['alias' => '2-category', 'type' => 'catid', 'item' => self::CATEGORY]);
        $this->insertRow('#__kunena_topics', [
            'id' => self::TOPIC, 'category_id' => self::CATEGORY, 'subject' => 'Topic', 'hold' => 0, 'posts' => 1,
            'first_post_id' => self::FIRST, 'first_post_time' => 1000, 'first_post_userid' => self::AUTHOR,
            'last_post_id' => self::FIRST, 'last_post_time' => 1000, 'last_post_userid' => self::AUTHOR,
        ]);
        $this->insertRow('#__kunena_user_topics', ['user_id' => self::AUTHOR, 'topic_id' => self::TOPIC, 'category_id' => self::CATEGORY, 'posts' => 1, 'last_post_id' => self::FIRST, 'owner' => 1]);

        foreach ([[self::FIRST, 0, KunenaForum::PUBLISHED, 1000, 0, 'First'], [self::REPLY, self::FIRST, $replyHold, 1100, $scheduledTime, $replyText]] as [$id, $parent, $hold, $time, $scheduled, $text]) {
            $this->insertRow('#__kunena_messages', [
                'id' => $id, 'parent' => $parent, 'thread' => self::TOPIC, 'catid' => self::CATEGORY, 'userid' => self::AUTHOR,
                'name' => 'Author', 'subject' => 'Topic', 'time' => $time, 'hold' => $hold, 'scheduled_time' => $scheduled,
            ]);
            $this->insertRow('#__kunena_messages_text', ['mesid' => $id, 'message' => $text]);
        }
    }

    private function mention(): string
    {
        return '[mention userid=' . self::MENTIONED . ']anna[/mention]';
    }






    /**
     * The headers which the notification of the reply gives to the e-mail of the mentioned user (as a subscriber).
     */
    private function notificationHeaders(): array
    {
        $mail = new Mail();
        (new ReflectionMethod(KunenaMessageHelper::get(self::REPLY), 'addNotificationHeaders'))->invoke(KunenaMessageHelper::get(self::REPLY), $mail, 1, self::MENTIONED, false);

        return array_column($mail->getCustomHeaders(), 1, 0);
    }



    /**
     * A header is text: the name of the author was escaped for HTML ("Tom &amp; Jerry").
     */
    public function testTheAuthorHeaderIsTheNameAsText(): void
    {
        $this->seed('A reply');
        $this->getDBDriver()->setQuery("UPDATE #__users SET name = 'Tom & Jerry', username = 'Tom & Jerry' WHERE id = " . self::AUTHOR)->execute();
        $this->getDBDriver()->setQuery("UPDATE #__kunena_messages SET name = 'Tom & Jerry' WHERE id = " . self::REPLY)->execute();
        $this->option('emailForumHeaders', 1);

        $this->assertSame('Tom & Jerry', $this->notificationHeaders()['X-Forum-Who'] ?? null);
    }
}
