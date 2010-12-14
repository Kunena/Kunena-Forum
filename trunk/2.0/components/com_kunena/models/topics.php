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
	protected $items = false;

	protected function populateState() {
		$app = JFactory::getApplication ();
		$params = $app->getPageParameters('com_kunena');
		$config = KunenaFactory::getConfig ();

		$layout = JRequest::getCmd ( 'layout', 'default' );
		$this->setState ( 'layout', $layout );

		$this->setState ( 'user', KunenaFactory::getUser(JRequest::getInt ( 'user', null ))->userid );
		$this->setState ( 'list.mode', JRequest::getCmd ( 'mode', 'latest' ) );

		$latestcategory = $params->get('topics_categories', explode ( ',', $config->latestcategory ));
		if (in_array(0, $latestcategory)) {
			$latestcategory = false;
		}
		$this->setState ( 'list.categories', $latestcategory );
		$this->setState ( 'list.categories.in', $params->get('topics_catselection', $config->latestcategory_in) );

		$value = $app->getUserStateFromRequest ( "com_kunena.topics.{$layout}.list.time", 'sel', $params->get('topics_time', $config->show_list_time), 'int' );
		// FIXME: last visit and all
		$this->setState ( 'list.time', $value );

		// List state information
		$value = $app->getUserStateFromRequest ( "com_kunena.topics.{$layout}.list.limit", 'limit', 0, 'int' );
		if ($value < 1) $value = $config->threads_per_page;
		$this->setState ( 'list.limit', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.topics.{$layout}.list.ordering", 'filter_order', 'time', 'cmd' );
		//$this->setState ( 'list.ordering', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.topics.{$layout}.list.start", 'limitstart', 0, 'int' );
		//$value = JRequest::getInt ( 'limitstart', 0 );
		$this->setState ( 'list.start', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.topics.{$layout}.list.direction", 'filter_order_Dir', 'desc', 'word' );
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
				// FIXME:
				$hold = 1;
				$where = 'AND tt.hold=1';
				break;
			case 'deleted' :
				// FIXME:
				$hold = 3;
				$where = 'AND tt.hold>1';
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
		$posted = false;
		$favorites = false;
		$subscriptions = false;
		// Set order by
		$orderby = "tt.last_post_time DESC";
		switch ($this->getState ( 'list.mode' )) {
			case 'posted' :
				$posts = true;
				$orderby = "ut.last_post_time DESC";
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
			if ($config->shownew) {
				KunenaForumTopicHelper::fetchNewStatus($this->topics);
			}
		}
	}

	public function getTotal() {
		if ($this->total === false) {
			$this->getTopics();
		}
		return $this->total;
	}
}