<?php
/**
 * @version $Id: access.php 4163 2011-01-07 10:45:09Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined( '_JEXEC' ) or die();

class KunenaAccessJoomla16 extends KunenaAccess {
	protected $viewLevels = false;

	public function __construct() {
		$jversion = new JVersion ();
		if ($jversion->RELEASE != '1.6')
			return null;
		$this->priority = 25;
	}

	public function loadAdmins() {
		jimport ( 'joomla.access.access' );
		$rules = JAccess::getAssetRules ( 'com_kunena', true );
		$data = $rules->getData ();
		$data = $data ['core.admin']->getData ();
		$rlist = array ();
		foreach ( $data as $groupid => $access ) {
			if ($access) {
				$rlist = array_merge ( $rlist, JAccess::getUsersByGroup ( $groupid, true ) );
			}
		}
		$rlist = array_unique ( $rlist );
		$list = array();
		foreach ( $rlist as $userid ) {
			$item = new StdClass();
			$item->userid = (int) $userid;
			$item->catid = 0;
			$list[] = $item;
		}
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

	public function loadAllowedCategories($user) {
		$user = JFactory::getUser($user);

		$accesslevels = (array) $user->authorisedLevels();
		$groups_r = JAccess::getGroupsByUser($user->id, true);
		$groups = JAccess::getGroupsByUser($user->id, false);

		$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
		$catlist = array();
		foreach ( $categories as $category ) {
			// Check if user is a moderator
			if (self::isModerator($user->id, $category->id)) {
				$catlist[$category->id] = $category->id;
			}
			// Check against Joomla access level
			elseif ($category->accesstype == 'joomla') {
				if ( in_array($category->access, $accesslevels) ) {
					$catlist[$category->id] = $category->id;
				}
			}
			// Check against Joomla user group
			elseif ($category->accesstype == 'none') {
				$pub_access = (($category->pub_recurse && in_array($category->pub_access, $groups_r)) || in_array($category->pub_access, $groups));
				$admin_access = (($category->admin_recurse && in_array($category->admin_access, $groups_r)) || in_array($category->admin_access, $groups));

				if (($category->pub_access == 0)
					|| ($category->pub_access == - 1 && $user->id > 0)
					|| ( $pub_access )
					|| ( $admin_access )) {
					$catlist[$category->id] = $category->id;
				}
			}
		}
		return $catlist;
	}

	public function checkSubscribers($topic, &$userids) {
		$category = $topic->getCategory();
		if (empty($userids) || $category->pub_access <= 0)
			return $userids;

		// FIXME: finish this
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

		// Get all allowed Joomla groups to make sure that subscription is valid
		$db = JFactory::getDBO ();
		$public = array ();
		$admin = array ();
		if ($category->pub_access > 0) {
			$public = $this->getUsersByGroup($category->pub_access, $category->pub_recurse, $userids);
		}
		if ($access->pub_access > 0 && $access->admin_access > 0) {
			$admin = $this->getUsersByGroup($category->admin_access, $category->admin_recurse, $userids);
		}
		$userids = implode ( ',', array_unique ( array_merge ( $public, $admin ) ) );
	}

	/**
	 * Method to return a list of groups which have view level (derived from Joomla 1.6)
	 *
	 * @param	integer	$userId	Id of the user for which to get the list of authorised view levels.
	 *
	 * @return	array	List of view levels for which the user is authorised.
	 * @since	1.6
	 */
	protected static function getGroupsByViewLevels($userId)
	{
		// Get all groups that the user is mapped to recursively.
		$groups = self::getGroupsByUser($userId);

		// Only load the view levels once.
		if (empty(self::$viewLevels)) {
			// Get a database object.
			$db	= JFactory::getDBO();

			// Build the base query.
			$query	= $db->getQuery(true);
			$query->select('id, rules');
			$query->from('`#__viewlevels`');

			// Set the query for execution.
			$db->setQuery((string) $query);

			// Build the view levels array.
			foreach ($db->loadAssocList() as $level) {
				self::$viewLevels[$level['id']] = (array) json_decode($level['rules']);
			}
		}

		// Initialise the authorised array.
		$authorised = array(1);

		// FIXME: make this to work

		// Find the authorized levels.
		foreach (self::$viewLevels as $level => $rule)
		{
			foreach ($rule as $id)
			{
				if (($id < 0) && (($id * -1) == $userId)) {
					$authorised[] = $level;
					break;
				}
				// Check to see if the group is mapped to the level.
				elseif (($id >= 0) && in_array($id, $groups)) {
					$authorised[] = $level;
					break;
				}
			}
		}

		return $authorised;
	}

	/**
	 * Method to return a list of user Ids contained in a Group (derived from Joomla 1.6)
	 *
	 * @param	int		$groupId	The group Id
	 * @param	boolean	$recursive	Recursively include all child groups (optional)
	 *
	 * @return	array
	 * @since	1.6
	 */
	protected static function getUsersByGroup($groupId, $recursive = false, $inUsers = array())
	{
		// Get a database object.
		$db = JFactory::getDbo();

		$test = $recursive ? '>=' : '=';

		$inUsers = implode(',', $inUsers);

		// First find the users contained in the group
		$query	= $db->getQuery(true);
		$query->select('DISTINCT(user_id)');
		$query->from('#__usergroups as ug1');
		$query->join('INNER','#__usergroups AS ug2 ON ug2.lft'.$test.'ug1.lft AND ug1.rgt'.$test.'ug2.rgt');
		$query->join('INNER','#__user_usergroup_map AS m ON ug2.id=m.group_id');
		$query->where('ug1.id ='.$db->Quote($groupId));
		$query->where("user_id IN ({$inUsers})");

		$db->setQuery($query);

		$result = $db->loadResultArray();

		// Clean up any NULL values, just in case
		JArrayHelper::toInteger($result);

		return $result;
	}
}