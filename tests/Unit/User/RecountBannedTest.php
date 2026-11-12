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

class RecountBannedTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testRecountBuildsAValidGroupedBanQuery(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The PDO SQLite extension is required.');
        }

        \define('_JEXEC', 1);
        require_once __DIR__ . '/fixtures/recount_banned_stubs.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaUserHelper.php';

        $query = new class () {
            public string $update;
            public string $join;
            public string $set;

            public function update($table)
            {
                $this->update = $table;

                return $this;
            }

            public function join($type, $expression)
            {
                $this->join = $type . ' ' . $expression;

                return $this;
            }

            public function set($expression)
            {
                $this->set = $expression;

                return $this;
            }
        };
        $database = new class ($query) {
            public bool $executed = false;
            public $query;

            public function __construct($query)
            {
                $this->query = $query;
            }

            public function createQuery()
            {
                return $this->query;
            }

            public function quoteName($name, $alias = null)
            {
                $quoted = implode('.', array_map(static fn ($part) => '`' . $part . '`', explode('.', $name)));

                return $alias === null ? $quoted : $quoted . ' AS `' . $alias . '`';
            }

            public function setQuery($query)
            {
                if ($query !== $this->query) {
                    throw new \LogicException('Unexpected query');
                }
            }

            public function execute()
            {
                $this->executed = true;
            }

            public function getAffectedRows()
            {
                return 2;
            }
        };
        Factory::$database = $database;

        $this->assertSame(2, KunenaUserHelper::recountBanned());
        $this->assertTrue($database->executed);
        $this->assertSame('`#__kunena_users` AS `u`', $query->update);
        $this->assertSame('`u`.`banned`=`b`.`banned`', $query->set);
        $this->assertSame(1, preg_match('/^INNER \((.*)\) AS b ON `u`\.`userid`=`b`\.`userid`$/s', $query->join, $matches));

        $sqlite = new \PDO('sqlite::memory:');
        $sqlite->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $sqlite->exec('CREATE TABLE `#__kunena_users_banned` (`userid` INTEGER, `expiration` INTEGER)');
        $sqlite->exec('INSERT INTO `#__kunena_users_banned` VALUES (1, 100), (1, 300), (2, 200)');

        $this->assertSame(
            [['userid' => 1, 'banned' => 300], ['userid' => 2, 'banned' => 200]],
            $sqlite->query($matches[1] . ' ORDER BY `userid`')->fetchAll(\PDO::FETCH_ASSOC)
        );
    }
}
