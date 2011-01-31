<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined ( '_JEXEC' ) or die ( '' );

class KunenaLoginCommunityBuilder extends KunenaLogin {
	protected $joomlalogin = null;
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('communitybuilder');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->joomlalogin = KunenaLogin::getInstance('joomla');
		$this->priority = 50;
	}

	public function loginUser($username, $password, $rememberme, $return) {
		cbimport ( 'cb.authentication' );
		global $ueConfig;

		$cbAuthenticate = new CBAuthentication ();

		$messagesToUser = array ();
		$alertmessages = array ();
		$redirect_url = KunenaRoute::current();

		$loginType = ( isset( $ueConfig['login_type'] ) ? $ueConfig['login_type'] : 0 );
		$resultError = $cbAuthenticate->login ( $username, $password, $rememberme, 1, $redirect_url, $messagesToUser, $alertmessages, $loginType );

		if ($resultError) {
			return $resultError;
		} else {
			return null;
		}
	}

	public function logoutUser($return) {
		cbimport ( 'cb.authentication' );

		$cbAuthenticate = new CBAuthentication ();

		$redirect_url = KunenaRoute::current();
		$resultError = $cbAuthenticate->logout ( $redirect_url );

		if ($resultError) {
			return $resultError;
		} else {
			return null;
		}
	}

	public function getLoginFormFields() {
		return array (
			'form'=>'login',
			'field_username'=>'username',
			'field_password'=>'passwd',
			'field_remember'=>'remember',
			'field_return'=>'return',
			'option'=>'com_kunena',
			'view'=>'profile',
			'task'=>'login'
		);
	}

	public function getLogoutFormFields() {
		return array (
			'form'=>'login',
			'field_return'=>'return',
			'option'=>'com_kunena',
			'view'=>'profile',
			'task'=>'logout'
		);
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
