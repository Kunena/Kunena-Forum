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

namespace Kunena\Tests\Integration\Component\Administrator\Install;

use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\ComponentRecord;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use Kunena\Tests\Unit\Support\Files;
use ReflectionProperty;

/**
 * The upgrade to 6.4 moves the social websites of the users from their own columns into the column socials, then removes the
 * columns. Only the first 100 users were moved (chunk() gave the first chunk), and only the users without a ban date: the others
 * lost their social websites for good.
 */
class MoveUsersSocialsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    private bool $column = false;

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

        // Saving the socials clears the cache, which does nothing here.
        $container->share(CacheControllerFactoryInterface::class, fn () => $this->createStub(CacheControllerFactoryInterface::class));

        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        // The socials read the providers of the admin from the parameters of the component: none here.
        $record         = new ComponentRecord(['option' => 'com_kunena', 'enabled' => 1]);
        $record->params = new Registry();
        $components     = new ReflectionProperty(ComponentHelper::class, 'components');

        $this->saved        = ['container' => Factory::$container, 'language' => Factory::$language, 'components' => $components->getValue()];
        Factory::$container = $container;
        Factory::$language  = $language;
        $components->setValue(null, ['com_kunena' => $record]);

        // The column of a social website of a forum before 6.4: the tests add it, as the forum had it.
        $driver->setQuery('ALTER TABLE #__kunena_users ADD facebook varchar(75) NULL')->execute();
        $this->column = true;

        require_once Files::path('src/admin/install/sql/updates/php/6.4.0-2024-11-02_move_users_socials.php');
    }

    protected function tearDown(): void
    {
        if ($this->column) {
            $this->getDBDriver()->setQuery('ALTER TABLE #__kunena_users DROP facebook')->execute();
        }

        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            Factory::$language  = $this->saved['language'];
            (new ReflectionProperty(ComponentHelper::class, 'components'))->setValue(null, $this->saved['components']);
        }

        parent::tearDown();
    }

    public function testTheSocialsOfEveryUserAreMoved(): void
    {
        $rows = [];

        // 150 users; the user 150 was banned once (his ban is over).
        for ($id = 1; $id <= 150; $id++) {
            $rows[] = ['userid' => $id, 'facebook' => "fb$id", 'banned' => $id === 150 ? '2020-01-01 00:00:00' : '1000-01-01 00:00:00'];
        }

        $this->insertRows('#__kunena_users', $rows);

        kunena_640_2024_11_02_move_users_socials(null);

        foreach ([1, 101, 150] as $id) {
            $socials = json_decode((string) $this->fetchAll('SELECT socials FROM #__kunena_users WHERE userid = ' . $id)[0]['socials'], true);

            $this->assertSame("fb$id", $socials['facebook']['value'] ?? null, "the user $id");
        }
    }
}
