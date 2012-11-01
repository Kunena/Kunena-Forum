<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Joomla15
 *
 * @Copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Access Control for Joomla 1.5
 */
class KunenaAccessJoomla {
	protected $params = null;

	public function __construct($params) {
		$this->params = $params;
	}

	/**
	 * Get list of supported access types.
	 *
	 * List all access types you want to handle. All names must be less than 20 characters.
	 * Examples: joomla.level, mycomponent.groups, mycomponent.vipusers
	 *
	 * @return array	Supported access types.
	 */
	public function getAccessTypes() {
		static $accesstypes = array('joomla.level', 'joomla.group');
		return $accesstypes;
	}

	/**
	 * Get group name in selected access type.
	 *
	 * @param string	Access type.
	 * @param int		Group id.
	 */
	public function getGroupName($accesstype, $id=null){
		static $groups = false;
		if ($accesstype == 'joomla.group') {
			return $id > 1 ? JFactory::getACL ()->get_group_name($id) : '';
		} elseif ($accesstype == 'joomla.level') {
			if ($groups === false) {
				$db = JFactory::getDBO ();
				$query = "SELECT * FROM #__groups";
				$db->setQuery ($query);
				$groups = (array) $db->loadObjectList('id');
			}
			if ($id !== null) {
				return isset($groups[$id]) ? JText::_($groups[$id]->name) : '';
			}
			return $groups;
		}
	}

