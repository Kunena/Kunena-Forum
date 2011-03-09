<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.model' );
kimport('kunena.forum.category.helper');
kimport('kunena.forum.topic.helper');
kimport('kunena.user.helper');

require_once KPATH_ADMIN . '/models/categories.php';

/**
 * Category Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelCategory extends KunenaAdminModelCategories {
	protected $topics = false;
	protected $pending = array();
	protected $items = false;
	protected $topicActions = false;
	protected $actionMove = false;

	protected function populateState() {
		$layout = $this->getCmd ( 'layout', 'default' );
		$this->setState ( 'layout', $layout );

		// Administrator state
		if ($layout == 'manage' || $layout == 'create' || $layout == 'edit') {
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

	public function getCategories() {
		if ( $this->items === false ) {
			$this->items = array();
			$this->me = KunenaFactory::getUser();
			$this->config = KunenaFactory::getConfig();
			$catid = $this->getState ( 'item.id' );
			$layout = $this->getState ( 'layout' );
			$flat = false;

			if ($layout == 'user') {
				$categories[0] = KunenaForumCategoryHelper::getSubscriptions();
				$flat = true;
			} elseif ($catid) {
				$categories[0] = KunenaForumCategoryHelper::getCategories($catid);
				if (empty($categories[0]))
					return array();
			} else {
				$categories[0] = KunenaForumCategoryHelper::getChildren();
			}

		if ($flat) {
			$allsubcats = $categories[0];
		} else {
			$allsubcats = KunenaForumCategoryHelper::getChildren(array_keys($categories [0]), 1);
		}
		if (empty ( $allsubcats ))
			return array();

		if ($this->config->shownew && $this->me->userid) {
			KunenaForumCategoryHelper::getNewTopics(array_keys($allsubcats));
		}

		$modcats = array ();
		$lastpostlist = array ();
		$userlist = array();
		$topiclist = array();

		foreach ( $allsubcats as $subcat ) {
			if ($flat || isset ( $categories [0] [$subcat->parent_id] )) {

				$last = $subcat->getLastPosted ();
				if ($last->last_topic_id) {
					$topiclist[$last->last_topic_id] = $last->last_topic_id;
					// collect user ids for avatar prefetch when integrated
					$userlist [(int)$last->last_post_userid] = (int)$last->last_post_userid;
					$lastpostlist [(int)$subcat->id] = (int)$last->last_post_id;
					$last->_last_post_location = $last->last_topic_posts;
				}

				if ($this->config->listcat_show_moderators) {
					$subcat->moderators = $subcat->getModerators ( false );
					$userlist += $subcat->moderators;
				}

				if ($this->me->isModerator ( $subcat->id ))
					$modcats [] = $subcat->id;
			}
			$categories [$subcat->parent_id] [] = $subcat;
		}
		KunenaForumTopicHelper::getTopics($topiclist);

		if ($this->me->ordering != 0) {
			$topic_ordering = $this->me->ordering == 1 ? true : false;
		} else {
			$topic_ordering = $this->config->default_sort == 'asc' ? false : true;
		}

		$this->pending = array ();
		if ($this->me->userid && count ( $modcats )) {
			$catlist = implode ( ',', $modcats );
			$db = JFactory::getDBO ();
			$db->setQuery ( "SELECT catid, COUNT(*) AS count
				FROM #__kunena_messages
				WHERE catid IN ({$catlist}) AND hold=1
				GROUP BY catid" );
			$pending = $db->loadAssocList ();
			KunenaError::checkDatabaseError();
			foreach ( $pending as $item ) {
				if ($item ['count'])
					$this->pending [$item ['catid']] = $item ['count'];
			}
		}
		// Fix last post position when user can see unapproved or deleted posts
		if ($lastpostlist && !$topic_ordering && $this->me->userid && $this->me->isModerator()) {
			KunenaForumMessageHelper::loadLocation($lastpostlist);
		}

		// Prefetch all users/avatars to avoid user by user queries during template iterations
		KunenaUserHelper::loadUsers($userlist);

		if ($flat) {
			$this->items = $allsubcats;
		} else {
			$this->items = $categories;
		}
		}

		return $this->items;
	}

	public function getUnapprovedCount() {
		return $this->pending;
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
				'hold'=>$hold,
				'moved'=>1);

			list($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($catid, $limitstart, $limit, $params);
			if ($this->total > 0) {
				// collect user ids for avatar prefetch when integrated
				$userlist = array();
				foreach ( $this->topics as $topic ) {
					$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
					$userlist[intval($topic->last_post_userid)] = intval($topic->last_post_userid);
					$lastpostlist[intval($topic->last_post_id)] = intval($topic->last_post_id);
				}

				// Prefetch all users/avatars to avoid user by user queries during template iterations
				if ( !empty($userlist) ) KunenaUserHelper::loadUsers($userlist);

				KunenaForumTopicHelper::getUserTopics(array_keys($this->topics));
				KunenaForumTopicHelper::getKeywords(array_keys($this->topics));
				$lastreadlist = array();
				if ($config->shownew) {
					$lastreadlist = KunenaForumTopicHelper::fetchNewStatus($this->topics);
				}

				// Fetch last / new post positions when user can see unapproved or deleted posts
				if (($lastpostlist || $lastreadlist) && $me->userid && $me->isModerator()) {
					KunenaForumMessageHelper::loadLocation($lastpostlist + $lastreadlist);
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
		$actionDropdown[] = JHTML::_('select.option', 'none', JText::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
		if ($move) $actionDropdown[] = JHTML::_('select.option', 'move', JText::_('COM_KUNENA_MOVE_SELECTED'));
		if ($approve) $actionDropdown[] = JHTML::_('select.option', 'approve', JText::_('COM_KUNENA_APPROVE_SELECTED'));
		if ($delete) $actionDropdown[] = JHTML::_('select.option', 'delete', JText::_('COM_KUNENA_DELETE_SELECTED'));
		if ($permdelete) $actionDropdown[] = JHTML::_('select.option', 'permdel', JText::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
		if ($undelete) $actionDropdown[] = JHTML::_('select.option', 'restore', JText::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));

		if (count($actionDropdown) == 1) return null;
		return $actionDropdown;
	}

	public function getActionMove() {
		return $this->actionMove;
	}

	public function getModerators() {
		$moderators = $this->getCategory()->getModerators(false);
		if ( !empty($moderators) ) KunenaUserHelper::loadUsers($moderators);
		return $moderators;
	}
}