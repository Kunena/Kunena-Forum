<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

require_once dirname(__FILE__) . '/integration.php';

/**
 * Kunena Access Control for CommunityBuilder
 * @since Kunena
 */
class KunenaAccessComprofiler
{
	/**
	 * @var bool
	 * @since Kunena
	 */
	protected $categories = false;

	/**
	 * @var bool
	 * @since Kunena
	 */
	protected $groups = false;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $tree = array();

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaAccessComprofiler constructor.
	 *
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * Get list of supported access types.
	 *
	 * List all access types you want to handle. All names must be less than 20 characters.
	 *
	 * @return array    Supported access types.
	 * @since Kunena
	 */
	public function getAccessTypes()
	{
		static $accesstypes = array('communitybuilder');

		$params = array('accesstypes' => &$accesstypes);

		KunenaIntegrationComprofiler::trigger('getAccessTypes', $params);

		return $accesstypes;
	}

	/**
	 * Get group name in selected access type.
	 *
	 * @param   string $accesstype Access type.
	 * @param   int    $id         Group id.
	 *
	 * @return string|null
	 * @since Kunena
	 */
	public function getGroupName($accesstype, $id = null)
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
		$params = array('accesstype' => $accesstype, 'id' => $id, 'name' => &$name);

		KunenaIntegrationComprofiler::trigger('getGroupName', $params);

		return $name;
	}

	/**
	 * Get HTML list of the available groups
	 *
	 * @param   string $accesstype Access type.
	 * @param   int    $category   Group id.
	 *
	 * @return array
	 * @since Kunena
	 */
	public function getAccessOptions($accesstype, $category)
	{
		$html = array();

		if (!$accesstype || $accesstype == 'communitybuilder')
		{
			$this->loadCategories();
			$this->loadGroups();
			$options  = array();
			$selected = 'communitybuilder' == $category->accesstype && isset($this->groups[$category->access]) ? $category->access : null;

			foreach ($this->tree as $item)
			{
				if (!$selected && is_numeric($item->id))
				{
					$selected = $item->id;
				}

				$options[] = JHtml::_('select.option', $item->id, str_repeat('- ', $item->level) . $item->name, 'value', 'text', !is_numeric($item->id));
			}

			if (!$options)
			{
				$selected  = 0;
				$options[] = JHtml::_('select.option', 0, JText::_('PLG_KUNENA_COMPROFILER_NO_GROUPS_FOUND'), 'value', 'text');
			}

			$html ['communitybuilder']['access'] = array(
				'title' => JText::_('PLG_KUNENA_COMPROFILER_ACCESS_GROUP_TITLE'),
				'desc'  => JText::_('PLG_KUNENA_COMPROFILER_ACCESS_GROUP_DESC'),
				'input' => JHtml::_('select.genericlist', $options, 'access-communitybuilder', 'class="inputbox" size="10"', 'value', 'text', $selected)
			);
		}

		$params = array('accesstype' => $accesstype, 'category' => $category, 'html' => &$html);

		KunenaIntegrationComprofiler::trigger('getAccessOptions', $params);

		return $html;
	}

	/**
	 * Load moderators and administrators for listed categories.
	 *
	 * This function is used to add category administrators and moderators to listed categories. In addition
	 * integration can also add global administrators / moderators (catid=0).
	 *
	 * Results may be cached.
	 *
	 * @param   array $categories List of categories, null = all.
	 *
	 * @return array(array => u, 'category_id'=>c, 'role'=>r))
	 * @since Kunena
	 */
	public function loadCategoryRoles(array $categories = null)
	{
		$roles  = array();
		$params = array('categories' => $categories, 'roles' => &$roles);
		KunenaIntegrationComprofiler::trigger('loadCategoryRoles', $params);

		return $roles;
	}

	/**
	 * Authorise user actions in a category.
	 *
	 * Function returns a list of authorised actions. Missing actions are threaded as inherit.
	 *
	 * @param   KunenaForumCategory $category
	 * @param   int                 $userid
	 *
	 * @return array
	 * @since Kunena
	 */
	public function getAuthoriseActions(KunenaForumCategory $category, $userid)
	{
		$actions = array();
		$params  = array('category' => $category, 'userid' => $userid, 'actions' => &$actions);

		KunenaIntegrationComprofiler::trigger('getAuthoriseActions', $params);

		return $actions;
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
	 * @since Kunena
	 */
	public function authoriseCategories($userid, array &$categories)
	{
		$allowed = '0';
		$params  = array($userid, &$allowed);
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
	 * @param   mixed $topic   Category or topic.
	 * @param   array $userids list(allow, deny).
	 *
	 * @return array
	 * @since Kunena
	 */
	public function authoriseUsers(KunenaDatabaseObject $topic, array &$userids)
	{
		$allow = $deny = array();

		if (empty($userids))
		{
			return array($allow, $deny);
		}

		$category = $topic->getCategory();
		$params   = array('category' => $category, 'topic' => $topic, 'userids' => $userids, 'allow' => &$allow, 'deny' => &$deny);

		KunenaIntegrationComprofiler::trigger('authoriseUsers', $params);

		return array($allow, $deny);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function loadCategories()
	{
		if ($this->categories === false)
		{
			$this->categories = array();
			$params           = array('categories' => &$this->categories, 'groups' => $this->groups);
			KunenaIntegrationComprofiler::trigger('loadCategories', $params);
			$this->tree = new KunenaTree($this->categories);

			if ($this->groups !== false)
			{
				$this->tree->add($this->groups);
			}
		}
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function loadGroups()
	{
		if ($this->groups === false)
		{
			$this->groups = array();
			$params       = array('groups' => &$this->groups, 'categories' => $this->categories);
			KunenaIntegrationComprofiler::trigger('loadGroups', $params);

			if ($this->categories !== false)
			{
				$this->tree->add($this->groups);
			}
		}
	}
}
