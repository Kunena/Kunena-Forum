<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Model;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Forum\Category\Category;
use Kunena\Forum\Libraries\Forum\Category\CategoryHelper;
use RuntimeException;

/**
 * Category Model for Kunena
 *
 * @since  6.0
 */
class CategoryModel extends CategoriesModel
{
	/**
	 * @var     Category
	 * @since   Kunena 6.0
	 */
	protected $_admincategory = false;

	/**
	 * @return  boolean|Category|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getAdminCategory()
	{
		$category = CategoryHelper::get($this->getState($this->getName() . '.id'));

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

				$query = $db->getQuery(true)
					->select('a.id, a.name')
					->from("{$db->quoteName('#__kunena_categories')} AS a")
					->where("parent_id={$db->quote('0')}")
					->where("id!={$db->quote($category->id)}")
					->order('ordering');

				$db->setQuery($query);

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
	 * Method to auto-populate the model state.
	 *
	 * @param   null  $ordering   ordering
	 * @param   null  $direction  direction
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->context = 'com_kunena.admin.category';

		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		$layout        = $app->input->get('layout');
		$this->context = 'com_kunena.admin.category';

		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$value = Factory::getApplication()->input->getInt('catid');
		$this->setState($this->getName() . '.id', $value);
	}
}
