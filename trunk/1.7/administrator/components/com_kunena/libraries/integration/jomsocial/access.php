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
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaAccessJomSocial extends KunenaAccess {
	protected $joomlaAccess = null;
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('jomsocial');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->joomlaAccess = KunenaAccess::getInstance('joomla');
		$this->priority = 50;
	}

	function loadAdmins() {
		$this->joomlaAccess->loadAdmins();
		$this->adminsByCatid = $this->joomlaAccess->adminsByCatid;
		$this->adminsByUserid = $this->joomlaAccess->adminsByUserid;

		$db = JFactory::getDBO();
		$query	= "SELECT g.ownerid AS userid, c.id AS catid FROM #__kunena_categories AS c
			INNER JOIN #__community_groups AS g ON c.accesstype='jomsocial' AND c.access=g.id
			WHERE g.published=1";
		$db->setQuery( $query );
		$list = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError ())
			return;

		foreach ( $list as $item ) {
			$userid = intval ( $item->userid );
			$catid = intval ( $item->catid );
			$this->adminsByUserid [$userid] [$catid] = 1;
			$this->adminsByCatid [$catid] [$userid] = 1;
		}
	}

	function loadModerators() {
		$this->joomlaAccess->loadModerators();
		$this->moderatorsByCatid = $this->joomlaAccess->moderatorsByCatid;
		$this->moderatorsByUserid = $this->joomlaAccess->moderatorsByUserid;

		$db = JFactory::getDBO();
		$query	= "SELECT g.memberid AS userid, c.id AS catid FROM #__kunena_categories AS c
			INNER JOIN #__community_groups_members AS g ON c.accesstype='jomsocial' AND c.access=g.groupid
			WHERE c.published=1 AND g.approved=1 AND g.permissions={$db->Quote( COMMUNITY_GROUP_ADMIN )}";
		$db->setQuery( $query );
		$list = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError ())
			return;

		foreach ( $list as $item ) {
			$userid = intval ( $item->userid );
			$catid = intval ( $item->catid );
			$this->moderatorsByUserid [$userid] [$catid] = 1;
			$this->moderatorsByCatid [$catid] [$userid] = 1;
		}
	}

	function loadAllowedCategories($userid) {
		$userid = intval($userid);
		$allowed = $this->joomlaAccess->getAllowedCategories($userid);

		$db = JFactory::getDBO();
		$query	= "SELECT c.id FROM #__kunena_categories AS c
			INNER JOIN #__community_groups_members AS g ON c.accesstype='jomsocial' AND c.access=g.groupid
			WHERE c.published=1 AND g.approved=1 AND g.memberid={$db->quote($userid)}";
		$db->setQuery( $query );
		$list = $db->loadResultArray ();
		if (KunenaError::checkDatabaseError ())
			return;

		foreach ( $list as $catid ) {
			$allowed [intval($catid)] = 1;
		}
		return $allowed;
	}

	function getSubscribers($catid, $thread, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		$this->joomlaAccess->moderatorsByCatid = $this->moderatorsByCatid;
		$this->joomlaAccess->moderatorsByUserid = $this->moderatorsByUserid;
		return $this->joomlaAccess->getSubscribers($catid, $thread, $subscriptions, $moderators, $admins, $excludeList);
	}
}