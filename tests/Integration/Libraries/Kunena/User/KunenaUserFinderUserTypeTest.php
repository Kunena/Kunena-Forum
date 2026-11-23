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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUserFinder;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The filter "users" of the list of the users (no moderator, not banned, no super user): each user once. The filter joined the
 * groups of the users, a row for each group, so a user of two groups was counted twice and a super user who is in another group too
 * was listed.
 */
class KunenaUserFinderUserTypeTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

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
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['userlistRows' => 30]);

        $instance    = new ReflectionProperty(KunenaConfig::class, 'instance');
        $this->saved = ['container' => Factory::$container, 'config' => $instance->getValue(), 'language' => Factory::$language];
        $instance->setValue(null, $config);
        Factory::$container = $container;

        // The filter takes the date of now, which reads the language.
        Factory::$language = $this->createStub(Language::class);
        Factory::$language->method('getTag')->willReturn('en-GB');

        // The user 41 is in Registered and Author, the user 42 in Registered, the user 43 is a super user who is in Registered too.
        foreach ([41, 42, 43] as $id) {
            $this->insertRow('#__users', ['id' => $id, 'name' => "U$id", 'username' => "u$id", 'email' => "u$id@example.com", 'password' => '',
                'block' => 0, 'registerDate' => '2026-01-01 00:00:00', 'params' => '']);
            $this->insertRow('#__kunena_users', ['userid' => $id, 'moderator' => 0, 'banned' => '1000-01-01 00:00:00']);
        }

        $this->insertRows('#__user_usergroup_map', [
            ['user_id' => 41, 'group_id' => 2],
            ['user_id' => 41, 'group_id' => 3],
            ['user_id' => 42, 'group_id' => 2],
            ['user_id' => 43, 'group_id' => 2],
            ['user_id' => 43, 'group_id' => 8],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            Factory::$language  = $this->saved['language'];
            (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        }

        parent::tearDown();
    }

    public function testEachUserWhoIsNoSuperUserCountsOnce(): void
    {
        $this->assertSame(2, (new KunenaUserFinder())->filterByUserType('user')->count());
    }
}
