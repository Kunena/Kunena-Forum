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

	function isAdmin($uid = null, $catid=0) {
		return $this->joomlaAccess->isAdmin($uid);
	}

	function isModerator($uid=null, $catid=0) {
		return $this->joomlaAccess->isModerator($uid,$catid);
	}

	function getAllowedCategories($userid) {
		$allowed = $this->joomlaAccess->getAllowedCategories($userid);
		if (!$allowed) $allowed = '0';
		$params = array ($userid, &$allowed );
		$this->integration->trigger ( 'getAllowedForumsRead', $params );
		return $allowed;
	}

	function getSubscribers($catid, $thread, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		return $this->joomlaAccess->getSubscribers($catid, $thread, $subscriptions, $moderators, $admins, $excludeList);
	}
}
