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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use RuntimeException;

/**
 * The recount of a topic whose first and last posts are longer than 64 KB (issue #9719): the topic keeps a copy of both posts,
 * which must fit in its columns, also in the strict SQL mode in which the copy failed with "Data too long".
 */
class KunenaTopicHelperRecountLongPostTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const CATEGORY = 2;

    private const TOPIC = 1;

    /**
     * The objects of Joomla which the test replaces, put back after the test.
     */
    private array $saved = [];

    private string $first;

    private string $last;

    protected function setUp(): void
    {
        parent::setUp();

        // The helper needs the framework of Joomla.
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        // The helper reads the database of the container of Joomla: that is the database of the tests here. When a query fails,
        // the helper gives the error to the application, an administrator one, which throws it: the test fails with the error of
        // the database ("Data too long for column 'first_post_message'").
        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('isClient')->willReturnCallback(static fn ($client) => $client === 'administrator');
        $application->method('enqueueMessage')->willReturnCallback(static function ($message) {
            throw new RuntimeException($message);
        });

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
        ];

        Factory::$container   = $container;
        Factory::$application = $application;

        // Two texts longer than a text column (65,535 bytes), of different lengths, so that a copy of the wrong post or a cut
        // copy shows.
        $this->first = str_repeat('First post. ', 5800);
        $this->last  = str_repeat('The last post. ', 4700);

        $this->insertRow('#__kunena_categories', ['id' => self::CATEGORY, 'parentid' => 0, 'name' => 'Category', 'alias' => '2-category', 'published' => 1]);
        $this->insertRow('#__kunena_topics', ['id' => self::TOPIC, 'category_id' => self::CATEGORY, 'subject' => 'Long posts', 'hold' => 0]);
        $this->insertRows('#__kunena_messages', [
            ['id' => 1, 'parent' => 0, 'thread' => self::TOPIC, 'catid' => self::CATEGORY, 'subject' => 'Long posts', 'time' => 1000, 'hold' => 0],
            ['id' => 2, 'parent' => 1, 'thread' => self::TOPIC, 'catid' => self::CATEGORY, 'subject' => 'Long posts', 'time' => 1100, 'hold' => 0],
        ]);
        $this->insertRows('#__kunena_messages_text', [
            ['mesid' => 1, 'message' => $this->first],
            ['mesid' => 2, 'message' => $this->last],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            $this->getDBDriver()->setQuery("SET SESSION sql_mode = ''")->execute();

            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
        }

        parent::tearDown();
    }

    public function testTheRecountCopiesTheWholeFirstAndLastPost(): void
    {
        $this->assertGreaterThan(65535, \strlen($this->first));
        $this->assertGreaterThan(65535, \strlen($this->last));
        $this->assertNotSame(\strlen($this->first), \strlen($this->last));

        // The texts are stored whole: the column of the messages is as long as a post.
        $this->assertSame(
            [['mesid' => 1, 'length' => \strlen($this->first)], ['mesid' => 2, 'length' => \strlen($this->last)]],
            array_map(
                static fn ($row) => ['mesid' => (int) $row['mesid'], 'length' => (int) $row['length']],
                $this->fetchAll('SELECT mesid, LENGTH(message) AS length FROM #__kunena_messages_text ORDER BY mesid')
            )
        );

        // In the strict mode a value which does not fit fails ("Data too long for column 'first_post_message'") instead of
        // being cut.
        $this->getDBDriver()->setQuery("SET SESSION sql_mode = 'STRICT_ALL_TABLES'")->execute();

        $this->assertNotFalse(KunenaTopicHelper::recount(self::TOPIC));

        $topic = $this->fetchAll('SELECT first_post_id, last_post_id, first_post_message, last_post_message FROM #__kunena_topics WHERE id = ' . self::TOPIC);

        $this->assertCount(1, $topic);
        $this->assertSame([1, 2], [(int) $topic[0]['first_post_id'], (int) $topic[0]['last_post_id']]);
        $this->assertSame(\strlen($this->first), \strlen($topic[0]['first_post_message']));
        $this->assertSame(\strlen($this->last), \strlen($topic[0]['last_post_message']));
        $this->assertSame($this->first, $topic[0]['first_post_message']);
        $this->assertSame($this->last, $topic[0]['last_post_message']);
    }
}
