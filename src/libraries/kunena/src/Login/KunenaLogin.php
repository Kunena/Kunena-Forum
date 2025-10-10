<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Integration
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Login;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Users\Administrator\Helper\UsersHelper;
use Joomla\Component\Users\Administrator\Model\UserModel;
use Kunena\Forum\Libraries\Event\KunenaGetLoginEvent;

/**
 * Class KunenaLogin
 *
 * @since   Kunena 6.0
 */
class KunenaLogin
{
    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected static $instance = false;

    /**
     * @var     array| KunenaLogin[]
     * @since   Kunena 6.0
     */
    protected $instances = [];

    /**
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function __construct()
    {
        PluginHelper::importPlugin('kunena');

        $loginEvent = new KunenaGetLoginEvent('onKunenaGetLogin');
        Factory::getApplication()->getDispatcher()->dispatch('onKunenaGetLogin', $loginEvent);

        $classes = $loginEvent->getArgument('result', []);

        foreach ($classes as $class) {
            if ($class instanceof KunenaLoginAbstract) {
                $this->instances[] = $class;
            }
        }
    }

    /**
     * @param   null  $integration  integration
     *
     * @return  boolean|KunenaLogin
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function getInstance($integration = null)
    {
        if (self::$instance === false) {
            self::$instance = new KunenaLogin();
        }

        return self::$instance;
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function enabled(): bool
    {
        return !empty($this->instances);
    }

    /**
     * Method to login user by leverage Kunena plugin enabled
     *
     * @param   string  $username    The username of user which need to be logged
     * @param   string  $password    The password of user which need to be logged
     * @param   boolean $rememberme  If the user want to be remembered the next time it want to log
     * @param   null    $secretkey   The secret key for the TFA feature
     *
     * @return  boolean|string
     *
     * @since   Kunena 6.0
     */
    public function loginUser(string $username, string $password, bool $rememberme = false, $secretkey = null)
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'loginUser')) {
                return $login->loginUser($username, $password, $rememberme, $secretkey);
            }
        }

        return false;
    }

    /**
     * @param   null  $return  logout user
     *
     * @return  null|string
     *
     * @since   Kunena 6.0
     */
    public function logoutUser($return = null)
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'logoutUser')) {
                return $login->logoutUser($return);
            }
        }

        return false;
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function getRememberMe(): bool
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'getRememberMe')) {
                return $login->getRememberMe();
            }
        }

        return false;
    }

    /**
     * @return  null
     *
     * @since   Kunena 6.0
     */
    public function getLoginURL()
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'getLoginURL')) {
                return $login->getLoginURL();
            }
        }

        return false;
    }

    /**
     * @return  null
     *
     * @since   Kunena 6.0
     */
    public function getLogoutURL()
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'getLogoutURL')) {
                return $login->getLogoutURL();
            }
        }

        return false;
    }

    /**
     * @return  null
     *
     * @since   Kunena 6.0
     */
    public function getRegistrationURL()
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'getRegistrationURL')) {
                return $login->getRegistrationURL();
            }
        }

        return false;
    }

    /**
     * @return  null
     *
     * @since   Kunena 6.0
     */
    public function getResetURL()
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'getResetURL')) {
                return $login->getResetURL();
            }
        }

        return false;
    }

    /**
     * @return  null
     *
     * @since   Kunena 6.0
     */
    public function getRemindURL()
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'getRemindURL')) {
                return $login->getRemindURL();
            }
        }

        return false;
    }


    /**
     * Return the parameters of the plugin
     *
     * @return  boolean|false
     *
     * @since   Kunena 5.1
     */
    public function getParams()
    {
        foreach ($this->instances as $login) {
            if (method_exists($login, 'getParams')) {
                return $login->getParams();
            }
        }

        return false;
    }
}
