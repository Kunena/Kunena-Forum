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

jimport ('joomla.user.helper');
jimport ('joomla.mail.helper');
kimport ('kunena.error');
kimport ('kunena.user.helper');
kimport ('kunena.forum.category.helper');
kimport ('kunena.forum.topic.helper');
kimport ('kunena.forum.message.helper');
kimport ('kunena.forum.message.attachment');
kimport ('kunena.forum.message.attachment.helper');
kimport ('kunena.forum.message.thankyou.helper');

/**
 * Kunena Forum Message Class
 */
class KunenaForumMessage extends JObject {
	protected $_exists = false;
	protected $_db = null;
	protected $_attachments_add = array();
	protected $_attachments_del = array();
	protected $_topic = null;
	protected $_hold = 1;
	protected $_thread = 0;

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($identifier = 0) {
		// Always load the message -- if message does not exist: fill empty data
		$this->_db = JFactory::getDBO ();
		if($identifier !== false) $this->load ( $identifier );
	}

	/**
	 * Returns KunenaForumMessage object
	 *
	 * @access	public
	 * @param	identifier		The message to load - Can be only an integer.
	 * @return	KunenaForumMessage		The message object.
	 * @since	1.7
	 */
	static public function getInstance($identifier = null, $reload = false) {
		return KunenaForumMessageHelper::get($identifier, $reload);
	}

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	public function isNew() {
		static $readtopics = false;

		if (!KunenaFactory::getConfig()->shownew) {
			return false;
		}
		$session = KunenaFactory::getSession ();
		if (!$session->userid)
			return false;
		if ($readtopics === false)
			$readtopics = explode(',', $session->readtopics);
		return $this->time > $session->lasttime && !in_array($this->thread, $readtopics);
	}

