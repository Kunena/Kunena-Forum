<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Category
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Category;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Transliterate;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\String\StringHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\Tree\KunenaTree;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * Class CategoryHelper
 *
 * @since   Kunena 6.0
 */
abstract class KunenaCategoryHelper
{
	/**
	 * @var     KunenaCategory[]
	 * @since   Kunena 6.0
	 */
	public static $_instances;

	/**
	 * @var     mixed
	 * @since   Kunena 6.0
	 */
	protected static $_tree;

	/**
	 * @var     mixed
	 * @since   Kunena 6.0
	 */
	protected static $allowed;

	/**
	 * Initialize class.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function initialize(): void
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (KunenaFactory::getConfig()->get('cache_cat'))
		{
			$cache = Factory::getCache('com_kunena', 'callback');
			$cache->setLifeTime(180);
			self::$_instances = $cache->call(['CategoryHelper', 'loadCategories']);
		}
		else
		{
			self::$_instances = self::loadCategories();
		}

		if (\is_null(self::$_tree))
		{
			self::buildTree(self::$_instances);
		}

		self::$allowed = KunenaAccess::getInstance()->getAllowedCategories();
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  array|boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function loadCategories()
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_categories'))
			->order([$db->quoteName('ordering'), $db->quoteName('name')]);
		$db->setQuery($query);

		try
		{
			$instances = (array) $db->loadObjectList('id');
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		if (!$instances)
		{
			return [];
		}

		foreach ($instances as $id => $instance)
		{
			$kunenacategory      = self::get($id);
			$cat_instances [$id] = $kunenacategory;
		}

		// TODO: remove this by adding level into table
		self::buildTree($cat_instances);
		$heap = [null];

		while (($parent = array_shift($heap)) !== null)
		{
			foreach (self::$_tree [$parent] as $id => $children)
			{
				if (!empty($children))
				{
					array_push($heap, $id);
				}

				$cat_instances[$id]->level = $parent ? $cat_instances[$parent]->level + 1 : 0;
			}
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $cat_instances;
	}

	/**
	 * Returns the global Category object, only creating it if it doesn't already exist.
	 *
	 * @param   int   $identifier  The category to load - Can be only an integer.
	 * @param   bool  $reload      Reload category from the database.
	 *
	 * @return  KunenaCategory  The Category object.
	 *
	 * @since   Kunena 1.6
	 *
	 * @throws  Exception
	 */
	public static function get($identifier = null, $reload = false): KunenaCategory
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($identifier instanceof KunenaCategory)
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return $identifier;
		}

		if (!is_numeric($identifier))
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
			$category = new KunenaCategory;
			$category->load();

			return $category;
		}

		$id = \intval($identifier);

		if (empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new KunenaCategory(['id' => $id]);
			self::$_instances [$id]->load();
		}
		elseif ($reload)
		{
			self::$_instances [$id]->load();
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$_instances [$id];
	}

	/**
	 * @param   array  $instances  instances
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected static function buildTree(array $instances): void
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		self::$_tree = [];

		foreach ($instances as $instance)
		{
			if (!isset(self::$_tree [(int) $instance->id]))
			{
				self::$_tree [$instance->id] = [];
			}

			self::$_tree [$instance->parentid][$instance->id] = &self::$_tree [(int) $instance->id];
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @internal
	 *
	 * @param   KunenaCategory  $instance  instance
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public static function register(KunenaCategory $instance): void
	{
		if ($instance->exists())
		{
			$instance->level                  = isset(self::$_instances [$instance->parentid]) ? self::$_instances [$instance->parentid]->level + 1 : 0;
			self::$_instances [$instance->id] = $instance;

			if (!isset(self::$_tree [(int) $instance->id]))
			{
				self::$_tree [$instance->id]                      = [];
				self::$_tree [$instance->parentid][$instance->id] = &self::$_tree [$instance->id];
			}
		}
		else
		{
			unset(self::$_instances [$instance->id]);
			unset(self::$_tree [$instance->id], self::$_tree [$instance->parentid][$instance->id]);
		}
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  KunenaCategory[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getSubscriptions($user = null): array
	{
		$user  = KunenaUserHelper::get($user);
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select($db->quoteName('category_id'))
			->from($db->quoteName('#__kunena_user_categories'))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($user->userid))
			->andWhere($db->quoteName('subscribed') . ' = 1');
		$db->setQuery($query);

		try
		{
			$subscribed = (array) $db->loadColumn();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return [];
		}

		return self::getCategories($subscribed);
	}

	/**
	 * @param   bool|array  $ids        ids
	 * @param   bool        $reverse    reverse
	 * @param   string      $authorise  authorise
	 *
	 * @return array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws \Exception
	 */
	public static function getCategories($ids = false, $reverse = false, $authorise = 'read'): array
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($ids === false)
		{
			if ($authorise == 'none')
			{
				KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

				return self::$_instances;
			}

			$ids = self::$_instances;
		}
		elseif (\is_array($ids))
		{
			$ids = array_flip($ids);
		}
		else
		{
			$ids = [\intval($ids) => 1];
		}

		if (!$reverse)
		{
			$allowed = $authorise != 'none' ? array_intersect_key($ids, KunenaAccess::getInstance()->getAllowedCategories()) : $ids;
			$list    = array_intersect_key(self::$_instances, $allowed);

			if ($authorise != 'none' && $authorise != 'read')
			{
				foreach ($list as $category)
				{
					if (!$category->isAuthorised($authorise))
					{
						unset($list [$category->id]);
					}
				}
			}
		}
		else
		{
			$allowed = $authorise != 'none' ? array_intersect_key(self::$_instances, KunenaAccess::getInstance()->getAllowedCategories()) : self::$_instances;
			$list    = array_diff_key($allowed, $ids);

			if ($authorise != 'none' && $authorise != 'read')
			{
				foreach ($list as $category)
				{
					if (!$category->isAuthorised($authorise))
					{
						unset($list [$category->id]);
					}
				}
			}
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $list;
	}

	/**
	 * @param   array  $ids    ids
	 * @param   bool   $value  value
	 * @param   mixed  $user   user
	 *
	 * @return  integer
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws Exception
	 */
	public static function subscribe(array $ids, $value = true, $user = null): int
	{
		$count = 0;

		// Pre-load all items
		$usercategories = KunenaCategoryUserHelper::getCategories($ids, $user);

		foreach ($usercategories as $userCategory)
		{
			if ($userCategory->subscribed != (int) $value)
			{
				$count++;
			}

			$userCategory->subscribed = (int) $value;

			if (!$userCategory->params)
			{
				$userCategory->params = '';
			}

			$userCategory->save();
		}

		return $count;
	}

	/**
	 * Get subscribed categories ordered by latest post or parameter.
	 *
	 * @param   mixed  $user        user
	 * @param   int    $limitstart  limitstart
	 * @param   int    $limit       limit
	 * @param   array  $params      params
	 *
	 * @return  array (total, list)
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 */
	public static function getLatestSubscriptions($user, $limitstart = 0, $limit = 0, $params = []): array
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$db     = Factory::getContainer()->get('DatabaseDriver');
		$config = KunenaFactory::getConfig();

		if ($limit < 1)
		{
			$limit = $config->threadsPerPage;
		}

		$userids = \is_array($user) ? implode(",", $user) : KunenaUserHelper::get($user)->userid;
		$orderby = isset($params['orderby']) ? (string) $params['orderby'] : 'c.last_post_time DESC';
		$where   = isset($params['where']) ? (string) $params['where'] : '';
		$allowed = implode(',', array_keys(KunenaAccess::getInstance()->getAllowedCategories()));

		if (!$userids || !$allowed)
		{
			return [0, []];
		}

		// Get total count
		$query = $db->getQuery(true);
		$query->select('COUNT(DISTINCT c.id)')
			->from($db->quoteName('#__kunena_categories', 'c'))
			->innerJoin($db->quoteName('#__kunena_user_categories', 'u') . ' ON u.category_id = c.id')
			->where('u.user_id IN (' . $userids . ')')
			->where($db->quoteName('u.category_id') . ' IN (' . $allowed . ')')
			->where($db->quoteName('u.subscribed') . ' = 1 ' . $where);
		$db->setQuery($query);

		try
		{
			$total = (int) $db->loadResult();
		}
		catch (Exception $e)
		{
			KunenaError::displayDatabaseError($e);

			return [0, []];
		}

		if (!$total)
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return [0, []];
		}

		// If out of range, use last page
		if ($total < $limitstart)
		{
			$limitstart = \intval($total / $limit) * $limit;
		}

		$query = $db->getQuery(true);
		$query->select('c.id')
			->from($db->quoteName('#__kunena_categories', 'c'))
			->innerJoin($db->quoteName('#__kunena_user_categories', 'u') . ' ON u.category_id = c.id')
			->where('u.user_id IN (' . $userids . ') AND u.category_id IN (' . $allowed . ') AND u.subscribed=1 ' . $where)
			->group('c.id')
			->order($orderby);
		$query->setLimit($limit, $limitstart);
		$db->setQuery($query);

		try
		{
			$subscribed = (array) $db->loadColumn();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return [0, []];
		}

		$list = [];

		foreach ($subscribed as $id)
		{
			$list[$id] = self::$_instances[$id];
		}

		unset($subscribed);

		return [$total, $list];
	}

	/**
	 * @param   int|array  $catids  catids
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public static function getNewTopics($catids): void
	{
		$user = KunenaUserHelper::getMyself();

		if (!KunenaFactory::getConfig()->showNew || !$user->exists())
		{
			return;
		}

		$session    = KunenaFactory::getSession();
		$categories = self::getCategories($catids);
		$catlist    = [];

		foreach ($categories as $category)
		{
			$catlist += $category->getChannels();
			$catlist += $category->getChildren(-1);
		}

		if (empty($catlist))
		{
			return;
		}

		$catlist = implode(',', array_keys($catlist));
		$db      = Factory::getContainer()->get('DatabaseDriver');
		$query   = $db->getQuery(true);
		$query->select('t.category_id, COUNT(*) AS new')
			->from($db->quoteName('#__kunena_topics', 't'))
			->leftJoin($db->quoteName('#__kunena_user_categories', 'uc') . ' ON uc.category_id = t.category_id AND uc.user_id=' . $db->quote($user->userid))
			->leftJoin($db->quoteName('#__kunena_user_read', 'ur') . ' ON ur.topic_id = t.id AND ur.user_id=' . $db->quote($user->userid))
			->where('t.category_id IN (' . $catlist . ')')
			->where('t.hold = 0')
			->where('t.last_post_time > ' . $db->quote($session->getAllReadTime()))
			->where('uc.allreadtime IS NULL OR t.last_post_time > uc.allreadtime')
			->where('ur.topic_id IS NULL OR t.last_post_id != ur.message_id')
			->group($db->quoteName('category_id'));
		$db->setQuery($query);

		try
		{
			$newlist = (array) $db->loadObjectList('category_id');
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return;
		}

		if (empty($newlist))
		{
			return;
		}

		$new = [];

		foreach ($newlist as $id => $item)
		{
			$new[$id] = (int) $item->new;
		}

		foreach ($categories as $category)
		{
			$channels = $category->getChannels();
			$channels += $category->getChildren(-1);
			$category->getNewCount(array_sum(array_intersect_key($new, $channels)));
		}
	}

	/**
	 * @param   string      $accesstype  accesstype
	 * @param   bool|array  $groupids    groupids
	 *
	 * @return  KunenaCategory[]
	 *
	 * @since   Kunena 6.0
	 */
	public static function getCategoriesByAccess($accesstype = 'joomla.level', $groupids = false): array
	{
		if (\is_array($groupids))
		{
			$groupids = array_unique($groupids);
		}
		else
		{
			$groupids = [\intval($groupids)];
		}

		$list = [];

		foreach (self::$_instances as $instance)
		{
			if ($instance->accesstype == $accesstype && ($groupids === false || \in_array($instance->access, $groupids)))
			{
				$list [$instance->id] = $instance;
			}
		}

		return $list;
	}

	/**
	 * @param   int    $id      id
	 * @param   int    $levels  levels
	 * @param   array  $params  params
	 *
	 * @return  KunenaCategory[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	public static function getParents($id = 0, $levels = 100, $params = []): array
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$unpublished = isset($params['unpublished']) ? (bool) $params['unpublished'] : 0;
		$action      = isset($params['action']) ? (string) $params['action'] : 'read';

		if (!isset(self::$_instances [$id]) || !self::$_instances [$id]->isAuthorised($action, null))
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return [];
		}

		$list   = [];
		$parent = self::$_instances [$id]->parentid;

		while ($parent && $levels--)
		{
			if (!isset(self::$_instances [$parent]))
			{
				KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

				return [];
			}

			if (!$unpublished && self::$_instances[$parent]->published != 1)
			{
				KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

				return [];
			}

			$list[$parent] = self::$_instances [$parent];

			$parent = self::$_instances [$parent]->parentid;
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return array_reverse($list, true);
	}

	/**
	 * @param   int    $levels  levels
	 * @param   array  $params  params
	 *
	 * @return  KunenaCategory[]
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	public static function getOrphaned($levels = 0, $params = []): array
	{
		$list = [];

		foreach (self::getCategoryTree(false) as $catid => $children)
		{
			if ($catid && !self::get($catid)->exists())
			{
				foreach (self::getChildren($catid, $levels, $params) as $category)
				{
					if ($category->parentid == $catid)
					{
						$category->name = Text::_('COM_KUNENA_CATEGORY_ORPHAN') . ' : ' . $category->name;
					}

					$list[$category->id] = $category;
				}
			}
		}

		return $list;
	}

	/**
	 * @param   int  $parent  parent
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function getCategoryTree($parent = 0): array
	{
		if ($parent === false)
		{
			return self::$_tree;
		}

		return isset(self::$_tree[$parent]) ? self::$_tree[$parent] : [];
	}

	/**
	 * @param   int    $parents  parents
	 * @param   int    $levels   levels
	 * @param   array  $params   params
	 *
	 * @return array
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 *
	 */
	public static function getChildren($parents = 0, $levels = 0, $params = []): array
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!\is_array($parents) && !isset(self::$_tree[$parents]))
		{
			KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return [];
		}

		static $defaults = [
			'ordering'  => 'ordering',
			'direction' => 1,
			'search'    => '',
			'action'    => 'read',
			'selected'  => 0,
			'parents'   => true,
		];

		$parents             = (array) $parents;
		$params              = (array) $params;
		$optimize            = empty($params);
		$params              += $defaults;
		$params['published'] = isset($params['published']) ? (int) $params['published'] : (empty($params['unpublished']) ? 1 : null);

		$list = self::_getChildren($parents, $levels, $params, $optimize);

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $list;
	}

	/**
	 * @param   array  $parents   parents
	 * @param   int    $levels    levels
	 * @param   array  $params    params
	 * @param   bool   $optimize  optimize
	 *
	 * @return array
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 *
	 */
	protected static function _getChildren(array $parents, int $levels, array $params, bool $optimize): array
	{
		$list = [];

		foreach ($parents as $parent)
		{
			if ($parent instanceof KunenaCategory)
			{
				$parent = $parent->id;
			}

			if (!isset(self::$_tree [$parent]))
			{
				continue;
			}

			$cats = self::$_tree [$parent];

			if (!$optimize)
			{
				switch ($params['ordering'])
				{
					case 'catid' :
						if ($params['direction'] > 0)
						{
							ksort($cats);
						}
						else
						{
							krsort($cats);
						}
						break;
					case 'name' :
					case 'p.title' :
						if ($params['direction'] > 0)
						{
							uksort($cats, [__CLASS__, 'compareByNameAsc']);
						}
						else
						{
							uksort($cats, [__CLASS__, 'compareByNameDesc']);
						}
						break;
					case 'ordering' :
					default :
						if ($params['direction'] < 0)
						{
							$cats = array_reverse($cats, true);
						}
				}
			}

			foreach ($cats as $id => $children)
			{
				if (!isset(self::$_instances [$id]))
				{
					continue;
				}

				if ($id == $params['selected'])
				{
					continue;
				}

				$instance = self::$_instances[$id];

				$filtered = isset($params['published']) && $instance->published != $params['published'];

				if (!$optimize)
				{
					$filtered |= isset($params['filterTitle']) && (StringHelper::stristr($instance->name, (string) $params['filterTitle']) === false
							&& StringHelper::stristr($instance->alias, (string) $params['filterTitle']) === false);
					$filtered |= isset($params['filterType']);
					$filtered |= isset($params['filterAccess']) && ($instance->accesstype != 'joomla.level' || $instance->access != $params['filterAccess']);
					$filtered |= isset($params['filterLocked']) && $instance->locked != (int) $params['filterLocked'];
					$filtered |= isset($params['filterAllowPolls']) && $instance->allowPolls != (int) $params['filterAllowPolls'];
					$filtered |= isset($params['filterReview']) && $instance->review != (int) $params['filterReview'];
					$filtered |= isset($params['filterAnonymous']) && $instance->allowAnonymous != (int) $params['filterAnonymous'];
				}

				if ($filtered && $params['action'] != 'admin')
				{
					continue;
				}

				$clist = [];

				if ($levels && !empty($children))
				{
					$clist = self::_getChildren([$id], $levels - 1, $params, $optimize);
				}

				$allowed = $params['action'] == 'none' || ($params['action'] == 'read' && !empty(self::$allowed[$id])) || $instance->isAuthorised($params['action'], null);

				if (empty($clist) && !$allowed)
				{
					continue;
				}

				if (!empty($clist) || !$params['search'] || \intval($params['search']) == $id || StringHelper::stristr(Transliterate::utf8_latin_to_ascii($instance->name), (string) Transliterate::utf8_latin_to_ascii($params['search'])) || StringHelper::stristr($instance->name, (string) $params['search']))
				{
					if (!$filtered && (empty($clist) || $params['parents']))
					{
						$list [$id] = $instance;
					}

					$list += $clist;
				}
			}
		}

		return $list;
	}

	/**
	 * @param   string|array  $categories  categories
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public static function &getIndentation($categories): array
	{
		$tree = new KunenaTree($categories);

		return $tree->getIndentation();
	}

	/**
	 * @param   string|array  $categories  categories
	 *
	 * @return  boolean|integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function recount($categories = '')
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		if (\is_array($categories))
		{
			$categories = implode(',', $categories);
		}

		$categories = !empty($categories) ? "AND t.category_id IN ({$categories})" : '';

		// Update category post count and last post info on categories which have published topics
		$query = "UPDATE #__kunena_categories AS c
			INNER JOIN (
					SELECT t.category_id AS id, COUNT( * ) AS numTopics, SUM( t.posts ) AS numPosts, t2.id as last_topic_id
					FROM #__kunena_topics AS t INNER JOIN (SELECT t.id, t.category_id, t.last_post_time
															FROM #__kunena_topics AS t,
																	(SELECT category_id ,  max(last_post_time) as last_post_time
																	FROM  `#__kunena_topics`
																	WHERE hold =0
																	AND moved_id =0
															GROUP BY category_id) AS temp
															WHERE temp.last_post_time = t.last_post_time
															{$categories}
															AND t.category_id=temp.category_id
															) AS t2 ON t2.category_id=t.category_id
					WHERE t.hold =0
					AND t.moved_id =0
					{$categories}
					GROUP BY t.category_id
			) AS r ON r.id=c.id
			INNER JOIN #__kunena_topics AS tt ON tt.id=r.last_topic_id
			SET c.numTopics = r.numTopics,
				c.numPosts = r.numPosts,
				c.last_topic_id=r.last_topic_id,
				c.last_post_id = tt.last_post_id,
				c.last_post_time = tt.last_post_time";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows = $db->getAffectedRows();

		// Update categories which have no published topics
		$query = $db->getQuery(true);
		$query
			->update($db->quoteName('#__kunena_categories', 'c'))
			->leftJoin($db->quoteName('#__kunena_topics', 'tt') . ' ON c.id = tt.category_id AND tt.hold = 0')
			->set(
				"c.numTopics = 0,
				c.numPosts = 0,
				c.last_topic_id = 0,
				c.last_post_id = 0,
				c.last_post_time = 0"
			)
			->where("tt.id IS NULL");
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows += $db->getAffectedRows();

		if ($rows)
		{
			// If something changed, clean our cache
			KunenaCacheHelper::clearCategories();
		}

		return $rows;
	}

	/**
	 * @return  boolean|integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function fixAliases()
	{
		$db = Factory::getContainer()->get('DatabaseDriver');

		$rows    = 0;
		$queries = [];

		// Fix wrong category id in aliases
		$queries[] = "UPDATE #__kunena_aliases AS a INNER JOIN #__kunena_categories AS c ON a.alias = c.alias SET a.item = c.id WHERE a.type='catid'";

		// Delete aliases from non-existing categories
		$queries[] = "DELETE a FROM #__kunena_aliases AS a LEFT JOIN #__kunena_categories AS c ON a.item = c.id WHERE a.type='catid' AND c.id IS NULL";

		// Add missing category aliases
		$queries[] = "INSERT IGNORE INTO #__kunena_aliases (alias, type, item) SELECT alias, 'catid' AS type, id AS item FROM #__kunena_categories WHERE alias!=''";

		foreach ($queries as $query)
		{
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}

			$rows += $db->getAffectedRows();
		}

		return $rows;
	}

	/**
	 * Check in existing categories if the alias is already taken.
	 *
	 * @param   mixed  $categoryId  category
	 * @param   mixed  $alias       alias
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 *
	 */
	public static function getAlias($categoryId, $alias): bool
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kunena_categories'))
			->where($db->quoteName('id') . ' = ' . $db->quote($categoryId))
			->andWhere($db->quoteName('alias') . ' = ' . $db->quote($alias));
		$db->setQuery($query);

		try
		{
			$categoryinternalItems = $db->loadAssoc();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return false;
		}

		if (\is_array($categoryinternalItems))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param   mixed  $a  a
	 * @param   mixed  $b  b
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	public static function compareByNameAsc($a, $b): int
	{
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b]))
		{
			return 0;
		}

		return StringHelper::strcasecmp(self::$_instances[$a]->name, self::$_instances[$b]->name);
	}

	/**
	 * @param   mixed  $a  a
	 * @param   mixed  $b  b
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	public static function compareByNameDesc($a, $b): int
	{
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b]))
		{
			return 0;
		}

		return StringHelper::strcasecmp(self::$_instances[$b]->name, self::$_instances[$a]->name);
	}

	/**
	 * @param   mixed  $original  origin
	 * @param   mixed  $strip     strip
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public static function stripName($original, $strip)
	{
		$strip = trim($strip);

		if (strpos($original, $strip) !== false)
		{
			$original = str_replace($strip, '', $original);
		}

		return $original;
	}
}

try
{
	KunenaCategoryHelper::initialize();
}
catch (Exception $e)
{
}
