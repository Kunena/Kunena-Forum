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
use Kunena\Forum\Libraries\Tools\KunenaRatingAbuse;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The tool "Rating abuse" removes the ratings of a user: the rating which the lists of topics show is stored with the topic, and
 * it kept the score which the removed ratings had made.
 */
class KunenaRatingAbuseRemoveTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private mixed $savedContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The tool reads the database of the container of Joomla: that is the database of the tests here.
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $this->savedContainer = Factory::$container;
        Factory::$container   = $container;

        $this->insertRows('#__kunena_topics', [
            // Rated by the abuser only (1 star).
            ['id' => 1, 'category_id' => 2, 'subject' => 'One', 'rating' => 1],
            // Rated 1 by the abuser, 5 and 4 by others.
            ['id' => 2, 'category_id' => 2, 'subject' => 'Two', 'rating' => 3],
            // Not rated by the abuser.
            ['id' => 3, 'category_id' => 2, 'subject' => 'Three', 'rating' => 2],
        ]);
        $this->insertRows('#__kunena_rate', [
            ['topic_id' => 1, 'userid' => 101, 'rate' => 1, 'time' => '2026-01-01 00:00:00'],
            ['topic_id' => 2, 'userid' => 101, 'rate' => 1, 'time' => '2026-01-01 00:00:00'],
            ['topic_id' => 2, 'userid' => 102, 'rate' => 5, 'time' => '2026-01-01 00:00:00'],
            ['topic_id' => 2, 'userid' => 103, 'rate' => 4, 'time' => '2026-01-01 00:00:00'],
            ['topic_id' => 3, 'userid' => 102, 'rate' => 2, 'time' => '2026-01-01 00:00:00'],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    public function testTheTopicsOfTheRemovedRatingsGetTheRatingOfTheRatingsLeft(): void
    {
        $this->assertSame(2, KunenaRatingAbuse::removeOnSite([101]));

        $this->assertSame(
            ['1' => 0, '2' => 5, '3' => 2],
            array_map('intval', array_column($this->fetchAll('SELECT id, rating FROM #__kunena_topics ORDER BY id'), 'rating', 'id'))
        );
    }
}
