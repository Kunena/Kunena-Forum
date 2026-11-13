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
use Joomla\CMS\Language\Language;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The member viewing the page is always in the list of the users online (issue #6510), even when their session row is older
 * than the time limit; the other members keep the rules of the session table.
 */
class KunenaUserHelperOnlineUsersTest extends UnitTestCase
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

        // The configuration with "Online Users Contains = By time" and a limit of 180 seconds, a stub site application and a
        // language for the dates.
        $config   = self::bare(KunenaConfig::class, ['config' => ['showSessionType' => 2, 'showSessionStartTime' => 180]]);
        $site     = $this->createStub(CMSApplicationInterface::class);
        $site->method('get')->willReturnArgument(1);
        $site->method('getDispatcher')->willReturn(new Dispatcher());
        $language = $this->createStub(Language::class);
        $language->method('getTag')->willReturn('en-GB');

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'application' => Factory::$application,
            'container'   => Factory::$container,
            'language'    => Factory::$language,
        ];

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $config);
        Factory::$application = $site;
        Factory::$language    = $language;

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
        Factory::$container   = $this->saved['container'];
        Factory::$language    = $this->saved['language'];

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
     * The list of the users online, for the given current user and the session rows of the given ages in seconds which the
     * query of the session table returns.
     */
    private function online(int $me, array $ages = []): array
    {
        $rows = [];

        foreach ($ages as $userid => $age) {
            $rows[$userid] = (object) ['userid' => $userid, 'time' => time() - $age];
        }

        $query = $this->createStub(QueryInterface::class);

        foreach (['select', 'from', 'where', 'group', 'order'] as $method) {
            $query->method($method)->willReturnSelf();
        }

        $db = $this->createStub(DatabaseDriver::class);
        $db->method('createQuery')->willReturn($query);
        $db->method('loadObjectList')->willReturn($rows);

        Factory::$container = (new Container())->set(DatabaseInterface::class, $db)->set('DatabaseDriver', $db);

        (new ReflectionProperty(KunenaUserHelper::class, '_me'))->setValue(null, self::bare(KunenaUser::class, ['userid' => $me]));
        (new ReflectionProperty(KunenaUserHelper::class, '_online'))->setValue(null, null);

        return KunenaUserHelper::getOnlineUsers();
    }

    public function testTheCurrentUserWithoutASessionInTheLimitIsAddedFirst(): void
    {
        $online = $this->online(self::ME, [self::OTHER => 30]);

        $this->assertSame([self::ME, self::OTHER], array_keys($online));
        $this->assertSame(self::ME, $online[self::ME]->userid);
        $this->assertEqualsWithDelta(time(), $online[self::ME]->time, 5);
    }

    public function testTheSessionOfTheCurrentUserIsKeptAsItIs(): void
    {
        $online = $this->online(self::ME, [self::OTHER => 30, self::ME => 60]);

        $this->assertSame([self::OTHER, self::ME], array_keys($online), 'The current user is in the list once only.');
        $this->assertEqualsWithDelta(time() - 60, $online[self::ME]->time, 5, 'The time of the session row is not rewritten.');
    }

    public function testTheCurrentUserCountsWhenNobodyHasASession(): void
    {
        $online = $this->online(self::ME);

        $this->assertSame([self::ME], array_keys($online));
        $this->assertCount(1, $online);
    }

    public function testAGuestIsNotAdded(): void
    {
        $online = $this->online(0, [self::OTHER => 30]);

        $this->assertSame([self::OTHER], array_keys($online));
    }

    public function testNothingIsAddedWithoutACurrentUser(): void
    {
        $this->online(self::ME);
        (new ReflectionProperty(KunenaUserHelper::class, '_me'))->setValue(null, null);
        (new ReflectionProperty(KunenaUserHelper::class, '_online'))->setValue(null, null);

        $this->assertSame([], KunenaUserHelper::getOnlineUsers());
    }
}
