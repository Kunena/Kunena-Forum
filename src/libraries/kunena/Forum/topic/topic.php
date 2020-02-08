<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic;

defined('_JEXEC') or die();

use DateTime;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Database\KunenaDatabaseObject;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Exception\Authorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\Category;
use Kunena\Forum\Libraries\Forum\Category\CategoryHelper;
use Kunena\Forum\Libraries\Forum\Category\User\CategoryUserHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\Message;
use Kunena\Forum\Libraries\Forum\Message\MessageHelper;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\MessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\Poll\Poll;
use Kunena\Forum\Libraries\Forum\Topic\Poll\PollHelper;
use Kunena\Forum\Libraries\Forum\Topic\Rate\RateHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\Read\TopicUserReadHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\TopicUserHelper;
use Kunena\Forum\Libraries\Html\Parser;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;
use function defined;

/**
 * Class \Kunena\Forum\Libraries\Forum\Topic\Topic
 *
 * @since   Kunena 6.0
 * @property int    $id
 * @property int    $category_id
 * @property string $subject
 * @property int    $icon_id
 * @property int    $locked
 * @property int    $hold
 * @property int    $ordering
 * @property int    $posts
 * @property int    $hits
 * @property int    $attachments
 * @property int    $poll_id
 * @property int    $moved_id
 * @property int    $first_post_id
 * @property int    $first_post_time
 * @property int    $first_post_userid
 * @property string $first_post_message
 * @property string $first_post_guest_name
 * @property int    $last_post_id
 * @property int    $last_post_time
 * @property int    $last_post_userid
 * @property string $last_post_message
 * @property string $last_post_guest_name
 * @property string $params
 * @property int    $rating
 * @property int    $count
 *
 */
