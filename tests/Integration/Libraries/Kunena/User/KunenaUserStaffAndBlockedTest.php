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
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * Two lists of users. The ignore list: a user who became a moderator after he was put on it is ignored no more (moderators can
 * not be ignored; the old ignore kept hiding his posts and his e-mails). The top posters of the statistics: not the users who
 * are blocked in Joomla (a banned spammer with many posts was one of them).
 */
class KunenaUserStaffAndBlockedTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const MEMBER = 101;

    private const MODERATOR = 102;

    private const OTHER = 103;

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

        // As KunenaUserHelperRecountTest: the database of the tests, a guest as the current user, a bare configuration.
        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'site');
        $application->method('enqueueMessage')->willReturnCallback(static function ($message) {
            throw new RuntimeException($message);
        });

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['popUserCount' => 5, 'superAdminUserlist' => 0, 'enableIgnore' => 1]);

        // The user 102 is a moderator of the forum.
        $access = $this->createStub(KunenaAccess::class);
        $access->method('isAdmin')->willReturn(false);
        $access->method('isModerator')->willReturnCallback(static fn ($user = null) => (int) (\is_object($user) ? $user->userid : $user) === self::MODERATOR);

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'avatars'     => $this->replace(KunenaAvatar::class, 'instance', new KunenaAvatar()),
            'access'      => $this->replace(KunenaAccess::class, 'instance', $access),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        // After the application of the test: loading the helper makes the current user (a guest).
        $this->saved['users']      = $this->replace(KunenaUserHelper::class, '_instances', []);
        $this->saved['topposters'] = $this->replace(KunenaUserHelper::class, '_topposters', null);
        $this->saved['lists']      = $this->replace(KunenaUserIgnore::class, 'lists', []);

        foreach ([self::MEMBER => ['member', 5, 0], self::MODERATOR => ['moderator', 9, 0], self::OTHER => ['spammer', 50, 1]] as $id => [$name, $posts, $block]) {
            $this->insertRow('#__users', ['id' => $id, 'name' => $name, 'username' => $name, 'email' => "$name@example.org", 'block' => $block, 'params' => '']);
            $this->insertRow('#__kunena_users', ['userid' => $id, 'posts' => $posts]);
        }
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAvatar::class, 'instance', $this->saved['avatars']);
            $this->replace(KunenaAccess::class, 'instance', $this->saved['access']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['users']);
            $this->replace(KunenaUserHelper::class, '_topposters', $this->saved['topposters']);
            $this->replace(KunenaUserIgnore::class, 'lists', $this->saved['lists']);
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

    public function testABlockedUserIsNoTopPoster(): void
    {
        $this->assertSame([self::MODERATOR, self::MEMBER], array_map(static fn ($row) => (int) $row->id, KunenaUserHelper::getTopPosters()));
    }
}
