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
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Who gets the e-mail of a new topic of an author whom he follows: not a follower who is banned from the forum now, as for the
 * subscribers. A banned follower got the text of every new topic of the author.
 */
class KunenaUserFollowRecipientsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

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

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['enableFollow' => 1, 'useSystemEmails' => 0]);

        $this->saved = ['container' => Factory::$container, 'config' => $this->replace(KunenaConfig::class, 'instance', $config)];
        Factory::$container = $container;

        // The author 42 is followed by 101 (no ban), 102 (banned until next year) and 103 (a ban which ended).
        $users = [];

        foreach ([101, 102, 103] as $id) {
            $users[] = ['id' => $id, 'name' => "User $id", 'username' => "user$id", 'email' => "user$id@example.invalid", 'params' => '', 'block' => 0];
        }

        $this->insertRows('#__users', $users);
        $this->insertRows('#__kunena_users', [
            ['userid' => 102, 'banned' => gmdate('Y-m-d H:i:s', time() + 365 * 86400)],
            ['userid' => 103, 'banned' => '2020-01-01 00:00:00'],
        ]);
        $this->insertRows('#__kunena_user_follow', [
            ['user_id' => 101, 'followed_id' => 42],
            ['user_id' => 102, 'followed_id' => 42],
            ['user_id' => 103, 'followed_id' => 42],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
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

    public function testABannedFollowerGetsNoMail(): void
    {
        $ids = array_map(static fn ($row) => (int) $row->id, KunenaUserFollow::getRecipients(42));
        sort($ids);

        $this->assertSame([101, 103], $ids);
    }
}
