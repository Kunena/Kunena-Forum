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

namespace Kunena\Tests\Integration\Plugin\Privacy\Kunena;

use Joomla\CMS\Event\Privacy\ExportRequestEvent;
use Joomla\CMS\Event\Privacy\RemoveDataEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Plugin\Privacy\Kunena\Extension\Kunena;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;

/**
 * A request to remove the data of a user (the privacy tools of Joomla) removes the karma he gave and the karma he got, as it does
 * for the thank yous, the follows and the ignores. The karma which other users gave him stayed, with his id.
 */
class RemoveDataKarmaTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    private bool $karma = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_privacy_kunena/src/Helper/KunenaHelper.php';
        require_once \dirname(__DIR__, 5) . '/src/plugins/plg_privacy_kunena/src/Extension/Kunena.php';

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $container->share('DatabaseDriver', static fn () => $driver);

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        // The table of the karma is in the tables of an installation (create_tables.utf8.sql), not in the schema which makes the
        // tables of the tests: it is there for the test only.
        $driver->setQuery('CREATE TABLE IF NOT EXISTS #__kunena_karma (id int(11) NOT NULL AUTO_INCREMENT, userid int(11) NOT NULL, target_userid int(11) NOT NULL, time int(11) NOT NULL DEFAULT 0, PRIMARY KEY (id))')->execute();
        $driver->setQuery('DELETE FROM #__kunena_karma')->execute();
        $this->karma = true;
    }

    protected function tearDown(): void
    {
        if ($this->karma) {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS #__kunena_karma')->execute();
        }

        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testTheKarmaGivenByAndToTheUserIsRemoved(): void
    {
        // The user 42 gave karma to 7, got karma from 8; and 7 gave karma to 8.
        foreach ([[42, 7], [8, 42], [7, 8]] as [$from, $to]) {
            $this->getDBDriver()->setQuery("INSERT INTO #__kunena_karma (userid, target_userid, time) VALUES ($from, $to, 1000)")->execute();
        }

        $user     = new User();
        $user->id = 42;

        $event = $this->createStub(RemoveDataEvent::class);
        $event->method('getUser')->willReturn($user);

        (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor()->onPrivacyRemoveData($event);

        $this->assertEquals(
            [['userid' => 7, 'target_userid' => 8]],
            $this->fetchAll('SELECT userid, target_userid FROM #__kunena_karma ORDER BY id')
        );
    }
}
