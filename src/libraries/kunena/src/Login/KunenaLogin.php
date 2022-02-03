<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Integration
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
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

		$classes = Factory::getApplication()->triggerEvent('onKunenaGetLogin');

		foreach ($classes as $class)
		{
			if (!\is_object($class))
			{
				continue;
			}

			$this->instances[] = $class;
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
		if (self::$instance === false)
		{
			self::$instance = new KunenaLogin;
		}

		return self::$instance;
	}

	/**
	 * Method to check if TFA is enabled when user isn't logged
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getTwoFactorMethods(): int
	{
		return \count(AuthenticationHelper::getTwoFactorMethods());
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
	public function loginUser(string $username, string $password, bool $rememberme = false, $secretkey = null): ?bool
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
	 * @param   null  $return  logout user
	 *
	 * @return  null|string
	 *
	 * @since   Kunena 6.0
	 */
	public function logoutUser($return = null): ?string
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
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function getRememberMe(): bool
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
	 * @return  null
	 *
	 * @since   Kunena 6.0
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
	 * @return  null
	 *
	 * @since   Kunena 6.0
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
	 * @return  null
	 *
	 * @since   Kunena 6.0
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
	 * @return  null
	 *
	 * @since   Kunena 6.0
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
	 * @return  null
	 *
	 * @since   Kunena 6.0
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
	 * @param   integer  $userId  The user ID to check. Skip to use the current user.
	 *
	 * @return  boolean True if TFA is enabled for this user
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function isTFAEnabled($userId = null): bool
	{
		// Is TFA globally turned off?
		$twoFactorMethods = UsersHelper::getTwoFactorMethods();

		if (\count($twoFactorMethods) <= 1)
		{
			return false;
		}

		// Do we need to get the User ID?
		if (empty($userId))
		{
			$userId = Factory::getApplication()->getIdentity()->id;
		}

		// Has this user turned on TFA on their account?
		$model     = new UserModel;
		$otpConfig = $model->getOtpConfig($userId);

		return !(empty($otpConfig->method) || ($otpConfig->method == 'none'));
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
