<?php

namespace Joomla\CMS {
    class Factory
    {
        public static $database;

        public static function getContainer()
        {
            return new class () {
                public function get($name)
                {
                    return Factory::$database;
                }
            };
        }
    }
}

namespace Kunena\Forum\Libraries\Cache {
    class KunenaCacheHelper
    {
        public static function clearCategories(): void
        {
        }
    }
}

namespace Kunena\Forum\Libraries\Profiler {
    class KunenaProfiler
    {
        public static function getInstance()
        {
            throw new \RuntimeException('Application is not initialized');
        }
    }
}

namespace Kunena\Tests\Unit\Topic {
    class RecountDatabase
    {
        private $query;
        private int $affectedRows = 0;

        public function __construct(private \PDO $sqlite)
        {
        }

        public function quoteName($name, $alias = null)
        {
            $quoted = implode('.', array_map(static fn ($part) => '`' . $part . '`', explode('.', $name)));

            return $alias === null ? $quoted : $quoted . ' AS `' . $alias . '`';
        }

        public function createQuery()
        {
            return new RecountQuery();
        }

        public function setQuery($query): void
        {
            $this->query = $query;
        }

        public function execute(): void
        {
            if (\is_string($this->query)) {
                $this->affectedRows = 0;
                return;
            }

            $ids = $this->sqlite->query($this->query->selectIds())->fetchAll(\PDO::FETCH_COLUMN);
            $this->affectedRows = 0;

            foreach ($ids as $id) {
                $this->affectedRows += $this->sqlite->exec('UPDATE `#__kunena_categories` SET numTopics = 0, numPosts = 0 WHERE id = ' . (int) $id);
            }
        }

        public function getAffectedRows(): int
        {
            return $this->affectedRows;
        }
    }

    class RecountQuery
    {
        private string $table;
        private string $join;
        private array $conditions = [];

        public function update($table)
        {
            $this->table = $table;
            return $this;
        }

        public function leftJoin($join)
        {
            $this->join = $join;
            return $this;
        }

        public function set($fields)
        {
            return $this;
        }

        public function where($condition)
        {
            $this->conditions[] = $condition;
            return $this;
        }

        public function selectIds(): string
        {
            return 'SELECT c.id FROM ' . $this->table . ' LEFT JOIN ' . $this->join . ' WHERE ' . implode(' AND ', $this->conditions);
        }
    }
}
