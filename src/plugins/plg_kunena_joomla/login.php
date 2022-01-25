<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Joomla
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

/**
 * Class KunenaLoginJoomla
 *
 * @since   Kunena 6.0
 */
class KunenaLoginJoomla
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * Method to login via Joomla! framework
	 *
	 * @param   string   $username    Username of user
	 * @param   string   $password    Password of user
	 * @param   boolean  $rememberme  Remember the user next time it wants login
	 * @param   null     $secretkey   The secretkey given by user when TFA is enabled
	 *
	 * @return  boolean|\Exception  True on success, false if failed or silent handling is configured, or a \Exception object on authentication error.
	 *
	 * @since   Kunena
	 *
	 * @throws Exception
	 */
	public function loginUser(string $username, string $password, bool $rememberme, $secretkey = null)
	{
		$credentials = ['username' => $username, 'password' => $password];

		if ($secretkey)
		{
			$credentials['secretkey'] = $secretkey;
		}

		$options = ['remember' => $rememberme, 'silent'=> false];

		try
		{
			$logged   = Factory::getApplication()->login($credentials, $options);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		return $logged;
	}

	/**
	 * @return  boolean|string
	 *
	 * @since   Kunena
	 *
	 * @throws  Exception
	 */
	public function logoutUser()
	{
		$error = Factory::getApplication()->logout();

		return is_bool($error) ? '' : $error;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function getRememberMe(): bool
	{
		return (bool) PluginHelper::isEnabled('system', 'remember');
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getLoginURL(): string
	{
		return Route::_('index.php?option=com_users&view=login');
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getLogoutURL(): string
	{
		return Route::_('index.php?option=com_users&view=login');
	}

	/**
	 * @return  void|string
	 * @since   Kunena 6.0
	 */
	public function getRegistrationURL()
	{
		$usersConfig = ComponentHelper::getParams('com_users');

		if ($usersConfig->get('allowUserRegistration'))
		{
			return Route::_('index.php?option=com_users&view=registration');
		}

		return;
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getResetURL(): string
	{
		return Route::_('index.php?option=com_users&view=reset');
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getRemindURL(): string
	{
		return Route::_('index.php?option=com_users&view=remind');
	}

	/**
	 * Return the parameters of the plugin
	 *
	 * @return object|null
	 *
	 * @since   Kunena 5.1
	 */
	public function getParams(): ?object
	{
		return $this->params;
	}
}
