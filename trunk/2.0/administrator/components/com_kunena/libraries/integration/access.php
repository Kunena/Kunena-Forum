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
kimport ( 'kunena.error' );
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

	public function isAdmin($userid = null, $catid = 0) {
		$userid = $this->getUserid($userid);
		if ($userid === false) return false;

		// Visitors cannot be administrators
		if ($userid == 0) return false;

		// In backend every logged in user has global admin rights
		if (JFactory::getApplication()->isAdmin() && $userid == JFactory::getUser()->id)
			return true;

		if ($this->adminsByUserid === false) {
			$this->loadAdmins();
		}

		if (!is_numeric($catid)) return !empty($this->adminsByUserid[$userid]);
		if (!empty($this->adminsByUserid[$userid][0])) return true;
		if (!empty($this->adminsByUserid[$userid][$catid])) return true;

		return false;
	}

	public function isModerator($userid = null, $catid = 0) {
		$userid = $this->getUserid($userid);
		if ($userid === false) return false;

		// Visitors cannot be moderators
		if ($userid == 0) return false;

		// Administrators are always moderators
		if ($this->isAdmin($userid, $catid)) return true;

		if ($this->moderatorsByUserid === false) {
			$this->loadModerators();
		}

		if (isset($this->moderatorsByUserid[$userid])) {
			// Is user a global moderator?
			if (!empty($this->moderatorsByUserid[$userid][0])) return true;
			// Were we looking only for global moderator?
			if (!is_numeric($catid)) return false;
			// Is user moderator in any category?
			if ($catid == 0) return true;
			// Is user moderator in the category?
			if (!empty($this->moderatorsByUserid[$userid][$catid])) return true;
		}
		return false;
	}

	public function getAllowedCategories($userid = null, $rule = 'read') {
		static $read = false;
		$allowed = array();
		$userid = $this->getUserid($userid);
		if ($userid === false) return $allowed;
		switch ($rule) {
			case 'read':
			case 'post':
			case 'reply':
			case 'edit':
				if ($read === false) {
					$read = $this->loadAllowedCategories($userid);
				}
				$allowed = $read;
				break;
			case 'moderate':
				// Moderators have read/post/reply/edit permissions
				if ($this->moderatorsByUserid === false) {
					$this->loadModerators();
				}
				if (isset($this->moderatorsByUserid[$userid])) $allowed += $this->moderatorsByUserid[$userid];
			case 'admin':
				// Administrators have moderation permissions
				if ($this->adminsByUserid === false) {
					$this->loadAdmins();
				}
				if (isset($this->adminsByUserid[$userid])) $allowed += $this->adminsByUserid[$userid];
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

	protected function getUserid($userid) {
		$user = KunenaFactory::getUser($userid);
		if ($user instanceof KunenaUser) {
			return $user->userid;
		}
		return false;
	}

	abstract protected function loadAdmins();
	abstract protected function loadModerators();
	abstract protected function loadAllowedCategories($userid);
	abstract protected function _get_subscribers($catid, $thread);

	function getSubscribers($catid, $topic, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		$thread = intval ( $topic );
		if (! KunenaForumCategoryHelper::get($catid) || ! KunenaForumTopicHelper::get($topic))
			return array();

		if ($subscriptions) {
			$subslist = $this->_get_subscribers($catid, $topic);
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

		$query = new JDatabaseQuery();
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
}