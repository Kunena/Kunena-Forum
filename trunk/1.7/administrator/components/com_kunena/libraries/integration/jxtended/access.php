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
			} elseif (($row->pub_access == 0)
				or ($row->pub_access == - 1 && $userid > 0)
				or ($row->pub_access > 0 && self::_has_rights ( $usergroups, $row->pub_access, $row->pub_recurse ))
				or ($row->admin_access > 0 && self::_has_rights ( $usergroups, $row->admin_access, $row->admin_recurse ))) {
				$catlist[$row->id] = 1;
			}
		}
		return $catlist;
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
		$query ="SELECT user_id FROM #__kunena_user_topics WHERE topic_id={$thread}
				UNION
				SELECT user_id FROM #__kunena_user_categories WHERE category_id={$catid}";
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

		$subslist = -1;
		$modlist = -1;
		$adminlist = -1;
		$arogroups = '';
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
			$subslist = $this->_get_subscribers($catid, $thread);
			$subslist = !empty($subslist) ? implode(',', array_keys($subslist)) : '-1';
		}
		if ($moderators) {
			if ($this->moderatorsByCatid === false) {
				$this->loadModerators();
			}
			if (!empty($this->moderatorsByCatid[0])) $modlist = $this->moderatorsByCatid[0];
			if (!empty($this->moderatorsByCatid[$catid])) $modlist += $this->moderatorsByCatid[$catid];
			$modlist = !empty($modlist) ? implode(',', array_keys($modlist)) : '-1';
		}
		if ($admins) {
			if ($this->adminsByCatid === false) {
				$this->loadAdmins();
			}
			if (!empty($this->adminsByCatid[0])) $adminlist = $this->adminsByCatid[0];
			if (!empty($this->adminsByCatid[$catid])) $adminlist += $this->adminsByCatid[$catid];
			$adminlist = !empty($adminlist) ? implode(',', array_keys($adminlist)) : '-1';
		}

		$querysel = "SELECT u.id, u.name, u.username, u.email,
					IF( u.id IN ({$subslist}), 1, 0 ) AS subscription,
					IF( u.id IN ({$modlist}), 1, 0 ) AS moderator,
					IF( u.id IN ({$adminlist}), 1, 0 ) AS admin
					FROM #__users AS u
					LEFT JOIN #__core_acl_aro AS a ON u.id=a.value AND section_value='users'
					LEFT JOIN #__core_acl_groups_aro_map AS g ON g.aro_id=a.id";

		$where = array ();
		if ($subscriptions)
			$where [] = " ( u.id IN ({$subslist}) ) " . ($arogroups ? " AND {$arogroups}" : '') . " ) ";
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

		unset($subslist, $modlist, $adminlist);
		return $subsList;
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
		if ($cache == null) {
			$cache = array();
		}

		// Generate unique cache id.
		$cacheId = 'acl_get_groups_'.$sectionValue.'-'.$value.'-'.$rootGroupValue.'-'.$type;

		if (!isset($cache[$cacheId]))
		{
			if (!class_exists('JDatabaseQuery')) {
				kimport('joomla.database.databasequery');
			}

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
			//echo $db->getQuery();
			$cache[$cacheId] = $db->loadResultArray();
		}

		return $cache[$cacheId];
	}
}