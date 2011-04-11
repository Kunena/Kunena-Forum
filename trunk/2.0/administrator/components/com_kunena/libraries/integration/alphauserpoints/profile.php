<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaProfileAlphaUserPoints extends KunenaProfile {
	public function __construct() {
		$aup = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
		if (! file_exists ( $aup ))
			return;
		require_once ($aup);
		$this->priority = 60;
	}

	public function getUserListURL($action = '', $xhtml = true) {
		$config = KunenaFactory::getConfig ();
		$my = JFactory::getUser();
		if ( $config->userlist_allowed == 1 && $my->id == 0  ) return false;
		if (method_exists ( 'AlphaUserPointsHelper', 'getAupUsersURL' ))
			return AlphaUserPointsHelper::getAupUsersURL ();
		else {
			// For AUP 1.5.3 etc..
			static $AUP_itemid = false;
			if ($AUP_itemid === false) {
				$db = JFactory::getDBO ();
				$query = "SELECT id FROM #__menu WHERE `link`='index.php?option=com_alphauserpoints&view=users' AND `type`='component' AND `published`='1'";
				$db->setQuery ( $query );
				$AUP_itemid = intval ( $db->loadResult () );
			}
			return JRoute::_ ( 'index.php?option=com_alphauserpoints&view=users&Itemid=' . $AUP_itemid, $xhtml );
		}
	}

	public function getProfileURL($user, $task = '', $xhtml = true) {
		if ($user == 0)
			return false;
		$user = KunenaFactory::getUser ( $user );
		$my = JFactory::getUser ();
		if ($user === false)
			return false;
		$userid = $my->id != $user->userid ? '&userid=' . AlphaUserPointsHelper::getAnyUserReferreID ( $user->userid ) : '';
		if (method_exists ( 'AlphaUserPointsHelper', 'getItemidAupProfil' )) {
			$AUP_itemid = AlphaUserPointsHelper::getItemidAupProfil ();
		} else {
			$db = JFactory::getDBO ();
			$query = "SELECT id FROM #__menu WHERE link='index.php?option=com_alphauserpoints&view=account' AND type='component' AND published='1'";
			$db->setQuery ( $query );
			$AUP_itemid = intval ( $db->loadResult () );
		}
		return JRoute::_ ( 'index.php?option=com_alphauserpoints&view=account' . $userid . '&Itemid=' . $AUP_itemid, $xhtml );
	}

	public function _getTopHits($limit=0) {
		$db = JFactory::getDBO ();
		$query = "SELECT userid AS id, profileviews AS count FROM #__alpha_userpoints WHERE a.profileviews>0 ORDER BY profileviews DESC";
		$db->setQuery ( $query, 0, $limit );
		$top = $db->loadObjectList ();
		KunenaError::checkDatabaseError ();
		return $top;
	}

	public function showProfile($userid, &$msg_params) {
	}
}
