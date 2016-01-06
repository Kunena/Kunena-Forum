<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Message
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
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
class KunenaForumMessage extends KunenaDatabaseObject
{
	/**
	 * @var int
	 */
	public $id = null;

	protected $_table = 'KunenaMessages';
	protected $_db = null;
	/**
	 * @var KunenaAttachment[]
	 */
	protected $_attachments_add = array();
	/**
	 * @var KunenaAttachment[]
	 */
	protected $_attachments_del = array();
	protected $_topic = null;
	protected $_hold = 1;
	protected $_thread = 0;

	protected $_authcache = array();
	protected $_authtcache = array();
	protected $_authfcache = array();
	protected static $actions = array(
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
		'attachment.createimage'=>array('Read','AttachmentsImage'),
		'attachment.createfile'=>array('Read','AttachmentsFile'),
		'attachment.delete'=>array(),
		// TODO: In the future we might want to restrict this: array('Read','EditTime'),
	);

	/**
	 * @param mixed $properties
	 *
	 * @internal
	 */
	public function __construct($properties = null)
	{
		$this->_db = JFactory::getDbo();
		parent::__construct($properties);
	}

	public function __destruct()
	{
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
	static public function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumMessageHelper::get($identifier, $reload);
	}

	/**
	 * @param mixed $user
	 *
	 * @return bool
	 */
	public function isNew($user = null)
	{
		$user = KunenaUserHelper::get($user);
		if (!KunenaFactory::getConfig()->shownew || !$user->exists())
		{
			return false;
		}

		$session = KunenaFactory::getSession ();

		if ($this->time < $session->getAllReadTime())
		{
			return false;
		}

		$allreadtime = KunenaForumCategoryUserHelper::get($this->getCategory(), $user)->allreadtime;

		if ($allreadtime && $this->time < $allreadtime)
		{
			return false;
		}

		$read = KunenaForumTopicUserReadHelper::get($this->getTopic(), $user);

		if ($this->id == $read->message_id || $this->time < $read->time) {
			return false;
		}

		return true;
	}

	/**
	 * Get published state in text.
	 *
	 * @return string
	 *
	 * @since  K4.0
	 */
	public function getState()
	{
		switch ($this->hold) {
			case 0:
				return 'published';
			case 1:
				return 'unapproved';
			case 2:
			case 3:
				return 'deleted';
		}

		return 'unknown';
	}

