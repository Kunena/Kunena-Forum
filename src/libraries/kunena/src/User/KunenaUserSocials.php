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
 * @property string  $bebo
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
     * @var    string  Bebo
     * @since  Kunena 6.4.0
     */
    public $bebo = '';

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

    /**
     * @return  KunenaUserSocials|mixed
     *
     * @throws  Exception
     * @since   Kunena 6.4
     */
    public static function getInstance(): ?KunenaUserSocials
    {
        static $instance = null;

        if (!$instance) {
            $options = ['defaultgroup' => 'com_kunena'];
            $cache = Factory::getContainer()
                ->get(CacheControllerFactoryInterface::class)
                ->createCacheController('output', $options);
            $instance = $cache->get('configuration', 'com_kunena');

            if (!$instance) {
                $instance = new KunenaUserSocials();
                $instance->load();
            }

            $cache->store($instance, 'configuration', 'com_kunena');
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
            ->from($db->quoteName('#__kunena_users'));
        $db->setQuery($query);

        try {
            $config = $db->loadAssoc();
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
        }

        if ($config) {
            $params = json_decode($config['params']);
            $this->bind($params);
        }

        // Perform custom validation of config data before we let anybody access it.
        $this->check();

        $plugins = [];
        Factory::getApplication()->triggerEvent('onKunenaGetConfiguration', ['kunena.configuration', &$plugins]);
        $this->plugins = [];
    }

    /**
     * @param   mixed  $properties  properties
     *
     * @return  void
     *
     * @since   Kunena 6.0
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
     * @since   Kunena 6.0
     */
    public function check(): void
    {
        // Add anything that requires validation

        // Need to have at least one per page of these
        $this->messagesPerPage       = max($this->messagesPerPage, 1);
        $this->messagesPerPageSearch = max($this->messagesPerPageSearch, 1);
        $this->threadsPerPage        = max($this->threadsPerPage, 1);
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

        $db->setQuery("REPLACE INTO #__kunena_users SET socials={$db->quote(json_encode($params))}");

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
     * @since   Kunena 6.0
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
     * @since   Kunena 6.0
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
     * @since   Kunena 6.0
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
}
