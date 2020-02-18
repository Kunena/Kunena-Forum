<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Message
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Message\Kunenalist\Recent;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\Message;
use Kunena\Forum\Libraries\Forum\Message\MessageFinder;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Model\TopicsModel;
use function defined;

/**
 * Class ComponentKunenaControllerMessageListRecentDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerMessageListRecentDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Message/List';

	/**
	 * @var     array|Message[]
	 * @since   Kunena 6.0
	 */
	public $messages;

	/**
	 * Prepare category list display.
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

		$this->model = new TopicsModel([], $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state   = $this->model->getState();
		$this->me      = KunenaUserHelper::getMyself();
		$this->moreUri = null;

		$this->embedded = $this->getOptions()->get('embedded', false);

		if ($this->embedded)
		{
			$this->moreUri = new Uri('index.php?option=com_kunena&view=topics&layout=posts&mode=' . $this->state->get('list.mode')
				. '&userid=' . $this->state->get('user') . '&limit=' . $this->state->get('list.limit')
			);
			$this->moreUri->setVar('Itemid', KunenaRoute::getItemID($this->moreUri));
		}

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');
		$view  = $this->state->get('view');

		// Handle &sel=x parameter.
		$time = $this->state->get('list.time');

		if ($time < 0)
		{
			$time = null;
		}
		elseif ($time == 0)
		{
			$time = new Date(KunenaFactory::getSession()->lasttime);
		}
		else
		{
			$time = new Date(Factory::getDate()->toUnix() - ($time * 3600));
		}

		$userid = $this->state->get('user');

		if ($userid == '*')
		{
			$userid = null;
		}

		$user = is_numeric($userid) ? KunenaUserHelper::get($userid) : null;

		// Get categories for the filter.
		$categoryIds = $this->state->get('list.categories');
		$reverse     = !$this->state->get('list.categories.in');
		$authorise   = 'read';
		$order       = 'time';

		$finder = new MessageFinder;
		$finder->filterByTime($time);

		switch ($this->state->get('list.mode'))
		{
			case 'unapproved' :
				$authorise = 'topic.post.approve';
				$finder
					->filterByUser(null, 'author')
					->filterByHold([1]);
				break;
			case 'deleted' :
				$authorise = 'topic.post.undelete';
				$finder
					->filterByUser($user, 'author')
					->filterByHold([2, 3]);
				break;
			case 'mythanks' :
				$finder
					->filterByUser($user, 'thanker')
					->filterByHold([0]);
				break;
			case 'thankyou' :
				$finder
					->filterByUser($user, 'thankee')
					->filterByHold([0]);
				break;
			default :
				$finder
					->filterByUser($user, 'author')
					->filterByHold([0]);
				break;
		}

		$categories = \Kunena\Forum\Libraries\Forum\Category\CategoryHelper::getCategories($categoryIds, $reverse, $authorise);
		$finder->filterByCategories($categories);

		$this->pagination = new Pagination($finder->count(), $start, $limit);

		$doc = Factory::getApplication()->getDocument();

		if (!$start)
		{
			foreach ($doc->_links as $key => $value)
			{
				if (is_array($value))
				{
					if (array_key_exists('relation', $value))
					{
						if ($value['relation'] == 'canonical')
						{
							$canonicalUrl               = KunenaRoute::_();
							$doc->_links[$canonicalUrl] = $value;
							unset($doc->_links[$key]);
							break;
						}
					}
				}
			}
		}

		$pagdata = $this->pagination->getData();

		if ($pagdata->previous->link)
		{
			$pagdata->previous->link = str_replace('limitstart=0', '', $pagdata->previous->link);
			$doc->addHeadLink($pagdata->previous->link, 'prev');
		}

		if ($pagdata->next->link)
		{
			$doc->addHeadLink($pagdata->next->link, 'next');
		}

		$page = $this->pagination->pagesCurrent;

		if ($page > 1)
		{
			foreach ($doc->_links as $key => $value)
			{
				if (is_array($value))
				{
					if (array_key_exists('relation', $value))
					{
						if ($value['relation'] == 'canonical')
						{
							$canonicalUrl               = KunenaRoute::_();
							$doc->_links[$canonicalUrl] = $value;
							unset($doc->_links[$key]);
							break;
						}
					}
				}
			}
		}

		if ($this->moreUri)
		{
			$this->pagination->setUri($this->moreUri);
		}

		$this->messages = $finder
			->order($order, -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		// Load topics...
		$topicIds = [];

		foreach ($this->messages as $message)
		{
			$topicIds[(int) $message->thread] = (int) $message->thread;
		}

		$this->topics = \Kunena\Forum\Libraries\Forum\Topic\TopicHelper::getTopics($topicIds, 'none');

		$userIds = $mesIds = [];

		foreach ($this->messages as $message)
		{
			$userIds[(int) $message->userid] = (int) $message->userid;
			$mesIds[(int) $message->id]      = (int) $message->id;
		}

		if ($this->topics)
		{
			$this->prepareTopics($userIds, $mesIds);
		}

		switch ($this->state->get('list.mode'))
		{
			case 'unapproved':
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_UNAPPROVED');
				$actions          = ['approve', 'delete', 'move', 'permdelete'];
				break;
			case 'deleted':
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DELETED');
				$actions          = ['undelete', 'delete', 'move', 'permdelete'];
				break;
			case 'mythanks':
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_MYTHANKS');
				$actions          = ['approve', 'delete', 'permdelete'];
				break;
			case 'thankyou':
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_THANKYOU');
				$actions          = ['approve', 'delete', 'permdelete'];
				break;
			case 'recent':
			default:
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT');

				$view = $this->input->get('view');

				if ($view == 'user')
				{
					$userName              = $user->getName();
					$charMapApostropheOnly = ['s', 'S', 'z', 'Z'];

					if (in_array(substr($userName, -1), $charMapApostropheOnly))
					{
						$userName2 = "";
					}
					else
					{
						$userName2 = "'s ";
					}

					$this->headerText = Text::sprintf(Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT_NEW'), $userName, $userName2);
				}

				$actions = ['approve', 'delete', 'move', 'permdelete'];
		}

		$this->actions = $this->getMessageActions($this->messages, $actions);
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument()
	{
		$page  = $this->pagination->pagesCurrent;
		$total = $this->pagination->pagesTotal;
		$user  = KunenaUserHelper::get($this->state->get('user'));

		$headerText      = $this->headerText . ' ' . Text::_('COM_KUNENA_FROM') . ' ' . $user->getName() . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
		$menu_item       = $this->app->getMenu()->getActive();
		$componentParams = ComponentHelper::getParams('com_config');
		$robots          = $componentParams->get('robots');

		if ($robots == 'noindex, follow')
		{
			$this->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$this->setMetaData('robots', 'index, nofollow');
		}
		elseif ($robots == 'noindex, nofollow')
		{
			$this->setMetaData('robots', 'noindex, nofollow');
		}
		else
		{
			$this->setMetaData('robots', 'index, follow');
		}

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if ($this->state->get('list.mode') == 'latest' && !empty($this->state->get('user')))
			{
				$this->setTitle($headerText);
			}
			elseif (!empty($params_title))
			{
				$keywords = $params->get('menu-title');
				$this->setTitle($keywords);
			}
			else
			{
				$this->title = $this->headerText . ' ' . Text::_('COM_KUNENA_ON') . ' ' . $menu_item->title;
				$this->setTitle($this->title);
			}

			if ($this->state->get('list.mode') == 'latest' && !empty($this->state->get('user')))
			{
				$this->setDescription($headerText);
			}
			elseif (!empty($params_description))
			{
				$description = $params->get('menu-meta_description') . ' ' . Text::_('COM_KUNENA_ON') . ' ' . $menu_item->title . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setDescription($description);
			}
			else
			{
				$description = $this->headerText . ' ' . Text::_('COM_KUNENA_ON') . ' ' . $menu_item->title . ': ' . $this->config->board_title . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$this->setMetaData('robots', $robots);
			}
		}
	}
}
