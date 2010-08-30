<?php
/**
 * @version $Id$
 * Kunenalogin Module
 * @package Kunena login
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

class ModKunenaLogin {
	function __construct($params) {
		// Include the syndicate functions only once
		require_once (KUNENA_PATH . DS . 'class.kunena.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.link.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.image.class.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.login.php');
		require_once (KUNENA_PATH_LIB . DS . 'kunena.timeformat.class.php');
		$this->params = $params;

		// load Kunena main language file so we can leverage langaueg strings from it
		KunenaFactory::loadLanguage();
	}

	function display() {
		$this->login = CKunenaLogin::getloginFields ();
		$this->logout = CKunenaLogin::getlogoutFields ();

		$this->my = JFactory::getUser ();
		$this->private = KunenaFactory::getPrivateMessaging ();
		$this->PMCount = $this->private->getUnreadCount ( $this->my->id );
		$this->PMlink = $this->private->getInboxLink ( $this->PMCount ? JText::sprintf ( 'MOD_KUNENALOGIN_NEW_MESSAGE', $this->PMCount ) : JText::_ ( 'MOD_KUNENALOGIN_MYMESSAGES' ) );

		$this->params->def ( 'greeting', 1 );
		$this->type = $this->getType ();
		$this->return = $this->getReturnURL ();

		$this->loadCSS = $this->loadCSS ( 'mod_kunenalogin' ); // load CSS stylesheet
		$this->user = KunenaFactory::getUser ();

		require (JModuleHelper::getLayoutPath ( 'mod_kunenalogin' ));
	}

	function getReturnURL() {
		$itemid = $this->params->get ( $this->type );
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
		$user = KunenaFactory::getUser ();
		return ($user->userid) ? 'logout' : 'login';
	}

	function loadCSS($name) {
		$document = JFactory::getDocument ();
		$live_path = JURI::base ( true ) . '/';

		// add CSS stylesheet
		$document->addStyleSheet ( $live_path . "modules/$name/tmpl/css/$name.css", "text/css" );
	}

	function kunenaAvatar($userid) {
		$kunena_user = KunenaFactory::getUser ( ( int ) $userid );
		$username = $kunena_user->getName(); // Takes care of realname vs username setting
		$avatarlink = $kunena_user->getAvatarLink ( '', $this->params->get ( 'avatar_w' ), $this->params->get ( 'avatar_h' ) );
		return CKunenaLink::GetProfileLink ( $userid, $avatarlink, $username );
	}
}