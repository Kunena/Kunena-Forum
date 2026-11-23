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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The lists which leave out the shadows of moved topics (recent topics, the topics of a user, the similar topics, the API) call
 * filterByMoved(false). The finder kept the value but never applied it, so a topic moved with a shadow was listed twice.
 */
class KunenaTopicFinderMovedTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena'];
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
        $dispatcher = new Dispatcher();
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);

        // Loading the helper of the users makes the current user: it reads the configuration and the avatars of Kunena itself.
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['threadsPerPage' => 20]);

        // The categories first: the helper loads them when it is loaded, which the replaced application could not do.
        $this->saved = ['categories' => $this->replace(KunenaCategoryHelper::class, '_instances', [])];

        $this->saved         += ['container' => Factory::$container, 'application' => Factory::$application];
        Factory::$container   = $container;
        Factory::$application = $application;

        $this->saved += [
            'config'  => $this->replace(KunenaConfig::class, 'instance', $config),
            'avatars' => $this->replace(KunenaAvatar::class, 'instance', new KunenaAvatar()),
            'access'  => $this->replace(KunenaAccess::class, 'instance', $this->createStub(KunenaAccess::class)),
        ];

        // The current user (a member) and no category where the users see their own topics only.
        $me = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
        $me->userid = 42;

        $this->saved += ['me' => $this->replace(KunenaUserHelper::class, '_me', $me)];

        // The topic 10 was moved to the topic 11 and left a shadow.
        $this->insertRows('#__kunena_topics', [
            ['id' => 10, 'category_id' => 2, 'subject' => 'Moved', 'hold' => 0, 'moved_id' => 11],
            ['id' => 11, 'category_id' => 3, 'subject' => 'Moved', 'hold' => 0, 'moved_id' => 0],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            $this->replace(KunenaUserHelper::class, '_me', $this->saved['me']);
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
            $this->replace(KunenaAvatar::class, 'instance', $this->saved['avatars']);
            $this->replace(KunenaAccess::class, 'instance', $this->saved['access']);
        }

        parent::tearDown();
    }

    public function testTheShadowIsLeftOut(): void
    {
        $reflection = new ReflectionClass(KunenaTopicFinder::class);
        $finder     = $reflection->newInstanceWithoutConstructor();
        $db         = $this->getDBDriver();
        $reflection->getProperty('db')->setValue($finder, $db);
        $reflection->getProperty('query')->setValue($finder, $db->createQuery()->from($db->quoteName('#__kunena_topics', 'a')));

        $this->assertSame(1, $finder->filterByMoved(false)->count());
    }

    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
