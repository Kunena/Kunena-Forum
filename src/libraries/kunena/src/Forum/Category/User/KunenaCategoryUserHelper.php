<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Category.User
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Category\User;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class \Kunena\Forum\Libraries\Forum\Category\CategoryUserHelper
 *
 * @since   Kunena 6.0
 */
abstract class KunenaCategoryUserHelper
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $_instances = [];

	/**
	 * Get an instance of \Kunena\Forum\Libraries\Forum\Category\CategoryUser object.
	 *
	 * @param   null|int  $category  The category id to load.
	 * @param   mixed     $user      The user id to load - Can be only an integer.
	 * @param   bool      $reload    Reload objects from the database.
	 *
	 * @return  KunenaCategoryUser    The user category object.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function get($category = null, $user = null, $reload = false): KunenaCategoryUser
	{
		if ($category instanceof KunenaCategory)
		{
			$category = $category->id;
		}

		$category = \intval($category);
		$user     = KunenaUserHelper::get($user);

		if ($category === null)
		{
			return new KunenaCategoryUser(null, $user);
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
	 * @param   bool|array|int  $ids   The category ids to load.
	 * @param   mixed           $user  The user id to load.
	 *
	 * @return  KunenaCategoryUser[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getCategories($ids = false, $user = null): array
	{
		$user = KunenaUserHelper::get($user);

		if ($ids === false)
		{
			// Get categories which are seen by current user
			$ids = KunenaCategoryHelper::getCategories();
		}
		elseif (!\is_array($ids))
		{
			$ids = [$ids];
		}

		// Convert category objects into ids
		foreach ($ids as $i => $id)
		{
			if ($id instanceof KunenaCategory)
			{
				$ids[$i] = $id->id;
			}
		}

		$ids = array_unique($ids);
		self::loadCategories($ids, $user);

		$list = [];

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
	 * @param   array       $ids   The category ids to load.
	 * @param   KunenaUser  $user  user
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected static function loadCategories(array $ids, KunenaUser $user): void
	{
		foreach ($ids as $i => $id)
		{
			$iid = \intval($id);

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
		$db     = Factory::getContainer()->get('DatabaseDriver');
		$query  = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_user_categories'))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($user->userid))
			->andWhere($db->quoteName('category_id') . ' IN (' . $idlist . ')');
		$db->setQuery($query);

		try
		{
			$results = (array) $db->loadAssocList('category_id');
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		foreach ($ids as $id)
		{
			if (isset($results[$id]))
			{
				$instance = new KunenaCategoryUser;
				$instance->bind($results[$id]);
				$instance->exists(true);
				self::$_instances [$user->userid][$id] = $instance;
			}
			else
			{
				self::$_instances [$user->userid][$id] = new KunenaCategoryUser($id, $user);
			}
		}

		unset($results);
	}

	/**
	 * @param   array  $ids   ids
	 * @param   null   $user  users
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function markRead(array $ids, $user = null): void
	{
		$user = KunenaUserHelper::get($user);

		$items      = self::getCategories($ids, $user);
		$updateList = [];
		$insertList = [];

		$db   = Factory::getContainer()->get('DatabaseDriver');
		$time = Factory::getDate()->toUnix();

		foreach ($items as $item)
		{
			if ($item->exists())
			{
				$updateList[] = (int) $item->category_id;
			}
			else
			{
				$insertList[] = $db->quote($user->userid) . ', ' . $db->quote($item->category_id) . ', ' . $db->quote($time) . ', ' . $db->quote('');
			}
		}

		if ($updateList)
		{
			$idlist = implode(',', $updateList);
			$query  = $db->getQuery(true);
			$query
				->update($db->quoteName('#__kunena_user_categories'))
				->set('allreadtime = ' . $db->quote($time))
				->where('user_id = ' . $db->quote($user->userid))
				->where($db->quoteName('category_id') . ' IN (' . $idlist . ')');
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		if ($insertList)
		{
			$query = $db->getQuery(true);
			$query
				->insert($db->quoteName('#__kunena_user_categories'))
				->columns('user_id, category_id, allreadtime, params')
				->values($insertList);
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}
	}
}
