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

	protected function _get_subscribers($catid, $thread) {
		// Get all allowed Joomla groups to make sure that subscription is valid
		$db = JFactory::getDBO ();
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
		// FIXME: doesn't work
		if ($groups)
			$groups = "u.gid IN ({$groups})";

		$query ="SELECT u.id, 1 AS topic
					FROM #__kunena_user_topics AS s
					INNER JOIN #__users AS u ON s.user_id=u.id
					WHERE s.topic_id={$thread} {$groups}
				UNION
				SELECT u.id, 0 AS topic
					FROM #__kunena_user_categories AS s
					INNER JOIN #__users AS u ON s.user_id=u.id
					WHERE s.category_id={$catid} {$groups}";
		$db->setQuery ($query);
		$userids = $db->loadObjectList('id');
		KunenaError::checkDatabaseError();
		return $userids;
	}
}
