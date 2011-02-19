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

	protected function loadAdmins() {
		return parent::loadAdmins($this->joomlaAccess->loadAdmins());
	}

	protected function loadModerators() {
		$list = $this->joomlaAccess->loadModerators();

		$db = JFactory::getDBO();
		$query	= "SELECT g.memberid AS userid, c.id AS catid
			FROM #__kunena_categories AS c
			INNER JOIN #__community_groups_members AS g ON c.accesstype='jomsocial' AND c.access=g.groupid
			WHERE c.published=1 AND g.approved=1 AND g.permissions={$db->Quote( COMMUNITY_GROUP_ADMIN )}";
		$db->setQuery( $query );
		$jslist = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError ();

		return parent::loadModerators(array_merge($list, $jslist));
	}

	protected function loadAllowedCategories($userid) {
		$allowed = $this->joomlaAccess->getAllowedCategories($userid);

		$db = JFactory::getDBO();
		$query	= "SELECT c.id FROM #__kunena_categories AS c
			INNER JOIN #__community_groups_members AS g ON c.accesstype='jomsocial' AND c.access=g.groupid
			WHERE c.published=1 AND g.approved=1 AND g.memberid={$db->quote($userid)}";
		$db->setQuery( $query );
		$list = (array) $db->loadResultArray ();
		KunenaError::checkDatabaseError ();

		foreach ( $list as $catid ) {
			$allowed [$catid] = $catid;
		}
		return $allowed;
	}

	protected function checkSubscribers($category, &$userids) {
		// TODO: check if user should get email or not
		if ($category->accesstype != 'jomsocial') {
			$this->joomlaAccess->checkSubscribers($category, $userids);
		}
	}
}