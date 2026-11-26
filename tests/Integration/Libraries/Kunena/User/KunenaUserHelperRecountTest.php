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
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * The posts of the users counted again from their topics (#__kunena_user_topics): of all the users, or of the users given only. A
 * topic counted again (KunenaTopic::update() after a post of a guest, a move of several posts...) counted the posts of every user of
 * the forum again; it counts those of its own users now.
 */
class KunenaUserHelperRecountTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    /**
     * The objects of Joomla which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        // The helper needs the framework of Joomla.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The helper reads the database of the container of Joomla: that is the database of the tests here. When the query
        // fails, the helper gives the error to the application, an administrator one, which throws it: the test fails with the
        // error of the database. Loading the helper makes the current user, a guest here: his table needs the dispatcher, and
        // he reads the configuration and the avatars, a bare configuration and the avatars of Kunena itself (no plugin).
        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'administrator');
        $application->method('enqueueMessage')->willReturnCallback(static function ($message) {
            throw new RuntimeException($message);
        });

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'avatars'     => $this->replace(KunenaAvatar::class, 'instance', new KunenaAvatar()),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        $this->insertRows('#__kunena_users', [
            ['userid' => 101, 'posts' => 0],
            ['userid' => 102, 'posts' => 0],
            ['userid' => 103, 'posts' => 9],
        ]);

        // 101 has posts in two topics, 102 in one, 103 none any more.
        $this->insertRows('#__kunena_user_topics', [
            ['user_id' => 101, 'topic_id' => 1, 'category_id' => 2, 'posts' => 2],
            ['user_id' => 101, 'topic_id' => 2, 'category_id' => 2, 'posts' => 3],
            ['user_id' => 102, 'topic_id' => 1, 'category_id' => 2, 'posts' => 4],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAvatar::class, 'instance', $this->saved['avatars']);
        }

        parent::tearDown();
    }

    /**
     * The posts of the users: user => posts.
     */
    private function posts(): array
    {
        $posts = [];

        foreach ($this->fetchAll('SELECT userid, posts FROM #__kunena_users ORDER BY userid') as $row) {
            $posts[(int) $row['userid']] = (int) $row['posts'];
        }

        return $posts;
    }

    public function testTheRecountOfAllTheUsers(): void
    {
        KunenaUserHelper::recount();

        $this->assertSame([101 => 5, 102 => 4, 103 => 0], $this->posts());
    }

    public function testTheRecountOfSomeUsersLeavesTheOthers(): void
    {
        KunenaUserHelper::recount([101, 103]);

        $this->assertSame([101 => 5, 102 => 0, 103 => 0], $this->posts());
    }

    public function testNoIdIsNoUser(): void
    {
        // Ids which are no ids (0, text) are left out: with nothing left, all the users are counted.
        KunenaUserHelper::recount([0, 'x']);

        $this->assertSame([101 => 5, 102 => 4, 103 => 0], $this->posts());
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
