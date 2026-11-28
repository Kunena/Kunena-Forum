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
use Joomla\CMS\Event\Finder\AfterSaveEvent;
use Joomla\CMS\Event\Finder\BeforeSaveEvent;
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
use Kunena\Forum\Libraries\Event\KunenaBeforeDeleteEvent;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Tables\TableKunenaCategories;
use Kunena\Forum\Libraries\Tables\TableKunenaTopics;
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
     * The language of an item is the one of its category (else of its topic): every post was "all languages", so the posts of a
     * French category were in the results of the English pages, with the menu item of the language of the indexer (a 404 there).
     */
    public function testTheItemsHaveTheLanguageOfTheirCategory(): void
    {
        $this->getDBDriver()->setQuery("UPDATE #__kunena_categories SET language = 'fr-FR' WHERE id = 2")->execute();

        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());
        (new ReflectionProperty(Kunena::class, 'db'))->setValue($plugin, $this->getDBDriver());
        (new ReflectionProperty(Kunena::class, 'viewLevels'))->setValue($plugin, []);

        $items   = [];
        $indexer = $this->createStub(Indexer::class);
        $indexer->method('index')->willReturnCallback(static function ($item) use (&$items) {
            $items[] = [(int) $item->id, $item->language, str_contains((string) $item->route, 'Itemid')];

            return 1;
        });
        (new ReflectionProperty(Kunena::class, 'indexer'))->setValue($plugin, $indexer);

        $state              = new \stdClass();
        $state->batchSize   = 10;
        $state->batchOffset = 0;
        $state->totalItems  = 3;
        $state->pluginState = ['Kunena' => ['offset' => 0, 'total' => 3]];
        $this->replace(Indexer::class, 'state', $state);

        $plugin->onBuildIndex();

        $this->assertSame([[1, 'fr-FR', false], [4, 'fr-FR', false], [9, 'fr-FR', false]], $items);
    }

    /**
     * A message without a row of its text (Diagnostics knows them) gives no item: its batch had one item less than its limit, which
     * ended the index, and the messages after that batch were never indexed. The batch counts the messages it read.
     */
    public function testAMessageWithoutTextDoesNotEndTheIndex(): void
    {
        $this->insertRow('#__kunena_messages', ['id' => 2, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'name' => 'U',
            'userid' => 0, 'email' => '', 'subject' => 'Message 2', 'time' => 2, 'ip' => '', 'hold' => 0]);

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

        // Batches of 2 messages, 4 messages to index (the message 2 has no text).
        $state               = new \stdClass();
        $state->batchSize    = 2;
        $state->batchOffset  = 0;
        $state->totalItems   = 4;
        $state->pluginState  = ['Kunena' => ['offset' => 0, 'total' => 4]];
        $this->replace(Indexer::class, 'state', $state);

        for ($batch = 0; $batch < 5 && Indexer::getState()->pluginState['Kunena']['offset'] < 4; $batch++) {
            Indexer::getState()->batchOffset = 0;
            $plugin->onBuildIndex();
        }

        $this->assertSame([1, 4, 9], $this->indexed);
    }

    /**
     * A topic is moved into another category, or deleted: KunenaTopic::move() and publish() change the category and the state of
     * its messages in the database and save the topic. The messages are indexed again with their new access level and state; they
     * were not, and the posts of a topic moved into a closed category or deleted stayed in the results of the search of the site.
     */
    public function testTheMessagesOfATopicAreIndexedAgainWhenTheTopicMovesOrIsDeleted(): void
    {
        $db = $this->getDBDriver();

        // The adapter loads the constants of Kunena (api.php) before it indexes again: the paths of the test are set already.
        if (!\defined('KUNENA_LOADED')) {
            \define('KUNENA_LOADED', 1);
        }

        // The category 3 is for the level 3 (Special); Smart Search removes the nodes of the taxonomy which are not used any more.
        $this->insertRow('#__kunena_categories', ['id' => 3, 'parentid' => 1, 'name' => 'Staff', 'alias' => '3-staff', 'published' => 1,
            'accesstype' => 'joomla.level', 'access' => 3]);
        $db->setQuery('CREATE TABLE IF NOT EXISTS #__finder_taxonomy (id int unsigned NOT NULL AUTO_INCREMENT, parent_id int unsigned NOT NULL DEFAULT 0,'
            . ' lft int NOT NULL DEFAULT 0, rgt int NOT NULL DEFAULT 0, level int unsigned NOT NULL DEFAULT 0, path varchar(400) NOT NULL DEFAULT \'\','
            . ' title varchar(255) NOT NULL DEFAULT \'\', alias varchar(400) NOT NULL DEFAULT \'\', state tinyint unsigned NOT NULL DEFAULT 1,'
            . ' access tinyint unsigned NOT NULL DEFAULT 1, language char(7) NOT NULL DEFAULT \'\', PRIMARY KEY (id))')->execute();
        $db->setQuery('CREATE TABLE IF NOT EXISTS #__finder_taxonomy_map (link_id int unsigned NOT NULL, node_id int unsigned NOT NULL,'
            . ' PRIMARY KEY (link_id, node_id))')->execute();

        try {
            $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
            $plugin->setDatabase($db);
            (new ReflectionProperty(Kunena::class, 'db'))->setValue($plugin, $db);
            (new ReflectionProperty(Kunena::class, 'viewLevels'))->setValue($plugin, [1 => [1], 3 => [6]]);

            $items   = [];
            $indexer = $this->createStub(Indexer::class);
            $indexer->method('index')->willReturnCallback(static function ($item) use (&$items) {
                $items[] = [(int) $item->id, (int) $item->access, (int) $item->state];

                return 1;
            });
            (new ReflectionProperty(Kunena::class, 'indexer'))->setValue($plugin, $indexer);

            // The messages of the topic are read in this request (the page of the topic) before it moves.
            KunenaMessageHelper::getMessages([1, 4, 9], 'none');

            // The topic moves into the category 3 (as KunenaTopic::move() does it).
            $this->saveTopic($plugin, static function () use ($db) {
                $db->setQuery('UPDATE #__kunena_messages SET catid = 3 WHERE thread = 1')->execute();
                $db->setQuery('UPDATE #__kunena_topics SET category_id = 3 WHERE id = 1')->execute();
            });
            $this->assertSame([[1, 3, 1], [4, 3, 1], [9, 3, 1]], $items);

            // The topic is deleted (as KunenaTopic::publish() does it).
            $items = [];
            $this->saveTopic($plugin, static function () use ($db) {
                $db->setQuery('UPDATE #__kunena_messages SET hold = 2 WHERE thread = 1')->execute();
                $db->setQuery('UPDATE #__kunena_topics SET hold = 2 WHERE id = 1')->execute();
            });
            $this->assertSame([[1, 3, 0], [4, 3, 0], [9, 3, 0]], $items);

            // A topic which is saved without a change of its category or state (a new reply, a hit) is not indexed again.
            $items = [];
            $this->saveTopic($plugin, static function () {
            });
            $this->assertSame([], $items);

            // The topic is restored, and moved into the category 2 again with a shadow: the messages go to a new topic (2), the
            // topic 1 stays as the shadow which points to it (as KunenaTopic::move() does it).
            $db->setQuery('UPDATE #__kunena_messages SET hold = 0 WHERE thread = 1')->execute();
            $db->setQuery('UPDATE #__kunena_topics SET hold = 0 WHERE id = 1')->execute();
            $this->insertRow('#__kunena_topics', ['id' => 2, 'category_id' => 2, 'subject' => 'Topic', 'hold' => 0, 'posts' => 3,
                'first_post_id' => 1, 'last_post_id' => 9]);
            KunenaCategoryHelper::get(2);
            $items = [];
            $this->saveTopic($plugin, static function () use ($db) {
                $db->setQuery('UPDATE #__kunena_messages SET catid = 2, thread = 2 WHERE thread = 1')->execute();
                $db->setQuery('UPDATE #__kunena_topics SET moved_id = 2 WHERE id = 1')->execute();
            });
            $this->assertSame([[1, 1, 1], [4, 1, 1], [9, 1, 1]], $items);
        } finally {
            $db->setQuery('DROP TABLE IF EXISTS #__finder_taxonomy')->execute();
            $db->setQuery('DROP TABLE IF EXISTS #__finder_taxonomy_map')->execute();
        }
    }

    /**
     * A section is unpublished (Categories > Unpublish): the messages of its categories are hidden with it. The adapter indexed the
     * messages of a category again only when the access level of a category of levels changed: they stayed in the results.
     */
    public function testTheMessagesOfTheCategoriesOfAnUnpublishedSectionAreIndexedAgain(): void
    {
        $db = $this->getDBDriver();

        if (!\defined('KUNENA_LOADED')) {
            \define('KUNENA_LOADED', 1);
        }

        $db->setQuery('CREATE TABLE IF NOT EXISTS #__finder_taxonomy (id int unsigned NOT NULL AUTO_INCREMENT, parent_id int unsigned NOT NULL DEFAULT 0,'
            . ' lft int NOT NULL DEFAULT 0, rgt int NOT NULL DEFAULT 0, level int unsigned NOT NULL DEFAULT 0, path varchar(400) NOT NULL DEFAULT \'\','
            . ' title varchar(255) NOT NULL DEFAULT \'\', alias varchar(400) NOT NULL DEFAULT \'\', state tinyint unsigned NOT NULL DEFAULT 1,'
            . ' access tinyint unsigned NOT NULL DEFAULT 1, language char(7) NOT NULL DEFAULT \'\', PRIMARY KEY (id))')->execute();
        $db->setQuery('CREATE TABLE IF NOT EXISTS #__finder_taxonomy_map (link_id int unsigned NOT NULL, node_id int unsigned NOT NULL,'
            . ' PRIMARY KEY (link_id, node_id))')->execute();

        try {
            $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
            $plugin->setDatabase($db);
            (new ReflectionProperty(Kunena::class, 'db'))->setValue($plugin, $db);
            (new ReflectionProperty(Kunena::class, 'viewLevels'))->setValue($plugin, [1 => [1]]);

            $items   = [];
            $indexer = $this->createStub(Indexer::class);
            $indexer->method('index')->willReturnCallback(static function ($item) use (&$items) {
                $items[] = [(int) $item->id, (int) $item->state];

                return 1;
            });
            (new ReflectionProperty(Kunena::class, 'indexer'))->setValue($plugin, $indexer);

            // The section 1 is unpublished as KunenaCategory::save() does it: the object of the category and its row.
            $table = new TableKunenaCategories($db);
            $table->load(1);
            $plugin->onFinderBeforeSave(new BeforeSaveEvent('onFinderBeforeSave', ['context' => 'com_kunena.category', 'subject' => $table, 'isNew' => false]));
            KunenaCategoryHelper::get(1)->published = 0;
            $db->setQuery('UPDATE #__kunena_categories SET published = 0 WHERE id = 1')->execute();
            $table->load(1);
            $plugin->onFinderAfterSave(new AfterSaveEvent('onFinderAfterSave', ['context' => 'com_kunena.category', 'subject' => $table, 'isNew' => false]));

            $this->assertSame([[1, 0], [4, 0], [9, 0]], $items);
        } finally {
            $db->setQuery('DROP TABLE IF EXISTS #__finder_taxonomy')->execute();
            $db->setQuery('DROP TABLE IF EXISTS #__finder_taxonomy_map')->execute();
        }
    }

    /**
     * A topic or a category is deleted: its messages leave the index first. The list of the messages was read from a cache with an
     * index which was not there yet ("Undefined array key", a warning with every deletion).
     */
    public function testATopicAndACategoryAreDeletedWithoutWarnings(): void
    {
        $db     = $this->getDBDriver();
        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($db);

        // A topic and a category without messages: nothing to remove from the index.
        $topic = new TableKunenaTopics($db);
        $topic->id = 99;
        $category = new TableKunenaCategories($db);
        $category->id = 99;

        $warnings = [];
        set_error_handler(static function (int $level, string $message) use (&$warnings): bool {
            $warnings[] = $message;

            return true;
        }, E_WARNING);

        try {
            $plugin->onFinderBeforeDelete(new KunenaBeforeDeleteEvent('onFinderBeforeDelete', ['context' => 'com_kunena.topic', 'subject' => $topic]));
            $plugin->onFinderBeforeDelete(new KunenaBeforeDeleteEvent('onFinderBeforeDelete', ['context' => 'com_kunena.category', 'subject' => $category]));
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $warnings);
    }

    /**
     * Save the topic 1 as KunenaDatabaseObject::save() does: the events before and after, with the table of the topic; $change
     * writes the change to the database in between.
     */
    private function saveTopic(Kunena $plugin, callable $change): void
    {
        $table = new TableKunenaTopics($this->getDBDriver());
        $table->load(1);
        $plugin->onFinderBeforeSave(new BeforeSaveEvent('onFinderBeforeSave', ['context' => 'com_kunena.topic', 'subject' => $table, 'isNew' => false]));
        $change();
        $table->load(1);
        $plugin->onFinderAfterSave(new AfterSaveEvent('onFinderAfterSave', ['context' => 'com_kunena.topic', 'subject' => $table, 'isNew' => false]));
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
