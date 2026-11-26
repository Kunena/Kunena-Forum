<?php

namespace Joomla\CMS\Date {
    class Date
    {
        private int $time;

        public function __construct($date = 'now')
        {
            $this->time = strtotime($date);
        }

        public function toSql(): string
        {
            return gmdate('Y-m-d H:i:s', $this->time);
        }

        public function toUnix(): int
        {
            return $this->time;
        }
    }
}

namespace Joomla\CMS {
    class Factory
    {
        public static $database;
        public static $identity;

        public static function getContainer()
        {
            return new class () {
                public function get($name)
                {
                    return Factory::$database;
                }
            };
        }

        public static function getApplication()
        {
            return new class () {
                public function getIdentity()
                {
                    return Factory::$identity;
                }
            };
        }
    }
}

namespace Joomla\CMS\Language {
    class Text
    {
        public static function _($key, ...$args)
        {
            return $key;
        }

        public static function sprintf($key, ...$args)
        {
            return $key . ':' . implode(',', $args);
        }
    }
}

namespace Joomla\Database {
    interface DatabaseInterface {}
    class DatabaseDriver implements DatabaseInterface {}
}

namespace Kunena\Forum\Libraries\Database {
    class KunenaDatabaseObject {}
}

namespace Kunena\Forum\Libraries\Date {
    class KunenaDate
    {
        public static function getInstance($date)
        {
            return new class ($date) {
                public function __construct(private string $date) {}
                public function toKunena()
                {
                    return $this->date;
                }
            };
        }
    }
}

namespace Kunena\Forum\Libraries\Exception {
    class KunenaException extends \RuntimeException {}
}

namespace Kunena\Forum\Libraries\User {
    class KunenaUser
    {
        public int $userid = 0;
        public bool $moderator = false;
        public bool $admin = false;
        public bool $exists = false;

        public function isModerator($category = null) { return $this->moderator; }
        public function isAdmin() { return $this->admin; }
        public function exists() { return $this->exists; }
        public function isBanned() { return false; }
        public function getName() { return 'user'; }
    }

    class KunenaUserHelper
    {
        public static $user;
        public static $users = [];
        public static string $ip = '';

        public static function getMyself() { return self::$user; }
        public static function get($id) { return self::$users[$id] ?? new KunenaUser(); }
        public static function getUserIp(): string { return self::$ip; }
    }
}

namespace Kunena\Forum\Libraries\Tables {
    class KunenaTable
    {
        protected $_tbl;
        protected $_tbl_key;
        protected $_db;
        private bool $existing = false;

        public function __construct($table, $key, $db)
        {
            $this->_tbl = $table;
            $this->_tbl_key = $key;
            $this->_db = $db;
        }

        public function reset()
        {
            foreach ($this->getProperties() as $key => $value) {
                $this->$key = null;
            }
        }

        public function getProperties(): array
        {
            return array_intersect_key(get_object_vars($this), array_flip([
                'id', 'userid', 'ip', 'blocked', 'expiration', 'created_by', 'created_time',
                'reason_private', 'reason_public', 'modified_by', 'modified_time', 'comments', 'params',
            ]));
        }

        public function bind($data, $ignore = [])
        {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }

            return true;
        }

        public function load($id)
        {
            if (!isset($this->_db->rows[$id])) {
                return false;
            }

            return $this->bind($this->_db->rows[$id]);
        }

        public function exists($value)
        {
            $this->existing = $value;
        }

        public function store()
        {
            if (!$this->existing) {
                $this->id = count($this->_db->rows) + 1;
            }

            $this->_db->rows[$this->id] = $this->getProperties();

            return true;
        }
    }
}
