<?php

namespace Kunena\Tests\Unit\Topic;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class CategoryRecountAfterDeleteTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testEmptyCategoryRecountIgnoresShadowTopicsAndOtherCategories(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The PDO SQLite extension is required.');
        }

        \define('_JEXEC', 1);
        require_once __DIR__ . '/fixtures/category_recount_stubs.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/Forum/Category/KunenaCategoryHelper.php';

        $sqlite = new \PDO('sqlite::memory:');
        $sqlite->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $sqlite->exec('CREATE TABLE `#__kunena_categories` (id INTEGER, numTopics INTEGER, numPosts INTEGER)');
        $sqlite->exec('CREATE TABLE `#__kunena_topics` (id INTEGER, category_id INTEGER, hold INTEGER, moved_id INTEGER)');
        $sqlite->exec('INSERT INTO `#__kunena_categories` VALUES (10, 2, 2), (20, 3, 3)');
        $sqlite->exec('INSERT INTO `#__kunena_topics` VALUES (1, 10, 0, 7)');
        \Joomla\CMS\Factory::$database = new RecountDatabase($sqlite);

        \Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper::recount(10);

        self::assertSame([[10, 0, 0], [20, 3, 3]], $sqlite->query('SELECT * FROM `#__kunena_categories` ORDER BY id')->fetchAll(\PDO::FETCH_NUM));
    }
}
