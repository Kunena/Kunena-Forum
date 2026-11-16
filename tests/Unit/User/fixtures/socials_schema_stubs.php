<?php

namespace Joomla\CMS {
    class Factory
    {
        public static $database;
        public static $application;

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
            return self::$application;
        }
    }
}

namespace Joomla\CMS\Language {
    class Text
    {
        public static function _($key)
        {
            return $key;
        }
    }
}

namespace Kunena\Forum\Libraries\User {
    class KunenaUserHelper
    {
        public static $user;

        public static function getMyself()
        {
            return self::$user;
        }
    }
}

namespace Kunena\Forum\Libraries\Error {
    class KunenaError
    {
        public static $error;

        public static function displayDatabaseError($exception): void
        {
            self::$error = $exception;
        }
    }
}
