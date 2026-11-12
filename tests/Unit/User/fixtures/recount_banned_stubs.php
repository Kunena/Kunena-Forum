<?php

namespace Joomla\CMS {
    class Factory
    {
        public static $database;

        public static function getApplication()
        {
            return new class () {
                public function getIdentity()
                {
                    return (object) ['id' => 0];
                }
            };
        }

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

namespace Kunena\Forum\Libraries\User {
    class KunenaUser
    {
        public function __construct($id)
        {
        }
    }

}

namespace Kunena\Forum\Libraries\Factory {
    class KunenaFactory
    {
        public static function getAvatarIntegration()
        {
            return new class () {
                public function load($ids)
                {
                }
            };
        }
    }
}
