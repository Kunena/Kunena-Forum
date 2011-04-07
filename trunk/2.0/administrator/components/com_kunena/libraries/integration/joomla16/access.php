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

jimport ( 'joomla.access.access' );

class KunenaAccessJoomla16 extends KunenaAccess {
	protected $viewLevels = false;

	public function __construct() {
		$jversion = new JVersion ();
		if ($jversion->RELEASE != '1.6')
			return null;
		$this->priority = 25;
	}

	public function loadAdmins() {
		$admins = array_merge($this->getAuthorisedUsers('core.admin', 'com_kunena'), $this->getAuthorisedUsers('core.manage', 'com_kunena'));
		$list = array();
		foreach ( $admins as $userid ) {
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

	public function loadAllowedCategories($user, &$categories) {
		$user = JFactory::getUser($user);

		$accesslevels = (array) $user->authorisedLevels();
		$groups_r = JAccess::getGroupsByUser($user->id, true);
		$groups = JAccess::getGroupsByUser($user->id, false);

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
			return;

		$userlist = implode(',', $userids);

		if ($category->accesstype == 'joomla') {
			// TODO: Check against Joomla access level
		} elseif ($category->accesstype == 'none') {
			// Check against Joomla user groups
			$public = $this->getUsersByGroup($category->pub_access, $category->pub_recurse, $userids);
			$admin = $category->pub_access > 0 && $category->admin_access ? $this->getUsersByGroup($category->admin_access, $category->admin_recurse, $userids) : array();
			$userids = array_unique ( array_merge ( $public, $admin ) );
		} else {
			return;
		}
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
	 * @since	Joomla 1.6
	 */
	protected static function getUsersByGroup($groupId, $recursive = false, $inUsers = array())
	{
		// Get a database object.
		$db = JFactory::getDbo();

		$test = $recursive ? '>=' : '=';

		if (empty($groupId)) {
			return array();
		}
		if (is_array($groupId)) {
			$groupId = implode(',', $groupId);
		}
		$inUsers = implode(',', $inUsers);

		// First find the users contained in the group
		$query	= $db->getQuery(true);
		$query->select('DISTINCT(user_id)');
		$query->from('#__usergroups as ug1');
		$query->join('INNER','#__usergroups AS ug2 ON ug2.lft'.$test.'ug1.lft AND ug1.rgt'.$test.'ug2.rgt');
		$query->join('INNER','#__user_usergroup_map AS m ON ug2.id=m.group_id');
		$query->where("ug1.id IN ({$groupId})");
		if ($inUsers) $query->where("user_id IN ({$inUsers})");

		$db->setQuery($query);

		$result = (array) $db->loadResultArray();

		// Clean up any NULL values, just in case
		JArrayHelper::toInteger($result);

		return $result;
	}

	protected function getAuthorisedUsers($action, $asset = null) {
		$action = strtolower(preg_replace('#[\s\-]+#', '.', trim($action)));
		$asset  = strtolower(preg_replace('#[\s\-]+#', '.', trim($asset)));

		// Default to the root asset node.
		if (empty($asset)) {
			$asset = 1;
		}

		// Get all asset rules
		$rules = JAccess::getAssetRules ( $asset, true );
		$data = $rules->getData ();

		// Get all action rules for the asset
		$groups = array ();
		if (!empty($data [$action])) {
			$groups = $data [$action]->getData ();
		}

		// Split groups into allow and deny list
		$allow = array ();
		$deny = array ();
		foreach ( $groups as $groupid => $access ) {
			if ($access) {
				$allow[] = $groupid;
			} else {
				$deny[] = $groupid;
			}
		}

		// Get userids
		if ($allow) {
			// These users can do the action
			$allow = $this->getUsersByGroup ( $allow, true );
		}
		if ($deny) {
			// But these users have explicit deny for the action
			$deny = $this->getUsersByGroup ( $deny, true );
		}

		// Remove denied users from allowed users list
		return array_diff ( $allow, $deny );
	}
}