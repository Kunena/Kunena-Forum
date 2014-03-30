<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Message
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumMessage
 *
 * @property int $parent
 * @property int $thread
 * @property int $catid
 * @property string $name
 * @property int $userid
 * @property string $email
 * @property string $subject
 * @property int $time
 * @property string $ip
 * @property int $topic_emoticon
 * @property int $locked
 * @property int $hold
 * @property int $ordering
 * @property int $hits
 * @property int $moved
 * @property int $modified_by
 * @property string $modified_time
 * @property string $modified_reason
 * @property string $params
 * @property string $message
 */
class KunenaForumMessage extends KunenaDatabaseObject {
	/**
	 * @var int
	 */
	public $id = null;

	protected $_table = 'KunenaMessages';
	protected $_db = null;
	/**
	 * @var KunenaForumMessageAttachment[]
	 */
	protected $_attachments_add = array();
	/**
	 * @var KunenaForumMessageAttachment[]
	 */
	protected $_attachments_del = array();
	protected $_topic = null;
	protected $_hold = 1;
	protected $_thread = 0;

	/**
	 * @param mixed $properties
	 *
	 * @internal
	 */
	public function __construct($properties = null) {
		$this->_db = JFactory::getDBO ();
		parent::__construct($properties);
	}

	public function __destruct() {
		unset($this->_db);
		unset($this->_topic);
	}

	/**
	 * Returns KunenaForumMessage object.
	 *
	 * @param int $identifier	The message to load - Can be only an integer.
	 * @param bool $reload
	 *
	 * @return KunenaForumMessage
	 */
	static public function getInstance($identifier = null, $reload = false) {
		return KunenaForumMessageHelper::get($identifier, $reload);
	}

	/**
	 * @param mixed $user
	 *
	 * @return bool
	 */
	public function isNew($user = null) {
		$user = KunenaUserHelper::get($user);
		if (!KunenaFactory::getConfig()->shownew || !$user->exists()) {
			return false;
		}
		$session = KunenaFactory::getSession ();
		if ($this->time < $session->lasttime) {
			return false;
		}
		$allreadtime = KunenaForumCategoryUserHelper::get($this->getCategory(), $user)->allreadtime;
		if ($allreadtime && $this->time < JFactory::getDate($allreadtime)->toUnix()) {
			return false;
		}
		$read = KunenaForumTopicUserReadHelper::get($this->getTopic(), $user);
		if ($this->id == $read->message_id || $this->time < $read->time) {
			return false;
		}

		return true;
	}

	/**
	 * @param null|KunenaForumCategory $category	Fake category if needed. Used for aliases.
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public function getUrl($category = null, $xhtml = true) {
		return $this->getTopic()->getUrl($category, $xhtml, $this);
	}

	/**
	 * @param null|KunenaForumCategory $category	Fake category if needed. Used for aliases.
	 *
	 * @return JUri
	 */
	public function getUri($category = null) {
		return $this->getTopic()->getUri($category, $this);
	}

	/**
	 *  Get permament topic URL without domain.
	 *
	 * If you want to add domain (for email etc), you can prepend the output with this:
	 * JUri::getInstance()->toString(array('scheme', 'host', 'port'))
	 *
	 * @param null|KunenaForumCategory $category	Fake category if needed. Used for aliases.
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public function getPermaUrl($category = null, $xhtml = true) {
		$uri = $this->getPermaUri($category);
		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * @param null|KunenaForumCategory $category
	 *
	 * @return JUri
	 */
	public function getPermaUri($category = null) {
		$category = $category ? KunenaForumCategoryHelper::get($category) : $this->getCategory();
		if (!$this->exists() || !$category->exists()) return null;
		$uri = JUri::getInstance("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$this->thread}&mesid={$this->id}");
		return $uri;
	}

