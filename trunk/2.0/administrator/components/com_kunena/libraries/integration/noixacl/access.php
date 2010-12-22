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

class KunenaAccessNoixACL extends KunenaAccess {
	function __construct() {
		if (!is_file(JPATH_ADMINISTRATOR.'/components/com_noixacl/noixacl.php'))
			return null;
		$this->priority = 40;
	}

	protected function loadAdmins() {
		$db = JFactory::getDBO ();
		$query = "SELECT u.id AS userid, 0 AS catid FROM #__users AS u
			WHERE u.block='0' AND u.usertype IN ('Administrator', 'Super Administrator')";
		$db->setQuery ( $query );
		$list = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError ();
		return parent::loadAdmins($list);
	}

	protected function loadModerators() {
		$db = JFactory::getDBO ();
		$query = "SELECT u.id AS userid, m.catid
				FROM #__users AS u
				INNER JOIN #__kunena_users AS ku ON u.id=ku.userid
				LEFT JOIN #__kunena_moderation AS m ON u.id=m.userid
				LEFT JOIN #__kunena_categories AS c ON m.catid=c.id
				WHERE u.block='0' AND ku.moderator='1' AND (m.catid IS NULL OR c.moderated='1')";
		$db->setQuery ( $query );
		$list = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError ();
		return parent::loadModerators($list);
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
			} elseif ($row->pub_access == 0 ||
				($userid > 0 && (
				($row->pub_access == - 1)
				|| ($row->pub_access > 0 && self::_has_rights ( $multigroups, $row->pub_access, $row->pub_recurse ))
				|| ($row->admin_access > 0 && self::_has_rights ( $multigroups, $row->admin_access, $row->admin_recurse ))))) {
				$catlist[$row->id] = 1;
			}
		}
		return $catlist;
	}

	protected function _has_rights($usergroups, $groupid, $recurse) {
		if (in_array($groupid, $usergroups))
			return 1;
		if ($usergroups && $recurse) {
			$childs = $this->_get_groups($groupid, $recurse);
			if (array_intersect($childs, $usergroups))
				return 1;
		}
		return 0;
	}

	protected function _get_groups($groupid, $recurse) {
		static $groups = array();

		if (isset ($groups[$groupid]))
			return $groups[$groupid];

		if ($groupid > 0 && $recurse) {
			$acl = JFactory::getACL ();
			$groups[$groupid] = $acl->get_group_children ( $groupid, 'ARO', 'RECURSE' );
			$groups[$groupid][] = $groupid;
			return $groups[$groupid];
		}
		return array($groupid);
	}

	protected function checkSubscribers($topic, &$userids) {
		$userlist = implode(',', $userids);

		$db = JFactory::getDBO ();
		$query = new JDatabaseQuery();
		$query->select('u.id');
		$query->from('#__users AS u');
		$query->where("u.block=0");
		$query->where("u.id IN ({$userlist})");

		$category = $topic->getCategory();
		if ($category->accesstype == 'joomla') {
			// Check against Joomla access level
			if ( $category->access > 1 ) {
				// Special users = not in registered group
				$query->where("u.gid!=18");
			}
		} elseif ($category->accesstype == 'none') {
			// Check against Joomla user groups
			$public = $this->_get_groups($category->pub_access, $category->pub_recurse);
			$admin = $category->pub_access > 0 ? $this->_get_groups($category->admin_access, $category->admin_recurse) : array();
			$groups = implode ( ',', array_unique ( array_merge ( $public, $admin ) ) );
			if ($groups) {
				$query->join('LEFT', "#__noixacl_multigroups AS g ON g.id_user=u.id");
				$query->where("(u.gid IN ({$groups}) OR g.id_group IN ({$groups}))");
			}
		} else {
			return array();
		}

		$db->setQuery ($query);
		$userids = (array) $db->loadObjectList('id');
		KunenaError::checkDatabaseError();
	}
}
