<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Models
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Topic Model for Kunena
 *
 * @since  2.0
 */
class KunenaModelTopic extends KunenaModel
{
	protected $topics = false;

	protected $messages = false;

	protected $items = false;

	protected $topic = false;

	/**
	 *
	 */
	protected function populateState()
	{
		$active = $this->app->getMenu()->getActive();
		$active = $active ? (int) $active->id : 0;

		$layout = $this->me->getTopicLayout();
		$this->setState('layout', $layout);

		$template          = KunenaFactory::getTemplate();
		$profile_location  = $template->params->get('avatarPosition', 'left');
		$profile_direction = $profile_location == 'left' || $profile_location == 'right' ? 'vertical' : 'horizontal';
		$this->setState('profile.location', $profile_location);
		$this->setState('profile.direction', $profile_direction);

		$catid = $this->getInt('catid', 0);
		$this->setState('item.catid', $catid);

		$id = $this->getInt('id', 0);
		$this->setState('item.id', $id);

		$id = $this->getInt('mesid', 0);
		$this->setState('item.mesid', $id);

		$access = KunenaAccess::getInstance();
		$value  = $access->getAllowedHold($this->me, $catid);
		$this->setState('hold', $value);

		$value = $this->getInt('limit', 0);

		if ($value < 1 || $value > 100)
		{
			$value = $this->config->messages_per_page;
		}

		$this->setState('list.limit', $value);

		// $value = $this->getUserStateFromRequest ( "com_kunena.topic_{$active}_{$layout}_list_ordering", 'filter_order', 'time', 'cmd' );
		// $this->setState ( 'list.ordering', $value );

		$value = $this->getInt('limitstart', 0);

		if ($value < 0)
		{
			$value = 0;
		}

		$this->setState('list.start', $value);

		$value = $this->getUserStateFromRequest("com_kunena.topic_{$active}_{$layout}_list_direction", 'filter_order_Dir', '', 'word');

		if (!$value)
		{
			if ($this->me->ordering != '0' && $this->me->exists())
			{
				$value = $this->me->ordering == '1' ? 'desc' : 'asc';
			}
			else
			{
				$value = $this->config->default_sort == 'asc' ? 'asc' : 'desc';
			}
		}

		if ($value != 'asc')
		{
			$value = 'desc';
		}

		$this->setState('list.direction', $value);
	}

	/**
	 * @return KunenaForumCategory
	 */
	public function getCategory()
	{
		return KunenaForumCategoryHelper::get($this->getState('item.catid'));
	}

	/**
	 * @return boolean|KunenaForumTopic
	 */
	public function getTopic()
	{
		if ($this->topic === false)
		{
			$mesid = $this->getState('item.mesid');

			if ($mesid)
			{
				// Find actual topic by fetching current message
				$message = KunenaForumMessageHelper::get($mesid);
				$topic   = KunenaForumTopicHelper::get($message->thread);
				$this->setState('list.start', intval($topic->getPostLocation($mesid) / $this->getState('list.limit')) * $this->getState('list.limit'));
			}
			else
			{
				$topic = KunenaForumTopicHelper::get($this->getState('item.id'));
				$ids   = array();

				// If topic has been moved, find the new topic
				while ($topic->moved_id)
				{
					if (isset($ids[$topic->moved_id]))
					{
						// Break on loops
						return false;
					}

					$ids[$topic->moved_id] = 1;
					$topic                 = KunenaForumTopicHelper::get($topic->moved_id);
				}

				// If topic doesn't exist, check if there's a message with the same id

				/*
				if (! $topic->exists()) {
					$message = KunenaForumMessageHelper::get($this->getState ( 'item.id'));
					if ($message->exists()) {
						$topic = KunenaForumTopicHelper::get($message->thread);
					}
				}*/
			}

			$this->topic = $topic;
		}

		return $this->topic;
	}

