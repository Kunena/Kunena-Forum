<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Topics Model for Kunena
 *
 * @since  2.0
 */
class KunenaModelTopics extends KunenaModel
{
	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $topics = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $messages = false;

	/**
	 * @var integer
	 * @since Kunena
	 */
	protected $total = 0;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $topicActions = false;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $actionMove = false;

	/**
	 * @since Kunena
	 */
	protected function populateState()
	{
		$params = $this->getParameters();
		$this->setState('params', $params);

		$format = $this->getWord('format', 'html');
		$this->setState('format', $format);

		$active = $this->app->getMenu()->getActive();
		$active = $active ? (int) $active->id : 0;
		$layout = $this->getWord('layout', 'default');
		$this->setState('layout', $layout);

		$display = $this->getUserStateFromRequest('com_kunena.users_display', 'display', 'topics');
		$this->setState('display', $display);

		$userid = $this->getInt('userid', -1);

		if ($userid < 0)
		{
			$userid = $this->me->userid;
		}
		elseif ($userid > 0)
		{
			$userid = KunenaFactory::getUser($userid)->userid;
		}
		else
		{
			$userid = 0;
		}

		$this->setState('user', $userid);

		$mode = $this->getWord('mode', 'default');
		$this->setState('list.mode', $mode);

		$modetype = $this->getWord('modetype', '');
		$this->setState('list.modetype', $modetype);

		$catid = $this->getInt('catid');
		$this->setState('list.categories.exclude', 0);

		if ($catid)
		{
			$latestcategory    = array($catid);
			$latestcategory_in = true;

			// Check if the category is in excluded list
			if (!empty($this->config->rss_excluded_categories))
			{
				$cat_excluded = explode(',', $this->config->rss_excluded_categories);

				if (in_array($catid, $cat_excluded))
				{
					$latestcategory    = $this->config->rss_excluded_categories;
					$latestcategory_in = 0;
					$this->setState('list.categories.exclude', 1);
				}
			}
		}
		else
		{
			if (Factory::getDocument()->getType() != 'feed')
			{
				// Get configuration from menu item.
				$latestcategory    = $params->get('topics_categories', '');
				$latestcategory_in = $params->get('topics_catselection', '');

				// Make sure that category list is an array.
				if (!is_array($latestcategory))
				{
					$latestcategory = explode(',', $latestcategory);
				}

				// Default to global configuration.
				if (in_array('', $latestcategory, true))
				{
					$latestcategory = $this->config->latestcategory;
				}

				if ($latestcategory_in == '')
				{
					$latestcategory_in = $this->config->latestcategory_in;
				}
			}
			else
			{
				// Use RSS configuration.
				if (!empty($this->config->rss_excluded_categories))
				{
					$latestcategory    = $this->config->rss_excluded_categories;
					$latestcategory_in = 0;
				}
				else
				{
					$latestcategory    = $this->config->rss_included_categories;
					$latestcategory_in = 1;
				}
			}

			if (!empty($latestcategory) && !is_array($latestcategory))
			{
				$latestcategory = explode(',', $latestcategory);
			}
			elseif (empty($latestcategory) && !is_array($latestcategory))
			{
				$latestcategory = array();
			}
			else
			{
				if (count($latestcategory) == 1)
				{
					if ($latestcategory[0] == 0)
					{
						$latestcategory = array();
					}
				}
			}

			if (count($latestcategory) == 0)
			{
				$latestcategory = false;
			}
		}

		$this->setState('list.categories', $latestcategory);
		$this->setState('list.categories.in', $latestcategory_in);

		// Selection time.
		if (Factory::getDocument()->getType() != 'feed')
		{
			// Selection time from user state / menu item / url parameter / configuration.
			if (!$this->me->exists() || $this->me->exists() && $this->me->userListtime == -2)
			{
				$value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_time", 'sel', $params->get('topics_time', $this->config->show_list_time), 'int');
				$this->setState('list.time', (int) $value);
			}

			if ($this->me->exists() && $this->me->userListtime != -2)
			{
				$value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_time", 'sel', $this->me->userListtime, 'int');
				$this->setState('list.time', (int) $value);
			}
		}
		else
		{
			// Selection time.
			$value = $this->getString('sel', $this->config->rss_timelimit);
			$this->setState('list.time', $value);
		}

		// List state information
		$value        = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_limit", 'limit', 0, 'int');
		$defaultlimit = $format != 'feed' ? $this->config->threads_per_page : $this->config->rss_limit;

		if ($value < 1 || $value > 100)
		{
			$value = $defaultlimit;
		}

		$this->setState('list.limit', $value);

		$value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_ordering", 'filter_order', 'id', 'cmd');
		$this->setState('list.ordering', $value);

		$value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_start", 'limitstart', 0, 'int');

		// $value = $this->getInt ( 'limitstart', 0 );
		$this->setState('list.start', $value);

		$value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_direction", 'filter_order_Dir', 'desc', 'word');

		if ($value != 'asc')
		{
			$value = 'desc';
		}

		$this->setState('list.direction', $value);
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function getTopics()
	{
		if ($this->topics === false)
		{
			$layout = $this->getState('layout');
			$mode   = $this->getState('list.mode');

			if ($mode == 'plugin')
			{
				$pluginmode = $this->getState('list.modetype');

				if (!empty($pluginmode))
				{
					$total  = 0;
					$topics = false;

					Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');
					Factory::getApplication()->triggerEvent('onKunenaGetTopics', array($layout, $pluginmode, &$topics, &$total, $this));

					if (!empty($topics))
					{
						$this->topics = $topics;
						$this->total  = $total;
						$this->_common();
					}
				}
			}

			if ($this->topics === false)
			{
				switch ($layout)
				{
					case 'user':
						$this->getUserTopics();
						break;
					default:
						$this->getRecentTopics();
				}
			}
		}

		return $this->topics;
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	protected function getRecentTopics()
	{
		$catid      = $this->getState('item.id');
		$limitstart = $this->getState('list.start');
		$limit      = $this->getState('list.limit');
		$time       = $this->getState('list.time');

		if (Factory::getDocument()->getType() != 'feed')
		{
			if ($time < 0)
			{
				$time = 0;
			}
			elseif ($time == 0)
			{
				$time = KunenaFactory::getSession()->lasttime;
			}
			else
			{
				$time = Factory::getDate()->toUnix() - ((int) $time * 3600);
			}
		}
		else
		{
			if ($time == 'month')
			{
				$time = new \DateTime('1 month ago');
			}
			elseif ($time == 'week')
			{
				$time = new \DateTime('1 week ago');
			}
			else
			{
				$time = new \DateTime('1 year ago');
			}

			$time = $time->getTimestamp();
		}

		$latestcategory    = $this->getState('list.categories');
		$latestcategory_in = $this->getState('list.categories.in');

		$hold     = 0;
		$where    = '';
		$lastpost = true;

		// Reset topics.
		$this->total  = 0;
		$this->topics = array();

		switch ($this->getState('list.mode'))
		{
			case 'topics' :
				$lastpost = false;
				break;
			case 'sticky' :
				$where = 'AND tt.ordering>0';
				break;
			case 'locked' :
				$where = 'AND tt.locked>0';
				break;
			case 'noreplies' :
				$where = 'AND tt.posts=1';
				break;
			case 'unapproved' :
				$allowed = KunenaForumCategoryHelper::getCategories(false, false, 'topic.approve');

				if (empty($allowed))
				{
					return;
				}

				$allowed = implode(',', array_keys($allowed));
				$hold    = '1';
				$where   = "AND tt.category_id IN ({$allowed})";
				break;
			case 'deleted' :
				$allowed = KunenaForumCategoryHelper::getCategories(false, false, 'topic.undelete');

				if (empty($allowed))
				{
					return;
				}

				$allowed = implode(',', array_keys($allowed));
				$hold    = '2';
				$where   = "AND tt.category_id IN ({$allowed})";
				break;
			case 'replies' :
			default :
				break;
		}

		$params = array(
			'reverse'   => !$latestcategory_in,
			'exclude'   => $this->setState('list.categories.exclude', 0),
			'orderby'   => $lastpost ? 'tt.last_post_time DESC' : 'tt.first_post_time DESC',
			'starttime' => $time,
			'hold'      => $hold,
			'where'     => $where, );

		list($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($latestcategory, $limitstart, $limit, $params);

		$this->_common();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	protected function getUserTopics()
	{
		$catid      = $this->getState('item.id');
		$limitstart = $this->getState('list.start');
		$limit      = $this->getState('list.limit');

		$latestcategory    = $this->getState('list.categories');
		$latestcategory_in = $this->getState('list.categories.in');

		$started       = false;
		$posts         = false;
		$favorites     = false;
		$subscriptions = false;

		// Set order by
		$orderby = "tt.last_post_time DESC";

		switch ($this->getState('list.mode'))
		{
			case 'posted' :
				$posts   = true;
				$orderby = "ut.last_post_id DESC";
				break;
			case 'started' :
				$started = true;
				break;
			case 'favorites' :
				$favorites = true;
				break;
			case 'subscriptions' :
				$subscriptions = true;
				break;
			default :
				$posts         = true;
				$favorites     = true;
				$subscriptions = true;
				$orderby       = "ut.favorite DESC, tt.last_post_time DESC";
				break;
		}

		$params = array(
			'reverse'    => !$latestcategory_in,
			'orderby'    => $orderby,
			'hold'       => 0,
			'user'       => $this->getState('user'),
			'started'    => $started,
			'posted'     => $posts,
			'favorited'  => $favorites,
			'subscribed' => $subscriptions, );

		list($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($latestcategory, $limitstart, $limit, $params);

		$this->_common();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	protected function getPosts()
	{
		$this->topics = array();

		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');

		// Time will be calculated inside KunenaForumMessageHelper::getLatestMessages()
		$time = $this->getState('list.time');

		$params              = array();
		$params['mode']      = $this->getState('list.mode');
		$params['reverse']   = !$this->getState('list.categories.in');
		$params['starttime'] = $time;
		$params['user']      = $this->getState('user');
		list($this->total, $this->messages) = KunenaForumMessageHelper::getLatestMessages($this->getState('list.categories'), $start, $limit, $params);

		$topicids = array();

		foreach ($this->messages as $message)
		{
			$topicids[$message->thread] = $message->thread;
		}

		$authorise = 'read';

		switch ($params['mode'])
		{
			case 'unapproved':
				$authorise = 'approve';
				break;
			case 'deleted':
				$authorise = 'undelete';
				break;
		}

		$this->topics = KunenaForumTopicHelper::getTopics($topicids, $authorise);

		$userlist = $postlist = array();

		foreach ($this->messages as $message)
		{
			$userlist[intval($message->userid)] = intval($message->userid);
			$postlist[intval($message->id)]     = intval($message->id);
		}

		$this->_common($userlist, $postlist);
	}

	/**
	 * @param   array $userlist userlist
	 * @param   array $postlist postlist
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	protected function _common(array $userlist = array(), array $postlist = array())
	{
		if ($this->total > 0)
		{
			// Collect user ids for avatar prefetch when integrated
			$lastpostlist = array();

			foreach ($this->topics as $topic)
			{
				$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
				$userlist[intval($topic->last_post_userid)]  = intval($topic->last_post_userid);
				$lastpostlist[intval($topic->last_post_id)]  = intval($topic->last_post_id);
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			if (!empty($userlist))
			{
				KunenaUserHelper::loadUsers($userlist);
			}

			KunenaForumTopicHelper::getUserTopics(array_keys($this->topics));
			$lastreadlist = KunenaForumTopicHelper::fetchNewStatus($this->topics);

			// Fetch last / new post positions when user can see unapproved or deleted posts
			if ($postlist || $lastreadlist || ($this->me->userid && ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus())))
			{
				KunenaForumMessageHelper::loadLocation($postlist + $lastpostlist + $lastreadlist);
			}
		}
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function getMessages()
	{
		if ($this->topics === false)
		{
		    $this->getPosts();
		}

		return $this->messages;
	}

	/**
	 * @return integer
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function getTotal()
	{
		if ($this->topics === false)
		{
			$this->getTopics();
		}

		return $this->total;
	}

	/**
	 * @return array|null
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function getTopicActions()
	{
		if ($this->topics === false)
		{
			$this->getTopics();
		}

		$delete = $approve = $undelete = $move = $permdelete = false;

		foreach ($this->topics as $topic)
		{
			if (!$delete && $topic->isAuthorised('delete'))
			{
				$delete = true;
			}

			if (!$approve && $topic->isAuthorised('approve'))
			{
				$approve = true;
			}

			if (!$undelete && $topic->isAuthorised('undelete'))
			{
				$undelete = true;
			}

			if (!$move && $topic->isAuthorised('move'))
			{
				$move = $this->actionMove = true;
			}

			if (!$permdelete && $topic->isAuthorised('permdelete'))
			{
				$permdelete = true;
			}
		}

		$actionDropdown[] = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));

		if ($this->getState('list.mode') == 'subscriptions')
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'unsubscribe', Text::_('COM_KUNENA_UNSUBSCRIBE_SELECTED'));
		}

		if ($this->getState('list.mode') == 'favorites')
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'unfavorite', Text::_('COM_KUNENA_UNFAVORITE_SELECTED'));
		}

		if ($move)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'move', Text::_('COM_KUNENA_MOVE_SELECTED'));
		}

		if ($approve)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'approve', Text::_('COM_KUNENA_APPROVE_SELECTED'));
		}

		if ($delete)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'delete', Text::_('COM_KUNENA_DELETE_SELECTED'));
		}

		if ($permdelete)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'permdel', Text::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
		}

		if ($undelete)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'restore', Text::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
		}

		if (count($actionDropdown) == 1)
		{
			return;
		}

		return $actionDropdown;
	}

	/**
	 * @return array|null
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function getPostActions()
	{
		if ($this->messages === false)
		{
			$this->getPosts();
		}

		$delete = $approve = $undelete = $move = $permdelete = false;

		foreach ($this->messages as $message)
		{
			if (!$delete && $message->isAuthorised('delete'))
			{
				$delete = true;
			}

			if (!$approve && $message->isAuthorised('approve'))
			{
				$approve = true;
			}

			if (!$undelete && $message->isAuthorised('undelete'))
			{
				$undelete = true;
			}

			if (!$permdelete && $message->isAuthorised('permdelete'))
			{
				$permdelete = true;
			}
		}

		$actionDropdown[] = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));

		if ($approve)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'approve_posts', Text::_('COM_KUNENA_APPROVE_SELECTED'));
		}

		if ($delete)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'delete_posts', Text::_('COM_KUNENA_DELETE_SELECTED'));
		}

		if ($permdelete)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'permdel_posts', Text::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
		}

		if ($undelete)
		{
			$actionDropdown[] = HTMLHelper::_('select.option', 'restore_posts', Text::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
		}

		if (count($actionDropdown) == 1)
		{
			return;
		}

		return $actionDropdown;
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function getActionMove()
	{
		return $this->actionMove;
	}
}
