<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaAccessCommunityBuilder extends KunenaAccess {
	protected $joomlaAccess = null;
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('communitybuilder');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->joomlaAccess = KunenaAccess::getInstance('joomla');
		$this->priority = 50;
	}

	function loadAdmins() {
		$this->joomlaAccess->loadAdmins();
		$this->adminsByCatid = $this->joomlaAccess->adminsByCatid;
		$this->adminsByUserid = $this->joomlaAccess->adminsByUserid;
		$params = array ('byCatid'=>&$this->adminsByCatid, 'byUserid'=>&$this->adminsByUserid);
		$this->integration->trigger ( 'getAdmins', $params );
	}

	function loadModerators() {
		$this->joomlaAccess->loadModerators();
		$this->moderatorsByCatid = $this->joomlaAccess->moderatorsByCatid;
		$this->moderatorsByUserid = $this->joomlaAccess->moderatorsByUserid;
		$params = array ('byCatid'=>&$this->moderatorsByCatid, 'byUserid'=>&$this->moderatorsByUserid);
		$this->integration->trigger ( 'getModerators', $params );
	}

	function loadAllowedCategories($userid) {
		$allowed = $this->joomlaAccess->getAllowedCategories($userid);
		$params = array ('userid'=>$userid, 'rules'=>array('read'=>&$allowed) );
		$this->integration->trigger ( 'getCategoryAccessRules', $params );
		return $allowed;
	}

	function getSubscribers($catid, $thread, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		$this->joomlaAccess->moderatorsByCatid = $this->moderatorsByCatid;
		$this->joomlaAccess->moderatorsByUserid = $this->moderatorsByUserid;
		return $this->joomlaAccess->getSubscribers($catid, $thread, $subscriptions, $moderators, $admins, $excludeList);
	}
}