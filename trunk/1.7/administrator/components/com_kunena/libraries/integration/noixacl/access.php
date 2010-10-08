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
	function __construct() {
		if (!is_file(JPATH_ADMINISTRATOR.'/components/com_noixacl/noixacl.php'))
			return null;
		$this->priority = 40;
	}

	protected function loadAdmins() {
		$this->adminsByCatid = array ();
		$this->adminsByUserid = array ();
		$db = JFactory::getDBO ();
		$query = "SELECT u.id FROM #__users AS u
			WHERE u.block='0' AND u.usertype IN ('Administrator', 'Super Administrator')";
		$db->setQuery ( $query );
		$list = $db->loadResultArray ();
		KunenaError::checkDatabaseError ();
		foreach ( $list as $item ) {
			$userid = intval ( $item );
			$this->adminsByCatid [0] [$userid] = 1;
			$this->adminsByUserid [$userid] [0] = 1;
		}
	}

	protected function loadModerators() {
		$this->moderatorsByCatid = array ();
		$this->moderatorsByUserid = array ();
		$db = JFactory::getDBO ();
		$query = "SELECT u.id AS userid, m.catid
				FROM #__users AS u
				INNER JOIN #__kunena_users AS ku ON u.id=ku.userid
				LEFT JOIN #__kunena_moderation AS m ON u.id=m.userid
				LEFT JOIN #__kunena_categories AS c ON m.catid=c.id
				WHERE u.block='0' AND ku.moderator='1' AND (m.catid IS NULL OR c.moderated='1')";
		$db->setQuery ( $query );
		$list = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError ())
			return;
		foreach ( $list as $item ) {
			$userid = intval ( $item->userid );
			$catid = intval ( $item->catid );
			$this->moderatorsByUserid [$userid] [$catid] = 1;
			$this->moderatorsByCatid [$catid] [$userid] = 1;
		}
	}

	protected function loadAllowedCategories($userid) {
		$acl = JFactory::getACL ();
		$db = JFactory::getDBO ();
		$user = JFactory::getUser();

		if ($userid != 0) {
			$aro_group = $acl->getAroGroup ( $userid );
			$gid = $aro_group->id;
		} else {
			$gid = 0;
		}

		$query = "SELECT id, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse
				FROM #__kunena_categories
				WHERE published='1' AND (accesstype='none' OR accesstype='joomla' OR accesstype='noixacl')";
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

		//get NoixACL access levels for all user groups
		$groups = implode(',', $multigroups);
		$query = "SELECT l.id_levels
		FROM #__noixacl_groups_level AS l
		WHERE l.id_group IN ($groups)";
		$db->setQuery( $query );
		$levels = array_unique(explode(',', implode(',', (array) $db->loadResultArray())));
		if (KunenaError::checkDatabaseError()) return array();

		$catlist = array();
		foreach ( $rows as $row ) {
			if (self::isModerator($userid, $row->id)) {
				$catlist[$row->id] = 1;
			} elseif ($row->accesstype == 'joomla') {
				if ( $row->access <= $user->get('aid') )
					$catlist[$row->id] = 1;
			} elseif ($row->accesstype == 'noixacl') {
				if ( in_array($row->access, $levels) )
					$catlist[$row->id] = 1;
			} elseif (($row->pub_access == 0)
				or ($row->pub_access == - 1 && $userid > 0)
				or ($row->pub_access > 0 && self::_has_rights ( $acl, $multigroups, $row->pub_access, $row->pub_recurse ))
				or ($row->admin_access > 0 && self::_has_rights ( $acl, $multigroups, $row->admin_access, $row->admin_recurse ))) {
				$catlist[$row->id] = 1;
			}
		}
		return $catlist;
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
		$db = JFactory::getDBO ();
		$query = "SELECT pub_access, pub_recurse, admin_access, admin_recurse FROM #__kunena_categories WHERE id={$catid}";
		$db->setQuery ($query);
		$access = $db->loadObject ();
		if (KunenaError::checkDatabaseError() || !$access) return array();

		$modlist = array();
		$adminlist = array();
		if ($moderators) {
			if ($this->moderatorsByCatid === false) {
				$this->loadModerators();
			}
			if (!empty($this->moderatorsByCatid[0])) $modlist = $this->moderatorsByCatid[0];
			if (!empty($this->moderatorsByCatid[$catid])) $modlist += $this->moderatorsByCatid[$catid];
		}
		if ($admins) {
			if ($this->adminsByCatid === false) {
				$this->loadAdmins();
			}
			if (!empty($this->adminsByCatid[0])) $adminlist = $this->adminsByCatid[0];
			if (!empty($this->adminsByCatid[$catid])) $adminlist += $this->adminsByCatid[$catid];
		}
		$modlist = !empty($modlist) ? implode(',', array_keys($modlist)) : '-1';
		$adminlist = !empty($adminlist) ? implode(',', array_keys($adminlist)) : '-1';

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
					IF( u.id IN ({$modlist}), 1, 0 ) AS moderator,
					IF( u.id IN ({$adminlist}), 1, 0 ) AS admin
					FROM #__users AS u
					LEFT JOIN #__noixacl_multigroups AS g ON g.id_user=u.id
					LEFT JOIN #__kunena_subscriptions AS s ON u.id=s.userid AND s.thread={$thread}
					LEFT JOIN #__kunena_subscriptions_categories AS sc ON u.id=sc.userid AND sc.catid={$catid}
					GROUP BY u.id";

		$where = array ();
		if ($subscriptions)
			$where [] = " ( ( (s.thread IS NOT NULL) OR (sc.catid IS NOT NULL) )" . ($arogroups ? " AND {$arogroups}" : '') . " ) ";
		if ($moderators)
			$where [] = " ( u.id IN ({$modlist}) ) ";
		if ($admins)
			$where [] = " ( u.id IN ({$adminlist}) ) ";

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
