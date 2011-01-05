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
defined ( '_JEXEC' ) or die ( '' );

require_once KPATH_ADMIN . '/libraries/integration/integration.php';
kimport ( 'error' );
kimport ( 'category' );
kimport ( 'joomla.database.databasequery' );

abstract class KunenaAccess {
	public $priority = 0;

	protected static $instance = false;
	protected $adminsByCatid = false;
	protected $adminsByUserid = false;
	protected $moderatorsByCatid = false;
	protected $moderatorsByUserid = false;

	abstract public function __construct();

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			$config = KunenaFactory::getConfig ();
			if (! $integration)
				$integration = $config->integration_access;
			self::$instance = KunenaIntegration::initialize ( 'access', $integration );
		}

		return self::$instance;
	}

	public function clearCache() {
		$this->adminsByCatid = false;
		$this->adminsByUserid = false;
		$this->moderatorsByCatid = false;
		$this->moderatorsByUserid = false;

		$db = JFactory::getDBO ();
		$db->setQuery ( "UPDATE #__kunena_sessions SET allowed='na'" );
		$db->query ();
		KunenaError::checkDatabaseError();
	}

	public function getAccessLevelsList($category) {
		if ($category->accesstype == 'joomla')
			return JHTML::_('list.accesslevel', $category);
		return $category->access;
	}

	public function getAdmins($catid = 0) {
		if ($this->adminsByCatid === false) {
			$this->loadAdmins();
		}
		return !empty($this->adminsByCatid[$catid]) ? $this->adminsByCatid[$catid] : array();
	}

	public function getModerators($catid = 0) {
		if ($this->moderatorsByCatid === false) {
			$this->loadModerators();
		}
		return !empty($this->moderatorsByCatid[$catid]) ? $this->moderatorsByCatid[$catid] : array();
	}

	public function isAdmin($user = null, $catid = 0) {
		$user = KunenaFactory::getUser($user);

		// Visitors cannot be administrators
		if (!$user->exists()) return false;

		// In backend every logged in user has global admin rights (for now)
		if (JFactory::getApplication()->isAdmin())
			return true;

		// Load administrators list
		if ($this->adminsByUserid === false) {
			$this->loadAdmins();
		}

		// If $catid is not numeric: Is user administrator in ANY category?
		if (!is_numeric($catid)) return !empty($this->adminsByUserid[$user->userid]);

		// Is user a global administrator?
		if (!empty($this->adminsByUserid[$user->userid][0])) return true;
		// Is user a category administrator?
		if (!empty($this->adminsByUserid[$user->userid][$catid])) return true;

		return false;
	}

	public function isModerator($user = null, $catid = 0) {
		$user = KunenaFactory::getUser($user);

		// Visitors cannot be moderators
		if (!$user->exists()) return false;

		// Administrators are always moderators
		if ($this->isAdmin($user, $catid)) return true;

		// Load moderators list
		if ($this->moderatorsByUserid === false) {
			$this->loadModerators();
		}

		if (isset($this->moderatorsByUserid[$user->userid])) {
			// Is user a global moderator?
			if (!empty($this->moderatorsByUserid[$user->userid][0])) return true;
			// Were we looking only for global moderator?
			if (!is_numeric($catid)) return false;
			// Is user moderator in ANY category?
			if ($catid == 0) return true;
			// Is user a category moderator?
			if (!empty($this->moderatorsByUserid[$user->userid][$catid])) return true;
		}
		return false;
	}

	public function getAllowedCategories($user = null, $rule = 'read') {
		static $read = false;

		$user = KunenaFactory::getUser($user);

		$allowed = array();
		switch ($rule) {
			case 'read':
			case 'post':
			case 'reply':
			case 'edit':
				if ($read === false) {
					$read = $this->loadAllowedCategories($user->userid);
				}
				$allowed = $read;
				break;
			case 'moderate':
				if ($this->moderatorsByUserid === false) {
					$this->loadModerators();
				}
				if (isset($this->moderatorsByUserid[$user->userid])) $allowed += $this->moderatorsByUserid[$user->userid];
				// Continue: Administrators have also moderation permissions
				case 'admin':
				if ($this->adminsByUserid === false) {
					$this->loadAdmins();
				}
				if (isset($this->adminsByUserid[$user->userid])) $allowed += $this->adminsByUserid[$user->userid];
		}
		return $allowed;
	}

	public function getAllowedHold($user, $catid, $string=true) {
		// hold = 0: normal
		// hold = 1: unapproved
		// hold = 2: deleted
		$user = KunenaFactory::getUser($user);
		$config = KunenaFactory::getConfig ();

		$hold [0] = 0;
		if ($this->isModerator ( $user->userid, $catid )) {
			$hold [1] = 1;
		}
		if ($this->isAdmin ( $user->userid, $catid )
			|| ($config->mod_see_deleted && $this->isModerator( $user->userid, $catid ))) {
			$hold [2] = 2;
			$hold [3] = 3;
	}
		if ($string) $hold = implode ( ',', $hold );
		return $hold;
	}

	function getSubscribers($catid, $topicid, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		if ($subscriptions) {
			$subslist = $this->loadSubscribers($catid, $topicid);
		}
		if ($moderators) {
			if ($this->moderatorsByCatid === false) {
				$this->loadModerators();
			}
			$modlist = array();
			if (!empty($this->moderatorsByCatid[0])) $modlist += $this->moderatorsByCatid[0];
			if (!empty($this->moderatorsByCatid[$catid])) $modlist += $this->moderatorsByCatid[$catid];
		}
		if ($admins) {
			if ($this->adminsByCatid === false) {
				$this->loadAdmins();
			}
			$adminlist = array();
			if (!empty($this->adminsByCatid[0])) $adminlist += $this->adminsByCatid[0];
			if (!empty($this->adminsByCatid[$catid])) $adminlist += $this->adminsByCatid[$catid];
		}

		$query = new KDatabaseQuery();
		$query->select('u.id, u.name, u.username, u.email');
		$query->from('FROM #__users AS u');
		$query->where("u.block=0");
		$userlist = array();
		if (!empty($subslist)) {
			$userlist = $subslist;
			$subslist = implode(',', array_keys($subslist));
			$query->select("IF( u.id IN ({$subslist}), 1, 0 ) AS subscription");
		} else {
			$query->select("0 AS subscription");
		}
		if (!empty($modlist)) {
			$userlist += $modlist;
			$modlist = implode(',', array_keys($modlist));
			$query->select("IF( u.id IN ({$modlist}), 1, 0 ) AS moderator");
		} else {
			$query->select("0 AS moderator");
		}
		if (!empty($adminlist)) {
			$userlist += $adminlist;
			$adminlist = implode(',', array_keys($adminlist));
			$query->select("IF( u.id IN ({$adminlist}), 1, 0 ) AS admin");
		} else {
			$query->select("0 AS admin");
		}
		$userlist = array_diff_key($userlist, $excludeList);
		if (!empty($userlist)) {
			$userlist = implode(',', array_keys($userlist));
			$query->where("u.id IN ({$userlist})");
			$db = JFactory::getDBO();
			$db->setQuery ( $query );
			$subsList = $db->loadObjectList ();
			if (KunenaError::checkDatabaseError()) return array();
		}

		unset($subslist, $modlist, $adminlist, $userlist);
		return $subsList;
	}

	protected function loadAdmins($list = array()) {
		foreach ( $list as $item ) {
			$userid = intval ( $item->userid );
			$catid = intval ( $item->catid );
			$this->adminsByUserid [$userid] [$catid] = 1;
			$this->adminsByCatid [$catid] [$userid] = 1;
		}
		return $list;
	}

	protected function loadModerators($list = array()) {
		foreach ( $list as $item ) {
			$userid = intval ( $item->userid );
			$catid = intval ( $item->catid );
			$this->moderatorsByUserid [$userid] [$catid] = 1;
			$this->moderatorsByCatid [$catid] [$userid] = 1;
		}
		return $list;
	}

	protected function &loadSubscribers($catid, $topicid) {
		$category = KunenaCategory::getInstance($catid);
		$db = JFactory::getDBO ();
		$query ="SELECT userid AS user_id FROM #__kunena_subscriptions WHERE thread={$topicid}
				UNION
				SELECT userid AS user_id FROM #__kunena_subscriptions_categories WHERE catid={$catid}";
		$db->setQuery ($query);
		$userids = (array) $db->loadResultArray();
		KunenaError::checkDatabaseError();
		if (!empty($userids)) $this->checkSubscribers($category, $userids);
		$userids = (array) array_combine ($userids, $userids);
		return $userids;
	}

	abstract protected function checkSubscribers($topic, &$userids);
	abstract protected function loadAllowedCategories($userid);
}