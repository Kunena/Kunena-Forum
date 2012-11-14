<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Comprofiler
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLoginComprofiler {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function loginUser($username, $password, $rememberme) {
		cbimport ( 'cb.authentication' );
		global $ueConfig;

		$cbAuthenticate = new CBAuthentication ();

		$messagesToUser = array ();
		$alertmessages = array ();
		$redirect_url = KunenaRoute::current();

		$loginType = ( isset( $ueConfig['login_type'] ) ? $ueConfig['login_type'] : 0 );
		$resultError = $cbAuthenticate->login ( $username, $password, $rememberme, 1, $redirect_url, $messagesToUser, $alertmessages, $loginType );

		return $resultError ? $resultError : null;
	}

	public function logoutUser() {
		cbimport ( 'cb.authentication' );

		$cbAuthenticate = new CBAuthentication ();

		$redirect_url = KunenaRoute::current();
		$resultError = $cbAuthenticate->logout ( $redirect_url );

		return $resultError ? $resultError : null;
	}

	public function getRememberMe() {
		$db = JFactory::getDbo();
		if (version_compare(JVERSION, '1.7','>')) {
			$db->setQuery( "SELECT params from #__modules WHERE module = 'mod_cblogin' ORDER BY ordering", 0, 1 );
			$raw_params = $db->loadResult();
		} else {
			$db->setQuery( "SELECT params from #__extensions WHERE element='mod_cblogin' AND type='module'", 0, 1 );
			$raw_params = $db->loadResult();
		}
		$params = new cbParamsBase( $raw_params );
		return $params->get( 'remember_enabled', 1);
	}

	public function getLoginURL() {
		return cbSef ( 'index.php?option=com_comprofiler&task=login' );
	}

	public function getLogoutURL() {
		return cbSef ( 'index.php?option=com_comprofiler&task=logout' );
	}

	public function getRegistrationURL() {
		$usersConfig = JComponentHelper::getParams ( 'com_users' );
		if ($usersConfig->get ( 'allowUserRegistration' ))
			return cbSef ( 'index.php?option=com_comprofiler&task=registers' );
	}

	public function getResetURL() {
		return cbSef ( 'index.php?option=com_comprofiler&task=lostPassword' );
	}

	public function getRemindURL() {
		return cbSef( 'index.php?option=com_comprofiler&task=lostPassword' );
	}

}
