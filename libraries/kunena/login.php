<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaLogin
 */
class KunenaLogin {
	protected static $instance = false;
	/**
	 * @var array|KunenaLogin[]
	 */
	protected $instances = array();

	public function __construct() {
		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$classes = $dispatcher->trigger('onKunenaGetLogin');
		foreach ($classes as $class) {
			if (!is_object($class)) continue;
			$this->instances[] = $class;
		}
	}

	public function enabled() {
		// TODO: do better
		return !empty($this->instances);
	}

	public static function getInstance($integration = null) {
		if (self::$instance === false) {
			self::$instance = new KunenaLogin();
		}
		return self::$instance;
	}

	public function loginUser($username, $password, $rememberme=0, $secretkey = null, $return = null) {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'loginUser')) {
				return $login->loginUser($username, $password, $rememberme, $secretkey = null, $return);
			}
		}
		return false;
	}

	public function logoutUser($return = null) {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'logoutUser')) {
				return $login->logoutUser($return);
			}
		}
		return false;
	}

	public function getRememberMe() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getRememberMe')) {
				return $login->getRememberMe();
			}
		}
		return false;
	}

	public function getLoginURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getLoginURL')) {
				return $login->getLoginURL();
			}
		}
		return null;
	}

	public function getLogoutURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getLogoutURL')) {
				return $login->getLogoutURL();
			}
		}
		return null;
	}

	public function getRegistrationURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getRegistrationURL')) {
				return $login->getRegistrationURL();
			}
		}
		return null;
	}

	public function getResetURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getResetURL')) {
				return $login->getResetURL();
			}
		}
		return null;
	}

	public function getRemindURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getRemindURL')) {
				return $login->getRemindURL();
			}
		}
		return null;
	}

	/**
	 * Checks if the Two Factor Authentication method is globally enabled and if the
	 * user has enabled a specific TFA method on their account. Only if both conditions
	 * are met will this method return true;
	 *
	 * @param   integer  $userId  The user ID to check. Skip to use the current user.
	 *
	 * @return boolean True if TFA is enabled for this user
	 */
	public function isTFAEnabled($userId = null)
	{
		if ( !version_compare(JVERSION, '3.2', '>') )
		{
			return false;
		}

		// Include the necessary user model and helper
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';

		// Is TFA globally turned off?
		$twoFactorMethods = UsersHelper::getTwoFactorMethods();

		if (count($twoFactorMethods) <= 1)
		{
			return false;
		}

		// Do we need to get the User ID?
		if (empty($userId))
		{
			$userId = JFactory::getUser()->id;
		}

		// Has this user turned on TFA on their account?
		$model = new UsersModelUser;
		$otpConfig = $model->getOtpConfig($userId);

		return !(empty($otpConfig->method) || ($otpConfig->method == 'none'));
	}

	/**
	 * Checks if the provided secret code is a valid two factor authentication
	 * code for the user whose $userId is provided. If TFA is disabled globally
	 * or for the specific user you will receive true.
	 *
	 * @param   string   $code    The secret code to check
	 * @param   integer  $userId  The user ID to check. Skip to use the current user.
	 *
	 * @return boolean True if you should accept the code
	 */
	public function isValidTFA($code, $userId = null)
	{
		// Include the necessary user model
		require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';

		// Do we need to get the User ID?
		if (empty($userId))
		{
			$userId = JFactory::getUser()->id;
		}

		// Check the secret code
		$model = new UsersModelUser;
		$options = array(
				'warn_if_not_req'	=> false,
		);

		return $model->isValidSecretKey($userId, $code, $options);
	}
}
