<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicPollDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicPollDisplay extends KunenaControllerDisplay
{
	public $me;

	public $category;

	/**
	 * @var KunenaForumTopic
	 */
	public $topic;

	public $poll;

	public $uri;

	/**
	 * Prepare poll display.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$this->topic = KunenaForumTopicHelper::get($this->input->getInt('id'));
		$this->category = $this->topic->getCategory();
		$this->config = KunenaFactory::getConfig();
		$this->me = KunenaUserHelper::getMyself();

		// Need to check if poll is allowed in this category.
		$this->topic->tryAuthorise('poll.read');

		$this->poll = $this->topic->getPoll();
		$this->usercount = $this->poll->getUserCount();
		$this->usersvoted = $this->poll->getUsers();
		$this->voted = $this->poll->getMyVotes();

		if (!empty($this->alwaysVote))
		{
			// Authorise forced vote.
			$this->topic->tryAuthorise('poll.vote');
			$this->name = 'Topic/Poll/Vote';
		}
		elseif (!$this->voted && $this->topic->isAuthorised('poll.vote'))
		{
			$this->name = 'Topic/Poll/Vote';
		}
		else
		{
			$this->name = 'Topic/Poll/Results';

			$this->show_title = true;

			$this->users_voted_list = array();
			$this->users_voted_morelist = array();

			if ($this->config->pollresultsuserslist && !empty($this->usersvoted))
			{
				$userids_votes = array();

				foreach ($this->usersvoted as $userid => $vote)
				{
					$userids_votes[] = $userid;
				}

				$loaded_users = KunenaUserHelper::loadUsers($userids_votes);

				$i = 0;

				foreach ($loaded_users as $userid => $user)
				{
					if ($i <= '4')
					{
						$this->users_voted_list[] = $loaded_users[$userid]->getLink();
					}
					else
					{
						$this->users_voted_morelist[] = $loaded_users[$userid]->getLink();
					}

					$i++;
				}
			}
		}

		$this->uri = "index.php?option=com_kunena&view=topic&layout=poll&catid={$this->category->id}&id={$this->topic->id}";
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->setTitle(JText::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaHtmlParser::parseText($this->poll->title));
	}
}
