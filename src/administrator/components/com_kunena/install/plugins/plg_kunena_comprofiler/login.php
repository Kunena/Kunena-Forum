<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Comprofiler
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

class KunenaLoginComprofiler
{
	protected $params = null;

	/**
	 * KunenaLoginComprofiler constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param $username
	 * @param $password
	 * @param $rememberme
	 *
	 * @return null
	 */
	public function loginUser($username, $password, $rememberme)
	{
		cbimport('cb.authentication');
		global $ueConfig;

		$cbAuthenticate = new CBAuthentication;

		$messagesToUser = array();
		$alertmessages  = array();
		$redirect_url   = KunenaRoute::current();

		$loginType   = (isset($ueConfig['login_type']) ? $ueConfig['login_type'] : 0);
		$resultError = $cbAuthenticate->login($username, $password, $rememberme, 1, $redirect_url, $messagesToUser, $alertmessages, $loginType);

		return $resultError ? $resultError : null;
	}

	/**
	 * @return null
	 */
	public function logoutUser()
	{
		cbimport('cb.authentication');

		$cbAuthenticate = new CBAuthentication;

		$redirect_url = KunenaRoute::current();
		$resultError  = $cbAuthenticate->logout($redirect_url);

		return $resultError ? $resultError : null;
	}

	/**
	 * @return mixed
	 */
	public function getRememberMe()
	{
		$db = JFactory::getDbo();
		// TODO: test if works (see #1079)
		$db->setQuery("SELECT params FROM #__extensions WHERE element='mod_cblogin' AND type='module'", 0, 1);
		$raw_params = $db->loadResult();
		$params     = new cbParamsBase($raw_params);

		return $params->get('remember_enabled', 1);
	}

	/**
	 * @return string
	 */
	public function getLoginURL()
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('login');
	}

	/**
	 * @return string
	 */
	public function getLogoutURL()
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('logout');
	}

	/**
	 * @return null|string
	 */
	public function getRegistrationURL()
	{
		global $_CB_framework, $ueConfig;

		$usersConfig = JComponentHelper::getParams('com_comprofiler');

		if ($ueConfig['reg_admin_allowcbregistration'] == 1
			|| ($ueConfig['reg_admin_allowcbregistration'] == 0 && $usersConfig->get('allowUserRegistration')))
		{
			return $_CB_framework->viewUrl('registers');
		}

		return null;
	}

	/**
	 * @return string
	 */
	public function getResetURL()
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('lostpassword');
	}

	/**
	 * @return string
	 */
	public function getRemindURL()
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('lostpassword');
	}
}
