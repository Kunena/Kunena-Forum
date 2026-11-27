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

    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testTheKarmaGivenByAndToTheUserIsRemoved(): void
    {
        // The user 42 gave karma to 7, got karma from 8; and 7 gave karma to 8.
        foreach ([[42, 7], [8, 42], [7, 8]] as [$from, $to]) {
            $this->insertRow('#__kunena_karma', ['userid' => $from, 'target_userid' => $to, 'time' => 1000]);
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

    /**
     * The export of the data of a user has what the removal deletes: also the karma he gave and his subscriptions and
     * favourites, of categories and of topics. They were left out.
     */
    public function testTheExportHasTheKarmaAndTheSubscriptions(): void
    {
        $this->insertRow('#__kunena_karma', ['userid' => 42, 'target_userid' => 7, 'time' => 1000]);
        $this->insertRow('#__kunena_user_categories', ['user_id' => 42, 'category_id' => 3, 'subscribed' => 1, 'params' => '']);
        $this->insertRow('#__kunena_user_topics', ['user_id' => 42, 'topic_id' => 9, 'category_id' => 3, 'subscribed' => 1, 'favorite' => 1]);

        $user     = new User();
        $user->id = 42;

        $domains = [];
        $event   = $this->createStub(ExportRequestEvent::class);
        $event->method('getUser')->willReturn($user);
        $event->method('addResult')->willReturnCallback(static function ($result) use (&$domains): void {
            $domains = $result;
        });

        // The user has no profile of the forum (he never used it): the export is made all the same.
        $plugin = (new ReflectionClass(Kunena::class))->newInstanceWithoutConstructor();
        $plugin->setDatabase($this->getDBDriver());
        $plugin->onPrivacyExportRequest($event);

        $items = [];

        foreach ($domains as $domain) {
            $items[$domain->name] = \count($domain->getItems());
        }

        $this->assertSame(1, $items['kunena_user_karma'] ?? null);
        $this->assertSame(1, $items['kunena_user_categories'] ?? null);
        $this->assertSame(1, $items['kunena_user_topics'] ?? null);
    }
}
