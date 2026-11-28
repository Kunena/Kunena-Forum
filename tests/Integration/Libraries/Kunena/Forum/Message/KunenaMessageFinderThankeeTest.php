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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Message;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageFinder;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The messages for which a user was thanked (the list of the thank yous received, MessageListingRecentDisplay): each message once.
 * The finder joined the thank yous, a row for each member who thanked, so the count was too high and the pages short or empty.
 */
class KunenaMessageFinderThankeeTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private ?Container $savedContainer = null;

    private mixed $savedConfig = null;

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

        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        // The finder takes the number of messages of a page from the configuration.
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['messagesPerPage' => 20]);
        $instance          = new ReflectionProperty(KunenaConfig::class, 'instance');
        $this->savedConfig = $instance->getValue();
        $instance->setValue(null, $config);

        // The messages 1 and 2 of the user 42, the message 3 of the user 43. Three members thanked for the message 1.
        $rows = [];

        foreach ([1 => 42, 2 => 42, 3 => 43] as $id => $userid) {
            $rows[] = ['id' => $id, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'name' => 'U' . $userid, 'userid' => $userid,
                'email' => '', 'subject' => 'S', 'time' => $id, 'ip' => '', 'hold' => 0];
        }

        $this->insertRows('#__kunena_messages', $rows);
        $this->insertRows('#__kunena_thankyou', [
            ['postid' => 1, 'userid' => 7, 'targetuserid' => 42, 'time' => '2026-01-01 00:00:00'],
            ['postid' => 1, 'userid' => 8, 'targetuserid' => 42, 'time' => '2026-01-01 00:00:00'],
            ['postid' => 1, 'userid' => 9, 'targetuserid' => 42, 'time' => '2026-01-01 00:00:00'],
            ['postid' => 2, 'userid' => 7, 'targetuserid' => 42, 'time' => '2026-01-01 00:00:00'],
            ['postid' => 3, 'userid' => 7, 'targetuserid' => 43, 'time' => '2026-01-01 00:00:00'],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
            (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->savedConfig);
        }

        parent::tearDown();
    }

    public function testEachMessageForWhichTheUserWasThankedCountsOnce(): void
    {
        $user = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaUser::class, 'userid'))->setValue($user, 42);

        $this->assertSame(2, (new KunenaMessageFinder())->filterByUser($user, 'thankee')->count());
    }

    /**
     * A message which he made anonymous after the thank yous (its author is 0, the thank yous keep him as their target) is not in his
     * list: the list of his profile told every member who wrote the anonymous message.
     */
    public function testAMessageMadeAnonymousIsNotInTheList(): void
    {
        $this->getDBDriver()->setQuery('UPDATE #__kunena_messages SET userid = 0, name = ' . $this->getDBDriver()->quote('Anonymous') . ' WHERE id = 2')->execute();

        $user = (new ReflectionClass(KunenaUser::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaUser::class, 'userid'))->setValue($user, 42);

        $this->assertSame(1, (new KunenaMessageFinder())->filterByUser($user, 'thankee')->count());
    }
}
