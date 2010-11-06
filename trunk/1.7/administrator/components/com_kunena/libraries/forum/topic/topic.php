<?php
/**
 * @version $Id$
 * Kunena Component - KunenaForumTopic Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.forum.category.helper');
kimport ('kunena.forum.topic.helper');
kimport ('kunena.forum.topic.user.helper');
kimport ('kunena.forum.message.helper');

/**
 * Kunena Forum Topic Class
 */
class KunenaForumTopic extends JObject {
	protected $_exists = false;
	protected $_db = null;
	protected $_hold = 1;
	protected $_posts = 0;

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($identifier = 0) {
		// Always load the topic -- if topic does not exist: fill empty data
		$this->_db = JFactory::getDBO ();
		$this->load ( $identifier );
	}

	/**
	 * Returns KunenaForumTopic object
	 *
	 * @access	public
	 * @param	identifier		The topic to load - Can be only an integer.
	 * @return	KunenaForumTopic		The topic object.
	 * @since	1.7
	 */
	static public function getInstance($identifier = null, $reset = false) {
		return KunenaForumTopicHelper::get($identifier, $reset);
	}

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	public function subscribe($value=1, $user=null) {
		$usertopic = $this->getUserTopic($user);
		$usertopic->subscribed = (int)$value;
		return $usertopic->save();
	}

	public function favorite($value=1, $user=null) {
		$usertopic = $this->getUserTopic($user);
		$usertopic->favorite = (int)$value;
		return $usertopic->save();
	}

	public function sticky($value=1) {
		$this->ordering = (int)$value;
		return $this->save();
	}

	public function lock($value=1) {
		$this->locked = (int)$value;
		return $this->save();
	}

	public function publish($value=KunenaForum::PUBLISHED) {
		$this->hold = (int)$value;
		return $this->save();
	}

