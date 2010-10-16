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

class KunenaAccessJoomla16 extends KunenaAccess {
	function __construct() {
		if (!is_dir(JPATH_LIBRARIES.'/joomla/access'))
			return;
		$this->priority = 25;
	}

	protected function loadAdmins() {
		$this->adminsByCatid = array ();
		$this->adminsByUserid = array ();
		jimport ( 'joomla.access.access' );
		$rules = JAccess::getAssetRules ( 'com_kunena', true );
		$data = $rules->getData ();
		$data = $data ['core.admin']->getData ();
		$list = array ();
		foreach ( $data as $groupid => $access ) {
			if ($access) {
				$list = array_merge ( $list, JAccess::getUsersByGroup ( $groupid, true ) );
			}
		}
		$list = array_unique ( $list );
		foreach ( $list as $item ) {
			$userid = intval ( $item );
			$this->adminsByCatid [0] [$userid] = $userid;
			$this->adminsByUserid [$userid] [0] = 0;
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
			$this->moderatorsByUserid [$userid] [$catid] = $catid;
			$this->moderatorsByCatid [$catid] [$userid] = $userid;
		}
	}

	function loadAllowedCategories($userid) {
		$db = JFactory::getDBO ();
		$user = JFactory::getUser();
		$groups = implode(',', $user->authorisedLevels());

		$query = "SELECT id, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse
				FROM #__kunena_categories
				WHERE published='1' AND (accesstype='none' OR accesstype='joomla')";
		$db->setQuery ( $query );
		$rows = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return array();

		$accesslevels = (array) $user->authorisedLevels();
		$groups_r = JAccess::getGroupsByUser($userid, true);
		$groups = JAccess::getGroupsByUser($userid, false);

		$catlist = array();
		foreach ( $rows as $row ) {
			if (self::isModerator($userid, $row->id)) {
				$catlist[$row->id] = 1;
			} elseif ($row->accesstype == 'joomla') {
				if ( in_array($row->access, $accesslevels) )
					$catlist[$row->id] = 1;
			} else {
				$pub_access = (($row->pub_recurse && in_array($row->pub_access, $groups_r)) || in_array($row->pub_access, $groups));
				$admin_access = (($row->admin_recurse && in_array($row->admin_access, $groups_r)) || in_array($row->admin_access, $groups));

				if (($row->pub_access == 0)
					|| ($row->pub_access == - 1 && $userid > 0)
					|| ( $pub_access )
					|| ( $admin_access )) {
					$catlist[$row->id] = 1;
				}
			}
		}
		return $catlist;
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

		$groups = '';
		if ($subscriptions) {
			// Get all allowed Joomla groups to make sure that subscription is valid
			$kunena_acl = JFactory::getACL ();
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

		// FIXME
		$querysel = "SELECT u.id, u.name, u.username, u.email,
					IF( (ut.subscribed=1) OR (uc.subscribed=1), 1, 0 ) AS subscription,
					IF( u.id IN ({$modlist}), 1, 0 ) AS moderator,
					IF( u.id IN ({$adminlist}), 1, 0 ) AS admin
					FROM #__users AS u
					LEFT JOIN #__kunena_user_topics AS ut ON u.id=ut.user_id AND ut.topic_id={$thread}
					LEFT JOIN #__kunena_user_categories AS uc ON u.id=uc.user_id AND uc.category_id={$catid}";

		$where = array ();
		if ($subscriptions)
			$where [] = " ( ( ut.subscribed=1 OR uc.subscribed=1 )" . ($groups ? " AND {$groups}" : '') . " ) ";
		if ($moderators)
			$where [] = " ( u.id IN ({$modlist}) ) ";
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
