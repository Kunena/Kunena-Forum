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

namespace Kunena\Tests\Unit\Libraries\Kunena\Session;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUser;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use Kunena\Forum\Libraries\Session\KunenaSession;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\Tables\KunenaSessions;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The session of a member who has no row yet is saved twice in one request ("Mark all forum read" as his first request: the forum
 * saves the session, the task saves it again): the second save updates the row. It inserted it again, and the duplicate key ended
 * the request in an error.
 */
class KunenaSessionSaveTwiceTest extends UnitTestCase
{
    private const USER = 42;

    private mixed $categoryUsers = null;

    private mixed $users = null;

    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadUserHelper();

        // The time of "mark all read" of the member is the one of his session: no row of the categories is saved.
        $read              = (new ReflectionClass(KunenaCategoryUser::class))->newInstanceWithoutConstructor();
        $read->allreadtime = 1000;

        // The member, as the forum has him.
        $users       = new ReflectionProperty(KunenaUserHelper::class, '_instances');
        $this->users = $users->getValue();
        $users->setValue(null, [self::USER => self::bare(KunenaUser::class, ['userid' => self::USER, '_exists' => true])]);

        $instances           = new ReflectionProperty(KunenaCategoryUserHelper::class, '_instances');
        $this->categoryUsers = $instances->getValue();
        $instances->setValue(null, [self::USER => [0 => $read]]);
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(KunenaCategoryUserHelper::class, '_instances'))->setValue(null, $this->categoryUsers);
        (new ReflectionProperty(KunenaUserHelper::class, '_instances'))->setValue(null, $this->users);

        parent::tearDown();
    }

    /**
     * Loading KunenaUserHelper loads the user of the request: give it a guest, an empty configuration and the avatars of Kunena,
     * and take them back afterwards.
     */
    private function loadUserHelper(): void
    {
        if (class_exists(KunenaUserHelper::class, false)) {
            return;
        }

        $avatars     = new ReflectionProperty(KunenaAvatar::class, 'instance');
        $oldAvatars  = $avatars->getValue();
        $config      = new ReflectionProperty(KunenaConfig::class, 'instance');
        $oldConfig   = $config->getValue();
        $application = Factory::$application;
        $empty       = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
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
            Factory::$application = $application;
        }
    }

    public function testTheSecondSaveUpdatesTheRow(): void
    {
        // The table of the sessions, which says whether each store inserts (the row does not exist) or updates.
        $stores = [];
        $table  = $this->createPartialMock(KunenaSessions::class, ['bind', 'check', 'store']);
        $table->method('bind')->willReturn(true);
        $table->method('check')->willReturn(true);
        $table->expects($this->exactly(2))->method('store')->willReturnCallback(function () use (&$stores, $table) {
            $stores[] = $table->exists() ? 'update' : 'insert';

            return true;
        });

        $session = $this->createPartialMock(KunenaSession::class, ['getTable']);
        $session->expects($this->exactly(2))->method('getTable')->willReturn($table);

        foreach (['userid' => self::USER, 'lasttime' => 1000, 'currvisit' => 2000, 'readtopics' => 0, 'allowed' => 'na', 'allreadtime' => 1000] as $name => $value) {
            (new ReflectionProperty(KunenaSession::class, $name))->setValue($session, $value);
        }

        $this->assertTrue($session->save());
        $this->assertTrue($session->save());
        $this->assertSame(['insert', 'update'], $stores);
    }
}
