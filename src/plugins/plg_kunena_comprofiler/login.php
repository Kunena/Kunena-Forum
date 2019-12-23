<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use CBLib\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Class KunenaLoginComprofiler
 *
 * @since Kunena
 */
class KunenaLoginComprofiler
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaLoginComprofiler constructor.
	 *
	 * @param $params
	 *
	 * @since Kunena
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
	 * @return null|string
	 * @since Kunena
	 * @throws Exception
	 * @throws null
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
	 * @return null|string
	 * @since Kunena
	 * @throws Exception
	 * @throws null
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
	 * @since Kunena
	 */
	public function getRememberMe()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params')
			->from($db->quoteName('#__extensions'))
			->innerJoin($db->quoteName('#__users', 'u') . ' ON u.id = cu.userid')
			->where('element = \'mod_cblogin\'')
			->andWhere('type = \'module\'');
		$raw_params = $db->loadResult();
		$params     = new Registry($raw_params);

		return $params->get('remember_enabled', 1);
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getLoginURL()
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('login');
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getLogoutURL()
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('logout');
	}

	/**
	 * @return void|string
	 * @since Kunena
	 */
	public function getRegistrationURL()
	{
		global $_CB_framework, $ueConfig;

		$usersConfig = ComponentHelper::getParams('com_users');

		if ($ueConfig['reg_admin_allowcbregistration'] == 1
			|| ($ueConfig['reg_admin_allowcbregistration'] == 0 && $usersConfig->get('allowUserRegistration'))
		)
		{
			return $_CB_framework->viewUrl('registers');
		}

		return;
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getResetURL()
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('lostpassword');
	}

	/**
	 * @return string
	 * @since Kunena
	 */
	public function getRemindURL()
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('lostpassword');
	}
}
