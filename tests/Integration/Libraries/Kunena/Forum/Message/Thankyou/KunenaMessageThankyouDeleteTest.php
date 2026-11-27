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

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyou;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Taking a thank you back lowers the counter of the user who got it, as it was stored. It lowered the counter of the author of
 * the message now: of a post made anonymous (user 0), the thanked user kept the thank you in his counter.
 */
class KunenaMessageThankyouDeleteTest extends IntegrationTestCase implements DBTestInterface
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

        // The library reads the database of the container of Joomla: that is the database of the tests here. Loading the helper
        // of the users makes the current user, a guest: a bare configuration and the avatars of Kunena.
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
            'config'      => $this->replace(KunenaConfig::class, 'instance', (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor()),
            'avatars'     => $this->replace(KunenaAvatar::class, 'instance', new KunenaAvatar()),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        $this->saved['users']    = $this->replace(KunenaUserHelper::class, '_instances', []);
        $this->saved['messages'] = $this->replace(KunenaMessageHelper::class, '_instances', []);

        // The post 61 of 42, made anonymous since (no author): 43 thanked it, 42 has that one thank you.
        $this->insertRow('#__kunena_messages', ['id' => 61, 'parent' => 0, 'thread' => 60, 'catid' => 2, 'userid' => 0, 'subject' => 'Anonymous', 'time' => 1000, 'hold' => 0]);
        $this->insertRow('#__kunena_messages_text', ['mesid' => 61, 'message' => 'Text']);
        $this->insertRow('#__kunena_thankyou', ['postid' => 61, 'userid' => 43, 'targetuserid' => 42, 'time' => '2026-01-01 00:00:00']);
        $this->insertRows('#__kunena_users', [['userid' => 42, 'thankyou' => 1], ['userid' => 0, 'thankyou' => 0]]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAvatar::class, 'instance', $this->saved['avatars']);
            $this->replace(KunenaUserHelper::class, '_instances', $this->saved['users']);
            $this->replace(KunenaMessageHelper::class, '_instances', $this->saved['messages']);
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

    public function testTheThankedUserLosesTheThankYouOfAPostMadeAnonymous(): void
    {
        $thanker = self::bareUser(43);
        $this->replace(KunenaUserHelper::class, '_instances', [43 => $thanker]);

        $thankyou = (new ReflectionClass(KunenaMessageThankyou::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaMessageThankyou::class, 'id'))->setValue($thankyou, 61);
        (new ReflectionProperty(KunenaMessageThankyou::class, 'users'))->setValue($thankyou, [43 => '2026-01-01 00:00:00']);

        $this->assertTrue($thankyou->delete($thanker));

        $this->assertSame([], $this->fetchAll('SELECT postid FROM #__kunena_thankyou'));
        $this->assertSame(
            ['0' => 0, '42' => 0],
            array_map('intval', array_column($this->fetchAll('SELECT userid, thankyou FROM #__kunena_users ORDER BY userid'), 'thankyou', 'userid'))
        );
    }

    private static function bareUser(int $id): KunenaUser
    {
        $user         = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
        $user->userid = $id;
        (new ReflectionProperty(KunenaUser::class, '_exists'))->setValue($user, true);

        return $user;
    }
}
