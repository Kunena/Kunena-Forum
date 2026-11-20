<?php

namespace Kunena\Tests\Unit\Topic;

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class TopicDeleteTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDeleteRemovesOnlyTheTopicsTextsAndRecountsItsCategory(): void
    {
        $db = $this->setUpDatabase();
        $topic = $this->topic(1, 10);

        self::assertTrue($topic->delete());
        self::assertSame([201], $db->query('SELECT mesid FROM `#__kunena_messages_text`')->fetchAll(\PDO::FETCH_COLUMN));
        self::assertSame([201], $db->query('SELECT id FROM `#__kunena_messages`')->fetchAll(\PDO::FETCH_COLUMN));
        self::assertSame([201], $db->query('SELECT postid FROM `#__kunena_thankyou`')->fetchAll(\PDO::FETCH_COLUMN));
        self::assertSame([[10, 1, 1], [20, 1, 1]], $db->query('SELECT * FROM categories ORDER BY id')->fetchAll(\PDO::FETCH_NUM));
        self::assertSame([10], KunenaCategoryHelper::$recounts);
        self::assertSame(1, KunenaUserHelper::$recounts);
        self::assertSame(1, KunenaMessageThankyouHelper::$recounts);

        self::assertTrue($this->topic(2, 10)->delete());
        self::assertSame([], $db->query('SELECT mesid FROM `#__kunena_messages_text`')->fetchAll(\PDO::FETCH_COLUMN));
        self::assertSame([[10, 0, 0], [20, 1, 1]], $db->query('SELECT * FROM categories ORDER BY id')->fetchAll(\PDO::FETCH_NUM));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testDeleteWithoutRecountAndShadowDelete(): void
    {
        $db = $this->setUpDatabase();
        self::assertTrue($this->topic(1, 10)->delete(false));
        self::assertSame([], KunenaCategoryHelper::$recounts);
        self::assertSame(0, KunenaUserHelper::$recounts);
        self::assertSame([201], $db->query('SELECT mesid FROM `#__kunena_messages_text`')->fetchAll(\PDO::FETCH_COLUMN));

        $shadow = $this->topic(2, 10);
        $shadow->moved_id = 3;
        self::assertTrue($shadow->delete());
        self::assertSame([], KunenaCategoryHelper::$recounts);
        self::assertSame([201], $db->query('SELECT mesid FROM `#__kunena_messages_text`')->fetchAll(\PDO::FETCH_COLUMN));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRecountFailureIsReported(): void
    {
        $this->setUpDatabase();
        KunenaCategoryHelper::$fail = true;
        self::assertFalse($this->topic(1, 10)->delete());
    }

    private function setUpDatabase(): \PDO
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The PDO SQLite extension is required.');
        }

        \define('_JEXEC', 1);
        require_once __DIR__ . '/fixtures/delete_stubs.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/Forum/Topic/KunenaTopic.php';

        $db = new \PDO('sqlite::memory:');
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->exec('CREATE TABLE topics (id INTEGER, category_id INTEGER, posts INTEGER)');
        $db->exec('CREATE TABLE categories (id INTEGER, topics INTEGER, posts INTEGER)');
        $db->exec('CREATE TABLE `#__kunena_messages` (id INTEGER, thread INTEGER)');
        $db->exec('CREATE TABLE `#__kunena_messages_text` (mesid INTEGER)');
        $db->exec('CREATE TABLE `#__kunena_thankyou` (postid INTEGER)');
        $db->exec('CREATE TABLE `#__kunena_user_topics` (topic_id INTEGER)');
        $db->exec('CREATE TABLE `#__kunena_user_read` (topic_id INTEGER)');
        $db->exec('CREATE TABLE `#__kunena_rate` (topic_id INTEGER)');
        $db->exec('INSERT INTO topics VALUES (1, 10, 2), (2, 10, 1), (3, 20, 1)');
        $db->exec('INSERT INTO categories VALUES (10, 2, 3), (20, 1, 1)');
        $db->exec('INSERT INTO `#__kunena_messages` VALUES (101, 1), (102, 1), (201, 2)');
        $db->exec('INSERT INTO `#__kunena_messages_text` VALUES (101), (102), (201)');
        $db->exec('INSERT INTO `#__kunena_thankyou` VALUES (101), (102), (201)');
        Factory::$database = new DeleteDatabase($db);

        return $db;
    }

    private function topic(int $id, int $categoryId): KunenaTopic
    {
        $topic = new KunenaTopic();
        $topic->id = $id;
        $topic->category_id = $categoryId;
        $topic->moved_id = 0;
        $topic->poll_id = 0;

        return $topic;
    }
}
