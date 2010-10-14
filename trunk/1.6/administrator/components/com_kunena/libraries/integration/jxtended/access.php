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

class KunenaAccessJXtended extends KunenaAccess {
	protected static $admins = false;
	protected static $moderators = false;
	protected static $catmoderators = false;

	function __construct() {
		if (!function_exists('jximport'))
			return null;
		if (!jximport('jxtended.acl.acl'))
			return null;
		$this->priority = 40;
		$this->jxacl = new JxAcl();
	}

	function loadAdmins() {
		if (self::$admins === false) {
			self::$admins = array();
			$db = JFactory::getDBO();
			$db->setQuery ("SELECT u.id FROM #__users AS u"
				." WHERE u.block='0' "
				." AND u.usertype IN ('Administrator', 'Super Administrator')");
			self::$admins = $db->loadResultArray();
			KunenaError::checkDatabaseError();
		}
	}

	function loadModerators() {
		if (self::$moderators === false) {
			self::$moderators = array();
			self::$catmoderators = array();
			$db = JFactory::getDBO();
			$db->setQuery ("SELECT u.id AS uid, m.catid FROM #__users AS u"
				." INNER JOIN #__kunena_users AS p ON u.id=p.userid"
				." LEFT JOIN #__kunena_moderation AS m ON u.id=m.userid"
				." LEFT JOIN #__kunena_categories AS c ON m.catid=c.id"
				." WHERE u.block='0' AND p.moderator='1' AND (m.catid IS NULL OR c.moderated='1')");
			$list = $db->loadObjectList();
			if (KunenaError::checkDatabaseError()) return;
			foreach ($list as $item) {
				self::$moderators[$item->uid][] = $item->catid;
				self::$catmoderators[intval($item->catid)][] = $item->uid;
			}
		}
	}

	function isAdmin($uid = null, $catid=0) {
		// Avoid loading instances if it is possible
		$my = JFactory::getUser();
		if ($uid === null || (is_numeric($uid) && $uid == $my->id)){
			$uid = $my;
		}
		if ($uid instanceof JUser) {
			$usertype = $uid->get('usertype');
			return ($usertype == 'Administrator' || $usertype == 'Super Administrator');
		}
		if (!is_numeric($uid) || $uid == 0) return false;

		self::loadAdmins();

		if (in_array($uid, self::$admins)) return true;
		return false;
	}

	function isModerator($uid=null, $catid=0) {
		$my = JFactory::getUser();
		if ($uid === null || (is_numeric($uid) && $uid == $my->id)){
			$uid = $my;
		}
		// Administrators are always moderators
		if (self::isAdmin($uid)) return true;
		if ($uid instanceof JUser) {
			$uid = $uid->id;
		}
		// Visitors cannot be moderators
		if (!is_numeric($uid) || $uid == 0) return false;

		self::loadModerators();

		if (isset(self::$moderators[$uid])) {
			// Is user a global moderator?
			if (in_array(null, self::$moderators[$uid], true)) return true;
			// Were we looking only for global moderator?
			if ($catid === null || $catid === false) return false;
			// Is user moderator in any category?
			if (!$catid && count(self::$moderators[$uid])) return true;
			// Is user moderator in the category?
			if (in_array($catid, self::$moderators[$uid])) return true;
		}
		return false;
	}

	function getAllowedCategories($userid) {
		$db = JFactory::getDBO ();
		$query = "SELECT id, pub_access, pub_recurse, admin_access, admin_recurse
				FROM #__kunena_categories
				WHERE published='1'";
		$db->setQuery ( $query );
		$rows = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return array();

		$user = JFactory::getUser();
		$usergroups = $this->jxacl->acl_get_groups('users', $userid);

		$catlist = array();
		foreach ( $rows as $row ) {
			if (self::isModerator($userid, $row->id)) {
				$catlist[$row->id] = $row->id;
			} elseif (($row->pub_access == 0)
				or ($row->pub_access == - 1 && $userid > 0)
				or ($row->pub_access > 0 && self::_has_rights ( $usergroups, $row->pub_access, $row->pub_recurse ))
				or ($row->admin_access > 0 && self::_has_rights ( $usergroups, $row->admin_access, $row->admin_recurse ))) {
				$catlist[$row->id] = $row->id;
			}
		}
		return implode(',', $catlist);
	}

