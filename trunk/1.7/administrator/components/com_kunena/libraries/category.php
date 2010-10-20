<?php
/**
 * @version $Id$
 * Kunena Component - KunenaUser class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

kimport ('error');

/**
 * Kunena Category Class
 */
class KunenaCategory extends JObject {
	// Global for every instance
	protected static $_instances = false;
	protected static $_tree = array ();
	protected static $_names = array ();

	protected $_exists = false;
	protected $_db = null;
	protected $_topics = false;
	protected $_posts = false;
	protected $_lastid = false;


	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($identifier = 0) {
		// Always load the category -- if category does not exist: fill empty data
		$this->_db = JFactory::getDBO ();
		$this->load ( $identifier );
	}

	/**
	 * Returns the global KunenaCategory object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$id		The category to load - Can be only an integer.
	 * @return	KunenaCategory	The Category object.
	 * @since	1.6
	 */
	static public function getInstance($identifier = null, $reset = false) {
		if (self::$_instances === false) {
			self::loadCategories();
		}

		$c = __CLASS__;

		if ($identifier instanceof KunenaCategory) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new $c ();

		if (! $reset && empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new $c ( $id );
		}

		return self::$_instances [$id];
	}

	public function exists() {
		return $this->_exists;
	}

	public function getTopics() {
		$this->buildInfo();
		return $this->_topics;
	}

	public function getPosts() {
		$this->buildInfo();
		return $this->_posts;
	}

	public function getLastPosted() {
		$this->buildInfo();
		return self::$_instances [$this->_lastid ];
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

	protected function buildInfo() {
		if ($this->_topics !== false)
			return;
		$this->_topics = $this->numTopics;
		$this->_posts = $this->numPosts;
		$this->_lastid = $this->id;
		$categories = self::getChildren($this->id);
		foreach ($categories as $category) {
			$category->buildInfo();
			$this->_topics += $category->numTopics;
			$this->_posts += $category->numPosts;
			if (self::$_instances [$this->_lastid]->last_post_time < $category->last_post_time)
				$this->_lastid = $category->id;
		}
	}

	static protected function loadCategories() {
		$c = __CLASS__;
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_categories ORDER BY ordering, name";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ();
		KunenaError::checkDatabaseError ();

		self::$_instances = array();
		foreach ( $results as $category ) {
			$instance = new $c ();
			$instance->bind ( $category );
			$instance->_exists = true;
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
	}

	static public function getCategories($ids = false, $reverse = false) {
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
				if (isset(self::$_instances [$id]) && self::$_instances [$id]->authorize()) {
					$list [$id] = self::$_instances [$id];
				}
			}
		} else {
			foreach ( self::$_instances as $category ) {
				if (!in_array($category->id, $ids) && $category->authorize()) {
					$list [$category->id] = $category;
				}
			}
		}

		return $list;
	}

