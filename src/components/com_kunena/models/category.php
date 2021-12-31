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

require_once KPATH_ADMIN . '/models/categories.php';

/**
 * Category Model for Kunena
 *
 * @since        2.0
 */
class KunenaModelCategory extends KunenaAdminModelCategories
{
	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $topics = false;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $pending = array();

	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected $items = false;

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
	 * @throws Exception
	 */
	protected function populateState()
	{
		$layout = $this->getCmd('layout', 'default');
		$this->setState('layout', $layout);

		$params = $this->getParameters();
		$this->setState('params', $params);

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

		// Administrator state
		if ($layout == 'manage' || $layout == 'create' || $layout == 'edit')
		{
			parent::populateState();

			return;
		}

		$active = $this->app->getMenu()->getActive();
		$active = $active ? (int) $active->id : 0;
		$catid  = $this->getInt('catid', 0);
		$this->setState('item.id', $catid);

		$format = $this->getWord('format', 'html');
		$this->setState('format', $format);

		// List state information
		$value        = $this->getUserStateFromRequest("com_kunena.category{$catid}_{$format}_list_limit", 'limit', 0, 'int');
		$defaultlimit = $format != 'feed' ? $this->config->threads_per_page : $this->config->rss_limit;

		if ($value < 1 || $value > 100)
		{
			$value = $defaultlimit;
		}

		$this->setState('list.limit', $value);

		// $value = $this->getUserStateFromRequest ( "com_kunena.category{$catid}_{$format}_{$active}_list_ordering", 'filter_order', 'time', 'cmd' );
		// $this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest("com_kunena.category{$catid}_{$format}_list_start", 'limitstart', 0, 'int');
		$this->setState('list.start', $value);

		$value = $this->getUserStateFromRequest("com_kunena.category{$catid}_{$format}_{$active}_list_direction", 'filter_order_Dir', 'desc', 'word');

		if ($value != 'asc')
		{
			$value = 'desc';
		}

		$this->setState('list.direction', $value);
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	public function getLastestCategories()
	{
		if ($this->items === false)
		{
			$this->items = array();
			$user        = KunenaFactory::getUser();
			list($total, $categories) = KunenaForumCategoryHelper::getLatestSubscriptions($user->userid);
			$this->items = $categories;
		}

		return $this->items;
	}

	/**
	 * @return array|boolean|KunenaForumCategory[]
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function getCategories()
	{
		if ($this->items === false)
		{
			$this->items = array();
			$catid       = $this->getState('item.id');
			$layout      = $this->getState('layout');
			$flat        = false;

			if ($layout == 'user')
			{
				$categories[0] = KunenaForumCategoryHelper::getSubscriptions();
				$flat          = true;
			}
			elseif ($catid)
			{
				$categories[0] = KunenaForumCategoryHelper::getCategories($catid);

				if (empty($categories[0]))
				{
					return array();
				}
			}
			else
			{
				$categories[0] = KunenaForumCategoryHelper::getChildren();
			}

			if ($flat)
			{
				$allsubcats = $categories[0];
			}
			else
			{
				$allsubcats = KunenaForumCategoryHelper::getChildren(array_keys($categories [0]), 1);
			}

			if (empty($allsubcats))
			{
				return array();
			}

			KunenaForumCategoryHelper::getNewTopics(array_keys($allsubcats));

			$modcats      = array();
			$lastpostlist = array();
			$userlist     = array();
			$topiclist    = array();

			foreach ($allsubcats as $subcat)
			{
				if ($flat || isset($categories [0] [$subcat->parent_id]))
				{
					$last = $subcat->getLastCategory();

					if ($last->last_topic_id)
					{
						// Get list of topics
						$topiclist[$last->last_topic_id] = $last->last_topic_id;
					}

					if ($this->config->listcat_show_moderators)
					{
						// Get list of moderators
						$subcat->moderators = $subcat->getModerators(false, false);
						$userlist           += $subcat->moderators;
					}

					if ($this->me->isModerator($subcat))
					{
						$modcats [] = $subcat->id;
					}
				}

				$categories [$subcat->parent_id] [] = $subcat;
			}

			// Prefetch topics
			$topics = KunenaForumTopicHelper::getTopics($topiclist);

			foreach ($topics as $topic)
			{
				// Prefetch users
				$userlist [$topic->last_post_userid] = $topic->last_post_userid;
				$lastpostlist [$topic->id]           = $topic->last_post_id;
			}

			if ($this->me->ordering != 0)
			{
				$topic_ordering = $this->me->ordering == 1 ? true : false;
			}
			else
			{
				$topic_ordering = $this->config->default_sort == 'asc' ? false : true;
			}

			$this->pending = array();

			if ($this->me->userid && count($modcats))
			{
				$catlist = implode(',', $modcats);
				$db      = Factory::getDBO();
				$db->setQuery(
					"SELECT catid, COUNT(*) AS count
				FROM #__kunena_messages
				WHERE catid IN ({$catlist}) AND hold=1
				GROUP BY catid"
				);

				try
				{
					$pending = $db->loadAssocList();
				}
				catch (JDatabaseExceptionExecuting $e)
				{
					KunenaError::displayDatabaseError($e);
				}

				foreach ($pending as $item)
				{
					if ($item ['count'])
					{
						$this->pending [$item ['catid']] = $item ['count'];
					}
				}
			}

			// Fix last post position when user can see unapproved or deleted posts
			if ($lastpostlist && !$topic_ordering && ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus()))
			{
				KunenaForumMessageHelper::getMessages($lastpostlist);
				KunenaForumMessageHelper::loadLocation($lastpostlist);
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			KunenaUserHelper::loadUsers($userlist);

			if ($flat)
			{
				$this->items = $allsubcats;
			}
			else
			{
				$this->items = $categories;
			}
		}

		return $this->items;
	}

	/**
	 * @return array
	 * @since Kunena
	 */
	public function getUnapprovedCount()
	{
		return $this->pending;
	}

	/**
	 * @return KunenaForumCategory
	 * @since Kunena
	 */
	public function getCategory()
	{
		return KunenaForumCategoryHelper::get($this->getState('item.id'));
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
			$catid      = $this->getState('item.id');
			$limitstart = $this->getState('list.start');
			$limit      = $this->getState('list.limit');
			$format     = $this->getState('format');

			$topic_ordering = $this->getCategory()->topic_ordering;

			$access = KunenaAccess::getInstance();
			$hold   = $format == 'feed' ? 0 : $access->getAllowedHold($this->me, $catid);
			$moved  = $format == 'feed' ? 0 : 1;
			$params = array(
				'hold'  => $hold,
				'moved' => $moved, );

			switch ($topic_ordering)
			{
				case 'alpha':
					$params['orderby'] = 'tt.ordering DESC, tt.subject ASC ';
					break;
				case 'creation':
					$params['orderby'] = 'tt.ordering DESC, tt.first_post_time ' . strtoupper($this->getState('list.direction'));
					break;
				case 'views':
					$params['orderby'] = 'tt.ordering DESC, tt.hits ' . strtoupper($this->getState('list.direction'));
					break;
				case 'posts':
					$params['orderby'] = 'tt.ordering DESC, tt.posts ' . strtoupper($this->getState('list.direction'));
					break;
				case 'lastpost':
				default:
					$params['orderby'] = 'tt.ordering DESC, tt.last_post_time ' . strtoupper($this->getState('list.direction'));
			}

			if ($format == 'feed')
			{
				$catid = array_keys(KunenaForumCategoryHelper::getChildren($catid, 100) + array($catid => 1));
			}

			list($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($catid, $limitstart, $limit, $params);

			if ($this->total > 0)
			{
				// Collect user ids for avatar prefetch when integrated
				$userlist     = array();
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
				if ($lastreadlist || $this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus())
				{
					KunenaForumMessageHelper::loadLocation($lastpostlist + $lastreadlist);
				}
			}
		}

		return $this->topics;
	}

	/**
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	public function getTotal()
	{
		if ($this->total === false)
		{
			$this->getTopics();
		}

		return $this->total;
	}

	/**
	 * @return array|null
	 * @throws Exception
	 * @since Kunena
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
	 * @return boolean
	 * @since Kunena
	 */
	public function getActionMove()
	{
		return $this->actionMove;
	}

	/**
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function getModerators()
	{
		$moderators = $this->getCategory()->getModerators(false);

		return $moderators;
	}

	/**
	 * @return array|null
	 * @since Kunena
	 */
	public function getCategoryActions()
	{
		$actionDropdown[] = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		$actionDropdown[] = HTMLHelper::_('select.option', 'unsubscribe', Text::_('COM_KUNENA_UNSUBSCRIBE_SELECTED'));

		if (count($actionDropdown) == 1)
		{
			return;
		}

		return $actionDropdown;
	}
}
