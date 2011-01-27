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
defined( '_JEXEC' ) or die();

class KunenaAccessNoixACL extends KunenaAccess {
	function __construct() {
		$jversion = new JVersion ();
		if ($jversion->RELEASE != '1.5')
			return null;

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

	protected function loadAllowedCategories($user) {
		$user = JFactory::getUser($user);
		$db = JFactory::getDBO ();

		// Get Joomla group for current user
		if ($user->id != 0) {
			$acl = JFactory::getACL ();
			$gid = $acl->getAroGroup ( $user->id )->id;
		} else {
			$gid = 0;
		}

		// Get NoixACL multigroups for current user
		$query = "SELECT g.id
		FROM #__core_acl_aro_groups AS g
		INNER JOIN #__noixacl_multigroups AS m
		WHERE g.id = m.id_group AND m.id_user = {$db->quote($user->id)}";
		$db->setQuery( $query );
		$multigroups = (array) $db->loadResultArray();
		$multigroups[] = $user->gid;
		if (KunenaError::checkDatabaseError()) return array();

		// Get NoixACL access levels for all user groups
		$groups = implode(',', $multigroups);
		$query = "SELECT l.id_levels
		FROM #__noixacl_groups_level AS l
		WHERE l.id_group IN ($groups)";
		$db->setQuery( $query );
		$levels = array_unique(explode(',', implode(',', (array) $db->loadResultArray())));
		if (KunenaError::checkDatabaseError()) return array();

		$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
		$catlist = array();
		foreach ( $categories as $category ) {
			// Check if user is a moderator
			if (self::isModerator($user->id, $category->id)) {
				$catlist[$category->id] = $category->id;
			}
			// Check against Joomla access level
			elseif ($category->accesstype == 'joomla') {
				if ( $category->access <= $user->get('aid') )
					$catlist[$category->id] = $category->id;
			}
			// Check against NoixACL access level
			elseif ($category->accesstype == 'noixacl') {
				if ( in_array($category->access, $levels) )
					$catlist[$category->id] = $category->id;
			}
			// Check against Joomla user group
			elseif ($category->accesstype == 'none') {
				if ($category->pub_access == 0 ||
					($user->id > 0 && (
					($category->pub_access == - 1)
					|| ($category->pub_access > 0 && self::_has_rights ( $multigroups, $category->pub_access, $category->pub_recurse ))
					|| ($category->admin_access > 0 && self::_has_rights ( $multigroups, $category->admin_access, $category->admin_recurse ))))) {
					$catlist[$category->id] = $category->id;
				}
			}
		}
		return $catlist;
	}

	protected function checkSubscribers($topic, &$userids) {
		$category = $topic->getCategory();
		if (empty($userids))
			return $userids;

		$userlist = implode(',', $userids);

		$db = JFactory::getDBO ();
		$query = new KunenaDatabaseQuery();
		$query->select('u.id');
		$query->from('#__users AS u');
		$query->where("u.block=0");
		$query->where("u.id IN ({$userlist})");

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
}
