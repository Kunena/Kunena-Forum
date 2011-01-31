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

class KunenaProfileJomSocial extends KunenaProfile
{
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('jomsocial');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->priority = 50;
	}

	public function getUserListURL($action='', $xhtml = true)
	{
		$kunena_config = KunenaFactory::getConfig ();
		$my = JFactory::getUser();
		if ( $kunena_config->userlist_allowed == 1 && $my->id == 0  ) return false;
		return CRoute::_('index.php?option=com_community&view=search&task=browse', $xhtml);
	}

	public function getProfileURL($userid, $task='', $xhtml = true)
	{
		if ($userid == 0) return false;
		// Get CUser object
		$user = CFactory::getUser($userid);
		if($user === null) return false;
		return CRoute::_('index.php?option=com_community&view=profile&userid='.$userid, $xhtml);
	}

	public function getProfileView($PopUserCount=0) {
		$_db = &JFactory::getDBO ();
		$_config = KunenaFactory::getConfig ();

		$queryName = $_config->username ? "username" : "name";
		if (!$PopUserCount) $PopUserCount = $_config->popusercount;
		$query = "SELECT u.id AS user_id, c.view AS hits, u.{$queryName} AS user FROM #__community_users as c
					LEFT JOIN #__users as u on u.id=c.userid
					WHERE c.view>'0' ORDER BY c.view DESC";
		$_db->setQuery ( $query, 0, $PopUserCount );
		$topJomsocialProfileView = $_db->loadObjectList ();
		KunenaError::checkDatabaseError();

		return $topJomsocialProfileView;
	}

	public function showProfile($userid, &$msg_params) {}
}