class Topic extends KunenaDatabaseObject
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected static $actions = [
		'none'                        => [],
		'read'                        => ['Read'],
		'create'                      => ['NotExists', 'GuestWrite'],
		'reply'                       => ['Read', 'NotHold', 'GuestWrite', 'NotMoved', 'Unlocked'],
		'edit'                        => ['Read', 'NotMoved', 'Unlocked', 'Own'],
		'move'                        => ['Read'],
		'approve'                     => ['Read', 'NotMoved'],
		'delete'                      => ['Read'],
		'undelete'                    => ['Read'],
		'permdelete'                  => ['Read', 'Permdelete'],
		'favorite'                    => ['Read'],
		'subscribe'                   => ['Read'],
		'sticky'                      => ['Read'],
		'lock'                        => ['Read'],
		'rate'                        => ['Read', 'Unlocked'],
		'poll.read'                   => ['Read', 'Poll'],
		'poll.create'                 => ['Own'],
		'poll.edit'                   => ['Read', 'NoVotes'],
		'poll.delete'                 => ['Read', 'Own', 'Poll'],
		'poll.vote'                   => ['Read', 'Poll', 'Vote'],
		'post.read'                   => ['Read'],
		'post.thankyou'               => ['Read', 'NotMoved', 'Unlocked'],
		'post.unthankyou'             => ['Read', 'Unlocked'],
		'post.reply'                  => ['Read', 'NotHold', 'GuestWrite', 'NotMoved', 'Unlocked'],
		'post.edit'                   => ['Read', 'Unlocked'],
		'post.move'                   => ['Read'],
		'post.approve'                => ['Read'],
		'post.delete'                 => ['Read', 'Unlocked'],
		'post.undelete'               => ['Read'],
		'post.permdelete'             => ['Read', 'Permdelete'],
		'post.attachment.read'        => ['Read'],
		'post.attachment.createimage' => ['Unlocked'],
		'post.attachment.createfile'  => ['Unlocked'],
		'post.attachment.delete'      => [],
		// TODO: In the future we might want to restrict this: array('Read','Unlocked'),
	];

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $id = null;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $unread = 0;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	public $lastread = 0;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $_table = 'KunenaTopics';

	/**
	 * @var     DatabaseDriver|void
	 * @since   Kunena 6.0
	 */
	protected $_db = null;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_authcache = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_authccache = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $_authfcache = [];

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $_hold = 1;

	/**
	 * @var     integer
	 * @since   Kunena 6.0
	 */
	protected $_posts = 0;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $_pagination = null;

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
		if (!empty($this->id))
		{
			$this->_exists = true;
			$this->_hold   = $this->hold;
			$this->_posts  = $this->posts;
		}
		else
		{
			parent::__construct($properties);
		}

		$this->_db = Factory::getDBO();
	}

	/**
	 * Returns \Kunena\Forum\Libraries\Forum\Topic\Topic object.
	 *
	 * @param   int   $identifier  The topic to load - Can be only an integer.
	 * @param   bool  $reset       reset
	 *
	 * @return  Topic
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getInstance($identifier = null, $reset = false)
	{
		return TopicHelper::get($identifier, $reset);
	}

	/**
	 * Subscribe / Unsubscribe user to this topic.
	 *
	 * @param   bool   $value  True for subscribe, false for unsubscribe.
	 * @param   mixed  $user   user
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function subscribe($value = true, $user = null)
	{
		$usertopic             = $this->getUserTopic($user);
		$usertopic->subscribed = (boolean) $value;

		if (!$usertopic->save())
		{
			$this->setError($usertopic->getError());

			return false;
		}

		return true;
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  User\TopicUser
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getUserTopic($user = null)
	{
		return TopicUserHelper::get($this, $user);
	}

	/**
	 * Favorite / unfavorite user to this topic.
	 *
	 * @param   bool   $value  True for favorite, false for unfavorite.
	 * @param   mixed  $user   user
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function favorite($value = true, $user = null)
	{
		$usertopic           = $this->getUserTopic($user);
		$usertopic->favorite = (int) $value;

		if (!$usertopic->save())
		{
			$this->setError($usertopic->getError());

			return false;
		}

		return true;
	}

	/**
	 * @param   int  $value  values
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function sticky($value = 1)
	{
		$this->ordering = (int) $value;

		return $this->save();
	}

	/**
	 * Method to save the \Kunena\Forum\Libraries\Forum\Topic\Topic object to the database.
	 *
	 * @param   bool  $cascade  cascade
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function save($cascade = true)
	{
		$topicDelta = $this->delta();
		$postDelta  = $this->posts - $this->_posts;

		$isNew = !$this->exists();

		if (!parent::save())
		{
			return false;
		}

		$this->_posts = $this->posts;

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = [];

		if ($cascade)
		{
			$category = $this->getCategory();

			if (!$category->update($this, $topicDelta, $postDelta))
			{
				$this->setError($category->getError());
			}
		}

		return true;
	}

	/**
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	protected function delta()
	{
		if (!$this->hold && $this->_hold)
		{
			// Create or publish topic
			return 1;
		}

		if ($this->hold && !$this->_hold)
		{
			// Delete or unpublish topic
			return -1;
		}

		return 0;
	}

	/**
	 * @param   null|bool  $exists  exists
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 */
	public function exists($exists = null)
	{
		if ($exists !== null)
		{
			$this->_hold  = $this->hold;
			$this->_posts = $this->posts;
		}

		return parent::exists($exists);
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
		return CategoryHelper::get($this->category_id);
	}

	/**
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getActions()
	{
		$txt = '';

		if ($this->ordering)
		{
			$txt = $this->getCategory()->class_sfx ? $txt . '' : $txt . '-stickymsg';
		}

		if ($this->hold == 1)
		{
			$txt .= ' unapproved';
		}
		else
		{
			if ($this->hold)
			{
				$txt .= ' deleted';
			}
		}

		if ($this->moved_id > 0)
		{
			$txt .= ' moved';
		}

		if ($this->locked)
		{
			$txt .= ' locked';
		}

		return $txt;
	}

	/**
	 * @param   int  $value  value
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function lock($value = 1)
	{
		$this->locked = (int) $value;

		return $this->save();
	}

	/**
	 * @param   mixed        $user  user
	 * @param   bool|string  $glue  glue
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function getKeywords($user = null, $glue = false)
	{
		return;
	}

	/**
	 * @param   int  $value  value
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function publish($value = KunenaForum::PUBLISHED)
	{
		if ($value < 0 || $value > 3)
		{
			$value = 0;
		}

		$this->hold = (int) $value;
		$query      = $this->_db->getQuery(true);
		$query->update($this->_db->quoteName('#__kunena_messages'))
			->set($this->_db->quoteName('hold') . ' = ' . $this->_db->quote($this->hold))
			->where($this->_db->quoteName('thread') . ' = ' . (int) $this->id . ' AND ' . $this->_db->quoteName('hold') . ' = ' . $this->_db->quote($this->_hold));

		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return $this->_db->getAffectedRows() ? $this->recount() : $this->save();
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function recount()
	{
		if (!$this->moved_id)
		{
			// Recount total posts and attachments
			$query = $this->_db->getQuery(true);
			$query->select('COUNT(DISTINCT ' . $this->_db->quoteName('m.id') . ') AS ' . $this->_db->quoteName('posts') . ', COUNT(' . $this->_db->quoteName('a.id') . ') AS ' . $this->_db->quoteName('attachments'))
				->from($this->_db->quoteName('#__kunena_messages', 'm'))
				->leftJoin($this->_db->quoteName('#__kunena_attachments', 'a') . ' ON ' . $this->_db->quoteName('m.id') . ' = ' . $this->_db->quoteName('a.mesid'))
				->where($this->_db->quoteName('m.hold') . ' = ' . $this->_db->quote($this->hold) . ' AND ' . $this->_db->quoteName('m.thread') . ' = ' . $this->_db->quote($this->id))
				->group($this->_db->quoteName('m.thread'));
			$this->_db->setQuery($query);

			try
			{
				$result = $this->_db->loadAssoc();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}

			if (!$result)
			{
				$this->posts = 0;

				// Double check if all posts have been removed from the database
				$query = $this->_db->getQuery(true);
				$query->select('COUNT(' . $this->_db->quoteName('m.id') . ') AS ' . $this->_db->quoteName('posts') . ', MIN(' . $this->_db->quoteName('m.hold') . ') AS ' . $this->_db->quoteName('hold'))
					->from($this->_db->quoteName('#__kunena_messages', 'm'))
					->where($this->_db->quoteName('m.thread') . ' = ' . $this->_db->quote($this->id))
					->group($this->_db->quoteName('m.thread'));
				$this->_db->setQuery($query);

				try
				{
					$result = $this->_db->loadAssoc();
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);

					return false;
				}

				if ($result)
				{
					// Information in the database was wrong, recount topic
					$this->hold = $result['hold'];
					$this->recount();
				}

				return true;
			}

			$this->bind($result);
		}

		return $this->update();
	}

	/**
	 * @param   Message  $message    message
	 * @param   int      $postdelta  postdelta
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function update($message = null, $postdelta = 0)
	{
		// Update post count
		$this->posts += $postdelta;
		$exists      = $message && $message->exists();

		if (!$this->exists())
		{
			if (!$exists)
			{
				$this->setError(Text::_('COM_KUNENA_LIB_TOPIC_NOT_EXISTS'));

				return false;
			}

			$this->id = $message->id;
		}

		if ($exists && $message->thread == $this->id && $message->hold == $this->hold)
		{
			// If message belongs into this topic and has same state, we may need to update cache
			$this->updatePostInfo($message->id, $message->time, $message->userid, $message->message, $message->name);
		}
		elseif (!$this->moved_id)
		{
			if (!isset($this->hold))
			{
				$this->hold = KunenaForum::TOPIC_DELETED;
			}

			// If message isn't visible anymore, check if we need to update cache
			if (!$exists || $this->first_post_id == $message->id)
			{
				// If message got deleted and was cached, we need to find new first post
				$db    = Factory::getDBO();
				$query = $this->_db->getQuery(true);
				$query->select('*')
					->from($this->_db->quoteName('#__kunena_messages', 'm'))
					->innerJoin($this->_db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $this->_db->quoteName('t.mesid') . '=' . $this->_db->quoteName('m.id'))
					->where($this->_db->quoteName('m.thread') . ' = ' . $db->quote($this->id) . ' AND ' . $this->_db->quoteName('m.hold') . ' = ' . $this->_db->quote($this->hold))
					->order($this->_db->quoteName('m.time') . ' ASC, ' . $this->_db->quoteName('m.id') . ' ASC');
				$query->setLimit(1);
				$db->setQuery($query);

				try
				{
					$first = $db->loadObject();
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);
				}

				if ($first)
				{
					$this->first_post_time = 0;
					$this->updatePostInfo($first->id, $first->time, $first->userid, $first->message, $first->name);
				}
				else
				{
					$this->updatePostInfo(false);
				}
			}

			if (!$exists || $this->last_post_id == $message->id)
			{
				// If topic got deleted and was cached, we need to find new last post
				$db    = Factory::getDBO();
				$query = $this->_db->getQuery(true);
				$query->select('*')
					->from($this->_db->quoteName('#__kunena_messages', 'm'))
					->innerJoin($this->_db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $this->_db->quoteName('t.mesid') . ' = ' . $this->_db->quoteName('m.id'))
					->where($this->_db->quoteName('m.thread') . ' = ' . $db->quote($this->id) . ' AND ' . $this->_db->quoteName('m.hold') . ' = ' . $this->_db->quote($this->hold))
					->order($this->_db->quoteName('m.time') . ' ASC, ' . $this->_db->quoteName('m.id') . ' ASC');
				$query->setLimit(1);
				$db->setQuery($query);

				try
				{
					$last = $db->loadObject();
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);
				}

				if ($last)
				{
					$this->last_post_time = 0;
					$this->updatePostInfo($last->id, $last->time, $last->userid, $last->message, $last->name);
				}
				else
				{
					$this->updatePostInfo(false);
				}
			}
		}

		if (!$this->first_post_id || !$this->last_post_id)
		{
			// If topic has no visible posts, mark it deleted and recount
			$this->hold = $exists ? $message->hold : KunenaForum::TOPIC_DELETED;
			$this->recount();
		}

		if (!($message && $message->exists()) && !$this->posts)
		{
			return $this->delete();
		}

		if (!$this->save())
		{
			return false;
		}

		if ($exists && $message->userid && abs($postdelta) <= 1)
		{
			// Update user topic
			$usertopic = $this->getUserTopic($message->userid);

			if (!$usertopic->update($message, $postdelta))
			{
				$this->setError($usertopic->getError());
			}

			// Update post count from user
			$user        = KunenaUserHelper::get($message->userid);
			$user->posts += $postdelta;

			if (!$user->save())
			{
				$this->setError($user->getError());
			}
		}
		else
		{
			TopicUserHelper::recount($this->id);

			// FIXME: optimize
			KunenaUserHelper::recount();
		}

		return true;
	}

	/**
	 * @param   int     $id       id
	 * @param   int     $time     time
	 * @param   int     $userid   userid
	 * @param   string  $message  message
	 * @param   string  $name     name
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function updatePostInfo($id, $time = 0, $userid = 0, $message = '', $name = '')
	{
		if ($id === false)
		{
			$this->first_post_id         = 0;
			$this->first_post_time       = 0;
			$this->first_post_userid     = 0;
			$this->first_post_message    = '';
			$this->first_post_guest_name = '';
			$this->last_post_id          = 0;
			$this->last_post_time        = 0;
			$this->last_post_userid      = 0;
			$this->last_post_message     = '';
			$this->last_post_guest_name  = '';

			return;
		}

		if (!$this->first_post_time || ($this->first_post_time > $time || ($this->first_post_time == $time && $this->first_post_id >= $id)))
		{
			$this->first_post_id         = $id;
			$this->first_post_time       = $time;
			$this->first_post_userid     = $userid;
			$this->first_post_message    = $message;
			$this->first_post_guest_name = $name;
		}

		if ($this->last_post_time < $time || ($this->last_post_time == $time && $this->last_post_id <= $id))
		{
			$this->last_post_id         = $id;
			$this->last_post_time       = $time;
			$this->last_post_userid     = $userid;
			$this->last_post_message    = $message;
			$this->last_post_guest_name = $name;
		}
	}

	/**
	 * Method to delete the \Kunena\Forum\Libraries\Forum\Topic\Topic object from the database.
	 *
	 * @param   bool  $recount  recount
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function delete($recount = true)
	{
		if (!$this->exists())
		{
			return true;
		}

		if (!parent::delete())
		{
			return false;
		}

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = [];

		// NOTE: shadow topic doesn't exist, DO NOT DELETE OR CHANGE ANY EXTERNAL INFORMATION
		if ($this->moved_id == 0)
		{
			$db = Factory::getDBO();

			// Delete user topics
			$queries[] = "DELETE FROM #__kunena_user_topics WHERE topic_id={$db->quote($this->id)}";

			// Delete user read
			$queries[] = "DELETE FROM #__kunena_user_read WHERE topic_id={$db->quote($this->id)}";

			if ($this->poll_id)
			{
				// Delete poll (users)
				$queries[] = "DELETE FROM #__kunena_polls_users WHERE pollid={$db->quote($this->poll_id)}";

				// Delete poll (options)
				$queries[] = "DELETE FROM #__kunena_polls_options WHERE pollid={$db->quote($this->poll_id)}";

				// Delete poll
				$queries[] = "DELETE FROM #__kunena_polls WHERE id={$db->quote($this->poll_id)}";
			}

			// Delete thank yous
			$queries[] = "DELETE t FROM #__kunena_thankyou AS t INNER JOIN #__kunena_messages AS m ON m.id=t.postid WHERE m.thread={$db->quote($this->id)}";

			// Delete all messages
			$queries[] = "DELETE m, t FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.thread={$db->quote($this->id)}";

			// Delete rating
			$queries[] = "DELETE FROM #__kunena_rate WHERE topic_id={$db->quote($this->id)}";

			foreach ($queries as $query)
			{
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (ExecutionFailureException $e)
				{
					KunenaError::displayDatabaseError($e);

					return false;
				}
			}

			// FIXME: add recount statistics
			if ($recount)
			{
				KunenaUserHelper::recount();
				MessageThankyouHelper::recount();
			}
		}

		return true;
	}

	/**
	 * Send email notifications from the first post in the topic.
	 *
	 * @param   null|string  $url  url
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	public function sendNotification($url = null)
	{
		// Reload message just in case if it was published by bulk update.
		MessageHelper::get($this->first_post_id, true)->sendNotification($url);
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
		return KunenaUserHelper::getAuthor($this->first_post_userid, $this->first_post_guest_name);
	}

	/**
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 */
	public function getHits()
	{
		return $this->hits;
	}

	/**
	 * Increase hit counter for this topic.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function hit()
	{
		$app     = Factory::getApplication();
		$lasthit = $app->getUserState('com_kunena.topic.lasthit');

		if ($lasthit == $this->id)
		{
			return;
		}

		// Update only hit - not entire object
		$table     = $this->getTable();
		$table->id = $this->id;

		if ($table->hit())
		{
			$this->hits++;
			$app->setUserState('com_kunena.topic.lasthit', $this->id);
		}
	}

	/**
	 * @param   int|void  $limitstart  Null if all pages need to be active.
	 * @param   int       $limit       limit
	 * @param   int       $display     display
	 * @param   string    $prefix      prefix
	 *
	 * @return  Pagination
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getPagination($limitstart = 0, $limit = 6, $display = 4, $prefix = '')
	{
		if (!$this->_pagination)
		{
			$this->_pagination = new Pagination($this->posts, $limitstart, $limit, $prefix);
			$this->_pagination
				->setUri($this->getUri())
				->setDisplayedPages($display);

			if ($limitstart == null)
			{
				$this->_pagination->pagesCurrent = 0;
			}
		}

		return $this->_pagination;
	}

	/**
	 * @param   mixed   $category  category
	 * @param   string  $action    action
	 *
	 * @return  Uri|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getUri($category = null, $action = null)
	{
		$category = $category ? CategoryHelper::get($category) : $this->getCategory();
		$Itemid   = KunenaRoute::getCategoryItemid($category);

		if ($action instanceof Message)
		{
			$message = $action;
			$action  = 'post' . $message->id;
		}

		$uri = Uri::getInstance("index.php?option=com_kunena&view=topic&catid={$category->id}&id={$this->id}&action={$action}&Itemid={$Itemid}");

		if ($uri->getVar('action') !== null)
		{
			$uri->delVar('action');
			$mesid = 0;
			$limit = max(1, intval(KunenaFactory::getConfig()->messages_per_page));

			if (isset($message))
			{
				$mesid = $message->id;
			}
			elseif ((string) $action === (string) (int) $action)
			{
				if ($action > 0)
				{
					$uri->setVar('limitstart', $action * $limit);
				}
			}
			else
			{
				switch ($action)
				{
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

			if ($mesid)
			{
				if (KunenaUserHelper::getMyself()->getTopicLayout() != 'threaded' && $mesid > 0)
				{
					$uri->setFragment($mesid);
					$limitstart = intval($this->getPostLocation($mesid) / $limit) * $limit;

					if ($limitstart)
					{
						$uri->setVar('limitstart', $limitstart);
					}
				}
				else
				{
					$uri->setVar('mesid', $mesid);
				}
			}
		}

		return $uri;
	}

	/**
	 * @param   int          $mesid      mesid
	 * @param   string|void  $direction  direction
	 * @param   mixed        $hold       hold
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getPostLocation($mesid, $direction = null, $hold = null)
	{
		if (is_null($direction))
		{
			$direction = KunenaUserHelper::getMyself()->getMessageOrdering();
		}

		if (!isset($this->lastread))
		{
			$this->lastread = $this->last_post_id;
			$this->unread   = 0;
		}

		if ($mesid == 'unread')
		{
			$mesid = $this->lastread;
		}

		if ($this->moved_id || !KunenaUserHelper::getMyself()->isModerator($this->getCategory()))
		{
			if ($mesid == 'first' || $mesid == $this->first_post_id)
			{
				return $direction == 'asc' ? 0 : $this->posts - 1;
			}

			if ($mesid == 'last' || $mesid == $this->last_post_id)
			{
				return $direction == 'asc' ? $this->posts - 1 : 0;
			}

			if ($mesid == $this->unread)
			{
				return $direction == 'asc' ? $this->posts - max($this->unread, 1) : 0;
			}
		}

		if ($mesid == 'first')
		{
			$direction = ($direction == 'asc' ? 0 : 'both');
		}

		if ($mesid == 'last')
		{
			$direction = ($direction == 'asc' ? 'both' : 0);
		}

		return MessageHelper::getLocation($mesid, $direction, $hold);
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  User\TopicUser
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getUserInfo($user = null): User\TopicUser
	{
		return TopicUserHelper::get($this->id, $user);
	}

	/**
	 * @return  KunenaUser
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getFirstPostAuthor()
	{
		return KunenaUserHelper::getAuthor($this->first_post_userid, $this->first_post_guest_name);
	}

	/**
	 * @return  KunenaUser
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getLastPostAuthor()
	{
		return KunenaUserHelper::getAuthor($this->last_post_userid, $this->last_post_guest_name);
	}

	/**
	 * @return  KunenaDate
	 *
	 * @since   Kunena 6.0
	 */
	public function getFirstPostTime()
	{
		return new KunenaDate($this->first_post_time);
	}

	/**
	 * @return  KunenaDate
	 *
	 * @since   Kunena 6.0
	 */
	public function getLastPostTime()
	{
		return new KunenaDate($this->last_post_time);
	}

	/**
	 * Resolve/get current topic.
	 *
	 * @return  Topic  Returns this topic or move target if this was moved.
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 */
	public function getTopic()
	{
		$ids   = [];
		$topic = $this;

		// If topic has been moved, find the new topic
		while ($topic->moved_id)
		{
			if (isset($ids[$topic->moved_id]))
			{
				throw new RuntimeException(Text::_('COM_KUNENA_VIEW_TOPIC_ERROR_LOOP'), 500);
			}

			$ids[$topic->moved_id] = 1;
			$topic                 = TopicHelper::get($topic->moved_id);
		}

		return $topic;
	}

	/**
	 * @param   string  $field  field
	 *
	 * @return  integer|string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function displayField($field)
	{
		switch ($field)
		{
			case 'id':
				return intval($this->id);
			case 'subject':
				return Parser::parseText($this->subject);
		}

		return '';
	}

	/**
	 * @param   string  $category_icon  icon
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getIcon($category_icon = '')
	{
		return KunenaFactory::getTemplate()->getTopicIcon($this);
	}

	/**
	 * @param   mixed  $hold  hold
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getReplies($hold = null)
	{
		return (int) max($this->getTotal($hold) - 1, 0);
	}

	/**
	 * @param   mixed  $hold  hold
	 *
	 * @return  integer
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getTotal($hold = null)
	{
		if ($this->moved_id || !KunenaUserHelper::getMyself()->isModerator($this->getCategory()))
		{
			return (int) max($this->posts, 0);
		}

		return MessageHelper::getLocation($this->last_post_id, 'both', $hold) + 1;
	}

	/**
	 * Get permament topic URL without domain.
	 *
	 * If you want to add domain (for email etc), you can prepend the output with this:
	 * Uri::getInstance()->toString(array('scheme', 'host', 'port'))
	 *
	 * @param   Category  $category  category
	 * @param   bool      $xhtml     xhtml
	 * @param   string    $action    action
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getPermaUrl($category = null, $xhtml = true, $action = null)
	{
		return $this->getUrl($category, $xhtml, $action);
	}

	/**
	 * @param   mixed        $category  category
	 * @param   bool         $xhtml     xhtml
	 * @param   null|string  $action    action
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function getUrl($category = null, $xhtml = true, $action = null)
	{
		return KunenaRoute::getTopicUrl(
			$this, $xhtml, $action,
			$category ? CategoryHelper::get($category) : $this->getCategory()
		);
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
	 * @param   array|bool  $fields      fields
	 * @param   mixed       $user        user
	 * @param   array|void  $safefields  safefields
	 *
	 * @return  Message
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 */
	public function newReply($fields = [], $user = null, $safefields = null)
	{
		$user     = KunenaUserHelper::get($user);
		$category = $this->getCategory();

		$message = new Message;
		$message->setTopic($this);
		$message->parent  = $this->first_post_id;
		$message->thread  = $this->id;
		$message->catid   = $this->category_id;
		$message->name    = $user->getName('');
		$message->userid  = $user->userid;
		$message->subject = $this->subject;
		$message->ip      = !empty(KunenaUserHelper::getUserIp()) ? KunenaUserHelper::getUserIp() : '';

		if ($this->hold)
		{
			// If topic was unapproved or deleted, use the same state for the new message
			$message->hold = $this->hold;
		}
		else
		{
			// Otherwise message is either unapproved or published depending if the category is moderated or not
			$message->hold = $category->review ? (int) !$category->isAuthorised('moderate', $user) : 0;
		}

		if ($fields === true)
		{
			$user             = KunenaUserHelper::get($this->first_post_userid);
			$text             = preg_replace('/\[confidential\](.*?)\[\/confidential\]/su', '', $this->first_post_message);
			$message->message = "[quote=\"{$user->getName($this->first_post_guest_name)}\" post={$this->first_post_id}]" . $text . "[/quote]";
		}
		else
		{
			if ($safefields)
			{
				$message->bind($safefields);
			}

			if ($fields)
			{
				$message->bind($fields, ['name', 'email', 'subject', 'message'], true);
			}
		}

		return $message;
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
	public function hasNew($user = null)
	{
		$user = KunenaUserHelper::get($user);

		if (!KunenaFactory::getConfig()->shownew || !$user->exists())
		{
			return false;
		}

		$session = KunenaFactory::getSession();

		if ($this->last_post_time <= $session->getAllReadTime())
		{
			return false;
		}

		$userinfo = CategoryUserHelper::get($this->getCategory(), $user);

		if ($userinfo->allreadtime && $this->last_post_time <= $userinfo->allreadtime)
		{
			return false;
		}

		$read = TopicUserReadHelper::get($this, $user);

		if ($this->last_post_time <= $read->time)
		{
			return false;
		}

		return true;
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
	public function markRead($user = null)
	{
		$user = KunenaUserHelper::get($user);

		if (!KunenaFactory::getConfig()->shownew || !$user->exists() || Factory::getApplication()->getIdentity()->guest)
		{
			return false;
		}

		$read             = TopicUserReadHelper::get($this, $user);
		$read->time       = Factory::getDate()->toUnix();
		$read->message_id = $this->last_post_id;
		$read->save();

		return true;
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
	 * @param   bool        $throw   throw
	 *
	 * @return  boolean
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
			if (!isset($this->_authccache[$user->userid][$action]))
			{
				$this->_authccache[$user->userid][$action] = $this->getCategory()->tryAuthorise('topic.' . $action, $user, false);
			}

			$this->_authcache[$user->userid][$action] = $this->_authccache[$user->userid][$action];

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
	 * Method to load a \Kunena\Forum\Libraries\Forum\Topic\Topic object by id.
	 *
	 * @param   null  $id  The topic id to be loaded.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function load($id = null)
	{
		$exists       = parent::load($id);
		$this->_hold  = $this->hold === null ? 1 : $this->hold;
		$this->_posts = $this->posts;

		return $exists;
	}

	/**
	 * Move topic or parts of it into another category or topic.
	 *
	 * @param   object  $target        Target \Kunena\Forum\Libraries\Forum\Category\Category or
	 *                                 \Kunena\Forum\Libraries\Forum\Topic\Topic
	 * @param   mixed   $ids           false, array of message Ids or Joomla\CMS\Date\Date
	 * @param   bool    $shadow        Leave visible shadow topic.
	 * @param   string  $subject       New subject
	 * @param   bool    $subjectall    Change subject from every message
	 * @param   int     $topic_iconid  Define a new topic icon
	 * @param   int     $keep_poll     Define if you want keep the poll to the original topic or to the splitted topic
	 *
	 * @return  boolean|Category|Topic    Target \Kunena\Forum\Libraries\Forum\Category\Category or
	 *                                    \Kunena\Forum\Libraries\Forum\Topic\Topic or false on failure
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function move($target, $ids = false, $shadow = false, $subject = '', $subjectall = false, $topic_iconid = null, $keep_poll = 0)
	{
		// Warning: logic in this function is very complicated and even with full understanding its easy to miss some details!

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = [];

		// Cleanup input
		if (!($ids instanceof Date))
		{
			if (!is_array($ids))
			{
				$ids = explode(',', (string) $ids);
			}

			$mesids = [];

			foreach ($ids as $id)
			{
				$mesids[(int) $id] = (int) $id;
			}

			unset($mesids[0]);
			$ids = implode(',', $mesids);
		}

		$subject = (string) $subject;

		// First we need to check if there will be messages left in the old topic
		if ($ids)
		{
			$query = $this->_db->getQuery(true);
			$query->select('COUNT(*)')
				->from($this->_db->quoteName('#__kunena_messages'))
				->where($this->_db->quoteName('thread') . ' = ' . $this->_db->quote($this->id));

			if ($ids instanceof Date)
			{
				// All older messages will remain (including unapproved, deleted)
				$query->where($this->_db->quoteName('time') . ' < ' . $this->_db->quote($ids->toUnix()));
			}
			else
			{
				// All messages that were not selected will remain
				$query->where($this->_db->quoteName('id') . ' NOT IN (' . $ids . ')');
			}

			$this->_db->setQuery($query);

			try
			{
				$oldcount = (int) $this->_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				$app = Factory::getApplication();
				$app->enqueueMessage($e->getMessage());
			}

			// So are we moving the whole topic?
			if (!$oldcount)
			{
				$ids = '';
			}
		}

		$categoryFrom = $this->getCategory();

		// Find out where we are moving the messages
		if (!$target || !$target->exists())
		{
			$this->setError(Text::sprintf('COM_KUNENA_MODERATION_ERROR_NO_TARGET', $this->id));

			return false;
		}
		elseif ($target instanceof Topic)
		{
			// Move messages into another topic (original topic will always remain, either as real one or shadow)

			if ($target == $this)
			{
				// We cannot move topic into itself
				$this->setError(Text::sprintf('COM_KUNENA_MODERATION_ERROR_SAME_TARGET_THREAD', $this->id, $this->id));

				return false;
			}

			if ($this->moved_id)
			{
				// Moved topic cannot be merged with another topic -- it has no posts to be moved
				$this->setError(Text::sprintf('COM_KUNENA_MODERATION_ERROR_ALREADY_SHADOW', $this->id));

				return false;
			}

			if ($this->poll_id && $target->poll_id)
			{
				// We cannot currently have 2 polls in one topic -- fail
				$this->setError(Text::_('COM_KUNENA_MODERATION_CANNOT_MOVE_TOPIC_WITH_POLL_INTO_ANOTHER_WITH_POLL'));

				return false;
			}

			if ($subjectall)
			{
				$subject = $target->subject;
			}
		}
		elseif ($target instanceof Category)
		{
			// Move messages into category

			if ($target->isSection())
			{
				// Section cannot have any topics
				$this->setError(Text::_('COM_KUNENA_MODERATION_ERROR_NOT_MOVE_SECTION'));

				return false;
			}

			// Save category information for later use
			$categoryTarget = $target;

			if ($this->moved_id)
			{
				// Move shadow topic and we are done
				$this->category_id = $categoryTarget->id;

				if ($subject)
				{
					$this->subject = $subject;
				}

				$this->save(false);

				return $target;
			}

			if ($shadow || $ids)
			{
				// Create new topic for the moved messages
				$target = clone $this;
				$target->exists(false);
				$target->id     = 0;
				$target->hits   = 0;
				$target->params = '';
			}
			else
			{
				// If we just move into another category, we can keep using the old topic
				$target = $this;
			}

			// Did user want to change the subject?
			if ($subject)
			{
				$target->subject = $subject;
			}

			// Did user want to change the topic icon?
			if ($topic_iconid !== null)
			{
				$target->icon_id = $topic_iconid;
			}

			// Did user want to change category?
			$target->category_id = $categoryTarget->id;
		}
		else
		{
			$this->setError(Text::_('COM_KUNENA_MODERATION_ERROR_WRONG_TARGET'));

			return false;
		}

		// For now on we assume that at least one message will be moved (=authorization check was called on topic/message)

		// We will soon need target topic id, so save if it doesn't exist
		if (!$target->exists())
		{
			if (!$target->save(false))
			{
				$this->setError($target->getError());

				return false;
			}
		}

		// Move messages (set new category and topic)

		$query = $this->_db->getQuery(true);
		$query->update($this->_db->quoteName('#__kunena_messages'))
			->set($this->_db->quoteName('catid') . ' = ' . $this->_db->quote($target->category_id))
			->set($this->_db->quoteName('thread') . ' = ' . $this->_db->quote($target->id))
			->where($this->_db->quoteName('thread') . ' = ' . $this->_db->quote($this->id));

		// Did we want to change subject from all the messages?
		if ($subjectall && !empty($subject))
		{
			$query->set($this->_db->quoteName('subject') . ' = ' . $this->_db->quote($subject));
		}

		if ($ids instanceof Date)
		{
			// Move all newer messages (includes unapproved, deleted messages)
			$query->where($this->_db->quoteName('time') . ' >= ' . $this->_db->quote($ids->toUnix()));
		}
		elseif ($ids)
		{
			// Move individual messages
			$query->where($this->_db->quoteName('id') . ' IN (' . $ids . ')');
		}

		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage($query);
		}

		// Make sure that all messages in topic have unique time (deterministic without ORDER BY time, id)
		$query = "SET @ktime:=0";
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage($e->getMessage());
		}

		$query = 'UPDATE ' . $this->_db->quoteName('#__kunena_messages') . ' SET ' . $this->_db->quoteName('time') . ' = IF(time <= @ktime,@ktime:=@ktime+1,@ktime:=time) WHERE thread=' . $target->id . ' ORDER BY time ASC, id ASC';
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage($e->getMessage());
		}

		if ($keep_poll)
		{
			$target->poll_id = 0;
		}
		else
		{
			// If all messages were moved into another topic, we need to move poll as well
			if ($this->poll_id && !$ids && $target != $this)
			{
				// Note: We may already have saved cloned target (having poll_id already in there)
				$target->poll_id = $this->poll_id;

				// Note: Do not remove poll from shadow: information could still be used to show icon etc
				$query = $this->_db->getQuery(true);
				$query->update($this->_db->quoteName('#__kunena_polls'))
					->set($this->_db->quoteName('threadid') . ' = ' . $this->_db->quote($target->id))
					->where($this->_db->quoteName('threadid') . ' = ' . $this->_db->quote($this->id));

				$this->_db->setQuery($query);

				try
				{
					$this->_db->execute();
				}
				catch (RuntimeException $e)
				{
					$app = Factory::getApplication();
					$app->enqueueMessage($e->getMessage());
				}
			}

			// When moving only first message keep poll only on target topic
			if ($this->poll_id && $target != $this && $ids)
			{
				if ($ids && $this->first_post_id)
				{
					$this->poll_id = 0;
				}
			}
		}

		if (!$ids && $target != $this)
		{
			// Leave shadow from old topic
			$this->moved_id = $target->id;

			if (!$shadow)
			{
				// Mark shadow topic as deleted
				$this->hold = 2;
			}
		}

		// Note: We already saved possible target earlier, now save only $this
		if (!$this->save(false))
		{
			return false;
		}

		if (!$ids && !empty($categoryTarget))
		{
			// Move topic into another category

			// Update user topic information (topic, category)
			TopicUserHelper::move($this, $target);

			// TODO: do we need this?
			// \Kunena\Forum\Libraries\Forum\Topic\\Kunena\Forum\Libraries\Forum\Topic\User\Read\Helper::move($this, $target);
			// Remove topic and posts from the old category
			$categoryFrom->update($this, -1, -$this->posts);

			// Add topic and posts into the new category
			$categoryTarget->update($target, 1, $this->posts);
		}
		elseif (!$ids)
		{
			// Moving topic into another topic

			// Add new posts, hits and attachments into the target topic
			$target->posts       += $this->posts;
			$target->hits        += $this->hits;
			$target->attachments += $this->attachments;

			// Update first and last post information into the target topic
			$target->updatePostInfo(
				$this->first_post_id, $this->first_post_time, $this->first_post_userid,
				$this->first_post_message, $this->first_post_guest_name
			);
			$target->updatePostInfo(
				$this->last_post_id, $this->last_post_time, $this->last_post_userid,
				$this->last_post_message, $this->last_post_guest_name
			);

			// Save target topic
			if (!$target->save(false))
			{
				$this->setError($target->getError());

				return false;
			}

			// Update user topic information (topic, category)
			TopicUserHelper::merge($this, $target);

			// TODO: do we need this?
			// \Kunena\Forum\Libraries\Forum\Topic\\Kunena\Forum\Libraries\Forum\Topic\User\Read\Helper::merge($this, $target);
			// Remove topic and posts from the old category
			$this->getCategory()->update($this, -1, -$this->posts);

			// Add posts into the new category
			$target->getCategory()->update($target, 0, $this->posts);
		}
		else
		{
			// Both topics have changed and we have no idea how much: force full recount
			// TODO: we can do this faster..
			$this->recount();
			$target->recount();
		}

		return $target;
	}

	/**
	 * Method to put the \Kunena\Forum\Libraries\Forum\Topic\Topic object on trash this is still present in database.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function trash()
	{
		if (!$this->exists())
		{
			return true;
		}

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = [];

		$db        = Factory::getDBO();
		$queries[] = "UPDATE #__kunena_messages SET hold='2' WHERE thread={$db->quote($this->id)}";
		$queries[] = "UPDATE #__kunena_topics SET hold='2' WHERE id={$db->quote($this->id)}";

		foreach ($queries as $query)
		{
			$db->setQuery($query);

			try
			{
				$this->_db->execute();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}
		}

		return true;
	}

	/**
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function resetvotes()
	{
		if (!$this->poll_id)
		{
			return false;
		}

		$query = $this->_db->getQuery(true);
		$query->update($this->_db->quoteName('#__kunena_polls_options'))
			->set($this->_db->quoteName('votes') . ' = 0')
			->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->poll_id));
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$query = $this->_db->getQuery(true);
		$query->delete($this->_db->quoteName('#__kunena_polls_users'))
			->where($this->_db->quoteName('pollid') . ' = ' . $this->_db->quote($this->poll_id));
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return true;
	}

	/**
	 * Return the number of rating given to the topic
	 *
	 * @return  integer
	 *
	 * @since   Kunena 5.1.3
	 */
	public function getReviewCount()
	{
		return RateHelper::getCount($this->id);
	}

	/**
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
	 *
	 * @since   Kunena 6.0
	 */
	protected function authoriseNotExists(KunenaUser $user)
	{
		// Check that topic does not exist
		if ($this->_exists)
		{
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
	protected function authoriseRead(KunenaUser $user)
	{
		// Check that user can read topic
		if (!$this->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		// TODO: Allow owner to see his posts.
		if ($this->hold)
		{
			if (!$user->exists())
			{
				return new Authorise(Text::_('COM_KUNENA_NO_ACCESS'), 401);
			}

			$access = Access::getInstance();
			$hold   = $access->getAllowedHold($user->userid, $this->category_id, false);

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
		// Check that topic is not unapproved or deleted
		if ($this->hold)
		{
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
	 */
	protected function authoriseNotMoved(KunenaUser $user)
	{
		// Check that topic is not moved
		if ($this->moved_id)
		{
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
	protected function authoriseUnlocked(KunenaUser $user)
	{
		// Check that topic is not locked or user is a moderator
		if ($this->locked && !$user->isModerator($this->getCategory()))
		{
			return new Authorise(Text::_('COM_KUNENA_POST_ERROR_TOPIC_LOCKED'), 403);
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
		// Guests cannot own a topic.
		if (!$user->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_POST_NOT_MODERATOR'), 401);
		}

		// Check that topic owned by the user or user is a moderator
		$usertopic = $this->getUserTopic($user);

		if (!$usertopic->owner && !$user->isModerator($this->getCategory()))
		{
			return new Authorise(Text::_('COM_KUNENA_POST_NOT_MODERATOR'), 403);
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
	protected function authorisePoll(KunenaUser $user)
	{
		// Check that user can vote
		$poll = $this->getPoll();

		if (!$poll->exists())
		{
			return new Authorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_NO_POLL'), 404);
		}

		return;
	}

	/**
	 * @return  Poll
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getPoll()
	{
		$poll           = PollHelper::get($this->poll_id);
		$poll->threadid = $this->id;

		return $poll;
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
	protected function authoriseVote(KunenaUser $user)
	{
		// Check that user can vote
		$config = KunenaFactory::getConfig();
		$poll   = $this->getPoll();
		$votes  = $poll->getMyVotes($user);

		if (!$config->pollallowvoteone && $votes)
		{
			$time_zone   = CMSApplication::getInstance('site')->get('offset');
			$objTimeZone = new DateTimeZone($time_zone);

			// Check the time between two votes
			$date_a = new DateTime($poll->getMyTime(), $objTimeZone);
			$date_b = new DateTime('now', $objTimeZone);

			$interval = date_diff($date_a, $date_b);

			if ($interval->format('%H:%I:%S') < $config->polltimebtvotes)
			{
				return new Authorise(Text::_('COM_KUNENA_TOPIC_VOTE_NEED_TO_WAIT_BEFORE_TO_CHANGE_VOTE'), 403);
			}
		}

		if ($votes && $config->pollallowvoteone)
		{
			return new Authorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_ONLY_ONCE'), 403);
		}

		if ($votes >= $config->pollnbvotesbyuser && $config->pollallowvoteone)
		{
			return new Authorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_TOO_MANY_TIMES'), 403);
		}

		if ($config->polltimebtvotes && (int) $poll->getMyTime($user) + (int) $config->polltimebtvotes > Factory::getDate()->toUnix())
		{
			return new Authorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_TOO_EARLY'), 403);
		}

		if ($this->locked)
		{
			return new Authorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_POLL_TOPIC_LOCKED'), 403);
		}

		if ($poll->polltimetolive != '1000-01-01 00:00:00' && $poll->getTimeToLive() < Factory::getDate()->toUnix())
		{
			return new Authorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_POLL_EXPIRED'), 403);
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
	protected function authoriseNoVotes(KunenaUser $user)
	{
		$poll   = $this->getPoll();
		$config = KunenaFactory::getConfig();

		if ($poll->exists() && $poll->getUserCount() && !$config->allow_edit_poll)
		{
			return new Authorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_ONGOING_POLL'), 403);
		}

		return;
	}

	/**
	 * Check if user has the right to perm delete the message
	 *
	 * @param   KunenaUser  $user  user
	 *
	 * @return  Authorise|void
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

		if ($user->isModerator($this->getCategory()) && !$config->moderator_permdelete || !$user->isModerator($this->getCategory()))
		{
			return new Authorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
		}

		return null;
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