	/**
	 * @return array|boolean|KunenaForumMessage[]
	 */
	public function getMessages()
	{
		if ($this->messages === false)
		{
			$layout         = $this->getState('layout');
			$threaded       = ($layout == 'indented' || $layout == 'threaded');
			$this->messages = KunenaForumMessageHelper::getMessagesByTopic($this->getState('item.id'),
				$this->getState('list.start'), $this->getState('list.limit'), $this->getState('list.direction'), $this->getState('hold'), $threaded);

			// Get thankyous for all messages in the page
			$thankyous = KunenaForumMessageThankyouHelper::getByMessage($this->messages);

			// First collect ids and users
			$userlist       = array();
			$this->threaded = array();
			$location       = $this->getState('list.start');

			foreach ($this->messages AS $message)
			{
				$message->replynum = ++$location;

				if ($threaded)
				{
					// Threaded ordering
					if (isset($this->messages[$message->parent]))
					{
						$this->threaded[$message->parent][] = $message->id;
					}
					else
					{
						$this->threaded[0][] = $message->id;
					}
				}

				$userlist[intval($message->userid)]      = intval($message->userid);
				$userlist[intval($message->modified_by)] = intval($message->modified_by);

				$thankyou_list     = $thankyous[$message->id]->getList();
				$message->thankyou = array();

				if (!empty($thankyou_list))
				{
					$message->thankyou = $thankyou_list;
				}
			}

			if (!isset($this->messages[$this->getState('item.mesid')]) && !empty($this->messages))
			{
				$this->setState('item.mesid', reset($this->messages)->id);
			}

			if ($threaded)
			{
				if (!isset($this->messages[$this->topic->first_post_id]))
				{
					$this->messages = $this->getThreadedOrdering(0, array('edge'));
				}
				else
				{
					$this->messages = $this->getThreadedOrdering();
				}
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			KunenaUserHelper::loadUsers($userlist);

			// Get attachments
			KunenaAttachmentHelper::getByMessage($this->messages);
		}

		return $this->messages;
	}

	/**
	 * @param   int   $parent
	 * @param   array $indent
	 *
	 * @return array
	 */
	protected function getThreadedOrdering($parent = 0, $indent = array())
	{
		$list = array();

		if (count($indent) == 1 && $this->getTopic()->getTotal() > $this->getState('list.start') + $this->getState('list.limit'))
		{
			$last = -1;
		}
		else
		{
			$last = end($this->threaded[$parent]);
		}

		foreach ($this->threaded[$parent] as $mesid)
		{
			$message = $this->messages[$mesid];
			$skip    = $message->id != $this->topic->first_post_id && $message->parent != $this->topic->first_post_id && !isset($this->messages[$message->parent]);

			if ($mesid != $last)
			{
				// Default sibling edge
				$indent[] = 'crossedge';
			}
			else
			{
				// Last sibling edge
				$indent[] = 'lastedge';
			}

			end($indent);
			$key = key($indent);

			if ($skip)
			{
				$indent[] = 'gap';
			}

			$list[$mesid]         = $this->messages[$mesid];
			$list[$mesid]->indent = $indent;

			if (empty($this->threaded[$mesid]))
			{
				// No children node
				$list[$mesid]->indent[] = ($mesid == $message->thread) ? 'single' : 'leaf';
			}
			else
			{
				// Has children node
				$list[$mesid]->indent[] = ($mesid == $message->thread) ? 'root' : 'node';
			}

			if (!empty($this->threaded[$mesid]))
			{
				// Fix edges
				if ($mesid != $last)
				{
					$indent[$key] = 'edge';
				}
				else
				{
					$indent[$key] = 'empty';
				}

				if ($skip)
				{
					$indent[$key + 1] = 'empty';
				}

				$list += $this->getThreadedOrdering($mesid, $indent);
			}

			if ($skip)
			{
				array_pop($indent);
			}

			array_pop($indent);
		}

		return $list;
	}

	/**
	 * @return integer
	 */
	public function getTotal()
	{
		return $this->getTopic()->getTotal();
	}

	/**
	 * @return integer
	 */
	public function getMyVotes()
	{
		return $this->getPoll()->getMyVotes();
	}

	/**
	 * @return array
	 */
	public function getModerators()
	{
		$moderators = $this->getCategory()->getModerators(false);

		return $moderators;
	}

	/**
	 * @return KunenaForumTopicPoll
	 */
	public function getPoll()
	{
		return $this->getTopic()->getPoll();
	}

	/**
	 * @return integer
	 */
	public function getPollUserCount()
	{
		return $this->getPoll()->getUserCount();
	}

	/**
	 * @return array
	 */
	public function getPollUsers()
	{
		return $this->getPoll()->getUsers();
	}
}
