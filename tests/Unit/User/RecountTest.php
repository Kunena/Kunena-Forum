<?php

/**
 * Kunena Component
 *
 * @package  Kunena.Tests
 * @license  https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class RecountTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRecountInsertsAndUpdatesUserPostCounts(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The PDO SQLite extension is required.');
        }

        \define('_JEXEC', 1);
        require_once __DIR__ . '/fixtures/recount_banned_stubs.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaUserHelper.php';

        $sqlite = new \PDO('sqlite::memory:');
        $sqlite->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $sqlite->exec('CREATE TABLE `#__kunena_users` (`userid` INTEGER PRIMARY KEY, `posts` INTEGER, `status` INTEGER DEFAULT 0)');
        $sqlite->exec('CREATE TABLE `#__kunena_user_topics` (`user_id` INTEGER, `posts` INTEGER)');
        $sqlite->exec('INSERT INTO `#__kunena_users` VALUES (1, 99, 7), (3, 42, 8)');
        $sqlite->exec('INSERT INTO `#__kunena_user_topics` VALUES (1, 2), (1, 3), (2, 4), (2, 0)');

        $database = new class ($sqlite) {
            public ?string $query = null;
            private int $affectedRows = 0;

            public function __construct(private \PDO $sqlite)
            {
            }

            public function createQuery()
            {
                return new class () {
                    private array $columns = [];
                    private string $from;
                    private string $group;

                    public function select($expression)
                    {
                        $this->columns[] = $expression;

                        return $this;
                    }

                    public function from($table)
                    {
                        $this->from = $table;

                        return $this;
                    }

                    public function group($expression)
                    {
                        $this->group = $expression;

                        return $this;
                    }

                    public function __toString(): string
                    {
                        return 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->from . ' GROUP BY ' . $this->group;
                    }
                };
            }

            public function quoteName($name)
            {
                return '`' . $name . '`';
            }

            public function setQuery($query)
            {
                $this->query = $query;
            }

            public function execute()
            {
                if ($this->query === null) {
                    throw new \LogicException('No query was set');
                }

                $sql = str_replace(
                    ' ON DUPLICATE KEY UPDATE `posts`=VALUES(`posts`)',
                    ' ON CONFLICT (`userid`) DO UPDATE SET `posts`=excluded.`posts`',
                    $this->query,
                    $replacements
                );

                if ($replacements !== 1) {
                    throw new \LogicException('Missing duplicate-key update');
                }

                $this->affectedRows = $this->sqlite->exec($sql);
            }

            public function getAffectedRows()
            {
                return $this->affectedRows;
            }
        };
        Factory::$database = $database;

        $this->assertSame(2, KunenaUserHelper::recount());
        $this->assertSame(
            [
                ['userid' => 1, 'posts' => 5, 'status' => 7],
                ['userid' => 2, 'posts' => 4, 'status' => 0],
                ['userid' => 3, 'posts' => 42, 'status' => 8],
            ],
            $sqlite->query('SELECT * FROM `#__kunena_users` ORDER BY `userid`')->fetchAll(\PDO::FETCH_ASSOC)
        );

        $sqlite->exec('UPDATE `#__kunena_user_topics` SET `posts` = 7 WHERE `user_id` = 2 AND `posts` = 4');
        $this->assertSame(2, KunenaUserHelper::recount());
        $this->assertSame(7, $sqlite->query('SELECT `posts` FROM `#__kunena_users` WHERE `userid` = 2')->fetchColumn());
    }
}
