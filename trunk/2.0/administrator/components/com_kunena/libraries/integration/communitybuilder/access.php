<?php
/**
 * @version $Id: access.php 4163 2011-01-07 10:45:09Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
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

	public function loadAdmins() {
		$list = $this->joomlaAccess->loadAdmins();
		// TODO: add support into CB
		$params = array ('list'=>&$list);
		$this->integration->trigger ( 'loadAdmins', $params );
		return $this->storeAdmins($list);
	}

	public function loadModerators() {
		$list = $this->joomlaAccess->loadModerators();
		// TODO: add support into CB
		$params = array ('list'=>&$list);
		$this->integration->trigger ( 'loadModerators', $params );
		return $this->storeModerators($list);
	}

	public function loadAllowedCategories($userid) {
		$allowed = $this->joomlaAccess->getAllowedCategories($userid);
		$allowed = implode(',', $allowed);
		$params = array ($userid, &$allowed);
		$this->integration->trigger ( 'getAllowedForumsRead', $params );
		return explode(',', $allowed);
	}

	public function checkSubscribers($topic, &$userids) {
		$category = $topic->getCategory();
		if ($category->accesstype == 'communitybuilder') {
			// TODO: add support into CB
			$params = array ($category, &$userids);
			$this->integration->trigger ( 'checkSubscribers', $params );
		} else {
			$this->joomlaAccess->checkSubscribers($topic, $userids);
		}
	}
}