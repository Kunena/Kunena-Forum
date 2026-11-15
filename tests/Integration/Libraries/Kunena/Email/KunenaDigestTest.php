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

namespace Kunena\Tests\Integration\Libraries\Kunena\Email;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Email\KunenaDigest;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Tests\Integration\DBTestHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The digests on the real tables: the posts collected for users, a post collected twice stays once, the collected posts by user
 * with the oldest time, the choices of the users from their Joomla parameters, and the diagnostics which find the collected posts
 * of messages which are gone.
 */
class KunenaDigestTest extends IntegrationTestCase implements DBTestInterface
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

        // The container of Joomla gives the database of the tests to the class.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        DBTestHelper::markDirty('#__kunena_digest');
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testThePostsAreCollectedOncePerUser(): void
    {
        KunenaDigest::collect(7, [10, 11], 1000);
        KunenaDigest::collect(7, [10], 1500);
        KunenaDigest::collect(8, [10], 2000);

        $pending = KunenaDigest::pending();

        $this->assertSame([10, 11], array_keys($pending));
        $this->assertSame([8, 7], $pending[10]['ids'], 'The newest post first.');
        $this->assertSame(1000, $pending[10]['first']);
        $this->assertSame([7], $pending[11]['ids']);
    }

    public function testNothingIsCollectedWithoutUsersOrMessage(): void
    {
        KunenaDigest::collect(7, []);
        KunenaDigest::collect(0, [10]);
        KunenaDigest::collect(7, [0, -3]);

        $this->assertSame([], KunenaDigest::pending());
    }

    public function testTheChoicesComeFromTheJoomlaParametersOfTheUsers(): void
    {
        $this->insertRows('#__users', [
            ['id' => 20, 'name' => 'Daily', 'username' => 'daily', 'params' => '{"kunena_digest":1}'],
            ['id' => 21, 'name' => 'Weekly', 'username' => 'weekly', 'params' => '{"kunena_digest":"2","language":"en-GB"}'],
            ['id' => 22, 'name' => 'Each', 'username' => 'each', 'params' => '{}'],
            ['id' => 23, 'name' => 'Broken', 'username' => 'broken', 'params' => 'not json'],
        ]);

        $this->assertSame(
            [20 => KunenaDigest::DAILY, 21 => KunenaDigest::WEEKLY, 22 => KunenaDigest::EACH, 23 => KunenaDigest::EACH],
            KunenaDigest::frequencies([20, 21, 22, 23, 99])
        );
    }

    public function testTheDiagnosticsFindAndDeleteTheCollectedPostsOfMessagesWhichAreGone(): void
    {
        KunenaDigest::collect(5, [10], 1000);
        KunenaDigest::collect(6, [10], 1000);

        // Only the message 6 exists.
        $this->insertRow('#__kunena_messages', ['id' => 6, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'name' => 'A', 'userid' => 1,
            'email' => '', 'subject' => 'Six', 'time' => 1, 'ip' => '']);

        $this->assertContains('digestOrphaned', KunenaDiagnostics::getList());
        $this->assertSame(1, KunenaDiagnostics::count('digestOrphaned'));
        $this->assertTrue(KunenaDiagnostics::delete('digestOrphaned'));
        $this->assertSame([6], KunenaDigest::pending()[10]['ids']);
    }
}