	public function getUserTopic($user=null) {
		$usertopic = KunenaForumTopicUserHelper::get($this->id, $user);
		$usertopic->category_id = $this->category_id;
		return $usertopic;
	}

	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->category_id);
	}

	public function authorise($action='read', $user=null, $silent=false) {
		static $actions  = array(
			'none'=>array(),
			'read'=>array('Read'),
			'create'=>array('NotExists'),
			'reply'=>array('Read','NotHold','NotMoved','Unlocked'),
			'edit'=>array('Read','NotMoved','Unlocked','Own'),
			'move'=>array('Read','NotMoved'),
			'approve'=>array('Read','NotMoved'),
			'delete'=>array('Read','Unlocked','Own'),
			'undelete'=>array('Read'),
			'permdelete'=>array('Read'),
			'sticky'=>array('Read'),
			'lock'=>array('Read'),
			'post.read'=>array('Read'),
			'post.reply'=>array('Read','NotHold','NotMoved','Unlocked'),
			'post.edit'=>array('Read','Unlocked','Own'),
			'post.move'=>array('Read'),
			'post.approve'=>array('Read'),
			'post.delete'=>array('Read','Unlocked','Own'),
			'post.undelete'=>array('Read'),
			'post.permdelete'=>array('Read'),
			'post.attachment.read'=>array('Read'),
			'post.attachment.create'=>array('Read','Unlocked','Own'),
			'post.attachment.delete'=>array('Read','Unlocked','Own'),
		);
		$user = KunenaUser::getInstance($user);
		if (!isset($actions[$action])) {
			if (!$silent) $this->setError ( JText::_ ( 'COM_KUNENA_LIB_TOPIC_NO_ACTION' ) );
			return false;
		}
		$category = $this->getCategory();
		$auth = $category->authorise('topic.'.$action, $user, $silent);
		if (!$auth) {
			if (!$silent) $this->setError ( $category->getError() );
			return false;
		}
		foreach ($actions[$action] as $function) {
			$authFunction = 'authorise'.$function;
			if (! method_exists($this, $authFunction) || ! $this->$authFunction($user)) {
				if (!$silent) $this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
				return false;
			}
		}
		return true;
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

	public function bind($data, $allow = array()) {
		if (!empty($allow)) $data = array_intersect_key($data, array_flip($allow));
		$this->setProperties ( $data );
	}

	/**
	 * Method to load a KunenaForumTopic object by id
	 *
	 * @access	public
	 * @param	mixed	$id The topic id to be loaded
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id) {
		// Create the table object
		$table = $this->getTable ();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		$this->_hold = $this->hold === null ? 1 : $this->hold;
		$this->_posts = (int)$this->posts;
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaForumTopic object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new topic
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
		//are we creating a new topic
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new topic return
		if (!$this->id) {
			$this->setError ( JText::_('COM_KUNENA_LIB_TOPIC_ERROR_NO_ID') );
			return false;
		}
		// If we aren't allowed to create new topic return
		if ($isnew && $updateOnly) {
			$this->setError ( JText::_('COM_KUNENA_LIB_TOPIC_ERROR_UPDATE_ONLY') );
			return false;
		}

		// Create the topics table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		$topicDelta = $this->delta();
		$postDelta = $this->posts-$this->_posts;

		//Store the topic data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}

		// Set the id for the KunenaForumTopic object in case we created a new topic.
		if ($result && $isnew) {
			$this->load ( $this->id );
		}

		$category = $this->getCategory();
		if (! $category->update($this, $topicDelta, $postDelta)) {
			$this->setError ( $category->getError () );
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaForumTopic object from the database
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
		KunenaForumCategoryHelper::recount();

		return $result;
	}

	public function update($message=null, $postdelta=0) {
		// Update post count
		$this->posts += $postdelta;
		if (!$this->exists()) {
			if (!$message) return false;
			$this->id = $message->id;
		}
		if ($message && $message->exists() && $message->hold == KunenaForum::PUBLISHED ) {
			// If message exists, we may need to update cache
			if (!$this->first_post_time || $this->first_post_time >= $message->time) {
				$this->first_post_id = $message->id;
				$this->first_post_time = $message->time;
				$this->first_post_userid = $message->userid;
				$this->first_post_message = $message->message;
				$this->first_post_guest_name = $message->name;
			}
			if ($this->last_post_time <= $message->time) {
				$this->last_post_id = $message->id;
				$this->last_post_time = $message->time;
				$this->last_post_userid = $message->userid;
				$this->last_post_message = $message->message;
				$this->last_post_guest_name = $message->name;
			}
		} else {
			if (!$message || $this->first_post_id == $message->id) {
				// If message got deleted and was cached, we need to find new first post
				$db = JFactory::getDBO ();
				$query = "SELECT * FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
					WHERE m.thread={$db->quote($this->id)} AND m.hold=0 ORDER BY m.time ASC";
				$db->setQuery ( $query, 0, 1 );
				$first = $db->loadObject ();
				KunenaError::checkDatabaseError ();

				if ($first) {
					$this->first_post_id = $first->id;
					$this->first_post_time = $first->time;
					$this->first_post_userid = $first->userid;
					$this->first_post_message = $first->message;
					$this->first_post_guest_name = $first->name;
				}
			}
			if (!$message || $this->last_post_id == $message->id) {
				// If topic got deleted and was cached, we need to find new last post
				$db = JFactory::getDBO ();
				$query = "SELECT * FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
					WHERE m.thread={$db->quote($this->id)} AND m.hold=0 ORDER BY m.time DESC";
				$db->setQuery ( $query, 0, 1 );
				$last = $db->loadObject ();
				KunenaError::checkDatabaseError ();

				if ($last) {
					$this->last_post_id = $last->id;
					$this->last_post_time = $last->time;
					$this->last_post_userid = $last->userid;
					$this->last_post_message = $last->message;
					$this->last_post_guest_name = $last->name;
				}
			}
			if (isset($first) && !$first) {
				// If topic has no posts, mark it deleted
				$this->hold = KunenaForum::DELETED;
				$this->posts = 0;
				$this->first_post_id = 0;
				$this->first_post_time = 0;
				$this->first_post_userid = 0;
				$this->first_post_message = '';
				$this->first_post_guest_name = '';
				$this->last_post_id = 0;
				$this->last_post_time = 0;
				$this->last_post_userid = 0;
				$this->last_post_message = '';
				$this->last_post_guest_name = '';
			}
		}
		if ($this->first_post_id) {
			$this->hold = KunenaForum::PUBLISHED;
		}
		return $this->save();
	}

	// Internal functions

	protected function authoriseNotExists($user) {
		// Check that topic does not exist
		if ($this->_exists) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}
	protected function authoriseRead($user) {
		// TODO: allow user to see his own topic before it gets approved
		// Check that user can read topic
		if (!$this->_exists) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		if ($this->hold) {
			$access = KunenaFactory::getAccessControl();
			$hold = $access->getAllowedHold($user->userid, $this->category_id, false);
			if (!in_array($this->hold, $hold)) {
				$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
				return false;
			}
		}
		return true;
	}
	protected function authoriseNotHold($user) {
		// Check that topic is not unapproved or deleted
		if ($this->hold) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}
	protected function authoriseNotMoved($user) {
		// Check that topic is not moved
		if ($this->moved_id) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}
	protected function authoriseUnlocked($user) {
		// Check that topic is not locked or user is a moderator
		if ($this->locked && !$user->isModerator($this->category_id)) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ) );
			return false;
		}
		return true;
	}
	protected function authoriseOwn($user) {
		// Check that topic owned by the user or user is a moderator
		// TODO: check #__kunena_user_topics
		if ((!$this->first_post_userid || $this->first_post_userid != $user->userid) && !$user->isModerator($this->category_id)) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ) );
			return false;
		}
		return true;
	}

	protected function delta() {
		if (!$this->hold && $this->_hold) {
			// Create or publish topic
			return 1;
		} elseif ($this->hold && !$this->_hold) {
			// Delete or unpublish topic
			return -1;
		}
		return 0;
	}
}