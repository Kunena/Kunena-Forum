<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Category
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumCategoryHelper
 */
abstract class KunenaForumCategoryHelper
{
	/**
	 * @var KunenaForumCategory[]
	 */
	public static $_instances;
	protected static $_tree;
	protected static $allowed;

	/**
	 * Initialize class.
	 */
	static public function initialize()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if (KunenaConfig::getInstance()->get('cache_cat'))
		{
			// FIXME: Experimental caching.
			/** @var JCache|JCacheControllerCallback $cache */
			$cache = JFactory::getCache('com_kunena', 'callback');
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
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
	}

	/**
	 * Returns the global KunenaForumCategory object, only creating it if it doesn't already exist.
	 *
	 * @param int  $identifier	The category to load - Can be only an integer.
	 * @param bool $reload		Reload category from the database.
	 *
	 * @return KunenaForumCategory	The Category object.
	 *
	 * @since	1.6
	 */
	static public function get($identifier = null, $reload = false)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($identifier instanceof KunenaForumCategory)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

			return $identifier;
		}

		if (!is_numeric($identifier))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			$category = new KunenaForumCategory ();
			$category->load();

			return $category;
		}

		$id = intval ( $identifier );
		if (empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumCategory (array('id'=>$id));
			self::$_instances [$id]->load();
		} elseif ($reload) {
			self::$_instances [$id]->load();
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return self::$_instances [$id];
	}

	/**
	 * @param KunenaForumCategory $instance
	 * @internal
	 */
	static public function register($instance)
	{
		if ($instance->exists())
		{
			$instance->level = isset(self::$_instances [$instance->parent_id]) ? self::$_instances [$instance->parent_id]->level+1 : 0;
			self::$_instances [$instance->id] = $instance;

			if (!isset(self::$_tree [(int)$instance->id]))
			{
				self::$_tree [$instance->id] = array();
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
	 * @param mixed $user
	 *
	 * @return KunenaForumCategory[]
	 */
	static public function getSubscriptions($user = null)
	{
		$user = KunenaUserHelper::get($user);
		$db = JFactory::getDBO ();
		$query = "SELECT category_id FROM #__kunena_user_categories WHERE user_id={$db->Quote($user->userid)} AND subscribed=1";
		$db->setQuery ( $query );
		$subscribed = (array) $db->loadColumn ();

		if (KunenaError::checkDatabaseError())
		{
			return array();
		}

		return KunenaForumCategoryHelper::getCategories($subscribed);
	}

	/**
	 * @param array $ids
	 * @param bool  $value
	 * @param mixed $user
	 *
	 * @return int
	 *
	 * @since	2.0.0
	 */
	public static function subscribe($ids, $value = true, $user = null)
	{
		$count = 0;
		// Pre-load all items
		$usercategories = KunenaForumCategoryUserHelper::getCategories($ids, $user);

		foreach ($usercategories as $usercategory)
		{
			if ($usercategory->subscribed != (int)$value) $count++;
			$usercategory->subscribed = (int)$value;
			$usercategory->save();
		}

		return $count;
	}

	/**
	 * Get subscribed categories ordered by latest post or parameter.
	 *
	 * @param mixed $user
	 * @param int   $limitstart
	 * @param int   $limit
	 * @param array $params
	 *
	 * @return array (total, list)
	 *
	 * @since	2.0.0
	 */
	static public function getLatestSubscriptions($user, $limitstart = 0, $limit = 0, $params = array())
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$db = JFactory::getDBO ();
		$config = KunenaFactory::getConfig ();

		if ($limit < 1)
		{
			$limit = $config->threads_per_page;
		}

		$userids = is_array($user) ? implode(",", $user) : KunenaUserHelper::get($user)->userid;
		$orderby = isset($params['orderby']) ? (string) $params['orderby'] : 'c.last_post_time DESC';
		$where = isset($params['where']) ? (string) $params['where'] : '';
		$allowed = implode(',', array_keys(KunenaAccess::getInstance()->getAllowedCategories()));

		if (!$userids || !$allowed)
		{
			return array(0, array());
		}

		// Get total count
		$query = "SELECT COUNT(DISTINCT c.id) FROM #__kunena_categories AS c INNER JOIN #__kunena_user_categories AS u ON u.category_id = c.id WHERE u.user_id IN ({$userids}) AND u.category_id IN ({$allowed}) AND u.subscribed=1 {$where}";
		$db->setQuery ( $query );
		$total = ( int ) $db->loadResult ();

		if (KunenaError::checkDatabaseError() || !$total)
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

			return array(0, array());
		}

		// If out of range, use last page
		if ($total < $limitstart)
		{
			$limitstart = intval($total / $limit) * $limit;
		}

		$query = "SELECT c.id FROM #__kunena_categories AS c INNER JOIN #__kunena_user_categories AS u ON u.category_id = c.id WHERE u.user_id IN ({$userids}) AND u.category_id IN ({$allowed}) AND u.subscribed=1 {$where} GROUP BY c.id ORDER BY {$orderby}";
		$db->setQuery ( $query , $limitstart, $limit );
		$subscribed = (array) $db->loadColumn ();

		if (KunenaError::checkDatabaseError())
		{
			return array(0, array());
		}

		$list = array();
		foreach ( $subscribed as $id )
		{
			$list[$id] = self::$_instances[$id];
		}

		unset ($subscribed);

		return array($total, $list);
	}

	/**
	 * @param int|array $catids
	 */
	static public function getNewTopics($catids)
	{
		$user = KunenaUserHelper::getMyself();

		if (!KunenaFactory::getConfig()->shownew || !$user->exists())
		{
			return;
		}

		$session = KunenaFactory::getSession ();
		$categories = self::getCategories($catids);
		$catlist = array();

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
		$db = JFactory::getDBO ();
		$query = "SELECT t.category_id, COUNT(*) AS new
			FROM #__kunena_topics AS t
			LEFT JOIN #__kunena_user_categories AS uc ON uc.category_id=t.category_id AND uc.user_id={$db->Quote($user->userid)}
			LEFT JOIN #__kunena_user_read AS ur ON ur.topic_id=t.id AND ur.user_id={$db->Quote($user->userid)}
			WHERE t.category_id IN ($catlist) AND t.hold='0' AND t.last_post_time>{$db->Quote($session->getAllReadTime())}
				AND (uc.allreadtime IS NULL OR t.last_post_time>uc.allreadtime)
				AND (ur.topic_id IS NULL OR t.last_post_id != ur.message_id)
			GROUP BY category_id";
		$db->setQuery ( $query );
		$newlist = (array) $db->loadObjectList ('category_id');

		if (KunenaError::checkDatabaseError())
		{
			return;
		}

		if (empty($newlist))
		{
			return;
		}

		$new = array();
		foreach ($newlist AS $id=>$item)
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
	 * @param string $accesstype
	 * @param bool|array   $groupids
	 *
	 * @return KunenaForumCategory[]
	 */
	static public function getCategoriesByAccess($accesstype='joomla.level', $groupids = false)
	{
		if ($groupids === false)
		{
			// Continue
		}
		elseif (is_array ($groupids) )
		{
			$groupids = array_unique($groupids);
		}
		else
		{
			$groupids = array(intval($groupids));
		}

		$list = array ();
		foreach ( self::$_instances as $instance )
		{
			if ($instance->accesstype == $accesstype && ($groupids===false || in_array($instance->access, $groupids)))
			{
				$list [$instance->id] = $instance;
			}
		}

		return $list;
	}

	/**
	 * @param bool|array   $ids
	 * @param bool   $reverse
	 * @param string $authorise
	 *
	 * @return array|KunenaForumCategory[]
	 */
	static public function getCategories($ids = false, $reverse = false, $authorise='read')
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($ids === false)
		{
			if ($authorise == 'none')
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
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
			$ids = array(intval($ids)=>1);
		}

		if (!$reverse)
		{
			$allowed = $authorise != 'none' ? array_intersect_key($ids, KunenaAccess::getInstance()->getAllowedCategories()) : $ids;
			$list = array_intersect_key(self::$_instances, $allowed);

			if ($authorise != 'none' && $authorise != 'read')
			{
				foreach ( $list as $category )
				{
					/** @var KunenaForumCategory $category */
					if (!$category->authorise($authorise, null, true))
					{
						unset($list [$category->id]);
					}
				}
			}
		}
		else
		{
			$allowed = $authorise != 'none' ? array_intersect_key(self::$_instances, KunenaAccess::getInstance()->getAllowedCategories()) : self::$_instances;
			$list = array_diff_key($allowed, $ids);

			if ($authorise != 'none' && $authorise != 'read')
			{
				foreach ( $list as $category )
				{
					/** @var KunenaForumCategory $category */
					if (!$category->authorise($authorise, null, true))
					{
						unset($list [$category->id]);
					}
				}
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $list;
	}

	/**
	 * @param int   $id
	 * @param int   $levels
	 * @param array $params
	 *
	 * @return KunenaForumCategory[]
	 */
	static public function getParents($id = 0, $levels = 100, $params = array())
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		$unpublished = isset($params['unpublished']) ? (bool) $params['unpublished'] : 0;
		$action = isset($params['action']) ? (string) $params['action'] : 'read';

		if (!isset(self::$_instances [$id]) || !self::$_instances [$id]->authorise($action, null, true))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
			return array();
		}

		$list = array ();
		$parent = self::$_instances [$id]->parent_id;

		while ($parent && $levels--)
		{
			if (!isset(self::$_instances [$parent]))
			{
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
				return array();
			}

			if (!$unpublished && self::$_instances[$parent]->published != 1) {
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
				return array();
			}

			$list[$parent] = self::$_instances [$parent];

			$parent = self::$_instances [$parent]->parent_id;
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return array_reverse($list, true);
	}

	/**
	 * @param int   $parents
	 * @param int   $levels
	 * @param array $params
	 *
	 * @return array|KunenaForumCategory[]
	 */
	static public function getChildren($parents = 0, $levels = 0, $params = array())
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if (!is_array($parents) && !isset(self::$_tree[$parents]))
		{
			KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

			return array();
		}

		static $defaults = array(
			'ordering'=>'ordering',
			'direction'=>1,
			'search'=>'',
			'action'=>'read',
			'selected'=>0,
			'parents'=>true,
		);

		$parents = (array) $parents;
		$params = (array) $params;
		$optimize = empty($params);
		$params += $defaults;
		$params['published'] = isset($params['published']) ? (int) $params['published'] : (empty($params['unpublished']) ? 1 : null);

		$list = self::_getChildren($parents, $levels, $params, $optimize);

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $list;
	}

	/**
	 * @param array $parents
	 * @param int   $levels
	 * @param array $params
	 * @param bool  $optimize
	 *
	 * @return array|KunenaForumCategory[]
	 */
	static protected function _getChildren(array $parents, $levels, array $params, $optimize)
	{
		$list = array ();
		foreach ( $parents as $parent )
		{
			if ($parent instanceof KunenaForumCategory)
			{
				$parent = $parent->id;
			}

			if (! isset ( self::$_tree [$parent] ))
			{
				continue;
			}

			$cats = self::$_tree [$parent];

			if (!$optimize) switch ($params['ordering'])
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

			foreach ( $cats as $id => $children )
			{
				if (! isset ( self::$_instances [$id] ))
				{
					continue;
				}

				if ($id == $params['selected'])
				{
					continue;
				}

				$instance = self::$_instances[$id];

				$filtered  = isset($params['published']) && $instance->published != $params['published'];

				if (!$optimize)
				{
					$filtered |= isset($params['filter_title']) && (JString::stristr($instance->name, (string) $params['filter_title']) === false
							&& JString::stristr($instance->alias, (string) $params['filter_title']) === false);
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

				$clist = array ();

				if ($levels && ! empty ( $children ))
				{
					$clist = self::_getChildren(array($id), $levels - 1, $params, $optimize);
				}

				$allowed = $params['action'] == 'none' || ($params['action'] == 'read' && !empty(self::$allowed[$id])) || $instance->authorise($params['action'], null, true);

				if (empty($clist) && !$allowed)
				{
					continue;
				}

				if (! empty ( $clist ) || ! $params['search'] || intval ( $params['search'] ) == $id || JString::stristr ( $instance->name, ( string ) $params['search'] )) {
					if (!$filtered && (empty ( $clist ) || $params['parents'])) $list [$id] = $instance;
					$list += $clist;
				}
			}
		}

		return $list;
	}

	/**
	 * @param int   $levels
	 * @param array $params
	 *
	 * @return KunenaForumCategory[]
	 */
	static public function getOrphaned($levels = 0, $params = array())
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
						$category->name = JText::_ ( 'COM_KUNENA_CATEGORY_ORPHAN' ) . ' : ' . $category->name;
					}
					$list[$category->id] = $category;
				}
			}
		}

		return $list;
	}

	/**
	 * @param int $parent
	 *
	 * @return array
	 */
	static public function getCategoryTree($parent = 0)
	{
		if ($parent === false)
		{
			return self::$_tree;
		}

		return isset(self::$_tree[$parent]) ? self::$_tree[$parent] : array();
	}

	/**
	 * @param $categories
	 *
	 * @return array
	 */
	static public function &getIndentation($categories)
	{
		$tree = new KunenaTree($categories);

		return $tree->getIndentation();
	}

	/**
	 * @param string|array $categories
	 *
	 * @return bool|int
	 */
	static public function recount($categories = '')
	{
		$db = JFactory::getDBO ();

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
		$db->setQuery ( $query );
		$db->query ();

		if (KunenaError::checkDatabaseError ())
		{
			return false;
		}

		$rows = $db->getAffectedRows ();

		// Update categories which have no published topics
		$query = "UPDATE #__kunena_categories AS c
			LEFT JOIN #__kunena_topics AS tt ON c.id=tt.category_id AND tt.hold=0
			SET c.numTopics=0,
				c.numPosts=0,
				c.last_topic_id=0,
				c.last_post_id=0,
				c.last_post_time=0
			WHERE tt.id IS NULL";
		$db->setQuery ( $query );
		$db->query ();

		if (KunenaError::checkDatabaseError ())
		{
			return false;
		}

		$rows += $db->getAffectedRows ();

		if ($rows)
		{
			// If something changed, clean our cache
			KunenaCacheHelper::clearCategories();
		}

		return $rows;
	}

	/**
	 * @return bool|int
	 */
	static public function fixAliases()
	{
		$db = JFactory::getDBO ();

		$rows = 0;
		$queries = array();
		// Fix wrong category id in aliases
		$queries[] = "UPDATE #__kunena_aliases AS a INNER JOIN #__kunena_categories AS c ON a.alias = c.alias SET a.item = c.id WHERE a.type='catid'";
		// Delete aliases from non-existing categories
		$queries[] = "DELETE a FROM #__kunena_aliases AS a LEFT JOIN #__kunena_categories AS c ON a.item = c.id WHERE a.type='catid' AND c.id IS NULL";
		// Add missing category aliases
		$queries[] = "INSERT IGNORE INTO #__kunena_aliases (alias, type, item) SELECT alias, 'catid' AS type, id AS item FROM #__kunena_categories WHERE alias!=''";

		foreach ($queries as $query)
		{
			$db->setQuery ( $query );
			$db->query ();

			if (KunenaError::checkDatabaseError ())
			{
				return false;
			}

			$rows += $db->getAffectedRows ();
		}

		return $rows;
	}

	/**
	 * Check if alias is already taken.
	 *
	 * @param $category_id
	 * @param $alias
	 *
	 * @return bool
	 */
	static public function getAlias($category_id, $alias)
	{
		$db = JFactory::getDbo();
		$query = "SELECT * FROM #__kunena_categories WHERE id = {$db->quote($category_id)} AND alias = {$db->quote($alias)}";
		$db->setQuery($query);
		$category_items = $db->loadAssoc();

		// Check for an error message.
		if ($db->getErrorNum())
		{
			return false;
		}

		if (is_array($category_items))
		{
			return true;
		}

		return false;
	}

	// Internal functions:

	static public function &loadCategories()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_categories ORDER BY ordering, name";
		$db->setQuery ( $query );
		$instances = (array) $db->loadObjectList('id', 'KunenaForumCategory');
		KunenaError::checkDatabaseError ();

		// TODO: remove this by adding level into table
		self::buildTree($instances);
		$heap = array(0);

		while (($parent = array_shift($heap)) !== null)
		{
			foreach (self::$_tree [$parent] as $id=>$children)
			{
				if (!empty($children))
				{
					array_push($heap, $id);
				}

				$instances[$id]->level = $parent ? $instances[$parent]->level+1 : 0;
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $instances;
	}

	static protected function buildTree(array &$instances)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		self::$_tree = array();

		foreach ($instances as $instance)
		{
			if (!isset(self::$_tree [(int)$instance->id]))
			{
				self::$_tree [$instance->id] = array();
			}

			self::$_tree [$instance->parent_id][$instance->id] = &self::$_tree [(int)$instance->id];
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
	}
	/**
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	static public function compareByNameAsc($a, $b)
	{
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b]))
		{
			return 0;
		}

		return JString::strcasecmp(self::$_instances[$a]->name, self::$_instances[$b]->name);
	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	static public function compareByNameDesc($a, $b)
	{
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b]))
		{
			return 0;
		}

		return JString::strcasecmp(self::$_instances[$b]->name, self::$_instances[$a]->name);
	}

	static public function stripName($original, $strip)
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
