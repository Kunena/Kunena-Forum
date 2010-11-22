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

class KunenaAccessJXtended extends KunenaAccess {
	function __construct() {
		$loader = JPATH_ADMINISTRATOR . '/components/com_artofuser/libraries/loader.php';
		if (is_file($loader)) {
			require_once $loader;
		}
		if (!function_exists('juimport') || !function_exists('jximport'))
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
		$db = JFactory::getDBO ();
		$query = "SELECT id, accesstype, access, pub_access, pub_recurse, admin_access, admin_recurse
				FROM #__kunena_categories
				WHERE published='1' AND (accesstype='none' OR accesstype='joomla' OR accesstype='jxaccess')";
		$db->setQuery ( $query );
		$rows = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return array();

		$user = JFactory::getUser();
		$usergroups = $this->acl_get_groups('users', $userid);

		$catlist = array();
		foreach ( $rows as $row ) {
			if (self::isModerator($userid, $row->id)) {
				$catlist[$row->id] = 1;
			} elseif ($row->accesstype == 'joomla') {
				if ( $row->access <= $user->get('aid') )
					$catlist[$row->id] = 1;
			} elseif ($row->pub_access == 0 ||
				($userid > 0 && (
				($row->pub_access == - 1)
				|| ($row->pub_access > 0 && self::_has_rights ( $usergroups, $row->pub_access, $row->pub_recurse ))
				|| ($row->admin_access > 0 && self::_has_rights ( $usergroups, $row->admin_access, $row->admin_recurse ))))) {
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

	protected function _get_subscribers($catid, $thread) {
		$db = JFactory::getDBO ();

		$tquery = new JDatabaseQuery();
		$tquery->select('u.id, 1 AS topic');
		$tquery->from('#__kunena_user_topics AS s');
		$tquery->join('INNER', '#__users AS u ON s.user_id=u.id');
		$tquery->where("s.topic_id={$db->quote($thread)}");

		$cquery = new JDatabaseQuery();
		$cquery->select('u.id, 0 AS topic');
		$cquery->from('#__kunena_user_categories AS s');
		$cquery->join('INNER', '#__users AS u ON s.user_id=u.id');
		$cquery->where("s.topic_id={$db->quote($thread)}");

		// Get all allowed Joomla groups to make sure that subscription is valid
		$public = $this->_get_groups($access->pub_access, $access->pub_recurse);
		$admin = array();
		if ($access->pub_access > 0) {
			$admin = $this->_get_groups($access->admin_access, $access->admin_recurse);
		}
		$groups = array_unique ( array_merge ( $public, $admin ) );
		if ($groups) {
			$groups = implode ( ',', $groups );

			$tquery->join('INNER', "#__core_acl_aro AS a ON u.id=a.value AND section_value='users'");
			$tquery->join('INNER', "#__core_acl_groups_aro_map AS g ON g.aro_id=a.id");
			$tquery->where("g.group_id IN ({$groups})");

			$cquery->join('INNER', "#__core_acl_aro AS a ON u.id=a.value AND section_value='users'");
			$cquery->join('INNER', "#__core_acl_groups_aro_map AS g ON g.aro_id=a.id");
			$cquery->where("g.group_id IN ({$groups})");
		}

		$db->setQuery ("{$tquery} UNION {$cquery}");
		$userids = $db->loadObjectList('id');
		KunenaError::checkDatabaseError();
		return $userids;
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
	protected function acl_get_groups($sectionValue, $value, $rootGroupValue=NULL, $type='ARO')
	{
		// @todo More advanced caching to span session
		static $cache = null;

		$db		= JFactory::getDbo();
		$type	= strtolower($type);

		if ($type != 'aro' && $type != 'axo') {
			// @todo Throw an expection
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
			$query = new JDatabaseQuery();

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