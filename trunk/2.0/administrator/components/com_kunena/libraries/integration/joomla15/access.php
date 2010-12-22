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

class KunenaAccessJoomla15 extends KunenaAccess {
	function __construct() {
		if (is_dir(JPATH_LIBRARIES.'/joomla/access'))
			return null;
		$this->priority = 25;
	}

	protected function loadAdmins() {
		$db = JFactory::getDBO ();
		$query = "SELECT u.id AS userid, 0 AS catid
			FROM #__users AS u
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

		if ($user->id != 0) {
			$acl = JFactory::getACL ();
			$gid = $acl->getAroGroup ( $user->id )->id;
		} else {
			$gid = 0;
		}

		$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
		$catlist = array();
		foreach ( $categories as $category ) {
			// Check if user is a moderator
			if (self::isModerator($user->id, $category->id)) {
				$catlist[$category->id] = $category->id;
			}
			// Check against Joomla access level
			if ($category->accesstype == 'joomla') {
				if ( $category->access <= $user->get('aid') ) {
					$catlist[$category->id] = $category->id;
				}
			}
			// Check against Joomla user group
			if ($category->accesstype == 'none') {
				if (self::isModerator($user->id, $category->id)) {
					$catlist[$category->id] = $category->id;
				} elseif ($category->pub_access == 0 ||
					($user->id > 0 && (
					($category->pub_access == - 1)
					|| ($category->pub_access > 0 && self::_has_rights ( $gid, $category->pub_access, $category->pub_recurse ))
					|| ($category->admin_access > 0 && self::_has_rights ( $gid, $category->admin_access, $category->admin_recurse ))))) {
					$catlist[$category->id] = $category->id;
				}
			}
		}
		return $catlist;
	}

	protected function checkSubscribers($topic, &$userids) {
		$userlist = implode(',', $userids);

		$db = JFactory::getDBO ();
		$query = new JDatabaseQuery();
		$query->select('u.id');
		$query->from('#__users AS u');
		$query->where("u.block=0");
		$query->where("u.id IN ($userlist)");

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
			if ($groups)
				$query->where("u.gid IN ({$groups})");
		} else {
			return array();
		}

		$db->setQuery ($query);
		$userids = (array) $db->loadResultArray();
		KunenaError::checkDatabaseError();
	}

	protected function _has_rights($usergroup, $groupid, $recurse) {
		if ($usergroup == $groupid)
			return 1;
		if ($usergroup && $recurse) {
			$childs = $this->_get_groups($groupid, $recurse);
			if (in_array ( $usergroup, $childs ));
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
