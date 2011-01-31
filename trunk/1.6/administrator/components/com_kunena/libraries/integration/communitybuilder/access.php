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

	protected function loadAdmins() {
		$list = $this->joomlaAccess->loadAdmins();
		// TODO: add support into CB
		$params = array ('list'=>&$list);
		$this->integration->trigger ( 'loadAdmins', $params );
		return parent::loadAdmins($list);
	}

	protected function loadModerators() {
		$list = $this->joomlaAccess->loadModerators();
		// TODO: add support into CB
		$params = array ('list'=>&$list);
		$this->integration->trigger ( 'loadModerators', $params );
		return parent::loadModerators($list);
	}

	protected function loadAllowedCategories($userid) {
		$allowed = $this->joomlaAccess->getAllowedCategories($userid);
		$allowed = implode(',', $allowed);
		$params = array ($userid, &$allowed);
		$this->integration->trigger ( 'getAllowedForumsRead', $params );
		return explode(',', $allowed);
	}

	protected function checkSubscribers($category, &$userids) {
		if ($category->accesstype == 'communitybuilder') {
			// TODO: add support into CB
			$params = array ($category, &$userids);
			$this->integration->trigger ( 'checkSubscribers', $params );
		} else {
			$this->joomlaAccess->checkSubscribers($category, $userids);
		}
	}
}