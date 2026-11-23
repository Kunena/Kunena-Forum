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

namespace Kunena\Tests\Integration\Libraries\Kunena\Api;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Api\KunenaApi;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserName;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * The author of a topic and of a post in the REST API. A message keeps the name of its member as it was shown when he posted, and
 * HTML-escaped (KunenaMessage::check() stores getName()): the API gave that name, so after a change of the name (or of the setting
 * "username or real name") it gave the old one, the login name which the forum no longer shows, and "Tom &amp; Jerry" in the JSON.
 * The site shows the current name of the member; a guest keeps the name of his message.
 */
class KunenaApiAuthorTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

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

        // No category is cached: the messages load their category from the database. (Done first: the helper loads all the
        // categories when it is loaded, which the replaced application could not do.)
        $this->saved = [
            'categories' => $this->replace(KunenaCategoryHelper::class, '_instances', []),
            'host'       => $_SERVER['HTTP_HOST'] ?? null,
        ];
        $_SERVER['HTTP_HOST'] = 'localhost';

        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        // No router of the site: the addresses (not checked here) are made from the root of the site.
        $container->share('SiteRouter', static fn () => throw new RuntimeException('No router in the test'));
        $container->share('config', static fn () => new Registry(['live_site' => 'http://localhost/forum']));
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');

        // The forum shows the real names.
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['username' => KunenaUserName::REALNAME, 'teaser' => 0]);

        // No profile integration: KunenaUser::getName() chooses the name itself, as the Kunena profile does.
        $profile          = new KunenaProfile();
        $profile->enabled = false;

        $this->saved += [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'avatars'     => $this->replace(KunenaAvatar::class, 'instance', new KunenaAvatar()),
            'profile'     => $this->replace(KunenaProfile::class, 'instance', $profile),
            'access'      => $this->replace(KunenaAccess::class, 'instance', $this->createStub(KunenaAccess::class)),
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
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

        // 50: login "tom", real name "Tom & Jerry"; he posted when the forum showed the login names.
        $this->insertRow('#__users', ['id' => 50, 'name' => 'Tom & Jerry', 'username' => 'tom', 'email' => 'a@example.com', 'params' => '{}']);
        $this->insertRow('#__kunena_users', ['userid' => 50]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAvatar::class, 'instance', $this->saved['avatars']);
            $this->replace(KunenaProfile::class, 'instance', $this->saved['profile']);
            $this->replace(KunenaAccess::class, 'instance', $this->saved['access']);
            $this->replace(PluginHelper::class, 'plugins', $this->saved['plugins']);
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);

            if ($this->saved['host'] === null) {
                unset($_SERVER['HTTP_HOST']);
            } else {
                $_SERVER['HTTP_HOST'] = $this->saved['host'];
            }
            $this->replace(KunenaUserHelper::class, '_me', $this->saved['me']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['instances']);
            $this->replace(KunenaUserHelper::class, '_instances_name', $this->saved['byname']);
            $this->replace(KunenaUserName::class, 'displayNames', $this->saved['names']);
            $this->replace(KunenaUserName::class, 'displayNameField', $this->saved['field']);
        }

        parent::tearDown();
    }

    public function testTheAuthorOfATopicIsTheNameTheForumShowsNow(): void
    {
        $this->assertSame('Tom & Jerry', KunenaApi::topic($this->topic(50, 'tom'))->author);
    }

    public function testTheAuthorOfATopicOfAGuestIsTheNameOfHisMessage(): void
    {
        $this->assertSame('Visitor & Co', KunenaApi::topic($this->topic(0, 'Visitor & Co'))->author);
    }

    public function testTheAuthorOfAPostIsTheNameTheForumShowsNow(): void
    {
        $this->assertSame('Tom & Jerry', KunenaApi::post($this->message(50, 'tom'), KunenaUserHelper::get(50))->author);
        $this->assertSame('Visitor & Co', KunenaApi::post($this->message(0, 'Visitor & Co'), KunenaUserHelper::get(50))->author);
    }

    public function testADeletedMemberKeepsTheNameOfHisMessageAsText(): void
    {
        // The message keeps the name HTML-escaped (KunenaMessage::check() stored getName()).
        $this->assertSame('Old & Name', KunenaApi::topic($this->topic(99999999, 'Old &amp; Name'))->author);
    }

    /**
     * A topic whose first post has the author $userid, with the name stored in the topic.
     */
    private function topic(int $userid, string $name): KunenaTopic
    {
        $topic = (new ReflectionClass(KunenaTopic::class))->newInstanceWithoutConstructor();

        foreach (['id' => 1, 'category_id' => 2, 'subject' => 'Topic', 'first_post_id' => 1, 'first_post_userid' => $userid,
            'first_post_guest_name' => $name, 'posts' => 1] as $property => $value) {
            $topic->$property = $value;
        }

        return $topic;
    }

    /**
     * The first message of a topic in a published category, by $userid, with the name stored in the message.
     */
    private function message(int $userid, string $name): KunenaMessage
    {
        $this->replace(KunenaCategoryHelper::class, '_instances', []);
        $this->replace(KunenaTopicHelper::class, '_instances', []);
        $this->getDBDriver()->setQuery('DELETE FROM #__kunena_categories WHERE id = 2')->execute();
        $this->getDBDriver()->setQuery('DELETE FROM #__kunena_topics WHERE id = 1')->execute();
        $this->insertRow('#__kunena_categories', ['id' => 2, 'parentid' => 0, 'name' => 'Forum', 'alias' => 'forum', 'published' => 1, 'params' => '{}']);
        $this->insertRow('#__kunena_topics', ['id' => 1, 'category_id' => 2, 'subject' => 'Topic', 'first_post_id' => 1, 'first_post_userid' => $userid]);

        $message = (new ReflectionClass(KunenaMessage::class))->newInstanceWithoutConstructor();

        foreach (['id' => 1, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'userid' => $userid, 'name' => $name, 'subject' => 'Topic',
            'message' => '', 'time' => 1000] as $property => $value) {
            $message->$property = $value;
        }

        return $message;
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
