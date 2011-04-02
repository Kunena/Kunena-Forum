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
defined( '_JEXEC' ) or die('');

class KunenaProfileKunena extends KunenaProfile
{
	public function __construct() {
		$this->priority = 25;
	}

	public function getUserListURL($action='', $xhtml = true)
	{
		$kunena_config = KunenaFactory::getConfig ();
		$my = JFactory::getUser();
		if ( $kunena_config->userlist_allowed == 1 && $my->id == 0  ) return false;
		return KunenaRoute::_('index.php?option=com_kunena&func=userlist'.$action, $xhtml);
	}

	public function getProfileURL($user, $task='', $xhtml = true)
	{
		if ($user == 0) return false;
		$user = KunenaFactory::getUser($user);
		$my = JFactory::getUser();
		if ($user === false) return false;
		$userid = $my->id != $user->userid ? "&userid={$user->userid}" : '';
		$do = $task ? '&do='.$task : '';
		return KunenaRoute::_("index.php?option=com_kunena&func=profile{$do}{$userid}", $xhtml);
	}

	public function getProfileView($PopUserCount=0) {
		$_db = &JFactory::getDBO ();
		$_config = KunenaFactory::getConfig ();

		$queryName = $_config->username ? "username" : "name";
		if (!$PopUserCount) $PopUserCount = $_config->popusercount;
		$query = "SELECT u.uhits AS hits, u.userid AS user_id, j.id, j.{$queryName} AS user FROM #__kunena_users AS u
					INNER JOIN #__users AS j ON j.id = u.userid
					WHERE u.uhits>'0' AND j.block=0 ORDER BY u.uhits DESC";
		$_db->setQuery ( $query, 0, $PopUserCount );
		$topKunenaProfileView = $_db->loadObjectList ();
		KunenaError::checkDatabaseError();

		return $topKunenaProfileView;
	}

	public function showProfile($userid, &$msg_params) {}
}
