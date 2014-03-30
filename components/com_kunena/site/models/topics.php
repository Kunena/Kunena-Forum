<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Topics Model for Kunena
 *
 * @since		2.0
 */
class KunenaModelTopics extends KunenaModel {
	protected $topics = false;
	protected $messages = false;
	protected $total = 0;
	protected $topicActions = false;
	protected $actionMove = false;

	protected function populateState() {
		$params = $this->getParameters();
		$this->setState ( 'params', $params );

		$format = $this->getWord ( 'format', 'html' );
		$this->setState ( 'format', $format );

		$active = $this->app->getMenu ()->getActive ();
		$active = $active ? (int) $active->id : 0;
		$layout = $this->getWord ( 'layout', 'default' );
		$this->setState ( 'layout', $layout );

		$userid = $this->getInt ( 'userid', -1 );
		if ($userid < 0) {
			$userid = $this->me->userid;
		} elseif($userid > 0) {
			$userid = KunenaFactory::getUser($userid)->userid;
		} else {
			$userid = 0;
		}
		$this->setState ( 'user', $userid );

		$mode = $this->getWord ( 'mode', 'default' );
		$this->setState ( 'list.mode', $mode );

		$modetype = $this->getWord ( 'modetype', '' );
		$this->setState ( 'list.modetype', $modetype );

		$catid = $this->getInt ( 'catid' );
		if ($catid) {
			$latestcategory = array($catid);
			$latestcategory_in = true;
		} else {
			if (JFactory::getDocument()->getType() != 'feed') {
				// Get configuration from menu item.
				$latestcategory = $params->get('topics_categories', '');
				$latestcategory_in = $params->get('topics_catselection', '');

				// Make sure that category list is an array.
				if (!is_array($latestcategory)) $latestcategory = explode ( ',', $latestcategory );

				// Default to global configuration.
				if (in_array('', $latestcategory, true)) $latestcategory = $this->config->latestcategory;
				if ($latestcategory_in == '') $latestcategory_in = $this->config->latestcategory_in;

			} else {
				// Use RSS configuration.
				if(!empty($this->config->rss_excluded_categories)) {
					$latestcategory = $this->config->rss_excluded_categories;
					$latestcategory_in = 0;
				} else {
					$latestcategory = $this->config->rss_included_categories;
					$latestcategory_in = 1;
				}

			}
			if (!is_array($latestcategory)) $latestcategory = explode ( ',', $latestcategory );
			if (empty($latestcategory) || in_array(0, $latestcategory)) {
				$latestcategory = false;
			}

		}
		$this->setState ( 'list.categories', $latestcategory );
		$this->setState ( 'list.categories.in', $latestcategory_in );

		// Selection time.
		if (JFactory::getDocument()->getType() != 'feed') {
			// Selection time from user state / menu item / url parameter / configuration.
			$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_time", 'sel', $params->get('topics_time', $this->config->show_list_time), 'int' );
			$this->setState ( 'list.time', (int) $value );

		} else {
			// Selection time.
			$value = $this->getInt ('sel', $this->config->rss_timelimit);
			$this->setState ( 'list.time', $value );

		}

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_limit", 'limit', 0, 'int' );
		if ($value < 1 || $value > 100) $value = $this->config->threads_per_page;
		$this->setState ( 'list.limit', $value );

		//$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_list_ordering", 'filter_order', 'time', 'cmd' );
		//$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_start", 'limitstart', 0, 'int' );
		//$value = $this->getInt ( 'limitstart', 0 );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_direction", 'filter_order_Dir', 'desc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );
	}

