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

namespace Kunena\Tests\Integration\Libraries\Kunena\Tools;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Tools\KunenaUserMerge;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The tool "Merge users": a reaction of one account on a post of the other becomes a reaction of the user on his own post, which
 * nobody may make (KunenaMessageReactions::mayReact()): neither the button nor the task could take it away, and it counted. It is
 * removed with the merge, as the thank yous, the karma, the follows and the ignores of a user to himself.
 */
class KunenaUserMergeOwnReactionsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const FROM = 101;

    private const INTO = 102;

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
        $dispatcher = new Dispatcher();
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));

        // An application of the command line (the helpers of the users load with it) and a language for the texts.
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        $config = (new \ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new \ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['ratingEnabled' => 0, 'readOnly' => 0]);

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'access'      => $this->replace(KunenaAccess::class, 'instance', $this->createStub(KunenaAccess::class)),
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        // A post of the second account; the first one reacted to it, and another user (103) too.
        $this->insertRow('#__kunena_messages', ['id' => 1, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'userid' => self::INTO, 'subject' => 'Post', 'time' => 1000, 'hold' => 0]);
        $this->insertRows('#__kunena_reactions', [
            ['message_id' => 1, 'user_id' => self::FROM, 'reaction' => 'like', 'created_time' => 1000],
            ['message_id' => 1, 'user_id' => 103, 'reaction' => 'like', 'created_time' => 1000],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAccess::class, 'instance', $this->saved['access']);
            $this->replace(PluginHelper::class, 'plugins', $this->saved['plugins']);
        }

        parent::tearDown();
    }

    /**
     * Set a static property of a class and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new \ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }

    public function testTheReactionOnTheOwnPostIsRemoved(): void
    {
        KunenaUserMerge::merge(self::FROM, self::INTO);

        $this->assertSame([103], array_map('intval', array_column($this->fetchAll('SELECT user_id FROM #__kunena_reactions ORDER BY user_id'), 'user_id')));
    }
}
