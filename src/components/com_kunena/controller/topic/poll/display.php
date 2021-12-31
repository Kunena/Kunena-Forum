<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerTopicPollDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicPollDisplay extends KunenaControllerDisplay
{
	/**
	 * @var
	 * @since Kunena
	 */
	public $me;

	/**
	 * @var
	 * @since Kunena
	 */
	public $category;

	/**
	 * @var KunenaForumTopic
	 * @since Kunena
	 */
	public $topic;

	/**
	 * @var
	 * @since Kunena
	 */
	public $poll;

	/**
	 * @var
	 * @since Kunena
	 */
	public $uri;

	/**
	 * Prepare poll display.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$this->topic    = KunenaForumTopicHelper::get($this->input->getInt('id'));
		$this->category = $this->topic->getCategory();
		$this->config   = KunenaFactory::getConfig();
		$this->me       = KunenaUserHelper::getMyself();

		// Need to check if poll is allowed in this category.
		$this->topic->tryAuthorise('poll.read');

		$this->poll       = $this->topic->getPoll();
		$this->usercount  = $this->poll->getUserCount();
		$this->usersvoted = $this->poll->getUsers();

		if (is_object($this->poll->getMyVotes()))
		{
			$this->userhasvoted = $this->poll->getMyVotes();
		}
		else
		{
			$this->userhasvoted = 0;
		}

		$datenow = new \Joomla\CMS\Date\Date('now');
		$datepolltimetolive = new \Joomla\CMS\Date\Date($this->poll->polltimetolive);

		if (!empty($this->poll->polltimetolive))
		{
			if ($datepolltimetolive < $datenow)
			{
				$this->polllifespan = true;
			}
 			else
			{
				$this->polllifespan = false;
			}
		}

		if (!empty($this->alwaysVote))
		{
			// Authorise forced vote.
			$this->topic->tryAuthorise('poll.vote');
			$this->topic->tryAuthorise('reply');
			$this->name = 'Topic/Poll/Vote';
		}
		elseif (!$this->userhasvoted && $this->topic->isAuthorised('poll.vote') && $this->topic->isAuthorised('reply'))
		{
			$this->name = 'Topic/Poll/Vote';
		}
		else
		{
			$this->name = 'Topic/Poll/Results';

			$this->show_title = true;

			$this->users_voted_list     = array();
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
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaHtmlParser::parseText($this->poll->title));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$this->setKeywords(Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaHtmlParser::parseText($this->poll->title));
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription(Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaHtmlParser::parseText($this->poll->title));
			}
		}
	}
}
