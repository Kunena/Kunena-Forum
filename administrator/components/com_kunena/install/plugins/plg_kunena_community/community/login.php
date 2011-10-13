<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration.JomSocial
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLoginCommunity {
	public function getLoginURL() {
		return CRoute::_('index.php?option=com_community&view=frontpage');
	}

	public function getLogoutURL() {
		return CRoute::_('index.php?option=com_community&view=frontpage');
	}

	public function getRegistrationURL() {
		$usersConfig = JComponentHelper::getParams ( 'com_users' );
		if ($usersConfig->get ( 'allowUserRegistration' ))
			return CRoute::_('index.php?option=com_community&view=register');
	}
}
