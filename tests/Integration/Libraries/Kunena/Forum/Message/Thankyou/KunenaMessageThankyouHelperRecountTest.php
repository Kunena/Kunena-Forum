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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Message\Thankyou;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The recount of the users (Tools, Recount statistics) counts the thank yous which a member received. It only set the members
 * without any thank you to 0: a count which was wrong (two "unthank" requests at once took 2 off) stayed wrong.
 */
class KunenaMessageThankyouHelperRecountTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    /**
     * @return  string[]
     */
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

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testTheRecountOfTheUsersCountsTheThankYous(): void
    {
        // The member 42 has 2 thank yous and a count of 5; the member 43 has none and a count of 3.
        foreach ([42, 43] as $id) {
            $this->insertRow('#__users', ['id' => $id, 'name' => "User $id", 'username' => "user$id", 'params' => '']);
        }

        $this->insertRows('#__kunena_users', [
            ['userid' => 42, 'thankyou' => 5],
            ['userid' => 43, 'thankyou' => 3],
        ]);
        $this->insertRows('#__kunena_thankyou', [
            ['postid' => 1, 'userid' => 7, 'targetuserid' => 42, 'time' => '2026-01-01 00:00:00'],
            ['postid' => 2, 'userid' => 8, 'targetuserid' => 42, 'time' => '2026-01-01 00:00:00'],
        ]);

        KunenaMessageThankyouHelper::recountThankyou();

        $this->assertEquals(
            [['userid' => 42, 'thankyou' => 2], ['userid' => 43, 'thankyou' => 0]],
            $this->fetchAll('SELECT userid, thankyou FROM #__kunena_users WHERE userid IN (42, 43) ORDER BY userid')
        );
    }
}
