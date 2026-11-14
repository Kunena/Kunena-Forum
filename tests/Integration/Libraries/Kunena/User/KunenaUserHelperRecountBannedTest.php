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
 * The recount of the banned state of the users after a ban or an unban in the frontend (issue #10344): the query failed on
 * MariaDB with a syntax error, shown after the ban.
 */
class KunenaUserHelperRecountBannedTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const BANNED_USER = 101;

    private const OTHER_USER = 102;

    private const OTHER_BANNED = '2001-02-03 04:05:06';

    private const FOREVER_USER = 103;

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
            ['userid' => self::BANNED_USER, 'banned' => '1000-01-01 00:00:00'],
            ['userid' => self::OTHER_USER, 'banned' => self::OTHER_BANNED],
            ['userid' => self::FOREVER_USER, 'banned' => '1000-01-01 00:00:00'],
        ]);

        // Several bans of one user, the latest one not the last row, a ban without expiration of another user, and a ban of an
        // address only, later than all the others: it must not be given to a user.
        $this->insertRows('#__kunena_users_banned', [
            ['userid' => self::BANNED_USER, 'expiration' => '2030-01-01 00:00:00', 'created_by' => 42, 'created_time' => '2026-01-01 00:00:00'],
            ['userid' => self::BANNED_USER, 'expiration' => '2031-06-15 12:00:00', 'created_by' => 42, 'created_time' => '2026-01-02 00:00:00'],
            ['userid' => self::BANNED_USER, 'expiration' => '2029-12-31 23:59:59', 'created_by' => 42, 'created_time' => '2026-01-03 00:00:00'],
            ['userid' => self::FOREVER_USER, 'expiration' => null, 'created_by' => 42, 'created_time' => '2026-01-04 00:00:00'],
            ['userid' => null, 'ip' => '192.0.2.1', 'expiration' => '2040-01-01 00:00:00', 'created_by' => 42, 'created_time' => '2026-01-04 00:00:00'],
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

    public function testTheRecountGivesEveryBannedUserHisLatestExpiration(): void
    {
        $affected = KunenaUserHelper::recountBanned();

        // Only the two banned users changed: the user without bans keeps his value, the ban of an address changes nobody.
        $this->assertSame(2, $affected);

        $users = array_column($this->fetchAll('SELECT userid, banned FROM #__kunena_users ORDER BY userid'), 'banned', 'userid');

        $this->assertSame(
            [self::BANNED_USER => '2031-06-15 12:00:00', self::OTHER_USER => self::OTHER_BANNED, self::FOREVER_USER => null],
            $users
        );

        // A second recount changes nothing.
        $this->assertSame(0, KunenaUserHelper::recountBanned());
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