	public function getTopics() {
		if ($this->topics === false) {
			$layout = $this->getState ( 'layout' );
			$mode = $this->getState('list.mode');
			if ($mode == 'plugin') {
				$pluginmode = $this->getState('list.modetype');
				if(!empty($pluginmode)) {
					$total = 0;
					$topics = false;

					JPluginHelper::importPlugin('kunena');
					$dispatcher = JDispatcher::getInstance();
					$dispatcher->trigger('onKunenaGetTopics', array($layout, $pluginmode, &$topics, &$total, $this));

					if(!empty($topics)) {
						$this->topics = $topics;
						$this->total = $total;
						$this->_common ();
					}
				}
			}
			if ($this->topics === false) {
				switch ($layout) {
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

	protected function getRecentTopics() {
		$catid = $this->getState ( 'item.id' );
		$limitstart = $this->getState ( 'list.start' );
		$limit = $this->getState ( 'list.limit' );
		$time = $this->getState ( 'list.time' );
		if ($time < 0) {
			$time = 0;
		} elseif ($time == 0) {
			$time = KunenaFactory::getSession ()->lasttime;
		} else {
			$time = JFactory::getDate ()->toUnix () - ($time * 3600);
		}

		$latestcategory = $this->getState ( 'list.categories' );
		$latestcategory_in = $this->getState ( 'list.categories.in' );

		$hold = 0;
		$where = '';
		$lastpost = true;

		// Reset topics.
		$this->total = 0;
		$this->topics = array();

		switch ($this->getState ( 'list.mode' )) {
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
				if (empty($allowed)) {
					return;
				}
				$allowed = implode(',', array_keys($allowed));
				$hold = '1';
				$where = "AND tt.category_id IN ({$allowed})";
				break;
			case 'deleted' :
				$allowed = KunenaForumCategoryHelper::getCategories(false, false, 'topic.undelete');
				if (empty($allowed)) {
					return;
				}
				$allowed = implode(',', array_keys($allowed));
				$hold = '2';
				$where = "AND tt.category_id IN ({$allowed})";
				break;
			case 'replies' :
			default :
				break;
		}

		$params = array (
			'reverse' => ! $latestcategory_in,
			'orderby' => $lastpost ? 'tt.last_post_time DESC' : 'tt.first_post_time DESC',
			'starttime' => $time,
			'hold' => $hold,
			'where' => $where );

		list ( $this->total, $this->topics ) = KunenaForumTopicHelper::getLatestTopics ( $latestcategory, $limitstart, $limit, $params );
		$this->_common ();
	}

	protected function getUserTopics() {
		$catid = $this->getState ( 'item.id' );
		$limitstart = $this->getState ( 'list.start' );
		$limit = $this->getState ( 'list.limit' );

		$latestcategory = $this->getState ( 'list.categories' );
		$latestcategory_in = $this->getState ( 'list.categories.in' );

		$started = false;
		$posts = false;
		$favorites = false;
		$subscriptions = false;
		// Set order by
		$orderby = "tt.last_post_time DESC";
		switch ($this->getState ( 'list.mode' )) {
			case 'posted' :
				$posts = true;
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
				$posts = true;
				$favorites = true;
				$subscriptions = true;
				$orderby = "ut.favorite DESC, tt.last_post_time DESC";
				break;
		}

		$params = array (
			'reverse' => ! $latestcategory_in,
			'orderby' => $orderby,
			'hold' => 0,
			'user' => $this->getState ( 'user' ),
			'started' => $started,
			'posted' => $posts,
			'favorited' => $favorites,
			'subscribed' => $subscriptions );

		list ( $this->total, $this->topics ) = KunenaForumTopicHelper::getLatestTopics ( $latestcategory, $limitstart, $limit, $params );
		$this->_common ();
	}

	protected function getPosts() {
		$this->topics = array();

		$start = $this->getState ( 'list.start' );
		$limit = $this->getState ( 'list.limit' );
		// Time will be calculated inside KunenaForumMessageHelper::getLatestMessages()
		$time = $this->getState ( 'list.time' );

		$params = array();
		$params['mode'] = $this->getState ( 'list.mode' );
		$params['reverse'] = ! $this->getState ( 'list.categories.in' );
		$params['starttime'] = $time;
		$params['user'] = $this->getState ( 'user' );
		list ($this->total, $this->messages) = KunenaForumMessageHelper::getLatestMessages($this->getState ( 'list.categories' ), $start, $limit, $params);

		$topicids = array();
		foreach ( $this->messages as $message ) {
			$topicids[$message->thread] = $message->thread;
		}
		$authorise = 'read';
		switch ($params['mode']) {
			case 'unapproved':
				$authorise = 'approve';
				break;
			case 'deleted':
				$authorise = 'undelete';
				break;
		}
		$this->topics = KunenaForumTopicHelper::getTopics ( $topicids, $authorise );

		$userlist = $postlist = array();
		foreach ( $this->messages as $message ) {
			$userlist[intval($message->userid)] = intval($message->userid);
			$postlist[intval($message->id)] = intval($message->id);
		}

		$this->_common($userlist, $postlist);
	}

	protected function _common(array $userlist = array(), array $postlist = array()) {
		if ($this->total > 0) {
			// collect user ids for avatar prefetch when integrated
			$lastpostlist = array();
			foreach ( $this->topics as $topic ) {
				$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
				$userlist[intval($topic->last_post_userid)] = intval($topic->last_post_userid);
				$lastpostlist[intval($topic->last_post_id)] = intval($topic->last_post_id);
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			if ( !empty($userlist) ) KunenaUserHelper::loadUsers($userlist);

			KunenaForumTopicHelper::getUserTopics(array_keys($this->topics));
			KunenaForumTopicHelper::getKeywords(array_keys($this->topics));
			$lastpostlist += KunenaForumTopicHelper::fetchNewStatus($this->topics);
			// Fetch last / new post positions when user can see unapproved or deleted posts
			$me = KunenaUserHelper::get();
			if ($postlist || ($lastpostlist && $me->userid && ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus()))) {
				KunenaForumMessageHelper::loadLocation($postlist + $lastpostlist);
			}
		}
	}

	public function getMessages() {
		if ($this->topics === false) {
			$this->getPosts();
		}
		return $this->messages;
	}

	public function getTotal() {
		if ($this->topics === false) {
			$this->getTopics();
		}
		return $this->total;
	}

	public function getTopicActions() {
		if ($this->topics === false) {
			$this->getTopics();
		}
		$delete = $approve = $undelete = $move = $permdelete = false;
		foreach ($this->topics as $topic) {
			if (!$delete && $topic->authorise('delete')) $delete = true;
			if (!$approve && $topic->authorise('approve')) $approve = true;
			if (!$undelete && $topic->authorise('undelete')) $undelete = true;
			if (!$move && $topic->authorise('move')) {
				$move = $this->actionMove = true;
			}
			if (!$permdelete && $topic->authorise('permdelete')) $permdelete = true;
		}
		$actionDropdown[] = JHtml::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		if ($this->getState ('list.mode') == 'subscriptions')
			$actionDropdown[] = JHtml::_('select.option', 'unsubscribe', JText::_('COM_KUNENA_UNSUBSCRIBE_SELECTED'));
		if ($this->getState ('list.mode') == 'favorites')
			$actionDropdown[] = JHtml::_('select.option', 'unfavorite', JText::_('COM_KUNENA_UNFAVORITE_SELECTED'));
		if ($move) $actionDropdown[] = JHtml::_('select.option', 'move', JText::_('COM_KUNENA_MOVE_SELECTED'));
		if ($approve) $actionDropdown[] = JHtml::_('select.option', 'approve', JText::_('COM_KUNENA_APPROVE_SELECTED'));
		if ($delete) $actionDropdown[] = JHtml::_('select.option', 'delete', JText::_('COM_KUNENA_DELETE_SELECTED'));
		if ($permdelete) $actionDropdown[] = JHtml::_('select.option', 'permdel', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
		if ($undelete) $actionDropdown[] = JHtml::_('select.option', 'restore', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));

		if (count($actionDropdown) == 1) return null;
		return $actionDropdown;
	}

	public function getPostActions() {
		if ($this->messages === false) {
			$this->getPosts();
		}

		$delete = $approve = $undelete = $move = $permdelete = false;
		foreach ($this->messages as $message) {
			if (!$delete && $message->authorise('delete')) $delete = true;
			if (!$approve && $message->authorise('approve')) $approve = true;
			if (!$undelete && $message->authorise('undelete')) $undelete = true;
			if (!$permdelete && $message->authorise('permdelete')) $permdelete = true;
		}
		$actionDropdown[] = JHtml::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		if ($approve) $actionDropdown[] = JHtml::_('select.option', 'approve_posts', JText::_('COM_KUNENA_APPROVE_SELECTED'));
		if ($delete) $actionDropdown[] = JHtml::_('select.option', 'delete_posts', JText::_('COM_KUNENA_DELETE_SELECTED'));
		if ($permdelete) $actionDropdown[] = JHtml::_('select.option', 'permdel_posts', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
		if ($undelete) $actionDropdown[] = JHtml::_('select.option', 'restore_posts', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));

		if (count($actionDropdown) == 1) return null;
		return $actionDropdown;
	}

	public function getActionMove() {
		return $this->actionMove;
	}
}
