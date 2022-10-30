<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\IpHelper;

/**
 * Class KunenaForumMessage
 *
 * @property int    $parent
 * @property int    $thread
 * @property int    $catid
 * @property string $name
 * @property int    $userid
 * @property string $email
 * @property string $subject
 * @property int    $time
 * @property string $ip
 * @property int    $topic_emoticon
 * @property int    $locked
 * @property int    $hold
 * @property int    $ordering
 * @property int    $hits
 * @property int    $moved
 * @property int    $modified_by
 * @property string $modified_time
 * @property string $modified_reason
 * @property string $params
 * @property string $message
 * @since Kunena
 */
class KunenaForumMessage extends KunenaDatabaseObject
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $actions = array(
		'none'                   => array(),
		'read'                   => array('Read'),
		'reply'                  => array('Read', 'NotHold', 'GuestWrite'),
		'edit'                   => array('Read', 'Own', 'EditTime'),
		'move'                   => array('Read'),
		'approve'                => array('Read'),
		'delete'                 => array('Read', 'Own', 'Delete'),
		'thankyou'               => array('Read', 'Thankyou'),
		'unthankyou'             => array('Read'),
		'undelete'               => array('Read'),
		'permdelete'             => array('Read', 'Permdelete'),
		'attachment.read'        => array('Read'),
		'attachment.createimage' => array('Read', 'AttachmentsImage'),
		'attachment.createfile'  => array('Read', 'AttachmentsFile'),
		'attachment.delete'      => array(),
	);

	/**
	 * @var integer
	 * @since Kunena
	 */
	public $id = null;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $_table = 'KunenaMessages';

	/**
	 * @var JDatabaseDriver|null
	 * @since Kunena
	 */
	protected $_db = null;

	/**
	 * @var KunenaAttachment[]
	 * @since Kunena
	 */
	protected $_attachments_add = array();

	/**
	 * @var KunenaAttachment[]
	 * @since Kunena
	 */
	protected $_attachments_del = array();

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $_topic = null;

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $_hold = 1;

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $_thread = 0;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $_authcache = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $_authtcache = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $_authfcache = array();

	/**
	 * @var boolean
	 * @since Kunena 5.2
	 */
	protected $_approved = false;

	/**
	 * @param   mixed $properties properties
	 *
	 * @internal
	 * @since Kunena
	 */
	public function __construct($properties = null)
	{
		$this->_db = Factory::getDbo();
		parent::__construct($properties);
	}

	/**
	 * Returns KunenaForumMessage object.
	 *
	 * @param   int  $identifier The message to load - Can be only an integer.
	 * @param   bool $reload     reload
	 *
	 * @return KunenaForumMessage
	 * @since Kunena
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return KunenaForumMessageHelper::get($identifier, $reload);
	}

	/**
	 * Destruct
	 * @since Kunena
	 */
	public function __destruct()
	{
		unset($this->_db);
		unset($this->_topic);
	}

	/**
	 * @param   mixed $user user
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function isNew($user = null)
	{
		$user = KunenaUserHelper::get($user);

		if (!KunenaFactory::getConfig()->shownew || !$user->exists())
		{
			return false;
		}

		$session = KunenaFactory::getSession();

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

		if ($this->id == $read->message_id || $this->time < $read->time)
		{
			return false;
		}

		return true;
	}

	/**
	 * @return KunenaForumCategory
	 * @since Kunena
	 */
	public function getCategory()
	{
		return KunenaForumCategoryHelper::get($this->catid);
	}

	/**
	 * @return KunenaForumTopic
	 * @since Kunena
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
	 * @param   KunenaForumTopic $topic topic
	 *
	 * @since Kunena
	 * @return void
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
	 * Get published state in text.
	 *
	 * @return string
	 *
	 * @since  K4.0
	 */
	public function getState()
	{
		switch ($this->hold)
		{
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
	 * @param   null|KunenaForumCategory $category Fake category if needed. Used for aliases.
	 * @param   bool                     $xhtml    xhtml
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function getUrl($category = null, $xhtml = true)
	{
		return $this->getTopic()->getUrl($category, $xhtml, $this);
	}

	/**
	 * @param   null|KunenaForumCategory $category Fake category if needed. Used for aliases.
	 *
	 * @return \Joomla\CMS\Uri\Uri
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getUri($category = null)
	{
		return $this->getTopic()->getUri($category, $this);
	}

	/**
	 * Prepare the data and save to reply
	 * 
	 * @param   array      $fields              Fields to prepare teh message
	 * @param   int        $useridfromparent    Userid of the user of parent
	 * @param   null|array $safefields          safefields
	 *
	 * @return array
	 * @throws null
	 * @since Kunena
	 */
	public function newReply($fields = array(), $useridfromparent = 0, $safefields = null)
	{
		$user     = KunenaUserHelper::get();
		$topic    = $this->getTopic();
		$category = $this->getCategory();

		$message = new KunenaForumMessage;
		$message->setTopic($topic);
		$message->parent  = $this->id;
		$message->thread  = $topic->id;
		$message->catid   = $topic->category_id;
		$message->name    = $user->getName('');
		$message->userid  = $user->userid;
		$message->subject = $this->subject;
		$message->ip      = IpHelper::getIP();

		// Add IP to user.
		if (KunenaConfig::getInstance()->iptracking)
		{
			if (empty($user->ip))
			{
				$user->ip = IpHelper::getIP();
			}
		}

		if (KunenaConfig::getInstance()->allow_change_subject && $topic->first_post_userid == $message->userid || KunenaUserHelper::getMyself()->isModerator())
		{
			if (isset($fields['subject']))
			{
				$topic->subject = $fields['subject'];
			}
		}

		if ($topic->hold)
		{
			// If topic was unapproved or deleted, use the same state for the new message
			$message->hold = $topic->hold;
		}
		else
		{
			// Otherwise message is either unapproved or published depending if the category is moderated or not
			$message->hold = $category->review ? (int) !$category->isAuthorised('moderate', $user) : 0;
		}

		if ($fields === true)
		{
			$userfromparent = KunenaUserHelper::get($useridfromparent);
			$userfromparentname =$userfromparent->getName();
			if (empty($userfromparent->getName()))
			{
				$userfromparentname = 'anonymous';
			}

			$find                 = array('/\[hide\](.*?)\[\/hide\]/su', '/\[confidential\](.*?)\[\/confidential\]/su');
			$replace              = '';
			$text                 = preg_replace($find, $replace, $this->message);
			$message->message     = "[quote=\"{$userfromparentname} post={$this->id} userid={$useridfromparent}\"]" . $text . "[/quote]";
		}
		else
		{
			if (is_array($safefields))
			{
				$message->bind($safefields);
			}

			if (is_array($fields))
			{
				$message->bind($fields, array('name', 'email', 'subject', 'message'), true);
			}
		}

		return array($topic, $message);
	}

	/**
	 * Send email notifications from the message.
	 *
	 * @param   null|string $url url
	 * @param   boolean     $approved false
	 *
	 * @return boolean|null
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function sendNotification($url = null, $approved = false)
	{
		$config = KunenaFactory::getConfig();

		if (!$config->get('send_emails'))
		{
			return false;
		}

		if ($this->hold > 1)
		{
			return false;
		}
		elseif ($this->hold == 1)
		{
			$mailsubs   = 0;
			$mailmods   = $config->mailmod >= 0;
			$mailadmins = $config->mailadmin >= 0;
		}
		else
		{
			$mailsubs   = (bool) $config->allowsubscriptions;
			$mailmods   = $config->mailmod >= 1;
			$mailadmins = $config->mailadmin >= 1;
		}

		$this->_approved = $approved;

		$once = false;

		if ($mailsubs)
		{
			if (!$this->parent)
			{
				// New topic: Send email only to category subscribers
				$mailsubs = $config->category_subscriptions != 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : 0;
				$once     = $config->category_subscriptions == 'topic';
			}
			elseif ($config->category_subscriptions != 'post')
			{
				// Existing topic: Send email only to topic subscribers
				$mailsubs = $config->topic_subscriptions != 'disabled' ? KunenaAccess::TOPIC_SUBSCRIPTION : 0;
				$once     = $config->topic_subscriptions == 'first';
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
			$url = Uri::getInstance()->toString(array('scheme', 'host', 'port')) . $this->getPermaUrl();
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
				KunenaError::warning(Text::_('COM_KUNENA_EMAIL_DISABLED'));

				return false;
			}
			elseif (!\Joomla\CMS\Mail\MailHelper::isEmailAddress($config->getEmail()))
			{
				KunenaError::warning(Text::_('COM_KUNENA_EMAIL_INVALID'));

				return false;
			}

			$topic = $this->getTopic();

			// Make a list from all receivers; split the receivers into two distinct groups.
			$sentusers = array();
			$receivers = array(0 => array(), 1 => array());

			foreach ($emailToList as $emailTo)
			{
				if (!$emailTo->email || !\Joomla\CMS\Mail\MailHelper::isEmailAddress($emailTo->email))
				{
					continue;
				}

				if ($config->emailVisibleAddress != $emailTo->email)
				{
					$receivers[$emailTo->subscription][] = $emailTo->email;
					$sentusers[]                         = $emailTo->id;
				}
			}

			$mailnamesender  = !empty($config->email_sender_name) ? \Joomla\CMS\Mail\MailHelper::cleanAddress($config->email_sender_name) : \Joomla\CMS\Mail\MailHelper::cleanAddress($config->board_title);
			$mailsubject = \Joomla\CMS\Mail\MailHelper::cleanSubject($topic->subject . " (" . $this->getCategory()->name . ")");
			$subject     = $this->subject ? $this->subject : $topic->subject;

			// Create email.
			$mail = \Joomla\CMS\Factory::getMailer();
			$mail->setSubject($mailsubject);
			$mail->setSender(array($config->getEmail(), $mailnamesender));

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
				$sentusers = implode(',', $sentusers);
				$db        = Factory::getDbo();
				$query     = $db->getQuery(true)
					->update('#__kunena_user_topics')
					->set('subscribed=2')
					->where("topic_id={$this->thread}")
					->where("user_id IN ({$sentusers})")
					->where('subscribed=1');

				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (JDatabaseExceptionExecuting $e)
				{
					KunenaError::displayDatabaseError($e);
				}
			}
		}

		return true;
	}

	/**
	 *  Get permament topic URL without domain.
	 *
	 * If you want to add domain (for email etc), you can prepend the output with this:
	 * Uri::getInstance()->toString(array('scheme', 'host', 'port'))
	 *
	 * @param   null|KunenaForumCategory $category Fake category if needed. Used for aliases.
	 * @param   bool                     $xhtml    xhtml
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getPermaUrl($category = null, $xhtml = true)
	{
		$uri = $this->getPermaUri($category);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * @param   null|KunenaForumCategory $category category
	 *
	 * @return \Joomla\CMS\Uri\Uri
	 * @since Kunena
	 */
	public function getPermaUri($category = null)
	{
		$category = $category ? KunenaForumCategoryHelper::get($category) : $this->getCategory();

		if (!$this->exists() || !$category->exists())
		{
			return false;
		}

		$uri = Uri::getInstance("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$this->thread}&mesid={$this->id}");

		return $uri;
	}

	/**
	 * @param   \Joomla\CMS\Mail\Mail $mail         mail
	 * @param   int                   $subscription subscription
	 * @param   string                $subject      subject
	 * @param   string                $url          url
	 * @param   bool                  $once         once
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function attachEmailBody($mail, $subscription, $subject, $url, $once)
	{
		$layout = KunenaLayout::factory('Email/Subscription')->debug(false)
			->set('mail', $mail)
			->set('message', $this)
			->set('messageUrl', $url)
			->set('once', $once)
		->set('approved', $this->_approved);

		try
		{
			$msg = trim($layout->render($subscription ? 'default' : 'moderator'));
		}
		catch (Exception $e)
		{
		}

		$mail->setBody($msg);
	}

	/**
	 * @param   int $value value
	 *
	 * @return boolean
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function publish($value = KunenaForum::PUBLISHED)
	{
		if ($this->hold == $value)
		{
			return true;
		}

		$this->hold = (int) $value;
		$result     = $this->save();

		return $result;
	}

	/**
	 * Method to save the KunenaForumMessage object to the database.
	 *
	 * @return    boolean True on success
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function save()
	{
		$user = KunenaUserHelper::getMyself();

		if ($user->userid == 0 && $this->userid)
		{
			$user = KunenaUserHelper::get($this->userid);
		}

		if ($user->userid == 0 && !KunenaFactory::getConfig()->pubwrite)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN'), 401);
		}

		$isNew = !$this->_exists;

		$topic    = $this->getTopic();
		$newTopic = !$topic->exists();

		if ($newTopic)
		{
			// Create topic, but do not cascade changes to category etc..
			if (!$topic->save(false))
			{
				$this->setError($topic->getError());

				return false;
			}

			$this->_thread = $this->thread = $topic->id;
		}

		// Create message
		if (!parent::save())
		{
			// If we created a new topic, remember to delete it too.
			if ($newTopic)
			{
				$topic->delete();
			}

			return false;
		}

		if ($isNew)
		{
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

				$this->setError(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'));

				return false;
			}

			$table = $this->getTable();
			$table->bind($this->getProperties());
			$table->exists(true);

			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Cascade changes to other tables
		$this->update($newTopic);

		return true;
	}

	/**
	 * @return boolean
	 * @throws null
	 * @since Kunena
	 */
	protected function updateAttachments()
	{
		// Save new attachments and update message text
		$message = $this->message;

		foreach ($this->_attachments_add as $tmpid => $attachment)
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
			$this->message = preg_replace('/\[attachment\:' . $tmpid . '\].*?\[\/attachment\]/u', "[attachment={$attachment->id}]{$attachment->filename}[/attachment]", $this->message);
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

			$this->message = preg_replace('/\[attachment\=' . $attachment->id . '\].*?\[\/attachment\]/u', '', $this->message);
			$this->message = preg_replace('/\[attachment\]' . $attachment->filename . '\[\/attachment\]/u', '', $this->message);
		}

		// Remove missing temporary attachments from the message text
		$this->message = trim(preg_replace('/\[attachment\:\d+\].*?\[\/attachment\]/u', '', $this->message));

		// Return true if we changed the message contents
		return $this->message != $message;
	}

	/**
	 * Method to delete the KunenaForumMessage object from the database.
	 *
	 * @return boolean    True on success
	 * @throws Exception
	 * @since Kunena
	 * @throws null
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
			$file = Uri::root() . $attachment->filename;
			KunenaFile::delete($file);

			if (!$attachment->delete())
			{
				$this->setError($attachment->getError());
			}
		}

		$db = Factory::getDBO();

		// Delete thank yous
		$queries[] = "DELETE FROM #__kunena_thankyou WHERE postid={$db->quote($this->id)}";

		// Delete message
		$queries[] = "DELETE FROM #__kunena_messages_text WHERE mesid={$db->quote($this->id)}";

		// Cascade changes into other tables
		$this->update();

		foreach ($queries as $query)
		{
			$db->setQuery($query);
			$db->execute();

			try
			{
				$db->execute();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		KunenaForumMessageThankyouHelper::recount();

		return true;
	}

	/**
	 * @param   bool|array $ids    ids
	 * @param   string     $action action
	 *
	 * @return KunenaAttachment[]
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function getAttachments($ids = false, $action = 'read')
	{
		if ($ids === false)
		{
			$attachments = KunenaAttachmentHelper::getByMessage($this->id, $action);
		}
		else
		{
			$attachments = KunenaAttachmentHelper::getById($ids, $action);

			foreach ($attachments as $id => $attachment)
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
	 * @param   bool $newTopic new topic
	 *
	 * @throws Exception
	 * @since Kunena
	 * @return void
	 */
	protected function update($newTopic = false)
	{
		// If post was published and then moved, we need to update old topic
		if (!$this->_hold && $this->_thread && $this->_thread != $this->thread)
		{
			$topic = KunenaForumTopicHelper::get($this->_thread);

			if (!$topic->update($this, -1))
			{
				$this->setError($topic->getError());
			}
		}

		$postDelta = $this->delta();
		$topic     = $this->getTopic();

		// New topic
		if ($newTopic)
		{
			$topic->hold = 0;
		}

		// Update topic
		if (!$this->hold && $topic->hold && $topic->exists())
		{
			// We published message -> publish and recount topic
			$topic->hold = 0;
			$topic->recount();
		}
		elseif (!$topic->update($this, $postDelta))
		{
			$this->setError($topic->getError());
		}

		// Activity integration

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('finder');
		$activity = KunenaFactory::getActivityIntegration();

		if ($postDelta < 0)
		{
			Factory::getApplication()->triggerEvent('onDeleteKunenaPost', array(array($this->id)));
			$activity->onAfterDelete($this);
		}
		elseif ($postDelta > 0)
		{
			$topic->markRead();

			if ($this->parent == 0)
			{
				$activity->onAfterPost($this);
			}
			else
			{
				$activity->onAfterReply($this);
			}
		}
	}

	/**
	 * @return integer
	 * @since Kunena
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
	 * @param   mixed $user user
	 *
	 * @return KunenaForumTopicUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getUserTopic($user = null)
	{
		return $this->getTopic()->getUserTopic($user);
	}

	/**
	 * @return KunenaForumMessageThankyou
	 * @throws Exception
	 * @since Kunena
	 */
	public function getThankyou()
	{
		return KunenaForumMessageThankyouHelper::get($this->id);
	}

	/**
	 * @return KunenaForumMessage
	 * @since Kunena
	 */
	public function getParent()
	{
		return KunenaForumMessageHelper::get($this->parent);
	}

	/**
	 * @return KunenaUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getAuthor()
	{
		return KunenaUserHelper::getAuthor($this->userid, $this->name);
	}

	/**
	 * @return KunenaDate
	 * @since Kunena
	 */
	public function getTime()
	{
		return new KunenaDate($this->time);
	}

	/**
	 * @return KunenaUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getModifier()
	{
		return KunenaUserHelper::get($this->modified_by);
	}

	/**
	 * @return KunenaDate
	 * @since Kunena
	 */
	public function getModifiedTime()
	{
		return new KunenaDate($this->modified_time);
	}

	/**
	 * Display required field from message table
	 *
	 * @param   string  $field   field
	 * @param   boolean $html    html
	 * @param   string  $context context
	 *
	 * @return integer|string
	 * @throws Exception
	 * @since Kunena
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
				return $html ? KunenaHtmlParser::parseBBCode($this->message, $this, 0, $context) : KunenaHtmlParser::stripBBCode($this->message, 0, $html);
		}

		return '';
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param   string     $action action
	 * @param   KunenaUser $user   user
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @throws null
	 * @since  K4.0
	 */
	public function isAuthorised($action = 'read', KunenaUser $user = null)
	{
		if (KunenaFactory::getConfig()->read_only)
		{
			// Special case to ignore authorisation.
			if ($action != 'read')
			{
				return false;
			}
		}

		return !$this->tryAuthorise($action, $user, false);
	}

	/**
	 * Throws an exception if user isn't authorised to do the action.
	 *
	 * @param   string     $action action
	 * @param   KunenaUser $user   user
	 * @param   bool       $throw  trow
	 *
	 * @return mixed
	 * @throws null
	 * @since  K4.0
	 */
	public function tryAuthorise($action = 'read', KunenaUser $user = null, $throw = true)
	{
		// Special case to ignore authorisation.
		if ($action == 'none')
		{
			return false;
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
				throw new InvalidArgumentException(Text::sprintf('COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action), 500);
			}

			// Load category authorisation.
			if (!isset($this->_authtcache[$user->userid][$action]))
			{
				$this->_authtcache[$user->userid][$action] = $this->getTopic()->tryAuthorise('post.' . $action, $user, false);
			}

			$this->_authcache[$user->userid][$action] = $this->_authtcache[$user->userid][$action];

			if (empty($this->_authcache[$user->userid][$action]))
			{
				foreach (self::$actions[$action] as $function)
				{
					if (!isset($this->_authfcache[$user->userid][$function]))
					{
						$authFunction                                = 'authorise' . $function;
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
	 * @param   array $fields fields
	 * @param   mixed $user   user
	 *
	 * @throws null
	 * @since Kunena
	 * @return void
	 */
	public function edit($fields = array(), $user = null)
	{
		$user = KunenaUserHelper::get($user);

		$this->bind($fields, array('name', 'email', 'subject', 'message', 'modified_reason'), true);

		// Update rest of the information
		$category            = $this->getCategory();
		$this->hold          = $category->review && !$category->isAuthorised('moderate', $user) ? 1 : $this->hold;
		$this->modified_by   = $user->userid;
		$this->modified_time = Factory::getDate()->toUnix();
	}

	/**
	 * @param   mixed $user user
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	public function makeAnonymous($user = null)
	{
		$user = KunenaUserHelper::get($user);

		if ($user->userid == $this->userid && $this->modified_by == $this->userid)
		{
			// I am the author and previous modification was made by me => delete modification information to hide my personality
			$this->modified_by     = 0;
			$this->modified_time   = 0;
			$this->modified_reason = '';
		}
		elseif ($user->userid == $this->userid)
		{
			// I am the author, but somebody else has modified the message => leave modification information intact
			$this->modified_by     = null;
			$this->modified_time   = null;
			$this->modified_reason = null;
		}

		// Remove userid, email and ip address
		$this->userid = 0;
		$this->ip     = '';
		$this->email  = '';
	}

	/**
	 * @param   int    $tmpid   tmpid
	 * @param   string $postvar postvar
	 * @param   null   $catid   catid
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function uploadAttachment($tmpid, $postvar, $catid = null)
	{
		$attachment                     = new KunenaAttachment;
		$attachment->userid             = $this->userid;
		$success                        = $attachment->upload($postvar, $catid);
		$this->_attachments_add[$tmpid] = $attachment;

		return $success;
	}

	/**
	 * Add listed attachments to the message.
	 *
	 * If attachment is already pointing to the message, this function has no effect.
	 * Currently only orphan attachments can be added.
	 *
	 * @param   array $ids ids
	 *
	 * @since  K4.0
	 * @throws Exception
	 * @throws null
	 * @return void
	 */
	public function addAttachments(array $ids)
	{
		$this->_attachments_add += $this->getAttachments($ids, 'none');
	}

	/**
	 * Remove listed attachments from the message.
	 *
	 * @param   bool|int|array $ids ids
	 *
	 * @deprecated K4.0
	 * @since      Kunena
	 * @throws Exception
	 * @throws null
	 * @return void
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
	 * Remove listed attachments from the message.
	 *
	 * @param   array $ids ids
	 *
	 * @since  K4.0
	 * @throws Exception
	 * @throws null
	 * @return void
	 */
	public function removeAttachments(array $ids)
	{
		$this->_attachments_del += $this->getAttachments($ids, 'none');
	}

	/**
	 * Get the number of attachments into a message
	 *
	 * @return array|StdClass
	 * @throws Exception
	 * @since Kunena
	 */
	public function getNbAttachments()
	{
		$attachments = KunenaAttachmentHelper::getNumberAttachments($this->id);

		$attachs        = new StdClass;
		$attachs->image = 0;
		$attachs->file  = 0;
		$attachs->total = 0;

		foreach ($attachments as $attach)
		{
			if ($attach->isImage())
			{
				$attachs->image = $attachs->image + 1;
			}
			else
			{
				$attachs->file = $attachs->file + 1;
			}

			$attachs->total = $attachs->total + 1;
		}

		return $attachs;
	}

	/**
	 * Method to load a KunenaForumMessage object by id.
	 *
	 * @param   mixed $id The message id to be loaded
	 *
	 * @return boolean    True on success
	 * @since Kunena
	 */
	public function load($id = null)
	{
		$exists        = parent::load($id);
		$this->_hold   = $exists ? $this->hold : 1;
		$this->_thread = $this->thread;

		return $exists;
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function check()
	{
		$author = KunenaUserHelper::get($this->userid);

		// Check username
		if (!$this->userid)
		{
			$this->name = trim($this->name);

			// Unregistered or anonymous users: Do not allow existing username
			$nicktaken = \Joomla\CMS\User\UserHelper::getUserId($this->name);

			if (empty($this->name) || $nicktaken)
			{
				$this->name = Text::_('COM_KUNENA_USERNAME_ANONYMOUS');
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
			if (!\Joomla\CMS\Mail\MailHelper::isEmailAddress($this->email))
			{
				$this->setError(Text::sprintf('COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_INVALID'));

				return false;
			}
		}
		elseif (!KunenaUserHelper::getMyself()->exists() && KunenaFactory::getConfig()->askemail)
		{
			$this->setError(Text::_('COM_KUNENA_LIB_MESSAGE_ERROR_EMAIL_EMPTY'));

			return false;
		}

		// Do not allow no posting date or dates from the future
		$now = Factory::getDate()->toUnix();

		if (!$this->time || $this->time > $now)
		{
			$this->time = $now;
		}

		// Do not allow identical posting times inside topic (simplifies logic)
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
			$this->setError(Text::_('COM_KUNENA_LIB_MESSAGE_ERROR_HOLD_INVALID'));

			return false;
		}

		if ($this->modified_by !== null)
		{
			if (!$this->modified_by)
			{
				$this->modified_time   = 0;
				$this->modified_reason = '';
			}
			elseif (!$this->modified_time)
			{
				$this->modified_time = Factory::getDate()->toUnix();
			}
		}

		// Flood protection
		$config = KunenaFactory::getConfig();

		if ($config->floodprotection && !$this->getCategory()->isAuthorised('moderate') && !$this->exists())
		{
			$this->_db->setQuery("SELECT MAX(time) FROM #__kunena_messages WHERE ip={$this->_db->quote($this->ip)}");

			try
			{
				$lastPostTime = $this->_db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}

			if ($lastPostTime + $config->floodprotection > Factory::getDate()->toUnix())
			{
				$this->setError(Text::sprintf('COM_KUNENA_LIB_MESSAGE_ERROR_FLOOD', (int) $config->floodprotection));

				return false;
			}
		}

		if (!$this->exists() && !$this->getCategory()->isAuthorised('moderate'))
		{
			// Ignore identical messages (posted within 5 minutes)
			$duplicatetimewindow = Factory::getDate()->toUnix() - 5 * 60;
			$this->_db->setQuery("SELECT m.id FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid
				WHERE m.userid={$this->_db->quote($this->userid)}
				AND m.ip={$this->_db->quote($this->ip)}
				AND t.message={$this->_db->quote($this->message)}
				AND m.time>={$this->_db->quote($duplicatetimewindow)}"
			);

			try
			{
				$id = $this->_db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}

			if ($id)
			{
				$this->setError(Text::_('COM_KUNENA_POST_DUPLICATE_IGNORED'));

				return false;
			}
		}

		return true;
	}

	/**
	 * Get the substring
	 *
	 * @param   string  $string string
	 * @param   integer $start  start
	 * @param   integer $length length
	 *
	 * @return string
	 * @since K5.0.2
	 */
	public function getsubstr($string, $start, $length)
	{
		$mbString = extension_loaded('mbstring');

		if ($mbString)
		{
			$title = mb_substr($string, $start, $length);
		}
		else
		{
			$title2 = substr($string, $start, $length);
			$title  = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
				'|[\x00-\x7F][\x80-\xBF]+' .
				'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
				'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
				'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
				'', $title2
			);
		}

		return $title;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseRead(KunenaUser $user)
	{
		if ($this->hold && !$user->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 401);
		}

		// Check that user has the right to see the post (user can see his own unapproved posts)
		if ($this->hold > 1 || ($this->hold == 1 && $this->userid != $user->userid))
		{
			$access = KunenaAccess::getInstance();
			$hold   = $access->getAllowedHold($user->userid, $this->catid, false);

			if (!in_array($this->hold, $hold))
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @since Kunena
	 */
	protected function authoriseNotHold(KunenaUser $user)
	{
		if ($this->hold)
		{
			// Nobody can reply to unapproved or deleted post
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		return;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseOwn(KunenaUser $user)
	{
		// Guests cannot own posts.
		if (!$user->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 401);
		}

		// Check that topic owned by the user or user is a moderator
		// TODO: check #__kunena_user_topics
		if ($this->userid != $user->userid && !$user->isModerator($this->getCategory()))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
		}

		return;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseThankyou(KunenaUser $user)
	{
		// Check that message is not your own
		if (!KunenaFactory::getConfig()->showthankyou)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 403);
		}

		if (!$user->userid)
		{
			// TODO: better error message
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 401);
		}

		if (!$this->userid || $this->userid == $user->userid)
		{
			// TODO: better error message
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 403);
		}

		return;
	}

	/**
	 * This function check the edit time from the author of the post and return if the user is allowed to edit his post.
	 *
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseEditTime(KunenaUser $user)
	{
		// Do not perform rest of the checks to moderators and admins
		if ($user->isModerator($this->getCategory()))
		{
			return false;
		}

		// User is only allowed to edit post within time specified in the configuration
		$config = KunenaFactory::getConfig();

		if (intval($config->useredit) != 1)
		{
			// Edit never allowed
			if (intval($config->useredit) == 0)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}

			// Edit allowed if replies
			if (intval($config->useredit) == 2 && $this->getTopic()->getReplies())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_IF_REPLIES'), 403);
			}

			// Edit allowed for the first message of the topic
			if (intval($config->useredit) == 4 && $this->id != $this->getTopic()->first_post_id)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_ONLY_FIRST_MESSAGE_OF_TOPIC'), 403);
			}

			// Edit allowed for the last message of the topic
			if (intval($config->useredit) == 3 && $this->id != $this->getTopic()->last_post_id)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_ONLY_LAST_MESSAGE_OF_TOPIC'), 403);
			}
		}

		if (intval($config->useredittime) != 0)
		{
			// Check whether edit is in time
			$modtime = $this->modified_time ? $this->modified_time : $this->time;

			if ($modtime + intval($config->useredittime) < Factory::getDate()->toUnix() && intval($config->useredittimegrace) == 0)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}
			elseif (intval($config->useredittimegrace) != 0 && $modtime + intval($config->useredittime) + intval($config->useredittimegrace) < Factory::getDate()->toUnix())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseDelete(KunenaUser $user)
	{
		$config = KunenaFactory::getConfig();

		if (!$user->isModerator($this->getCategory()) && $config->userdeletetmessage != '2')
		{
			// Never
			if ($config->userdeletetmessage == '0')
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
			}

			// When no replies
			if ($config->userdeletetmessage == '1' && ($this->getTopic()->first_post_id != $this->id || $this->getTopic()->getReplies()))
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
			}

			// All except the first message of the topic
			if ($config->userdeletetmessage == '3' && $this->id == $this->getTopic()->first_post_id)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_ONLY_FIRST_MESSAGE'), 403);
			}

			// Only the last message
			if ($config->userdeletetmessage == '4' && $this->id != $this->getTopic()->last_post_id)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_ONLY_LAST_MESSAGE'), 403);
			}
		}
	}

	/**
	 * Check if user has the right to perm delete the message
	 *
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|NULL
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authorisePermdelete(KunenaUser $user)
	{
		$config = KunenaFactory::getConfig();

		if ($user->isAdmin() || $user->isModerator())
		{
			return null;
		}

		if ($user->isModerator($this->getTopic()->getCategory()) && !$config->moderator_permdelete || !$user->isModerator($this->getTopic()->getCategory()))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
		}

		return null;
	}

	/**
	 * Check if user has the right to upload image attachment
	 *
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|NULL
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseAttachmentsImage(KunenaUser $user)
	{
		if (empty(KunenaFactory::getConfig()->image_upload))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
		}

		if (KunenaFactory::getConfig()->image_upload == 'admin')
		{
			if (!$user->isAdmin())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_ADMINISTRATORS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->image_upload == 'registered')
		{
			if (!$user->userid)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_REGISTERED_USERS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->image_upload == 'moderator')
		{
			$category = $this->getCategory();

			if (!$user->isModerator($category))
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_MODERATORS'), 403);
			}
		}

		return;
	}

	/**
	 * Check if user has the right to upload file attachment
	 *
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|NULL
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseAttachmentsFile(KunenaUser $user)
	{
		if (empty(KunenaFactory::getConfig()->file_upload))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
		}

		if (KunenaFactory::getConfig()->file_upload == 'admin')
		{
			if (!$user->isAdmin())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_ADMINISTRATORS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->file_upload == 'registered')
		{
			if (!$user->userid)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_REGISTERED_USERS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->file_upload == 'moderator')
		{
			$category = $this->getCategory();

			if (!$user->isModerator($category))
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_MODERATORS'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseGuestWrite(KunenaUser $user)
	{
		// Check if user is guest and they can create or reply topics
		if ($user->userid == 0 && !KunenaFactory::getConfig()->pubwrite)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN'), 401);
		}

		return;
	}
}
