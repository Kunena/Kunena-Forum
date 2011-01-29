<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.model' );
kimport('kunena.forum.category.helper');
kimport('kunena.forum.topic.helper');
kimport('kunena.user.helper');

/**
 * Topics Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelTopics extends KunenaModel {
	protected $topics = false;
	protected $messages = false;
	protected $total = 0;

	protected function populateState() {
		$app = JFactory::getApplication ();
		$params = $this->getParameters();
		$this->setState ( 'params', $params );
		$config = KunenaFactory::getConfig ();

		$active = $app->getMenu ()->getActive ();
		$active = $active ? (int) $active->id : 0;
		$layout = $this->getWord ( 'layout', 'default' );
		$this->setState ( 'layout', $layout );

		$userid = $this->getInt ( 'userid', -1 );
		if ($userid < 0) {
			$userid = KunenaFactory::getUser()->userid;
		} elseif($userid > 0) {
			$userid = KunenaFactory::getUser($userid)->userid;
		} else {
			$userid = 0;
		}
		$this->setState ( 'user', $userid );

		$mode = $this->getWord ( 'mode', 'default' );
		$this->setState ( 'list.mode', $mode );

		$latestcategory = $params->get('topics_categories', $config->latestcategory );
		if (!is_array($latestcategory)) $latestcategory = explode ( ',', $latestcategory );
		if (empty($latestcategory) || in_array(0, $latestcategory)) {
			$latestcategory = false;
		}
		$this->setState ( 'list.categories', $latestcategory );
		$this->setState ( 'list.categories.in', (bool)$params->get('topics_catselection', $config->latestcategory_in) );

		$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_list_time", 'sel', $params->get('topics_time', $config->show_list_time), 'int' );
		// FIXME: last visit and all
		$this->setState ( 'list.time', $value );

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_list_limit", 'limit', 0, 'int' );
		if ($value < 1) $value = $config->threads_per_page;
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_list_ordering", 'filter_order', 'time', 'cmd' );
		//$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_list_start", 'limitstart', 0, 'int' );
		//$value = $this->getInt ( 'limitstart', 0 );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.topics_{$active}_{$layout}_{$mode}_list_direction", 'filter_order_Dir', 'desc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );
	}

	public function getMessageOrdering() {
		$me = KunenaUserHelper::get();
		if ($me->ordering != '0') {
			$ordering = $me->ordering == '1' ? 'desc' : 'asc';
		} else {
			$config = KunenaFactory::getConfig ();
			$ordering = $config->default_sort == 'asc' ? 'asc' : 'desc';
		}
		if ($ordering != 'asc')
			$ordering = 'desc';
		return $ordering;
	}

	public function getTopics() {
		if ($this->topics === false) {
			$layout = $this->getState ( 'layout');
			switch ($layout) {
				case 'user':
					$this->getUserTopics();
					break;
				default:
					$this->getRecentTopics();
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
					return array(0, array());
				}
				$allowed = implode(',', array_keys($allowed));
				$hold = '1';
				$where = "AND tt.category_id IN ({$allowed})";
				break;
			case 'deleted' :
				$allowed = KunenaForumCategoryHelper::getCategories(false, false, 'topic.undelete');
				if (empty($allowed)) {
					return array(0, array());
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
		$this->count = 0;
		kimport ('kunena.databasequery');

		$userid = $this->getState ( 'user' );
		$start = $this->getState ( 'list.start' );
		$limit = $this->getState ( 'list.limit' );
		$db = JFactory::getDBO();

		$cquery = new KunenaDatabaseQuery();
		$cquery->select('COUNT(*)')
			->from('#__kunena_messages AS m')
			->innerJoin('#__kunena_topics AS tt ON m.thread = tt.id')
			->where('m.moved=0'); // TODO: remove column

		$rquery = new KunenaDatabaseQuery();
		$rquery->select('m.*, t.message')
			->from('#__kunena_messages AS m')
			->innerJoin('#__kunena_messages_text AS t ON m.id = t.mesid')
			->innerJoin('#__kunena_topics AS tt ON m.thread = tt.id')
			->where('m.moved=0') // TODO: remove column
			->order('m.time DESC');

		$authorise = 'read';
		$hold = 'm.hold=0'; // AND tt.hold=0';
		$userfield = 'm.userid';
		switch ($this->getState ( 'list.mode' )) {
			case 'unapproved':
				$authorise = 'approve';
				$hold = "m.hold=1 AND tt.hold<=1";
				break;
			case 'deleted':
				$authorise = 'undelete';
				$hold = "m.hold>=2";
				break;
			case 'mythanks':
				$userfield = 'th.userid';
				$cquery->innerJoin('#__kunena_thankyou AS th ON m.id = th.postid');
				$rquery->innerJoin('#__kunena_thankyou AS th ON m.id = th.postid');
				break;
			case 'thankyou':
				$userfield = 'th.targetuserid';
				$cquery->innerJoin('#__kunena_thankyou AS th ON m.id = th.postid');
				$rquery->innerJoin('#__kunena_thankyou AS th ON m.id = th.postid');
				break;
			case 'recent':
			default:
		}
		$allowed = KunenaForumCategoryHelper::getCategories($this->getState ( 'list.categories' ), ! $this->getState ( 'list.categories.in' ), 'topic.'.$authorise);
		if (empty($allowed)) {
			return;
		}
		$allowed = implode(',', array_keys($allowed));
		$cquery->where("tt.category_id IN ({$allowed})");
		$rquery->where("tt.category_id IN ({$allowed})");
		$cquery->where($hold);
		$rquery->where($hold);
		if ($userid) {
			$cquery->where("{$userfield}={$db->Quote($userid)}");
			$rquery->where("{$userfield}={$db->Quote($userid)}");
		}
		$time = $this->getState ( 'list.time' );
		if ($time == 0) {
			$time = KunenaFactory::getSession ()->lasttime;
		} elseif ($time > 0) {
			$time = JFactory::getDate ()->toUnix () - ($time * 3600);
		}
		// Negative time means no time
		if ($time > 0) {
			$cquery->where("m.time>{$db->Quote($time)}");
			$rquery->where("m.time>{$db->Quote($time)}");
		}

		$db->setQuery ( $cquery );
		$this->total = ( int ) $db->loadResult ();
		if (KunenaError::checkDatabaseError() || !$this->total) return;

		$db->setQuery ( $rquery, $start, $limit );
		$this->messages = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		$topicids = array();
		foreach ( $this->messages as $message ) {
			$topicids[$message->thread] = $message->thread;
		}
		$this->topics = KunenaForumTopicHelper::getTopics ( $topicids, $authorise );
		$this->_common();
	}

	protected function _common() {
		if ($this->total > 0) {
			$config = KunenaFactory::getConfig ();

			// collect user ids for avatar prefetch when integrated
			$userlist = array();
			foreach ( $this->topics as $topic ) {
				$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
				$userlist[intval($topic->last_post_userid)] = intval($topic->last_post_userid);
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			if ( !empty($userlist) ) KunenaUserHelper::loadUsers($userlist);

			KunenaForumTopicHelper::getUserTopics(array_keys($this->topics));
			KunenaForumTopicHelper::getKeywords(array_keys($this->topics));
			if ($config->shownew) {
				KunenaForumTopicHelper::fetchNewStatus($this->topics);
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
}