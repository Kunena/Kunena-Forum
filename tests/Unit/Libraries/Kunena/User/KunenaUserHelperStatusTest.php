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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The member viewing the page is never shown as away or offline to themselves (issue #6196); the others keep the rules of the
 * session table.
 */
class KunenaUserHelperStatusTest extends UnitTestCase
{
    private const int ME = 42;

    private const int OTHER = 99;

    /**
     * The values which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        // The helper and the user need the framework of Joomla.
        $this->requireJoomla();

        // The configuration with the status of the users on, and a site application with a session lifetime of 15 minutes.
        $config = self::bare(KunenaConfig::class, ['config' => ['userStatus' => 1]]);
        $site   = $this->createStub(CMSApplicationInterface::class);
        $site->method('get')->willReturnArgument(1);
        $site->method('getDispatcher')->willReturn(new Dispatcher());

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'application' => Factory::$application,
        ];

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $config);
        Factory::$application = $site;

        $this->loadHelper();

        foreach (['_me', '_instances', '_online'] as $name) {
            $this->saved[$name] = (new ReflectionProperty(KunenaUserHelper::class, $name))->getValue();
        }
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        Factory::$application = $this->saved['application'];

        foreach (['_me', '_instances', '_online'] as $name) {
            (new ReflectionProperty(KunenaUserHelper::class, $name))->setValue(null, $this->saved[$name]);
        }
    }

    /**
     * Loading the helper runs KunenaUserHelper::initialize(), which makes the current user of the site: it gets a stand-in of
     * the database and the avatars of Kunena itself, so that no database and no plugin is used.
     */
    private function loadHelper(): void
    {
        if (class_exists(KunenaUserHelper::class, false)) {
            return;
        }

        $db = $this->createStub(DatabaseDriver::class);
        $db->method('getTableColumns')->willReturn(['userid' => 'int', 'status' => 'tinyint']);

        $container = (new Container())->set(DatabaseInterface::class, $db)->set('DatabaseDriver', $db);
        $avatars   = new ReflectionProperty(KunenaAvatar::class, 'instance');
        $saved     = [Factory::$container, $avatars->getValue()];

        Factory::$container = $container;
        $avatars->setValue(null, new KunenaAvatar());

        try {
            class_exists(KunenaUserHelper::class);
        } finally {
            Factory::$container = $saved[0];
            $avatars->setValue(null, $saved[1]);
        }
    }

    /**
     * The current user and another user with the given statuses, and the session rows of the given ages in seconds.
     */
    private function forum(int $myStatus = 0, int $otherStatus = 0, array $ages = []): void
    {
        $me    = self::bare(KunenaUser::class, ['userid' => self::ME, 'status' => $myStatus]);
        $other = self::bare(KunenaUser::class, ['userid' => self::OTHER, 'status' => $otherStatus]);
        $rows  = [];

        foreach ($ages as $userid => $age) {
            $rows[$userid] = (object) ['userid' => $userid, 'time' => time() - $age];
        }

        (new ReflectionProperty(KunenaUserHelper::class, '_me'))->setValue(null, $me);
        (new ReflectionProperty(KunenaUserHelper::class, '_instances'))->setValue(null, [self::ME => $me, self::OTHER => $other]);
        (new ReflectionProperty(KunenaUserHelper::class, '_online'))->setValue(null, $rows);
    }

    public function testTheCurrentUserWithAnOldSessionGetsTheirOwnStatus(): void
    {
        $this->forum(ages: [self::ME => 120]);
        $this->assertSame(0, KunenaUserHelper::getStatus(self::ME));

        $this->forum(myStatus: 2, ages: [self::ME => 120]);
        $this->assertSame(2, KunenaUserHelper::getStatus(self::ME));
    }

    public function testTheCurrentUserWithoutASessionGetsTheirOwnStatus(): void
    {
        $this->forum();
        $this->assertSame(0, KunenaUserHelper::getStatus(self::ME));

        $this->forum(myStatus: 3);
        $this->assertSame(3, KunenaUserHelper::getStatus(self::ME), 'An invisible member sees "Invisible", not "Offline".');
    }

    public function testAnotherUserWithAnOldSessionIsStillAway(): void
    {
        $this->forum(ages: [self::OTHER => 120]);

        $this->assertSame(1, KunenaUserHelper::getStatus(self::OTHER));
    }

    public function testAnotherUserWithoutASessionIsStillOffline(): void
    {
        $this->forum();

        $this->assertSame(-1, KunenaUserHelper::getStatus(self::OTHER));
    }

    public function testAGuestIsStillOffline(): void
    {
        $this->forum(ages: [self::ME => 120]);

        $guest = self::bare(KunenaUser::class, ['userid' => 0, 'status' => 0]);
        $this->assertSame(-1, KunenaUserHelper::getStatus($guest));

        // Also when the current user is a guest.
        (new ReflectionProperty(KunenaUserHelper::class, '_me'))->setValue(null, $guest);
        $this->assertSame(-1, KunenaUserHelper::getStatus($guest));
    }

    public function testNothingIsReturnedWhenTheStatusOfTheUsersIsOff(): void
    {
        $this->forum(ages: [self::ME => 5]);
        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, self::bare(KunenaConfig::class, ['config' => ['userStatus' => 0]]));

        $this->assertFalse(KunenaUserHelper::getStatus(self::ME));
        $this->assertFalse(KunenaUserHelper::getStatus(self::OTHER));
    }
}
