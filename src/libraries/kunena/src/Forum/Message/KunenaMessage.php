<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Message
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message;

\defined('_JEXEC') or die();

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
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Email\KunenaEmail;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\Read\KunenaTopicUserReadHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use StdClass;

/**
 * Class \Kunena\Forum\Libraries\Forum\Message\Message
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
 * @since   Kunena 6.0
 */
class KunenaMessage extends KunenaDatabaseObject
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

	public $thankyou = [];

	public $replynum;

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
	 * @var     KunenaAttachment[]
	 * @since   Kunena 6.0
	 */
	protected $_attachments_add = [];

	/**
	 * @var     KunenaAttachment[]
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
	 * @var boolean
	 * @since Kunena 5.2
	 */
	protected $_approved = false;

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
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $urlNotification;

	/**
	 * @param   mixed  $properties  properties
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 * @internal
	 */
	public function __construct($properties = null)
	{
		$this->_db = Factory::getContainer()->get('DatabaseDriver');
		parent::__construct($properties);
	}

	/**
	 * Returns \Kunena\Forum\Libraries\Forum\Message\Message object.
	 *
	 * @param   int   $identifier  The message to load - Can be only an integer.
	 * @param   bool  $reload      reload
	 *
	 * @return  KunenaMessage
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function getInstance($identifier = null, $reload = false): KunenaMessage
	{
		return KunenaMessageHelper::get($identifier, $reload);
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
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function isNew($user = null): bool
	{
		$user = KunenaUserHelper::get($user);

		if (!KunenaFactory::getConfig()->showNew || !$user->exists())
		{
			return false;
		}

		$session = KunenaFactory::getSession();

		if ($this->time < $session->getAllReadTime())
		{
			return false;
		}

		$allreadtime = KunenaCategoryUserHelper::get($this->getCategory(), $user)->allreadtime;

		if ($allreadtime && $this->time < $allreadtime)
		{
			return false;
		}

		$read = KunenaTopicUserReadHelper::get($this->getTopic(), $user);

		if ($this->id == $read->message_id || $this->time < $read->time)
		{
			return false;
		}

		return true;
	}

	/**
	 * @return  KunenaCategory
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getCategory(): KunenaCategory
	{
		return KunenaCategoryHelper::get($this->catid);
	}

	/**
	 * @return \Kunena\Forum\Libraries\Forum\Topic\KunenaTopic|null
	 *
	 * @since   Kunena 6.0
	 * @throws \Exception
	 */
	public function getTopic(): ?KunenaTopic
	{
		if (!$this->_topic)
		{
			$this->_topic = KunenaTopicHelper::get($this->thread);
		}

		return $this->_topic;
	}

	/**
	 * @param   KunenaTopic  $topic  topic
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function setTopic(KunenaTopic $topic): void
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
	public function getState(): string
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
	 * @param   null|KunenaCategory  $category  Fake category if needed. Used for aliases.
	 * @param   bool                 $xhtml     xhtml
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getUrl($category = null, $xhtml = true): string
	{
		return $this->getTopic()->getUrl($category, $xhtml, $this);
	}

	/**
	 * @param   null|KunenaCategory  $category  Fake category if needed. Used for aliases.
	 *
	 * @return  Uri
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getUri($category = null)
	{
		return $this->getTopic()->getUri($category, $this);
	}

	/**
	 * @param   array       $fields      fields
	 * @param   int         $useridfromparent
	 * @param   null|array  $safefields  safefields
	 *
	 * @return  array
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function newReply($fields = [], $useridfromparent = 0, $safefields = null): array
	{
		$user     = KunenaUserHelper::get();
		$topic    = $this->getTopic();
		$category = $this->getCategory();

		$message = new KunenaMessage;
		$message->setTopic($topic);
		$message->parent  = $this->id;
		$message->thread  = $topic->id;
		$message->catid   = $topic->category_id;
		$message->name    = $user->getName('');
		$message->userid  = $user->userid;
		$message->subject = $this->subject;
		$message->ip      = KunenaUserHelper::getUserIp();

		// Add IP to user.
		if (KunenaConfig::getInstance()->ipTracking)
		{
			if (empty($user->ip))
			{
				$user->ip = KunenaUserHelper::getUserIp();
			}
		}

		if (KunenaConfig::getInstance()->allowChangeSubject && $topic->first_post_userid == $message->userid || KunenaUserHelper::getMyself()->isModerator())
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
			$userfromparent     = KunenaUserHelper::get($useridfromparent);
			$userfromparentname = $userfromparent->getName();

			if (empty($userfromparent->getName()))
			{
				$userfromparentname = 'anonymous';
			}

			$find             = ['/\[hide\](.*?)\[\/hide\]/su', '/\[confidential\](.*?)\[\/confidential\]/su'];
			$replace          = '';
			$text             = preg_replace($find, $replace, $this->message);
			$message->message = "[quote=\"{$userfromparentname} post={$this->id} userid={$useridfromparent}\"]" . $text . "[/quote]";
		}
		else
		{
			if (\is_array($safefields))
			{
				$message->bind($safefields);
			}

			if (\is_array($fields))
			{
				$message->bind($fields, ['name', 'email', 'subject', 'message'], true);
			}
		}

		return [$topic, $message];
	}

	/**
	 * Send email notifications from the message.
	 *
	 * @param   null|string  $url       url
	 * @param   boolean      $approved  false
	 *
	 * @return  boolean|false
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function sendNotification($url = null, $approved = false): bool
	{
		$config = KunenaFactory::getConfig();

		if (!$config->get('sendEmails'))
		{
			return false;
		}

		// Restore app input context
		Factory::getApplication()->input->set('message', $this);

		$url = $this->urlNotification;

		if ($this->hold > 1)
		{
			return false;
		}

		if ($this->hold == 1)
		{
			$mailsubs            = 0;
			$mailModeratorss     = $config->mailModerators >= 0;
			$mailAdministratorss = $config->mailAdministrators >= 0;
		}
		else
		{
			$mailsubs            = (bool) $config->allowSubscriptions;
			$mailModeratorss     = $config->mailModerators >= 1;
			$mailAdministratorss = $config->mailAdministrators >= 1;
		}

		$once = false;

		if ($mailsubs)
		{
			if (!$this->parent)
			{
				// New topic: Send email only to category subscribers
				$mailsubs = $config->categorySubscriptions != 'disabled' ? KunenaAccess::CATEGORY_SUBSCRIPTION : 0;
				$once     = $config->categorySubscriptions == 'topic';
			}
			elseif ($config->categorySubscriptions != 'post')
			{
				// Existing topic: Send email only to topic subscribers
				$mailsubs = $config->topicSubscriptions != 'disabled' ? KunenaAccess::TOPIC_SUBSCRIPTION : 0;
				$once     = $config->topicSubscriptions == 'first';
			}
			else
			{
				// Existing topic: Send email to both category and topic subscribers
				$mailsubs = $config->topicSubscriptions == 'disabled'
					? KunenaAccess::CATEGORY_SUBSCRIPTION
					: KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION;

				// FIXME: category subscription can override topic
				$once = $config->topicSubscriptions == 'first';
			}
		}

		if (!$url)
		{
			$url = Uri::getInstance()->toString(['scheme', 'host', 'port']) . $this->getPermaUrl();
		}

		// Get all subscribers, moderators and admins who should get the email.
		$emailToList = KunenaAccess::getInstance()->getSubscribers(
			$this->catid,
			$this->thread,
			$mailsubs,
			$mailModeratorss,
			$mailAdministratorss,
			$this->userid
		);

		if (empty($emailToList))
		{
			return true;
		}

		if (!$config->getEmail())
		{
			KunenaError::warning(Text::_('COM_KUNENA_EMAIL_DISABLED'));

			return false;
		}

		if (!MailHelper::isEmailAddress($config->getEmail()))
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

		$mailnamesender = !empty($config->email_sender_name) ? \Joomla\CMS\Mail\MailHelper::cleanAddress($config->email_sender_name) : \Joomla\CMS\Mail\MailHelper::cleanAddress($config->board_title);
		$mailsubject    = MailHelper::cleanSubject($topic->subject . " (" . $this->getCategory()->name . ")");
		$subject        = $this->subject ? $this->subject : $topic->subject;

		// Create email.
		$user = Factory::getApplication()->getIdentity();
		$mail = Factory::getMailer();
		$mail->setSubject($mailsubject);
		$mail->setSender([$config->getEmail(), $mailnamesender]);
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
					'mail'       => '',
					'subject'    => $subject,
					'name'       => $this->name,
					'message'    => $this->message,
					'messageUrl' => $url,
					'once'       => $once,
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
					'mail'       => '',
					'subject'    => $subject,
					'name'       => $this->name,
					'message'    => $this->message,
					'messageUrl' => $url,
					'once'       => $once,
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
		$recv_amount = \count($receivers[1]) + \count($receivers[0]);

		Log::add("$recv_amount subscriptions for msg $mid sent for {$time_secs} [ms]", Log::DEBUG, 'kunena');
		KunenaLog::log(
			($ok) ? KunenaLog::TYPE_REPORT : KunenaLog::TYPE_ERROR,
			KunenaLog::LOG_TOPIC_NOTIFY,
			"$recv_amount subscriptions sent for $time_secs sec",
			$this->getCategory(),
			$this->getTopic(),
			KunenaFactory::getUser($this->userid)
		);

		// Update subscriptions.
		if ($once && $sentusers)
		{
			$sentusers = implode(',', $sentusers);
			$db        = Factory::getContainer()->get('DatabaseDriver');
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

		return true;
	}

	/**
	 *  Get permament topic URL without domain.
	 *
	 * If you want to add domain (for email etc), you can prepend the output with this:
	 * Uri::getInstance()->toString(array('scheme', 'host', 'port'))
	 *
	 * @param   null|KunenaCategory  $category  Fake category if needed. Used for aliases.
	 * @param   bool                 $xhtml     xhtml
	 *
	 * @return  string
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getPermaUrl($category = null, $xhtml = true): string
	{
		$uri = $this->getPermaUri($category);

		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * @param   null|KunenaCategory  $category  category
	 *
	 * @return  Uri|boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getPermaUri($category = null)
	{
		$category = $category ? KunenaCategoryHelper::get($category) : $this->getCategory();

		if (!$this->exists() || !$category->exists())
		{
			return false;
		}

		return Uri::getInstance("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$this->thread}&mesid={$this->id}");
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function notificationCloseConnection(): void
	{
		$app = Factory::getApplication();
		$app->setHeader('Connection', 'close');
	}

	/**
	 * @param   int  $value  value
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena
	 */
	public function publish($value = KunenaForum::PUBLISHED)
	{
		if ($this->hold == $value)
		{
			return true;
		}

		$this->hold = (int) $value;

		return $this->save();
	}

	/**
	 * Method to save the \Kunena\Forum\Libraries\Forum\Message\Message object to the database.
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 * @since   Kunena 6.0
	 */
	public function save(): bool
	{
		$user = KunenaUserHelper::getMyself();

		if ($user->userid == 0 && $this->userid)
		{
			$user = KunenaUserHelper::get($this->userid);
		}

		if ($user->userid == 0 && !KunenaFactory::getConfig()->pubWrite)
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
	 * @return  boolean
	 *
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	protected function updateAttachments(): bool
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
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function delete(): bool
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

		$db = Factory::getContainer()->get('DatabaseDriver');

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

		KunenaMessageThankyouHelper::recount();

		return true;
	}

	/**
	 * @param   bool|array  $ids     ids
	 * @param   string      $action  action
	 *
	 * @return  KunenaAttachment[]
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena
	 */
	public function getAttachments($ids = false, $action = 'read'): array
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
	 * @param   bool  $newTopic  new topic
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function update($newTopic = false): void
	{
		// If post was published and then moved, we need to update old topic
		if (!$this->_hold && $this->_thread && $this->_thread != $this->thread)
		{
			$topic = KunenaTopicHelper::get($this->_thread);

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
	protected function delta(): int
	{
		if (!$this->hold && ($this->_hold || $this->thread != $this->_thread))
		{
			// Publish message or move it into new topic
			return 1;
		}

		if (!$this->_hold && $this->hold)
		{
			// Unpublish message
			return -1;
		}

		return 0;
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  \Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUser
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getUserTopic($user = null)
	{
		return $this->getTopic()->getUserTopic($user);
	}

	/**
	 * @return  KunenaMessageThankyouHelper
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getThankyou(): KunenaMessageThankyouHelper
	{
		return KunenaMessageThankyouHelper::get($this->id);
	}

	/**
	 * @return  KunenaMessage
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getParent(): KunenaMessage
	{
		return KunenaMessageHelper::get($this->parent);
	}

	/**
	 * @return  KunenaUser
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getAuthor(): KunenaUser
	{
		return KunenaUserHelper::getAuthor($this->userid, $this->name);
	}

	/**
	 * @return  KunenaDate
	 *
	 * @since   Kunena 6.0
	 */
	public function getTime(): KunenaDate
	{
		return new KunenaDate($this->time);
	}

	/**
	 * @return  KunenaUser
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getModifier(): KunenaUser
	{
		return KunenaUserHelper::get($this->modified_by);
	}

	/**
	 * @return  KunenaDate
	 *
	 * @since   Kunena 6.0
	 */
	public function getModifiedTime(): KunenaDate
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
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function displayField(string $field, $html = true, $context = '')
	{
		switch ($field)
		{
			case 'id':
				return \intval($this->id);
			case 'subject':
				return KunenaParser::parseText($this->subject);
			case 'message':
				return $html ? KunenaParser::parseBBCode($this->message, $this, 0, $context) : KunenaParser::stripBBCode($this->message, 0, $html);
		}

		return '';
	}

	/**
	 * Returns true if user is authorised to do the action.
	 *
	 * @param   string           $action  action
	 * @param   KunenaUser|null  $user    user
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 4.0
	 */
	public function isAuthorised(string $action = 'read', KunenaUser $user = null)
	{
		if (KunenaFactory::getConfig()->readOnly)
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
	 * @param   string           $action  action
	 * @param   KunenaUser|null  $user    user
	 * @param   bool             $throw   trow
	 *
	 * @return  mixed
	 *
	 * @throws Exception
	 * @since   Kunena 4.0
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
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function edit($fields = [], $user = null): void
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
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function makeAnonymous($user = null): void
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
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function uploadAttachment(int $tmpid, string $postvar, $catid = null): bool
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
	 * @param   array  $ids  ids
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 4.0
	 */
	public function addAttachments(array $ids): void
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
	 * @throws  Exception
	 * @since   Kunena 4.0
	 */
	public function removeAttachments(array $ids): void
	{
		$this->_attachments_del += $this->getAttachments($ids, 'none');
	}

	/**
	 * Get the number of attachments into a message
	 *
	 * @return  StdClass
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function getNbAttachments()
	{
		$attachments = KunenaAttachmentHelper::getNumberAttachments($this->id);

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
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public function load($id = null): bool
	{
		$exists        = parent::load($id);
		$this->_hold   = $exists ? $this->hold : 1;
		$this->_thread = $this->thread;

		return $exists;
	}

	/**
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function check(): bool
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
		elseif (!KunenaUserHelper::getMyself()->exists() && KunenaFactory::getConfig()->askEmail)
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

		if ($config->floodProtection && !$this->getCategory()->isAuthorised('moderate') && !$this->exists())
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

			if ($lastPostTime + $config->floodProtection > Factory::getDate()->toUnix())
			{
				$this->setError(Text::sprintf('COM_KUNENA_LIB_MESSAGE_ERROR_FLOOD', (int) $config->floodProtection));

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
	public function getsubstr(string $string, int $start, int $length): string
	{
		$mbString = \extension_loaded('mbstring');

		if ($mbString)
		{
			$title = mb_substr($string, $start, $length);
		}
		else
		{
			$title2 = substr($string, $start, $length);
			$title  = preg_replace(
				'/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
				'|[\x00-\x7F][\x80-\xBF]+' .
				'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
				'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
				'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
				'',
				$title2
			);
		}

		return $title;
	}

	/**
	 * @param   Mail    $mail          mail
	 * @param   int     $subscription  subscription
	 * @param   string  $subject       subject
	 * @param   string  $url           url
	 * @param   bool    $once          once
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function attachEmailBody(Mail $mail, int $subscription, string $subject, string $url, bool $once): void
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
		}

		$mail->setBody($msg);
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
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

			if (!\in_array($this->hold, $hold))
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|void
	 *
	 * @since   Kunena 6.0
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
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
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
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authoriseThankyou(KunenaUser $user)
	{
		// Check that message is not your own
		if (!KunenaFactory::getConfig()->showThankYou)
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
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|boolean|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
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

		if (\intval($config->userEdit) != 1)
		{
			// Edit never allowed
			if (\intval($config->userEdit) == 0)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}

			// Edit allowed if replies
			if (\intval($config->userEdit) == 2 && $this->getTopic()->getReplies())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_IF_REPLIES'), 403);
			}

			// Edit allowed for the first message of the topic
			if (\intval($config->userEdit) == 4 && $this->id != $this->getTopic()->first_post_id)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_ONLY_FIRST_MESSAGE_OF_TOPIC'), 403);
			}

			// Edit allowed for the last message of the topic
			if (\intval($config->userEdit) == 3 && $this->id != $this->getTopic()->last_post_id)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_ALLOWED_ONLY_LAST_MESSAGE_OF_TOPIC'), 403);
			}
		}

		if (\intval($config->userEditTime) != 0)
		{
			// Check whether edit is in time
			$modtime = $this->modified_time ? $this->modified_time : $this->time;

			if ($modtime + \intval($config->userEditTime) < Factory::getDate()->toUnix() && \intval($config->userEditTimeGrace) == 0)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}
			elseif (\intval($config->userEditTimeGrace) != 0 && $modtime + \intval($config->userEditTime) + \intval($config->userEditTimeGrace) < Factory::getDate()->toUnix())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_EDIT_NOT_ALLOWED'), 403);
			}
		}

		return;
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authoriseDelete(KunenaUser $user)
	{
		$config = KunenaFactory::getConfig();

		if (!$user->isModerator($this->getCategory()) && $config->userDeleteMessage != '2')
		{
			// Never
			if ($config->userDeleteMessage == '0')
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
			}

			// When no replies
			if ($config->userDeleteMessage == '1' && ($this->getTopic()->first_post_id != $this->id || $this->getTopic()->getReplies()))
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
			}

			// All except the first message of the topic
			if ($config->userDeleteMessage == '3' && $this->id == $this->getTopic()->first_post_id)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_ONLY_FIRST_MESSAGE'), 403);
			}

			// Only the last message
			if ($config->userDeleteMessage == '4' && $this->id != $this->getTopic()->last_post_id)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_ONLY_LAST_MESSAGE'), 403);
			}
		}
	}

	/**
	 * Check if user has the right to perm delete the message
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|NULL
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authorisePermdelete(KunenaUser $user)
	{
		$config = KunenaFactory::getConfig();

		if ($user->isAdmin() || $user->isModerator())
		{
			return false;
		}

		if ($user->isModerator($this->getTopic()->getCategory()) && !$config->moderatorPermDelete || !$user->isModerator($this->getTopic()->getCategory()))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
		}

		return;
	}

	/**
	 * Check if user has the right to upload image attachment
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authoriseAttachmentsImage(KunenaUser $user)
	{
		if (empty(KunenaFactory::getConfig()->imageUpload))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
		}

		if (KunenaFactory::getConfig()->imageUpload == 'admin')
		{
			if (!$user->isAdmin())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_ADMINISTRATORS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->imageUpload == 'registered')
		{
			if (!$user->userid)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_IMAGE_ONLY_FOR_REGISTERED_USERS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->imageUpload == 'moderator')
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
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authoriseAttachmentsFile(KunenaUser $user)
	{
		if (empty(KunenaFactory::getConfig()->fileUpload))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_NOT_ALLOWED'), 403);
		}

		if (KunenaFactory::getConfig()->fileUpload == 'admin')
		{
			if (!$user->isAdmin())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_ADMINISTRATORS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->fileUpload == 'registered')
		{
			if (!$user->userid)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ATTACHMENTS_FILE_ONLY_FOR_REGISTERED_USERS'), 403);
			}
		}

		if (KunenaFactory::getConfig()->fileUpload == 'moderator')
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
	 * @param   KunenaUser  $user  user
	 *
	 * @return  KunenaExceptionAuthorise|void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function authoriseGuestWrite(KunenaUser $user)
	{
		// Check if user is guest and they can create or reply topics
		if ($user->userid == 0 && !KunenaFactory::getConfig()->pubWrite)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN'), 401);
		}

		return;
	}
}
