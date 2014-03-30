<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Topic
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumTopic
 *
 * @property int $category_id
 * @property string $subject
 * @property int $icon_id
 * @property int $locked
 * @property int $hold
 * @property int $ordering
 * @property int $posts
 * @property int $hits
 * @property int $attachments
 * @property int $poll_id
 * @property int $moved_id
 * @property int $first_post_id
 * @property int $first_post_time
 * @property int $first_post_userid
 * @property string $first_post_message
 * @property string $first_post_guest_name
 * @property int $last_post_id
 * @property int $last_post_time
 * @property int $last_post_userid
 * @property string $last_post_message
 * @property string $last_post_guest_name
 * @property string $params
 */
class KunenaForumTopic extends KunenaDatabaseObject {
	/**
	 * @var int
	 */
	public $id = null;
	public $unread = 0;
	public $lastread = 0;

	protected $_table = 'KunenaTopics';
	protected $_db = null;
	protected $_authcache = array();
	protected $_authccache = array();
	protected $_authfcache = array();
	protected $_hold = 1;
	protected $_posts = 0;
	protected $_pagination = null;
	protected $_keywords = null;
	protected static $actions  = array(
			'none'=>array(),
			'read'=>array('Read'),
			'create'=>array('NotExists'),
			'reply'=>array('Read','NotHold','NotMoved','Unlocked'),
			'edit'=>array('Read','NotMoved','Unlocked','Own'),
			'move'=>array('Read'),
			'approve'=>array('Read','NotMoved'),
			'delete'=>array('Read'),
			'undelete'=>array('Read'),
			'permdelete'=>array('Read'),
			'favorite'=>array('Read'),
			'subscribe'=>array('Read'),
			'sticky'=>array('Read'),
			'lock'=>array('Read'),
			'poll.read'=>array('Read'),
			'poll.create'=>array('Own'),
			'poll.edit'=>array('Read','Own','NoVotes'),
			'poll.delete'=>array('Read','Own'),
			'poll.vote'=>array('Read', 'Vote'),
			'post.read'=>array('Read'),
			'post.thankyou'=>array('Read','NotMoved'),
			'post.unthankyou'=>array('Read'),
			'post.reply'=>array('Read','NotHold','NotMoved','Unlocked'),
			'post.edit'=>array('Read','Unlocked'),
			'post.move'=>array('Read'),
			'post.approve'=>array('Read'),
			'post.delete'=>array('Read','Unlocked'),
			'post.undelete'=>array('Read'),
			'post.permdelete'=>array('Read'),
			'post.attachment.read'=>array('Read'),
			'post.attachment.create'=>array('Read','Unlocked'),
			'post.attachment.delete'=>array(), // TODO: In the future we might want to restrict this: array('Read','Unlocked'),
		);

	/**
	 * @param mixed $properties
	 *
	 * @internal
	 */
	public function __construct($properties = null) {
		$this->_db = JFactory::getDBO ();
		parent::__construct($properties);
	}

	/**
	 * Returns KunenaForumTopic object.
	 *
	 * @param int $identifier	The topic to load - Can be only an integer.
	 * @param bool $reset
	 *
	 * @return KunenaForumTopic
	 */
	static public function getInstance($identifier = null, $reset = false) {
		return KunenaForumTopicHelper::get($identifier, $reset);
	}

	/**
	 * @param null|bool $exists
	 *
	 * @return bool
	 */
	function exists($exists = null) {
		if ($exists !== null) {
			$this->_hold = $this->hold;
			$this->_posts = $this->posts;
		}
		return parent::exists($exists);
	}

	/**
	 * Subscribe / Unsubscribe user to this topic.
	 *
	 * @param bool  $value	True for subscribe, false for unsubscribe.
	 * @param mixed $user
	 *
	 * @return bool
	 */
	public function subscribe($value=true, $user=null) {
		$usertopic = $this->getUserTopic($user);
		$usertopic->subscribed = (int)$value;
		if (!$usertopic->save()) {
			$this->setError($usertopic->getError());
			return false;
		}
		return true;
	}

