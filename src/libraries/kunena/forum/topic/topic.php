<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Class KunenaForumTopic
 *
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
 * @since Kunena
 */
class KunenaForumTopic extends KunenaDatabaseObject
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected static $actions = array(
		'none'                        => array(),
		'read'                        => array('Read'),
		'create'                      => array('NotExists', 'GuestWrite'),
		'reply'                       => array('Read', 'NotHold', 'GuestWrite', 'NotMoved', 'Unlocked'),
		'edit'                        => array('Read', 'NotMoved', 'Unlocked', 'Own'),
		'move'                        => array('Read'),
		'approve'                     => array('Read', 'NotMoved'),
		'delete'                      => array('Read'),
		'undelete'                    => array('Read'),
		'permdelete'                  => array('Read', 'Permdelete'),
		'favorite'                    => array('Read'),
		'subscribe'                   => array('Read'),
		'sticky'                      => array('Read'),
		'lock'                        => array('Read'),
		'rate'                        => array('Read', 'Unlocked'),
		'poll.read'                   => array('Read', 'Poll'),
		'poll.create'                 => array('Own'),
		'poll.edit'                   => array('Read', 'NoVotes'),
		'poll.delete'                 => array('Read', 'Own', 'Poll'),
		'poll.vote'                   => array('Read', 'Poll', 'Vote'),
		'post.read'                   => array('Read'),
		'post.thankyou'               => array('Read', 'NotMoved', 'Unlocked'),
		'post.unthankyou'             => array('Read', 'Unlocked'),
		'post.reply'                  => array('Read', 'NotHold', 'GuestWrite', 'NotMoved', 'Unlocked'),
		'post.edit'                   => array('Read', 'Unlocked'),
		'post.move'                   => array('Read'),
		'post.approve'                => array('Read'),
		'post.delete'                 => array('Read', 'Unlocked'),
		'post.undelete'               => array('Read'),
		'post.permdelete'             => array('Read', 'Permdelete'),
		'post.attachment.read'        => array('Read'),
		'post.attachment.createimage' => array('Unlocked'),
		'post.attachment.createfile'  => array('Unlocked'),
		'post.attachment.delete'      => array(),
		// TODO: In the future we might want to restrict this: array('Read','Unlocked'),
	);

	/**
	 * @var integer
	 * @since Kunena
	 */
	public $id = null;

	/**
	 * @var integer
	 * @since Kunena
	 */
	public $unread = 0;

	/**
	 * @var integer
	 * @since Kunena
	 */
	public $lastread = 0;

	/**
	 * @var string
	 * @since Kunena
	 */
	protected $_table = 'KunenaTopics';

	/**
	 * @var JDatabaseDriver|null
	 * @since Kunena
	 */
	protected $_db = null;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $_authcache = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $_authccache = array();

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $_authfcache = array();

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $_hold = 1;

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $_posts = 0;

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $_pagination = null;

	/**
	 * @param   mixed $properties properties
	 *
	 * @internal
	 * @since Kunena
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
	 * Returns KunenaForumTopic object.
	 *
	 * @param   int  $identifier The topic to load - Can be only an integer.
	 * @param   bool $reset      reset
	 *
	 * @return KunenaForumTopic
	 * @since Kunena
	 */
	public static function getInstance($identifier = null, $reset = false)
	{
		return KunenaForumTopicHelper::get($identifier, $reset);
	}

	/**
	 * Subscribe / Unsubscribe user to this topic.
	 *
	 * @param   bool  $value True for subscribe, false for unsubscribe.
	 * @param   mixed $user  user
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function subscribe($value = true, $user = null)
	{
		$usertopic             = $this->getUserTopic($user);
		$usertopic->subscribed = (int) $value;

		if (!$usertopic->save())
		{
			$this->setError($usertopic->getError());

			return false;
		}

		return true;
	}

	/**
	 * @param   mixed $user user
	 *
	 * @return KunenaForumTopicUser
	 * @since Kunena
	 * @throws Exception
	 */
	public function getUserTopic($user = null)
	{
		$usertopic = KunenaForumTopicUserHelper::get($this, $user);

		return $usertopic;
	}

	/**
	 * Favorite / unfavorite user to this topic.
	 *
	 * @param   bool  $value True for favorite, false for unfavorite.
	 * @param   mixed $user  user
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
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
	 * @param   int $value values
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function sticky($value = 1)
	{
		$this->ordering = (int) $value;

		return $this->save();
	}

	/**
	 * Method to save the KunenaForumTopic object to the database.
	 *
	 * @param   bool $cascade cascade
	 *
	 * @return boolean    True on success.
	 * @throws Exception
	 * @since Kunena
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
		$this->_authfcache = $this->_authccache = $this->_authcache = array();

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
	 * @return integer
	 * @since Kunena
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
	 * @param   null|bool $exists exists
	 *
	 * @return boolean
	 * @since Kunena
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
	 * @return KunenaForumCategory
	 * @since Kunena
	 */
	public function getCategory()
	{
		return KunenaForumCategoryHelper::get($this->category_id);
	}

	/**
	 * @return string
	 * @since Kunena
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
	 * @param   int $value value
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function lock($value = 1)
	{
		$this->locked = (int) $value;

		return $this->save();
	}

	/**
	 * @param   mixed       $user user
	 * @param   bool|string $glue glue
	 *
	 * @return void
	 * @since Kunena
	 */
	public function getKeywords($user = null, $glue = false)
	{
		return;
	}

	/**
	 * @param   int $value value
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function publish($value = KunenaForum::PUBLISHED)
	{
		if ($value < 0 || $value > 3)
		{
			$value = 0;
		}

		$this->hold = (int) $value;
		$query      = new KunenaDatabaseQuery;
		$query->update('#__kunena_messages')->set("hold={$this->hold}")
			->where("thread={$this->id}")->where("hold={$this->_hold}");

		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return $this->_db->getAffectedRows() ? $this->recount() : $this->save();
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function recount()
	{
		if (!$this->moved_id)
		{
			// Recount total posts and attachments
			$query = "SELECT COUNT(DISTINCT m.id) AS posts, COUNT(a.id) AS attachments
					FROM #__kunena_messages AS m
					LEFT JOIN #__kunena_attachments AS a ON m.id=a.mesid
					WHERE m.hold={$this->_db->quote($this->hold)} AND m.thread={$this->_db->quote($this->id)}
					GROUP BY m.thread";
			$this->_db->setQuery($query);

			try
			{
				$result = $this->_db->loadAssoc();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}

			if (!$result)
			{
				$this->posts = 0;

				// Double check if all posts have been removed from the database
				$query = "SELECT COUNT(m.id) AS posts, MIN(m.hold) AS hold
						FROM #__kunena_messages AS m
						WHERE m.thread={$this->_db->quote($this->id)}
						GROUP BY m.thread";
				$this->_db->setQuery($query);

				try
				{
					$result = $this->_db->loadAssoc();
				}
				catch (JDatabaseExceptionExecuting $e)
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
	 * @param   KunenaForumMessage $message   message
	 * @param   int                $postdelta postdelta
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
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
				$query = "SELECT * FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
					WHERE m.thread={$db->quote($this->id)} AND m.hold={$this->hold} ORDER BY m.time ASC, m.id ASC";
				$db->setQuery($query, 0, 1);

				try
				{
					$first = $db->loadObject();
				}
				catch (JDatabaseExceptionExecuting $e)
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
				$query = "SELECT * FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON t.mesid=m.id
					WHERE m.thread={$db->quote($this->id)} AND m.hold={$this->hold} ORDER BY m.time DESC, m.id DESC";
				$db->setQuery($query, 0, 1);

				try
				{
					$last = $db->loadObject();
				}
				catch (JDatabaseExceptionExecuting $e)
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
			KunenaForumTopicUserHelper::recount($this->id);

			// FIXME: optimize
			KunenaUserHelper::recount();
		}

		return true;
	}

	/**
	 * @param   int    $id      id
	 * @param   int    $time    time
	 * @param   int    $userid  userid
	 * @param   string $message message
	 * @param   string $name    name
	 *
	 * @since Kunena
	 * @return void
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
	 * Method to delete the KunenaForumTopic object from the database.
	 *
	 * @param   bool $recount recount
	 *
	 * @return boolean    True on success.
	 * @throws Exception
	 * @since Kunena
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
		$this->_authfcache = $this->_authccache = $this->_authcache = array();

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
				catch (JDatabaseExceptionExecuting $e)
				{
					KunenaError::displayDatabaseError($e);

					return false;
				}
			}

			// FIXME: add recount statistics
			if ($recount)
			{
				KunenaUserHelper::recount();
				KunenaForumMessageThankyouHelper::recount();
			}
		}

		return true;
	}

	/**
	 * Send email notifications from the first post in the topic.
	 *
	 * @param   null|string $url url
	 * @param   boolean     $approved false
	 *
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 * @return void
	 */
	public function sendNotification($url = null, $approved = false)
	{
		// Reload message just in case if it was published by bulk update.
		KunenaForumMessageHelper::get($this->first_post_id, true)->sendNotification($url, $approved);
	}

	/**
	 * @return KunenaUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getAuthor()
	{
		return KunenaUserHelper::getAuthor($this->first_post_userid, $this->first_post_guest_name);
	}

	/**
	 * @return integer
	 * @since Kunena
	 */
	public function getHits()
	{
		return $this->hits;
	}

	/**
	 * Increase hit counter for this topic.
	 * @since Kunena
	 * @throws Exception
	 * @return void
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
	 * @param   int|null $limitstart Null if all pages need to be active.
	 * @param   int      $limit      limit
	 * @param   int      $display    display
	 * @param   string   $prefix     prefix
	 *
	 * @return \Joomla\CMS\Pagination\Pagination|KunenaPagination
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getPagination($limitstart = 0, $limit = 6, $display = 4, $prefix = '')
	{
		if (!$this->_pagination)
		{
			$this->_pagination = new KunenaPagination($this->posts, $limitstart, $limit, $prefix);
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
	 * @param   mixed  $category category
	 * @param   string $action   action
	 *
	 * @return \Joomla\CMS\Uri\Uri|null
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getUri($category = null, $action = null)
	{
		$category = $category ? KunenaForumCategoryHelper::get($category) : $this->getCategory();
		$Itemid   = KunenaRoute::getCategoryItemid($category);

		if (!$this->exists() || !$category->exists())
		{
			return;
		}

		if ($action instanceof KunenaForumMessage)
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
				$uri->setFragment($mesid);
				$limitstart = intval($this->getPostLocation($mesid) / $limit) * $limit;

				if ($limitstart)
				{
					$uri->setVar('limitstart', $limitstart);
				}
			}
		}

		return $uri;
	}

	/**
	 * @param   int         $mesid     mesid
	 * @param   string|null $direction direction
	 * @param   mixed       $hold      hold
	 *
	 * @return integer
	 * @throws Exception
	 * @since Kunena
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

		return KunenaForumMessageHelper::getLocation($mesid, $direction, $hold);
	}

	/**
	 * @param   mixed $user user
	 *
	 * @return KunenaForumTopicUser
	 * @since Kunena
	 * @throws Exception
	 */
	public function getUserInfo($user = null)
	{
		return KunenaForumTopicUserHelper::get($this->id, $user);
	}

	/**
	 * @return KunenaUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getFirstPostAuthor()
	{
		return KunenaUserHelper::getAuthor($this->first_post_userid, $this->first_post_guest_name);
	}

	/**
	 * @return KunenaUser
	 * @throws Exception
	 * @since Kunena
	 */
	public function getLastPostAuthor()
	{
		return KunenaUserHelper::getAuthor($this->last_post_userid, $this->last_post_guest_name);
	}

	/**
	 * @return KunenaDate
	 * @since Kunena
	 */
	public function getFirstPostTime()
	{
		return new KunenaDate($this->first_post_time);
	}

	/**
	 * @return KunenaDate
	 * @since Kunena
	 */
	public function getLastPostTime()
	{
		return new KunenaDate($this->last_post_time);
	}

	/**
	 * Resolve/get current topic.
	 *
	 * @return  KunenaForumTopic  Returns this topic or move target if this was moved.
	 *
	 * @throws  RuntimeException  If there is a redirect loop on moved_id.
	 *
	 * @since  K4.0
	 */
	public function getTopic()
	{
		$ids   = array();
		$topic = $this;

		// If topic has been moved, find the new topic
		while ($topic->moved_id)
		{
			if (isset($ids[$topic->moved_id]))
			{
				throw new RuntimeException(Text::_('COM_KUNENA_VIEW_TOPIC_ERROR_LOOP'), 500);
			}

			$ids[$topic->moved_id] = 1;
			$topic                 = KunenaForumTopicHelper::get($topic->moved_id);
		}

		return $topic;
	}

	/**
	 * @param   string $field field
	 *
	 * @return integer|string
	 * @throws Exception
	 * @since Kunena
	 */
	public function displayField($field)
	{
		switch ($field)
		{
			case 'id':
				return intval($this->id);
			case 'subject':
				return KunenaHtmlParser::parseText($this->subject);
		}

		return '';
	}

	/**
	 * @param   string $category_icon icon
	 *
	 * @return string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getIcon($category_icon = '')
	{
		return KunenaFactory::getTemplate()->getTopicIcon($this);
	}

	/**
	 * @param   mixed $hold hold
	 *
	 * @return integer
	 * @throws Exception
	 * @since Kunena
	 */
	public function getReplies($hold = null)
	{
		return (int) max($this->getTotal($hold) - 1, 0);
	}

	/**
	 * @param   mixed $hold hold
	 *
	 * @return integer
	 * @throws Exception
	 * @since Kunena
	 */
	public function getTotal($hold = null)
	{
		if ($this->moved_id || !KunenaUserHelper::getMyself()->isModerator($this->getCategory()))
		{
			return (int) max($this->posts, 0);
		}

		return KunenaForumMessageHelper::getLocation($this->last_post_id, 'both', $hold) + 1;
	}

	/**
	 * Get permament topic URL without domain.
	 *
	 * If you want to add domain (for email etc), you can prepend the output with this:
	 * Uri::getInstance()->toString(array('scheme', 'host', 'port'))
	 *
	 * @param   KunenaForumCategory $category category
	 * @param   bool                $xhtml    xhtml
	 * @param   string              $action   action
	 *
	 * @return boolean
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function getPermaUrl($category = null, $xhtml = true, $action = null)
	{
		return $this->getUrl($category, $xhtml, $action);
	}

	/**
	 * @param   mixed       $category category
	 * @param   bool        $xhtml    xhtml
	 * @param   null|string $action   action
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getUrl($category = null, $xhtml = true, $action = null)
	{
		return KunenaRoute::getTopicUrl(
			$this, $xhtml, $action,
			$category ? KunenaForumCategoryHelper::get($category) : $this->getCategory()
		);
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
	 * @param   array|bool $fields     fields
	 * @param   mixed      $user       user
	 * @param   array|null $safefields safefields
	 *
	 * @return KunenaForumMessage
	 * @throws null
	 * @since Kunena
	 */
	public function newReply($fields = array(), $user = null, $safefields = null)
	{
		$user     = KunenaUserHelper::get($user);
		$category = $this->getCategory();

		$message = new KunenaForumMessage;
		$message->setTopic($this);
		$message->parent  = $this->first_post_id;
		$message->thread  = $this->id;
		$message->catid   = $this->category_id;
		$message->name    = KunenaFactory::getProfile()->getprofileName($user, '');
		$message->userid  = $user->userid;
		$message->subject = $this->subject;
		$message->ip      = !empty($_SERVER ["REMOTE_ADDR"]) ? $_SERVER ["REMOTE_ADDR"] : '';

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
			$message->message = "[quote=\"{KunenaFactory::getProfile()->getprofileName($user, $this->first_post_guest_name)}\" post={$this->first_post_id}]" . $text . "[/quote]";
		}
		else
		{
			if ($safefields)
			{
				$message->bind($safefields);
			}

			if ($fields)
			{
				$message->bind($fields, array('name', 'email', 'subject', 'message'), true);
			}
		}

		return $message;
	}

	/**
	 * @param   mixed $user user
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
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

		$userinfo = KunenaForumCategoryUserHelper::get($this->getCategory(), $user);

		if ($userinfo->allreadtime && $this->last_post_time <= $userinfo->allreadtime)
		{
			return false;
		}

		$read = KunenaForumTopicUserReadHelper::get($this, $user);

		if ($this->last_post_time <= $read->time)
		{
			return false;
		}

		return true;
	}

	/**
	 * @deprecated
	 *
	 * @param   null $user user
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function markNew($user = null)
	{
		return $this->markRead($user);
	}

	/**
	 * @param   mixed $user user
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function markRead($user = null)
	{
		$user = KunenaUserHelper::get($user);

		if (!KunenaFactory::getConfig()->shownew || !$user->exists() || Factory::getUser()->guest)
		{
			return false;
		}

		$read             = KunenaForumTopicUserReadHelper::get($this, $user);
		$read->time       = Factory::getDate()->toUnix();
		$read->message_id = $this->last_post_id;
		$read->save();

		return true;
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
	 * @param   bool       $throw  throw
	 *
	 * @return boolean
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
	 * Method to load a KunenaForumTopic object by id.
	 *
	 * @param   null $id The topic id to be loaded.
	 *
	 * @return boolean    True on success.
	 * @since Kunena
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
	 * @param   object $target       Target KunenaForumCategory or KunenaForumTopic
	 * @param   mixed  $ids          false, array of message Ids or \Joomla\CMS\Date\Date
	 * @param   bool   $shadow       Leave visible shadow topic.
	 * @param   string $subject      New subject
	 * @param   bool   $subjectall   Change subject from every message
	 * @param   int    $topic_iconid Define a new topic icon
	 * @param   int    $keep_poll    Define if you want keep the poll to the original topic or to the splitted topic
	 *
	 * @return    boolean|KunenaForumCategory|KunenaForumTopic    Target KunenaForumCategory or KunenaForumTopic or
	 *                                                            false on failure
	 * @throws Exception
	 * @since Kunena
	 */
	public function move($target, $ids = false, $shadow = false, $subject = '', $subjectall = false, $topic_iconid = null, $keep_poll = 0)
	{
		// Warning: logic in this function is very complicated and even with full understanding its easy to miss some details!

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = array();

		// Cleanup input
		if (!($ids instanceof \Joomla\CMS\Date\Date))
		{
			if (!is_array($ids))
			{
				$ids = explode(',', (string) $ids);
			}

			$mesids = array();

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
			$query = new KunenaDatabaseQuery;
			$query->select('COUNT(*)')->from('#__kunena_messages')->where("thread={$this->id}");

			if ($ids instanceof \Joomla\CMS\Date\Date)
			{
				// All older messages will remain (including unapproved, deleted)
				$query->where("time<{$ids->toUnix()}");
			}
			else
			{
				// All messages that were not selected will remain
				$query->where("id NOT IN ({$ids})");
			}

			$this->_db->setQuery($query);

			try
			{
				$oldcount = (int) $this->_db->loadResult();
			}
			catch (JDatabaseExceptionExecuting $e)
			{
				throw new RuntimeException($e->getMessage(), $e->getCode());
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
		elseif ($target instanceof KunenaForumTopic)
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
		elseif ($target instanceof KunenaForumCategory)
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

		$query = new KunenaDatabaseQuery;
		$query->update('#__kunena_messages')->set("catid={$target->category_id}")->set("thread={$target->id}")->where("thread={$this->id}");

		// Did we want to change subject from all the messages?
		if ($subjectall && !empty($subject))
		{
			$query->set("subject={$this->_db->quote($subject)}");
		}

		if ($ids instanceof \Joomla\CMS\Date\Date)
		{
			// Move all newer messages (includes unapproved, deleted messages)
			$query->where("time>={$ids->toUnix()}");
		}
		elseif ($ids)
		{
			// Move individual messages
			$query->where("id IN ({$ids})");
		}

		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		// Make sure that all messages in topic have unique time (deterministic without ORDER BY time, id)
		$query = "SET @ktime:=0";
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		$query = "UPDATE #__kunena_messages SET time=IF(time<=@ktime,@ktime:=@ktime+1,@ktime:=time) WHERE thread={$target->id} ORDER BY time ASC, id ASC";
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
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

				$query = "UPDATE #__kunena_polls SET `threadid`={$this->_db->Quote($target->id)} WHERE `threadid`={$this->_db->Quote($this->id)}";
				$this->_db->setQuery($query);

				try
				{
					$this->_db->execute();
				}
				catch (JDatabaseExceptionExecuting $e)
				{
					throw new RuntimeException($e->getMessage(), $e->getCode());
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
			KunenaForumTopicUserHelper::move($this, $target);

			// TODO: do we need this?
			// KunenaForumTopicUserReadHelper::move($this, $target);
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
			KunenaForumTopicUserHelper::merge($this, $target);

			// TODO: do we need this?
			// KunenaForumTopicUserReadHelper::merge($this, $target);
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
	 * Method to put the KunenaForumTopic object on trash this is still present in database.
	 *
	 * @return boolean    True on success.
	 * @throws Exception
	 * @since Kunena
	 */
	public function trash()
	{
		if (!$this->exists())
		{
			return true;
		}

		// Clear authentication cache
		$this->_authfcache = $this->_authccache = $this->_authcache = array();

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
			catch (JDatabaseExceptionExecuting $e)
			{
				KunenaError::displayDatabaseError($e);

				return false;
			}
		}

		return true;
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function resetvotes()
	{
		if (!$this->poll_id)
		{
			return false;
		}

		$query = "UPDATE #__kunena_polls_options SET votes=0 WHERE pollid={$this->_db->quote($this->poll_id)}";
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		$query = "DELETE FROM #__kunena_polls_users WHERE pollid={$this->_db->quote($this->poll_id)}";
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			KunenaError::displayDatabaseError($e);

			return false;
		}

		return true;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @since Kunena
	 */
	protected function authoriseNotExists(KunenaUser $user)
	{
		// Check that topic does not exist
		if ($this->_exists)
		{
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
	protected function authoriseRead(KunenaUser $user)
	{
		// Check that user can read topic
		if (!$this->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		// TODO: Allow owner to see his posts.
		if ($this->hold)
		{
			if (!$user->exists())
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 401);
			}

			$access = KunenaAccess::getInstance();
			$hold   = $access->getAllowedHold($user->userid, $this->category_id, false);

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
		// Check that topic is not unapproved or deleted
		if ($this->hold)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		return;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @since Kunena
	 */
	protected function authoriseNotMoved(KunenaUser $user)
	{
		// Check that topic is not moved
		if ($this->moved_id)
		{
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
	protected function authoriseUnlocked(KunenaUser $user)
	{
		// Check that topic is not locked or user is a moderator
		if ($this->locked && !$user->isModerator($this->getCategory()))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_TOPIC_LOCKED'), 403);
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
		// Guests cannot own a topic.
		if (!$user->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_NOT_MODERATOR'), 401);
		}

		// Check that topic owned by the user or user is a moderator
		$usertopic = $this->getUserTopic($user);

		if (!$usertopic->owner && !$user->isModerator($this->getCategory()))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_NOT_MODERATOR'), 403);
		}

		return;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @since Kunena
	 */
	protected function authorisePoll(KunenaUser $user)
	{
		// Check that user can vote
		$poll = $this->getPoll();

		if (!$poll->exists())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_NO_POLL'), 404);
		}

		return;
	}

	/**
	 * Return the number of rating given to the topic
	 *
	 * @return integer
	 * @since Kunena 5.1.3
	 *
	 */
	public function getReviewCount()
	{
		return KunenaForumTopicRateHelper::getCount($this->id);
	}

	/**
	 * @return KunenaForumTopicPoll
	 * @since Kunena
	 */
	public function getPoll()
	{
		$poll           = KunenaForumTopicPollHelper::get($this->poll_id);
		$poll->threadid = $this->id;

		return $poll;
	}

	/**
	 * @param   KunenaUser $user user
	 *
	 * @return KunenaExceptionAuthorise|null
	 * @throws Exception
	 * @since Kunena
	 */
	protected function authoriseVote(KunenaUser $user)
	{
		// Check that user can vote
		$config = KunenaFactory::getConfig();
		$poll   = $this->getPoll();
		$votes  = $poll->getMyVotes($user);

		if (!$config->pollallowvoteone && $votes)
		{
			$time_zone   = Joomla\CMS\Application\CMSApplication::getInstance('site')->get('offset');
			$objTimeZone = new DateTimeZone($time_zone);

			// Check the time between two votes
			$date_a = new DateTime($poll->getMyTime(), $objTimeZone);
			$date_b = new DateTime('now', $objTimeZone);

			$interval = date_diff($date_a, $date_b);

			if ($interval->format('%H:%I:%S') < $config->polltimebtvotes)
			{
				return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_TOPIC_VOTE_NEED_TO_WAIT_BEFORE_TO_CHANGE_VOTE'), 403);
			}
		}

		if ($votes && $config->pollallowvoteone)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_ONLY_ONCE'), 403);
		}

		if ($votes >= $config->pollnbvotesbyuser && $config->pollallowvoteone)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_TOO_MANY_TIMES'), 403);
		}

		if ($config->polltimebtvotes && (int) $poll->getMyTime($user) + (int) $config->polltimebtvotes > Factory::getDate()->toUnix())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_TOO_EARLY'), 403);
		}

		if ($this->locked)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_POLL_TOPIC_LOCKED'), 403);
		}

		if ($poll->polltimetolive != '1000-01-01 00:00:00' && $poll->getTimeToLive() < Factory::getDate()->toUnix())
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_VOTE_POLL_EXPIRED'), 403);
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
	protected function authoriseNoVotes(KunenaUser $user)
	{
		$poll   = $this->getPoll();
		$config = KunenaFactory::getConfig();

		if ($poll->exists() && $poll->getUserCount() && !$config->allow_edit_poll)
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_LIB_TOPIC_AUTHORISE_FAILED_ONGOING_POLL'), 403);
		}

		return;
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

		if ($user->isModerator($this->getCategory()) && !$config->moderator_permdelete || !$user->isModerator($this->getCategory()))
		{
			return new KunenaExceptionAuthorise(Text::_('COM_KUNENA_POST_ERROR_DELETE_REPLY_AFTER'), 403);
		}

		return null;
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
