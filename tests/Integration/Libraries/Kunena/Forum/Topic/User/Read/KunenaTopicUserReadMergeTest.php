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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic\User\Read;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\User\Read\KunenaTopicUserReadHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;

/**
 * The read marks of a merge: a user who read only one of the two topics has not read the messages of the other one. His mark was
 * kept as it was, so the messages of the other topic which are older than it were read for him: the merged topic had no "new"
 * marker and no new count for a user who never opened the topic with these messages.
 */
class KunenaTopicUserReadMergeTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const OLD = 10;

    private const TARGET = 20;

    private ?Container $savedContainer = null;

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

        // The user 1 read only the merged topic (at 5000), the user 2 only the target (at 5000), the user 3 both (4000 and 4500).
        $this->insertRows('#__kunena_user_read', [
            ['user_id' => 1, 'topic_id' => self::OLD, 'category_id' => 2, 'message_id' => 11, 'time' => 5000],
            ['user_id' => 2, 'topic_id' => self::TARGET, 'category_id' => 2, 'message_id' => 21, 'time' => 5000],
            ['user_id' => 3, 'topic_id' => self::OLD, 'category_id' => 2, 'message_id' => 11, 'time' => 4000],
            ['user_id' => 3, 'topic_id' => self::TARGET, 'category_id' => 2, 'message_id' => 21, 'time' => 4500],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->savedContainer !== null) {
            Factory::$container = $this->savedContainer;
        }

        parent::tearDown();
    }

    private static function topic(int $id, int $firstTime): KunenaTopic
    {
        $topic                  = (new ReflectionClass(KunenaTopic::class))->newInstanceWithoutConstructor();
        $topic->id              = $id;
        $topic->category_id     = 2;
        $topic->first_post_time = $firstTime;

        return $topic;
    }

    public function testAUserWhoReadOneTopicHasNotReadTheOther(): void
    {
        // The merged topic starts at 3000, the target at 2000 (its first post is 2000 before and after the merge).
        $this->assertTrue(KunenaTopicUserReadHelper::merge(self::topic(self::OLD, 3000), self::topic(self::TARGET, 2000), 2000));

        $marks = array_map(
            'intval',
            array_column($this->fetchAll('SELECT user_id, time FROM #__kunena_user_read WHERE topic_id = ' . self::TARGET . ' ORDER BY user_id'), 'time', 'user_id')
        );

        // Before the first message of the topic which he did not read; the earlier of the two marks for the user who read both.
        $this->assertSame(['1' => 1999, '2' => 2999, '3' => 4000], $marks);
        $this->assertSame([], $this->fetchAll('SELECT user_id FROM #__kunena_user_read WHERE topic_id = ' . self::OLD));
    }
}
