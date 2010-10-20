<?php
/**
 * @version $Id$
 * Kunena Component - KunenaTopic class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

kimport ('error');
kimport ('category');

/**
 * Kunena Topic Class
 */
class KunenaTopic extends JObject {
	// Global for every instance
	protected static $_instances = array();

	protected $_exists = false;
	protected $_db = null;

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
	 * Returns KunenaTopic object
	 *
	 * @access	public
	 * @param	identifier		The topic to load - Can be only an integer.
	 * @return	KunenaTopic		The topic object.
	 * @since	1.7
	 */
	static public function getInstance($identifier = null, $reset = false) {
		$c = __CLASS__;

		if ($identifier instanceof KunenaTopic) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new $c ();

		if ($reset || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new $c ( $id );
		}

		return self::$_instances [$id];
	}

	public function exists() {
		return $this->_exists;
	}

	static protected function loadTopics($ids) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode($ids);
		$c = __CLASS__;
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_topics WHERE id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new $c ();
				$instance->bind ( $results[$id] );
				$instance->_exists = true;
				self::$_instances [$id] = $instance;
			} else {
				self::$_instances [$id] = null;
			}
		}
		unset ($results);
	}

	static public function getTopics($ids = false) {
		if ($ids === false) {
			return self::$_instances;
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array($ids);
		}
		self::loadTopics($ids);

		$list = array ();
		foreach ( $ids as $id ) {
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->authorize()) {
				$list [$id] = self::$_instances [$id];
			}
		}

		return $list;
	}

	static public function getLatestTopics($categories=false, $limitstart=0, $limit=0, $params=array()) {
		$db = JFactory::getDBO ();
		if ($limit < 1) $limit = KunenaFactory::getConfig ()->threads_per_page;

		$reverse = isset($params['reverse']) ? (int) $params['reverse'] : 0;
		$orderby = isset($params['orderby']) ? (string) $params['orderby'] : 'tt.last_post_time DESC';
		$starttime = isset($params['starttime']) ? (int) $params['starttime'] : 0;
		$user = isset($params['user']) ? KunenaFactory::getUser($params['user']) : KunenaFactory::getUser();
		$hold = isset($params['hold']) ? (string) $params['hold'] : 0;
		$where = isset($params['where']) ? (string) $params['where'] : '';

		if (strstr('ut.last_', $orderby)) {
			$post_time_field = 'ut.last_post_time';
		} elseif (strstr('tt.first_', $orderby)) {
			$post_time_field = 'tt.first_post_time';
		} else {
			$post_time_field = 'tt.last_post_time';
		}

		$whereuser = array();
		if (!empty($params['started'])) $whereuser[] = 'ut.owner=1';
		if (!empty($params['replied'])) $whereuser[] = '(ut.owner=0 AND ut.posts>0)';
		if (!empty($params['posted'])) $whereuser[] = 'ut.posts>0';
		if (!empty($params['favorited'])) $whereuser[] = 'ut.favorite=1';
		if (!empty($params['subscriped'])) $whereuser[] = 'ut.subscribed=1';

		$catlist = implode(',', array_keys(KunenaCategory::getCategories($categories, $reverse)));

		$wheretime = ($starttime ? " AND {$post_time_field}>{$db->Quote($starttime)}" : '');
		$whereuser = ($whereuser ? " AND ut.user_id={$db->Quote($user->userid)} AND (".implode(' OR ',$whereuser).')' : '');
		$where = "tt.moved_id='0' AND tt.hold IN ({$hold}) AND tt.category_id IN ({$catlist}) {$whereuser} {$wheretime} {$where}";

		// Get total count
		if ($whereuser)
			$query = "SELECT COUNT(*) FROM #__kunena_user_topics AS ut INNER JOIN #__kunena_topics AS tt ON tt.id=ut.topic_id WHERE {$where}";
		else
			$query = "SELECT COUNT(*) FROM #__kunena_topics AS tt WHERE {$where}";
		$db->setQuery ( $query );
		$total = ( int ) $db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$total) return array(0, array());

		// Get items
		if ($whereuser)
			$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
				FROM #__kunena_user_topics AS ut
				INNER JOIN #__kunena_topics AS tt ON tt.id=ut.topic_id
				WHERE {$where} ORDER BY {$orderby}";
		else
			$query = "SELECT tt.*, ut.posts AS myposts, ut.last_post_id AS my_last_post_id, ut.favorite, tt.last_post_id AS lastread, 0 AS unread
				FROM #__kunena_topics AS tt
				LEFT JOIN #__kunena_user_topics AS ut ON tt.id=ut.topic_id AND ut.user_id={$db->Quote($user->userid)}
				WHERE {$where} ORDER BY {$orderby}";
		$db->setQuery ( $query, $limitstart, $limit );
		$topics = ( array ) $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return array(0, array());

		return array($total, $topics);
	}

	public function getCategory() {
		return KunenaCategory::getInstance($this->category_id);
	}

	public function authorize($action='read', $userid=null) {
		if ($action == 'none') return true;
		$access = KunenaFactory::getAccessControl();
		$catids = $access->getAllowedCategories($userid, $action);
		return !empty($catids[0]) || !empty($catids[$this->category_id]);
	}

	/**
	 * Method to get the topics table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The topics table name to be used
	 * @param	string	The topics table prefix to be used
	 * @return	object	The topics table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaTopics', $prefix = 'Table') {
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
	 * Method to load a KunenaTopic object by id
	 *
	 * @access	public
	 * @param	mixed	$id The topic id to be loaded
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id) {
		// Create the table object
		$table = &$this->getTable ();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaTopic object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new topic
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
		// Create the topics table object
		$table = &$this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		//are we creating a new topic
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new topic return
		if ($isnew && $updateOnly) {
			return true;
		}

		//Store the topic data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}

		// Set the id for the KunenaTopic object in case we created a new topic.
		if ($result && $isnew) {
			$this->load ( $table->get ( 'id' ) );
			self::$_instances [$table->get ( 'id' )] = $this;
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaTopic object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function delete() {
		if (!$this->exists()) {
			return true;
		}

		// Create the table object
		$table = &$this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
		}
		$this->_exists = false;

		$db = JFactory::getDBO ();
		// Delete user topics
		$queries[] = "DELETE FROM #__kunena_user_topics WHERE topic_id={$db->quote($this->id)}";
		// Delete user read
		$queries[] = "DELETE FROM #__kunena_user_read WHERE topic_id={$db->quote($this->id)}";
		// Delete poll (users)
		$queries[] = "DELETE FROM #__kunena_polls_users WHERE pollid={$db->quote($this->poll_id)}";
		// Delete poll (options)
		$queries[] = "DELETE FROM #__kunena_polls_options WHERE pollid={$db->quote($this->poll_id)}";
		// Delete poll
		$queries[] = "DELETE FROM #__kunena_polls WHERE id={$db->quote($this->poll_id)}";
		// Delete thank yous
		$queries[] = "DELETE t FROM #__kunena_thankyou AS t INNER JOIN #__kunena_messages AS m ON m.id=t.postid WHERE m.thread={$db->quote($this->id)}";
		// Delete all messages
		$queries[] = "DELETE m, t FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.thread={$db->quote($this->id)}";
		// TODO: delete attachments

		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}

		// TODO: remove dependency
		require_once KPATH_SITE.'/class.kunena.php';
		CKunenaTools::reCountUserPosts();
		KunenaCategory::recount();

		return $result;
	}

	static function recount() {
		$db = JFactory::getDBO ();

		// Recount total posts, total attachments
		$query ="UPDATE jos_kunena_topics AS tt
			INNER JOIN (SELECT m.thread, COUNT(DISTINCT m.id) AS posts, COUNT(a.id) as attachments
				FROM jos_kunena_messages AS m
				LEFT JOIN jos_kunena_attachments AS a ON m.id=a.mesid
				WHERE m.hold=0
				GROUP BY m.thread) AS t ON t.thread=tt.id
			SET tt.posts=t.posts,
				tt.attachments=t.attachments";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Update first post information (by time)
		$query ="UPDATE #__kunena_topics AS tt
			INNER JOIN (SELECT thread, MIN(time) AS time FROM #__kunena_messages WHERE hold=0 GROUP BY thread) AS l ON tt.id=l.thread
			INNER JOIN #__kunena_messages AS m ON l.thread=m.thread AND m.time=l.time
			INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
			SET tt.first_post_id = m.id,
				tt.first_post_time = m.time,
				tt.first_post_userid = m.userid,
				tt.first_post_message = t.message,
				tt.first_post_guest_name = IF(m.userid>0,null,m.name)";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;

		// Update last post information (by time)
		$query ="UPDATE #__kunena_topics AS tt
			INNER JOIN (SELECT thread, MAX(time) AS time FROM #__kunena_messages WHERE hold=0 GROUP BY thread) AS l ON tt.id=l.thread
			INNER JOIN #__kunena_messages AS m ON l.thread=m.thread AND m.time=l.time
			INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
			SET tt.last_post_id = m.id,
				tt.last_post_time = m.time,
				tt.last_post_userid = m.userid,
				tt.last_post_message = t.message,
				tt.last_post_guest_name = IF(m.userid>0,null,m.name)";
		$db->setQuery($query);
		$db->query ();
		if (KunenaError::checkDatabaseError ())
			return;
	}
}