<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Category
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class KunenaForumCategoryHelper
 * @since Kunena
 */
abstract class KunenaForumCategoryHelper
{
	/**
	 * @var KunenaForumCategory[]
	 * @since Kunena
	 */
	public static $_instances;

	/**
	 * @var mixed
	 * @since Kunena
	 */
	protected static $_tree;

	/**
	 * @var mixed
	 * @since Kunena
	 */
	protected static $allowed;

	/**
	 * Initialize class.
	 * @since Kunena
	 * @throws Exception
	 * @return void
	 */
	public static function initialize()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (KunenaConfig::getInstance()->get('cache_cat'))
		{
			$cache = Factory::getCache('com_kunena', 'callback');
			$cache->setLifeTime(180);
			self::$_instances = $cache->call(array('KunenaForumCategoryHelper', 'loadCategories'));
		}
		else
		{
			self::$_instances = self::loadCategories();
		}

		if (is_null(self::$_tree))
		{
			self::buildTree(self::$_instances);
		}

		self::$allowed = KunenaAccess::getInstance()->getAllowedCategories();
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return array|boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public static function &loadCategories()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$db    = Factory::getDBO();
		$query = "SELECT * FROM #__kunena_categories ORDER BY ordering, name";
		$db->setQuery($query);

		try
		{
			$instances = (array) $db->loadObjectList('id', 'KunenaForumCategory');
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		// TODO: remove this by adding level into table
		self::buildTree($instances);
		$heap = array(0);

		while (($parent = array_shift($heap)) !== null)
		{
			foreach (self::$_tree [$parent] as $id => $children)
			{
				if (!empty($children))
				{
					array_push($heap, $id);
				}

				$instances[$id]->level = $parent ? $instances[$parent]->level + 1 : 0;
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $instances;
	}

	/**
	 * @param   array $instances instances
	 *
	 * @since Kunena
	 * @return void
	 */
	protected static function buildTree(array &$instances)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		self::$_tree = array();

		foreach ($instances as $instance)
		{
			if (!isset(self::$_tree [(int) $instance->id]))
			{
				self::$_tree [$instance->id] = array();
			}

			self::$_tree [$instance->parent_id][$instance->id] = &self::$_tree [(int) $instance->id];
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @param   KunenaForumCategory $instance instance
	 *
	 * @internal
	 * @since Kunena
	 * @return void
	 */
	public static function register($instance)
	{
		if ($instance->exists())
		{
			$instance->level                  = isset(self::$_instances [$instance->parent_id]) ? self::$_instances [$instance->parent_id]->level + 1 : 0;
			self::$_instances [$instance->id] = $instance;

			if (!isset(self::$_tree [(int) $instance->id]))
			{
				self::$_tree [$instance->id]                       = array();
				self::$_tree [$instance->parent_id][$instance->id] = &self::$_tree [$instance->id];
			}
		}
		else
		{
			unset(self::$_instances [$instance->id]);
			unset(self::$_tree [$instance->id], self::$_tree [$instance->parent_id][$instance->id]);
		}
	}

	/**
	 * @param   mixed $user user
	 *
	 * @return KunenaForumCategory[]
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getSubscriptions($user = null)
	{
		$user  = KunenaUserHelper::get($user);
		$db    = Factory::getDBO();
		$query = "SELECT category_id FROM #__kunena_user_categories WHERE user_id={$db->Quote($user->userid)} AND subscribed=1";
		$db->setQuery($query);

		try
		{
			$subscribed = (array) $db->loadColumn();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return array();
		}

		return self::getCategories($subscribed);
	}

	/**
	 * @param   bool|array $ids       ids
	 * @param   bool       $reverse   reverse
	 * @param   string     $authorise authorise
	 *
	 * @return array|KunenaForumCategory[]
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getCategories($ids = false, $reverse = false, $authorise = 'read')
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($ids === false)
		{
			if ($authorise == 'none')
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

				return self::$_instances;
			}

			$ids = self::$_instances;
		}
		elseif (is_array($ids))
		{
			$ids = array_flip($ids);
		}
		else
		{
			$ids = array(intval($ids) => 1);
		}

		if (!$reverse)
		{
			$allowed = $authorise != 'none' ? array_intersect_key($ids, KunenaAccess::getInstance()->getAllowedCategories()) : $ids;
			$list    = array_intersect_key(self::$_instances, $allowed);

			if ($authorise != 'none' && $authorise != 'read')
			{
				foreach ($list as $category)
				{
					// @var KunenaForumCategory $category

					if (!$category->isAuthorised($authorise, null, true))
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
					// @var KunenaForumCategory $category

					if (!$category->isAuthorised($authorise, null, true))
					{
						unset($list [$category->id]);
					}
				}
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $list;
	}

	/**
	 * @param   array $ids   ids
	 * @param   bool  $value value
	 * @param   mixed $user  user
	 *
	 * @return integer
	 *
	 * @throws Exception
	 * @since    2.0.0
	 */
	public static function subscribe($ids, $value = true, $user = null)
	{
		$count = 0;

		// Pre-load all items
		$usercategories = KunenaForumCategoryUserHelper::getCategories($ids, $user);

		foreach ($usercategories as $usercategory)
		{
			if ($usercategory->subscribed != (int) $value)
			{
				$count++;
			}

			$usercategory->subscribed = (int) $value;
			$usercategory->save();
		}

		return $count;
	}

	/**
	 * Get subscribed categories ordered by latest post or parameter.
	 *
	 * @param   mixed $user       user
	 * @param   int   $limitstart limitstart
	 * @param   int   $limit      limit
	 * @param   array $params     params
	 *
	 * @return array (total, list)
	 *
	 * @throws Exception
	 * @since    2.0.0
	 */
	public static function getLatestSubscriptions($user, $limitstart = 0, $limit = 0, $params = array())
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		$db     = Factory::getDBO();
		$config = KunenaFactory::getConfig();

		if ($limit < 1)
		{
			$limit = $config->threads_per_page;
		}

		$userids = is_array($user) ? implode(",", $user) : KunenaUserHelper::get($user)->userid;
		$orderby = isset($params['orderby']) ? (string) $params['orderby'] : 'c.last_post_time DESC';
		$where   = isset($params['where']) ? (string) $params['where'] : '';
		$allowed = implode(',', array_keys(KunenaAccess::getInstance()->getAllowedCategories()));

		if (!$userids || !$allowed)
		{
			return array(0, array());
		}

		// Get total count
		$query = "SELECT COUNT(DISTINCT c.id) FROM #__kunena_categories AS c INNER JOIN #__kunena_user_categories AS u ON u.category_id = c.id WHERE u.user_id IN ({$userids}) AND u.category_id IN ({$allowed}) AND u.subscribed=1 {$where}";
		$db->setQuery($query);

		try
		{
			$total = (int) $db->loadResult();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return array(0, array());
		}

		if (!$total)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return array(0, array());
		}

		// If out of range, use last page
		if ($total < $limitstart)
		{
			$limitstart = intval($total / $limit) * $limit;
		}

		$query = "SELECT c.id FROM #__kunena_categories AS c INNER JOIN #__kunena_user_categories AS u ON u.category_id = c.id WHERE u.user_id IN ({$userids}) AND u.category_id IN ({$allowed}) AND u.subscribed=1 {$where} GROUP BY c.id ORDER BY {$orderby}";
		$db->setQuery($query, $limitstart, $limit);

		try
		{
			$subscribed = (array) $db->loadColumn();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return array(0, array());
		}

		$list = array();

		foreach ($subscribed as $id)
		{
			$list[$id] = self::$_instances[$id];
		}

		unset($subscribed);

		return array($total, $list);
	}

	/**
	 * @param   int|array $catids catids
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 * @return void
	 */
	public static function getNewTopics($catids)
	{
		$user = KunenaUserHelper::getMyself();

		if (!KunenaFactory::getConfig()->shownew || !$user->exists())
		{
			return;
		}

		$session    = KunenaFactory::getSession();
		$categories = self::getCategories($catids);
		$catlist    = array();

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
		$db      = Factory::getDBO();
		$query   = "SELECT t.category_id, COUNT(*) AS new
			FROM #__kunena_topics AS t
			LEFT JOIN #__kunena_user_categories AS uc ON uc.category_id=t.category_id AND uc.user_id={$db->Quote($user->userid)}
			LEFT JOIN #__kunena_user_read AS ur ON ur.topic_id=t.id AND ur.user_id={$db->Quote($user->userid)}
			WHERE t.category_id IN ($catlist) AND t.hold='0' AND t.last_post_time>{$db->Quote($session->getAllReadTime())}
				AND (uc.allreadtime IS NULL OR t.last_post_time>uc.allreadtime)
				AND (ur.topic_id IS NULL OR t.last_post_id != ur.message_id)
			GROUP BY category_id";
		$db->setQuery($query);

		try
		{
			$newlist = (array) $db->loadObjectList('category_id');
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return;
		}

		if (empty($newlist))
		{
			return;
		}

		$new = array();

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
	 * @param   string     $accesstype accesstype
	 * @param   bool|array $groupids   groupids
	 *
	 * @return KunenaForumCategory[]
	 * @since Kunena
	 */
	public static function getCategoriesByAccess($accesstype = 'joomla.level', $groupids = false)
	{
		if ($groupids === false)
		{
			// Continue
		}
		elseif (is_array($groupids))
		{
			$groupids = array_unique($groupids);
		}
		else
		{
			$groupids = array(intval($groupids));
		}

		$list = array();

		foreach (self::$_instances as $instance)
		{
			if ($instance->accesstype == $accesstype && ($groupids === false || in_array($instance->access, $groupids)))
			{
				$list [$instance->id] = $instance;
			}
		}

		return $list;
	}

	/**
	 * @param   int   $id     id
	 * @param   int   $levels levels
	 * @param   array $params params
	 *
	 * @return KunenaForumCategory[]
	 * @throws null
	 * @since Kunena
	 */
	public static function getParents($id = 0, $levels = 100, $params = array())
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		$unpublished = isset($params['unpublished']) ? (bool) $params['unpublished'] : 0;
		$action      = isset($params['action']) ? (string) $params['action'] : 'read';

		if (!isset(self::$_instances [$id]) || !self::$_instances [$id]->isAuthorised($action, null))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return array();
		}

		$list   = array();
		$parent = self::$_instances [$id]->parent_id;

		while ($parent && $levels--)
		{
			if (!isset(self::$_instances [$parent]))
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

				return array();
			}

			if (!$unpublished && self::$_instances[$parent]->published != 1)
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

				return array();
			}

