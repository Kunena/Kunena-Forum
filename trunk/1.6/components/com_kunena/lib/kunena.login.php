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

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaLogin {

	function getReturnURL($type) {
		// stay on the same page
		$uri = JFactory::getURI ();
		$url = $uri->toString ( array ('path', 'query', 'fragment' ) );
		return base64_encode ( $url );
	}

	function getType() {
		$user = & JFactory::getUser ();
		return (! $user->get ( 'guest' )) ? 'logout' : 'login';
	}

	function getMyAvatar() {

		$this->my = JFactory::getUser();
		$profile = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig ();
		//first we gather some information about this person
		$juserinfo = JUser::getInstance ( $this->my->id );

		$Itemid = JRequest::getInt ( 'Itemid' );
		if ($profile != NULL) {
			if ($this->config->username)
				$this->kunena_username = $juserinfo->username; // externally used  by pathway, myprofile_menu
			else
				$this->kunena_username = $juserinfo->name;
		}

		return $profile->getAvatarLink('kavatar', 'welcome');
	}

	function getloginFields() {
		$login = KunenaFactory::getLogin();
		if (!$login) return;
		return $login->getLoginFormFields();
	}

	function getlogoutFields() {
		$login = KunenaFactory::getLogin();
		if (!$login) return;
		return $login->getLogoutFormFields();
	}

	function getRegisterLink() {
		$login = KunenaFactory::getLogin();
		if (!$login) return '';
		$url = $login->getRegistrationURL();
		if (!$url) return '';
		return CKunenaLink::GetHrefLink($url, JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'));
	}

	function getLostPasswordLink() {
		$login = KunenaFactory::getLogin();
		if (!$login) return '';
		$url = $login->getResetURL();
		if (!$url) return '';
		return CKunenaLink::GetHrefLink($url, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'));
	}

	function getLostUserLink() {
		$login = KunenaFactory::getLogin();
		if (!$login) return '';
		$url = $login->getRemindURL();
		if (!$url) return '';
		return CKunenaLink::GetHrefLink($url, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'));
	}
}

?>