	/**
	 * Favorite / unfavorite user to this topic.
	 *
	 * @param bool  $value	True for favorite, false for unfavorite.
	 * @param mixed $user
	 *
	 * @return bool
	 */
	public function favorite($value=true, $user=null) {
		$usertopic = $this->getUserTopic($user);
		$usertopic->favorite = (int)$value;
		if (!$usertopic->save()) {
			$this->setError($usertopic->getError());
			return false;
		}
		return true;
	}

	/**
	 * @param int $value
	 *
	 * @return bool
	 */
	public function sticky($value=1) {
		$this->ordering = (int)$value;
		return $this->save();
	}

	/**
	 * @param int $value
	 *
	 * @return bool
	 */
	public function lock($value=1) {
		$this->locked = (int)$value;
		return $this->save();
	}

	/**
	 * @param mixed $user
	 * @param bool|string $glue
	 *
	 * @return array|string
	 */
	public function getKeywords($user=null, $glue=false) {
		$config = KunenaFactory::getConfig();
		if ($user !== false) {
			$user = KunenaUserHelper::get($user);
			// Guests or non-existing cannot have personal keywords
			if (!$config->userkeywords || !$user->exists()) {
				return $glue ? '' : array();
			}
			$user = $user->userid;
		} elseif (!$config->keywords) {
			return $glue ? '' : array();
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

	/**
	 * @param mixed $keywords
	 * @param mixed $user
	 * @param null|string $glue
	 *
	 * @return bool
	 */
	public function setKeywords($keywords, $user=null, $glue=null) {
		$config = KunenaFactory::getConfig();
		if ($user !== false) {
			$user = KunenaUserHelper::get($user);
			// Guests or non-existing cannot have personal keywords
			if (!$config->userkeywords || !$user->exists())
				return false;
			$user = $user->userid;
		} elseif (!$config->keywords) {
			return false;
		}
		$user = (int) $user;
		$keywords = KunenaKeywordHelper::setTopicKeywords($keywords, $this->id, $user);
		if ($keywords === false)
			return false;
		$this->_keywords[$user] = $keywords;
		return true;
	}

	/**
	 * @param int $value
	 *
	 * @return bool
	 */
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

	/**
	 * Send email notifications from the first post in the topic.
	 *
	 * @param null|string $url
	 */
	public function sendNotification($url=null) {
		// Reload message just in case if it was published by bulk update.
		KunenaForumMessageHelper::get($this->first_post_id, true)->sendNotification($url);
	}

	/**
	 * @param mixed $user
	 *
	 * @return KunenaForumTopicUser
	 */
	public function getUserTopic($user=null) {
		$usertopic = KunenaForumTopicUserHelper::get($this, $user);
		return $usertopic;
	}

	/**
	 * @return KunenaUser
	 */
	public function getAuthor() {
		return KunenaUserHelper::getAuthor($this->first_post_userid, $this->first_post_guest_name);
	}

	/**
	 * @return KunenaForumCategory
	 */
	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->category_id);
	}

	/**
	 * @return KunenaForumTopicPoll
	 */
	public function getPoll() {
		static $poll = null;
		if (!$poll) {
			$poll = KunenaForumTopicPollHelper::get($this->poll_id);
			$poll->threadid = $this->id;
		}
		return $poll;
	}

	/**
	 * @return int
	 */
	public function getHits() {
		return $this->hits;
	}

	/**
	 * Increase hit counter for this topic.
	 */
	public function hit() {
		$app = JFactory::getApplication();
		$lasthit = $app->getUserState('com_kunena.topic.lasthit');
		if ($lasthit == $this->id) return;

		// Update only hit - not entire object
		$table = $this->getTable();
		$table->id = $this->id;

		if ( $table->hit() ) {
			$this->hits++;
			$app->setUserState('com_kunena.topic.lasthit', $this->id);
		}
	}

