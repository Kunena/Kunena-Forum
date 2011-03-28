<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.databasequery');
kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.user.helper');
kimport ('kunena.forum.category.helper');
kimport ('kunena.forum.topic.helper');
kimport ('kunena.forum.topic.user.helper');
kimport ('kunena.forum.topic.poll.helper');
kimport ('kunena.forum.message');
kimport ('kunena.forum.message.helper');
kimport ('kunena.forum.message.attachment.helper');
kimport ('kunena.keyword.helper');

/**
 * Kunena Forum Topic Class
 */
class KunenaForumTopic extends JObject {
	protected $_exists = false;
	protected $_db = null;
	protected $_hold = 1;
	protected $_posts = 0;
	protected $_pagination = null;

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
		if ($exists !== null) {
			$this->_exists = $exists;
			$this->_hold = $this->hold;
			$this->_posts = $this->posts;
		}
		return $return;
	}

	public function subscribe($value=1, $user=null) {
		$usertopic = $this->getUserTopic($user);
		$usertopic->subscribed = (int)$value;
		if (!$usertopic->save()) {
			$this->setError($usertopic->getError());
		}
		return !$this->getError();
	}

	public function favorite($value=1, $user=null) {
		$usertopic = $this->getUserTopic($user);
		$usertopic->favorite = (int)$value;
		if (!$usertopic->save()) {
			$this->setError($usertopic->getError());
		}
		return !$this->getError();
	}

	public function sticky($value=1) {
		$this->ordering = (int)$value;
		return $this->save();
	}

	public function lock($value=1) {
		$this->locked = (int)$value;
		return $this->save();
	}

	public function getKeywords($user=null, $glue=false) {
		if ($user !== false) {
			$user = KunenaUserHelper::get($user);
			// Guests or non-existing cannot have personal keywords
			if (!$user->exists())
				return $glue ? '' : array();
			$user = $user->userid;
		}
		$user = (int) $user;
		if (!isset($this->_keywords[$user])) {
			$this->_keywords[$user] = KunenaKeywordHelper::getByTopics($this->id, $user);
			ksort($this->_keywords[$user]);
		}
		if ($glue) {
			$keywords = array_keys($this->_keywords[$user]);
			foreach ($keywords as &$keyword) {
				if (strpos($keyword, ' ') !== false)
					$keyword = '"'.$keyword.'"';
			}
			return implode($glue, $keywords);
		}
		return $this->_keywords[$user];
	}

	public function setKeywords($keywords, $user=null, $glue=null) {
		if ($user !== false) {
			$user = KunenaUserHelper::get($user);
			// Guests or non-existing cannot have personal keywords
			if (!$user->exists())
				return false;
			$user = $user->userid;
		}
		$user = (int) $user;
		$keywords = KunenaKeywordHelper::setTopicKeywords($keywords, $this->id, $user);
		if ($keywords === false)
			return false;
		$this->_keywords[$user] = $keywords;
		return true;
	}

	public function publish($value=KunenaForum::PUBLISHED) {
		if ($value<0 || $value>3) $value = 0;
		elseif ($value>3) $value = 3;
		$this->hold = (int)$value;
		$query = new KunenaDatabaseQuery();
		$query->update('#__kunena_messages')->set("hold={$this->hold}")->where("thread={$this->id}")->where("hold={$this->_hold}");
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		if (KunenaError::checkDatabaseError()) return false;
		return $this->recount();
	}

	public function getUserTopic($user=null) {
		$usertopic = KunenaForumTopicUserHelper::get($this->id, $user);
		return $usertopic;
	}

	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->category_id);
	}

	public function getPoll() {
		static $poll = null;
		if (!$poll) {
			$poll = KunenaForumTopicPollHelper::get($this->poll_id);
			$poll->threadid = $this->id;
		}
		return $poll;
	}

	public function newPosts() {
		static $readtopics = false;

		$session = KunenaFactory::getSession ();
		if (!$session->userid)
			return false;
		if ($readtopics === false)
			$readtopics = explode(',', $session->readtopics);
		return $this->last_post_time > $session->lasttime && !in_array($this->id, $readtopics);
	}

	public function getHits() {
		return $this->hits;
	}

	public function getPagination($limitstart=0, $limit=6, $display=4, $prefix='') {
		if (!$this->_pagination) {
			kimport ('kunena.html.pagination');
			$this->_pagination = new KunenaHtmlPagination($this->posts, $limitstart, $limit, $prefix);
			$this->_pagination->setDisplay($display, "index.php?option=com_kunena&view=topic&catid={$this->category_id}&id={$this->id}");
		}
		return $this->_pagination;
	}

	public function getFirstPostAuthor() {
		return KunenaFactory::getUser($this->first_post_userid);
	}

	public function getLastPostAuthor() {
		return KunenaFactory::getUser($this->last_post_userid);
	}

	public function getFirstPostTime() {
		return new KunenaDate($this->first_post_time);
	}

	public function getLastPostTime() {
		return new KunenaDate($this->last_post_time);
	}

	public function getIcon() {
		return KunenaFactory::getTemplate()->getTopicIcon($this);
	}

	public function getTotal($hold=null) {
		return $this->getReplies($hold) + 1;
	}

	public function getReplies($hold=null) {
		$me = KunenaFactory::getUser();
		if ($this->moved_id || !$me->isModerator($this->category_id)) {
			return $this->posts - 1;
		}
		return KunenaForumMessageHelper::getLocation($this->last_post_id, 'both', $hold);
	}

	public function getPostLocation($mesid, $direction = 'asc', $hold=null) {
		if (!isset($this->lastread)) {
			$this->lastread = $this->last_post_id;
			$this->unread = 0;
		}
		if ($mesid == 'unread') $mesid = $this->lastread;
		$me = KunenaFactory::getUser();
		if ($this->moved_id || !$me->isModerator($this->category_id)) {
			if ($mesid == 'first' || $mesid == $this->first_post_id) return $direction = 'asc' ? 0 : $this->posts-1;
			if ($mesid == 'last' || $mesid == $this->last_post_id) return $direction = 'asc' ? $this->posts-1 : 0;
			if ($mesid == $this->unread) return $direction = 'asc' ? $this->posts - max($this->unread, 1) : 0;
		}
		if ($mesid == 'first') $direction == 'asc' ? 0 : 'both';
		if ($mesid == 'last') $direction == 'asc' ? 'both' : 0;
		if (!$direction) return 0;
		return KunenaForumMessageHelper::getLocation($mesid, $direction, $hold);
	}

	public function newReply($fields=array(), $user=null) {
		$user = KunenaUserHelper::get($user);
		$category = $this->getCategory();

		$message = new KunenaForumMessage();
		$message->setTopic($this);
		$message->parent = $this->first_post_id;
		$message->thread = $this->id;
		$message->catid = $this->category_id;
		$message->name = $user->getName('');
		$message->userid = $user->userid;
		$message->subject = $this->subject;
		$message->ip = $_SERVER ["REMOTE_ADDR"];
		if ($this->hold) {
			// If topic was unapproved or deleted, use the same state for the new message
			$message->hold = $this->hold;
		} else {
			// Otherwise message is either unapproved or published depending if the category is moderated or not
			$message->hold = $category->review ? (int)!$category->authorise ('moderate', $user, true) : 0;
		}
		if ($fields === true) {
			$user = KunenaFactory::getUser($this->first_post_userid);
			$text = preg_replace('/\[confidential\](.*?)\[\/confidential\]/su', '', $this->first_post_message );
			$message->message = "[quote=\"{$user->getName($this->first_post_guest_name)}\" post={$this->first_post_id}]" .  $text . "[/quote]";
		} elseif (is_array($fields)) {
			$message->bind($fields, array ('name', 'email', 'subject', 'message' ));
		}
		return $message;
	}

	function markRead($user = null) {
		$user = KunenaUserHelper::get($user);
		if (! $user->exists())
			return;

		$db = JFactory::getDBO ();
		$session = KunenaFactory::getSession ();

		$readTopics = explode ( ',', $session->readtopics );
		if (! in_array ( $this->id, $readTopics )) {
			$readTopics[] = $this->id;
			$readTopics = implode ( ',', $readTopics );
		} else {
			$readTopics = false; // do not update session
		}

		if ($readTopics) {
			$db->setQuery ( "UPDATE #__kunena_sessions SET readtopics={$db->Quote($readTopics)} WHERE userid={$db->Quote($user->userid)}" );
			$db->query ();
			KunenaError::checkDatabaseError();
		}
	}

	// TODO: this code needs to be removed after new indication handling is in its place
	function markNew() {
		// Mark topic read for current user
		$this->markRead ();

		// Mark topic unread for others

		// First take care of old sessions to make our job easier and faster
		$lasttime = $this->get ( 'time' ) - max(intval(JFactory::getConfig()->getValue( 'config.lifetime' ))*60, intval(KunenaFactory::getConfig ()->fbsessiontimeout)) - 60;
		$query = "UPDATE #__kunena_sessions SET readtopics='0' WHERE currvisit<{$this->_db->quote($lasttime)}";
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		$dberror = KunenaError::checkDatabaseError ();
		if ($dberror) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_SESSIONS' ) );
			return false;
		}

		$me = KunenaFactory::getUser();
		// Then look at users who have read the thread
		$query = "SELECT userid, readtopics FROM #__kunena_sessions WHERE readtopics LIKE '%{$this->id}%' AND userid!={$this->_db->quote($me->userid)}";
		$this->_db->setQuery ( $query );
		$sessions = $this->_db->loadObjectList ();
		$dberror = KunenaError::checkDatabaseError ();
		if ($dberror) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_SESSIONS' ) );
			return false;
		}

		// And clear current thread
		$errcount = 0;
		foreach ( $sessions as $session ) {
			$readtopics = $session->readtopics;
			$rt = explode ( ",", $readtopics );
			$key = array_search ( $this->id, $rt );
			if ($key !== false) {
				unset ( $rt [$key] );
				$readtopics = implode ( ",", $rt );
				$query = "UPDATE #__kunena_sessions SET readtopics={$this->_db->quote($readtopics)} WHERE userid={$this->_db->quote($session->userid)}";
				$this->_db->setQuery ( $query );
				$this->_db->query ();
				$dberror = KunenaError::checkDatabaseError ();
				if ($dberror)
					$errcount ++;
			}
		}
		if ($errcount) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_SESSIONS' ) );
			return false;
		}
		return true;
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
			'favorite'=>array('Read'),
			'subscribe'=>array('Read'),
			'sticky'=>array('Read'),
			'lock'=>array('Read'),
			'poll.read'=>array('Read'),
			'poll.create'=>array('Own'),
			'poll.edit'=>array('Read','Own'),
			'poll.delete'=>array('Read','Own'),
			'poll.vote'=>array('Read', 'Vote'),
			'post.read'=>array('Read'),
			'post.thankyou'=>array('Read','NotMoved'),
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
			if (!$silent) $this->setError ( __CLASS__.'::'.__FUNCTION__.'(): '.JText::sprintf ( 'COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action ) );
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
				if (!$silent && !$this->getError()) $this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
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

	public function move($target, $ids=false, $shadow=false, $subject='', $subjectall=false) {
		// Warning: logic in this function is very complicated and even with full understanding its easy to miss some details!

		// Cleanup input
		if (!($ids instanceof JDate)) {
			if (!is_array($ids)) {
				$ids = explode(',', (string)$ids);
			}
			$mesids = array();
			foreach ($ids as $id) {
				$mesids[(int) $id] = (int) $id;
			}
			unset ($mesids[0]);
			$ids = implode(',', $mesids);
		}
		$subject = (string) $subject;

		// First we need to check if there will be messages left in the old topic
		if ($ids) {
			$query = new KunenaDatabaseQuery();
			$query->select('COUNT(*)')->from('#__kunena_messages')->where("thread={$this->id}");

			if ($ids instanceof JDate) {
				// All older messages will remain (including unapproved, deleted)
				$query->where("time<{$ids->toUnix()}");
			} else {
				// All messages that were not selected will remain
				$query->where("id NOT IN ({$ids})");
			}
			$this->_db->setQuery ( $query );
			$oldcount = (int)$this->_db->loadResult();
			if ($this->_db->getErrorNum () ) {
				$this->setError($this->_db->getError());
				return false;
			}
			// So are we moving the whole topic?
			if (!$oldcount) {
				$ids = '';
			}
		}

		// Find out where we are moving the messages
		if (!$target || !$target->exists()) {
			$this->setError(JText::printf('COM_KUNENA_MODERATION_ERROR_NO_TARGET', $this->id));
			return false;

		} elseif ($target instanceof KunenaForumTopic) {
			// Move messages into another topic (original topic will always remain, either as real one or shadow)

			if ($target == $this) {
				// We cannot move topic into itself
				$this->setError(JText::sprintf('COM_KUNENA_MODERATION_ERROR_SAME_TARGET_THREAD', $this->id, $this->id));
				return false;
			}
			if ($this->moved_id) {
				// Moved topic cannot be merged with another topic -- it has no posts to be moved
				$this->setError(JText::sprintf('COM_KUNENA_MODERATION_ERROR_ALREADY_SHADOW', $this->id));
				return false;
			}
			if ( $this->poll_id && $target->poll_id ) {
				// We cannot currently have 2 polls in one topic -- fail
				$this->setError(JText::_('COM_KUNENA_MODERATION_CANNOT_MOVE_TOPIC_WITH_POLL_INTO_ANOTHER_WITH_POLL'));
				return false;
			}

		} elseif ($target instanceof KunenaForumCategory) {
			// Move messages into category

			if ( $target->parent_id == 0 ) {
				// Section cannot have any topics
				$this->setError(JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MOVE_SECTION'));
				return false;
			}

			// Save category information for later use
			$categoryTarget = $target;
			$categoryFrom = $this->getCategory();

			if ($shadow || $ids) {
				// Create new topic for the moved messages
				$target = clone $this;
				$target->id = 0;
			} else {
				// If we just move into another category, we can keep using the old topic
				$target = $this;
			}
			// Did user want to change subject?
			if ($subject) $target->subject = $subject;
			// Did user want to change category?
			$target->category_id = $categoryTarget->id;

		} else {
			$this->setError(JText::_('COM_KUNENA_MODERATION_ERROR_WRONG_TARGET'));
			return false;

		}

		// For now on we assume that at least one message will be moved (=authorization check was called on topic/message)

		// We will soon need target topic id, so save if it doesn't exist
		if (!$target->exists()) {
			// FIXME: check if we can break binding: topic->id == message->id
			$this->setError(JText::_('COM_KUNENA_MODERATION_ERROR_WRONG_TARGET'));
			return false;

			if (!$target->save()) {
				$this->setError($target->getError());
				return false;
			}
		}

		// Move messages (set new category and topic)
		{
			$query = new KunenaDatabaseQuery();
			$query->update('#__kunena_messages')->set("catid={$target->category_id}")->set("thread={$target->id}")->where("thread={$this->id}");
			// Did we want to change subject from all the messages?
			if ($subjectall && !empty($subject)) $query->set("subject={$this->_db->quote($subject)}");

			if ($ids instanceof JDate) {
				// Move all newer messages (includes unapproved, deleted messages)
				$query->where("time>={$ids->toUnix()}");
			} elseif ($ids) {
				// Move individual messages
				$query->where("id IN ({$ids})");
			}

			$this->_db->setQuery ( $query );
			$this->_db->query();
			if ($this->_db->getErrorNum () ) {
				$this->setError($this->_db->getError());
				return false;
			}
		}

		// If all messages were moved into another topic, we need to move poll as well
		if ( $this->poll_id && !$ids && $target != $this ) {
			// Note: We may already have saved cloned target (having poll_id already in there)
			$target->poll_id = $this->poll_id;
			// Note: Do not remove poll from shadow: information could still be used to show icon etc

			$query = "UPDATE #__kunena_polls SET `threadid`={$this->_db->Quote($target->id)} WHERE `threadid`={$this->_db->Quote($this->id)}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			if ($this->_db->getErrorNum () ) {
				$this->setError($this->_db->getError());
				return false;
			}
		}

		if (isset($categoryTarget)) {
			// Move topic into another category

			if ($target != $this) {
				// Leave shadow
				$this->moved_id = $target->id;
			}
			// Note: We already saved possible target earlier
			if (!$this->save()) {
				return false;
			}

			// Update user topic information (topic, category)
			KunenaForumTopicUserHelper::move($this, $target);
			// Remove topic and posts from the old category
			$categoryFrom->update($this, -1, -$this->posts);
			// Add topic and posts into the new category
			$categoryTarget->update($target, 1, $this->posts);

		} else {
			// Moving topic into another topic

			if (!$ids) {
				// Create shadow topic from $this
				$this->moved_id = $target->id;
				if (!$shadow) {
					// Mark shadow topic as deleted
					$this->hold=2;
				}
				// Save shadow topic
				if (!$this->save()) {
					return false;
				}

				// Add new posts, hits and attachments into the target topic
				$target->posts += $this->posts;
				$target->hits += $this->hits;
				$target->attachments += $this->attachments;
				// Update first and last post information into the target topic
				$target->updatePostInfo($this->first_post_id, $this->first_post_time, $this->first_post_userid,
					$this->first_post_message, $this->first_post_guest_name);
				$target->updatePostInfo($this->last_post_id, $this->last_post_time, $this->last_post_userid,
					$this->last_post_message, $this->last_post_guest_name);
				// Save target topic
				if (!$target->save()) {
					return false;
				}

				// Update user topic information (topic, category)
				KunenaForumTopicUserHelper::merge($this, $target);
				// Remove topic and posts from the old category
				$this->getCategory()->update($this, -1, -$this->posts);
				// Add posts into the new category
				$target->getCategory()->update($target, 0, $this->posts);

			} else {
				// Both topics have changed and we have no idea how much: force full recount
				// TODO: we can do this faster..
				$this->recount();
				$target->recount();
			}
		}

		return true;
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
		if (! $table->store ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		// Set the id for the KunenaForumTopic object in case we created a new topic.
		if ($isnew) {
			$this->load ( $table->id );
		}

		$category = $this->getCategory();
		if (! $category->update($this, $topicDelta, $postDelta)) {
			$this->setError ( $category->getError () );
		}

		return true;
	}

	/**
	 * Method to put the KunenaForumTopic object on trash this is still present in database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function trash() {
		if (!$this->exists()) {
			return true;
		}

		$db = JFactory::getDBO ();
		$queries[] = "UPDATE #__kunena_messages SET hold='2' WHERE thread={$db->quote($this->id)}";
		$queries[] = "UPDATE #__kunena_topics SET hold='2' WHERE id={$db->quote($this->id)}";

		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}

		return true;
	}

	/**
	 * Method to delete the KunenaForumTopic object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function delete($recount = true) {
		if (!$this->exists()) {
			return true;
		}

		// Create the table object
		$table = $this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
		}
		$this->_exists = false;

		// NOTE: shadow topic doesn't exist, DO NOT DELETE OR CHANGE ANY EXTERNAL INFORMATION
		if ($this->moved_id == 0) {
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

			foreach ($queries as $query) {
				$db->setQuery($query);
				$db->query();
				KunenaError::checkDatabaseError ();
			}

			if ($recount) {
				KunenaUserHelper::recount();
				KunenaForumCategoryHelper::recount();
				KunenaForumMessageAttachmentHelper::cleanup();
			}
		}
		return $result;
	}

	public function updatePostInfo($id, $time=0, $userid=0, $message='', $name='') {
		if ($id === false) {
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
			return;
		}
		if (!$this->first_post_time || $this->first_post_time >= $time) {
			$this->first_post_id = $id;
			$this->first_post_time = $time;
			$this->first_post_userid = $userid;
			$this->first_post_message = $message;
			$this->first_post_guest_name = $name;

		}
		if ($this->last_post_time <= $time) {
			$this->last_post_id = $id;
			$this->last_post_time = $time;
			$this->last_post_userid = $userid;
			$this->last_post_message = $message;
			$this->last_post_guest_name = $name;
		}
	}

	public function update($message=null, $postdelta=0) {
		// Update post count
		$this->posts += $postdelta;
		$exists = $message && $message->exists();
		if (!$this->exists()) {
			if (!$exists) {
				$this->setError(JText::_('COM_KUNENA_LIB_TOPIC_NOT_EXISTS'));
				return false;
			}
			$this->id = $message->id;
		}
		if ($exists && $message->thread == $this->id && $message->hold == $this->hold) {
			// If message belongs into this topic and has same state, we may need to update cache
			$this->updatePostInfo($message->id, $message->time, $message->userid, $message->message, $message->name);
		} elseif (!$this->moved_id) {
			// If message isn't visible anymore, check if we need to update cache
			if (!$exists || $this->first_post_id == $message->id) {
				// If message got deleted and was cached, we need to find new first post
				$db = JFactory::getDBO ();
				$query = "SELECT * FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
					WHERE m.thread={$db->quote($this->id)} AND m.hold={$this->hold} ORDER BY m.time ASC";
				$db->setQuery ( $query, 0, 1 );
				$first = $db->loadObject ();
				KunenaError::checkDatabaseError ();

				if ($first) {
					$this->updatePostInfo($first->id, $first->time, $first->userid, $first->message, $first->name);
				} else {
					$this->updatePostInfo(false);
				}
			}
			if (!$exists || $this->last_post_id == $message->id) {
				// If topic got deleted and was cached, we need to find new last post
				$db = JFactory::getDBO ();
				$query = "SELECT * FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
					WHERE m.thread={$db->quote($this->id)} AND m.hold={$this->hold} ORDER BY m.time DESC";
				$db->setQuery ( $query, 0, 1 );
				$last = $db->loadObject ();
				KunenaError::checkDatabaseError ();

				if ($last) {
					$this->updatePostInfo($last->id, $last->time, $last->userid, $last->message, $last->name);
				} else {
					$this->updatePostInfo(false);
				}
			}
			if ($this->hold != KunenaForum::TOPIC_DELETED && (!$this->first_post_id || !$this->last_post_id)) {
				// If topic has no visible posts, mark it deleted and recount
				$this->hold = $exists ? $message->hold : KunenaForum::TOPIC_DELETED;
				$this->recount();
			}
		}

		if(!$this->save()) {
			return false;
		}

		if ($exists && $message->userid && abs($postdelta) == 1) {
			// Update user topic
			$usertopic = $this->getUserTopic($message->userid);
			if (!$usertopic->update($message, $postdelta)) {
				$this->setError ( $usertopic->getError () );
			}
			// Update post count from user
			$user = KunenaFactory::getUser($message->userid);
			$user->posts += $postdelta;
			if (!$user->save()) {
				$this->setError ( $user->getError () );
			}
		} else {
			KunenaForumTopicUserHelper::recount($this->id);
			KunenaUserHelper::recount($this->id);
		}

		return true;
	}

	public function recount() {
		if (!$this->moved_id) {
			// Recount total posts and attachments
			$query ="SELECT COUNT(DISTINCT m.id) AS posts, COUNT(a.id) AS attachments
					FROM #__kunena_messages AS m
					LEFT JOIN #__kunena_attachments AS a ON m.id=a.mesid
					WHERE m.hold={$this->_db->quote($this->hold)} AND m.thread={$this->_db->quote($this->id)}
					GROUP BY m.thread";
			$this->_db->setQuery($query);
			$result = $this->_db->loadAssoc ();
			if (KunenaError::checkDatabaseError ())
				return false;
			if (!$result) {
				$result = array('posts'=>0, 'attachments'=>0);
			}
			$this->bind($result);
		}
		return $this->update();
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
		// Check that user can read topic
		if (!$this->exists()) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		if ($this->hold > 1 || ($this->hold && !$this->getUserTopic($user)->owner)) {
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
		$usertopic = $this->getUserTopic($user);
		if (!$usertopic->owner && !$user->isModerator($this->category_id)) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' ) );
			return false;
		}
		return true;
	}
	protected function authoriseVote($user) {
		// Check that user can vote
		$config = KunenaFactory::getConfig();
		$poll = $this->getPoll();
		$voted = $poll->getMyVotes($user);
// TODO: allow support for only one vote without possibility to change it
//		if ($voted && $config->pollallowvoteone) {
//			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_ONLY_ONCE' ) );
//			return false;
//		}
		if ($voted->votes >= $config->pollnbvotesbyuser) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_TOO_MANY_TIMES' ) );
			return false;
		}
		if ($config->polltimebtvotes && $voted->time + $config->polltimebtvotes > JFactory::getDate()->toUnix()) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_TOO_EARLY' ) );
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