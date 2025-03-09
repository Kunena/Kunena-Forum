<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Error\KunenaError;

/**
 * Class KunenaUserSocials
 *
 * @property string  $x_social
 * @property string  $facebook
 * @property string  $myspace
 * @property string  $linkedin
 * @property string  $linkedin_company
 * @property string  $digg
 * @property string  $skype
 * @property string  $yim
 * @property string  $google
 * @property string  $github
 * @property string  $microsoft
 * @property string  $blogspot
 * @property string  $flickr
 * @property string  $instagram
 * @property string  $qqsocial
 * @property string  $qzone
 * @property string  $weibo
 * @property string  $wechat
 * @property string  $vk
 * @property string  $telegram
 * @property string  $apple
 * @property string  $vimeo
 * @property string  $whatsapp
 * @property string  $youtube
 * @property string  $ok
 * @property string  $pinterest
 * @property string  $reddit
 * @property string  $bluesky_app
 *
 * @since   Kunena 6.4
 */
class KunenaUserSocials
{
    /**
     * @var    string  x_social
     * @since  Kunena 6.4.0
     */
    public $x_social = '';

    /**
     * @var    string  Facebook
     * @since  Kunena 6.4.0
     */
    public $facebook = '';

    /**
     * @var    string  Myspace
     * @since  Kunena 6.4.0
     */
    public $myspace = '';

    /**
     * @var    string  Linkedin
     * @since  Kunena 6.4.0
     */
    public $linkedin = '';

    /**
     * @var    string Linkedin_company
     * @since  Kunena 6.4.0
     */
    public $linkedin_company = '';

    /**
     * @var    string    Digg
     * @since  Kunena 6.4.0
     */
    public $digg = '';

    /**
     * @var    string  Skype
     * @since  Kunena 6.4.0
     */
    public $skype = '';

    /**
     * @var    string  Yim
     * @since  Kunena 6.4.0
     */
    public $yim = '';

    /**
     * @var    string  Google
     * @since  Kunena 6.4.0
     */
    public $google = '';

    /**
     * @var    string  Github
     * @since  Kunena 6.4.0
     */
    public $github = '';

    /**
     * @var    string  Microsoft
     * @since  Kunena 6.4.0
     */
    public $microsoft = '';

    /**
     * @var    string  Blogspot
     * @since  Kunena 6.4.0
     */
    public $blogspot = '';

    /**
     * @var    string  Flickr
     * @since  Kunena 6.4.0
     */
    public $flickr = '';

    /**
     * @var    string  Instagram
     * @since  Kunena 6.4.0
     */
    public $instagram = '';

    /**
     * @var    string  Qqsocial
     * @since  Kunena 6.4.0
     */
    public $qqsocial = '';

    /**
     * @var    string  Qzone
     * @since  Kunena 6.4.0
     */
    public $qzone = '';

    /**
     * @var    string  Weibo
     * @since  Kunena 6.4.0
     */
    public $weibo = '';

    /**
     * @var    string  Wechat
     * @since  Kunena 6.4.0
     */
    public $wechat = '';

    /**
     * @var    string  Vk
     * @since  Kunena 6.4.0
     */
    public $vk = '';

    /**
     * @var    string  Telegram
     * @since  Kunena 6.4.0
     */
    public $telegram = '';

    /**
     * @var    string  Apple
     * @since  Kunena 6.4.0
     */
    public $apple = '';

    /**
     * @var    string  Vimeo
     * @since  Kunena 6.4.0
     */
    public $vimeo = '';

    /**
     * @var    string  Whatsapp
     * @since  Kunena 6.4.0
     */
    public $whatsapp = '';

    /**
     * @var    string  Youtube
     * @since  Kunena 6.4.0
     */
    public $youtube = '';

    /**
     * @var    string  Ok
     * @since  Kunena 6.4.0
     */
    public $ok = '';

    /**
     * @var    string  Pinterest
     * @since  Kunena 6.4.0
     */
    public $pinterest = '';

    /**
     * @var    string  Reddit
     * @since  Kunena 6.4.0
     */
    public $reddit = '';

    /**
     * @var    string  Bluesky_app
     * @since  Kunena 6.4.0
     */
    public $bluesky_app = ''; 
    
    protected $userid = 0;