	/**
	 * @param int    $limitstart
	 * @param int    $limit
	 * @param int    $display
	 * @param string $prefix
	 *
	 * @return JPagination
	 */
	public function getPagination($limitstart=0, $limit=6, $display=4, $prefix='') {
		if (!$this->_pagination) {
			$this->_pagination = new KunenaPagination($this->posts, $limitstart, $limit, $prefix);
			$this->_pagination->setDisplayedPages($display, "index.php?option=com_kunena&view=topic&catid={$this->category_id}&id={$this->id}");
		}
		return $this->_pagination;
	}

	/**
	 * @param mixed $user
	 *
	 * @return KunenaForumTopicUser
	 */
	public function getUserInfo($user = null) {
		return KunenaForumTopicUserHelper::get($this->id, $user);
	}

	/**
	 * @return KunenaUser
	 */
	public function getFirstPostAuthor() {
		return KunenaUserHelper::getAuthor($this->first_post_userid, $this->first_post_guest_name);
	}

	/**
	 * @return KunenaUser
	 */
	public function getLastPostAuthor() {
		return KunenaUserHelper::getAuthor($this->last_post_userid, $this->last_post_guest_name);
	}

	/**
	 * @return KunenaDate
	 */
	public function getFirstPostTime() {
		return new KunenaDate($this->first_post_time);
	}

	/**
	 * @return KunenaDate
	 */
	public function getLastPostTime() {
		return new KunenaDate($this->last_post_time);
	}

