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

namespace Kunena\Tests\Integration\Libraries\Kunena\Access;

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
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use Kunena\Tests\Unit\Support\Files;
use ReflectionClass;
use ReflectionProperty;

/**
 * A moderator or an administrator who is banned in the forum may moderate no more (isModerator() and isAdmin() refuse him), and he
 * is not one of those who get the reports and the posts to approve of a category, nor named under "Moderators": the banned only
 * moderator of a category got them, and the administrators, who get them when a category has no moderator, were not told.
 */
class KunenaAccessBannedModeratorsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    /**
     * The objects of Joomla and of Kunena which the test replaces, put back after the test.
     */
    private array $saved = [];

    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // The categories and the topics need the framework of Joomla.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // As in KunenaTopicHelperTrashTest: the database of the container is the one of the tests, a command line application
        // without plugins, the settings which the code reads, caches and access which do nothing.
        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn($dispatcher);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'cli');
        $language = $this->createStub(Language::class);
        $language->method('transliterate')->willReturnArgument(0);
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['threadsPerPage' => 20, 'ratingEnabled' => 0, 'readOnly' => 0]);

        $this->saved = [
            'categories'  => $this->replace(KunenaCategoryHelper::class, '_instances', []),
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

        // No user of another test: the users are loaded from the rows of this one.
        $this->replace(KunenaUserHelper::class, '_instances', []);

        // The user 42 is banned until 9999, the user 43 is not, the ban of the user 44 is over.
        $this->insertRows('#__users', [
            ['id' => 42, 'name' => 'Banned', 'username' => 'banned', 'email' => 'banned@example.com', 'params' => '{}'],
            ['id' => 43, 'name' => 'Moderator', 'username' => 'moderator', 'email' => 'moderator@example.com', 'params' => '{}'],
            ['id' => 44, 'name' => 'Former', 'username' => 'former', 'email' => 'former@example.com', 'params' => '{}'],
        ]);
        $this->insertRows('#__kunena_users', [
            ['userid' => 42, 'banned' => '9999-12-31 23:59:59'],
            ['userid' => 43],
            ['userid' => 44, 'banned' => '2001-01-01 00:00:00'],
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
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);

            // The helpers keep the topics and the users for the request: the next test has rows of its own.
            $this->replace(KunenaTopicHelper::class, '_instances', []);
            $this->replace(KunenaUserHelper::class, '_instances', []);
        }

        parent::tearDown();
    }

    /**
     * Set a static property of a class and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }

    public function testTheBannedUsersAreLeftOut(): void
    {
        $this->assertSame([43 => 43, 44 => 44], KunenaAccess::withoutBanned([42 => 42, 43 => 43, 44 => 44]));
    }

    public function testTheReportsAndTheApprovalsAreNotForBannedModerators(): void
    {
        // Before the administrators are taken for a category without a moderator.
        $source = str_replace("\r\n", "\n", Files::read('src/libraries/kunena/src/Access/KunenaAccess.php'));
        $start  = strpos($source, '    public function getSubscribers(');
        $body   = substr($source, $start, (int) strpos($source, "\n    }\n", $start) - $start);

        $this->assertStringContainsString('$modlist   = self::withoutBanned($modlist);', $body);
        $this->assertStringContainsString('$adminlist = self::withoutBanned($adminlist);', $body);
        $this->assertLessThan(strpos($body, 'if (empty($modlist)) {'), strpos($body, 'self::withoutBanned($modlist)'));
    }

    public function testThePagesDoNotNameBannedModerators(): void
    {
        foreach (['src/site/src/Model/CategoryModel.php', 'src/site/src/Model/TopicModel.php'] as $file) {
            $this->assertStringContainsString('return KunenaAccess::withoutBanned($this->getCategory()->getModerators(false));', Files::read($file), $file);
        }

        // The moderators of the subcategories of the index.
        $this->assertStringContainsString('array_keys(KunenaAccess::withoutBanned($access->getModerators($subcat->id)))', Files::read('src/site/src/Model/CategoryModel.php'));
    }
}
