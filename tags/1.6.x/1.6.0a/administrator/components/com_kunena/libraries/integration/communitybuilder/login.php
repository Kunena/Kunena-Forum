<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
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

	public function getLoginFormFields() {
		return $this->joomlalogin->getLoginFormFields();
	}

	public function getLogoutFormFields() {
		return $this->joomlalogin->getLogoutFormFields();
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
