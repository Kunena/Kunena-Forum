<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

/**
 * Categories Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelCategories extends KunenaModel
{
	/**
	 * @var
	 * @since Kunena
	 */
	public $context;

	/**
	 * @var KunenaForumCategory[]
	 * @since Kunena
	 */
	protected $_admincategories = false;

	/**
	 * @var KunenaForumCategory
	 * @since Kunena
	 */
	protected $_admincategory = false;

	/**
	 * @return \Joomla\CMS\Pagination\Pagination
	 * @since Kunena
	 */
	public function getAdminNavigation()
	{
		$navigation = new \Joomla\CMS\Pagination\Pagination($this->getState('list.total'), $this->getState('list.start'), $this->getState('list.limit'));

		return $navigation;
	}

	/**
	 * @return array|boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function getAdminOptions()
	{
		$category = $this->getAdminCategory();

		if (!$category)
		{
			return false;
		}

		$category->params = new Registry($category->params);

		$catList    = array();
		$catList [] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_TOPLEVEL'));

		// Make a standard yes/no list
		$published    = array();
		$published [] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_PUBLISHED'));
		$published [] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_UNPUBLISHED'));

		// Make a standard yes/no list
		$yesno    = array();
		$yesno [] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$yesno [] = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));

		// Anonymous posts default
		$post_anonymous    = array();
		$post_anonymous [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_X_REG'));
		$post_anonymous [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_X_ANO'));

		$cat_params                = array();
		$cat_params['ordering']    = 'ordering';
		$cat_params['toplevel']    = Text::_('COM_KUNENA_TOPLEVEL');
		$cat_params['sections']    = 1;
		$cat_params['unpublished'] = 1;
		$cat_params['catid']       = $category->id;
		$cat_params['action']      = 'admin';

		$channels_params           = array();
		$channels_params['catid']  = $category->id;
		$channels_params['action'] = 'admin';
		$channels_options          = array();
		$channels_options []       = HTMLHelper::_('select.option', 'THIS', Text::_('COM_KUNENA_CATEGORY_CHANNELS_OPTION_THIS'));
		$channels_options []       = HTMLHelper::_('select.option', 'CHILDREN', Text::_('COM_KUNENA_CATEGORY_CHANNELS_OPTION_CHILDREN'));

		if (empty($category->channels))
		{
			$category->channels = 'THIS';
		}

		$topic_ordering_options   = array();
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'lastpost', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_LASTPOST'));
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'creation', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_CREATION'));
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'alpha', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_ALPHA'));
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'views', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_VIEWS'));
		$topic_ordering_options[] = HTMLHelper::_('select.option', 'posts', Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_POSTS'));

		$aliases = array_keys($category->getAliases());

		$lists                     = array();
		$lists ['accesstypes']     = KunenaAccess::getInstance()->getAccessTypesList($category);
		$lists ['accesslists']     = KunenaAccess::getInstance()->getAccessOptions($category);
		$lists ['categories']      = HTMLHelper::_('kunenaforum.categorylist', 'parent_id', 0, null, $cat_params, 'class="inputbox"', 'value', 'text', $category->parent_id);
		$lists ['channels']        = HTMLHelper::_('kunenaforum.categorylist', 'channels[]', 0, $channels_options, $channels_params, 'class="inputbox" multiple="multiple"', 'value', 'text', explode(',', $category->channels));
		$lists ['aliases']         = $aliases ? HTMLHelper::_('kunenaforum.checklist', 'aliases', $aliases, true, 'category_aliases') : null;
		$lists ['published']       = HTMLHelper::_('select.genericlist', $published, 'published', 'class="inputbox"', 'value', 'text', $category->published);
		$lists ['forumLocked']     = HTMLHelper::_('select.genericlist', $yesno, 'locked', 'class="inputbox" size="1"', 'value', 'text', $category->locked);
		$lists ['forumReview']     = HTMLHelper::_('select.genericlist', $yesno, 'review', 'class="inputbox" size="1"', 'value', 'text', $category->review);
		$lists ['allow_polls']     = HTMLHelper::_('select.genericlist', $yesno, 'allow_polls', 'class="inputbox" size="1"', 'value', 'text', $category->allow_polls);
		$lists ['allow_anonymous'] = HTMLHelper::_('select.genericlist', $yesno, 'allow_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->allow_anonymous);
		$lists ['post_anonymous']  = HTMLHelper::_('select.genericlist', $post_anonymous, 'post_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->post_anonymous);
		$lists ['topic_ordering']  = HTMLHelper::_('select.genericlist', $topic_ordering_options, 'topic_ordering', 'class="inputbox" size="1"', 'value', 'text', $category->topic_ordering);
		$lists ['allow_ratings']   = HTMLHelper::_('select.genericlist', $yesno, 'allow_ratings', 'class="inputbox" size="1"', 'value', 'text', $category->allow_ratings);

		$options                 = array();
		$options[0]              = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_NEVER'));
		$options[1]              = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_SECTION'));
		$options[2]              = HTMLHelper::_('select.option', '2', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_CATEGORY'));
		$options[3]              = HTMLHelper::_('select.option', '3', Text::_('COM_KUNENA_A_CATEGORY_CFG_OPTION_SUBCATEGORY'));
		$lists['display_parent'] = HTMLHelper::_('select.genericlist', $options, 'params[display][index][parent]', 'class="inputbox" size="1"', 'value', 'text', $category->params->get('display.index.parent', '3'));

		unset($options[1]);

		$lists['display_children'] = HTMLHelper::_('select.genericlist', $options, 'params[display][index][children]', 'class="inputbox" size="1"', 'value', 'text', $category->params->get('display.index.children', '3'));

		$topicicons     = array();
		$topiciconslist = KunenaFolder::folders(JPATH_ROOT . '/media/kunena/topic_icons');

		foreach ($topiciconslist as $icon)
		{
			$topicicons[] = HTMLHelper::_('select.option', $icon, $icon);
		}

		if (empty($category->iconset))
		{
			$value = KunenaTemplate::getInstance()->params->get('DefaultIconset');
		}
		else
		{
			$value = $category->iconset;
		}

		$lists ['category_iconset'] = HTMLHelper::_('select.genericlist', $topicicons, 'iconset', 'class="inputbox" size="1"', 'value', 'text', $value);

		return $lists;
	}

	/**
	 * @return boolean|KunenaForumCategory|void
	 * @throws Exception
	 * @since Kunena
	 */
	public function getAdminCategory()
	{
		$category = KunenaForumCategoryHelper::get($this->getState('item.id'));

		if (!$this->me->isAdmin($category))
		{
			return false;
		}

		if ($this->_admincategory === false)
		{
			if ($category->exists())
			{
				if (!$category->isCheckedOut($this->me->userid))
				{
					$category->checkout($this->me->userid);
				}
			}
			else
			{
				// New category is by default child of the first section -- this will help new users to do it right
				$db = Factory::getDBO();
				$db->setQuery("SELECT a.id, a.name FROM #__kunena_categories AS a WHERE parent_id='0' AND id!='$category->id' ORDER BY ordering");

				try
				{
					$sections = $db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage());

					return;
				}

				$category->parent_id     = $this->getState('item.parent_id');
				$category->published     = 0;
				$category->ordering      = 9999;
				$category->pub_recurse   = 1;
				$category->admin_recurse = 1;
				$category->accesstype    = 'joomla.level';
				$category->access        = 1;
				$category->pub_access    = 1;
				$category->admin_access  = 8;
			}

			$this->_admincategory = $category;
		}

		return $this->_admincategory;
	}

	/**
	 * @return array|boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function getAdminModerators()
	{
		$category = $this->getAdminCategory();

		if (!$category)
		{
			return false;
		}

		$moderators = $category->getModerators(false);

		return $moderators;
	}

	/**
	 * @param   null $pks   pks
	 * @param   null $order order
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function saveorder($pks = null, $order = null)
	{
		$table      = \Joomla\CMS\Table\Table::getInstance('KunenaCategories', 'Table');
		$conditions = array();

		if (empty($pks))
		{
			return false;
		}

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);

			if ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					Factory::getApplication()->enqueueMessage($table->getError());

					return false;
				}

				// Remember to reorder within position and client_id
				$condition = $this->getReorderConditions($table);
				$found     = false;

				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$key          = $table->getKeyName();
					$conditions[] = array($table->$key, $condition);
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * @param $table
	 *
	 * @return array
	 * @since Kunena
	 */
	protected function getReorderConditions($table)
	{
		$condition   = array();
		$condition[] = 'parent_id = ' . (int) $table->parent_id;

		return $condition;
	}

	/**
	 * Get list of categories to be displayed in drop-down select in batch
	 *
	 * @since 5.1.0
	 * @return array
	 * @throws Exception
	 * @throws null
	 */
	public function getBatchCategories()
	{
		$categories         = $this->getAdminCategories();
		$batch_categories   = array();
		$batch_categories[] = HTMLHelper::_('select.option', 'select', Text::_('JSELECT'));

		foreach ($categories as $category)
		{
			$batch_categories [] = HTMLHelper::_('select.option', $category->id, str_repeat('...', count($category->indent) - 1) . ' ' . $category->name);
		}

		$list = HTMLHelper::_('select.genericlist', $batch_categories, 'batch_catid_target', 'class="inputbox" size="1"', 'value', 'text', 'select');

		return $list;
	}

	/**
	 * @return array|KunenaForumCategory[]
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function getAdminCategories()
	{
		if ($this->_admincategories === false)
		{
			$params = array(
				'ordering'           => $this->getState('list.ordering'),
				'direction'          => $this->getState('list.direction') == 'asc' ? 1 : -1,
			    'search'             => $this->getState('filter.search'),
				'unpublished'        => 1,
				'published'          => $this->getState('filter.published'),
				'filter_title'       => $this->getState('filter.title'),
				'filter_type'        => $this->getState('filter.type'),
				'filter_access'      => $this->getState('filter.access'),
				'filter_locked'      => $this->getState('filter.locked'),
				'filter_allow_polls' => $this->getState('filter.allow_polls'),
				'filter_review'      => $this->getState('filter.review'),
				'filter_anonymous'   => $this->getState('filter.anonymous'),
				'action'             => 'none'
			);

			$catid      = $this->getState('item.id', 0);
			$categories = array();
			$orphans    = array();

			if ($catid)
			{
				$categories   = KunenaForumCategoryHelper::getParents($catid, $this->getState('filter.levels') - 1, array('unpublished' => 1, 'action' => 'none'));
				$categories[] = KunenaForumCategoryHelper::get($catid);
			}
			else
			{
				$orphans = KunenaForumCategoryHelper::getOrphaned($this->getState('filter.levels') - 1, $params);
			}

			$categories = array_merge($categories, KunenaForumCategoryHelper::getChildren($catid, $this->getState('filter.levels') - 1, $params));
			$categories = array_merge($orphans, $categories);

			$categories = KunenaForumCategoryHelper::getIndentation($categories);
			$this->setState('list.total', count($categories));

			if ($this->getState('list.limit'))
			{
				$this->_admincategories = array_slice($categories, $this->getState('list.start'), $this->getState('list.limit'));
			}
			else
			{
				$this->_admincategories = $categories;
			}

			$admin = 0;
			$acl   = KunenaAccess::getInstance();

			foreach ($this->_admincategories as $category)
			{
				// TODO: Following is needed for J!2.5 only:
				$parent            = $category->getParent();
				$siblings          = array_keys(KunenaForumCategoryHelper::getCategoryTree($category->parent_id));
				$category->up      = $this->me->isAdmin($parent) && reset($siblings) != $category->id;
				$category->down    = $this->me->isAdmin($parent) && end($siblings) != $category->id;
				$category->reorder = $this->me->isAdmin($parent);

				// Get ACL groups for the category.
				$access               = $acl->getCategoryAccess($category);
				$category->accessname = array();

				foreach ($access as $item)
				{
					if (!empty($item['admin.link']))
					{
						$category->accessname[] = '<a href="' . htmlentities($item['admin.link'], ENT_COMPAT, 'utf-8') . '">' . htmlentities($item['title'], ENT_COMPAT, 'utf-8') . '</a>';
					}
					else
					{
						$category->accessname[] = htmlentities($item['title'], ENT_COMPAT, 'utf-8');
					}
				}

				$category->accessname = implode(' / ', $category->accessname);

				// Checkout?
				if ($this->me->isAdmin($category) && $category->isCheckedOut(0))
				{
				    $category->editor = KunenaFactory::getUser($category->checked_out)->getName();
				}
				else
				{
					$category->checked_out = 0;
					$category->editor      = '';
				}

				$admin += $this->me->isAdmin($category);
			}

			$this->setState('list.count.admin', $admin);
		}

		if (!empty($orphans))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_CATEGORY_ORPHAN_DESC'), 'notice');
		}

		return $this->_admincategories;
	}

	/**
	 * Method to auto-populate the model state.
	 * @since Kunena
	 * @throws Exception
	 */
	protected function populateState()
	{
		$this->context = 'com_kunena.admin.categories';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		// List state information.
		$value = $this->getUserStateFromRequest($this->context . '.list.start', 'limitstart', 0, 'int');
		$this->setState('list.start', $value);

		$value = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
		$this->setState('list.limit', $value);

		$value = $this->getUserStateFromRequest($this->context . '.list.ordering', 'filter_order', 'ordering', 'cmd');
		$this->setState('list.ordering', $value);

		$value = $this->getUserStateFromRequest($this->context . '.list.direction', 'filter_order_Dir', 'asc', 'word');

		if ($value != 'asc')
		{
			$value = 'desc';
		}

		$this->setState('list.direction', $value);

		$filter_active = '';

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $value);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $value !== '' ? (int) $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.title', 'filter_title', '', 'string');
		$this->setState('filter.title', $value !== '' ? $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '', 'string');
		$this->setState('filter.type', $value !== '' ? $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '', 'string');
		$this->setState('filter.access', $value !== '' ? (int) $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.locked', 'filter_locked', '', 'string');
		$this->setState('filter.locked', $value !== '' ? (int) $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.allow_polls', 'filter_allow_polls', '', 'string');
		$this->setState('filter.allow_polls', $value !== '' ? (int) $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.review', 'filter_review', '', 'string');
		$this->setState('filter.review', $value !== '' ? (int) $value : null);

		$filter_active .= $value = $this->getUserStateFromRequest($this->context . '.filter.anonymous', 'filter_anonymous', '', 'string');
		$this->setState('filter.anonymous', $value !== '' ? (int) $value : null);

		$this->setState('filter.active', !empty($filter_active));

		// TODO: implement
		$value = $this->getUserStateFromRequest($this->context . ".filter.levels", 'levellimit', 10, 'int');
		$this->setState('filter.levels', $value);

		$catid     = $this->getUserStateFromRequest($this->context . '.filter.catid', 'catid', 0, 'int');
		$layout    = $this->getWord('layout', 'edit');
		$parent_id = 0;

		if ($layout == 'create')
		{
			$parent_id = $catid;
			$catid     = 0;
		}

		$this->setState('item.id', $catid);
		$this->setState('item.parent_id', $parent_id);
	}
}