	static public function compareByNameAsc($a, $b) {
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b])) return 0;
		return JString::strcasecmp(self::$_instances[$a]->name, self::$_instances[$b]->name);
	}

	static public function compareByNameDesc($a, $b) {
		if (!isset(self::$_instances[$a]) || !isset(self::$_instances[$b])) return 0;
		return JString::strcasecmp(self::$_instances[$b]->name, self::$_instances[$a]->name);
	}

	public function getParent() {
		if (!isset(self::$_instances [$this->parent_id])) {
			$c = __CLASS__;
			$instance = new $c();
			$instance->name = JText::_ ( 'COM_KUNENA_TOPLEVEL' );
			$instance->_exists = true;
			return $instance;
		}
		return self::$_instances [$this->parent_id];
	}

	static public function getParents($id = 0, $levels = 10, $params = array()) {
		if (self::$_instances === false) {
			self::loadCategories();
		}
		$unpublished = isset($params['unpublished']) ? (bool) $params['unpublished'] : 0;
		$action = isset($params['action']) ? (string) $params['action'] : 'read';

		if (!isset(self::$_instances [$id]) || !self::$_instances [$id]->authorize($action)) return array();
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
						uksort ( $cats, array ('KunenaCategory', 'compareByNameAsc' ) );
					else
						uksort ( $cats, array ('KunenaCategory', 'compareByNameDesc' ) );
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
				if (empty ( $clist ) && ! self::$_instances [$id]->authorize ( $action ))
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

	public function authorize($action='read', $userid=null) {
		if ($action == 'none') return true;
		$access = KunenaFactory::getAccessControl();
		$catids = $access->getAllowedCategories($userid, $action);
		return !empty($catids[0]) || !empty($catids[$this->id]);
	}

	/**
	 * Get userids, who can administrate this category
	 **/
	public function getAdmins($includeGlobal = true) {
		$access = KunenaFactory::getAccessControl();
		$userlist = array();
		if (!empty($this->catid)) $userlist = $access->getAdmins($this->catid);
		if ($includeGlobal) $userlist += $access->getAdmins();
		return $userlist;
	}

	/**
	 * Get userids, who can moderate this category
	 **/
	public function getModerators($includeGlobal = true) {
		$access = KunenaFactory::getAccessControl();
		$userlist = array();
		if (!empty($this->id)) $userlist = $access->getModerators($this->id);
		if ($includeGlobal) $userlist += $access->getModerators();
		foreach ($userlist as $userid => $val) {
			$userlist[$userid] = $userid;
		}
		return $userlist;
	}

	/**
	 * Change user moderator status
	 **/
	public function setModerator($user, $status = 1) {
		// Do not allow this action if current user isn't admin in this category
		if (!KunenaFactory::getUser()->isAdmin($this->id)) {
			$this->setError ( JText::sprintf('COM_KUNENA_ERROR_NOT_CATEGORY_ADMIN', $this->name) );
			return false;
		}

		// Check if category exists
		if (!$this->exists()) return false;

		// Check if user exists
		$user = KunenaFactory::getUser($user);
		if (!($user instanceof KunenaUser) || !$user->exists()) {
			return false;
		}

		$catids = $user->getAllowedCategories('moderate');

		// Do not touch global moderators
		if (!empty($catids[0])) {
			return true;
		}

		// If the user state remains the same, do nothing
		if (empty($catids[$this->catid]) == $status) {
			return true;
		}

		$db = JFactory::getDBO ();
		if ($status == 1) {
			$query = "INSERT INTO #__kunena_moderation (catid, userid) VALUES  ({$db->quote($this->id)}, {$db->quote($user->userid)})";
			$db->setQuery ( $query );
			$db->query ();
			// Finally set user to be a moderator
			if (!KunenaError::checkDatabaseError () && $user->moderator == 0) {
				$user->moderator = 1;
				$user->save();
			}
		} else {
			$query = "DELETE FROM #__kunena_moderation WHERE catid={$db->Quote($this->id)} AND userid={$db->Quote($user->userid)}";
			$db->setQuery ( $query );
			$db->query ();
			unset($catids[$this->id]);
			// Finally check if user looses his moderator status
			if (!KunenaError::checkDatabaseError () && empty($catids)) {
				$user->moderator = 0;
				$user->save();
			}
		}

		$access = KunenaFactory::getAccessControl();
		$access->clearCache();
	}

	/**
	 * Method to get the category table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The category table name to be used
	 * @param	string	The category table prefix to be used
	 * @return	object	The category table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaCategory', $prefix = 'Table') {
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance ( $tabletype ['name'], $tabletype ['prefix'] );
	}

	public function bind($data, $ignore = array()) {
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties ( $data );
	}

	/**
	 * Method to load a KunenaCategory object by catid
	 *
	 * @access	public
	 * @param	mixed	$identifier The category id of the user to load
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id) {
		// Create the user table object
		$table = &$this->getTable ();

		// Load the KunenaTableUser object based on the user id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaCategory object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new category
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
		// Do not allow this action if current user isn't admin in this category
		// FIXME:
		if ($this->exists() && !KunenaFactory::getUser()->isAdmin($this->id)) {
			$this->setError ( JText::sprintf('COM_KUNENA_ERROR_NOT_CATEGORY_ADMIN', $this->name) );
			return false;
		}

		// Create the user table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		//are we creating a new user
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new users return
		if ($isnew && $updateOnly) {
			return true;
		}

		//Store the user data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}
		$table->reorder ();

		$access = KunenaFactory::getAccessControl();
		$access->clearCache();

		// Set the id for the KunenaUser object in case we created a new category.
		if ($result && $isnew) {
			$this->load ( $table->get ( 'id' ) );
			self::$_instances [$table->get ( 'id' )] = $this;
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaCategory object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function delete() {
		// Do not allow this action if current user isn't admin in this category
		if (!KunenaFactory::getUser()->isAdmin($this->id)) {
			$this->setError ( JText::sprintf('COM_KUNENA_ERROR_NOT_CATEGORY_ADMIN', $this->name) );
			return false;
		}

		if (!$this->exists()) {
			return true;
		}

		// Create the user table object
		$table = &$this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
		}
		$this->_exists = false;

		$access = KunenaFactory::getAccessControl();
		$access->clearCache();

		$db = JFactory::getDBO ();
		// Delete moderators
		$queries[] = "DELETE FROM #__kunena_moderation WHERE catid={$db->quote($this->id)}";
		// Delete favorites
		$queries[] = "DELETE f FROM #__kunena_favorites AS f LEFT JOIN #__kunena_messages AS m ON m.id=f.thread WHERE m.catid={$db->quote($this->id)}";
		// Delete subscriptions
		$queries[] = "DELETE s FROM #__kunena_subscriptions AS s LEFT JOIN #__kunena_messages AS m ON m.id=s.thread WHERE m.catid={$db->quote($this->id)}";
		// Delete category subscriptions
		$queries[] = "DELETE FROM #__kunena_subscriptions_categories WHERE catid={$db->quote($this->id)}";
		// Delete thank yous
		$queries[] = "DELETE t FROM #__kunena_thankyou AS t LEFT JOIN #__kunena_messages AS m ON m.id=t.postid WHERE m.catid={$db->quote($this->id)}";
		// Delete poll users
		$queries[] = "DELETE p FROM #__kunena_polls_users AS p LEFT JOIN #__kunena_messages AS m ON m.id=p.pollid WHERE m.catid={$db->quote($this->id)}";
		// Delete poll options
		$queries[] = "DELETE p FROM #__kunena_polls_options AS p LEFT JOIN #__kunena_messages AS m ON m.id=p.pollid WHERE m.catid={$db->quote($this->id)}";
		// Delete polls
		$queries[] = "DELETE p FROM #__kunena_polls AS p LEFT JOIN #__kunena_messages AS m ON m.id=p.threadid WHERE m.catid={$db->quote($this->id)}";
		// Delete messages
		$queries[] = "DELETE m, t FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.catid={$db->quote($this->id)}";
		// TODO: delete attachments

		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}

		// TODO: remove dependency
		require_once KPATH_SITE.'/class.kunena.php';
		CKunenaTools::reCountUserPosts();
		self::recount();

		return $result;
	}

	/**
	 * Method to check out the KunenaCategory object
	 *
	 * @access	public
	 * @param	integer	$who
	 * @return	boolean	True if checked out by somebody else
	 * @since 1.6
	 */
	public function checkout($who) {
		if (!KunenaFactory::getUser()->isAdmin($this->id)) {
			$this->setError ( JText::sprintf('COM_KUNENA_ERROR_NOT_CATEGORY_ADMIN', $this->name) );
			return true;
		}

		if (!$this->_exists)
			return false;

		// Create the user table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->checkout($who);

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );

		return $result;
	}

	/**
	 * Method to check in the KunenaCategory object
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function checkin() {
		if (!KunenaFactory::getUser()->isAdmin($this->id)) {
			$this->setError ( JText::sprintf('COM_KUNENA_ERROR_NOT_CATEGORY_ADMIN', $this->name) );
			return false;
		}

		if (!$this->_exists)
			return true;

		// Create the user table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->checkin();

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );

		return $result;
	}

	/**
	 * Method to check if an item is checked out
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function isCheckedOut($with) {
		if (!$this->_exists)
			return false;

		// Create the user table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->isCheckedOut($with);
		return $result;
	}

	static function recount() {
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
	}

}
