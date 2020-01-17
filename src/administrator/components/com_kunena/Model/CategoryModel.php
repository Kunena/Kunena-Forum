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
use RuntimeException;

/**
 * Category Model for Kunena
 *
 * @since  6.0
 */
class CategoryModel extends \KunenaModel
{
	/**
	 * @var     KunenaForumCategory
	 * @since   Kunena 6.0
	 */
	protected $_admincategory = false;

	/**
	 * @return  boolean|KunenaForumCategory|void
	 *
	 * @since   Kunena 6.0
	 *
	 */
	public function getAdminCategory()
	{
		$category = \KunenaForumCategoryHelper::get($this->getState('item.id'));

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
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->context = 'com_kunena.admin.category';

		$value = Factory::getApplication()->input->getInt('catid');
		$this->setState($this->getName() . 'item.id', $value);

		// List state information.
		parent::populateState($ordering, $direction);
	}
}
