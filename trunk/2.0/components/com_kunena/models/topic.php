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
kimport('kunena.forum.message.helper');
kimport('kunena.user.helper');
kimport('kunena.forum.topic.poll.helper');

/**
 * Topic Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelTopic extends KunenaModel {
	protected $topics = false;
	protected $messages = false;
	protected $items = false;

	protected function populateState() {
		$app = JFactory::getApplication ();
		$me = KunenaUserHelper::get();
		$config = KunenaFactory::getConfig ();
		$active = $app->getMenu ()->getActive ();
		$active = $active ? (int) $active->id : 0;
		$layout = $this->getCmd ( 'layout', 'default' );

		$template = KunenaFactory::getTemplate();
		$profile_location = $template->params->get('avatarPosition', 'left');
		$profile_direction = $profile_location == 'left' || $profile_location == 'right' ? 'vertical' : 'horizontal';
		$this->setState ( 'profile.location', $profile_location );
		$this->setState ( 'profile.direction', $profile_direction );

		$catid = $this->getInt ( 'catid', 0 );
		$this->setState ( 'item.catid', $catid );

		$id = $this->getInt ( 'id', 0 );
		$this->setState ( 'item.id', $id );

		$id = $this->getInt ( 'mesid', 0 );
		$this->setState ( 'item.mesid', $id );

		$access = KunenaFactory::getAccessControl();
		$value = $access->getAllowedHold($me, $catid);
		$this->setState ( 'hold', $value );

		$value = $this->getInt ( 'limit', 0 );
		if ($value < 1) $value = $config->messages_per_page;
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.topic_{$active}_{$layout}_list_ordering", 'filter_order', 'time', 'cmd' );
		//$this->setState ( 'list.ordering', $value );

		$value = $this->getInt ( 'limitstart', 0 );
		if ($value < 0) $value = 0;
		$this->setState ( 'list.start', $value );

		//$value = $this->getUserStateFromRequest ( "com_kunena.topic_{$active}_{$layout}_list_direction", 'filter_order_Dir', 'desc', 'word' );
		if ($me->ordering != '0') {
			$value = $me->ordering == '1' ? 'desc' : 'asc';
		} else {
			$value = $config->default_sort == 'asc' ? 'asc' : 'desc';
		}
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );
	}

	public function getCategory() {
		return KunenaForumCategoryHelper::get($this->getState ( 'item.catid'));
	}

	public function getTopic() {
		$topic = KunenaForumTopicHelper::get($this->getState ( 'item.id'));
		$ids = array();
		// If topic has been moved, find the new topic
		while ($topic->moved_id) {
			if (isset($ids[$topic->moved_id])) {
				// Break on loops
				return false;
			}
			$ids[$topic->moved_id] = 1;
			$topic = KunenaForumTopicHelper::get($topic->moved_id);
		}
		// If topic doesn't exist, check if there's a message with the same id
		if (! $topic->exists()) {
			$message = KunenaForumMessageHelper::get($this->getState ( 'item.id'));
			if ($message->exists()) {
				$topic = KunenaForumTopicHelper::get($message->thread);
			}
		}
		return $topic;
	}

	public function getMessages() {
		if ($this->messages === false) {
			$this->messages = KunenaForumMessageHelper::getMessagesByTopic($this->getState ( 'item.id'),
				$this->getState ( 'list.start'), $this->getState ( 'list.limit'), $this->getState ( 'list.direction'), $this->getState ( 'hold'));

			// First collect the message ids of the first message and all replies
			$userlist = array();
			$ids = array();
			foreach($this->messages AS $message){
				$ids[$message->id] = $message->id;
				$userlist[intval($message->userid)] = intval($message->userid);
				$userlist[intval($message->modified_by)] = intval($message->modified_by);
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			KunenaUserHelper::loadUsers($userlist);

			// Get attachments
			KunenaForumMessageAttachmentHelper::getByMessage($this->messages);
		}

		return $this->messages;
	}

	public function getTotal() {
		$hold = $this->getState ( 'hold');
		if ($hold) {
			// FIXME:
			return $this->getTopic()->posts;
		}
		return $this->getTopic()->posts;
	}

	public function getModerators() {
		$moderators = $this->getCategory()->getModerators(false);
		if ( !empty($moderators) ) KunenaUserHelper::loadUsers($moderators);
		return $moderators;
	}

	public function getPolldata() {
		$data = KunenaForumTopicPollHelper::getPollData($this->getState ( 'item.id'));
		return $data;
	}

	public function getVoters() {
		$voters = KunenaForumTopicPollHelper::getTotalVoters($this->getState ( 'item.id'));
		return $voters;
	}

	public function getUsersvoted() {
		$usersvoted = KunenaForumTopicPollHelper::getUsersVotes($this->getState ( 'item.id'));
		return $usersvoted;
	}

	public function getUsersHasVoted() {
		$usersvoted = KunenaForumTopicPollHelper::userHasAlreadyVoted($this->getState ( 'item.id'));
		return $usersvoted;
	}
}