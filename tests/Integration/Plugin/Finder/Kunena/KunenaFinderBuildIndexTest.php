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

namespace Kunena\Tests\Integration\Plugin\Finder\Kunena;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Plugin\Finder\Kunena\Extension\Kunena;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The index of Smart Search, built in batches (Smart Search > Index, or the indexer of the scheduler): every message is indexed once.
 * The adapter kept a cursor of the message ids for the next batch but never moved it, so every batch read the first messages again
 * and the messages after the first batch never came into the index.
 */
class KunenaFinderBuildIndexTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    /**
     * @var  int[]  The ids of the messages given to the indexer of Joomla.
     */
    private array $indexed = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $root = \dirname(__DIR__, 5);
        require_once $root . '/src/plugins/plg_finder_kunena/src/Extension/Kunena.php';

        // The text of the index is made by the BBCode parser, in the folder External of the library.
        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', $root . '/src/libraries/kunena');
        }

        spl_autoload_register(static function ($class) use ($root) {
            if (strpos($class, 'Nbbc\\') === 0&& is_file($file = $root . '/src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php')) {
                require_once $file;
            }
        });

        // The library reads the database of the container of Joomla: that is the database of the tests here. The application has
        // no menu item of the site and no plugin, and gives the session where the indexer keeps its state.
        $driver     = $this->getDBDriver();
        $dispatcher = new Dispatcher();
        $container  = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share(DispatcherInterface::class, static fn () => $dispatcher);
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));

        $menu = $this->createStub(AbstractMenu::class);
        $menu->method('getItems')->willReturn([]);
        // The administrator application (Smart Search > Index) is final: an instance without its constructor, with the parts which the code reads.
        $application = (new ReflectionClass(AdministratorApplication::class))->newInstanceWithoutConstructor();
        $application->setDispatcher($dispatcher);
        $application->setSession($this->createStub(Session::class));
        (new ReflectionProperty(AdministratorApplication::class, 'name'))->setValue($application, 'administrator');
        (new ReflectionProperty(AdministratorApplication::class, 'config'))->setValue($application, new Registry(['access' => 1]));
        (new ReflectionProperty(AdministratorApplication::class, 'menus'))->setValue($application, ['site' => $menu]);
        (new ReflectionProperty(AdministratorApplication::class, 'input'))->setValue($application, new Input([]));
        $language = $this->createStub(Language::class);
        $language->method('transliterate')->willReturnArgument(0);
        $language->method('_')->willReturnArgument(0);
        (new ReflectionProperty(AdministratorApplication::class, 'language'))->setValue($application, $language);
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['messagesPerPage' => 20, 'readOnly' => 0, 'ratingEnabled' => 0, 'template' => 'aurelia', 'fallbackEnglish' => 0, 'debug' => 0, 'boardTitle' => 'Board', 'cache' => 0, 'cacheTime' => 60]);

        // The BBCode parser reads the parameters of the template.
        $template = (new ReflectionClass(KunenaTemplate::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaTemplate::class, 'params'))->setValue($template, new Registry(['disableEmoticons' => 1]));

        $this->saved = [
            'categories'  => $this->replace(KunenaCategoryHelper::class, '_instances', []),
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'config'      => $this->replace(KunenaConfig::class, 'instance', $config),
            'access'      => $this->replace(KunenaAccess::class, 'instance', $this->createStub(KunenaAccess::class)),
            'plugins'     => $this->replace(PluginHelper::class, 'plugins', []),
            'components'  => $this->replace(ComponentHelper::class, 'components', ['com_kunena' => (object) ['id' => 1, 'enabled' => 1]]),
            'state'       => $this->replace(Indexer::class, 'state', null),
            'templates'   => $this->replace(KunenaTemplate::class, '_instances', ['aurelia' => $template]),
            'uri'         => $this->replace(Uri::class, 'instances', ['SERVER' => new Uri('http://localhost/administrator/index.php')]),
            'root'        => $this->replace(Uri::class, 'root', ['prefix' => 'http://localhost', 'path' => '']),
            'base'        => $this->replace(Uri::class, 'base', ['prefix' => 'http://localhost', 'path' => '/administrator']),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        $this->insertRows('#__kunena_categories', [
            ['id' => 1, 'parentid' => 0, 'name' => 'Section', 'alias' => '1-section', 'published' => 1],
            ['id' => 2, 'parentid' => 1, 'name' => 'Category', 'alias' => '2-category', 'published' => 1],
        ]);
        $this->insertRow('#__kunena_topics', ['id' => 1, 'category_id' => 2, 'subject' => 'Topic', 'hold' => 0, 'posts' => 3,
            'first_post_id' => 1, 'last_post_id' => 9]);

        // Three messages, with a gap in their ids.
        foreach ([1, 4, 9] as $id) {
            $this->insertRow('#__kunena_messages', ['id' => $id, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'name' => 'U',
                'userid' => 0, 'email' => '', 'subject' => "Message $id", 'time' => $id, 'ip' => '', 'hold' => 0]);
            $this->insertRow('#__kunena_messages_text', ['mesid' => $id, 'message' => "Text $id"]);
        }
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
            $this->replace(ComponentHelper::class, 'components', $this->saved['components']);
            $this->replace(Indexer::class, 'state', $this->saved['state']);
            $this->replace(KunenaTemplate::class, '_instances', $this->saved['templates']);
            $this->replace(Uri::class, 'instances', $this->saved['uri']);
            $this->replace(Uri::class, 'root', $this->saved['root']);
            $this->replace(Uri::class, 'base', $this->saved['base']);
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);
            $this->replace(KunenaTopicHelper::class, '_instances', []);
            KunenaMessageHelper::cleanup();
        }

        parent::tearDown();
    }

    public function testEveryMessageIsIndexedOnceWhenTheIndexIsBuiltInBatches(): void
    {
        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());
        (new ReflectionProperty(Kunena::class, 'db'))->setValue($plugin, $this->getDBDriver());
        (new ReflectionProperty(Kunena::class, 'viewLevels'))->setValue($plugin, []);

        $indexer = $this->createStub(Indexer::class);
        $indexer->method('index')->willReturnCallback(function ($item) {
            $this->indexed[] = (int) $item->id;

            return 1;
        });
        (new ReflectionProperty(Kunena::class, 'indexer'))->setValue($plugin, $indexer);

        // The state of the indexer of Joomla: batches of 2 messages, 3 messages to index.
        $state               = new \stdClass();
        $state->batchSize    = 2;
        $state->batchOffset  = 0;
        $state->totalItems   = 3;
        $state->pluginState  = ['Kunena' => ['offset' => 0, 'total' => 3]];
        $this->replace(Indexer::class, 'state', $state);

        // As the controller of Smart Search does: a new batch until the adapter has indexed its total.
        for ($batch = 0; $batch < 5 && Indexer::getState()->pluginState['Kunena']['offset'] < 3; $batch++) {
            Indexer::getState()->batchOffset = 0;
            $plugin->onBuildIndex();
        }

        $this->assertSame([1, 4, 9], $this->indexed);
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
}
