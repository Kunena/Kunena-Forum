<?php
/**
 * @version $Id$
 * Kunena Component - KunenaForumCategory Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.user.ban');
kimport ('kunena.forum.category.helper');

/**
 * Kunena Forum Category Class
 */
class KunenaForumCategory extends JObject {
	protected $_exists = false;
	protected $_db = null;
	protected $_topics = false;
	protected $_posts = false;
	protected $_lastid = false;
	protected $_authcache = array();

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
	 * Returns the global KunenaForumCategory object, only creating it if it doesn't already exist.
	 *
	 * @access	public
	 * @param	int	$id		The category to load - Can be only an integer.
	 * @return	KunenaForumCategory	The Category object.
	 * @since	1.6
	 */
	static public function getInstance($identifier = null, $reload = false) {
		return KunenaForumCategoryHelper::get($identifier, $reload);
	}

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
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
		return KunenaForumCategoryHelper::get($this->_lastid);
	}

	public function newTopic($user=null) {
		kimport ('kunena.forum.topic');
		kimport ('kunena.forum.message');

		$user = KunenaUser::getInstance($user);
		$topic = new KunenaForumTopic();
		$message = new KunenaForumMessage();
		$topic->category_id = $message->catid = $this->catid;
		$message->name = $user->getName('');
		$message->userid = $user->userid;
		return array($topic, $message);
	}

	public function getParent() {
		$parent = KunenaForumCategoryHelper::get($this->parent_id);
		if (!$parent->exists()) {
			$parent->name = JText::_ ( 'COM_KUNENA_TOPLEVEL' );
			$parent->_exists = true;
		}
		return $parent;
	}

	public function authorise($action='read', $user=null) {
		static $actions  = array(
			'none'=>array(),
			'read'=>array('Read'),
			'moderate'=>array('Read', 'NotBanned', 'Moderate'),
			'admin'=>array('Read', 'NotBanned', 'Admin'),
			'topic.read'=>array('Read'),
			'topic.create'=>array('Read', 'GuestWrite', 'NotBanned', 'NotSection', 'Unlocked'),
			'topic.reply'=>array('Read', 'GuestWrite', 'NotBanned', 'NotSection', 'Unlocked'),
			'topic.edit'=>array('Read', 'NotBanned', 'Unlocked'),
			'topic.approve'=>array('Read',' NotBanned', 'Moderate'),
			'topic.delete'=>array('Read', 'NotBanned', 'Unlocked'),
			'topic.undelete'=>array('Read', 'NotBanned', 'Moderate'),
			'topic.permdelete'=>array('Read', 'NotBanned', 'Admin'),
			'topic.sticky'=>array('Read','NotBanned', 'Moderate'),
			'topic.lock'=>array('Read','NotBanned', 'Moderate'),
			'topic.post.read'=>array('Read'),
			'topic.post.reply'=>array('Read', 'GuestWrite', 'NotBanned', 'NotSection', 'Unlocked'),
			'topic.post.edit'=>array('Read', 'NotBanned', 'Unlocked'),
			'topic.post.approve'=>array('Read', 'NotBanned', 'Moderate'),
			'topic.post.delete'=>array('Read', 'NotBanned', 'Unlocked'),
			'topic.post.undelete'=>array('Read', 'NotBanned', 'Moderate'),
			'topic.post.permdelete'=>array('Read', 'NotBanned', 'Admin'),
		);
		$user = KunenaUser::getInstance($user);
		if (!isset($actions[$action])) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		foreach ($actions[$action] as $function) {
			$authFunction = 'authorise'.$function;
			if (! method_exists($this, $authFunction) || ! $this->$authFunction($user)) {
				$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
				return false;
			}
		}
		return true;
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
		if (!KunenaUser::getInstance()->isAdmin($this->id)) {
			$this->setError ( JText::sprintf('COM_KUNENA_ERROR_NOT_CATEGORY_ADMIN', $this->name) );
			return false;
		}

		// Check if category exists
		if (!$this->exists()) return false;

		// Check if user exists
		$user = KunenaUser::getInstance($user);
		if (!$user->exists()) {
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
	public function getTable($type = 'KunenaCategories', $prefix = 'Table') {
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
	 * Method to load a KunenaForumCategory object by catid
	 *
	 * @access	public
	 * @param	mixed	$identifier The category id of the user to load
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id) {
		// Create the user table object
		$table = &$this->getTable ();

		// Load the KunenaTableCategories object based on the id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaForumCategory object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new category
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
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
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaForumCategory object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function delete() {
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
		KunenaForumCategoryHelper::recount();

		return $result;
	}

	/**
	 * Method to check out the KunenaForumCategory object
	 *
	 * @access	public
	 * @param	integer	$who
	 * @return	boolean	True if checked out by somebody else
	 * @since 1.6
	 */
	public function checkout($who) {
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
	 * Method to check in the KunenaForumCategory object
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function checkin() {
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

	public function update($topic, $topicdelta=0, $postdelta=0) {
		if ($topic->moved_id) return;
		// Update topic and post count
		$this->numTopics += $topicdelta;
		$this->numPosts += $postdelta;
		if ($topic->exists() && $topic->hold==0 ) {
			// If topic exists and has new post, we need to update cache
			if ($this->last_post_time < $topic->last_post_time) {
				$this->last_topic_id = $topic->id;
				$this->last_topic_subject = $topic->subject;
				$this->last_post_id = $topic->last_post_id;
				$this->last_post_time = $topic->last_post_time;
				$this->last_post_userid = $topic->last_post_userid;
				$this->last_post_message = $topic->last_post_message;
				$this->last_post_guest_name = $topic->last_post_guest_name;
			}
		} elseif ($this->last_topic_id == $topic->id) {
			// If topic got deleted and was cached, we need to find last post
			$db = JFactory::getDBO ();
			$query = "SELECT * FROM #__kunena_topics WHERE category_id={$db->quote($this->id)} AND hold=0 AND modev_id=0 ORDER BY last_post_time DESC";
			$db->setQuery ( $query, 0, 1 );
			$topic = $db->loadObject ();
			KunenaError::checkDatabaseError ();

			if ($topic) {
				$this->last_topic_id = $topic->id;
				$this->last_topic_subject = $topic->subject;
				$this->last_post_id = $topic->last_post_id;
				$this->last_post_time = $topic->last_post_time;
				$this->last_post_userid = $topic->last_post_userid;
				$this->last_post_message = $topic->last_post_message;
				$this->last_post_guest_name = $topic->last_post_guest_name;
			} else {
				$this->numTopics = 0;
				$this->numPosts = 0;
				$this->last_topic_id = 0;
				$this->last_topic_subject = '';
				$this->last_post_id = 0;
				$this->last_post_time = 0;
				$this->last_post_userid = 0;
				$this->last_post_message = '';
				$this->last_post_guest_name = '';
			}
		}
		$this->save();
	}

	// Internal functions

	protected function buildInfo() {
		if ($this->_topics !== false)
			return;
		$this->_topics = $this->numTopics;
		$this->_posts = $this->numPosts;
		$this->_lastid = $this->id;
		$categories = KunenaForumCategoryHelper::getChildren($this->id);
		foreach ($categories as $category) {
			$category->buildInfo();
			$this->_topics += $category->numTopics;
			$this->_posts += $category->numPosts;
			if (KunenaForumCategoryHelper::get($this->_lastid)->last_post_time < $category->last_post_time)
				$this->_lastid = $category->id;
		}
	}

	protected function authoriseRead($user) {
		// Checks if user can read category
		if (!$this->_exists) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		if (!isset($this->_authcache[$user->userid])) {
			$access = KunenaFactory::getAccessControl();
			$catids = $access->getAllowedCategories($user);
			$this->_authcache[$user->userid] = !empty($catids[0]) || !empty($catids[$this->id]);
		}
		if (!$this->_authcache[$user->userid]) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}
	protected function authoriseNotBanned($user) {
		$banned = $user->isBanned();
		if ($banned) {
			$banned = KunenaUserBan::getInstanceByUserid($user->userid, true);
			if (!$banned->isLifetime()) {
				require_once(KPATH_SITE.'/lib/kunena.timeformat.class.php');
				$this->setError ( JText::sprintf ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY', CKunenaTimeformat::showDate($banned->expiration)) );
				return false;
			} else {
				$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS' ) );
				return false;
			}
		}
		return true;
	}
	protected function authoriseGuestWrite($user) {
		// Check if user is guest and they can create or reply topics
		if ($user->id == 0 && !KunenaFactory::getConfig()->pubwrite) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' ) );
			return false;
		}

		return true;
	}
	protected function authoriseNotSection($user) {
		// Check if category is not a section
		if (!$this->parent) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_IS_SECTION' ) );
			return false;
		}
		return true;
	}
	protected function authoriseUnlocked($user) {
		// Check that category is not locked or that user is a moderator
		if ($this->locked && !$user->isModerator($this->id)) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' ) );
			return false;
		}
		return true;
	}
	protected function authoriseModerate($user) {
		// Check that user is moderator
		if (!$user->isModerator($this->id)) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ) );
			return false;
		}
		return true;
	}
	protected function authoriseAdmin($user) {
		// Check that user is admin
		if (!$user->isAdmin($this->id)) {
			$this->setError ( JText::_ ( 'COM_KUNENA_MODERATION_ERROR_NOT_ADMIN' ) );
			return false;
		}
		return true;
	}
}
