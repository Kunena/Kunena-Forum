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

class modKunenaLoginHelper {
	function getReturnURL($params, $type) {
		$itemid = $params->get ( $type );
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
		$user = JFactory::getUser ();
		return (! $user->get ( 'guest' )) ? 'logout' : 'login';
	}

	function loadCSS($name) {
		$document = JFactory::getDocument ();
		$live_path = JURI::base ( true ) . '/';

		// add CSS stylesheet
		$document->addStyleSheet ( $live_path . "modules/$name/tmpl/css/$name.css", "text/css" );
	}

	function kunenaAvatar($userid, $params) {
		$kunena_user = KunenaFactory::getUser ( ( int ) $userid );
		$avatarlink = $kunena_user->getAvatarLink ( '', $params->get ( 'avatar_w' ), $params->get ( 'avatar_h' ) );
		return CKunenaLink::GetProfileLink ( $userid, $avatarlink, $kunena_user->name );
	}
}