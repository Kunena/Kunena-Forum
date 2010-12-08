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
		$config = KunenaFactory::getConfig ();
		$layout = JRequest::getCmd ( 'layout', 'default' );

		$latestcategory = explode ( ',', $config->latestcategory );
		if (in_array(0, $latestcategory)) {
			$latestcategory = false;
		}
		$latestcategory_in = $config->latestcategory_in;
		$this->setState ( 'list.categories', $latestcategory );
		$this->setState ( 'list.categories.in', $latestcategory_in );

		$value = $app->getUserStateFromRequest ( "com_kunena.topics.{$layout}.list.time", 'sel', $config->show_list_time, 'int' );
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
			$catid = $this->getState ( 'item.id');
			$limitstart = $this->getState ( 'list.start');
			$limit = $this->getState ( 'list.limit');
			$time = $this->getState ( 'list.time');
			if ($time < 0) {
				$time = 0;
			} elseif ($time == 0) {
				$time = KunenaFactory::getSession ()->lasttime;
			} else {
				$time = JFactory::getDate()->toUnix() - ($time * 3600);
			}

			$latestcategory = $this->getState ( 'list.categories' );
			$latestcategory_in = $this->getState ( 'list.categories.in' );

			$config = KunenaFactory::getConfig ();
			$access = KunenaFactory::getAccessControl();
			$me = KunenaUserHelper::get();
			$hold = $access->getAllowedHold($me, $catid);

			// FIXME:
			$lastpost = true;
			$where = '';

			$params = array(
				'reverse'=>!$latestcategory_in,
				'orderby'=>$lastpost ? 'tt.last_post_time DESC' : 'tt.first_post_time DESC',
				'starttime'=>$time,
				'hold'=>0,
				'where'=>$where);

			list ($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($latestcategory, $limitstart, $limit, $params);

			if ($this->total > 0) {
				// collect user ids for avatar prefetch when integrated
				$userlist = array();
				$routerlist = array ();
				foreach ( $this->topics as $topic ) {
					$routerlist [$topic->id] = $topic->subject;
					if ($topic->ordering) $this->highlight++;
					$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
					$userlist[intval($topic->last_post_userid)] = intval($topic->last_post_userid);
				}
				require_once KPATH_SITE . '/router.php';
				KunenaRouter::loadMessages ( $routerlist );

				// Prefetch all users/avatars to avoid user by user queries during template iterations
				if ( !empty($userlist) ) KunenaUserHelper::loadUsers($userlist);

				KunenaForumTopicHelper::getUserTopics(array_keys($this->topics));
				if ($config->shownew) {
					KunenaForumTopicHelper::fetchNewStatus($this->topics);
				}
			}
		}
		return $this->topics;
	}

	public function getTotal() {
		if ($this->total === false) {
			$this->getTopics();
		}
		return $this->total;
	}
}