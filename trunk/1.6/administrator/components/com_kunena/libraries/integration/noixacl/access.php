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

class KunenaAccessNoixACL extends KunenaAccess {
	protected static $admins = false;
	protected static $moderators = false;

	function __construct() {
		if (!is_file(JPATH_ADMINISTRATOR.'/components/com_noixacl/noixacl.php'))
			return null;
		$this->priority = 40;
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
			$db = JFactory::getDBO();
			$db->setQuery ("SELECT u.id AS uid, m.catid FROM #__users AS u"
				." INNER JOIN #__kunena_users AS p ON u.id=p.userid"
				." LEFT JOIN #__kunena_moderation AS m ON u.id=m.userid"
				." LEFT JOIN #__kunena_categories AS c ON m.catid=c.id"
				." WHERE u.block='0' AND p.moderator='1' AND (m.catid IS NULL OR c.moderated='1')");
			$list = $db->loadObjectList();
			if (KunenaError::checkDatabaseError()) return;
			foreach ($list as $item) self::$moderators[$item->uid][] = $item->catid;
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
		$acl = JFactory::getACL ();
		$db = JFactory::getDBO ();
		$user = JFactory::getUser();

		if ($userid != 0) {
			$aro_group = $acl->getAroGroup ( $userid );
			$gid = $aro_group->id;
		} else {
			$gid = 0;
		}

		$query = "SELECT id, pub_access, pub_recurse, admin_access, admin_recurse
			FROM #__kunena_categories
			WHERE published='1'";
		$db->setQuery ( $query );
		$rows = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return array();

		//get NoixACL multigroups for current user
		$query = "SELECT g.id
			FROM #__core_acl_aro_groups AS g
			INNER JOIN #__noixacl_multigroups AS m
			WHERE g.id = m.id_group AND m.id_user = {$db->quote($userid)}";
		$db->setQuery( $query );
		$multigroups = (array) $db->loadResultArray();
		$multigroups[] = $user->gid;
		if (KunenaError::checkDatabaseError()) return array();

		$catlist = array();
		foreach ( $rows as $row ) {
			if (self::isModerator($userid, $row->id)) {
				$catlist[$row->id] = $row->id;
			} elseif (($row->pub_access == 0)
				or ($row->pub_access == - 1 && $userid > 0)
				or ($row->pub_access > 0 && self::_has_rights ( $acl, $multigroups, $row->pub_access, $row->pub_recurse ))
				or ($row->admin_access > 0 && self::_has_rights ( $acl, $multigroups, $row->admin_access, $row->admin_recurse ))) {
				$catlist[$row->id] = $row->id;
			}
		}
		return implode(',', $catlist);
	}

	protected function _has_rights(&$acl, $multigroups, $access, $recurse) {
		if (in_array($access, $multigroups))
			return 1;
		if ($recurse) {
			$childs = (array) $acl->get_group_children ( $access, 'ARO', 'RECURSE' );
			if (array_intersect($childs, $multigroups)) return 1;
		}
		return 0;
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

		$arogroups = '';
		if ($subscriptions) {
			// Get all allowed Joomla groups to make sure that subscription is valid
			$kunena_acl = &JFactory::getACL ();
			$public = array ();
			$admin = array ();
			if ($access->pub_access > 0) {
				if ($access->pub_recurse) {
					$public = $kunena_acl->get_group_children ( $access->pub_access, 'ARO', 'RECURSE' );
				}
				$public [] = $access->pub_access;
			}
			if ($access->pub_access > 0 && $access->admin_access > 0) {
				if ($access->admin_recurse) {
					$admin = $kunena_acl->get_group_children ( $access->admin_access, 'ARO', 'RECURSE' );
				}
				$admin [] = $access->admin_access;
			}
			$arogroups = implode ( ',', array_unique ( array_merge ( $public, $admin ) ) );
			if ($arogroups)
				$arogroups = "(u.gid IN ({$arogroups}) OR g.id_group IN ({$arogroups}))";
		}

		$querysel = "SELECT u.id, u.name, u.username, u.email,
					IF( (s.thread IS NOT NULL) OR (sc.catid IS NOT NULL), 1, 0 ) AS subscription,
					IF( c.moderated=1 AND p.moderator=1 AND ( m.catid IS NULL OR m.catid={$catid}), 1, 0 ) AS moderator,
					IF( u.gid IN (24, 25), 1, 0 ) AS admin
					FROM #__users AS u
					LEFT JOIN #__kunena_users AS p ON u.id=p.userid
					LEFT JOIN #__noixacl_multigroups AS g ON g.id_user=u.id
					LEFT JOIN #__kunena_categories AS c ON c.id={$catid}
					LEFT JOIN #__kunena_moderation AS m ON u.id=m.userid
					LEFT JOIN #__kunena_subscriptions AS s ON u.id=s.userid AND s.thread={$thread}
					LEFT JOIN #__kunena_subscriptions_categories AS sc ON u.id=sc.userid AND sc.catid=c.id
					GROUP BY u.id";

		$where = array ();
		if ($subscriptions)
			$where [] = " ( ( (s.thread IS NOT NULL) OR (sc.catid IS NOT NULL) )" . ($arogroups ? " AND {$arogroups}" : '') . " ) ";
		if ($moderators)
			$where [] = " ( c.moderated=1 AND p.moderator=1 AND ( m.catid IS NULL OR m.catid={$catid} ) ) ";
		if ($admins)
			$where [] = " ( u.gid IN (24, 25) ) ";

		$subsList = array ();
		if (count ($where)) {
			$where = " AND (" . implode ( ' OR ', $where ) . ")";
			$query = $querysel . " WHERE u.block=0 AND u.id NOT IN ($excludeList) $where GROUP BY u.id";
			$db->setQuery ( $query );
			$subsList = $db->loadObjectList ();
			if (KunenaError::checkDatabaseError()) return array();
		}
		return $subsList;
	}
}
