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

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Tools\KunenaSubscriptions;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;

/**
 * A subscription in the state 2 is a subscription: "first new post only" sets it when it sent the mail, until the user reads the
 * topic. The topic left the subscriptions of the user (he could not end it there), and the tool "Subscriptions" did not end it, so
 * the user was subscribed again at his next visit.
 */
class KunenaSubscriptionsFirstPostTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const USER = 42;

    private mixed $container = null;

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

        $this->container  = Factory::$container;
        Factory::$container = $container;

        // The topic 7, whose first new post was mailed to the user (2), and the topic 8 which he follows (1).
        foreach ([7 => 2, 8 => 1] as $topic => $subscribed) {
            $this->insertRow('#__kunena_topics', ['id' => $topic, 'category_id' => 2, 'subject' => "Topic $topic"]);
            $this->insertRow('#__kunena_user_topics', ['user_id' => self::USER, 'topic_id' => $topic, 'category_id' => 2, 'subscribed' => $subscribed]);
        }
    }

    protected function tearDown(): void
    {
        Factory::$container = $this->container;

        parent::tearDown();
    }

    public function testTheSubscriptionsOfTheUserHaveIt(): void
    {
        $reflection = new ReflectionClass(KunenaTopicFinder::class);
        $finder     = $reflection->newInstanceWithoutConstructor();
        $db         = $this->getDBDriver();
        $reflection->getProperty('db')->setValue($finder, $db);
        $reflection->getProperty('query')->setValue($finder, $db->createQuery()->from($db->quoteName('#__kunena_topics', 'a')));

        $user         = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
        $user->userid = self::USER;

        $finder->filterByUser($user, 'subscribed');
        $query = clone $reflection->getProperty('query')->getValue($finder);
        $query->select('a.id')->order('a.id');

        $this->assertSame([7, 8], array_map('intval', $db->setQuery($query)->loadColumn()));
    }

    public function testTheToolEndsIt(): void
    {
        $this->assertSame(1, KunenaSubscriptions::unsubscribeOnSite('topic', 7));
        $this->assertSame([['subscribed' => 0]], array_map(static fn ($row) => ['subscribed' => (int) $row['subscribed']], $this->fetchAll('SELECT subscribed FROM #__kunena_user_topics WHERE topic_id = 7')));
    }
}
