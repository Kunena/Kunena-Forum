<?php
/**
 * @version $Id: kunena.session.class.php 2071 2010-03-17 11:27:58Z mahagr $
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

class KunenaAccessJoomla16 extends KunenaAccess {
	protected static $admins = false;
	protected static $moderators = false;

	function __construct() {
		if (!is_dir(JPATH_LIBRARIES.'/joomla/access'))
			return;
		$this->priority = 25;
	}

	function loadAdmins() {
		if (self::$admins === false) {
			self::$admins = array();
			jimport('joomla.access.access');
			$rules = JAccess::getAssetRules('com_kunena', true);
			$data = $rules->getData();
			$data = $data['core.admin']->getData();
			foreach ($data as $groupid=>$access) {
				if ($access) {
					self::$admins = array_unique(array_merge(self::$admins, JAccess::getUsersByGroup($groupid, true)));
				}
			}
		}
	}

	function loadModerators() {
		if (self::$moderators === false) {
			self::$moderators = array();
			$db = JFactory::getDBO();
			$db->setQuery ("SELECT u.id AS uid, m.catid FROM #__users AS u"
				." LEFT JOIN #__kunena_users AS p ON u.id=p.userid"
				." LEFT JOIN #__kunena_moderation AS m ON u.id=m.userid"
				." LEFT JOIN #__kunena_categories AS c ON m.catid=c.id"
				." WHERE u.block='0' AND p.moderator='1' AND (m.catid IS NULL OR c.moderated='1')");
			$list = $db->loadObjectList();
			if (KunenaError::checkDatabaseError()) return;
			foreach ($list as $item) self::$moderators[$item->uid][] = $item->catid;
		}
	}

	function isAdmin($uid = null, $catid=0) {
		if ($uid === null){
			$my = JFactory::getUser();
			$uid = $my->id;
		}
		if ($uid instanceof JUser) {
			$uid = $uid->id;
		}
		// Visitors cannot be admins
		if (!is_numeric($uid) || $uid == 0) return false;

		self::loadAdmins();

		if (in_array($uid, self::$admins)) return true;
		return false;
	}

	function isModerator($uid=null, $catid=0) {
		static $instances = null;

		if ($uid === null){
			$my = JFactory::getUser();
			$uid = $my->id;
		}
		if ($uid instanceof JUser) {
			$uid = $uid->id;
		}
		// Visitors cannot be moderators
		if (!is_numeric($uid) || $uid == 0) return false;

		// Administrators have always moderator permissions
		self::loadAdmins();
		if (in_array($uid, self::$admins)) return true;

		self::loadModerators();
		if (isset(self::$moderators[$uid])) {
			// Is user a global moderator?
			if (in_array(null, self::$moderators[$uid], true)) return true;
			// Were we looking only for global moderator?
			if (!is_numeric($catid)) return false;
			// Is user moderator in any category?
			if (!$catid && count(self::$moderators[$uid])) return true;
			// Is user moderator in the category?
			if ($catid && in_array($catid, self::$moderators[$uid])) return true;
		}
		return false;
	}

	function getAllowedCategories($userid) {
		$db = JFactory::getDBO ();
		$query = "SELECT c.id, c.pub_access, c.pub_recurse, c.admin_access, c.admin_recurse
				FROM #__kunena_categories c
				WHERE published='1'";
		$db->setQuery ( $query );
		$rows = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return array();

		$groups_r = JAccess::getGroupsByUser($userid, true);
		$groups = JAccess::getGroupsByUser($userid, false);
		$catlist = array();
		foreach ( $rows as $row ) {
			$pub_access = (($row->pub_recurse && in_array($row->pub_access, $groups_r)) || in_array($row->pub_access, $groups));
			$admin_access = (($row->admin_recurse && in_array($row->admin_access, $groups_r)) || in_array($row->admin_access, $groups));

			if (($row->pub_access == 0)
				|| ($row->pub_access == - 1 && $userid > 0)
				|| (self::isModerator($userid, $row->id))
				|| ( $pub_access )
				|| ( $admin_access )) {
				$catlist[] = $row->id;
			}
		}
		return implode(',', $catlist);
	}

	function getSubscribers($catid, $thread, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0') {
		$catid = intval ( $catid );
		$thread = intval ( $thread );
		if (! $catid || ! $thread)
			return array();

		// Make sure that category exists and fetch access info
		$db = &JFactory::getDBO ();
		$query = "SELECT pub_access, pub_recurse, admin_access, admin_recurse FROM #__kunena_categories WHERE id={$catid}";
		$db->setQuery ($query);
		$access = $db->loadObject ();
		if (KunenaError::checkDatabaseError() || !$access) return array();

		if ($admins) {
			self::loadAdmins();
			$adminlist = implode(',', self::$admins);
			if (!$adminlist) $adminlist = 0;
		}

		$groups = '';
		if ($subscriptions) {
			// Get all allowed Joomla groups to make sure that subscription is valid
			$kunena_acl = &JFactory::getACL ();
			$public = array ();
			$admin = array ();
			if ($access->pub_access > 0) {
				if ($access->pub_recurse) {
					$query= $db->getQuery(true);
					$query->select('a.id');
					$query->from('#__usergroups AS a');
					$query->leftJoin('#__usergroups AS b ON b.lft <= a.lft AND b.rgt >= a.rgt');
					$query->where('a.id = ' . $access->pub_recurse);
					$db->setQuery($query);
					$public = $db->loadResultArray();
				} else {
					$public [] = $access->pub_access;
				}
			}
			if ($access->pub_access > 0 && $access->admin_access > 0) {
				if ($access->admin_recurse) {
					$query= $db->getQuery(true);
					$query->select('a.id');
					$query->from('#__usergroups AS a');
					$query->leftJoin('#__usergroups AS b ON b.lft <= a.lft AND b.rgt >= a.rgt');
					$query->where('a.id = ' . $access->admin_recurse);
					$db->setQuery($query);
					$admin= $db->loadResultArray();
				} else {
					$admin [] = $access->admin_access;
				}
			}
			$groups = implode ( ',', array_unique ( array_merge ( $public, $admin ) ) );
			if ($groups)
				$groups = "u.gid IN ({$groups})";
		}

		$querysel = "SELECT u.id, u.name, u.username, u.email,
					IF( (s.thread IS NOT NULL) OR (sc.catid IS NOT NULL), 1, 0 ) AS subscription,
					IF( c.moderated=1 AND p.moderator=1 AND ( m.catid IS NULL OR m.catid={$catid}), 1, 0 ) AS moderator,
					IF( u.id IN ({$adminlist}), 1, 0 ) AS admin
					FROM #__users AS u
					LEFT JOIN #__kunena_users AS p ON u.id=p.userid
					LEFT JOIN #__kunena_categories AS c ON c.id={$catid}
					LEFT JOIN #__kunena_moderation AS m ON u.id=m.userid AND m.catid=c.id
					LEFT JOIN #__kunena_subscriptions AS s ON u.id=s.userid AND s.thread={$thread}
					LEFT JOIN #__kunena_subscriptions_categories AS sc ON u.id=sc.userid AND sc.catid=c.id";

		$where = array ();
		if ($subscriptions)
			$where [] = " ( s.thread IS NOT NULL" . ($groups ? " AND {$groups}" : '') . " ) ";
		if ($moderators)
			$where [] = " ( c.moderated=1 AND p.moderator=1 AND ( m.catid IS NULL OR m.catid={$catid} ) ) ";
		if ($admins)
			$where [] = " ( u.id IN ({$adminlist}) ) ";

		$subsList = array ();
		if (count ($where)) {
			$where = " AND (" . implode ( ' OR ', $where ) . ")";
			$query = $querysel . " WHERE u.block=0 AND u.id NOT IN ({$excludeList}) {$where} GROUP BY u.id";
			$db->setQuery ( $query );
			$subsList = $db->loadObjectList ();
			if (KunenaError::checkDatabaseError()) return array();
		}
		return $subsList;
	}
}
