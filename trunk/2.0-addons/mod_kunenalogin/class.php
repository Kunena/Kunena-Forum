<?php
/**
 * @version $Id: class.php 4047 2010-12-21 07:59:21Z severdia $
 * Kunenalogin Module
 * @package Kunena login
 *
 * @Copyright (C) 2010-2011 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

class ModKunenaLogin {
	function __construct($params) {
		// Include the syndicate functions only once
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		kimport('kunena.date');
		$this->params = $params;

		// load Kunena main language file so we can leverage language strings from it
		KunenaFactory::loadLanguage();
	}

	function getReturnURL() {
		$itemid = $this->params->get ( $this->type );
		if ($itemid) {
			$item = JSite::getMenu ()->getItem ( $itemid );
			$url = JRoute::_ ( $item->link . '&Itemid=' . $itemid, false );
		} else {
			// stay on the same page
			$uri = JFactory::getURI ();
			$url = $uri->toString ( array ('path', 'query', 'fragment' ) );
		}

		return base64_encode ( $url );
	}

	function kunenaAvatar($userid) {
		$kunena_user = KunenaFactory::getUser ( ( int ) $userid );
		$username = $kunena_user->getName(); // Takes care of realname vs username setting
		$avatarlink = $kunena_user->getAvatarImage ( '', $this->params->get ( 'avatar_w' ), $this->params->get ( 'avatar_h' ) );
		return CKunenaLink::GetProfileLink ( $userid, $avatarlink, $username );
	}

	function display() {
		$this->my = JFactory::getUser ();
		$this->me = KunenaFactory::getUser ();
		$token = JUtility::getToken();

		// Add CSS stylesheet
		JFactory::getDocument ()->addStyleSheet ( JURI::root(true) . "/modules/mod_kunenalogin/tmpl/css/kunenalogin.css", "text/css" );

		$cache = JFactory::getCache('com_kunena', 'output');
		if ($cache->start("{$this->me->userid}.$token", 'mod_kunenalogin')) return;

		$login = KunenaFactory::getLogin();
		if (!$this->me->exists()) {
			$this->type = 'login';
			$this->login = null;
			if ($login) {
				$this->login = $login->getLoginFormFields();
				$this->register = $login->getRegistrationURL();
				$this->lostpassword = $login->getResetURL();
				$this->lostusername = $login->getRemindURL();
			}
		} else {
			$this->type = 'logout';
			$this->logout = null;
			$this->lastvisitDate = new KunenaDate($this->my->lastvisitDate);
			if ($login) {
				$this->logout = $login->getLogoutFormFields();
			}

			// Private messages
			$private = KunenaFactory::getPrivateMessaging();
			$this->privateMessages = '';
			if ($this->params->get('showmessage') && $private) {
				$count = $private->getUnreadCount($this->me->userid);
				$this->privateMessages = $private->getInboxLink($count ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $count) : JText::_('COM_KUNENA_PMS_INBOX'));
			}
		}
		$this->return = $this->getReturnURL ();

		require (JModuleHelper::getLayoutPath ( 'mod_kunenalogin' ));
		$cache->end();
	}
}