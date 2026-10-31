<?php

/**
 * Kunena Component
 *
 * Small stand-ins for the classes DoQuote() touches, so the real KunenaBBCode.php can run without Joomla. The layout stand-in
 * renders the real layout file of the template, with the same contract as KunenaLayout (variables are properties, escape()).
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Joomla\CMS\Uri {
    class Uri
    {
        public static function getInstance($uri = null)
        {
            return new self();
        }

        public static function base()
        {
            return 'https://forum.example/';
        }

        public function toString($parts = [])
        {
            return 'https://forum.example';
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

        public static function sprintf($key, ...$args)
        {
            return $key;
        }
    }
}

namespace Kunena\Forum\Libraries\Layout {
    #[\AllowDynamicProperties]
    class KunenaLayout
    {
        public static function factory($name)
        {
            return new self();
        }

        public function getPath()
        {
            return __DIR__ . '/../../../../src/site/template/aurelia/layouts/bbcode/quote/default.php';
        }

        public function set($key, $value)
        {
            $this->$key = $value;

            return $this;
        }

        public function escape($text)
        {
            return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
        }

        public function __toString(): string
        {
            ob_start();
            (function ($file) {
                include $file;
            })->call($this, $this->getPath());

            return ob_get_clean();
        }
    }
}

namespace Kunena\Forum\Libraries\User {
    class KunenaUserHelper
    {
        public static function get($id)
        {
            return new class () {
                public function getName()
                {
                    return 'Some Member';
                }
            };
        }
    }
}

namespace Kunena\Forum\Libraries\Forum\Message {
    class KunenaMessageHelper
    {
        /** @var array<int, object> Messages by id; anything else does not exist. */
        public static $messages = [];

        /** @var array<int, string> Every call of getUrl(), to see that no address was built. */
        public static $urlCalls = [];

        public static function get($id)
        {
            return self::$messages[$id] ?? self::message(false, false, '/never');
        }

        public static function message(bool $exists, bool $readable, string $url)
        {
            return new class ($exists, $readable, $url) {
                public function __construct(private bool $exists, private bool $readable, private string $url)
                {
                }

                public function exists()
                {
                    return $this->exists;
                }

                public function isAuthorised($action = 'read')
                {
                    return $action === 'read' && $this->readable;
                }

                public function getUrl($category = null, $xhtml = true)
                {
                    KunenaMessageHelper::$urlCalls[] = $this->url;

                    return $this->url;
                }
            };
        }
    }
}