	/**
	 * Get HTML list of the available groups
	 *
	 * @param string	Access type.
	 * @param int		Group id.
	 */
	public function getAccessOptions($accesstype, $category) {

		if (!$accesstype || $accesstype == 'joomla.level') {
			$object = new StdClass();
			$object->access = 'joomla.level' == $category->accesstype ? $category->access : 0;
			$html ['joomla-level']['access'] = array(
				'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_LEVEL_TITLE'),
				'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_LEVEL_DESC') .'<br /><br />'. JText::_('PLG_KUNENA_JOOMLA_ACCESS_LEVEL_DESC_J15'),
				'input' => JHTML::_('list.accesslevel', $object)
			);
			if (!$category->isSection()) {
				$joomlagroups = $this->_getJoomlaGroups();
				$html ['joomla-level']['post'] = array(
					'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUPS_POST_TITLE'),
					'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUPS_POST_DESC'),
					'input' => JHtml::_('select.genericlist', $joomlagroups, 'params-joomla-level[access_post][]', 'multiple="multiple" class="inputbox" size="9"', 'value', 'text', $category->params->getValue('access_post', array(18,30)))
				);
				$html ['joomla-level']['reply'] = array(
					'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUPS_REPLY_TITLE'),
					'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUPS_REPLY_DESC'),
					'input' => JHtml::_('select.genericlist', $joomlagroups, 'params-joomla-level[access_reply][]', 'multiple="multiple" class="inputbox" size="9"', 'value', 'text', $category->params->getValue('access_reply', array(18,30)))
				);
			}
		}
		if (!$accesstype || $accesstype == 'joomla.group') {
			$yesno = array ();
			$yesno [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_NO' ) );
			$yesno [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_YES' ) );

			$pub_groups = array ();
			$pub_groups [] = JHTML::_ ( 'select.option', 1, JText::_('COM_KUNENA_NOBODY') );
			$pub_groups [] = JHTML::_ ( 'select.option', 0, JText::_('COM_KUNENA_PUBLIC') );
			$pub_groups [] = JHTML::_ ( 'select.option', - 1, JText::_('COM_KUNENA_ALLREGISTERED') );
			$adm_groups = array ();
			$adm_groups [] = JHTML::_ ( 'select.option', 0, JText::_('COM_KUNENA_NOBODY') );

			$joomlagroups = $this->_getJoomlaGroups();
			$pub_groups = array_merge ( $pub_groups, $joomlagroups );
			$adm_groups = array_merge ( $adm_groups, $joomlagroups );

			$html ['joomla-group']['pub_access'] = array(
				'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_PRIMARY_TITLE'),
				'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_PRIMARY_DESC') .'<br /><br />'.
						JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_PRIMARY_DESC2') .'<br /><br />'.
						JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_PRIMARY_DESC_J15'),
				'input' => JHTML::_ ( 'select.genericlist', $pub_groups, 'pub_access', 'class="inputbox" size="10"', 'value', 'text', $category->pub_access )
			);
			$html ['joomla-group']['pub_recurse'] = array(
				'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_PRIMARY_CHILDS_TITLE'),
				'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_PRIMARY_CHILDS_DESC'),
				'input' => JHTML::_ ( 'select.genericlist', $yesno, 'pub_recurse', 'class="inputbox" size="1"', 'value', 'text', $category->pub_recurse )
			);
			$html ['joomla-group']['admin_access'] = array(
				'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_SECONDARY_TITLE'),
				'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_SECONDARY_DESC') .'<br /><br />'.
						JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_SECONDARY_DESC2') .'<br /><br />'.
						JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_SECONDARY_DESC_J15'),
				'input' => JHTML::_ ( 'select.genericlist', $adm_groups, 'admin_access', 'class="inputbox" size="10"', 'value', 'text', $category->admin_access )
			);
			$html ['joomla-group']['admin_recurse'] = array(
				'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_SECONDARY_CHILDS_TITLE'),
				'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUP_SECONDARY_CHILDS_DESC'),
				'input' => JHTML::_ ( 'select.genericlist', $yesno, 'admin_recurse', 'class="inputbox" size="1"', 'value', 'text', $category->admin_recurse )
			);
			if (!$category->isSection()) {
				$html ['joomla-group']['post'] = array(
						'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUPS_POST_TITLE'),
						'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUPS_POST_DESC'),
						'input' => JHtml::_('select.genericlist', $joomlagroups, 'params-joomla-group[access_post][]', 'multiple="multiple" class="inputbox" size="9"', 'value', 'text', $category->params->getValue('access_post', array(18,30)))
				);
				$html ['joomla-group']['reply'] = array(
						'title' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUPS_REPLY_TITLE'),
						'desc' => JText::_('PLG_KUNENA_JOOMLA_ACCESS_GROUPS_REPLY_DESC'),
						'input' => JHtml::_('select.genericlist', $joomlagroups, 'params-joomla-group[access_reply][]', 'multiple="multiple" class="inputbox" size="9"', 'value', 'text', $category->params->getValue('access_reply', array(18,30)))
				);
			}
		}
		return $html;
	}

	/**
	 * Load moderators and administrators for listed categories.
	 *
	 * This function is used to add category administrators and moderators to listed categories. In addition
	 * integration can also add global administrators (catid=0).
	 *
	 * Results may be cached.
	 *
	 * @param array $categories		List of categories, null = all.
	 *
	 * @return array of (catid=>userid)
	 */
	public function loadCategoryRoles(array $categories = null) {
		$db = JFactory::getDBO ();
		$query = "SELECT u.id AS user_id, 0 AS category_id, ".KunenaForum::ADMINISTRATOR." AS role
			FROM #__users AS u
			WHERE u.block='0' AND u.usertype IN ('Administrator', 'Super Administrator')";
		$db->setQuery ( $query );
		$list = (array) $db->loadObjectList ();
		KunenaError::checkDatabaseError ();
		return $list;
	}

	/**
	 * Authorise user actions in a category.
	 *
	 * Function returns a list of authorized actions. Missing actions are threaded as inherit.
	 *
	 * @param KunenaForumCategory $category
	 * @param int $userid
	 *
	 * @return array
	 */
	public function getAuthoriseActions(KunenaForumCategory $category, $userid) {
		$usergroups = $this->_get_user_groups($userid);
		$postg = (array) $category->params->getValue('access_post', array(18,30));
		$replyg = (array) $category->params->getValue('access_reply', array(18,30));
		foreach ($postg as $group) {
			$post = (bool) $this->_has_rights($usergroups, $group, true);
			if ($post) break;
		}
		foreach ($replyg as $group) {
			$reply = (bool) $this->_has_rights($usergroups, $group, true);
			if ($reply) break;
		}
		return array ('topic.create'=>$post, 'topic.reply'=>$reply, 'topic.post.reply'=>$reply);
	}

	/**
	 * Authorise list of categories.
	 *
	 * Function accepts array of id indexed KunenaForumCategory objects and removes unauthorised
	 * categories from the list.
	 *
	 * Results for the current user are saved into session.
	 *
	 * @param int $userid			User who needs the authorisation (null=current user, 0=visitor).
	 * @param array $categories		List of categories in access type.
	 *
	 * @return array, where category ids are in the keys.
	 */
	public function authoriseCategories($userid, array &$categories) {
		$user = JFactory::getUser($userid);

		// Workaround for missing aid
		$my = JFactory::getUser();
		if ($user->id == $my->id) {
			// Current user
			$user = $my;
		} elseif ($user->id) {
			// Other users
			$aid->aid = 1 ;
			$acl = JFactory::getACL();
			$grp = $acl->getAroGroup($user->id);
			if ($acl->is_group_child_of($grp->name, 'Registered') ||  $acl->is_group_child_of($grp->name, 'Public Backend')) {
				$user->aid = 2 ;
			}
		}

		// Get all Joomla user groups for current user
		$usergroups = $this->_get_user_groups($user->id);

		$catlist = array();
		foreach ( $categories as $category ) {
			// Check against Joomla access level
			if ($category->accesstype == 'joomla.level') {
				// 0 = Public, 1 = Registered, 2 = Special
				if ( $category->access <= $user->get('aid') ) {
					$catlist[$category->id] = $category->id;
				}
			}
			// Check against Joomla user group
			elseif ($category->accesstype == 'joomla.group') {
				// pub_access: 0 = Public, -1 = All registered, 1 = Nobody, >1 = Group ID
				// admin_access: 0 = Nobody, >1 = Group ID
				if ($category->pub_access == 0
					|| ($user->id > 0 && $category->pub_access == - 1)
					|| ($category->pub_access > 1 && $this->_has_rights ( $usergroups, $category->pub_access, $category->pub_recurse ))
					|| ($category->pub_access > 0 && $category->admin_access > 0 && $category->admin_access != $category->pub_access && $this->_has_rights ( $usergroups, $category->admin_access, $category->admin_recurse ))
				) {
					$catlist[$category->id] = $category->id;
				}
			}
		}
		return $catlist;
	}

	/**
	 * Authorise list of userids to topic or category.
	 *
	 * @param	mixed	Category or topic.
	 * @param	array	list(allow, deny).
	 */
	public function authoriseUsers(KunenaDatabaseObject $topic, array &$userids) {
		if (empty($userids)) {
			return;
		}

		$userlist = implode(',', $userids);

		$db = JFactory::getDBO ();
		$query = new KunenaDatabaseQuery();
		$query->select('u.id');
		$query->from('#__users AS u');
		$query->where("u.block=0");
		$query->where("u.id IN ({$userlist})");

		$category = $topic->getCategory();
		if ($category->accesstype == 'joomla.level') {
			// Check against Joomla access level
			if ( $category->access > 1 ) {
				// Special users: not in registered group
				$query->where("u.gid!=18");
			}
		} elseif ($category->accesstype == 'joomla.group') {
			// All users are allowed to see Public (0) or All Registered (-1) categories
			if ($category->pub_access <= 0) return array($userids, array());
			// Check against Joomla user groups
			$public = $this->_get_groups($category->pub_access, $category->pub_recurse);
			// Ignore admin_access if pub_access has the same group
			$admin = $category->admin_access != $category->pub_access ? $this->_get_groups($category->admin_access, $category->admin_recurse) : array();
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
		$allow = (array) $db->loadResultArray();
		$deny = array();
		KunenaError::checkDatabaseError();

		return array($allow, $deny);
	}

	// Internal functions

	protected function _has_rights($usergroups, $groupid, $recurse) {
		// Check the group itself
		if (in_array($groupid, $usergroups))
			return 1;
		// Check the children
		if ($usergroups && $recurse) {
			$childs = $this->_get_groups($groupid, $recurse);
			if (array_intersect($childs, $usergroups))
				return 1;
		}
		return 0;
	}

	protected function _get_groups($groupid, $recurse) {
		static $groups = false;

		// Public and All Registered: Allow all users
		if ($groupid <= 0) return array();
		// If no recursion is needed, just return the group ID
		if (!$recurse) return array($groupid);
		// Otherwise return the group and all its children
		if ($groups === false) {
			// Cache results
			$result = JFactory::getACL ()->_getBelow( '#__core_acl_aro_groups', 'g1.id, g2.id AS parent', null, null, null, null );
			$groups = array();
			foreach ($result as $group) {
				$groups[$group->parent][] = $group->id;
			}
		}
		return isset($groups[$groupid]) ? $groups[$groupid] : array();
	}

	protected function _get_user_groups($userid) {
		static $cache = array();

		$userid = intval($userid);
		if (!$userid) return array(29);

		if (!isset($cache[$userid])) {
			$db = JFactory::getDbo();

			$query = new KunenaDatabaseQuery();
			$query->select('g.id');
			$query->from('#__core_acl_aro AS o');
			$query->join('INNER', '#__core_acl_groups_aro_map AS gm ON gm.aro_id=o.id');
			$query->join('INNER', '#__core_acl_aro_groups AS g ON g.id = gm.group_id');
			$query->where("(o.section_value='users' AND o.value=". $db->quote($userid) .')');

			$db->setQuery($query);
			$cache[$userid] = $db->loadResultArray();
		}
		return $cache[$userid];
	}

	protected function _getJoomlaGroups() {
		static $joomlagroups = null;

		if (!isset($joomlagroups)) {
			$acl = JFactory::getACL ();
			$joomlagroups = $acl->get_group_children_tree ( null, 'USERS', false );
			foreach ($joomlagroups as &$group) {
				$group->text = preg_replace('/(^&nbsp; |\.&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)/', '- ', $group->text);
			}
		}
		return $joomlagroups;
	}
}
