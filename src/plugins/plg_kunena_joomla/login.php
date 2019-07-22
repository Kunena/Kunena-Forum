<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Joomla
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Class KunenaLoginJoomla
 * @since Kunena
 */
class KunenaLoginJoomla
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * Method to login via Joomla! framework
	 *
	 * @param   string   $username    Username of user
	 * @param   string   $password    Password of user
	 * @param   boolean  $rememberme  Remember the user next time it wants login
	 * @param   string   $secretkey   The secretkey given by user when TFA is enabled
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
	 */
	public function loginUser($username, $password, $rememberme, $secretkey = null)
	{
		$credentials = array('username' => $username, 'password' => $password);

		if ($secretkey)
		{
			$credentials['secretkey'] = $secretkey;
		}

		$options = array('remember' => $rememberme);
		$error   = Factory::getApplication()->login($credentials, $options);

		return is_bool($error) ? '' : $error;
	}

	/**
	 * @return boolean|string
	 * @since Kunena
	 * @throws Exception
	 */
	public function logoutUser()
	{
		$error = Factory::getApplication()->logout();

		return is_bool($error) ? '' : $error;
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function getRememberMe()
	{
		return (bool) PluginHelper::isEnabled('system', 'remember');
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getLoginURL()
	{
		return Route::_('index.php?option=com_users&view=login');
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getLogoutURL()
	{
		return Route::_('index.php?option=com_users&view=login');
	}

	/**
	 * @return void|string
	 * @since Kunena
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
	 * @return string
	 * @since Kunena
	 */
	public function getResetURL()
	{
		return Route::_('index.php?option=com_users&view=reset');
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getRemindURL()
	{
		return Route::_('index.php?option=com_users&view=remind');
	}

	/**
	 * Return the parameters of the plugin
	 *
	 * @return JRegistry
	 * @since Kunena 5.1
	 */
	public function getParams()
	{
		return $this->params;
	}
}
