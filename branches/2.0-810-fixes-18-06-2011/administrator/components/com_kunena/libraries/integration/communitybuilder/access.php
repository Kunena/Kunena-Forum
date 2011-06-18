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

class KunenaAccessCommunityBuilder extends KunenaAccess {
	protected $joomlaAccess = null;
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('communitybuilder');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->joomlaAccess = KunenaIntegration::initialize ( 'access', 'joomla');
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

	public function loadAllowedCategories($userid, &$categories) {
		$allowed = $this->joomlaAccess->getAllowedCategories($userid, $categories);
		$allowed = implode(',', $allowed);
		$params = array ($userid, &$allowed);
		$this->integration->trigger ( 'getAllowedForumsRead', $params );
		return explode(',', $allowed);
	}

	public function getGroupName($id){
		return $this->joomlaAccess->getGroupName($id);
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