			$list[$parent] = self::$_instances [$parent];

			$parent = self::$_instances [$parent]->parent_id;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return array_reverse($list, true);
	}

	/**
	 * @param   int   $levels levels
	 * @param   array $params params
	 *
	 * @return KunenaForumCategory[]
	 * @throws null
	 * @since Kunena
	 */
	public static function getOrphaned($levels = 0, $params = array())
	{
		$list = array();

		foreach (self::getCategoryTree(false) as $catid => $children)
		{
			if ($catid && !self::get($catid)->exists())
			{
				foreach (self::getChildren($catid, $levels, $params) as $category)
				{
					if ($category->parent_id == $catid)
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
	 * @param   int $parent parent
	 *
	 * @return array
	 * @since Kunena
	 */
	public static function getCategoryTree($parent = 0)
	{
		if ($parent === false)
		{
			return self::$_tree;
		}

		return isset(self::$_tree[$parent]) ? self::$_tree[$parent] : array();
	}

	/**
	 * Returns the global KunenaForumCategory object, only creating it if it doesn't already exist.
	 *
	 * @param   int  $identifier The category to load - Can be only an integer.
	 * @param   bool $reload     Reload category from the database.
	 *
	 * @return KunenaForumCategory    The Category object.
	 *
	 * @since    1.6
	 */
	public static function get($identifier = null, $reload = false)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if ($identifier instanceof KunenaForumCategory)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return $identifier;
		}

		if (!is_numeric($identifier))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
			$category = new KunenaForumCategory;
			$category->load();

			return $category;
		}

		$id = intval($identifier);

		if (empty(self::$_instances [$id]))
		{
			self::$_instances [$id] = new KunenaForumCategory(array('id' => $id));
			self::$_instances [$id]->load();
		}
		elseif ($reload)
		{
			self::$_instances [$id]->load();
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$_instances [$id];
	}

	/**
	 * @param   int   $parents parents
	 * @param   int   $levels  levels
	 * @param   array $params  params
	 *
	 * @return array|KunenaForumCategory[]
	 * @throws null
	 * @since Kunena
	 */
	public static function getChildren($parents = 0, $levels = 0, $params = array())
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!is_array($parents) && !isset(self::$_tree[$parents]))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

			return array();
		}

		static $defaults = array(
			'ordering'  => 'ordering',
			'direction' => 1,
			'search'    => '',
			'action'    => 'read',
			'selected'  => 0,
			'parents'   => true,
		);

		$parents             = (array) $parents;
		$params              = (array) $params;
		$optimize            = empty($params);
		$params              += $defaults;
		$params['published'] = isset($params['published']) ? (int) $params['published'] : (empty($params['unpublished']) ? 1 : null);

		$list = self::_getChildren($parents, $levels, $params, $optimize);

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $list;
	}

	/**
	 * @param   array $parents  parents
	 * @param   int   $levels   levels
	 * @param   array $params   params
	 * @param   bool  $optimize optimize
	 *
	 * @return array|KunenaForumCategory[]
	 * @throws null
	 * @since Kunena
	 */
	protected static function _getChildren(array $parents, $levels, array $params, $optimize)
	{
		$list = array();

		foreach ($parents as $parent)
		{
			if ($parent instanceof KunenaForumCategory)
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
							uksort($cats, array(__CLASS__, 'compareByNameAsc'));
						}
						else
						{
							uksort($cats, array(__CLASS__, 'compareByNameDesc'));
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
					$filtered |= isset($params['filter_title']) && (Joomla\String\StringHelper::stristr($instance->name, (string) $params['filter_title']) === false
							&& Joomla\String\StringHelper::stristr($instance->alias, (string) $params['filter_title']) === false);
					$filtered |= isset($params['filter_type']);
					$filtered |= isset($params['filter_access']) && ($instance->accesstype != 'joomla.level' || $instance->access != $params['filter_access']);
					$filtered |= isset($params['filter_locked']) && $instance->locked != (int) $params['filter_locked'];
					$filtered |= isset($params['filter_allow_polls']) && $instance->allow_polls != (int) $params['filter_allow_polls'];
					$filtered |= isset($params['filter_review']) && $instance->review != (int) $params['filter_review'];
					$filtered |= isset($params['filter_anonymous']) && $instance->allow_anonymous != (int) $params['filter_anonymous'];
				}

				if ($filtered && $params['action'] != 'admin')
				{
					continue;
				}

				$clist = array();

				if ($levels && !empty($children))
				{
					$clist = self::_getChildren(array($id), $levels - 1, $params, $optimize);
				}

				$allowed = $params['action'] == 'none' || ($params['action'] == 'read' && !empty(self::$allowed[$id])) || $instance->isAuthorised($params['action'], null);

				if (empty($clist) && !$allowed)
				{
					continue;
				}

				if (!empty($clist) || !$params['search'] || intval($params['search']) == $id || Joomla\String\StringHelper::stristr(Joomla\CMS\Language\Transliterate::utf8_latin_to_ascii($instance->name), (string) Joomla\CMS\Language\Transliterate::utf8_latin_to_ascii($params['search'])) ||  Joomla\String\StringHelper::stristr($instance->name, (string) $params['search']))
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
	 * @param   string|array $categories categories
	 *
	 * @return array
	 * @since Kunena
	 */
	public static function &getIndentation($categories)
	{
		$tree = new KunenaTree($categories);

		return $tree->getIndentation();
	}

	/**
	 * @param   string|array $categories categories
	 *
	 * @return boolean|integer
	 * @throws Exception
	 * @since Kunena
	 */
	public static function recount($categories = '')
	{
		$db = Factory::getDBO();

		if (is_array($categories))
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
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$rows = $db->getAffectedRows();

		// Update categories which have no published topics
		$query = "UPDATE #__kunena_categories AS c
			LEFT JOIN #__kunena_topics AS tt ON c.id=tt.category_id AND tt.hold=0
			SET c.numTopics=0,
				c.numPosts=0,
				c.last_topic_id=0,
				c.last_post_id=0,
				c.last_post_time=0
			WHERE tt.id IS NULL";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
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
	 * @return boolean|integer
	 * @throws Exception
	 * @since Kunena
	 */
	public static function fixAliases()
	{
		$db = Factory::getDBO();

		$rows    = 0;
		$queries = array();

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
			catch (JDatabaseExceptionExecuting $e)
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
	 * @param   mixed $category_id category
	 * @param   mixed $alias       alias
	 *
	 * @return boolean|void
	 * @throws Exception
	 * @since Kunena
	 */
	public static function getAlias($category_id, $alias)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__kunena_categories'))->where($db->quoteName('alias') . " = " . $db->quote($alias));
		$db->setQuery($query);

		try
		{
			$category_items = $db->loadAssoc();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage());

			return false;
		}

		if (is_array($category_items))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param   mixed $a a
	 * @param   mixed $b b
	 *
	 * @return integer
	 * @since Kunena
	 */
	public static function compareByNameAsc($a, $b)
	{
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b]))
		{
			return 0;
		}

		return Joomla\String\StringHelper::strcasecmp(self::$_instances[$a]->name, self::$_instances[$b]->name);
	}

	/**
	 * @param   mixed $a a
	 * @param   mixed $b b
	 *
	 * @return integer
	 * @since Kunena
	 */
	public static function compareByNameDesc($a, $b)
	{
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b]))
		{
			return 0;
		}

		return Joomla\String\StringHelper::strcasecmp(self::$_instances[$b]->name, self::$_instances[$a]->name);
	}

	/**
	 * @param   mixed $original origin
	 * @param   mixed $strip    strip
	 *
	 * @return mixed
	 * @since Kunena
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

KunenaForumCategoryHelper::initialize();
