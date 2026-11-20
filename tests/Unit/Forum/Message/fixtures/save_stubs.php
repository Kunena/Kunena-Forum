<?php

namespace Kunena\Forum\Libraries\Database {
    class KunenaDatabaseObject
    {
        protected $_exists = false;
        public string $saveOutcome = 'success';
        public ?\Exception $failure = null;

        public function exists($exists = null): bool
        {
            return $this->_exists;
        }

        public function save()
        {
            if ($this->saveOutcome === 'before') {
                throw $this->failure;
            }

            if ($this->saveOutcome === 'false') {
                return false;
            }

            $this->_exists = true;

            if ($this->saveOutcome === 'after') {
                throw $this->failure;
            }

            return true;
        }
    }
}

namespace Kunena\Forum\Libraries\User {
    class KunenaUserHelper
    {
        public static function getMyself()
        {
            return (object) ['userid' => 1];
        }
    }
}

namespace Kunena\Forum\Libraries\Forum\Topic {
    class KunenaTopic
    {
        public int $id = 42;
        public int $deleted = 0;
        public int $saved = 0;

        public function __construct(private bool $exists)
        {
        }

        public function exists(): bool
        {
            return $this->exists;
        }

        public function save($cascade = true): bool
        {
            $this->saved++;
            $this->exists = true;

            return true;
        }

        public function delete(): bool
        {
            $this->deleted++;
            $this->exists = false;

            return true;
        }
    }
}
