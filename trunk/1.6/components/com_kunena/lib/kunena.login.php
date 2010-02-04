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

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaLogin {

	function getReturnURL($type) {
		$itemid = '';
		if ($itemid) {
			$menu = & JSite::getMenu ();
			$item = $menu->getItem ( $itemid );
			$url = JRoute::_ ( $item->link . '&Itemid=' . $itemid, false );
		} else {
			// stay on the same page
			$uri = JFactory::getURI ();
			$url = $uri->toString ( array ('path', 'query', 'fragment' ) );
		}

		return base64_encode ( $url );
	}

	function getType() {
		$user = & JFactory::getUser ();
		return (! $user->get ( 'guest' )) ? 'logout' : 'login';
	}

	function getMyAvatar() {
		$this->config = & CKunenaConfig::getInstance ();
		$this->my = &JFactory::getUser ();
		//first we gather some information about this person
		$juserinfo = JUser::getInstance ( $this->my->id );
		$userinfo = CKunenaUserprofile::getInstance ( );

		$Itemid = JRequest::getInt ( 'Itemid' );
		$this->kunena_avatar = NULL;
		if ($userinfo != NULL) {
			$prefview = $userinfo->view;
			if ($this->config->username)
				$this->kunena_username = $juserinfo->username; // externally used  by pathway, myprofile_menu
			else
				$this->kunena_username = $juserinfo->name;
			$this->kunena_avatar = $userinfo->avatar;
		}

		$this->jr_avatar = '';
		if ($this->config->avatar_src == "jomsocial") {
			// Get CUser object
			$jsuser = & CFactory::getUser ( $this->my->id );
			$this->jr_avatar = '<img src="' . $jsuser->getThumbAvatar () . '" alt=" " />';
		} else if ($this->config->avatar_src == "cb") {
			$kunenaProfile = & CkunenaCBProfile::getInstance ();
			$this->jr_avatar = $kunenaProfile->showAvatar ( $this->my->id );
		} else if ($this->config->avatar_src == "aup") // integration AlphaUserPoints
		{
			$api_AUP = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
			if (file_exists ( $api_AUP )) {
				($this->config->fb_profile == 'aup') ? $showlink = 1 : $showlink = 0;
				$this->jr_avatar = AlphaUserPointsHelper::getAupAvatar ( $this->my->id, $showlink, $this->config->avatarsmallwidth, $this->config->avatarsmallheight );
			} // end integration AlphaUserPointselse
		} else {
			if ($this->kunena_avatar != "") {
				if (! file_exists ( KUNENA_PATH_UPLOADED . DS . 'avatars/s_' . $this->kunena_avatar )) {
					$this->jr_avatar = '<img src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $this->kunena_avatar . '" alt=" " style="max-width: ' . $this->config->avatarsmallwidth . 'px; max-height: ' . $this->config->avatarsmallheight . 'px;" />';
				} else {
					$this->jr_avatar = '<img src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_' . $this->kunena_avatar . '" alt=" " />';
				}
			} else {
				$this->jr_avatar = '<img src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt=" " />';
			}
		}
	return $this->jr_avatar;
	}

	function getRegisterLink() {
		$kunena_config = & CKunenaConfig::getInstance ();
		if ($kunena_config->fb_profile == 'cb') {
			return CKunenaCBProfile::getRegisterURL ();
		} else if ($kunena_config->fb_profile == 'jomsocial') {
			return CKunenaLink::GetJomsocialRegisterLink(JText::_(COM_KUNENA_PROFILEBOX_REGISTER));
		} else {
			return CKunenaLink::GetRegisterLink(JText::_(COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT));
		}
	}

	function getLostPasswordLink() {
		$kunena_config = & CKunenaConfig::getInstance ();
		if ($kunena_config->fb_profile == 'cb') {
			return CKunenaCBProfile::getLostPasswordURL ();
		} else if ($kunena_config->fb_profile == 'jomsocial') {
			return CKunenaLink::GetJomsocialLoginLink(JText::_(COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD));
		} else {
			return CKunenaLink::GetLostpassLink(JText::_(COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD));
		}
	}

	function getLostUserLink() {
		$kunena_config = & CKunenaConfig::getInstance ();
		if ($kunena_config->fb_profile == 'cb') {
			return '';
		} else if ($kunena_config->fb_profile == 'jomsocial') {
			return '';
		} else {
			return CKunenaLink::GetLostuserLink(JText::_(COM_KUNENA_PROFILEBOX_FORGOT_USERNAME));
		}
	}
}

?>