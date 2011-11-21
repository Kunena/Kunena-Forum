<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Comprofiler
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaAccessComprofiler {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	public function loadAdmins() {
		$list = array();
		$params = array ('list'=>&$list);
		KunenaIntegrationComprofiler::trigger ( 'loadAdmins', $params );
		return $this->storeAdmins($list);
	}

	public function loadModerators() {
		$list = array();
		$params = array ('list'=>&$list);
		KunenaIntegrationComprofiler::trigger ( 'loadModerators', $params );
		return $this->storeModerators($list);
	}

	public function loadAllowedCategories($userid, &$categories) {
		$allowed = $this->joomlaAccess->getAllowedCategories($userid, $categories);
		$allowed = implode(',', $allowed);
		$params = array ($userid, &$allowed);
		KunenaIntegrationComprofiler::trigger ( 'getAllowedForumsRead', $params );
		return explode(',', $allowed);
	}

	public function getGroupName($accesstype, $id){
		return $this->joomlaAccess->getGroupName($accesstype, $id);
	}

	public function checkSubscribers($topic, &$userids) {
		$category = $topic->getCategory();
		if ($category->accesstype == 'communitybuilder') {
			// TODO: add support into CB
			$params = array ($category, &$userids);
			KunenaIntegrationComprofiler::trigger ( 'checkSubscribers', $params );
		} else {
			$this->joomlaAccess->checkSubscribers($topic, $userids);
		}
	}
}
