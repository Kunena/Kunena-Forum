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
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The messages of a topic posted in the same second have each their own place, in the order of their ids (the order of the page of
 * the topic): they counted as neither before nor after each other, so both had the same place, and the link of one of them opened
 * the page of the other.
 */
class KunenaMessageHelperLocationSameTimeTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private mixed $container = null;

    protected function setUp(): void
    {
        parent::setUp();

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $this->container  = Factory::$container;
        Factory::$container = $container;

        KunenaMessageHelper::cleanup();

        // The topic 1: the message 1, then the messages 2, 3 and 4 in the same second.
        foreach ([1 => 1000, 2 => 2000, 3 => 2000, 4 => 2000] as $id => $time) {
            $this->insertRow('#__kunena_messages', ['id' => $id, 'thread' => 1, 'catid' => 2, 'time' => $time, 'hold' => 0]);
        }
    }

    protected function tearDown(): void
    {
        KunenaMessageHelper::cleanup();
        Factory::$container = $this->container;

        parent::tearDown();
    }

    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    public function testEachMessageHasItsOwnPlace(): void
    {
        $places = [];

        foreach ([1, 2, 3, 4] as $id) {
            $places[$id] = [
                'oldest first' => KunenaMessageHelper::getLocation($id, 'asc', [0 => 0]),
                'newest first' => KunenaMessageHelper::getLocation($id, 'desc', [0 => 0]),
            ];
        }

        $this->assertSame(
            [
                1 => ['oldest first' => 0, 'newest first' => 3],
                2 => ['oldest first' => 1, 'newest first' => 2],
                3 => ['oldest first' => 2, 'newest first' => 1],
                4 => ['oldest first' => 3, 'newest first' => 0],
            ],
            $places
        );
    }
}
