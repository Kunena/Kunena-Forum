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

use Joomla\CMS\Cache\CacheController;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\ComponentRecord;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\User\KunenaRankGroups;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionProperty;

/**
 * Kunena > Ranks: the user group of a rank is saved in the parameters of the component. Joomla keeps the parameters of the
 * components in its cache (the group _system) when the cache of the site is on: the group was saved without emptying it, so the
 * forum kept the old groups, and the next save of the parameters (the badges, the socials, the options) wrote them back.
 */
class KunenaRankGroupsSaveTest extends IntegrationTestCase implements DBTestInterface
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

    public function testTheCacheOfTheParametersIsEmptiedAfterTheSave(): void
    {
        KunenaRankGroups::setGroup(5, 2);

        $params = json_decode((string) $this->fetchAll('SELECT params FROM #__extensions')[0]['params'], true);

        $this->assertSame('Forum', $params['boardTitle']);
        $this->assertSame(['5' => 2], $params['rankgroups']);
        $this->assertContains('_system', $this->cleaned, 'The parameters of the component stay in the cache of Joomla.');
    }
}
