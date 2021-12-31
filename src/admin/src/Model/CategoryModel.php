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

namespace Kunena\Forum\Administrator\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use RuntimeException;

/**
 * Category Model for Kunena
 *
 * @since  6.0
 */
class CategoryModel extends CategoriesModel
{
	/**
	 * @var     KunenaCategory
	 * @since   Kunena 6.0
	 */
	protected $internalAdminCategory = false;

	/**
	 * @return  boolean|KunenaCategory|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getAdminCategory()
	{
		$category = KunenaCategoryHelper::get($this->getState($this->getName() . '.id'));

		if (!$this->me->isAdmin($category))
		{
			return false;
		}

		if ($this->internalAdminCategory === false)
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
				$db = Factory::getContainer()->get('DatabaseDriver');

				$query = $db->getQuery(true)
					->select('a.id, a.name')
					->from("{$db->quoteName('#__kunena_categories')} AS a")
					->where("parentid={$db->quote('0')}")
					->where("id!={$db->quote($category->id)}")
					->order('ordering');

				$db->setQuery($query);

				try
				{
					$this->sections = $db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage());

					return;
				}

				$category->parentid     = $this->getState('item.parent_id');
				$category->published    = 0;
				$category->ordering     = 9999;
				$category->pubRecurse   = 1;
				$category->adminRecurse = 1;
				$category->accesstype   = 'joomla.level';
				$category->access       = 1;
				$category->pubAccess    = 1;
				$category->adminAccess  = 8;
			}

			$this->internalAdminCategory = $category;
		}

		return $this->internalAdminCategory;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
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
