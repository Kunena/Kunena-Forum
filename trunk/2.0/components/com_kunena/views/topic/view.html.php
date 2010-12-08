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

kimport ( 'kunena.view' );

/**
 * Topic View
 */
class KunenaViewTopic extends KunenaView {
	function displayDefault($tpl = null) {
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		$this->assignRef ( 'topic', $this->get ( 'Topic' ) );
		if (! $this->category->authorise('read')) {
			$this->setError($this->category->getError());
		} elseif (! $this->topic->authorise('read')) {
			$this->setError($this->topic->getError());
		} else {
			$this->assignRef ( 'messages', $this->get ( 'Messages' ) );
			$this->assignRef ( 'total', $this->get ( 'Total' ) );
			$this->usertopic = $this->topic->getUserTopic();
		}
		$this->headerText =  JText::_('COM_KUNENA_MENU_LATEST_DESC');
		$this->title = JText::_('COM_KUNENA_ALL_DISCUSSIONS');
		// FIXME:
		$this->pagination = $this->getPagination ( $page=1, $totalpages=1, $maxpages=3 );
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$this->actions();
		$this->display($tpl);
	}

	function actions($tpl = null) {
		$catid = $this->state->get('item.catid');
		$id = $this->state->get('item.id');

		// Subscribe topic
		if ($this->usertopic->subscribed) {
			// this user is allowed to unsubscribe
			$this->thread_subscribe = CKunenaLink::GetTopicPostLink ( 'unsubscribe', $catid, $id, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_TOPIC') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNSUBSCRIBE_TOPIC_LONG') );
		} elseif ($this->topic->authorise('subscribe')) {
			// this user is allowed to subscribe
			$this->thread_subscribe = CKunenaLink::GetTopicPostLink ( 'subscribe', $catid, $id, CKunenaTools::showButton ( 'subscribe', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_TOPIC') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_SUBSCRIBE_TOPIC_LONG') );
		}

		// Favorite topic
		if ($this->usertopic->favorite) {
			// this user is allowed to unfavorite
			$this->thread_favorite = CKunenaLink::GetTopicPostLink ( 'unfavorite', $catid, $id, CKunenaTools::showButton ( 'favorite', JText::_('COM_KUNENA_BUTTON_UNFAVORITE_TOPIC') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_UNFAVORITE_TOPIC_LONG') );
		} elseif ($this->topic->authorise('favorite')) {
			// this user is allowed to add a favorite
			$this->thread_favorite = CKunenaLink::GetTopicPostLink ( 'favorite', $catid, $id, CKunenaTools::showButton ( 'favorite', JText::_('COM_KUNENA_BUTTON_FAVORITE_TOPIC') ), 'nofollow', 'kicon-button kbuttonuser btn-left', JText::_('COM_KUNENA_BUTTON_FAVORITE_TOPIC_LONG') );
		}

		// Reply topic
		if ($this->topic->authorise('reply')) {
			// this user is allowed to reply to this topic
			$this->thread_reply = CKunenaLink::GetTopicPostReplyLink ( 'reply', $catid, $this->topic->id, CKunenaTools::showButton ( 'reply', JText::_('COM_KUNENA_BUTTON_REPLY_TOPIC') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_REPLY_TOPIC_LONG') );
		}

		// New topic
		if ($this->category->authorise('topic.create')) {
			//this user is allowed to post a new topic
			$this->thread_new = CKunenaLink::GetPostNewTopicLink ( $catid, CKunenaTools::showButton ( 'newtopic', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC') ), 'nofollow', 'kicon-button kbuttoncomm btn-left', JText::_('COM_KUNENA_BUTTON_NEW_TOPIC_LONG') );
		}

		// Moderator specific stuff
		if ($this->category->authorise('moderate')) {
			if (!$this->topic->ordering) {
				$this->thread_sticky = CKunenaLink::GetTopicPostLink ( 'sticky', $catid, $id, CKunenaTools::showButton ( 'sticky', JText::_('COM_KUNENA_BUTTON_STICKY_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_STICKY_TOPIC_LONG') );
			} else {
				$this->thread_sticky = CKunenaLink::GetTopicPostLink ( 'unsticky', $catid, $id, CKunenaTools::showButton ( 'sticky', JText::_('COM_KUNENA_BUTTON_UNSTICKY_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_UNSTICKY_TOPIC_LONG') );
			}

			if (!$this->topic->locked) {
				$this->thread_lock = CKunenaLink::GetTopicPostLink ( 'lock', $catid, $id, CKunenaTools::showButton ( 'lock', JText::_('COM_KUNENA_BUTTON_LOCK_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_LOCK_TOPIC_LONG') );
			} else {
				$this->thread_lock = CKunenaLink::GetTopicPostLink ( 'unlock', $catid, $id, CKunenaTools::showButton ( 'lock', JText::_('COM_KUNENA_BUTTON_UNLOCK_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_UNLOCK_TOPIC_LONG') );
			}
			$this->thread_delete = CKunenaLink::GetTopicPostLink ( 'deletethread', $catid, $id, CKunenaTools::showButton ( 'delete', JText::_('COM_KUNENA_BUTTON_DELETE_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_DELETE_TOPIC_LONG') );
			$this->thread_moderate = CKunenaLink::GetTopicPostReplyLink ( 'moderatethread', $catid, $id, CKunenaTools::showButton ( 'moderate', JText::_('COM_KUNENA_BUTTON_MODERATE_TOPIC') ), 'nofollow', 'kicon-button kbuttonmod btn-left', JText::_('COM_KUNENA_BUTTON_MODERATE') );
		}
	}

	function displayPathway() {
		CKunenaTools::loadTemplate('/pathway.php');
	}

	function displayPoll() {
		if ($this->config->pollenabled == "1" && $this->topic->poll_id) {
			if ( $this->category->allow_polls ) {
				require_once (KPATH_SITE . DS . 'lib' .DS. 'kunena.poll.class.php');
				$kunena_polls = CKunenaPolls::getInstance();
				$kunena_polls->showPollbox();
			}
		}
	}

	function displayThreadActions($location=0) {
		static $locations = array('top', 'bottom');
		$location ^= 1;
		$this->goto = '<a name="forum'.$locations[$location].'"></a>';
		$this->goto .= CKunenaLink::GetSamePageAnkerLink ( 'forum'.$locations[$location], CKunenaTools::showIcon ( 'kforum'.$locations[$location], JText::_('COM_KUNENA_GEN_GOTO'.$locations[$location] ) ), 'nofollow', 'kbuttongoto');
		echo $this->loadTemplate('actions');
	}

	function displayForumJump() {
		if ($this->config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
	}

	function displayMessage($message) {
		return;

		$this->replynum += $this->replydir;
		$this->mmm ++;
		$message = new CKunenaViewMessage($this, $message);
		$message->display();
	}

	function getPagination($page, $totalpages, $maxpages) {
		$catid = $this->state->get('item.catid');
		$threadid = $this->state->get('item.id');
		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<ul class="kpagination">';
		$output .= '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';

		if ($startpage > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, 0, $this->config->messages_per_page, 1, '', $rel = 'follow' ) . '</li>';
			if ($startpage > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, ($i-1)*$this->config->messages_per_page, $this->config->messages_per_page, $i, '', $rel = 'follow' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetThreadPageLink ( 'view', $catid, $threadid, ($totalpages-1)*$this->config->messages_per_page, $this->config->messages_per_page, $totalpages, '', $rel = 'follow' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}
}