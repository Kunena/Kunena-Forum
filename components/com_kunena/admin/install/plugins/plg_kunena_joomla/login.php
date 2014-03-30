<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Joomla25
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLoginJoomla {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
		require_once JPATH_SITE.'/components/com_users/helpers/route.php';
	}

	public function loginUser($username, $password, $rememberme) {
		$credentials = array('username' => $username, 'password' => $password);
		$options = array('remember' => $rememberme);
		$error = JFactory::getApplication()->login ( $credentials, $options );
		return is_bool($error) ? '' : $error;
	}

	public function logoutUser() {
		$error = JFactory::getApplication()->logout ();
		return is_bool($error) ? '' : $error;
	}

	public function getRememberMe() {
		return (bool) JPluginHelper::isEnabled('system', 'remember');
	}

	public function getLoginURL() {
		$Itemid = UsersHelperRoute::getLoginRoute();
		return JRoute::_('index.php?option=com_users&view=login'.($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	public function getLogoutURL() {
		$Itemid = UsersHelperRoute::getLoginRoute();
		return JRoute::_('index.php?option=com_users&view=login'.($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	public function getRegistrationURL() {
		$usersConfig = JComponentHelper::getParams ( 'com_users' );
		if ($usersConfig->get ( 'allowUserRegistration' )) {
			$Itemid = UsersHelperRoute::getRegistrationRoute();
			return JRoute::_('index.php?option=com_users&view=registration'.($Itemid ? "&Itemid={$Itemid}" : ''));
		}
		return null;
	}

	public function getResetURL() {
		$Itemid = UsersHelperRoute::getResetRoute();
		return JRoute::_('index.php?option=com_users&view=reset'.($Itemid ? "&Itemid={$Itemid}" : ''));
	}

	public function getRemindURL() {
		$Itemid = UsersHelperRoute::getRemindRoute();
		return JRoute::_('index.php?option=com_users&view=remind'.($Itemid ? "&Itemid={$Itemid}" : ''));
	}
}
