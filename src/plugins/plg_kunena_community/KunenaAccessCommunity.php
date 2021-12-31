<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Community
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Tree\KunenaTree;

/**
 * Class KunenaAccessCommunity
 *
 * @since   Kunena 6.0
 */
class KunenaAccessCommunity
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $categories = false;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected $groups = false;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $tree = [];

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaAccessCommunity constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * Get list of supported access types.
	 *
	 * List all access types you want to handle. All names must be less than 20 characters.
	 * Examples: joomla.level, mycomponent.groups, mycomponent.vipusers
	 *
	 * @return  array    Supported access types.
	 * @since   Kunena 6.0
	 */
	public function getAccessTypes()
	{
		return ['jomsocial'];
	}

	/**
	 * Get group name in selected access type.
	 *
	 * @param   string  $accesstype  Access type.
	 * @param   null    $id          Group id.
	 *
	 * @return  boolean|void|string
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function getGroupName(string $accesstype, $id = null)
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

		return;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function loadGroups(): void
	{
		if ($this->groups === false)
		{
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select('id, CONCAT(\'c\', categoryid) AS parentid, name')
				->update($db->quoteName('#__community_groups'))
				->order('categoryid, name');
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

	/**
	 * Get HTML list of the available groups
	 *
	 * @param   string|null       $accesstype  Access type.
	 * @param   KunenaCategory    $category    Group id.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function getAccessOptions($accesstype, KunenaCategory $category): array
	{
		$html = [];

		if (!$accesstype || $accesstype == 'jomsocial')
		{
			$this->loadCategories();
			$this->loadGroups();
			$options  = [];
			$selected = 'jomsocial' == $category->accesstype && isset($this->groups[$category->access]) ? $category->access : null;

			foreach ($this->tree as $item)
			{
				if (!$selected && is_numeric($item->id))
				{
					$selected = $item->id;
				}

				$options[] = HTMLHelper::_('select.option', $item->id, str_repeat('- ', $item->level) . $item->name, 'value', 'text', !is_numeric($item->id));
			}

			$html ['jomsocial']['access'] = [
				'title' => Text::_('PLG_KUNENA_COMMUNITY_ACCESS_GROUP_TITLE'),
				'desc'  => Text::_('PLG_KUNENA_COMMUNITY_ACCESS_GROUP_DESC'),
				'input' => HTMLHelper::_('select.genericlist', $options, 'access-jomsocial', 'class="inputbox form-control" size="10"', 'value', 'text', $selected),
			];
		}

		return $html;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function loadCategories(): void
	{
		if ($this->categories === false)
		{
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select('SELECT CONCAT(\'c\', id) AS id, CONCAT(\'c\', parent) AS parentid, name')
				->update($db->quoteName('#__community_groups_category'))
				->order('parent, name');
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
	 * Load moderators and administrators for listed categories.
	 *
	 * This function is used to add category administrators and moderators to listed categories. In addition
	 * integration can also add global administrators (catid=0).
	 *
	 * Results may be cached.
	 *
	 * @param   array|null  $categories  List of categories, null = all.
	 *
	 * @return  array(array => u, 'category_id'=>c, 'role'=>r))
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function loadCategoryRoles(array $categories = null): array
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('g.memberid AS user_id, c.id AS category_id, ' . KunenaForum::ADMINISTRATOR . ' AS role')
			->from($db->quoteName('#__kunena_categories', 'c'))
			->innerJoin($db->quoteName('#__community_groups_members', 'g') . ' ON c.accesstype=\'jomsocial\' AND c.access = g.groupid')
			->where('c.published = 1')
			->andWhere('g.approved = 1')
			->andWhere('g.permissions = ' . $db->quote(COMMUNITY_GROUP_ADMIN));
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
	 * Function accepts array of id indexed \Kunena\Forum\Libraries\Forum\Category\Category objects and removes
	 * unauthorised categories from the list.
	 *
	 * Results for the current user are saved into session.
	 *
	 * @param   int    $userid      User who needs the authorisation (null=current user, 0=visitor).
	 * @param   array  $categories  List of categories in access type.
	 *
	 * @return  array, where category ids are in the keys.
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function authoriseCategories(int $userid, array &$categories): array
	{
		$allowed = [];

		if (KunenaFactory::getUser($userid)->exists())
		{
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select('c.id')
				->from($db->quoteName('#__kunena_categories', 'c'))
				->innerJoin($db->quoteName('#__community_groups_members', 'g') . ' ON c.accesstype = \'jomsocial\' AND c.access = g.groupid')
				->where('c.published = 1')
				->andWhere('g.approved = 1')
				->andWhere('g.memberid = ' . $db->quote((int) $userid));
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
	 * @param   mixed  $topic    Category or topic.
	 * @param   array  $userids  list(allow, deny).
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function authoriseUsers(KunenaDatabaseObject $topic, array $userids): array
	{
		if (empty($userids))
		{
			return [[], []];
		}

		$category = $topic->getCategory();
		$userlist = implode(',', $userids);

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('c.id')
			->from($db->quoteName('#__kunena_categories', 'c'))
			->innerJoin($db->quoteName('#__community_groups_members', 'g') . ' ON c.accesstype = \'jomsocial\' AND c.access = g.groupid')
			->where('c.id = ' . $db->quote((int) $category->id))
			->andWhere(' g.approved = 1')
			->andWhere('g.memberid IN (' . $userlist . ')');
		$db->setQuery($query);

		try
		{
			$allow = (array) $db->loadColumn();
			$deny  = [];
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return [$allow, $deny];
	}
}
