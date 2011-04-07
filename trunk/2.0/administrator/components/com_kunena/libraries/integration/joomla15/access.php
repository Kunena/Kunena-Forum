<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaAccessJoomla15 extends KunenaAccess {
	public function __construct() {
		$jversion = new JVersion ();
		if ($jversion->RELEASE != '1.5')
			return null;
		$this->priority = 25;

/* TOO SLOW:
		$authorization = JFactory::getACL();
		$authorization->addACL( 'com_kunena', 'administrator', 'users', 'super administrator' );
		$authorization->addACL( 'com_kunena', 'administrator', 'users', 'administrator' );
*/
	}

	public function loadAdmins() {
		$db = JFactory::getDBO ();
		$query = "SELECT u.id AS userid, 0 AS catid
			FROM #__users AS u
			WHERE u.block='0' AND u.usertype IN ('Administrator', 'Super Administrator')";
		$db->setQuery ( $query );
		$list = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError ();
		return $this->storeAdmins($list);
	}

	public function loadModerators() {
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
		return $this->storeModerators($list);
	}

	public function loadAllowedCategories($user, &$categories) {
		$user = JFactory::getUser($user);

		// Get all Joomla user groups for current user
		$usergroups = $user->id ? $this->acl_get_groups('users', $user->id) : array();

		$catlist = array();
		foreach ( $categories as $category ) {
			// Check if user is a moderator
			if (self::isModerator($user->id, $category->id)) {
				$catlist[$category->id] = $category->id;
			}
			// Check against Joomla access level
			elseif ($category->accesstype == 'joomla') {
				if ( $category->access <= $user->get('aid') ) {
					$catlist[$category->id] = $category->id;
				}
			}
			// Check against Joomla user group
			elseif ($category->accesstype == 'none') {
				if ($category->pub_access == 0 ||
					($user->id > 0 && (
					($category->pub_access == - 1)
					|| ($category->pub_access > 0 && self::_has_rights ( $usergroups, $category->pub_access, $category->pub_recurse ))
					|| ($category->admin_access > 0 && self::_has_rights ( $usergroups, $category->admin_access, $category->admin_recurse ))))) {
					$catlist[$category->id] = $category->id;
				}
			}
		}
		return $catlist;
	}

	public function checkSubscribers($topic, &$userids) {
		$category = $topic->getCategory();
		if (empty($userids) || $category->pub_access <= 0)
			return;

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
				$query->join('INNER', "#__core_acl_aro AS a ON u.id=a.value AND a.section_value='users'");
				$query->join('INNER', "#__core_acl_groups_aro_map AS g ON g.aro_id=a.id");
				$query->where("g.group_id IN ({$groups})");
			}
		} else {
			return;
		}

		$db->setQuery ($query);
		$userids = (array) $db->loadResultArray();
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

	/**
	 * JXtended: Grabs all groups mapped to an ARO.
	 *
	 * A root group value can be specified for looking at sub-tree
	 * (results include the root group)
	 *
	 * @param	string	The section value or the ARO or AXO
	 * @param	string	The value of the ARO or AXO
	 * @param	integer	The value of the group to start at (optional)
	 * @param	string	The type of group, either ARO or AXO (optional)
	 */
	protected function acl_get_groups($sectionValue, $value, $rootGroupValue=NULL, $type='ARO') {
		static $cache = null;

		$db		= JFactory::getDbo();
		$type	= strtolower($type);

		if ($type != 'aro' && $type != 'axo') {
			return array();
		}
		if (($sectionValue === '' || $sectionValue === null) && ($value === '' || $value === null)) {
			return array();
		}

		// Simple cache
		if ($cache === null) {
			$cache = array();
		}

		// Generate unique cache id.
		$cacheId = 'acl_get_groups_'.$sectionValue.'-'.$value.'-'.$rootGroupValue.'-'.$type;

		if (!isset($cache[$cacheId]))
		{
			$query = new KunenaDatabaseQuery();

			// Make sure we get the groups
			$query->select('DISTINCT g2.id');
			$query->from('#__core_acl_'.$type.' AS o');
			$query->join('INNER', '#__core_acl_groups_'.$type.'_map AS gm ON gm.'. $type .'_id=o.id');
			$query->join('INNER', '#__core_acl_'.$type.'_groups AS g1 ON g1.id = gm.group_id');

			$query->where('(o.section_value='. $db->quote($sectionValue) .' AND o.value='. $db->quote($value) .')');

			/*
			 * If root group value is specified, we have to narrow this query down
			 * to just groups deeper in the tree then what is specified.
			 * This essentially creates a virtual "subtree" and ignores all outside groups.
			 * Useful for sites like sourceforge where you may seperate groups by "project".
			 */
			if ( $rootGroupValue != '') {
				$query->join('INNER', '#__core_acl_'.$type.'_groups AS g3 ON g3.value='. $db->quote($rootGroupValue));
				$query->join('INNER', '#__core_acl_'.$type.'_groups AS g2 ON ((g2.lft BETWEEN g3.lft AND g1.lft) AND (g2.rgt BETWEEN g1.rgt AND g3.rgt))');
			}
			else {
				$query->join('INNER', '#__core_acl_'.$type.'_groups AS g2 ON (g2.lft <= g1.lft AND g2.rgt >= g1.rgt)');
			}

			$db->setQuery($query);
			$cache[$cacheId] = $db->loadResultArray();
		}

		return $cache[$cacheId];
	}
}