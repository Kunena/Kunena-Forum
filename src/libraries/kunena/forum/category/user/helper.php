<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Category.User
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class KunenaForumCategoryUserHelper
 * @since Kunena
 */
abstract class KunenaForumCategoryUserHelper
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $_instances = array();

	/**
	 * Get an instance of KunenaForumCategoryUser object.
	 *
	 * @param   null|int $category The category id to load.
	 * @param   mixed    $user     The user id to load - Can be only an integer.
	 * @param   bool     $reload   Reload objects from the database.
	 *
	 * @return KunenaForumCategoryUser    The user category object.
	 * @throws Exception
	 * @since Kunena
	 */
	public static function get($category = null, $user = null, $reload = false)
	{
		if ($category instanceof KunenaForumCategory)
		{
			$category = $category->id;
		}

		$category = intval($category);
		$user     = KunenaUserHelper::get($user);

		if ($category === null)
		{
			return new KunenaForumCategoryUser(null, $user);
		}

		if ($reload || empty(self::$_instances [$user->userid][$category]))
		{
			$user_categories                             = self::getCategories($category, $user);
			self::$_instances [$user->userid][$category] = array_pop($user_categories);
		}

		return self::$_instances [$user->userid][$category];
	}

	/**
	 * Get categories for a specific user.
	 *
	 * @param   bool|array|int $ids  The category ids to load.
	 * @param   mixed          $user The user id to load.
	 *
	 * @return KunenaForumCategoryUser[]
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getCategories($ids = false, $user = null)
	{
		$user = KunenaUserHelper::get($user);

		if ($ids === false)
		{
			// Get categories which are seen by current user
			$ids = KunenaForumCategoryHelper::getCategories();
		}
		elseif (!is_array($ids))
		{
			$ids = array($ids);
		}

		// Convert category objects into ids
		foreach ($ids as $i => $id)
		{
			if ($id instanceof KunenaForumCategory)
			{
				$ids[$i] = $id->id;
			}
		}

		$ids = array_unique($ids);
		self::loadCategories($ids, $user);

		$list = array();

		foreach ($ids as $id)
		{
			if (!empty(self::$_instances [$user->userid][$id]))
			{
				$list [$id] = self::$_instances [$user->userid][$id];
			}
		}

		return $list;
	}

	/**
	 * Load categories for a specific user.
	 *
	 * @param   array      $ids  The category ids to load.
	 * @param   KunenaUser $user user
	 *
	 * @throws Exception
	 * @since Kunena
	 * @return void
	 */
	protected static function loadCategories(array $ids, KunenaUser $user)
	{
		foreach ($ids as $i => $id)
		{
			$iid = intval($id);

			if ($iid != $id || isset(self::$_instances [$user->userid][$id]))
			{
				unset($ids[$i]);
			}
		}

		if (empty($ids))
		{
			return;
		}

		$idlist = implode(',', $ids);
		$db     = Factory::getDBO();
		$query  = "SELECT * FROM #__kunena_user_categories WHERE user_id={$db->quote($user->userid)} AND category_id IN ({$idlist})";
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList('category_id');
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach ($ids as $id)
		{
			if (isset($results[$id]))
			{
				$instance = new KunenaForumCategoryUser;
				$instance->bind($results[$id]);
				$instance->exists(true);
				self::$_instances [$user->userid][$id] = $instance;
			}
			else
			{
				self::$_instances [$user->userid][$id] = new KunenaForumCategoryUser($id, $user);
			}
		}

		unset($results);
	}

	/**
	 * @param   array $ids  ids
	 * @param   null  $user users
	 *
	 * @throws Exception
	 * @since Kunena
	 * @return void
	 */
	public static function markRead(array $ids, $user = null)
	{
		$user = KunenaUserHelper::get($user);

		$items      = self::getCategories($ids, $user);
		$updateList = array();
		$insertList = array();

		$db   = Factory::getDbo();
		$time = Factory::getDate()->toUnix();

		foreach ($items as $item)
		{
			if ($item->exists())
			{
				$updateList[] = (int) $item->category_id;
			}
			else
			{
				$insertList[] = "{$db->quote($user->userid)}, {$db->quote($item->category_id)}, {$db->quote($time)}";
			}
		}

		if ($updateList)
		{
			$idlist = implode(',', $updateList);
			$query  = $db->getQuery(true);
			$query
				->update('#__kunena_user_categories')
				->set("allreadtime={$db->quote($time)}")
				->where("user_id={$db->quote($user->userid)}")
				->where("category_id IN ({$idlist})");
			$db->setQuery($query);
			$db->execute();
		}

		if ($insertList)
		{
			$query = $db->getQuery(true);
			$query
				->insert('#__kunena_user_categories')
				->columns('user_id, category_id, allreadtime')
				->values($insertList);
			$db->setQuery($query);
			$db->execute();
		}
	}
}