	/**
	 * @param array $fields
	 * @param mixed $user
	 * @param null|array  $safefields
	 *
	 * @return array
	 */
	public function newReply($fields=array(), $user=null, $safefields=null) {
		$user = KunenaUserHelper::get($user);
		$topic = $this->getTopic();
		$category = $this->getCategory();

		$message = new KunenaForumMessage();
		$message->setTopic($topic);
		$message->parent = $this->id;
		$message->thread = $topic->id;
		$message->catid = $topic->category_id;
		$message->name = $user->getName('');
		$message->userid = $user->userid;
		$message->subject = $this->subject;
		$message->ip = $_SERVER ["REMOTE_ADDR"];
		if ($topic->hold) {
			// If topic was unapproved or deleted, use the same state for the new message
			$message->hold = $topic->hold;
		} else {
			// Otherwise message is either unapproved or published depending if the category is moderated or not
			$message->hold = $category->review ? (int)!$category->authorise ('moderate', $user, true) : 0;
		}
		if ($fields === true) {
			$user = KunenaFactory::getUser($this->userid);
			$text = preg_replace('/\[confidential\](.*?)\[\/confidential\]/su', '', $this->message );
			$message->message = "[quote=\"{$user->getName($this->name)}\" post={$this->id}]" .  $text . "[/quote]";
		} else {
			if (is_array($safefields)) $message->bind($safefields);
			if (is_array($fields)) $message->bind($fields, array ('name', 'email', 'subject', 'message' ), true);
		}
		return array($topic, $message);
	}

	/**
	 * Send email notifications from the message.
	 *
	 * @param null|string $url
	 *
	 * @return bool|null
	 */
	public function sendNotification($url=null) {
		$config = KunenaFactory::getConfig();
		if (!$config->get('send_emails')) {
			return null;
		}

		if ($this->hold > 1) {
			return null;
		} elseif ($this->hold == 1) {
			$mailsubs = 0;
			$mailmods = $config->mailmod >= 0;
			$mailadmins = $config->mailadmin >= 0;
		} else {
			$mailsubs = (bool) $config->allowsubscriptions;
			$mailmods = $config->mailmod >= 1;
			$mailadmins = $config->mailadmin >= 1;
		}

		$once = false;
		if ($mailsubs) {
			if (!$this->parent) {
				// New topic: Send email only to category subscribers
				$mailsubs = $config->category_subscriptions != 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : 0;
				$once = $config->category_subscriptions == 'topic';
			} elseif ($config->category_subscriptions != 'post') {
				// Existing topic: Send email only to topic subscribers
				$mailsubs = $config->topic_subscriptions != 'disabled' ? KunenaAccess::TOPIC_SUBSCRIPTION : 0;
				$once = $config->topic_subscriptions == 'first';
			} else {
				// Existing topic: Send email to both category and topic subscribers
				$mailsubs = $config->topic_subscriptions == 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION;
				// FIXME: category subcription can override topic
				$once = $config->topic_subscriptions == 'first';
			}
		}

		if (!$url) {
			$url = JUri::getInstance()->toString(array('scheme', 'host', 'port')) . $this->getPermaUrl(null);
		}
		//get all subscribers, moderators and admins who will get the email
		$me = KunenaUserHelper::get();
		$acl = KunenaAccess::getInstance();
		$emailToList = $acl->getSubscribers($this->catid, $this->thread, $mailsubs, $mailmods, $mailadmins, $me->userid);

		$topic = $this->getTopic();
		if (count ( $emailToList )) {
			if (! $config->getEmail() ) {
				KunenaError::warning ( JText::_ ( 'COM_KUNENA_EMAIL_DISABLED' ) );
				return false;
			} elseif ( ! JMailHelper::isEmailAddress ( $config->getEmail() ) ) {
				KunenaError::warning ( JText::_ ( 'COM_KUNENA_EMAIL_INVALID' ) );
				return false;
			}
			// clean up the message for review
			$message = KunenaHtmlParser::stripBBCode ( $this->message, 0, false );

			$mailsender = JMailHelper::cleanAddress ( $config->board_title );
			$mailsubject = JMailHelper::cleanSubject ( "[" . $config->board_title . "] " . $topic->subject . " (" . $this->getCategory()->name . ")" );
			$subject = $this->subject ? $this->subject : $topic->subject;

			// Make a list from all receivers
			$sentusers = array();
			$receivers = array(0=>array(), 1=>array());
			foreach ( $emailToList as $emailTo ) {
				if (! $emailTo->email || ! JMailHelper::isEmailAddress ( $emailTo->email )) {
					continue;
				}
				$receivers[$emailTo->subscription][] = $emailTo->email;
				$sentusers[] = $emailTo->id;
			}

			// Create email
			$mail = JFactory::getMailer();
			$mail->setSubject($mailsubject);
			$mail->setSender(array($config->getEmail(), $mailsender));

			// Send email to all subscribers
			if (!empty($receivers[1])) {
				$mail->setBody($this->createEmailBody(1, $subject, $url, $message, $once));
				$this->sendEmail($mail, $receivers[1]);
			}

			// Send email to all moderators
			if (!empty($receivers[0])) {
				$mail->setBody($this->createEmailBody(0, $subject, $url, $message, $once));
				$this->sendEmail($mail, $receivers[0]);
			}

			// Update subscriptions
			if ($once && $sentusers) {
				$sentusers = implode (',', $sentusers);
				$db = JFactory::getDBO();
				$query = "UPDATE #__kunena_user_topics SET subscribed=2 WHERE topic_id={$this->thread} AND user_id IN ({$sentusers}) AND subscribed=1";
				$db->setQuery ($query);
				$db->query ();
				KunenaError::checkDatabaseError();
			}
		}
		return true;
	}

