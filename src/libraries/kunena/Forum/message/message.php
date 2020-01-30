<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message;

defined('_JEXEC') or die();

use Exception;
use InvalidArgumentException;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Mail\MailTemplate;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Attachment\Attachment;
use Kunena\Forum\Libraries\Attachment\AttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Exception\Authorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\Category;
use Kunena\Forum\Libraries\Forum\Category\CategoryHelper;
use Kunena\Forum\Libraries\Forum\Category\User\CategoryUserHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\MessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\Topic;
use Kunena\Forum\Libraries\Forum\Topic\TopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\Read\TopicUserReadHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\TopicUserHelper;
use Kunena\Forum\Libraries\Html\Parser;
use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use StdClass;
use function defined;

/**
 * Class \Kunena\Forum\Libraries\Forum\Message\Message
 *
 * @since   Kunena 6.0
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
 *
 */
class Message extends KunenaDatabaseObject
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $actions = [
		'none'                   => [],
		'read'                   => ['Read'],
		'reply'                  => ['Read', 'NotHold', 'GuestWrite'],
		'edit'                   => ['Read', 'Own', 'EditTime'],
		'move'                   => ['Read'],
		'approve'                => ['Read'],
		'delete'                 => ['Read', 'Own', 'Delete'],
		'thankyou'               => ['Read', 'Thankyou'],
		'unthankyou'             => ['Read'],
		'undelete'               => ['Read'],
		'permdelete'             => ['Read', 'Permdelete'],
		'attachment.read'        => ['Read'],
		'attachment.createimage' => ['Read', 'AttachmentsImage'],
		'attachment.createfile'  => ['Read', 'AttachmentsFile'],
		'attachment.delete'      => [],
	];

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $id = null;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_table = 'KunenaMessages';

	/**
	 * @var     DatabaseDriver|null
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * @var     Attachment[]
	 * @since   Kunena 6.0
	 */
	protected $_attachments_add = [];

	/**
	 * @var     Attachment[]
	 * @since   Kunena 6.0
	 */
	protected $_attachments_del = [];

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_topic = null;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $_hold = 1;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $_thread = 0;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_authcache = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_authtcache = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_authfcache = [];

	/**
	 * @internal
	 *
	 * @param   mixed  $properties  properties
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct($properties = null)
	{
		$this->_db = Factory::getDbo();
		parent::__construct($properties);
	}

	/**
	 * Returns \Kunena\Forum\Libraries\Forum\Message\Message object.
	 *
	 * @param   int   $identifier  The message to load - Can be only an integer.
	 * @param   bool  $reload      reload
	 *
	 * @return  Message
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getInstance($identifier = null, $reload = false)
	{
		return MessageHelper::get($identifier, $reload);
	}

	/**
	 * Destruct
	 *
	 * @since   Kunena 6.0
	 */
	public function __destruct()
	{
		unset($this->_db);
		unset($this->_topic);
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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

		$allreadtime = CategoryUserHelper::get($this->getCategory(), $user)->allreadtime;

		if ($allreadtime && $this->time < $allreadtime)
		{
			return false;
		}

		$read = TopicUserReadHelper::get($this->getTopic(), $user);

		if ($this->id == $read->message_id || $this->time < $read->time)
		{
			return false;
		}

		return true;
	}

	/**
	 * @return  Category
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getCategory()
	{
		return CategoryHelper::get($this->catid);
	}

	/**
	 * @return  Topic
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getTopic()
	{
		if (!$this->_topic)
		{
			$this->_topic = TopicHelper::get($this->thread);
		}

		return $this->_topic;
	}

	/**
	 * @param   Topic  $topic  topic
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function setTopic(Topic $topic)
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
	 * @return  string
	 *
	 * @since   Kunena 4.0
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
	 * @param   null|Category  $category  Fake category if needed. Used for aliases.
	 * @param   bool           $xhtml     xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getUrl($category = null, $xhtml = true)
	{
		return $this->getTopic()->getUrl($category, $xhtml, $this);
	}

	/**
	 * @param   null|Category  $category  Fake category if needed. Used for aliases.
	 *
	 * @return  Uri
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getUri($category = null)
	{
		return $this->getTopic()->getUri($category, $this);
	}

	/**
	 * @param   array       $fields      fields
	 * @param   mixed       $user        user
	 * @param   null|array  $safefields  safefields
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	public function newReply($fields = [], $user = null, $safefields = null)
	{
		$user     = KunenaUserHelper::get($user);
		$topic    = $this->getTopic();
		$category = $this->getCategory();

		$message = new Message;
		$message->setTopic($topic);
		$message->parent  = $this->id;
		$message->thread  = $topic->id;
		$message->catid   = $topic->category_id;
		$message->name    = $user->getName('');
		$message->userid  = $user->userid;
		$message->subject = $this->subject;
		$message->ip      = KunenaUserHelper::getUserIp();

		// Add IP to user.
		if (KunenaConfig::getInstance()->iptracking)
		{
			if (empty($user->ip))
			{
				$user->ip = KunenaUserHelper::getUserIp();
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

		if ($fields['quote'] === true)
		{
			$user             = KunenaFactory::getUser($this->userid);
			$find             = ['/\[hide\](.*?)\[\/hide\]/su', '/\[confidential\](.*?)\[\/confidential\]/su', '/\[PRIVATE=(.*?)\]/su'];
			$replace          = '';
			$text             = preg_replace($find, $replace, $this->message);
			$message->message = "[quote=\"{$user->getName($this->name)}\" post={$this->id}]" . $text . "[/quote]";
		}
		else
		{
			if (is_array($safefields))
			{
				$message->bind($safefields);
			}

			if (is_array($fields))
			{
				$message->bind($fields, ['name', 'email', 'subject', 'message'], true);
			}
		}

		return [$topic, $message];
	}

	/**
	 * Send email notifications from the message.
	 *
	 * @param   null|string  $url  url
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function sendNotification($url = null)
	{
		if ($this->hold > 1)
		{
			return false;
		}

		$config = KunenaFactory::getConfig();

		if (!$config->get('send_emails'))
		{
			return false;
		}

		$this->urlNotification = $url;

		// Factory::getApplication()->RegisterEvent( 'onBeforeRespond', array($this, 'notificationCloseConnection') );
		Factory::getApplication()->RegisterEvent('onAfterRespond', [$this, 'notificationPost']);
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function notificationPost()
	{
		// Restore app input context
		Factory::getApplication()->input->set('message', $this);
		$config = KunenaFactory::getConfig();

		$url = $this->urlNotification;

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

		$once = false;

		if ($mailsubs)
		{
			if (!$this->parent)
			{
				// New topic: Send email only to category subscribers
				$mailsubs = $config->category_subscriptions != 'disabled' ? Access::CATEGORY_SUBSCRIPTION : 0;
				$once     = $config->category_subscriptions == 'topic';
			}
			elseif ($config->category_subscriptions != 'post')
			{
				// Existing topic: Send email only to topic subscribers
				$mailsubs = $config->topic_subscriptions != 'disabled' ? Access::TOPIC_SUBSCRIPTION : 0;
				$once     = $config->topic_subscriptions == 'first';
			}
			else
			{
				// Existing topic: Send email to both category and topic subscribers
				$mailsubs = $config->topic_subscriptions == 'disabled'
					? Access::CATEGORY_SUBSCRIPTION
					: Access::CATEGORY_SUBSCRIPTION | Access::TOPIC_SUBSCRIPTION;

				// FIXME: category subscription can override topic
				$once = $config->topic_subscriptions == 'first';
			}
		}

		if (!$url)
		{
			$url = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $this->getPermaUrl();
		}

		// Get all subscribers, moderators and admins who should get the email.
		$emailToList = Access::getInstance()->getSubscribers(
			$this->catid,
			$this->thread,
			$mailsubs,
			$mailmods,
			$mailadmins,
			$this->userid
		);

		if (empty($emailToList))
		{
			return true;
		}
		else
		{
			if (!$config->getEmail())
			{
				KunenaError::warning(Text::_('COM_KUNENA_EMAIL_DISABLED'));

				return false;
			}
			elseif (!MailHelper::isEmailAddress($config->getEmail()))
			{
				KunenaError::warning(Text::_('COM_KUNENA_EMAIL_INVALID'));

				return false;
			}

			$topic = $this->getTopic();

			// Make a list from all receivers; split the receivers into two distinct groups.
			$sentusers = [];
			$receivers = [0 => [], 1 => []];

			foreach ($emailToList as $emailTo)
			{
				if (!$emailTo->email || !MailHelper::isEmailAddress($emailTo->email))
				{
					continue;
				}

				$receivers[$emailTo->subscription][] = $emailTo->email;
				$sentusers[]                         = $emailTo->id;
			}

			$mailsender  = MailHelper::cleanAddress($config->board_title);
			$mailsubject = MailHelper::cleanSubject($topic->subject . " (" . $this->getCategory()->name . ")");
			$subject     = $this->subject ? $this->subject : $topic->subject;

			// Create email.
			$user = Factory::getUser();
			$mail = Factory::getMailer();
			$mail->setSubject($mailsubject);
			$mail->setSender([$config->getEmail(), $mailsender]);
			$app = Factory::getApplication();

			// Here is after respond sends. so close connection to leave browser, and
			// continue to work
			static::notificationCloseConnection();
			$app->getSession()->close();
			flush();

			$ok         = true;
			$start_time = microtime(true);

			// Send email to all subscribers.
			if (!empty($receivers[1]))
			{
				$mailer = new MailTemplate('com_kunena.reply', $user->getParam('language', $app->get('language')), $mail);
				$mailer->addTemplateData(
					[
						'mail'       => $mail,
						'subject'    => $subject,
						'message'    => $this,
						'messageUrl' => $url,
						'once'       => $once
					]
				);

				$ok = KunenaEmail::send($mailer, $receivers[1]);
			}

			// Send email to all moderators.
			if (!empty($receivers[0]))
			{
				$mailer = new MailTemplate('com_kunena.replymoderator', $user->getParam('language', $app->get('language')), $mail);
				$mailer->addTemplateData(
					[
						'mail'       => $mail,
						'subject'    => $subject,
						'message'    => $this,
						'messageUrl' => $url,
						'once'       => $once
					]
				);

				if (!KunenaEmail::send($mailer, $receivers[0]))
				{
					$ok = false;
				}
			}

			$end_time = microtime(true);

			$time_secs   = ($end_time - $start_time);
			$mid         = $this->thread;
			$recv_amount = count($receivers[1]) + count($receivers[0]);

			Log::add("$recv_amount subscriptions for msg $mid sent for {$time_secs} [ms]", Log::DEBUG, 'kunena');
			\Kunena\Forum\Libraries\Log\Log::log(($ok) ? \Kunena\Forum\Libraries\Log\Log::TYPE_REPORT : \Kunena\Forum\Libraries\Log\Log::TYPE_ERROR,
				\Kunena\Forum\Libraries\Log\Log::LOG_TOPIC_NOTIFY,
				"$recv_amount subscriptions sent for $time_secs sec",
				$this->getCategory(),
				$this->getTopic(),
				KunenaFactory::getUser($this->userid)
			);

			// Update subscriptions.
			if ($once && $sentusers)
			{
				$sentusers = implode(',', $sentusers);
				$db        = Factory::getDbo();
				$query     = $db->getQuery(true)
					->update($db->quoteName('#__kunena_user_topics'))
					->set($db->quoteName('subscribed') . ' = 2')
					->where($db->quoteName('topic_id') . ' = ' . $db->quote($this->thread))
					->where($db->quoteName('user_id') . ' IN (' . $sentusers . ')')
					->where($db->quoteName('subscribed') . ' = 1');
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (ExecutionFailureException $e)
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
	 * @param   null|Category  $category  Fake category if needed. Used for aliases.
	 * @param   bool           $xhtml     xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getPermaUrl($category = null, $xhtml = true)
	{
		$uri = $this->getPermaUri($category);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * @param   null|Category  $category  category
	 *
	 * @return  Uri|boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getPermaUri($category = null)
	{
		$category = $category ? CategoryHelper::get($category) : $this->getCategory();

		if (!$this->exists() || !$category->exists())
		{
			return false;
		}

		$uri = Uri::getInstance("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$this->thread}&mesid={$this->id}");

		return $uri;
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function notificationCloseConnection()
	{
		$app = Factory::getApplication();
		$app->setHeader('Connection', 'close');
	}

	/**
	 *
	 * @param   int  $value  value
	 *
	 * @return  boolean
	 *
	 * @since   Kunena
	 *
	 * @throws  null
	 * @throws  Exception
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
	 * Method to save the \Kunena\Forum\Libraries\Forum\Message\Message object to the database.
	 *
	 * @return  boolean|Authorise
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
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
			return new Authorise(Text::_('COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN'), 401);
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
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	protected function updateAttachments()
	{
		// Save new attachments and update message text
		$message = $this->message;
		$app     = Factory::getApplication();

		foreach ($this->_attachments_add as $tmpid => $attachment)
		{
			if ($attachment->exists() && $attachment->mesid)
			{
				try
				{
					$attachment->save();
				}
				catch (Exception $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
			}

			$attachment->mesid = $this->id;

			if ($attachment->IsImage())
			{
				try
				{
					$attachment->tryAuthorise('createimage', null, false);
				}
				catch (Exception $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
			}
			else
			{
				try
				{
					$attachment->tryAuthorise('createfile', null, false);
				}
				catch (Exception $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
			}

			try
			{
				$attachment->save();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
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
	 * Method to delete the \Kunena\Forum\Libraries\Forum\Message\Message object from the database.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
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
			File::delete($file);

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
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		MessageThankyouHelper::recount();

		return true;
	}

	/**
	 * @param   bool|array  $ids     ids
	 * @param   string      $action  action
	 *
	 * @return  Attachment[]
	 *
	 * @since   Kunena
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function getAttachments($ids = false, $action = 'read')
	{
		if ($ids === false)
		{
			$attachments = AttachmentHelper::getByMessage($this->id, $action);
		}
		else
		{
			$attachments = AttachmentHelper::getById($ids, $action);

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
	 * @param   bool  $newTopic  new topic
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function update($newTopic = false)
	{
		// If post was published and then moved, we need to update old topic
		if (!$this->_hold && $this->_thread && $this->_thread != $this->thread)
		{
			$topic = TopicHelper::get($this->_thread);

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
		// FIXME : load the plugin finder produce a fatal error
		// Joomla\CMS\Plugin\PluginHelper::importPlugin('finder');
		$activity = KunenaFactory::getActivityIntegration();

		if ($postDelta < 0)
		{
			Factory::getApplication()->triggerEvent('onDeleteKunenaPost', [[$this->id]]);
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
	 * @return  integer
	 *
	 * @since   Kunena 6.0
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
	 * @param   mixed  $user  user
	 *
	 * @return TopicUserHelper
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function getUserTopic($user = null)
	{
		return $this->getTopic()->getUserTopic($user);
	}

	/**
	 * @return  MessageThankyouHelper
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getThankyou()
	{
		return MessageThankyouHelper::get($this->id);
	}

	/**
	 * @return  Message
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getParent()
	{
		return MessageHelper::get($this->parent);
	}

	/**
	 * @return  KunenaUser
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getAuthor()
	{
		return KunenaUserHelper::getAuthor($this->userid, $this->name);
	}

	/**
	 * @return  KunenaDate
	 *
	 * @since   Kunena 6.0
	 */
	public function getTime()
	{
		return new KunenaDate($this->time);
	}

	/**
	 * @return  KunenaUser
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getModifier()
	{
		return KunenaUserHelper::get($this->modified_by);
	}

	/**
	 * @return  KunenaDate
	 *
	 * @since   Kunena 6.0
	 */
	public function getModifiedTime()
	{
		return new KunenaDate($this->modified_time);
	}

	/**
	 * Display required field from message table
	 *
	 * @param   string   $field    field
	 * @param   boolean  $html     html
	 * @param   string   $context  context
	 *
	 * @return  integer|string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayField($field, $html = true, $context = '')
	{
		switch ($field)
		{
			case 'id':
				return intval($this->id);
			case 'subject':
				return Parser::parseText($this->subject);
			case 'message':
				return $html ? Parser::parseBBCode($this->message, $this, 0, $context) : Parser::stripBBCode($this->message, 0, $html);
		}

		return '';
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param   string      $action  action
	 * @param   KunenaUser  $user    user
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  null
	 * @throws  Exception
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
	 * @param   string      $action  action
	 * @param   KunenaUser  $user    user
	 * @param   bool        $throw   trow
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  null
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
	 * @param   array  $fields  fields
	 * @param   mixed  $user    user
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	public function edit($fields = [], $user = null)
	{
		$user = KunenaUserHelper::get($user);

		$this->bind($fields, ['name', 'email', 'subject', 'message', 'modified_reason'], true);

		// Update rest of the information
		$category            = $this->getCategory();
		$this->hold          = $category->review && !$category->isAuthorised('moderate', $user) ? 1 : $this->hold;
		$this->modified_by   = $user->userid;
		$this->modified_time = Factory::getDate()->toUnix();
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
	 * @param   int     $tmpid    tmpid
	 * @param   string  $postvar  postvar
	 * @param   null    $catid    catid
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function uploadAttachment($tmpid, $postvar, $catid = null)
	{
		$attachment                     = new Attachment;
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
	 * @param   array  $ids  ids
	 *
	 * @return  void
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function addAttachments(array $ids)
	{
		$this->_attachments_add += $this->getAttachments($ids, 'none');
	}

	/**
	 * Remove listed attachments from the message.
	 *
	 * @param   array  $ids  ids
	 *
	 * @return  void
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function removeAttachments(array $ids)
	{
		$this->_attachments_del += $this->getAttachments($ids, 'none');
	}

	/**
	 * Get the number of attachments into a message
	 *
	 * @return  array|StdClass
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getNbAttachments()
	{
		$attachments = AttachmentHelper::getNumberAttachments($this->id);

		$attachs           = new StdClass;
		$attachs->inline   = 0;
		$attachs->image    = 0;
		$attachs->file     = 0;
		$attachs->total    = 0;
		$attachs->readable = 0;

		foreach ($attachments as $attach)
		{
			if ($attach->inline)
			{
				$attachs->inline = $attachs->inline + 1;
			}

			if ($attach->isImage())
			{
				$attachs->image = $attachs->image + 1;
			}
			else
			{
				$attachs->file = $attachs->file + 1;
			}

			$attachs->total = $attachs->total + 1;

			if ($attach->isAuthorised('read'))
			{
				if (!$attach->inline)
				{
					$attachs->readable = $attachs->readable + 1;
				}
			}
		}

		return $attachs;
	}

	/**
	 * Method to load a \Kunena\Forum\Libraries\Forum\Message\Message object by id.
	 *
	 * @param   mixed  $id  The message id to be loaded
	 *
	 * @return  boolean  True on success
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function load($id = null)
	{
		$exists        = parent::load($id);
		$this->_hold   = $exists ? $this->hold : 1;
		$this->_thread = $this->thread;

		return $exists;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function check()
	{
		$author = KunenaUserHelper::get($this->userid);

		// Check username
		if (!$this->userid)
		{
			$this->name = trim($this->name);

			// Unregistered or anonymous users: Do not allow existing username
			$nicktaken = UserHelper::getUserId($this->name);

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
			if (!MailHelper::isEmailAddress($this->email))
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
			catch (ExecutionFailureException $e)
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
			$query               = $this->_db->getQuery(true);
			$query->select('m.id')
				->from($this->_db->quoteName('#__kunena_messages', 'm'))
				->innerJoin($this->_db->quoteName('#__kunena_messages_text', 't') . ' ON m.id = t.mesid')
				->where($this->_db->quoteName('m.userid') . ' = ' . $this->_db->quote($this->userid))
				->andWhere($this->_db->quoteName('m.ip') . ' = ' . $this->_db->quote($this->ip))
				->andWhere($this->_db->quoteName('t.message') . ' = ' . $this->_db->quote($this->message))
				->andWhere($this->_db->quoteName('m.time') . ' >= ' . $this->_db->quote($duplicatetimewindow));
			$this->_db->setQuery($query);

			try
			{
				$id = $this->_db->loadResult();
			}
			catch (ExecutionFailureException $e)
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
	 * @param   string   $string  string
	 * @param   integer  $start   start
	 * @param   integer  $length  length
	 *
	 * @return  string
	 *
	 * @since   Kunena 5.0.2
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
	 *
	 * @param   Mail    $mail          mail
	 * @param   int     $subscription  subscription
	 * @param   string  $subject       subject
	 * @param   string  $url           url
	 * @param   bool    $once          once
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function attachEmailBody($mail, $subscription, $subject, $url, $once)
	{
		$layout = Layout::factory('Email/Subscription')->debug(false)
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
		}

		$mail->setBody($msg);
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseRead(KunenaUser $user)
	{
		if ($this->hold && !$user->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_NO_ACCESS'), 401);
		}

		// Check that user has the right to see the post (user can see his own unapproved posts)
		if ($this->hold > 1 || ($this->hold == 1 && $this->userid != $user->userid))
		{
			$access = Access::getInstance();
			$hold   = $access->getAllowedHold($user->userid, $this->catid, false);

			if (!in_array($this->hold, $hold))
			{
				return new Authorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 */
	protected function authoriseNotHold(KunenaUser $user)
	{
		if ($this->hold)
		{
			// Nobody can reply to unapproved or deleted post
			return new Authorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseOwn(KunenaUser $user)
	{
		// Guests cannot own posts.
		if (!$user->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 401);
		}

		// Check that topic owned by the user or user is a moderator
		// TODO: check #__kunena_user_topics
		if ($this->userid != $user->userid && !$user->isModerator($this->getCategory()))
		{
			return new Authorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseThankyou(KunenaUser $user)
	{
		// Check that message is not your own
		if (!KunenaFactory::getConfig()->showthankyou)
		{
			return new Authorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 403);
		}

		if (!$user->userid)
		{
			// TODO: better error message
			return new Authorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 401);
		}

		if (!$this->userid || $this->userid == $user->userid)
		{
			// TODO: better error message
			return new Authorise(Text::_('COM_KUNENA_THANKYOU_DISABLED'), 403);
		}

		return;
	}

	/**
	 * This function check the edit time from the author of the post and return if the user is allowed to edit his post.
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|boolean|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
				return new Authorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}

			// Edit allowed if replies
			if (intval($config->useredit) == 2 && $this->getTopic()->getReplies())
			{
				return new Authorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_IF_REPLIES'), 403);
			}

			// Edit allowed for the first message of the topic
			if (intval($config->useredit) == 4 && $this->id != $this->getTopic()->first_post_id)
			{
				return new Authorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_ONLY_FIRST_MESSAGE_OF_TOPIC'), 403);
			}

			// Edit allowed for the last message of the topic
			if (intval($config->useredit) == 3 && $this->id != $this->getTopic()->last_post_id)
			{
				return new Authorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_ONLY_LAST_MESSAGE_OF_TOPIC'), 403);
			}
		}

		if (intval($config->useredittime) != 0)
		{
			// Check whether edit is in time
			$modtime = $this->modified_time ? $this->modified_time : $this->time;

			if ($modtime + intval($config->useredittime) < Factory::getDate()->toUnix() && intval($config->useredittimegrace) == 0)
			{
				return new Authorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}
			elseif (intval($config->useredittimegrace) != 0 && $modtime + intval($config->useredittime) + intval($config->useredittimegrace) < Factory::getDate()->toUnix())
			{
				return new Authorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseDelete(KunenaUser $user)
	{
		$config = KunenaFactory::getConfig();

		if (!$user->isModerator($this->getCategory()) && $config->userdeletetmessage != '2')
		{
			// Never
			if ($config->userdeletetmessage == '0')
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
			}

			// When no replies
			if ($config->userdeletetmessage == '1' && ($this->getTopic()->first_post_id != $this->id || $this->getTopic()->getReplies()))
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
			}

			// All except the first message of the topic
			if ($config->userdeletetmessage == '3' && $this->id == $this->getTopic()->first_post_id)
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_ONLY_FIRST_MESSAGE'), 403);
			}

			// Only the last message
			if ($config->userdeletetmessage == '4' && $this->id != $this->getTopic()->last_post_id)
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_ONLY_LAST_MESSAGE'), 403);
			}
		}
	}

	/**
	 * Check if user has the right to perm delete the message
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|NULL
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
			return new Authorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
		}

		return null;
	}

	/**
	 * Check if user has the right to upload image attachment
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseAttachmentsImage(KunenaUser $user)
	{
		if (empty(KunenaFactory::getConfig()->image_upload))
		{
			return new Authorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
		}

		if (KunenaFactory::getConfig()->image_upload == 'admin')
		{
			if (!$user->isAdmin())
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_ADMINISTRATORS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->image_upload == 'registered')
		{
			if (!$user->userid)
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_REGISTERED_USERS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->image_upload == 'moderator')
		{
			$category = $this->getCategory();

			if (!$user->isModerator($category))
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_MODERATORS'), 403);
			}
		}

		return;
	}

	/**
	 * Check if user has the right to upload file attachment
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseAttachmentsFile(KunenaUser $user)
	{
		if (empty(KunenaFactory::getConfig()->file_upload))
		{
			return new Authorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
		}

		if (KunenaFactory::getConfig()->file_upload == 'admin')
		{
			if (!$user->isAdmin())
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_ADMINISTRATORS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->file_upload == 'registered')
		{
			if (!$user->userid)
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_REGISTERED_USERS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->file_upload == 'moderator')
		{
			$category = $this->getCategory();

			if (!$user->isModerator($category))
			{
				return new Authorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_MODERATORS'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function authoriseGuestWrite(KunenaUser $user)
	{
		// Check if user is guest and they can create or reply topics
		if ($user->userid == 0 && !KunenaFactory::getConfig()->pubwrite)
		{
			return new Authorise(Text::_('COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN'), 401);
		}

		return;
	}
}
