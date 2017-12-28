<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Community
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

class KunenaAccessCommunity
{
	protected $categories = false;

	protected $groups = false;

	protected $tree = array();

	protected $params = null;

	/**
	 * KunenaAccessCommunity constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * Get list of supported access types.
	 *
	 * List all access types you want to handle. All names must be less than 20 characters.
	 * Examples: joomla.level, mycomponent.groups, mycomponent.vipusers
	 *
	 * @return array    Supported access types.
	 */
	public function getAccessTypes()
	{
		static $accesstypes = array('jomsocial');

		return $accesstypes;
	}

	/**
	 * Get group name in selected access type.
	 *
	 * @param   string $accesstype Access type.
	 * @param   int    $id         Group id.
	 *
	 * @return string|null
	 */
	public function getGroupName($accesstype, $id = null)
	{
		if ($accesstype == 'jomsocial')
		{
			$this->loadGroups();

			if ($id !== null)
			{
				return isset($this->groups[$id]) ? $this->groups[$id]->name : '';
			}

			return $this->groups;
		}

		return null;
	}

	/**
	 * Get HTML list of the available groups
	 *
	 * @param   string $accesstype Access type.
	 * @param   int    $category   Group id.
	 *
	 * @return array
	 */
	public function getAccessOptions($accesstype, $category)
	{
		$html = array();

		if (!$accesstype || $accesstype == 'jomsocial')
		{
			$this->loadCategories();
			$this->loadGroups();
			$options  = array();
			$selected = 'jomsocial' == $category->accesstype && isset($this->groups[$category->access]) ? $category->access : null;

			foreach ($this->tree as $item)
			{
				if (!$selected && is_numeric($item->id))
				{
					$selected = $item->id;
				}

				$options[] = JHtml::_('select.option', $item->id, str_repeat('- ', $item->level) . $item->name, 'value', 'text', !is_numeric($item->id));
			}

			$html ['jomsocial']['access'] = array(
				'title' => JText::_('PLG_KUNENA_COMMUNITY_ACCESS_GROUP_TITLE'),
				'desc'  => JText::_('PLG_KUNENA_COMMUNITY_ACCESS_GROUP_DESC'),
				'input' => JHtml::_('select.genericlist', $options, 'access-jomsocial', 'class="inputbox" size="10"', 'value', 'text', $selected)
			);
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
	 * @param   array $categories List of categories, null = all.
	 *
	 * @return array(array => u, 'category_id'=>c, 'role'=>r))
	 */
	public function loadCategoryRoles(array $categories = null)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT g.memberid AS user_id, c.id AS category_id, " . KunenaForum::ADMINISTRATOR . " AS role
			FROM #__kunena_categories AS c
			INNER JOIN #__community_groups_members AS g ON c.accesstype='jomsocial' AND c.access=g.groupid
			WHERE c.published=1 AND g.approved=1 AND g.permissions={$db->Quote( COMMUNITY_GROUP_ADMIN )}";
		$db->setQuery($query);

		try
		{
			$list = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $list;
	}

	/**
	 * Authorise list of categories.
	 *
	 * Function accepts array of id indexed KunenaForumCategory objects and removes unauthorised
	 * categories from the list.
	 *
	 * Results for the current user are saved into session.
	 *
	 * @param   int   $userid     User who needs the authorisation (null=current user, 0=visitor).
	 * @param   array $categories List of categories in access type.
	 *
	 * @return array, where category ids are in the keys.
	 */
	public function authoriseCategories($userid, array &$categories)
	{
		$allowed = array();

		if (KunenaFactory::getUser($userid)->exists())
		{
			$db    = JFactory::getDBO();
			$query = "SELECT c.id FROM #__kunena_categories AS c
				INNER JOIN #__community_groups_members AS g ON c.accesstype='jomsocial' AND c.access=g.groupid
				WHERE c.published=1 AND g.approved=1 AND g.memberid={$db->quote($userid)}";
			$db->setQuery($query);

			try
			{
				$list = (array) $db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			foreach ($list as $catid)
			{
				$allowed [$catid] = $catid;
			}
		}

		return $allowed;
	}

	/**
	 * Authorise list of userids to topic or category.
	 *
	 * @param   mixed $topic   Category or topic.
	 * @param   array $userids list(allow, deny).
	 *
	 * @return array
	 */
	public function authoriseUsers(KunenaDatabaseObject $topic, array &$userids)
	{
		if (empty($userids))
		{
			return array(array(), array());
		}

		$category = $topic->getCategory();
		$userlist = implode(',', $userids);

		$db    = JFactory::getDBO();
		$query = "SELECT c.id FROM #__kunena_categories AS c
			INNER JOIN #__community_groups_members AS g ON c.accesstype='jomsocial' AND c.access=g.groupid
			WHERE c.id={$category->id} AND g.approved=1 AND g.memberid IN ({$userlist})";
		$db->setQuery($query);

		try
		{
			$allow = (array) $db->loadColumn();
			$deny  = array();
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return array($allow, $deny);
	}

	/**
	 *
	 */
	protected function loadCategories()
	{
		if ($this->categories === false)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT CONCAT('c', id) AS id, CONCAT('c', parent) AS parent_id, name
				FROM #__community_groups_category
				ORDER BY parent, name";
			$db->setQuery($query);

			try
			{
				$this->categories = (array) $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			$this->tree = new KunenaTree($this->categories);

			if ($this->groups !== false)
			{
				$this->tree->add($this->groups);
			}
		}
	}

	/**
	 *
	 */
	protected function loadGroups()
	{
		if ($this->groups === false)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT id, CONCAT('c', categoryid) AS parent_id, name
				FROM #__community_groups
				ORDER BY categoryid, name";
			$db->setQuery($query);

			try
			{
				$this->groups = (array) $db->loadObjectList('id');
			}
			catch (RuntimeException $e)
			{
				KunenaError::displayDatabaseError($e);
			}

			if ($this->categories !== false)
			{
				$this->tree->add($this->groups);
			}
		}
	}
}
