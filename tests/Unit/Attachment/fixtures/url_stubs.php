<?php

namespace Joomla\CMS {
    class Factory
    {
        public static function getContainer()
        {
            return new class () {
                public function get($name)
                {
                    return new class () {
                        public function get($name)
                        {
                            return false;
                        }
                    };
                }
            };
        }
    }
}

namespace Joomla\CMS\Uri {
    class Uri
    {
        public static function base()
        {
            return 'https://forum.example/';
        }
    }
}

namespace Kunena\Forum\Libraries\Route {
    class KunenaRoute
    {
        public static function _($url, $escape)
        {
            return $escape ? htmlspecialchars($url, ENT_COMPAT, 'UTF-8') : $url;
        }
    }
}
