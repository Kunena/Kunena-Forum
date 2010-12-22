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

	protected function loadAdmins() {
		$list = $this->joomlaAccess->loadAdmins();
		$params = array ('list'=>&$list);
		$this->integration->trigger ( 'getAdmins', $params );
		return parent::loadAdmins($list);
	}

	protected function loadModerators() {
		$list = $this->joomlaAccess->loadModerators();
		$params = array ('list'=>&$list);
		$this->integration->trigger ( 'getModerators', $params );
		return parent::loadModerators($list);
	}

	protected function loadAllowedCategories($userid) {
		$allowed = $this->joomlaAccess->getAllowedCategories($userid);
		$params = array ('userid'=>$userid, 'rules'=>array('read'=>&$allowed) );
		$this->integration->trigger ( 'getCategoryAccessRules', $params );
		return $allowed;
	}

	protected function checkSubscribers($topic, &$userids) {
		$category = $topic->getCategory();
		if ($category->accesstype != 'communitybuilder') {
			$this->joomlaAccess->checkSubscribers($topic, $userids);
		}
	}
}