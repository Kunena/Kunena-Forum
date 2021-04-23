<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Poll;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Topic\Poll\KunenaPoll;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use function defined;

/**
 * Class ComponentTopicControllerPollDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentTopicControllerPollDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var     KunenaCategory
	 * @since   Kunena 6.0
	 */
	public $category;

	/**
	 * @var     KunenaTopic
	 * @since   Kunena 6.0
	 */
	public $topic;

	/**
	 * @var     KunenaPoll
	 * @since   Kunena 6.0
	 */
	public $poll;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $uri;

	/**
	 * Prepare poll display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$this->topic    = KunenaTopicHelper::get($this->input->getInt('id'));
		$this->category = $this->topic->getCategory();
		$this->config   = KunenaFactory::getConfig();
		$this->me       = KunenaUserHelper::getMyself();

		// Need to check if poll is allowed in this category.
		$this->topic->tryAuthorise('poll.read');

		$this->poll = $this->topic->getPoll();
		$usercount  = $this->poll->getUserCount();
		$usersvoted = $this->poll->getUsers();

		if (is_object($this->poll->getMyVotes()))
		{
			$userhasvoted = $this->poll->getMyVotes();
		}
		else
		{
			$userhasvoted = 0;
		}

		if (!empty($this->alwaysVote))
		{
			// Authorise forced vote.
			$this->topic->tryAuthorise('poll.vote');
			$this->topic->tryAuthorise('reply');
			$this->name = 'Topic/Poll/Vote';
		}
		elseif (!$userhasvoted && $this->topic->isAuthorised('poll.vote') && $this->topic->isAuthorised('reply'))
		{
			$this->name = 'Topic/Poll/Vote';
		}
		else
		{
			$this->name = 'Topic/Poll/Results';

			$show_title = true;

			$users_voted_list     = [];
			$users_voted_morelist = [];

			if ($this->config->pollResultsUserslist && !empty($usersvoted))
			{
				$userids_votes = [];

				foreach ($usersvoted as $userid => $vote)
				{
					$userids_votes[] = $userid;
				}

				$loaded_users = KunenaUserHelper::loadUsers($userids_votes);

				$i = 0;

				foreach ($loaded_users as $userid => $user)
				{
					if ($i <= '4')
					{
						$users_voted_list[] = $loaded_users[$userid]->getLink();
					}
					else
					{
						$users_voted_morelist[] = $loaded_users[$userid]->getLink();
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
	 * @return  void|boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument(): bool
	{
		$menu_item = $this->app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaParser::parseText($this->poll->title));
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription(Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaParser::parseText($this->poll->title));
			}
		}
	}
}
