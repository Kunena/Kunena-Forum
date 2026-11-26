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

namespace Kunena\Tests\Unit\Libraries\Kunena\Access;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * In the backend, the current user administers the whole forum when he may manage Kunena (the right "Access Administration
 * Interface" of the component). It was every user of the backend: the library also runs on the pages of the backend which are not
 * of Kunena (the dashboard, a module, another extension), where Joomla did not ask that right, so a manager without it was an
 * administrator of the forum there.
 */
class KunenaAccessIsAdminBackendTest extends UnitTestCase
{
    private const USER = 5;

    private mixed $savedApplication = null;

    private ?KunenaUser $savedMyself = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();

        $this->savedApplication = Factory::$application;
        $this->loadUserHelper();
        $this->savedMyself = KunenaUserHelper::getMyself();
    }

    /**
     * Loading KunenaUserHelper runs its initialize(), which makes the current user of the application: give it an application
     * without a user (a guest), the avatars of Kunena itself and an empty configuration, and take them back afterwards.
     */
    private function loadUserHelper(): void
    {
        if (class_exists(KunenaUserHelper::class, false)) {
            return;
        }

        $avatars    = new ReflectionProperty(KunenaAvatar::class, 'instance');
        $oldAvatars = $avatars->getValue();
        $config     = new ReflectionProperty(KunenaConfig::class, 'instance');
        $oldConfig  = $config->getValue();
        $empty      = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($empty, []);

        $avatars->setValue(null, new KunenaAvatar());
        $config->setValue(null, $empty);
        $guest = $this->createStub(CMSApplicationInterface::class);
        $guest->method('getDispatcher')->willReturn(new Dispatcher());
        Factory::$application = $guest;

        try {
            class_exists(KunenaUserHelper::class);
        } finally {
            $avatars->setValue(null, $oldAvatars);
            $config->setValue(null, $oldConfig);
            Factory::$application = $this->savedApplication;
        }
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->savedApplication;
        KunenaUserHelper::setMyself($this->savedMyself);

        parent::tearDown();
    }

    /**
     * Whether the current user is an administrator of the forum.
     *
     * @param   bool   $backend  The page is of the backend.
     * @param   bool   $manage   The user may manage Kunena.
     * @param   array  $admins   The administrators which the access plugins give: user => category => 1.
     */
    private function isAdmin(bool $backend, bool $manage, array $admins = []): bool
    {
        $identity = $this->createStub(User::class);
        $identity->method('authorise')->willReturnCallback(
            static fn ($action, $asset = null) => $manage && $action === 'core.manage' && $asset === 'com_kunena'
        );

        $app = $this->createStub(CMSApplication::class);
        $app->method('isClient')->willReturnCallback(static fn ($client) => $backend ? $client === 'administrator' : $client === 'site');
        $app->method('getIdentity')->willReturn($identity);
        Factory::$application = $app;

        $user         = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
        $user->userid = self::USER;
        $user->banned = null;
        (new ReflectionProperty(KunenaUser::class, '_exists'))->setValue($user, true);
        KunenaUserHelper::setMyself($user);

        $access = (new ReflectionClass(KunenaAccess::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaAccess::class, 'adminsByUserid'))->setValue($access, $admins);

        return $access->isAdmin($user);
    }

    public function testTheUserWhoMayManageKunenaAdministersTheForumInTheBackend(): void
    {
        $this->assertTrue($this->isAdmin(true, true));
    }

    public function testAUserOfTheBackendWhoMayNotManageKunenaIsNoAdministrator(): void
    {
        $this->assertFalse($this->isAdmin(true, false));
    }

    public function testTheAdministratorOfTheAccessPluginsStaysOneWithoutTheRight(): void
    {
        $this->assertTrue($this->isAdmin(true, false, [self::USER => [0 => 1]]));
    }

    public function testTheRightToManageGivesNothingOnTheSite(): void
    {
        $this->assertFalse($this->isAdmin(false, true));
    }
}
