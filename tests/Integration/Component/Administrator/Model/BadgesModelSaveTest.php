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

namespace Kunena\Tests\Integration\Component\Administrator\Model;

use Joomla\CMS\Cache\CacheController;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\ComponentRecord;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Kunena\Forum\Administrator\Model\BadgesModel;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * Kunena > Badges saves the badges in the parameters of the component. Joomla keeps the parameters of the components in its cache
 * (the group _system) when the cache of the site is on: the badges were saved without emptying it, so the forum showed the old
 * badges, and the next save of the parameters (the socials, the options of the forum, a rank with a group) wrote the old parameters
 * back: the new badges were lost.
 */
class BadgesModelSaveTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    /** @var string[] */
    private array $cleaned = [];

    public function getSchemasToLoad(): array
    {
        return [];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver = $this->getDBDriver();

        // The row of the component in a table of the extensions of its own (the test database has none).
        $driver->setQuery('DROP TABLE IF EXISTS #__extensions')->execute();
        $driver->setQuery(
            'CREATE TABLE #__extensions (extension_id int NOT NULL AUTO_INCREMENT, element varchar(100) NOT NULL, type varchar(20) NOT NULL,'
            . ' params text NOT NULL, PRIMARY KEY (extension_id)) DEFAULT CHARSET=utf8mb4'
        )->execute();
        $driver->setQuery("INSERT INTO #__extensions (element, type, params) VALUES ('com_kunena', 'component', '{\"boardTitle\":\"Forum\"}')")->execute();

        // The cache: what is emptied is recorded.
        $cleaned = &$this->cleaned;
        $cache   = new class ($cleaned) extends CacheController {
            /** @var string[] */
            private array $groups;

            public function __construct(array &$groups)
            {
                $this->groups = &$groups;
            }

            public function clean($group = null)
            {
                $this->groups[] = (string) $group;

                return true;
            }
        };
        $factory = $this->createStub(CacheControllerFactoryInterface::class);
        $factory->method('createCacheController')->willReturn($cache);

        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(CacheControllerFactoryInterface::class, static fn () => $factory);

        $record         = new ComponentRecord(['option' => 'com_kunena', 'enabled' => 1]);
        $record->params = new Registry(['boardTitle' => 'Forum']);
        $components     = new ReflectionProperty(ComponentHelper::class, 'components');

        $this->saved        = ['container' => Factory::$container, 'components' => $components->getValue()];
        Factory::$container = $container;
        $components->setValue(null, ['com_kunena' => $record]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS #__extensions')->execute();

            Factory::$container = $this->saved['container'];
            (new ReflectionProperty(ComponentHelper::class, 'components'))->setValue(null, $this->saved['components']);
        }

        parent::tearDown();
    }

    /**
     * A badge with a label but no user group and no rank is refused, and nothing is saved: it was saved ("Badges saved"), and was
     * gone when the page came back (the badges read leave such a row out). An empty row of the form is no badge.
     */
    public function testABadgeWithoutGroupAndRankIsRefused(): void
    {
        $before = $this->fetchAll('SELECT params FROM #__extensions');
        $model  = (new ReflectionClass(BadgesModel::class))->newInstanceWithoutConstructor();

        // The texts are their keys, with the value.
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnCallback(static fn ($key) => $key . ' %s');
        $savedLanguage     = Factory::$language;
        Factory::$language = $language;

        try {
            $saved = $model->save(['badges' => [
                ['title' => '', 'color' => '#112233', 'usergroup' => '', 'rank' => ''],
                ['title' => 'Nobody', 'color' => '#112233', 'usergroup' => '', 'rank' => ''],
            ]]);
        } finally {
            Factory::$language = $savedLanguage;
        }

        $this->assertFalse($saved);
        $this->assertStringContainsString('Nobody', (string) $model->getError());
        $this->assertSame($before, $this->fetchAll('SELECT params FROM #__extensions'));
    }

    public function testTheCacheOfTheParametersIsEmptiedAfterTheSave(): void
    {
        $model = (new ReflectionClass(BadgesModel::class))->newInstanceWithoutConstructor();

        $this->assertTrue($model->save(['badges' => [['title' => 'Helper', 'color' => '#112233', 'usergroup' => '2', 'rank' => '']]]));

        $params = json_decode((string) $this->fetchAll('SELECT params FROM #__extensions')[0]['params'], true);

        $this->assertSame('Forum', $params['boardTitle']);
        $this->assertSame('Helper', $params['badges']['badges0']['title']);
        $this->assertContains('_system', $this->cleaned, 'The parameters of the component stay in the cache of Joomla.');
    }
}