	protected function _has_rights($usergroups, $groupid, $recurse) {
		if (in_array($groupid, $usergroups))
			return 1;
		if ($recurse) {
			$acl = JFactory::getACL ();
			$childs = $acl->get_group_children ( $groupid, 'ARO', 'RECURSE' );
			if (array_intersect($childs, $usergroups)) return 1;
		}
		return 0;
	}

	protected function _get_groups($groupid, $recurse) {
		$groups = array();
		if ($groupid > 0) {
			if ($recurse) {
				$acl = JFactory::getACL ();
				$groups = $acl->get_group_children ( $groupid, 'ARO', 'RECURSE' );
			}
			$groups [] = $groupid;
		}
		return $groups;
	}

	protected function _get_subscribers($catid, $thread) {
		$db = JFactory::getDBO ();
		$query ="SELECT userid FROM #__kunena_subscriptions WHERE thread={$thread}
				UNION
				SELECT userid FROM #__kunena_subscriptions_categories WHERE catid={$catid}";
		$db->setQuery ($query);
		$userids = $db->loadResultArray();
		KunenaError::checkDatabaseError();
		return $userids;
	}

	function getSubscribers($catid, $thread, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		$catid = intval ( $catid );
		$thread = intval ( $thread );
		if (! $catid || ! $thread)
			return array();

		// Make sure that category exists and fetch access info
		$db = JFactory::getDBO ();
		$query = "SELECT pub_access, pub_recurse, admin_access, admin_recurse FROM #__kunena_categories WHERE id={$catid}";
		$db->setQuery ($query);
		$access = $db->loadObject ();
		if (KunenaError::checkDatabaseError() || !$access) return array();

		$arogroups = '';
		$sub_ids = -1;
		$mod_ids = -1;
		$adm_ids = -1;

		if ($subscriptions) {
			// Get all allowed Joomla groups to make sure that subscription is valid
			$kunena_acl = JFactory::getACL ();
			$public = $this->_get_groups($access->pub_access, $access->pub_recurse);
			$admin = array();
			if ($access->pub_access > 0) {
				$admin = $this->_get_groups($access->admin_access, $access->admin_recurse);
			}
			$arogroups = implode ( ',', array_unique ( array_merge ( $public, $admin ) ) );
			if ($arogroups)
				$arogroups = "g.group_id IN ({$arogroups})";
			$sub_ids = implode( ',', $this->_get_subscribers($catid, $thread) );
			if (!$sub_ids) $sub_ids = -1;
		}
		if ($moderators) {
			self::loadModerators();
			if (!isset(self::$catmoderators[$catid])) self::$catmoderators[$catid] = array();
			if (!isset(self::$catmoderators[0])) self::$catmoderators[0] = array();
			$mod_ids = implode ( ',', array_unique ( array_merge ( self::$catmoderators[$catid], self::$catmoderators[0] ) ) );
			if (!$mod_ids) $mod_ids = -1;
		}
		if ($admins) {
			self::loadAdmins();
			$adm_ids = implode( ',', self::$admins );
			if (!$adm_ids) $adm_ids = -1;
		}

		$querysel = "SELECT u.id, u.name, u.username, u.email,
					IF( u.id IN ($sub_ids), 1, 0 ) AS subscription,
					IF( u.id IN ($mod_ids), 1, 0 ) AS moderator,
					IF( u.id IN ($adm_ids), 1, 0 ) AS admin
					FROM #__users AS u
					INNER JOIN #__core_acl_aro AS a ON u.id=a.value AND section_value='users'
					INNER JOIN #__core_acl_groups_aro_map AS g ON g.aro_id=a.id";

		$where = array ();
		if ($subscriptions)
			$where [] = " ( u.id IN ($sub_ids)" . ($arogroups ? " AND {$arogroups}" : '') . " ) ";
		if ($moderators)
			$where [] = " ( u.id IN ($mod_ids) ) ";
		if ($admins)
			$where [] = " ( u.id IN ($adm_ids) ) ";

		$subsList = array ();
		if (count ($where)) {
			$where = " AND (" . implode ( ' OR ', $where ) . ")";
			$query = $querysel . " WHERE u.block=0 AND u.id NOT IN ($excludeList) $where GROUP BY u.id";
			$db->setQuery ( $query );
			$subsList = $db->loadObjectList ();
			if (KunenaError::checkDatabaseError()) return array();
		}

		unset($sub_ids, $mod_ids, $adm_ids);
		return $subsList;
	}
}