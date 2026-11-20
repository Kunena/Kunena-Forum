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

namespace Joomla\Database {
    class ParameterType
    {
        public const INTEGER = 1;
    }
}

namespace Kunena\Forum\Libraries\Database {
    class KunenaDatabaseObject
    {
        protected $_exists = true;

        public function __construct($properties = null)
        {
        }

        public function exists(): bool
        {
            return $this->_exists;
        }

        public function delete(): bool
        {
            \Joomla\CMS\Factory::$database->sqlite->exec('DELETE FROM topics WHERE id = ' . (int) $this->id);
            $this->_exists = false;

            return true;
        }
    }
}

namespace Kunena\Forum\Libraries\Forum\Category {
    class KunenaCategoryHelper
    {
        public static array $recounts = [];
        public static bool $fail = false;

        public static function recount($categoryId)
        {
            self::$recounts[] = $categoryId;

            if (self::$fail) {
                return false;
            }

            $db = \Joomla\CMS\Factory::$database->sqlite;
            $db->exec('UPDATE categories SET topics = (SELECT COUNT(*) FROM topics WHERE category_id = ' . (int) $categoryId . '), posts = (SELECT COALESCE(SUM(posts), 0) FROM topics WHERE category_id = ' . (int) $categoryId . ') WHERE id = ' . (int) $categoryId);

            return 1;
        }
    }
}

namespace Kunena\Forum\Libraries\User {
    class KunenaUserHelper
    {
        public static int $recounts = 0;

        public static function recount(): void
        {
            self::$recounts++;
        }
    }
}

namespace Kunena\Forum\Libraries\Forum\Message\Thankyou {
    class KunenaMessageThankyouHelper
    {
        public static int $recounts = 0;

        public static function recount(): void
        {
            self::$recounts++;
        }
    }
}

namespace Kunena\Tests\Unit\Topic {
    class DeleteDatabase
    {
        private $query;

        public function __construct(public \PDO $sqlite)
        {
        }

        public function createQuery()
        {
            return new DeleteQuery();
        }

        public function quoteName($name)
        {
            return '`' . $name . '`';
        }

        public function setQuery($query): void
        {
            $this->query = $query;
        }

        public function execute(): void
        {
            $statement = $this->sqlite->prepare($this->query->sql);
            $statement->execute($this->query->parameters);
        }
    }

    class DeleteQuery
    {
        public string $sql = '';
        public array $parameters = [];

        public function delete($table)
        {
            $this->sql = 'DELETE FROM ' . $table;

            return $this;
        }

        public function where($condition)
        {
            $this->sql .= ' WHERE ' . $condition;

            return $this;
        }

        public function bind($name, &$value, $type): void
        {
            $this->parameters[$name] = &$value;
        }
    }
}