	public function newReply($fields=array(), $user=null) {
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
		} elseif (is_array($fields)) {
			$message->bind($fields, array ('name', 'email', 'subject', 'message' ));
		}
		return array($topic, $message);
	}

	public function sendNotification($url=null) {
		$config = KunenaFactory::getConfig();
		if ($this->hold > 1) {
			return;
		} elseif ($this->hold == 1) {
			$mailsubs = 0;
			$mailmods = (bool) $config->mailmod;
			$mailadmins = (bool) $config->mailadmin;
		} else {
			$mailsubs = (bool) $config->allowsubscriptions;
			$mailmods = 0;
			$mailadmins = 0;
		}

		kimport ('kunena.html.parser');
		$once = false;
		if ($mailsubs) {
			if (!$this->parent) {
				// New topic: Send email only to category subscribers
				$mailsubs = $config->category_subscriptions != 'disabled' ? 3 : 0;
				$once = $config->category_subscriptions == 'topic';
			} elseif ($config->category_subscriptions != 'post') {
				// Existing topic: Send email only to topic subscribers
				$mailsubs = $config->topic_subscriptions != 'disabled' ? 2 : 0;
				$once = $config->topic_subscriptions == 'first';
			} else {
				// Existing topic: Send email to both category and topic subscribers
				$mailsubs = $config->topic_subscriptions == 'disabled' ? 3 : 1;
				// FIXME: category subcription can override topic
				$once = $config->topic_subscriptions == 'first';
			}
		}

		if (!$url) {
			$url = JURI::root().trim(CKunenaLink::GetMessageURL($this->id, $this->catid, 0, false), '/');
		}
		//get all subscribers, moderators and admins who will get the email
		$me = KunenaUserHelper::get();
		$acl = KunenaFactory::getAccessControl();
		$emailToList = $acl->getSubscribers($this->catid, $this->thread, $mailsubs, $mailmods, $mailadmins, $me->userid);

		$category = $this->getCategory();
		$topic = $this->getTopic();
		if (count ( $emailToList )) {
			jimport('joomla.mail.helper');
			if (! $config->email ) {
				KunenaError::warning ( JText::_ ( 'COM_KUNENA_EMAIL_DISABLED' ) );
				return false;
			} elseif ( ! JMailHelper::isEmailAddress ( $config->email ) ) {
				KunenaError::warning ( JText::_ ( 'COM_KUNENA_EMAIL_INVALID' ) );
				return false;
			}
			// clean up the message for review
			$message = KunenaHtmlParser::plainBBCode ( $this->message );

			$mailsender = JMailHelper::cleanAddress ( $config->board_title );
			$mailsubject = JMailHelper::cleanSubject ( "[" . $config->board_title . "] " . $topic->subject . " (" . $category->name . ")" );
			$subject = $this->subject ? $this->subject : $topic->subject;

			$sentusers = array();
			foreach ( $emailToList as $emailTo ) {
				if (! $emailTo->email || ! JMailHelper::isEmailAddress ( $emailTo->email ))
					continue;

				if ($emailTo->subscription) {
					$msg1 = $this->parent ? JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION1' ) : JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION1_CAT' );
					$msg2 = $this->parent ? JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION2' ) : JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION2_CAT' );
				} else {
					$msg1 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD1' );
					$msg2 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD2' );
				}

				$msg = "$emailTo->name,\n\n";
				$msg .= $msg1 . " " . $config->board_title . "\n\n";
				// DO NOT REMOVE EXTRA SPACE, JMailHelper::cleanBody() removes "Subject:" from the message body
				$msg .= JText::_ ( 'COM_KUNENA_MESSAGE_SUBJECT' ) . " : " . $subject . "\n";
				$msg .= JText::_ ( 'COM_KUNENA_GEN_CATEGORY' ) . " : " . $category->name . "\n";
				$msg .= JText::_ ( 'COM_KUNENA_VIEW_POSTED' ) . " : " . $this->name . "\n\n";
				$msg .= "URL : {$url}\n\n";
				if ($config->mailfull) {
					$msg .= JText::_ ( 'COM_KUNENA_GEN_MESSAGE' ) . " :\n-----\n";
					$msg .= $message;
					$msg .= "\n-----\n\n";
				}
				$msg .= $msg2 . "\n";
				if ($emailTo->subscription && $once) {
					if ($this->parent) {
						$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_READ' ) . "\n";
					} else {
						$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_SUBSCRIBE' ) . "\n";
					}
				}
				$msg .= "\n";
				$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION3' ) . "\n";
				$msg = JMailHelper::cleanBody ( $msg );
				JUtility::sendMail ( $config->email, $mailsender, $emailTo->email, $mailsubject, $msg );
				$sentusers[] = $emailTo->id;
			}
			if ($once && $sentusers) {
				$sentusers = implode (',', $sentusers);
				$db = JFactory::getDBO();
				$query = "UPDATE #__kunena_user_topics SET subscribed=2 WHERE topic_id={$this->thread} AND user_id IN ({$sentusers}) AND subscribed=1";
				$db->setQuery ($query);
				$db->query ();
				KunenaError::checkDatabaseError();
			}
		}
	}

	public function publish($value=KunenaForum::PUBLISHED) {
		if ($this->hold == $value)
			return true;

		$this->hold = (int)$value;
		$result = $this->save();
		return $result;
	}

	public function getTopic() {
		if ($this->_topic) {
			return $this->_topic;
		}
		return KunenaForumTopicHelper::get($this->thread);
	}

	public function setTopic($topic) {
		$this->_topic = $topic;
		if ($topic->id) $this->thread = $topic->id;
	}

	public function getUserTopic($user=null) {
		return $this->getTopic()->getUserTopic($user);
	}

	public function getThankyou() {
		return KunenaForumMessageThankyouHelper::get($this->id);
	}

	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->catid);
	}

	public function getAuthor() {
		return KunenaUserHelper::getAuthor($this->userid, $this->name);
	}

	public function authorise($action='read', $user=null, $silent=false) {
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
			'attachment.delete'=>array('Read','Own','EditTime'),
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

	public function edit($fields = array(), $user=null) {
		$user = KunenaUserHelper::get($user);

		$this->bind($fields, array ('name', 'email', 'subject', 'message', 'modified_reason' ));

		// Update rest of the information
		$category = $this->getCategory();
		$this->hold = $category->review ? (int)!$category->authorise ('moderate', $user, true) : 0;
		$this->modified_by = $user->userid;
		$this->modified_time = JFactory::getDate()->toUnix();
	}

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

	public function uploadAttachment($tmpid, $postvar) {
		$attachment = new KunenaForumMessageAttachment();
		$attachment->mesid = $this->id;
		$attachment->userid = $this->userid;
		$success = $attachment->upload($postvar);
		$this->_attachments_add[$tmpid] = $attachment;
		return $success;
	}

	public function removeAttachment($ids=false) {
		if ($ids === false) {
			$this->_attachments_del = $this->getAttachments();
		} elseif (is_array($ids)) {
			$this->_attachments_del += array_combine($ids, $ids);
		} else {
			$this->_attachments_del[$ids] = $ids;
		}
	}

	public function getAttachments($ids=false) {
		if ($ids === false) {
			return KunenaForumMessageAttachmentHelper::getByMessage($this->id);
		} else {
			return KunenaForumMessageAttachmentHelper::getById($ids);
		}
	}

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
	 * Method to get the messages table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The messages table name to be used
	 * @param	string	The messages table prefix to be used
	 * @return	object	The messages table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaMessages', $prefix = 'Table') {
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
	 * Method to load a KunenaForumMessage object by id
	 *
	 * @access	public
	 * @param	mixed	$id The message id to be loaded
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
		$this->_thread = $this->thread;
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaForumMessage object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new message
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
		//are we creating a new message
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new message return
		if ($isnew && $updateOnly) {
			$this->setError ( JText::_('COM_KUNENA_LIB_MESSAGE_ERROR_UPDATE_ONLY') );
			return false;
		}

		if (! $this->check ()) {
			return false;
		}

		// Create the messages table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Check the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		// Store the message data in the database
		if (!$table->store ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		// Load KunenaForumMessage object in case we created a new message.
		if ($isnew) {
			$this->load ( $table->id );
			$this->_hold = 1;
		}

		$update = 0;
		if (!$this->thread) {
			// Update missing topic information
			$this->_thread = $this->thread = $this->id;
			++$update;
		}

		// Update attachments and message text
		$update += $this->updateAttachments();

		// Did we change anything?
		if ($update) {
			$table->bind ( $this->getProperties () );
			if (! $table->store ()) {
				$this->setError ( $table->getError () );
				return false;
			}
		}

		// Cascade changes to other tables
		$this->update();

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');
		$dispatcher->trigger('onAfterSaveKunenaPost', array($this->id));
		return true;
	}

	/**
	 * Method to delete the KunenaForumMessage object from the database
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
		$table = $this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
			return false;
		}
		$this->_exists = false;
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

		return $result;
	}

	// Internal functions

	protected function update() {
		// If post was published and then moved, we need to update old topic
		if (!$this->_hold && $this->_thread && $this->_thread != $this->thread) {
			$topic = KunenaForumTopicHelper::get($this->_thread);
			if (! $topic->update($this, -1)) {
				$this->setError ( $topic->getError () );
			}
		}

		$postDelta = $this->delta(true);
		$topic = $this->getTopic();
		// Create / update topic
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
			if ($this->parent == 0) {
				$me = KunenaUserHelper::getMyself();
				$topic->markRead();
				$activity->onAfterPost($this);
			} else {
				$topic->markNew();
				$activity->onAfterReply($this);
			}
		}
	}

	protected function authoriseRead($user) {
		// Check that user has the right to see the post (user can see his own unapproved posts)
		if ($this->hold > 1 || ($this->hold == 1 && $this->userid != $user->userid)) {
			$access = KunenaFactory::getAccessControl();
			$hold = $access->getAllowedHold($user->userid, $this->catid, false);
			if (!in_array($this->hold, $hold)) {
				$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
				return false;
			}
		}
		return true;
	}
	protected function authoriseNotHold($user) {
		if ($this->hold) {
			// Nobody can reply to unapproved or deleted post
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}
	protected function authoriseOwn($user) {
		// Check that topic owned by the user or user is a moderator
		// TODO: check #__kunena_user_topics
		if ((!$this->userid || $this->userid != $user->userid) && !$user->isModerator($this->catid)) {
			$this->setError ( JText::_ ( 'COM_KUNENA_POST_EDIT_NOT_ALLOWED' ) );
			return false;
		}
		return true;
	}
	protected function authoriseThankyou($user) {
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
	 * This function check the edit time for the author of the author
	 * of the post and return if the user is allwoed or not to edit
	 * her post
	 *
	 * @param timestamp $messagemodifiedtime	Time when the message has been edited
	 * @param timestamp $messagetime			Actual message time
	 */
	protected function authoriseEditTime($user) {
		// Do not perform rest of the checks to moderators and admins
		if ($user->isModerator($this->catid)) {
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

	protected function authoriseDelete($user) {
		if (!$user->isModerator($this->catid) && $this->getTopic()->last_post_id != $this->id) {
			$this->setError (JText::_ ( 'COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER' ) );
			return false;
		}
		return true;
	}


	protected function check() {
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
		} else if (! KunenaFactory::getUser()->userid && KunenaFactory::getConfig()->askemail) {
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_EMPTY' ) );
			return false;
		}

		if (!$this->time) {
			$this->time = JFactory::getDate()->toUnix();
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

		if (!$this->exists()) {
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
}