	/**
	 * @param string $field
	 *
	 * @return int|string
	 */
	public function displayField($field) {
		switch ($field) {
			case 'id':
				return intval($this->id);
			case 'subject':
				return KunenaHtmlParser::parseText($this->subject);
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function getIcon() {
		return KunenaFactory::getTemplate()->getTopicIcon($this);
	}

	/**
	 * @param mixed $hold
	 *
	 * @return int
	 */
	public function getTotal($hold=null) {
		if ($this->moved_id || !KunenaUserHelper::getMyself()->isModerator($this->getCategory())) {
			return (int) max($this->posts, 0);
		}
		return KunenaForumMessageHelper::getLocation($this->last_post_id, 'both', $hold) + 1;
	}

	/**
	 * @param mixed $hold
	 *
	 * @return int
	 */
	public function getReplies($hold=null) {
		return (int) max($this->getTotal($hold) - 1, 0);
	}

	/**
	 * @param mixed $category
	 * @param bool $xhtml
	 * @param null|string $action
	 *
	 * @return string
	 */
	public function getUrl($category = null, $xhtml = true, $action = null) {
		$uri = $this->getUri($category, $action);
		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * Get permament topic URL without domain.
	 *
	 * If you want to add domain (for email etc), you can prepend the output with this:
	 * JUri::getInstance()->toString(array('scheme', 'host', 'port'))
	 *
	 * @param KunenaForumCategory $category
	 * @param bool $xhtml
	 * @param string $action
	 *
	 * @return string
	 */
	public function getPermaUrl($category = null, $xhtml = true, $action = null) {
		return $this->getUrl($category, $xhtml, $action);
	}

	/**
	 * @param mixed $category
	 * @param string $action
	 *
	 * @return JUri|null
	 */
	public function getUri($category = null, $action = null) {
		$category = $category ? KunenaForumCategoryHelper::get($category) : $this->getCategory();
		if (!$this->exists() || !$category->exists()) return null;
		if ($action instanceof KunenaForumMessage) {
			$message = $action;
			$action = 'post'.$message->id;
		}

		$uri = JUri::getInstance("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$this->id}&action={$action}");
		if ($uri->getVar('action') !== null) {
			$uri->delVar('action');
			$mesid = 0;
			$limit = max(1, intval(KunenaFactory::getConfig()->messages_per_page));
			if (isset($message)) {
				$mesid = $message->id;
			} elseif ((string)$action === (string)(int)$action) {
				if ($action > 0) $uri->setVar('limitstart', $action * $limit);
			} else {
				switch ($action) {
					case 'first':
						$mesid = $this->first_post_id;
						break;
					case 'last':
						$mesid = $this->last_post_id;
						break;
					case 'unread':
						// Special case, improves caching
						$uri->setVar('layout', 'unread');
						// $mesid = $this->lastread ? $this->lastread : $this->last_post_id;
						break;
				}
			}
			if ($mesid) {
				if (KunenaUserHelper::getMyself()->getTopicLayout() != 'threaded' && $mesid > 0) {
					$uri->setFragment($mesid);
					$limitstart = intval($this->getPostLocation($mesid) / $limit) * $limit;
					if ($limitstart) $uri->setVar('limitstart', $limitstart);
				} else {
					$uri->setVar('mesid', $mesid);
				}
			}
		}
		return $uri;
	}

	/**
	 * @param int $mesid
	 * @param string|null $direction
	 * @param mixed $hold
	 *
	 * @return int
	 */
	public function getPostLocation($mesid, $direction = null, $hold = null) {
		if (is_null($direction)) $direction = KunenaUserHelper::getMyself()->getMessageOrdering();
		if (!isset($this->lastread)) {
			$this->lastread = $this->last_post_id;
			$this->unread = 0;
		}
		if ($mesid == 'unread') $mesid = $this->lastread;
		if ($this->moved_id || !KunenaUserHelper::getMyself()->isModerator($this->getCategory())) {
			if ($mesid == 'first' || $mesid == $this->first_post_id) return $direction == 'asc' ? 0 : $this->posts-1;
			if ($mesid == 'last' || $mesid == $this->last_post_id) return $direction == 'asc' ? $this->posts-1 : 0;
			if ($mesid == $this->unread) return $direction == 'asc' ? $this->posts - max($this->unread, 1) : 0;
		}
		if ($mesid == 'first') $direction = ($direction == 'asc' ? 0 : 'both');
		if ($mesid == 'last') $direction = ($direction == 'asc' ? 'both' : 0);
		return KunenaForumMessageHelper::getLocation($mesid, $direction, $hold);
	}

	/**
	 * @param array|bool $fields
	 * @param mixed  $user
	 * @param array|null  $safefields
	 *
	 * @return KunenaForumMessage
	 */
	public function newReply($fields=array(), $user=null, $safefields=null) {
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
		$message->ip = !empty($_SERVER ["REMOTE_ADDR"]) ? $_SERVER ["REMOTE_ADDR"] : '';
		if ($this->hold) {
			// If topic was unapproved or deleted, use the same state for the new message
			$message->hold = $this->hold;
		} else {
			// Otherwise message is either unapproved or published depending if the category is moderated or not
			$message->hold = $category->review ? (int)!$category->authorise ('moderate', $user, true) : 0;
		}
		if ($fields === true) {
			$user = KunenaUserHelper::get($this->first_post_userid);
			$text = preg_replace('/\[confidential\](.*?)\[\/confidential\]/su', '', $this->first_post_message );
			$message->message = "[quote=\"{$user->getName($this->first_post_guest_name)}\" post={$this->first_post_id}]" .  $text . "[/quote]";
		} else {
			if ($safefields) $message->bind($safefields);
			if ($fields) $message->bind($fields, array ('name', 'email', 'subject', 'message' ), true);
		}
		return $message;
	}

	/**
	 * @param mixed $user
	 *
	 * @return bool
	 */
	public function hasNew($user = null) {
		$user = KunenaUserHelper::get($user);
		if (!KunenaFactory::getConfig()->shownew || !$user->exists()) {
			return false;
		}
		$session = KunenaFactory::getSession ();
		if ($this->last_post_time <= $session->lasttime) {
			return false;
		}
		$userinfo = KunenaForumCategoryUserHelper::get($this->getCategory(), $user);
		if ($userinfo->allreadtime && $this->last_post_time <= JFactory::getDate($userinfo->allreadtime)->toUnix()) {
			return false;
		}
		$read = KunenaForumTopicUserReadHelper::get($this, $user);
		if ($this->last_post_time <= $read->time) {
			return false;
		}

		return true;
	}

	/**
	 * @param mixed $user
	 *
	 * @return bool
	 */
	public function markRead($user = null) {
		$user = KunenaUserHelper::get($user);
		if (!KunenaFactory::getConfig()->shownew || !$user->exists()) {
			return false;
		}
		$read = KunenaForumTopicUserReadHelper::get($this, $user);
		$read->time = JFactory::getDate()->toUnix();
		$read->message_id = $this->last_post_id;
		$read->save();

		return true;
	}

	/**
	 * @deprecated
	 */
	public function markNew($user = null) {
		return $this->markRead ($user);
	}

	/**
	 * @param string $action
	 * @param mixed  $user
	 * @param bool   $silent
	 *
	 * @return bool
	 */
	public function authorise($action='read', $user=null, $silent=false) {
		if ($action == 'none') return true;
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		if ($user === null) {
			$user = KunenaUserHelper::getMyself();
		} elseif (!($user instanceof KunenaUser)) {
			$user = KunenaUserHelper::get($user);
		}

		if (empty($this->_authcache[$user->userid][$action])) {
			if (!isset(self::$actions[$action])) {
				JError::raiseError(500, JText::sprintf ( 'COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action ) );
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
				return false;
			}

			if (!isset($this->_authccache[$user->userid][$action])) {
				$this->_authccache[$user->userid][$action] = $this->getCategory()->authorise('topic.'.$action, $user, null);
			}

			$this->_authcache[$user->userid][$action] = $this->_authccache[$user->userid][$action];
			if (empty($this->_authcache[$user->userid][$action])) {
				foreach (self::$actions[$action] as $function) {
					if (!isset($this->_authfcache[$user->userid][$function])) {
						$authFunction = 'authorise'.$function;
						$this->_authfcache[$user->userid][$function] = $this->$authFunction($user);
					}
					$error = $this->_authfcache[$user->userid][$function];
					if ($error) {
						$this->_authcache[$user->userid][$action] = $error;
						break;
					}
				}
			}
		}
		$error = $this->_authcache[$user->userid][$action];
		if ($silent === false && $error) $this->setError ( $error );

		if ($silent !== null) $error = !$error;
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $error;
	}

	/**
	 * Method to load a KunenaForumTopic object by id.
	 *
	 * @param null $id	The topic id to be loaded.
	 *
	 * @return bool	True on success.
	 */
	public function load($id = null) {
		$exists = parent::load($id);
		$this->_hold = $this->hold === null ? 1 : $this->hold;
		$this->_posts = $this->posts;
		return $exists;
	}

	/**
	 * Move topic or parts of it into another category or topic.
	 *
	 * @param object $target	Target KunenaForumCategory or KunenaForumTopic
	 * @param mixed $ids		false, array of message Ids or JDate
	 * @param bool $shadow		Leave visible shadow topic.
	 * @param string $subject	New subject
	 * @param bool $subjectall	Change subject from every message
	 *
	 * @return 	bool|KunenaForumCategory|KunenaForumTopic	Target KunenaForumCategory or KunenaForumTopic or false on failure
	 */
	public function move($target, $ids=false, $shadow=false, $subject='', $subjectall=false) {
		// Warning: logic in this function is very complicated and even with full understanding its easy to miss some details!

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = array();

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

		$categoryFrom = $this->getCategory();

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

			if ($subjectall) $subject = $target->subject;

		} elseif ($target instanceof KunenaForumCategory) {
			// Move messages into category

			if ( $target->isSection() ) {
				// Section cannot have any topics
				$this->setError(JText::_('COM_KUNENA_MODERATION_ERROR_NOT_MOVE_SECTION'));
				return false;
			}

			// Save category information for later use
			$categoryTarget = $target;

			if ($this->moved_id) {
				// Move shadow topic and we are done
				$this->category_id = $categoryTarget->id;
				if ($subject) $this->subject = $subject;
				$this->save(false);
				return $target;
			}

			if ($shadow || $ids) {
				// Create new topic for the moved messages
				$target = clone $this;
				$target->exists(false);
				$target->id = 0;
				$target->hits = 0;
				$target->params = '';
			} else {
				// If we just move into another category, we can keep using the old topic
				$target = $this;
			}
			// Did user want to change the subject?
			if ($subject) {
				$target->subject = $subject;
			}
			// Did user want to change category?
			$target->category_id = $categoryTarget->id;

		} else {
			$this->setError(JText::_('COM_KUNENA_MODERATION_ERROR_WRONG_TARGET'));
			return false;

		}

		// For now on we assume that at least one message will be moved (=authorization check was called on topic/message)

		// We will soon need target topic id, so save if it doesn't exist
		if (!$target->exists()) {
			if (!$target->save(false)) {
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
			// Make sure that all messages in topic have unique time (deterministic without ORDER BY time, id)
			$query = "SET @ktime:=0";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			if ($this->_db->getErrorNum () ) {
				$this->setError($this->_db->getError());
				return false;
			}
			$query = "UPDATE #__kunena_messages SET time=IF(time<=@ktime,@ktime:=@ktime+1,@ktime:=time) WHERE thread={$target->id} ORDER BY time ASC, id ASC";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
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

		if (!$ids && $target != $this) {
			// Leave shadow from old topic
			$this->moved_id = $target->id;
			if (!$shadow) {
				// Mark shadow topic as deleted
				$this->hold=2;
			}
		}
		// Note: We already saved possible target earlier, now save only $this
		if (!$this->save(false)) {
			return false;
		}

		if (!$ids && !empty($categoryTarget)) {
			// Move topic into another category

			// Update user topic information (topic, category)
			KunenaForumTopicUserHelper::move($this, $target);
			// TODO: do we need this?
			//KunenaForumTopicUserReadHelper::move($this, $target);
			// Remove topic and posts from the old category
			$categoryFrom->update($this, -1, -$this->posts);
			// Add topic and posts into the new category
			$categoryTarget->update($target, 1, $this->posts);

		} elseif (!$ids) {
			// Moving topic into another topic

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
			if (!$target->save(false)) {
				$this->setError($target->getError());
				return false;
			}

			// Update user topic information (topic, category)
			KunenaForumTopicUserHelper::merge($this, $target);
			// TODO: do we need this?
			//KunenaForumTopicUserReadHelper::merge($this, $target);
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

		return $target;
	}

	/**
	 * Method to save the KunenaForumTopic object to the database.
	 *
	 * @param bool $cascade
	 *
	 * @return bool	True on success.
	 *
	 */
	public function save($cascade = true) {
		$topicDelta = $this->delta();
		$postDelta = $this->posts-$this->_posts;

		$isNew = !$this->exists();
		if (!parent::save()) {
			return false;
		}
		$this->_posts = $this->posts;

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = array();

		if ($cascade) {
			$category = $this->getCategory();
			if (! $category->update($this, $topicDelta, $postDelta)) {
				$this->setError ( $category->getError () );
			}
		}
		if ($isNew) {
			KunenaForumTopicUserReadHelper::purge();
		}

		return true;
	}

	/**
	 * Method to put the KunenaForumTopic object on trash this is still present in database.
	 *
	 * @return bool	True on success.
	 */
	public function trash() {
		if (!$this->exists()) {
			return true;
		}

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = array();

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
	 * Method to delete the KunenaForumTopic object from the database.
	 *
	 * @param bool $recount
	 *
	 * @return bool	True on success.
	 */
	public function delete($recount = true) {
		if (!$this->exists()) {
			return true;
		}

		if (!parent::delete()) {
			return false;
		}

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = array();

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

			// FIXME: add recount statistics
			if ($recount) {
				KunenaUserHelper::recount();
				KunenaForumCategoryHelper::recount();
				KunenaForumMessageAttachmentHelper::cleanup();
				KunenaForumMessageThankyouHelper::recount();
			}
		}
		return true;
	}

	/**
	 * @param int    $id
	 * @param int    $time
	 * @param int    $userid
	 * @param string $message
	 * @param string $name
	 */
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
		if (!$this->first_post_time || ($this->first_post_time > $time || ($this->first_post_time == $time && $this->first_post_id >= $id))) {
			$this->first_post_id = $id;
			$this->first_post_time = $time;
			$this->first_post_userid = $userid;
			$this->first_post_message = $message;
			$this->first_post_guest_name = $name;

		}
		if ($this->last_post_time < $time || ($this->last_post_time == $time && $this->last_post_id <= $id)) {
			$this->last_post_id = $id;
			$this->last_post_time = $time;
			$this->last_post_userid = $userid;
			$this->last_post_message = $message;
			$this->last_post_guest_name = $name;
		}
	}

	/**
	 * @param KunenaForumMessage $message
	 * @param int  $postdelta
	 *
	 * @return bool
	 */
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
			if(!isset($this->hold)) $this->hold=KunenaForum::TOPIC_DELETED;
			// If message isn't visible anymore, check if we need to update cache
			if (!$exists || $this->first_post_id == $message->id) {
				// If message got deleted and was cached, we need to find new first post
				$db = JFactory::getDBO ();
				$query = "SELECT * FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
					WHERE m.thread={$db->quote($this->id)} AND m.hold={$this->hold} ORDER BY m.time ASC, m.id ASC";
				$db->setQuery ( $query, 0, 1 );
				$first = $db->loadObject ();
				KunenaError::checkDatabaseError ();

				if ($first) {
					$this->first_post_time = 0;
					$this->updatePostInfo($first->id, $first->time, $first->userid, $first->message, $first->name);
				} else {
					$this->updatePostInfo(false);
				}
			}
			if (!$exists || $this->last_post_id == $message->id) {
				// If topic got deleted and was cached, we need to find new last post
				$db = JFactory::getDBO ();
				$query = "SELECT * FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
					WHERE m.thread={$db->quote($this->id)} AND m.hold={$this->hold} ORDER BY m.time DESC, m.id DESC";
				$db->setQuery ( $query, 0, 1 );
				$last = $db->loadObject ();
				KunenaError::checkDatabaseError ();

				if ($last) {
					$this->last_post_time = 0;
					$this->updatePostInfo($last->id, $last->time, $last->userid, $last->message, $last->name);
				} else {
					$this->updatePostInfo(false);
				}
			}
		}

		if (!$this->first_post_id || !$this->last_post_id) {
			// If topic has no visible posts, mark it deleted and recount
			$this->hold = $exists ? $message->hold : KunenaForum::TOPIC_DELETED;
			$this->recount();
		}
		if (!($message && $message->exists()) && !$this->posts) {
			return $this->delete();
		}
		if(!$this->save()) {
			return false;
		}

		if ($exists && $message->userid && abs($postdelta) <= 1) {
			// Update user topic
			$usertopic = $this->getUserTopic($message->userid);
			if (!$usertopic->update($message, $postdelta)) {
				$this->setError ( $usertopic->getError () );
			}
			// Update post count from user
			$user = KunenaUserHelper::get($message->userid);
			$user->posts += $postdelta;
			if (!$user->save()) {
				$this->setError ( $user->getError () );
			}
		} else {
			KunenaForumTopicUserHelper::recount($this->id);
			// FIXME: optimize
			KunenaUserHelper::recount();
		}

		return true;
	}

	/**
	 * @return bool
	 */
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
				$this->posts = 0;
				// Double check if all posts have been removed from the database
				$query ="SELECT COUNT(m.id) AS posts, MIN(m.hold) AS hold
						FROM #__kunena_messages AS m
						WHERE m.thread={$this->_db->quote($this->id)}
						GROUP BY m.thread";
				$this->_db->setQuery($query);
				$result = $this->_db->loadAssoc ();
				if (KunenaError::checkDatabaseError ())
					return false;
				if ($result) {
					// Information in the database was wrong, recount topic
					$this->hold = $result->hold;
					$this->recount();
				}
				return true;
			}
			$this->bind($result);
		}
		return $this->update();
	}

	/**
	 * @param $mesid
	 *
	 * @return bool
	 */
	public function resetvotes($mesid) {
		if( !isset($mesid) ) return false;

		$query ="UPDATE #__kunena_polls_options SET votes=0 WHERE pollid={$this->_db->quote($mesid)}";
			$this->_db->setQuery($query);
			$this->_db->Query();
		if (KunenaError::checkDatabaseError ())
		return false;

		$query ="DELETE FROM #__kunena_polls_users WHERE pollid={$this->_db->quote($mesid)}";
			$this->_db->setQuery($query);
			$this->_db->Query();
		if (KunenaError::checkDatabaseError ())
		return false;

		return true;
	}


	// Internal functions

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseNotExists(KunenaUser $user) {
		// Check that topic does not exist
		if ($this->_exists) {
			return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseRead(KunenaUser $user) {
		// Check that user can read topic
		if (!$this->exists()) {
			return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
		}
		if ($this->hold > 1 || ($this->hold)) { // && !$this->getUserTopic($user)->owner)) {
			$access = KunenaAccess::getInstance();
			$hold = $access->getAllowedHold($user->userid, $this->category_id, false);
			if (!in_array($this->hold, $hold)) {
				return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
			}
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseNotHold(KunenaUser $user) {
		// Check that topic is not unapproved or deleted
		if ($this->hold) {
			return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseNotMoved(KunenaUser $user) {
		// Check that topic is not moved
		if ($this->moved_id) {
			return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseUnlocked(KunenaUser $user) {
		// Check that topic is not locked or user is a moderator
		if ($this->locked && !$user->isModerator($this->getCategory())) {
			return JText::_ ( 'COM_KUNENA_POST_ERROR_TOPIC_LOCKED' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseOwn(KunenaUser $user) {
		// Check that topic owned by the user or user is a moderator
		$usertopic = $this->getUserTopic($user);
		if (!$user->exists() || (!$usertopic->owner && !$user->isModerator($this->getCategory()))) {
			return JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseVote(KunenaUser $user) {
		// Check that user can vote
		$config = KunenaFactory::getConfig();
		$poll = $this->getPoll();
		$votes = $poll->getMyVotes($user);
		if (!$poll->exists()) {
			return JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_NO_POLL' );
		}
		if ($votes && $config->pollallowvoteone) {
			return JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_ONLY_ONCE' );
		}
		if ($votes >= $config->pollnbvotesbyuser) {
			return JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_TOO_MANY_TIMES' );
		}
		if ($config->polltimebtvotes && $poll->getMyTime($user) + (int) $config->polltimebtvotes > JFactory::getDate()->toUnix()) {
			return JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_TOO_EARLY' );
		}
		if ($this->locked ) {
			return JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_POLL_TOPIC_LOCKED' );
		}
		if ($poll->polltimetolive!='0000-00-00 00:00:00' && $poll->getTimeToLive() < JFactory::getDate()->toUnix() ) {
			return JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_POLL_EXPIRED' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return null|string
	 */
	protected function authoriseNoVotes(KunenaUser $user) {
		$poll = $this->getPoll();
		if ($poll->exists() && $poll->getUserCount()) {
			return JText::_ ( 'COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_ONGOING_POLL' );
		}
		return null;
	}

	/**
	 * @return int
	 */
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