	/**
	 * @param null|KunenaForumCategory $category	Fake category if needed. Used for aliases.
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public function getUrl($category = null, $xhtml = true)
	{
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
	public function getPermaUrl($category = null, $xhtml = true)
	{
		$uri = $this->getPermaUri($category);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * @param null|KunenaForumCategory $category
	 *
	 * @return JUri
	 */
	public function getPermaUri($category = null)
	{
		$category = $category ? KunenaForumCategoryHelper::get($category) : $this->getCategory();

		if (!$this->exists() || !$category->exists())
		{
			return null;
		}

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
	public function newReply($fields = array(), $user = null, $safefields = null)
	{
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

		if ($topic->hold)
		{
			// If topic was unapproved or deleted, use the same state for the new message
			$message->hold = $topic->hold;
		}
		else
		{
			// Otherwise message is either unapproved or published depending if the category is moderated or not
			$message->hold = $category->review ? (int)!$category->authorise ('moderate', $user, true) : 0;
		}

		if ($fields === true)
		{
			$user = KunenaFactory::getUser($this->userid);
			$text = preg_replace('/\[confidential\](.*?)\[\/confidential\]/su', '', $this->message );
			$text = preg_replace('/\[hide\](.*?)\[\/hide\]/su', '', $this->message );
			$message->message = "[quote=\"{$user->getName($this->name)}\" post={$this->id}]" .  $text . "[/quote]";
		}
		else
		{
			if (is_array($safefields))
			{
				$message->bind($safefields);
			}

			if (is_array($fields))
			{
				$message->bind($fields, array ('name', 'email', 'subject', 'message' ), true);
			}
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
	public function sendNotification($url = null)
	{
		$config = KunenaFactory::getConfig();

		if (!$config->get('send_emails'))
		{
			return null;
		}

		if ($this->hold > 1)
		{
			return null;
		}
		elseif ($this->hold == 1)
		{
			$mailsubs = 0;
			$mailmods = $config->mailmod >= 0;
			$mailadmins = $config->mailadmin >= 0;
		}
		else
		{
			$mailsubs = (bool) $config->allowsubscriptions;
			$mailmods = $config->mailmod >= 1;
			$mailadmins = $config->mailadmin >= 1;
		}

		$once = false;

		if ($mailsubs)
		{
			if (!$this->parent)
			{
				// New topic: Send email only to category subscribers
				$mailsubs = $config->category_subscriptions != 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : 0;
				$once = $config->category_subscriptions == 'topic';
			}
			elseif ($config->category_subscriptions != 'post')
			{
				// Existing topic: Send email only to topic subscribers
				$mailsubs = $config->topic_subscriptions != 'disabled' ? KunenaAccess::TOPIC_SUBSCRIPTION : 0;
				$once = $config->topic_subscriptions == 'first';
			}
			else
			{
				// Existing topic: Send email to both category and topic subscribers
				$mailsubs = $config->topic_subscriptions == 'disabled'
					? KunenaAccess::CATEGORY_SUBSCRIPTION
					: KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION;
				// FIXME: category subscription can override topic
				$once = $config->topic_subscriptions == 'first';
			}
		}

		if (!$url)
		{
			$url = JUri::getInstance()->toString(array('scheme', 'host', 'port')) . $this->getPermaUrl();
		}

		// Get all subscribers, moderators and admins who should get the email.
		$emailToList = KunenaAccess::getInstance()->getSubscribers(
			$this->catid,
			$this->thread,
			$mailsubs,
			$mailmods,
			$mailadmins,
			KunenaUserHelper::getMyself()->userid
		);

		if ($emailToList)
		{
			if (!$config->getEmail())
			{
				KunenaError::warning(JText::_('COM_KUNENA_EMAIL_DISABLED'));

				return false;
			}
			elseif (!JMailHelper::isEmailAddress($config->getEmail()))
			{
				KunenaError::warning(JText::_('COM_KUNENA_EMAIL_INVALID'));

				return false;
			}

			$topic = $this->getTopic();

			// Make a list from all receivers; split the receivers into two distinct groups.
			$sentusers = array();
			$receivers = array(0 => array(), 1 => array());

			foreach($emailToList as $emailTo)
			{
				if (!$emailTo->email || !JMailHelper::isEmailAddress($emailTo->email))
				{
					continue;
				}

				$receivers[$emailTo->subscription][] = $emailTo->email;
				$sentusers[] = $emailTo->id;
			}

			$mailsender = JMailHelper::cleanAddress($config->board_title);
			$mailsubject = JMailHelper::cleanSubject ( $config->board_title . ' ' . $topic->subject . " (" . $this->getCategory()->name . ")" );
			$subject = $this->subject ? $this->subject : $topic->subject;

			// Create email.
			$mail = JFactory::getMailer();
			$mail->setSubject($mailsubject);
			$mail->setSender(array($config->getEmail(), $mailsender));

			// Send email to all subscribers.
			if (!empty($receivers[1]))
			{
				$this->attachEmailBody($mail, 1, $subject, $url, $once);
				KunenaEmail::send($mail, $receivers[1]);
			}

			// Send email to all moderators.
			if (!empty($receivers[0]))
			{
				$this->attachEmailBody($mail, 0, $subject, $url, $once);
				KunenaEmail::send($mail, $receivers[0]);
			}

			// Update subscriptions.
			if ($once && $sentusers)
			{
				$sentusers = implode (',', $sentusers);
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->update('#__kunena_user_topics')
					->set('subscribed=2')
					->where("topic_id={$this->thread}")
					->where("user_id IN ({$sentusers})")
					->where('subscribed=1');

				$db->setQuery($query);
				$db->execute();
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
	public function publish($value=KunenaForum::PUBLISHED)
	{
		if ($this->hold == $value)
		{
			return true;
		}

		$this->hold = (int)$value;
		$result = $this->save();

		return $result;
	}

	/**
	 * @return KunenaForumTopic
	 */
	public function getTopic()
	{
		if (!$this->_topic)
		{
			$this->_topic = KunenaForumTopicHelper::get($this->thread);
		}

		return $this->_topic;
	}

	/**
	 * @param KunenaForumTopic $topic
	 */
	public function setTopic(KunenaForumTopic $topic)
	{
		$this->_topic = $topic;

		if ($topic->id)
		{
			$this->thread = $topic->id;
		}
	}

	/**
	 * @param mixed $user
	 *
	 * @return KunenaForumTopicUser
	 */
	public function getUserTopic($user = null)
	{
		return $this->getTopic()->getUserTopic($user);
	}

	/**
	 * @return KunenaForumMessageThankyou
	 */
	public function getThankyou()
	{
		return KunenaForumMessageThankyouHelper::get($this->id);
	}

	/**
	 * @return KunenaForumCategory
	 */
	public function getCategory()
	{
		return KunenaForumCategoryHelper::get($this->catid);
	}

	/**
	 * @return KunenaForumMessage
	 */
	public function getParent()
	{
		return KunenaForumMessageHelper::get($this->parent);
	}

	/**
	 * @return KunenaUser
	 */
	public function getAuthor()
	{
		return KunenaUserHelper::getAuthor($this->userid, $this->name);
	}

	/**
	 * @return KunenaDate
	 */
	public function getTime()
	{
		return new KunenaDate($this->time);
	}

	/**
	 * @return KunenaUser
	 */
	public function getModifier()
	{
		return KunenaUserHelper::get($this->modified_by);
	}

	/**
	 * @return KunenaDate
	 */
	public function getModifiedTime()
	{
		return new KunenaDate($this->modified_time);
	}

	/**
	 * Display required field from message table
	 *
	 * @param string $field
	 * @param boolean $html
	 * @param string $context
	 *
	 * @return int|string
	 */
	public function displayField($field, $html = true, $context = '')
	{
		switch ($field)
		{
			case 'id':
				return intval($this->id);
			case 'subject':
				return KunenaHtmlParser::parseText($this->subject);
			case 'message':
				// FIXME: add context to BBCode parser (and fix logic in the parser)
				return $html ? KunenaHtmlParser::parseBBCode($this->message, $this, 0, $context) : KunenaHtmlParser::stripBBCode
					($this->message, $this->parent, $html);
		}

		return '';
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param string     $action
	 * @param KunenaUser $user
	 *
	 * @return bool
	 *
	 * @since  K4.0
	 */
	public function isAuthorised($action='read', KunenaUser $user = null)
	{
		return !$this->tryAuthorise($action, $user, false);
	}

	/**
	 * Throws an exception if user isn't authorised to do the action.
	 *
	 * @param string      $action
	 * @param KunenaUser  $user
	 * @param bool        $throw
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws KunenaExceptionAuthorise
	 * @throws InvalidArgumentException
	 *
	 * @since  K4.0
	 */
	public function tryAuthorise($action='read', KunenaUser $user = null, $throw = true)
	{
		// Special case to ignore authorisation.
		if ($action == 'none')
		{
			return null;
		}

		// Load user if not given.
		if ($user === null)
		{
			$user = KunenaUserHelper::getMyself();
		}

		if (empty($this->_authcache[$user->userid][$action]))
		{
			// Unknown action - throw invalid argument exception.
			if (!isset(self::$actions[$action]))
			{
				throw new InvalidArgumentException(JText::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
			}

			// Load category authorisation.
			if (!isset($this->_authtcache[$user->userid][$action]))
			{
				$this->_authtcache[$user->userid][$action] = $this->getTopic()->tryAuthorise('post.'.$action, $user, false);
			}

			$this->_authcache[$user->userid][$action] = $this->_authtcache[$user->userid][$action];

			if (empty($this->_authcache[$user->userid][$action]))
			{
				foreach (self::$actions[$action] as $function)
				{
					if (!isset($this->_authfcache[$user->userid][$function]))
					{
						$authFunction = 'authorise'.$function;
						$this->_authfcache[$user->userid][$function] = $this->$authFunction($user);
					}

					$error = $this->_authfcache[$user->userid][$function];

					if ($error)
					{
						$this->_authcache[$user->userid][$action] = $error;
						break;
					}
				}
			}
		}
		$exception = $this->_authcache[$user->userid][$action];

		// Throw or return the exception.
		if ($throw && $exception)
		{
			throw $exception;
		}

		return $exception;
	}

	 /**
	 * @param string $action
	 * @param mixed  $user
	 * @param bool   $silent
	 *
	 * @return bool
	 * @deprecated K4.0
	 */
	public function authorise($action = 'read', $user = null, $silent = false)
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($user === null)
		{
			$user = KunenaUserHelper::getMyself();
		}
		elseif (!($user instanceof KunenaUser))
		{
			$user = KunenaUserHelper::get($user);
		}

		$exception = $this->tryAuthorise($action, $user, false);

		if ($silent === false && $exception)
		{
			$this->setError($exception->getMessage());
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if ($silent !== null)
		{
			return !$exception;
		}

		return $exception ? $exception->getMessage() : null;
	}

	/**
	 * @param array $fields
	 * @param mixed $user
	 */
	public function edit($fields = array(), $user = null)
	{
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
	public function makeAnonymous($user = null)
	{
		$user = KunenaUserHelper::get($user);

		if ($user->userid == $this->userid && $this->modified_by == $this->userid)
		{
			// I am the author and previous modification was made by me => delete modification information to hide my personality
			$this->modified_by = 0;
			$this->modified_time = 0;
			$this->modified_reason = '';
		}
		else if ($user->userid == $this->userid)
		{
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
	public function uploadAttachment($tmpid, $postvar, $catid = null)
	{
		$attachment = new KunenaAttachment;
		$attachment->userid = $this->userid;
		$success = $attachment->upload($postvar, $catid);
		$this->_attachments_add[$tmpid] = $attachment;

		return $success;
	}

	/**
	 * Add listed attachments to the message.
	 *
	 * If attachment is already pointing to the message, this function has no effect.
	 * Currently only orphan attachments can be added.
	 *
	 * @param array $ids
	 * @since  K4.0
	 */
	public function addAttachments(array $ids)
	{
		$this->_attachments_add += $this->getAttachments($ids, 'none');
	}

	/**
	 * Remove listed attachments from the message.
	 *
	 * @param array $ids
	 * @since  K4.0
	 */
	public function removeAttachments(array $ids)
	{
		$this->_attachments_del += $this->getAttachments($ids, 'none');
	}

	/**
	 * Remove listed attachments from the message.
	 *
	 * @param bool|int|array $ids
	 * @deprecated K4.0
	 */
	public function removeAttachment($ids)
	{
		if ($ids === false)
		{
			$ids = array_keys($this->getAttachments());
		}
		elseif (!is_array($ids))
		{
			$ids = array((int) $ids);
		}

		$this->removeAttachments($ids);
	}

	/**
	 * Get the number of attachments into a message
	 *
	 * @return array
	 */
	public function getNbAttachments()
	{
		$attachments = KunenaAttachmentHelper::getNumberAttachments($this->id);

		$attachs = new StdClass();
		$attachs->image = 0;
		$attachs->file = 0;
		$attachs->total = 0;

		foreach($attachments as $attach)
		{
			if ($attach->isImage())
			{
				$attachs->image = $attachs->image+1;
			}
			else
			{
				$attachs->file = $attachs->file+1;
			}

      $attachs->total = $attachs->total+1;
		}

		return $attachs;
	}

	/**
	 * @param  bool|array  $ids
	 * @param  string      $action
	 *
	 * @return KunenaAttachment[]
	 */
	public function getAttachments($ids=false, $action = 'read')
	{
		if ($ids === false)
		{
			$attachments = KunenaAttachmentHelper::getByMessage($this->id, $action);
		}
		else
		{
			$attachments = KunenaAttachmentHelper::getById($ids, $action);

			foreach ($attachments as $id=>$attachment)
			{
				if ($attachment->mesid && $attachment->mesid != $this->id)
				{
					unset($attachments[$id]);
				}
			}
		}

		return $attachments;
	}

	/**
	 * @return bool
	 */
	protected function updateAttachments()
	{
		// Save new attachments and update message text
		$message = $this->message;
		foreach ($this->_attachments_add as $tmpid=>$attachment)
		{
			if ($attachment->exists() && $attachment->mesid)
			{
				// Attachment exists and already belongs to a message => update.
				if (!$attachment->save())
				{
					$this->setError($attachment->getError());
					continue;
				}

				continue;
			}

			$attachment->mesid = $this->id;

			if ($attachment->IsImage())
			{
				$exception = $attachment->tryAuthorise('createimage', null, false);
			}
			else
			{
				$exception = $attachment->tryAuthorise('createfile', null, false);
			}

			if ($exception)
			{
				$this->setError($exception->getMessage());
				continue;
			}

			if (!$attachment->save())
			{
				$this->setError($attachment->getError());
				continue;
			}

			// Update attachments count and fix attachment name inside message
			$this->getTopic()->attachments++;
			$this->message = preg_replace('/\[attachment\:'.$tmpid.'\].*?\[\/attachment\]/u', "[attachment={$attachment->id}]{$attachment->filename}[/attachment]", $this->message);
		}

		// Delete removed attachments and update attachments count and message text
		foreach ($this->_attachments_del as $attachment)
		{
			if ($attachment->mesid && $attachment->mesid != $this->id)
			{
				// Attachment doesn't belong to this message => skip it.
				continue;
			}

			$exception = $attachment->tryAuthorise('delete', null, false);

			if ($exception)
			{
				$this->setError($exception->getMessage());
				continue;
			}

			if (!$attachment->delete())
			{
				$this->setError($attachment->getError());
			}
			else
			{
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
	public function load($id = null)
	{
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
	public function save()
	{
		$isNew = ! $this->_exists;

		$topic = $this->getTopic();
		$newTopic = !$topic->exists();

		if ($newTopic) {
			// Create topic, but do not cascade changes to category etc..
			if (!$topic->save(false))
			{
				$this->setError ( $topic->getError () );
				return false;
			}

			$this->_thread = $this->thread = $topic->id;
		}

		// Create message
		if (! parent::save ())
		{
			// If we created a new topic, remember to delete it too.
			if ($newTopic)
			{
				$topic->delete();
			}

			return false;
		}

		if ($isNew) {
			$this->_hold = 1;
		}

		// Update attachments and message text
		$update = $this->updateAttachments();

		// Did we change anything?
		if ($update)
		{
			if ($isNew && trim($this->message) == '')
			{
				// Oops, no attachments remain and the message becomes empty.
				// Let's delete the new message and fail on save.
				$this->delete();

				// If we created a new topic, remember to delete it too.
				if ($newTopic)
				{
					$topic->delete();
				}

				$this->setError(JText::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'));

				return false;
			}

			$table = $this->getTable ();
			$table->bind ( $this->getProperties () );
			$table->exists(true);

			if (! $table->store ())
			{
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
	public function delete()
	{
		if (!$this->exists())
		{
			return true;
		}

		if (!parent::delete())
		{
			return false;
		}

		$this->hold = 1;

		$attachments = $this->getAttachments();
		foreach ($attachments as $attachment)
		{
			if (!$attachment->delete())
			{
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

		foreach ($queries as $query)
		{
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
	public function check()
	{
		$author = KunenaUserHelper::get($this->userid);

		// Check username
		if (! $this->userid)
		{
			$this->name = trim($this->name);
			// Unregistered or anonymous users: Do not allow existing username
			$nicktaken = JUserHelper::getUserId ( $this->name );
			if (empty ( $this->name ) || $nicktaken)
			{
				$this->name = JText::_ ( 'COM_KUNENA_USERNAME_ANONYMOUS' );
			}
		}
		else
		{
			$this->name = $author->getName();
		}

		// Check email address
		$this->email = trim($this->email);

		if ($this->email)
		{
			// Email address must be valid
			if (! JMailHelper::isEmailAddress ( $this->email ))
			{
				$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_INVALID' ) );

				return false;
			}
		}
		else if (! KunenaUserHelper::getMyself()->exists() && KunenaFactory::getConfig()->askemail)
		{
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_EMPTY' ) );

			return false;
		}

		// Do not allow no posting date or dates from the future
		$now = JFactory::getDate()->toUnix();
		if (!$this->time || $this->time > $now)
		{
			$this->time = $now;
		}

		// Do not allow indentical posting times inside topic (simplifies logic)
		$topic = $this->getTopic();

		if (!$this->exists() && $topic->exists() && $this->time <= $topic->last_post_time)
		{
			$this->time = $topic->last_post_time + 1;
		}

		if ($this->modified_time > $now)
		{
			$this->modified_time = $now;
		}

		if ($this->modified_time && $this->modified_time < $this->time)
		{
			$this->modified_time = $this->time;
		}

		if ($this->hold < 0 || $this->hold > 3)
		{
			$this->setError ( JText::_ ( 'COM_KUNENA_LIB_MESSAGE_ERROR_HOLD_INVALID' ) );

			return false;
		}

		if ($this->modified_by !== null)
		{
			if (!$this->modified_by)
			{
				$this->modified_time = 0;
				$this->modified_reason = '';
			}
			elseif (!$this->modified_time)
			{
				$this->modified_time = JFactory::getDate()->toUnix();
			}
		}

		// Flood protection
		$config = KunenaFactory::getConfig();

		if ($config->floodprotection && ! $this->getCategory()->authorise('moderate') )
		{
			$this->_db->setQuery ( "SELECT MAX(time) FROM #__kunena_messages WHERE ip={$this->_db->quote($this->ip)}" );
			$lastPostTime = $this->_db->loadResult ();

			if ($this->_db->getErrorNum())
			{
				$this->setError ( $this->_db->getErrorMsg() );
				return false;
			}

			if ($lastPostTime + $config->floodprotection > JFactory::getDate()->toUnix())
			{
				$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_MESSAGE_ERROR_FLOOD', (int)$config->floodprotection ) );
				return false;
			}
		}

		if (!$this->exists() && !$this->getCategory()->authorise('moderate'))
		{
			// Ignore identical messages (posted within 5 minutes)
			$duplicatetimewindow = JFactory::getDate ()->toUnix() - 5 * 60;
			$this->_db->setQuery ( "SELECT m.id FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid
				WHERE m.userid={$this->_db->quote($this->userid)}
				AND m.ip={$this->_db->quote($this->ip)}
				AND t.message={$this->_db->quote($this->message)}
				AND m.time>={$this->_db->quote($duplicatetimewindow)}" );
			$id = $this->_db->loadResult ();

			if ($this->_db->getErrorNum())
			{
				$this->setError ( $this->_db->getErrorMsg() );
				return false;
			}

			if ($id)
			{
				$this->setError ( JText::_ ( 'COM_KUNENA_POST_DUPLICATE_IGNORED' ) );
				return false;
			}
		}

		return true;
	}

	// Internal functions

	protected function update($newTopic = false)
	{
		// If post was published and then moved, we need to update old topic
		if (!$this->_hold && $this->_thread && $this->_thread != $this->thread)
		{
			$topic = KunenaForumTopicHelper::get($this->_thread);

			if (! $topic->update($this, -1))
			{
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

		if ($postDelta < 0)
		{
			$dispatcher->trigger('onDeleteKunenaPost', array(array($this->id)));
			$activity->onAfterDelete($this);
		}
		elseif ($postDelta > 0)
		{
			$topic->markRead();

			if ($this->parent == 0) {
				$activity->onAfterPost($this);
			}
			else {
				$activity->onAfterReply($this);
			}
		}
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return KunenaExceptionAuthorise|null
	 */
	protected function authoriseRead(KunenaUser $user)
	{
		if ($this->hold && !$user->exists())
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 401);
		}

		// Check that user has the right to see the post (user can see his own unapproved posts)
		if ($this->hold > 1 || ($this->hold == 1 && $this->userid != $user->userid))
		{
			$access = KunenaAccess::getInstance();
			$hold = $access->getAllowedHold($user->userid, $this->catid, false);

			if (!in_array($this->hold, $hold))
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 403);
			}
		}

		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return KunenaExceptionAuthorise|null
	 */
	protected function authoriseNotHold(KunenaUser $user)
	{
		if ($this->hold)
		{
			// Nobody can reply to unapproved or deleted post
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return KunenaExceptionAuthorise|null
	 */
	protected function authoriseOwn(KunenaUser $user)
	{
		// Guests cannot own posts.
		if (!$user->exists())
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 401);
		}

		// Check that topic owned by the user or user is a moderator
		// TODO: check #__kunena_user_topics
		if ($this->userid != $user->userid && !$user->isModerator($this->getCategory()))
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
		}

		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return KunenaExceptionAuthorise|null
	 */
	protected function authoriseThankyou(KunenaUser $user)
	{
		// Check that message is not your own
		if(!KunenaFactory::getConfig()->showthankyou)
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_THANKYOU_DISABLED'), 403);
		}

		if (!$user->userid)
		{
			// TODO: better error message
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_THANKYOU_DISABLED'), 401);
		}

		if (!$this->userid || $this->userid == $user->userid)
		{
			// TODO: better error message
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_THANKYOU_DISABLED'), 403);
		}

		return null;
	}

	/**
	 * This function check the edit time from the author of the post and return if the user is allowed to edit his post.
	 *
	 * @param KunenaUser $user
	 *
	 * @return KunenaExceptionAuthorise|null
	 */
	protected function authoriseEditTime(KunenaUser $user)
	{
		// Do not perform rest of the checks to moderators and admins
		if ($user->isModerator($this->getCategory()))
		{
			return null;
		}

		// User is only allowed to edit post within time specified in the configuration
		$config = KunenaFactory::getConfig ();

		if (intval($config->useredit) == 0)
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
		}

		if (intval($config->useredit) == 2 && $this->getTopic()->first_post_id == $this->id && $this->getTopic()->last_post_id == $this->id)
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
		}

		if (intval($config->useredittime) != 0)
		{
			//Check whether edit is in time
			$modtime = $this->modified_time ? $this->modified_time : $this->time;

			if ($modtime + intval($config->useredittime) < JFactory::getDate()->toUnix())
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}
		}

		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return KunenaExceptionAuthorise|null
	 */
	protected function authoriseDelete(KunenaUser $user)
	{
		$config = KunenaFactory::getConfig();

		if (!$user->isModerator($this->getCategory())
				&& $config->userdeletetmessage != '2' && ($config->userdeletetmessage == '0' || $this->getTopic()->last_post_id != $this->id))
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
		}

		return null;
	}

	/**
	 * Check if user has the right to upload image attachment
	 *
	 * @param KunenaUser $user
	 * @return KunenaExceptionAuthorise|NULL
	 */
	protected function authoriseAttachmentsImage(KunenaUser $user)
	{
		if (empty(KunenaFactory::getConfig()->image_upload))
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
		}

		if (KunenaFactory::getConfig()->image_upload=='admin'  )
		{
			if (!$user->isAdmin())
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_ADMINISTRATORS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->image_upload=='registered')
		{
			if (!$user->userid )
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_REGISTERED_USERS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->image_upload=='moderator')
		{
			if (!$user->isModerator())
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_MODERATORS'), 403);
			}
		}

		return null;
	}

	/**
	 * Check if user has the right to upload file attachment
	 *
	 * @param KunenaUser $user
	 * @return KunenaExceptionAuthorise|NULL
	 */
	protected function authoriseAttachmentsFile(KunenaUser $user)
	{
		if (empty(KunenaFactory::getConfig()->file_upload) )
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
		}

		if (KunenaFactory::getConfig()->file_upload=='admin')
		{
			if (!$user->isAdmin())
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_ADMINISTRATORS'), 403);
			}
		}

		if(KunenaFactory::getConfig()->file_upload=='registered' )
		{
			if (!$user->userid )
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_REGISTERED_USERS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->file_upload=='moderator' )
		{
			if (!$user->isModerator())
			{
				return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_MODERATORS'), 403);
			}
		}

		return null;
	}

	/**
	 * @return int
	 */
	protected function delta()
	{
		if (!$this->hold && ($this->_hold || $this->thread != $this->_thread))
		{
			// Publish message or move it into new topic
			return 1;
		}
		elseif (!$this->_hold && $this->hold)
		{
			// Unpublish message
			return -1;
		}

		return 0;
	}

	/**
	 * @param JMail $mail
	 * @param int $subscription
	 * @param string $subject
	 * @param string $url
	 * @param bool $once
	 *
	 * @return string
	 */
	protected function attachEmailBody(JMail $mail, $subscription, $subject, $url, $once)
	{
		$layout = KunenaLayout::factory('Email/Subscription')->debug(false)
			->set('mail', $mail)
			->set('message', $this)
			->set('messageUrl', $url)
			->set('once', $once);

		try
		{
			$msg = trim($layout->render($subscription ? 'default' : 'moderator'));

		}
		catch (Exception $e)
		{
			// TODO: Deprecated in K4.0, remove in K5.0
			// Clean up the message for review.
			$message = KunenaHtmlParser::stripBBCode($this->message, 0, false);

			$config = KunenaFactory::getConfig();

			if ($subscription)
			{
				$msg1 = $this->get ( 'parent' ) ? JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION1' ) : JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION1_CAT' );
				$msg2 = $this->get ( 'parent' ) ? JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION2' ) : JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION2_CAT' );
			}
			else
			{
				$msg1 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD1' );
				$msg2 = JText::_ ( 'COM_KUNENA_POST_EMAIL_MOD2' );
			}

			$msg = $msg1 . " " . $config->board_title . "\n\n";
			// DO NOT REMOVE EXTRA SPACE, JMailHelper::cleanBody() removes "Subject:" from the message body
			$msg .= JText::_ ( 'COM_KUNENA_MESSAGE_SUBJECT' ) . " : " . $subject . "\n";
			$msg .= JText::_ ( 'COM_KUNENA_CATEGORY' ) . " : " . $this->getCategory()->name . "\n";
			$msg .= JText::_ ( 'COM_KUNENA_VIEW_POSTED' ) . " : " . $this->getAuthor()->getName('???', false) . "\n\n";
			$msg .= "URL : $url\n\n";

			if ($config->mailfull == 1)
			{
				$msg .= JText::_ ( 'COM_KUNENA_MESSAGE' ) . " :\n-----\n";
				$msg .= $message;
				$msg .= "\n-----\n\n";
			}

			$msg .= $msg2 . "\n";

			if ($subscription && $once)
			{
				if ($this->parent)
				{
					$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_READ' ) . "\n";
				}
				else
				{
					$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION_MORE_SUBSCRIBE' ) . "\n";
				}
			}

			$msg .= "\n";
			$msg .= JText::_ ( 'COM_KUNENA_POST_EMAIL_NOTIFICATION3' ) . "\n";
		}

		$mail->setBody($msg);
	}
}
