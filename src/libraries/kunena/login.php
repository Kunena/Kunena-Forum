<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      Integration
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class KunenaLogin
 * @since Kunena
 */
class KunenaLogin
{
	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $instance = false;

	/**
	 * @var array|KunenaLogin[]
	 * @since Kunena
	 */
	protected $instances = array();

	/**
	 * @since Kunena
	 */
	public function __construct()
	{
		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		$classes = Factory::getApplication()->triggerEvent('onKunenaGetLogin');

		foreach ($classes as $class)
		{
			if (!is_object($class))
			{
				continue;
			}

			$this->instances[] = $class;
		}
	}

	/**
	 * @param   null $integration integration
	 *
	 * @return boolean|KunenaLogin
	 * @since Kunena
	 */
	public static function getInstance($integration = null)
	{
		if (self::$instance === false)
		{
			self::$instance = new KunenaLogin;
		}

		return self::$instance;
	}

	/**
	 * Method to check if TFA is enabled when user ins't logged
	 *
	 * @return integer
	 * @since Kunena
	 */
	public static function getTwoFactorMethods()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

		return count(UsersHelper::getTwoFactorMethods());
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function enabled()
	{
		return !empty($this->instances);
	}

	/**
	 * Method to login user by leverage Kunena plugin enabled
	 *
	 * @param   string $username   The username of user which need to be logged
	 * @param   string $password   The password of user which need to be logged
	 * @param   int    $rememberme If the user want to be remembered the next time it want to log
	 * @param   string $secretkey  The secret key for the TFA feature
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function loginUser($username, $password, $rememberme = 0, $secretkey = null)
	{
		foreach ($this->instances as $login)
		{
			if (method_exists($login, 'loginUser'))
			{
				return $login->loginUser($username, $password, $rememberme, $secretkey);
			}
		}

		return false;
	}

	/**
	 * @param   null $return logout user
	 *
	 * @return boolean
	 * @since Kunena
	 */
	public function logoutUser($return = null)
	{
		foreach ($this->instances as $login)
		{
			if (method_exists($login, 'logoutUser'))
			{
				return $login->logoutUser($return);
			}
		}

		return false;
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function getRememberMe()
	{
		foreach ($this->instances as $login)
		{
			if (method_exists($login, 'getRememberMe'))
			{
				return $login->getRememberMe();
			}
		}

		return false;
	}

	/**
	 * @return null
	 * @since Kunena
	 */
	public function getLoginURL()
	{
		foreach ($this->instances as $login)
		{
			if (method_exists($login, 'getLoginURL'))
			{
				return $login->getLoginURL();
			}
		}

		return false;
	}

	/**
	 * @return null
	 * @since Kunena
	 */
	public function getLogoutURL()
	{
		foreach ($this->instances as $login)
		{
			if (method_exists($login, 'getLogoutURL'))
			{
				return $login->getLogoutURL();
			}
		}

		return false;
	}

	/**
	 * @return null
	 * @since Kunena
	 */
	public function getRegistrationURL()
	{
		foreach ($this->instances as $login)
		{
			if (method_exists($login, 'getRegistrationURL'))
			{
				return $login->getRegistrationURL();
			}
		}

		return false;
	}

	/**
	 * @return null
	 * @since Kunena
	 */
	public function getResetURL()
	{
		foreach ($this->instances as $login)
		{
			if (method_exists($login, 'getResetURL'))
			{
				return $login->getResetURL();
			}
		}

		return false;
	}

	/**
	 * @return null
	 * @since Kunena
	 */
	public function getRemindURL()
	{
		foreach ($this->instances as $login)
		{
			if (method_exists($login, 'getRemindURL'))
			{
				return $login->getRemindURL();
			}
		}

		return false;
	}

	/**
	 * Checks if the Two Factor Authentication method is globally enabled and if the
	 * user has enabled a specific TFA method on their account. Only if both conditions
	 * are met will this method return true;
	 *
	 * @param   integer $userId The user ID to check. Skip to use the current user.
	 *
	 * @return boolean True if TFA is enabled for this user
	 * @since Kunena
	 */
	public function isTFAEnabled($userId = null)
	{
		// Include the necessary user model and helper
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

		if (version_compare(JVERSION, '4.0.0-dev', '>='))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_users/Model/UserModel.php';
		}
		else
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';
		}

		// Is TFA globally turned off?
		$twoFactorMethods = UsersHelper::getTwoFactorMethods();

		if (count($twoFactorMethods) <= 1)
		{
			return false;
		}

		// Do we need to get the User ID?
		if (empty($userId))
		{
			$userId = Factory::getUser()->id;
		}

		// Has this user turned on TFA on their account?
		$model     = new UsersModelUser;
		$otpConfig = $model->getOtpConfig($userId);

		return !(empty($otpConfig->method) || ($otpConfig->method == 'none'));
	}

	/**
	 * Return the parameters of the plugin
	 *
	 * @return JRegistry
	 * @since Kunena 5.1
	 */
	public function getParams()
	{
	    foreach ($this->instances as $login)
	    {
	        if (method_exists($login, 'getParams'))
	        {
	            return $login->getParams();
	        }
	    }

	    return false;
	}
}
