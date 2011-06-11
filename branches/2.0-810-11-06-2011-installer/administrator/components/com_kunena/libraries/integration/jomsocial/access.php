<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaAccessJomSocial extends KunenaAccess {
	protected $joomlaAccess = null;
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('jomsocial');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->joomlaAccess = KunenaIntegration::initialize ( 'access', 'joomla');
		$this->priority = 50;
	}

	public function loadAdmins() {
		$list = $this->joomlaAccess->loadAdmins();

		$db = JFactory::getDBO();
		$query	= "SELECT g.memberid AS userid, c.id AS catid
			FROM #__kunena_categories AS c
			INNER JOIN #__community_groups_members AS g ON c.accesstype='jomsocial' AND c.access=g.groupid
			WHERE c.published=1 AND g.approved=1 AND g.permissions={$db->Quote( COMMUNITY_GROUP_ADMIN )}";
		$db->setQuery( $query );
		$jslist = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError ();
		return $this->storeAdmins($jslist);
	}

	public function loadModerators() {
		return $this->joomlaAccess->loadModerators();
	}

	public function loadAllowedCategories($userid, &$categories) {
		$allowed = $this->joomlaAccess->loadAllowedCategories($userid, $categories);

		if (KunenaFactory::getUser($userid)->exists()) {
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
		}
		return $allowed;
	}

	public function getGroupName($id){
		return $this->joomlaAccess->getGroupName($id);
	}

	public function checkSubscribers($topic, &$userids) {
		$category = $topic->getCategory();
		// TODO: check if user should get email or not
		if ($category->accesstype != 'jomsocial') {
			$this->joomlaAccess->checkSubscribers($topic, $userids);
		}
	}
}