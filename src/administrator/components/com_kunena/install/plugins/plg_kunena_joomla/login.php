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

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

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

		if (version_compare(JVERSION, '4.0', '<'))
		{
			require_once JPATH_SITE . '/components/com_users/helpers/route.php';
		}
	}

	/**
	 * Method to login via Joomla! framework
	 *
	 * @param   string  $username   Username of user
	 * @param   string  $password   Password of user
	 * @param   boolean $rememberme Remember the user next time it wants login
	 * @param   string  $secretkey  The secretkey given by user when TFA is enabled
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
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
	 * @throws Exception
	 * @since Kunena
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
		return (bool) \Joomla\CMS\Plugin\PluginHelper::isEnabled('system', 'remember');
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getLoginURL()
	{
		if (version_compare(JVERSION, '4.0', '>'))
		{
			$Itemid = '';
		}
		else
		{
			$Itemid = UsersHelperRoute::getLoginRoute();
		}

		return Route::_('index.php?option=com_users&view=login' . ($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getLogoutURL()
	{
		if (version_compare(JVERSION, '4.0', '>'))
		{
			$Itemid = '';
		}
		else
		{
			$Itemid = UsersHelperRoute::getLoginRoute();
		}

		return Route::_('index.php?option=com_users&view=login' . ($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	/**
	 * @return null|string
	 * @since Kunena
	 */
	public function getRegistrationURL()
	{
		$usersConfig = \Joomla\CMS\Component\ComponentHelper::getParams('com_users');

		if ($usersConfig->get('allowUserRegistration'))
		{
			if (version_compare(JVERSION, '4.0', '>'))
			{
				$Itemid = '';
			}
			else
			{
				$Itemid = UsersHelperRoute::getRegistrationRoute();
			}

			return Route::_('index.php?option=com_users&view=registration' . ($Itemid ? "&Itemid={$Itemid}" : ''));
		}

		return;
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getResetURL()
	{
		if (version_compare(JVERSION, '4.0', '>'))
		{
			$Itemid = '';
		}
		else
		{
			$Itemid = UsersHelperRoute::getResetRoute();
		}

		return Route::_('index.php?option=com_users&view=reset' . ($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getRemindURL()
	{
		if (version_compare(JVERSION, '4.0', '>'))
		{
			$Itemid = '';
		}
		else
		{
			$Itemid = UsersHelperRoute::getRemindRoute();
		}

		return Route::_('index.php?option=com_users&view=remind' . ($Itemid ? "&Itemid={$Itemid}" : ''));
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