	/**
	 * @param int $value
	 *
	 * @return bool
	 */
	public function publish($value=KunenaForum::PUBLISHED) {
		if ($this->hold == $value)
			return true;

		$this->hold = (int)$value;
		$result = $this->save();
		return $result;
	}

	/**
	 * @return KunenaForumTopic
	 */
	public function getTopic() {
		if (!$this->_topic) {
			$this->_topic = KunenaForumTopicHelper::get($this->thread);
		}
		return $this->_topic;
	}

	/**
	 * @param KunenaForumTopic $topic
	 */
	public function setTopic(KunenaForumTopic $topic) {
		$this->_topic = $topic;
		if ($topic->id) $this->thread = $topic->id;
	}

	/**
	 * @param mixed $user
	 *
	 * @return KunenaForumTopicUser
	 */
	public function getUserTopic($user=null) {
		return $this->getTopic()->getUserTopic($user);
	}

	/**
	 * @return KunenaForumMessageThankyou
	 */
	public function getThankyou() {
		return KunenaForumMessageThankyouHelper::get($this->id);
	}

	/**
	 * @return KunenaForumCategory
	 */
	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->catid);
	}

	/**
	 * @return KunenaForumMessage
	 */
	public function getParent() {
		return KunenaForumMessageHelper::get($this->parent);
	}

	/**
	 * @return KunenaUser
	 */
	public function getAuthor() {
		return KunenaUserHelper::getAuthor($this->userid, $this->name);
	}

	/**
	 * @return KunenaUser
	 */
	public function getModifier() {
		return KunenaUserHelper::get($this->modified_by);
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
			case 'message':
				// FIXME: add context to BBCode parser (and fix logic in the parser)
				return KunenaHtmlParser::parseBBCode($this->message);
		}
		return '';
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
		static $actions  = array(
			'none'=>array(),
			'read'=>array('Read'),
			'reply'=>array('Read','NotHold'),
			'edit'=>array('Read','Own','EditTime'),
			'move'=>array('Read'),
			'approve'=>array('Read'),
			'delete'=>array('Read','Own','EditTime', 'Delete'),
			'thankyou'=>array('Read', 'Thankyou'),
			'unthankyou'=>array('Read'),
			'undelete'=>array('Read'),
			'permdelete'=>array('Read'),
			'attachment.read'=>array('Read'),
			'attachment.create'=>array('Read','Own','EditTime'),
			'attachment.delete'=>array(), // TODO: In the future we might want to restrict this: array('Read','EditTime'),
		);
		$user = KunenaUserHelper::get($user);
		if (!isset($actions[$action])) {
			if (!$silent) $this->setError ( __CLASS__.'::'.__FUNCTION__.'(): '.JText::sprintf ( 'COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action ) );
			return false;
		}
		$topic = $this->getTopic();
		$auth = $topic->authorise('post.'.$action, $user, $silent);
		if (!$auth) {
			if (!$silent) $this->setError ( $topic->getError() );
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
	 * @param array $fields
	 * @param mixed $user
	 */
	public function edit($fields = array(), $user=null) {
		$user = KunenaUserHelper::get($user);

		$this->bind($fields, array ('name', 'email', 'subject', 'message', 'modified_reason' ), true);

		// Update rest of the information
		$category = $this->getCategory();
		$this->hold = $category->review && !$category->authorise('moderate', $user, true) ? 1 : $this->hold;
		$this->modified_by = $user->userid;
		$this->modified_time = JFactory::getDate()->toUnix();
	}

	/**
	 * @param mixed $user
	 */
	public function makeAnonymous($user=null) {
		$user = KunenaUserHelper::get($user);
		if ($user->userid == $this->userid && $this->modified_by == $this->userid) {
			// I am the author and previous modification was made by me => delete modification information to hide my personality
			$this->modified_by = 0;
			$this->modified_time = 0;
			$this->modified_reason = '';
		} else if ($user->userid == $this->userid) {
			// I am the author, but somebody else has modified the message => leave modification information intact
			$this->modified_by = null;
			$this->modified_time = null;
			$this->modified_reason = null;
		}
		// Remove userid, email and ip address
		$this->userid = 0;
		$this->ip = '';
		$this->email = '';
	}

	/**
	 * @param int    $tmpid
	 * @param string $postvar
	 * @param null   $catid
	 *
	 * @return bool
	 */
	public function uploadAttachment($tmpid, $postvar, $catid=null) {
		$attachment = new KunenaForumMessageAttachment();
		$attachment->mesid = $this->id;
		$attachment->userid = $this->userid;
		$success = $attachment->upload($postvar, $catid);
		$this->_attachments_add[$tmpid] = $attachment;
		return $success;
	}

	/**
	 * @param bool|array $ids
	 */
	public function removeAttachment($ids=false) {
		if ($ids === false) {
			$this->_attachments_del = $this->getAttachments();
		} elseif (is_array($ids)) {
			if (!empty($ids)) $this->_attachments_del += array_combine($ids, $ids);
		} else {
			$this->_attachments_del[$ids] = $ids;
		}
	}

	/**
	 * @param bool|array $ids
	 *
	 * @return KunenaForumMessageAttachment[]
	 */
	public function getAttachments($ids=false) {
		if ($ids === false) {
			return KunenaForumMessageAttachmentHelper::getByMessage($this->id);
		} else {
			$attachments = KunenaForumMessageAttachmentHelper::getById($ids);
			foreach ($attachments as $id=>$attachment) if ($attachment->mesid != $this->id) unset($attachments[$id]);
			return $attachments;
		}
	}

	/**
	 * @return bool
	 */
	protected function updateAttachments() {
		// Save new attachments and update message text
		$message = $this->message;
		foreach ($this->_attachments_add as $tmpid=>$attachment) {
			$attachment->mesid = $this->id;
			if (!$attachment->save()) {
				$this->setError ( $attachment->getError() );
			}
			// Update attachments count and fix attachment name inside message
			if ($attachment->exists()) {
				$this->getTopic()->attachments++;
				$this->message = preg_replace('/\[attachment\:'.$tmpid.'\].*?\[\/attachment\]/u', "[attachment={$attachment->id}]{$attachment->filename}[/attachment]", $this->message);
			}
		}
		// Delete removed attachments and update attachments count and message text
		$attachments = $this->getAttachments(array_keys($this->_attachments_del));
		foreach ($attachments as $attachment) {
			if (!$attachment->delete()) {
				$this->setError ( $attachment->getError() );
			} else {
				$this->getTopic()->attachments--;
			}
			$this->message = preg_replace('/\[attachment\='.$attachment->id.'\].*?\[\/attachment\]/u', '', $this->message);
			$this->message = preg_replace('/\[attachment\]'.$attachment->filename.'\[\/attachment\]/u', '', $this->message);
		}
		// Remove missing temporary attachments from the message text
		$this->message = trim(preg_replace('/\[attachment\:\d+\].*?\[\/attachment\]/u', '', $this->message));

		// Return true if we changed the message contents
		return ($this->message != $message);
	}

	/**
	 * Method to load a KunenaForumMessage object by id.
	 *
	 * @param mixed $id	The message id to be loaded
	 *
	 * @return bool	True on success
	 */
	public function load($id = null) {
		$exists = parent::load($id);
		$this->_hold = $exists ? $this->hold : 1;
		$this->_thread = $this->thread;
		return $exists;
	}

	/**
	 * Method to save the KunenaForumMessage object to the database.
	 *
	 * @return	boolean True on success
	 */
	public function save() {
		$isNew = ! $this->_exists;

		$topic = $this->getTopic();
		$newTopic = !$topic->exists();
		if ($newTopic) {
			// Create topic, but do not cascade changes to category etc..
			if (!$topic->save(false)) {
				$this->setError ( $topic->getError () );
				return false;
			}
			$this->_thread = $this->thread = $topic->id;
		}

		// Create message
		if (! parent::save ()) {
			// If we created a new topic, remember to delete it too.
			if ($newTopic) $topic->delete();
			return false;
		}

		if ($isNew) {
			$this->_hold = 1;
		}

		// Update attachments and message text
		$update = $this->updateAttachments();

		// Did we change anything?
		if ($update) {
			if ($isNew && trim($this->message) == '') {
				// Oops, no attachments remain and the message becomes empty.
				// Let's delete the new message and fail on save.
				$this->delete();
				// If we created a new topic, remember to delete it too.
				if ($newTopic) $topic->delete();
				$this->setError(JText::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'));
				return false;
			}
			$table = $this->getTable ();
			$table->bind ( $this->getProperties () );
			$table->exists(true);
			if (! $table->store ()) {
				$this->setError ( $table->getError () );
				return false;
			}
		}

		// Cascade changes to other tables
		$this->update($newTopic);

		return true;
	}

	/**
	 * Method to delete the KunenaForumMessage object from the database.
	 *
	 * @return bool	True on success
	 */
	public function delete() {
		if (!$this->exists()) {
			return true;
		}

		if (!parent::delete()) {
			return false;
		}
		$this->hold = 1;

		$attachments = $this->getAttachments();
		foreach ($attachments as $attachment) {
			if (!$attachment->delete()) {
				$this->setError ( $attachment->getError() );
			}
		}

		$db = JFactory::getDBO ();
		// Delete thank yous
		$queries[] = "DELETE FROM #__kunena_thankyou WHERE postid={$db->quote($this->id)}";
		// Delete message
		$queries[] = "DELETE FROM #__kunena_messages_text WHERE mesid={$db->quote($this->id)}";

		// Cascade changes into other tables
		$this->update();

		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}

		KunenaForumMessageThankyouHelper::recount();

		return true;
	}

	/**
	 * @return bool
	 */
	public function check() {
		$author = KunenaUserHelper::get($this->userid);

		// Check username
		if (! $this->userid) {
			$this->name = trim($this->name);
			// Unregistered or anonymous users: Do not allow existing username
			$nicktaken = JUserHelper::getUserId ( $this->name );
			if (empty ( $this->name ) || $nicktaken) {
				$this->name = JText::_ ( 'COM_KUNENA_USERNAME_ANONYMOUS' );
			}
		} else {
			$this->name = $author->getName();
		}

		// Check email address
		$this->email = trim($this->email);
		if ($this->email) {
			// Email address must be valid
			if (! JMailHelper::isEmailAddress ( $this->email )) {
				$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_INVALID' ) );
				return false;
			}
		} else if (! KunenaUserHelper::getMyself()->exists() && KunenaFactory::getConfig()->askemail) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_EMPTY' ) );
			return false;
		}

		// Do not allow no posting date or dates from the future
		$now = JFactory::getDate()->toUnix();
		if (!$this->time || $this->time > $now) {
			$this->time = $now;
		}
		// Do not allow indentical posting times inside topic (simplifies logic)
		$topic = $this->getTopic();
		if (!$this->exists() && $topic->exists() && $this->time <= $topic->last_post_time) {
			$this->time = $topic->last_post_time + 1;
		}
		if ($this->modified_time > $now) {
			$this->modified_time = $now;
		}
		if ($this->modified_time && $this->modified_time < $this->time) {
			$this->modified_time = $this->time;
		}
		if ($this->hold < 0 || $this->hold > 3) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_MESSAGE_ERROR_HOLD_INVALID' ) );
			return false;
		}
		if ($this->modified_by !== null) {
			if (!$this->modified_by) {
				$this->modified_time = 0;
				$this->modified_reason = '';
			} elseif (!$this->modified_time) {
				$this->modified_time = JFactory::getDate()->toUnix();
			}
		}

		// Flood protection
		$config = KunenaFactory::getConfig();
		if ($config->floodprotection && ! $this->getCategory()->authorise('moderate') ) {
			$this->_db->setQuery ( "SELECT MAX(time) FROM #__kunena_messages WHERE ip={$this->_db->quote($this->ip)}" );
			$lastPostTime = $this->_db->loadResult ();
			if ($this->_db->getErrorNum()) {
				$this->setError ( $this->_db->getErrorMsg() );
				return false;
			}
			if ($lastPostTime + $config->floodprotection > JFactory::getDate()->toUnix()) {
				$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_MESSAGE_ERROR_FLOOD', (int)$config->floodprotection ) );
				return false;
			}
		}

		if (!$this->exists() && !$this->getCategory()->authorise('moderate')) {
			// Ignore identical messages (posted within 5 minutes)
			$duplicatetimewindow = JFactory::getDate ()->toUnix() - 5 * 60;
			$this->_db->setQuery ( "SELECT m.id FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid
				WHERE m.userid={$this->_db->quote($this->userid)}
				AND m.ip={$this->_db->quote($this->ip)}
				AND t.message={$this->_db->quote($this->message)}
				AND m.time>={$this->_db->quote($duplicatetimewindow)}" );
			$id = $this->_db->loadResult ();
			if ($this->_db->getErrorNum()) {
				$this->setError ( $this->_db->getErrorMsg() );
				return false;
			}
			if ($id) {
				$this->setError ( JText::_ ( 'COM_KUNENA_POST_DUPLICATE_IGNORED' ) );
				return false;
			}
		}

		return true;
	}

	// Internal functions

	protected function update($newTopic = false) {
		// If post was published and then moved, we need to update old topic
		if (!$this->_hold && $this->_thread && $this->_thread != $this->thread) {
			$topic = KunenaForumTopicHelper::get($this->_thread);
			if (! $topic->update($this, -1)) {
				$this->setError ( $topic->getError () );
			}
		}

		$postDelta = $this->delta(true);
		$topic = $this->getTopic();
		// New topic
		if ($newTopic) {
			$topic->hold = 0;
		}
		// Update topic
		if (!$this->hold && $topic->hold && $topic->exists()) {
			// We published message -> publish and recount topic
			$topic->hold = 0;
			$topic->recount();
		} elseif (! $topic->update($this, $postDelta)) {
			$this->setError ( $topic->getError () );
		}

		// Activity integration
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');
		$activity = KunenaFactory::getActivityIntegration();
		if ($postDelta < 0) {
			$dispatcher->trigger('onDeleteKunenaPost', array(array($this->id)));
			$activity->onAfterDelete($this);
		} elseif ($postDelta > 0) {
			$topic->markRead();
			if ($this->parent == 0) {
				$activity->onAfterPost($this);
			} else {
				$activity->onAfterReply($this);
			}
		}
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return bool
	 */
	protected function authoriseRead(KunenaUser $user) {
		// Check that user has the right to see the post (user can see his own unapproved posts)
		if ($this->hold > 1 || ($this->hold == 1 && $this->userid != $user->userid)) {
			$access = KunenaAccess::getInstance();
			$hold = $access->getAllowedHold($user->userid, $this->catid, false);
			if (!in_array($this->hold, $hold)) {
				$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
				return false;
			}
		}
		return true;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return bool
	 */
	protected function authoriseNotHold(KunenaUser $user) {
		if ($this->hold) {
			// Nobody can reply to unapproved or deleted post
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return bool
	 */
	protected function authoriseOwn(KunenaUser $user) {
		// Check that topic owned by the user or user is a moderator
		// TODO: check #__kunena_user_topics
		if ((!$this->userid || $this->userid != $user->userid) && !$user->isModerator($this->getCategory())) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ) );
			return false;
		}
		return true;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return bool
	 */
	protected function authoriseThankyou(KunenaUser $user) {
		// Check that message is not your own
		if(!KunenaFactory::getConfig()->showthankyou) {
			$this->setError ( JText::_ ( 'COM_KUNENA_THANKYOU_DISABLED' ) );
			return false;
		}

		if (!$user->userid || !$this->userid || $this->userid == $user->userid) {
			// TODO: better error message
			$this->setError ( JText::_ ( 'COM_KUNENA_THANKYOU_DISABLED' ) );
			return false;
		}
		return true;
	}

	/**
	 * This function check the edit time from the author of the post and return if the user is allowed to edit his post.
	 *
	 * @param KunenaUser $user
	 *
	 * @return bool
	 */
	protected function authoriseEditTime(KunenaUser $user) {
		// Do not perform rest of the checks to moderators and admins
		if ($user->isModerator($this->getCategory())) {
			return true;
		}
		// User is only allowed to edit post within time specified in the configuration
		$config = KunenaFactory::getConfig ();
		if (intval($config->useredit) != 1) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ) );
			return false;
		}
		if (intval($config->useredittime) == 0) {
			return true;
		} else {
			//Check whether edit is in time
			$modtime = $this->modified_time;
			if (! $modtime) {
				$modtime = $this->time;
			}
			if ($modtime + intval($config->useredittime)< JFactory::getDate ()->toUnix()) {
				$this->setError ( JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ) );
				return false;
			}
		}
		return true;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return bool
	 */
	protected function authoriseDelete(KunenaUser $user) {
		$config = KunenaFactory::getConfig();
		if (!$user->isModerator($this->getCategory())
				&& $config->userdeletetmessage != '2' && ($config->userdeletetmessage == '0' || $this->getTopic()->last_post_id != $this->id)) {
			$this->setError (JText::_ ( 'COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER' ) );
			return false;
		}
		return true;
	}

	/**
	 * @return int
	 */
	protected function delta() {
		if (!$this->hold && ($this->_hold || $this->thread != $this->_thread)) {
			// Publish message or move it into new topic
			return 1;
		} elseif (!$this->_hold && $this->hold) {
			// Unpublish message
			return -1;
		}
		return 0;
	}

	/**
	 * @param object $mail
	 * @param array  $receivers
	 */
	protected function sendEmail($mail, array $receivers) {
		$config = KunenaFactory::getConfig();
		$email_recipient_count = !empty($config->email_recipient_count) ? $config->email_recipient_count : 1;
		$email_recipient_privacy = !empty($config->email_recipient_privacy) ? $config->email_recipient_privacy : 'bcc';

		// If we hide email addresses from other users, we need to add TO address to prevent email from becoming spam
		if ($email_recipient_count > 1 && $email_recipient_privacy == 'bcc'
			&& !empty($config->email_visible_address) && JMailHelper::isEmailAddress ( $config->email_visible_address )) {
			$mail->AddAddress($config->email_visible_address, JMailHelper::cleanAddress ( $config->board_title ));
			// Also make sure that email receiver limits are not violated (TO + CC + BCC = limit)
			if ($email_recipient_count > 9) $email_recipient_count--;
		}

		$chunks = array_chunk($receivers, $email_recipient_count);
		foreach ($chunks as $emails) {
			if ($email_recipient_count == 1 || $email_recipient_privacy == 'to') {
				$mail->ClearAddresses();
				$mail->addRecipient($emails);
			} elseif ($email_recipient_privacy == 'cc') {
				$mail->ClearCCs();
				$mail->addCC($emails);
			} else {
				$mail->ClearBCCs();
				$mail->addBCC($emails);
			}
			$mail->Send();
		}
	}

	/**
	 * @param int $subscription
	 * @param string $subject
	 * @param string $url
	 * @param string $message
	 * @param bool $once
	 *
	 * @return string
	 */
	protected function createEmailBody($subscription, $subject, $url, $message, $once) {
		$config = KunenaFactory::getConfig();
		if ($subscription) {
			$msg1 = $this->get ( 'parent' ) ? JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION1' ) : JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION1_CAT' );
			$msg2 = $this->get ( 'parent' ) ? JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION2' ) : JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION2_CAT' );
		} else {
			$msg1 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD1' );
			$msg2 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD2' );
		}

		$msg = $msg1 . " " . $config->board_title . "\n\n";
		// DO NOT REMOVE EXTRA SPACE, JMailHelper::cleanBody() removes "Subject:" from the message body
		$msg .= JText::_ ( 'COM_KUNENA_MESSAGE_SUBJECT' ) . " : " . $subject . "\n";
		$msg .= JText::_ ( 'COM_KUNENA_CATEGORY' ) . " : " . $this->getCategory()->name . "\n";
		$msg .= JText::_ ( 'COM_KUNENA_VIEW_POSTED' ) . " : " . $this->getAuthor()->getName('???', false) . "\n\n";
		$msg .= "URL : $url\n\n";
		if ($config->mailfull == 1) {
			$msg .= JText::_ ( 'COM_KUNENA_MESSAGE' ) . " :\n-----\n";
			$msg .= $message;
			$msg .= "\n-----\n\n";
		}
		$msg .= $msg2 . "\n";
		if ($subscription && $once) {
			if ($this->parent) {
				$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_READ' ) . "\n";
			} else {
				$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_SUBSCRIBE' ) . "\n";
			}
		}
		$msg .= "\n";
		$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION3' ) . "\n";
		return JMailHelper::cleanBody ( $msg );
	}
}
