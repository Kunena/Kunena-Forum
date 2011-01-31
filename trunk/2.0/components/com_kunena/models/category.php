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

require_once KPATH_ADMIN . '/models/category.php';

/**
 * Category Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelCategory extends KunenaAdminModelCategory {
	protected $topics = false;
	protected $items = false;

	protected function populateState() {
		$layout = $this->getCmd ( 'layout', 'default' );
		$this->setState ( 'layout', $layout );

		// Administrator state
		if ($layout == 'create' || $layout == 'edit') {
			return parent::populateState();
		}

		$app = JFactory::getApplication ();
		$config = KunenaFactory::getConfig ();
		$me = KunenaUserHelper::get();

		$active = $app->getMenu ()->getActive ();
		$active = $active ? (int) $active->id : 0;
		$catid = $this->getInt ( 'catid', 0 );
		$this->setState ( 'item.id', $catid );

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.category{$catid}_list_limit", 'limit', 0, 'int' );
		if ($value < 1) $value = $config->threads_per_page;
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.category{$catid}_{$active}_list_ordering", 'filter_order', 'time', 'cmd' );
		//$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.category{$catid}_list_start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.category{$catid}_{$active}_list_direction", 'filter_order_Dir', 'desc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );
	}

	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->getState ( 'item.id'));
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


			$config = KunenaFactory::getConfig ();
			$access = KunenaFactory::getAccessControl();
			$me = KunenaUserHelper::get();
			$hold = $access->getAllowedHold($me, $catid);
			$params = array(
				'orderby'=>'tt.ordering DESC, tt.last_post_time ' . strtoupper($this->getState ( 'list.direction')),
				'hold'=>$hold);

			list($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($catid, $limitstart, $limit, $params);
			if ($this->total > 0) {
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
		return $this->topics;
	}

	public function getTotal() {
		if ($this->total === false) {
			$this->getTopics();
		}
		return $this->total;
	}

	public function getModerators() {
		$moderators = $this->getCategory()->getModerators(false);
		if ( !empty($moderators) ) KunenaUserHelper::loadUsers($moderators);
		return $moderators;
	}
}