    /**
     * @return  KunenaUserSocials|mixed
     *
     * @throws  Exception
     * @since   Kunena 6.4
     */
    public static function getInstance($userid = 0, $useCache = true): ?KunenaUserSocials
    {
        static $instance = null;

        if (!$instance && $useCache) {
            $options = ['defaultgroup' => 'com_kunena'];
            $cache = Factory::getContainer()
                ->get(CacheControllerFactoryInterface::class)
                ->createCacheController('output', $options);
            $instance = $cache->get('usersocials', 'com_kunena');

            if (!$instance) {
                $instance = new KunenaUserSocials();
                
                if ($userid > 0) {
                    $instance->userid = $userid;
                }
                
                $instance->load();
            }

            $cache->store($instance, 'usersocials', 'com_kunena');
        } else {
            $instance = new KunenaUserSocials();
            
            if ($userid > 0) {
                $instance->userid = $userid;
            }
            
            $instance->load();
        }

        return $instance;
    }

    /**
     * Load the socials values from database table.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.4
     */
    public function load(): void
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery();
        $query->select('socials')
            ->from($db->quoteName('#__kunena_users'))
            ->where($db->quoteName('userid') . '=' . $this->userid);
        $db->setQuery($query);

        try {
            $socials = $db->loadAssoc();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
        }
        
        if ($socials) {
            $params = json_decode($socials['socials']);
            $this->bind($params);
        }

