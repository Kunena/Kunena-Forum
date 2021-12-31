<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use CBLib\Registry\Registry;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Class KunenaLoginComprofiler
 *
 * @since   Kunena 6.0
 */
class KunenaLoginComprofiler
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaLoginComprofiler constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * @param   string  $username    username
	 * @param   string  $password    password
	 * @param   string  $rememberme  remember me
	 *
	 * @return  null|string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws null
	 * @throws Exception
	 */
	public function loginUser(string $username, string $password, string $rememberme): ?string
	{
		cbimport('cb.authentication');
		global $ueConfig;

		$cbAuthenticate = new CBAuthentication;

		$messagesToUser = [];
		$alertmessages  = [];
		$redirect_url   = KunenaRoute::current();

		$loginType   = (isset($ueConfig['login_type']) ? $ueConfig['login_type'] : 0);
		$resultError = $cbAuthenticate->login($username, $password, $rememberme, 1, $redirect_url, $messagesToUser, $alertmessages, $loginType);

		return $resultError ? $resultError : null;
	}

	/**
	 * @return  null|string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function logoutUser(): ?string
	{
		cbimport('cb.authentication');

		$cbAuthenticate = new CBAuthentication;

		$redirect_url = KunenaRoute::current();
		$resultError  = $cbAuthenticate->logout($redirect_url);

		return $resultError ? $resultError : null;
	}

	/**
	 * @return  array|boolean|float|integer|object|string|null
	 *
	 * @since   Kunena 6.0
	 */
	public function getRememberMe()
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
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
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getLoginURL(): string
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('login');
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getLogoutURL(): string
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('logout');
	}

	/**
	 * @return  void|string
	 *
	 * @since   Kunena 6.0
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
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getResetURL(): string
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('lostpassword');
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getRemindURL(): string
	{
		global $_CB_framework;

		return $_CB_framework->viewUrl('lostpassword');
	}
}
