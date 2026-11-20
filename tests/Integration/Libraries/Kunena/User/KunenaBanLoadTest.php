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
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * A ban which is in the table is loaded as existing: the table returned nothing from bind(), so load() gave null and every
 * ban was "new" (the edit form said "new ban", and the expired bans were never marked as handled).
 */
class KunenaBanLoadTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const BAN = 5;

    private const USER = 101;

    /**
     * The objects of Joomla which the test replaces, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        // The ban and its table need the framework of Joomla.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The ban reads the database of the container of Joomla (the database of the tests here); the table sends its events
        // to the dispatcher of the container.
        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        $this->insertRow('#__kunena_users_banned', [
            'id'           => self::BAN,
            'userid'       => self::USER,
            'blocked'      => 0,
            'expiration'   => '9999-12-31 23:59:59',
            'created_by'   => 42,
            'created_time' => '2026-01-02 03:04:05',
            'comments'     => '{}',
            'params'       => '{}',
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
        }

        parent::tearDown();
    }

    public function testABanInTheTableExists(): void
    {
        $ban = new KunenaBan(self::BAN);

        $this->assertTrue($ban->exists());
        $this->assertSame(self::USER, (int) $ban->userid);
    }

    public function testParametersWrittenAsAnObjectAreLoaded(): void
    {
        // "{}" was decoded as an object, which the array property refused (TypeError).
        $ban = new KunenaBan(self::BAN);

        $this->assertSame([], $ban->params);
        $this->assertSame([], $ban->comments);
    }

    public function testAMissingBanDoesNotExist(): void
    {
        $this->assertFalse((new KunenaBan(999))->exists());
    }

    public function testNoIdIsNoBan(): void
    {
        $this->assertFalse((new KunenaBan(0))->exists());
        $this->assertFalse((new KunenaBan())->exists());
    }
}