        // Perform custom validation of config data before we let anybody access it.
        $this->check();
    }

    /**
     * @param   mixed  $properties  properties
     *
     * @return  void
     *
     * @since   Kunena 6.4
     */
    public function bind($properties): void
    {
        $this->setProperties($properties);
    }

    /**
     * Messages per page
     *
     * @return  void
     *
     * @since   Kunena 6.4
     */
    public function check(): void
    {
        // Add anything that requires validation
    }

    /**
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.4
     */
    public function save(): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Perform custom validation of config data before we write it.
        $this->check();

        // Get current configuration
        $params = get_object_vars($this);
        unset($params['id']);
        
        $fields = array(
            $db->quoteName('socials') . ' = ' . $db->quote(json_encode($params))
        );
        
        $conditions = array(
            $db->quoteName('userid') . ' = ' . $this->userid
        );
        
        $query = $db->createQuery()
            ->update($db->quoteName('#__kunena_users'))->set($fields)->where($conditions);

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
        }

        // Clear cache.
        KunenaCacheHelper::clear();
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.4
     */
    public function reset(): void
    {
        $instance = new KunenaUserSocials();
        $this->bind(get_object_vars($instance));
    }

    /**
     * @param   string  $name  Name of the plugin
     *
     * @return  Registry
     *
     * @internal
     *
     * @since   Kunena 6.4
     */
    public function getPlugin(string $name): Registry
    {
        return isset($this->plugins[$name]) ? $this->plugins[$name] : new Registry();
    }

    /**
     * Email set for the configuration
     *
     * @return  string
     *
     * @throws  Exception
     * @since   Kunena 6.4
     */
    public function getEmail(): string
    {
        $email = $this->email;

        return !empty($email) ? $email : Factory::getApplication()->get('mailfrom', '');
    }

    /**
     * Modifies existing property of the class object
     *
     * @param   string  $property  The name of the property.
     * @param   mixed   $value     The value of the property to set.
     *
     * @return  bool  true on success
     *
     * @since   Kunena 6.4
     */
    public function set($property, $value): bool
    {
        $this->$property = $value;

        return true;
    }

    /**
     * Set the object properties based on a named array/hash.
     *
     * @param   mixed  $properties  Either an associative array or another object.
     *
     * @return  boolean
     *
     * @since   Kunena 6.4
     */
    public function setProperties($properties)
    {
        if (\is_array($properties) || \is_object($properties)) {
            foreach ((array) $properties as $k => $v) {
                // Use the set function which might be overridden.
                $this->set($k, $v);
            }

            return true;
        }

        return false;
    }
    
    /**
     * Add the JSON content in colum params for the current user if it's empty
     *
     * @since   Kunena 6.4
     */
    public static function addSocialsParams()
    {
        $user = KunenaUserHelper::getMyself(); 

        if ($user->userid > 0 && empty($user->socials)) {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery();

            $fields = array(
                $db->quoteName('socials') . ' = ' . $db->quote('{
    "x_social": {
        "value": "",
        "url": "https://x.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_X_SOCIAL",
        "nourl": 0,
        "fa": "fa-brands fa-x-twitter"
    },
    "facebook": {
        "value": "",
        "url": "https://www.facebook.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_FACEBOOK",
        "nourl": 0,
        "fa": "fa-brands fa-facebook"
    },
    "myspace": {
        "value": "",
        "url": "https://www.myspace.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_MYSPACE",
        "nourl": 0,
        "fa": "fa-solid fa-square-share-nodes"
    },
    "linkedin": {
        "value": "",
        "url": "https://www.linkedin.com/in/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_LINKEDIN",
        "nourl": 0,
        "fa": "fa-brands fa-linkedin"
    },
    "linkedin_company": {
        "value": "",
        "url": "https://www.linkedin.com/company/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_LINKEDIN",
        "nourl": 0,
        "fa": "fa-brands fa-linkedin"
    },
    "digg": {
        "value": "",
        "url": "https://www.digg.com/users/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_DIGG",
        "nourl": 0,
        "fa": "fa-brands fa-digg"
    },
    "skype": {
        "value": "",
        "url": "skype:##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_SKYPE",
        "nourl": 0,
        "fa": "fa-brands fa-skype"
    },
    "yim": {
        "value": "",
        "url": "##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_YIM",
        "nourl": 1,
        "fa": "fa-brands fa-yahoo"
    },
    "google": {
        "value": "",
        "url": "##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_GOOGLE",
        "nourl": 1,
        "fa": "fa-brands fa-google"
    },
    "github": {
        "value": "",
        "url": "https://www.github.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_GITHUB",
        "nourl": 0,
        "fa": "fa-brands fa-github"
    },
    "microsoft": {
        "value": "",
        "url": "##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_MICROSOFT",
        "nourl": 1,
        "fa": "fa-brands fa-microsoft"
    },
    "blogspot": {
        "value": "",
        "url": "https://##VALUE##.blogspot.com/",
        "title": "COM_KUNENA_MYPROFILE_BLOGSPOT",
        "nourl": 0,
        "fa": "fa-solid fa-square-share-nodes"
    },
    "flickr": {
        "value": "",
        "url": "https://www.flickr.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_FLICKR",
        "nourl": 0,
        "fa": "fa-brands fa-flickr"
    },
    "instagram": {
        "value": "",
        "url": "https://www.instagram.com/##VALUE##/",
        "title": "COM_KUNENA_MYPROFILE_INSTAGRAM",
        "nourl": 0,
        "fa": "fa-brands fa-instagram"
    },
    "qqsocial": {
        "value": "",
        "url": "##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_QQSOCIAL",
        "nourl": 1,
        "fa": "fa-solid fa-square-share-nodes"
    },
    "qzone": {
        "value": "",
        "url": "##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_QZONE",
        "nourl": 1,
        "fa": "fa-solid fa-square-share-nodes"
    },
    "weibo": {
        "value": "",
        "url": "##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_WEIBO",
        "nourl": 1,
        "fa": "fa-brands fa-weibo"
    },
    "wechat": {
        "value": "",
        "url": "##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_WECHAT",
        "nourl": 1,
        "fa": "fa-solid fa-square-share-nodes"
    },
    "vk": {
        "value": "",
        "url": "https://vk.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_VK",
        "nourl": 0,
        "fa": "fa-brands fa-vk"
    },
    "telegram": {
        "value": "",
        "url": "https://t.me/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_TELEGRAM",
        "nourl": 0,
        "fa": "fa-brands fa-telegram"
    },
    "apple": {
        "value": "",
        "url": "##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_APPLE",
        "nourl": 1,
        "fa": "fa-brands fa-apple"
    },
    "vimeo": {
        "value": "",
        "url": "https://vimeo.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_VIMEO",
        "nourl": 0,
        "fa": "fa-brands fa-vimeo"
    },
    "whatsapp": {
        "value": "",
        "url": "https://wa.me/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_WHATSAPP",
        "nourl": 0,
        "fa": "fa-brands fa-whatsapp"
    },
    "youtube": {
        "value": "",
        "url": "https://www.youtube.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_YOUTUBE",
        "nourl": 0,
        "fa": "fa-brands fa-youtube"
    },
    "ok": {
        "value": "",
        "url": "https://ok.ru/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_OK",
        "nourl": 0,
        "fa": "fa-solid fa-square-share-nodes"
    },
    "pinterest": {
        "value": "",
        "url": "https://pinterest.com/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_PINTEREST",
        "nourl": 0,
        "fa": "fa-brands fa-pinterest"
    },
    "reddit": {
        "value": "",
        "url": "https://www.reddit.com/user/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_REDDIT",
        "nourl": 0,
        "fa": "fa-brands fa-reddit"
    },
    "bluesky_app": {
        "value": "",
        "url": "https://bsky.app/##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_BLUESKY_APP",
        "nourl": 0,
        "fa": "fa-brands fa-bluesky"
    },
    "threads": {
        "value": "",
        "url": "https://www.threads.net/@##VALUE##",
        "title": "COM_KUNENA_MYPROFILE_THREADS_APP",
        "nourl": 0,
        "fa": "fa-brands fa-threads"
    }
}')
            );
            
            $conditions = array(
                $db->quoteName('userid')  . ' = ' . $user->userid
            );

            $query->update($db->quoteName('#__kunena_users'))->set($fields)->where($conditions);
            $db->setQuery($query);

            try {
                $result = $db->execute();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }
    }
}
