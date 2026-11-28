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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Factory;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Controllers\TopicController;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The answers of StopForumSpam for the posts of guests are kept 10 minutes, and a failed lookup is not kept: the lifetime was 600
 * for Joomla, which counts it in minutes (10 hours), and a timeout or an error of the service was kept as "not listed", so the IP,
 * the name and the e-mail of a spammer posted without a check for 10 hours.
 */
class TopicStopForumSpamCacheTest extends UnitTestCase
{
    private mixed $container = null;

    private array $options = [];

    private array $stored = [];

    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadUserHelper();

        // A cache which keeps nothing yet and tells what it is given.
        $cache = $this->createStub(OutputController::class);
        $cache->method('get')->willReturn(false);
        $cache->method('store')->willReturnCallback(function ($data, $id) {
            $this->stored[$id] = $data;

            return true;
        });

        $factory = $this->createStub(CacheControllerFactoryInterface::class);
        $factory->method('createCacheController')->willReturnCallback(function ($type, $options) use ($cache) {
            $this->options = $options;

            return $cache;
        });

        $container = new Container();
        $container->share(CacheControllerFactoryInterface::class, static fn () => $factory);

        $this->container  = Factory::$container;
        Factory::$container = $container;
    }

    protected function tearDown(): void
    {
        Factory::$container = $this->container;

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

    /**
     * Whether a guest post is "blacklisted" when the service answers $answer (null: no answer).
     */
    private function check(?bool $answer): bool
    {
        $controller = $this->createPartialMock(TopicController::class, ['requestStopForumSpam']);
        $controller->expects($this->once())->method('requestStopForumSpam')->willReturn($answer);

        return self::invoke($controller, 'checkIfBlacklisted', (object) ['ip' => '192.0.2.7', 'name' => 'Spammer', 'email' => 'spam@example.test']);
    }

    public function testAFailedLookupIsNotKept(): void
    {
        $this->assertSame(['listed' => false, 'kept' => []], ['listed' => $this->check(null), 'kept' => array_values($this->stored)]);
    }

    public function testAnAnswerIsKeptTenMinutes(): void
    {
        $this->assertSame(
            ['listed' => true, 'kept' => ['1'], 'minutes' => 10],
            ['listed' => $this->check(true), 'kept' => array_values($this->stored), 'minutes' => $this->options['lifetime']]
        );
    }
}
