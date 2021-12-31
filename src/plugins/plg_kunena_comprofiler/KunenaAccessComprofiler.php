<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Tree\KunenaTree;

require_once __DIR__ . '/KunenaIntegrationComprofiler.php';

/**
 * Kunena Access Control for CommunityBuilder
 *
 * @since   Kunena 6.0
 */
class KunenaAccessComprofiler
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
	 * KunenaAccessComprofiler constructor.
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
	 *
	 * @return  array    Supported access types.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getAccessTypes(): array
	{
		static $accesstypes = ['communitybuilder'];

		$params = ['accesstypes' => &$accesstypes];

		KunenaIntegrationComprofiler::trigger('getAccessTypes', $params);

		return $accesstypes;
	}

	/**
	 * Get group name in selected access type.
	 *
	 * @param   string  $accesstype  Access type.
	 * @param   null    $id          Group id.
	 *
	 * @return  boolean|integer|null
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function getGroupName(string $accesstype, $id = null)
	{
		if ($accesstype == 'communitybuilder')
		{
			$this->loadGroups();

			if ($id !== null)
			{
				return isset($this->groups[$id]) ? $this->groups[$id]->name : $id;
			}

			return $this->groups;
		}

		$name   = null;
		$params = ['accesstype' => $accesstype, 'id' => $id, 'name' => &$name];

		KunenaIntegrationComprofiler::trigger('getGroupName', $params);

		return $name;
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
			$this->groups = [];
			$params       = ['groups' => &$this->groups, 'categories' => $this->categories];
			KunenaIntegrationComprofiler::trigger('loadGroups', $params);

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

		if (!$accesstype || $accesstype == 'communitybuilder')
		{
			$this->loadCategories();
			$this->loadGroups();
			$options  = [];
			$selected = 'communitybuilder' == $category->accesstype && isset($this->groups[$category->access]) ? $category->access : null;

			foreach ($this->tree as $item)
			{
				if (!$selected && is_numeric($item->id))
				{
					$selected = $item->id;
				}

				$options[] = HTMLHelper::_('select.option', $item->id, str_repeat('- ', $item->level) . $item->name, 'value', 'text', !is_numeric($item->id));
			}

			if (!$options)
			{
				$selected  = 0;
				$options[] = HTMLHelper::_('select.option', 0, Text::_('PLG_KUNENA_COMPROFILER_NO_GROUPS_FOUND'), 'value', 'text');
			}

			$html ['communitybuilder']['access'] = [
				'title' => Text::_('PLG_KUNENA_COMPROFILER_ACCESS_GROUP_TITLE'),
				'desc'  => Text::_('PLG_KUNENA_COMPROFILER_ACCESS_GROUP_DESC'),
				'input' => HTMLHelper::_('select.genericlist', $options, 'access-communitybuilder', 'class="inputbox form-control" size="10"', 'value', 'text', $selected),
			];
		}

		$params = ['accesstype' => $accesstype, 'category' => $category, 'html' => &$html];

		KunenaIntegrationComprofiler::trigger('getAccessOptions', $params);

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
			$this->categories = [];
			$params           = ['categories' => &$this->categories, 'groups' => $this->groups];
			KunenaIntegrationComprofiler::trigger('loadCategories', $params);
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
	 * integration can also add global administrators / moderators (catid=0).
	 *
	 * Results may be cached.
	 *
	 * @param   array|null  $categories  List of categories, null = all.
	 *
	 * @return  array(array => u, 'category_id'=>c, 'role'=>r))
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function loadCategoryRoles(array $categories = null): array
	{
		$roles  = [];
		$params = ['categories' => $categories, 'roles' => &$roles];
		KunenaIntegrationComprofiler::trigger('loadCategoryRoles', $params);

		return $roles;
	}

	/**
	 * Authorise user actions in a category.
	 *
	 * Function returns a list of authorised actions. Missing actions are threaded as inherit.
	 *
	 * @param   KunenaCategory  $category  category
	 * @param   int             $userid    userid
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function getAuthoriseActions(KunenaCategory $category, int $userid): array
	{
		$actions = [];
		$params  = ['category' => $category, 'userid' => $userid, 'actions' => &$actions];

		KunenaIntegrationComprofiler::trigger('getAuthoriseActions', $params);

		return $actions;
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
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function authoriseCategories(int $userid, array &$categories): array
	{
		$allowed = '0';
		$params  = [$userid, &$allowed];
		KunenaIntegrationComprofiler::trigger('getAllowedForumsRead', $params);

		if (is_string($allowed))
		{
			$allowed = explode(',', $allowed);
		}

		$allowed = (array) array_flip($allowed);

		foreach ($allowed as $id => &$value)
		{
			$value = $id;
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
		$allow = $deny = [];

		if (empty($userids))
		{
			return [$allow, $deny];
		}

		$category = $topic->getCategory();
		$params   = ['category' => $category, 'topic' => $topic, 'userids' => $userids, 'allow' => &$allow, 'deny' => &$deny];

		KunenaIntegrationComprofiler::trigger('authoriseUsers', $params);

		return [$allow, $deny];
	}
}
