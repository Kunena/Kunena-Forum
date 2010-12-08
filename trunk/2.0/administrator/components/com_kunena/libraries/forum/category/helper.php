<?php
/**
 * @version $Id$
 * Kunena Component - KunenaForumCategoryHelper class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.forum.category');

/**
 * Kunena Forum Category Helper Class
 */
class KunenaForumCategoryHelper {
	// Global for every instance
	protected static $_instances = false;
	protected static $_tree = array ();
	protected static $_names = array ();

	// Static class
	private function __construct() {}

	/**
	 * Returns the global KunenaForumCategory object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$id		The category to load - Can be only an integer.
	 * @return	KunenaForumCategory	The Category object.
	 * @since	1.6
	 */
	static public function get($identifier = null, $reload = false) {
		if (self::$_instances === false) {
			self::loadCategories();
		}

		if ($identifier instanceof KunenaForumCategory) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new KunenaForumCategory ();

		if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumCategory ( $id );
		}

		return self::$_instances [$id];
	}

	static public function getNewTopics($catids) {
		$session = KunenaFactory::getSession ();
		$readlist = $session->readtopics;
		$prevCheck = $session->lasttime;

		$catlist = implode(',', $catids);
		$db = JFactory::getDBO ();
		$query = "SELECT category_id, COUNT(*) AS new
			FROM #__kunena_topics
			WHERE category_id IN ($catlist) AND hold='0' AND last_post_time>{$db->Quote($prevCheck)} AND id NOT IN ({$readlist})
			GROUP BY category_id";
		$db->setQuery ( $query );
		$newlist = $db->loadObjectList ('category_id');
		if (KunenaError::checkDatabaseError()) return;
		$new = array();
		foreach ($catids AS $id) {
			if (isset($newlist[$id]))
				$new[$id] = $newlist[$id]->new;
			else
				$new[$id] = 0;
		}
		return $new;
	}

	static public function getCategoriesByAccess($groupids = false, $accesstype='joomla') {
		if (self::$_instances === false) {
			self::loadCategories();
		}

		if ($groupids === false) {
			return self::$_instances;
		} elseif (is_array ($groupids) ) {
			$groupids = array_unique($groupids);
		} else {
			$groupids = array(intval($groupids));
		}

		$list = array ();
		foreach ( self::$_instances as $instance ) {
			if ($instance->accesstype == $accesstype && ($groupids===false || in_array($instance->access, $groupids))) {
				$list [$instance->id] = $instance;
			}
		}

		return $list;
	}

	static public function getCategories($ids = false, $reverse = false, $authorise='read') {
		if (self::$_instances === false) {
			self::loadCategories();
		}

		if ($ids === false) {
			return self::$_instances;
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array(intval($ids));
		}

		$list = array ();
		if (!$reverse) {
			foreach ( $ids as $id ) {
				if (isset(self::$_instances [$id]) && self::$_instances [$id]->authorise($authorise, null, true)) {
					$list [$id] = self::$_instances [$id];
				}
			}
		} else {
			foreach ( self::$_instances as $category ) {
				if (!in_array($category->id, $ids) && $category->authorise($authorise, null, true)) {
					$list [$category->id] = $category;
				}
			}
		}

		return $list;
	}

	static public function getParents($id = 0, $levels = 10, $params = array()) {
		if (self::$_instances === false) {
			self::loadCategories();
		}
		$unpublished = isset($params['unpublished']) ? (bool) $params['unpublished'] : 0;
		$action = isset($params['action']) ? (string) $params['action'] : 'read';

		if (!isset(self::$_instances [$id]) || !self::$_instances [$id]->authorise($action, null, true)) return array();
		$list = array ();
		$parent = self::$_instances [$id]->parent_id;
		while ($parent && $levels--) {
			if (!isset(self::$_instances [$parent])) return array();
			if (!$unpublished && !self::$_instances [$parent]->published) return array();
			array_unshift($list, self::$_instances [$parent]);

			$parent = self::$_instances [$parent]->parent_id;
		}
		return $list;
	}

	static public function getChildren($parents = 0, $levels = 0, $params = array()) {
		if (self::$_instances === false) {
			self::loadCategories();
		}

		$ordering = isset($params['ordering']) ? (string) $params['ordering'] : 'ordering';
		$direction = isset($params['direction']) ? (int) $params['direction'] : 1;
		$search = isset($params['search']) ? (string) $params['search'] : '';
		$unpublished = isset($params['unpublished']) ? (bool) $params['unpublished'] : 0;
		$action = isset($params['action']) ? (string) $params['action'] : 'read';
		$selected = isset($params['selected']) ? (int) $params['selected'] : 0;

		if (!is_array($parents))
			$parents = array($parents);

		$list = array ();
		foreach ( $parents as $parent ) {
			if ($parent instanceof KunenaForumCategory) {
				$parent = $parent->id;
			}
			if (! isset ( self::$_tree [$parent] ))
				continue;
			$cats = self::$_tree [$parent];
			switch ($ordering) {
				case 'catid' :
					if ($direction > 0)
						ksort ( $cats );
					else
						krsort ( $cats );
					break;
				case 'name' :
					if ($direction > 0)
						uksort ( $cats, array (__CLASS__, 'compareByNameAsc' ) );
					else
						uksort ( $cats, array (__CLASS__, 'compareByNameDesc' ) );
					break;
				case 'ordering' :
				default :
					if ($direction < 0)
						$cats = array_reverse ( $cats, true );
			}

			foreach ( $cats as $id => $children ) {
				if (! isset ( self::$_instances [$id] ))
					continue;
				if (! $unpublished && ! self::$_instances [$id]->published)
					continue;
				if ($id == $selected)
					continue;
				$clist = array ();
				if ($levels && ! empty ( $children )) {
					$clist = self::getChildren ( $id, $levels - 1, $params );
				}
				if (empty ( $clist ) && ! self::$_instances [$id]->authorise ( $action, null, true ))
					continue;
				if (! empty ( $clist ) || ! $search || intval ( $search ) == $id || JString::stristr ( self::$_instances [$id]->name, ( string ) $search )) {
					$list [$id] = self::$_instances [$id];
					$list += $clist;
				}
			}
		}
		return $list;
	}

	static public function getCategoryTree($parent = 0) {
		if (self::$_instances === false) {
			self::loadCategories();
		}
		if ($parent === false) {
			return self::$_tree;
		}
		return isset(self::$_tree[$parent]) ? self::$_tree[$parent] : array();
	}

	static public function recount() {
		$db = JFactory::getDBO ();

		$db->setQuery ( "UPDATE #__kunena_categories
			SET numTopics=0, numPosts=0, last_topic_id=0, last_topic_subject='', last_post_id=0, last_post_time=0,
			last_post_userid=0, last_post_message='', last_post_guest_name=''");
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Update category post count
		$db->setQuery ( "INSERT INTO #__kunena_categories (id, numTopics, numPosts, last_topic_id)
			SELECT c.id, COUNT(*) AS numTopics, SUM(tt.posts) AS numPosts, MAX(tt.id) AS last_topic_id
			FROM #__kunena_topics as tt
			INNER JOIN #__kunena_categories AS c ON c.id=tt.category_id
			WHERE tt.hold=0 AND tt.moved_id=0
			GROUP BY tt.category_id
			ON DUPLICATE KEY UPDATE numTopics=VALUES(numTopics), numPosts=VALUES(numPosts), last_topic_id=VALUES(last_topic_id)" );
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Update last post info
		$db->setQuery ( "UPDATE #__kunena_categories AS c, #__kunena_topics AS t
			SET c.last_topic_subject = t.subject,
				c.last_post_id = t.last_post_id,
				c.last_post_time = t.last_post_time,
				c.last_post_userid = t.last_post_userid,
				c.last_post_message = t.last_post_message,
				c.last_post_guest_name = t.last_post_guest_name
			WHERE c.last_topic_id = t.id");
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		$cache = JFactory::getCache('com_kunena', 'output');
		$cache->clean('categories');
	}

	// Internal functions:

	static protected function loadCategories() {
		$cache = JFactory::getCache('com_kunena', 'output');
		$data = $cache->get('instances', 'com_kunena.categories');
		if ($data !== false) {
			list(self::$_instances, self::$_tree, self::$_names) = unserialize($data);
			return;
		}

		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_categories ORDER BY ordering, name";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ();
		KunenaError::checkDatabaseError ();

		self::$_instances = array();
		foreach ( $results as $category ) {
			$instance = new KunenaForumCategory ();
			$instance->bind ( $category );
			$instance->exists (true);
			self::$_instances [(int)$instance->id] = $instance;

			if (!isset(self::$_tree [(int)$instance->id])) {
				self::$_tree [(int)$instance->id] = array();
			}
			self::$_tree [(int)$instance->parent_id][(int)$instance->id] = &self::$_tree [(int)$instance->id];
			self::$_names [(int)$instance->id] = $instance->name;
		}
		unset ($results);

		// TODO: remove this by adding level and section into table
		$heap = array(0);
		while (($parent = array_shift($heap)) !== null) {
			foreach (self::$_tree [$parent] as $id=>$children) {
				if (!empty($children)) array_push($heap, $id);
				self::$_instances [$id]->level = $parent ? self::$_instances [$parent]->level+1 : 0;
				self::$_instances [$id]->section = !self::$_instances [$id]->level;
			}
		}
		$cache->store(serialize(array(self::$_instances, self::$_tree, self::$_names)), 'instances', 'com_kunena.categories');
	}

	static public function compareByNameAsc($a, $b) {
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b])) return 0;
		return JString::strcasecmp(self::$_instances[$a]->name, self::$_instances[$b]->name);
	}

	static public function compareByNameDesc($a, $b) {
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b])) return 0;
		return JString::strcasecmp(self::$_instances[$b]->name, self::$_instances[$a]->name);
	}
}
