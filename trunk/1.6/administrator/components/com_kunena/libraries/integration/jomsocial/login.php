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

class KunenaLoginJomSocial extends KunenaLogin {
	protected $joomlalogin = null;
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('jomsocial');
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
		return 'index.php?option=com_community&view=frontpage';
	}

	public function getLogoutURL() {
		return 'index.php?option=com_community&view=frontpage';
	}

	public function getRegistrationURL() {
		$usersConfig = JComponentHelper::getParams ( 'com_users' );
		if ($usersConfig->get ( 'allowUserRegistration' ))
			return 'index.php?option=com_community&view=register';
	}

	public function getResetURL() {
		return $this->joomlalogin->getResetURL();
	}

	public function getRemindURL() {
		return $this->joomlalogin->getRemindURL();
	}
}
