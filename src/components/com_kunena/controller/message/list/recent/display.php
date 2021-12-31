<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Message
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerMessageListRecentDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerMessageListRecentDisplay extends ComponentKunenaControllerTopicListDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Message/List';

	/**
	 * @var array|KunenaForumMessage[]
	 * @since Kunena
	 */
	public $messages;

	/**
	 * Prepare category list display.
	 *
	 * @return void
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/topics.php';
		$this->model = new KunenaModelTopics(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state   = $this->model->getState();
		$this->me      = KunenaUserHelper::getMyself();
		$this->moreUri = null;

		$this->embedded = $this->getOptions()->get('embedded', false);

		if ($this->embedded)
		{
			$this->moreUri = new \Joomla\CMS\Uri\Uri('index.php?option=com_kunena&view=topics&layout=posts&mode=' . $this->state->get('list.mode')
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
			$time = new \Joomla\CMS\Date\Date(KunenaFactory::getSession()->lasttime);
		}
		else
		{
			$time = new \Joomla\CMS\Date\Date(Factory::getDate()->toUnix() - ($time * 3600));
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

		$finder = new KunenaForumMessageFinder;
		$finder->filterByTime($time);

		switch ($this->state->get('list.mode'))
		{
			case 'unapproved' :
				$authorise = 'topic.post.approve';
				$finder
					->filterByUser(null, 'author')
					->filterByHold(array(1));
				break;
			case 'deleted' :
				$authorise = 'topic.post.undelete';
				$finder
					->filterByUser($user, 'author')
					->filterByHold(array(2, 3));
				break;
			case 'mythanks' :
				$finder
					->filterByUser($user, 'thanker')
					->filterByHold(array(0));
				break;
			case 'thankyou' :
				$finder
					->filterByUser($user, 'thankee')
					->filterByHold(array(0));
				break;
			default :
				$finder
					->filterByUser($user, 'author')
					->filterByHold(array(0));
				break;
		}

		$categories = KunenaForumCategoryHelper::getCategories($categoryIds, $reverse, $authorise);
		$finder->filterByCategories($categories);

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

		$doc = Factory::getDocument();

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
		$topicIds = array();

		foreach ($this->messages as $message)
		{
			$topicIds[(int) $message->thread] = (int) $message->thread;
		}

		$this->topics = KunenaForumTopicHelper::getTopics($topicIds, 'none');

		$userIds = $mesIds = array();

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
				$this->messagemore = 'COM_KUNENA_X_MESSAGES_MORE';
				$actions          = array('approve', 'delete', 'move', 'permdelete');
				break;
			case 'deleted':
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DELETED');
				$this->messagemore = 'COM_KUNENA_X_MESSAGES_MORE';
				$actions          = array('undelete', 'delete', 'move', 'permdelete');
				break;
			case 'mythanks':
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_MYTHANKS');
				$this->messagemore = 'COM_KUNENA_X_MESSAGES_MORE';
				$actions          = array('approve', 'delete', 'permdelete');
				break;
			case 'thankyou':
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_THANKYOU');
				$this->messagemore = 'COM_KUNENA_PROFILE_THANKYOU_GIVEN_TIMES';
				$actions          = array('approve', 'delete', 'permdelete');
				break;
			case 'recent':
			default:
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_POSTS_MODE_DEFAULT');
				$this->messagemore = 'COM_KUNENA_X_MESSAGES_MORE';

				$app  = \Joomla\CMS\Factory::getApplication();
				$view = $app->input->get('view');

				if ($view == 'user')
				{
				    $userName              = $user->getName();
					$charMapApostropheOnly = array('s', 'S', 'z', 'Z');

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

				$actions = array('approve', 'delete', 'move', 'permdelete');
		}

		$this->actions = $this->getMessageActions($this->messages, $actions);
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$page  = $this->pagination->pagesCurrent;
		$total = $this->pagination->pagesTotal;
		$user  = KunenaUserHelper::get($this->state->get('user'));

		$headerText      = $this->headerText . ' ' . Text::_('COM_KUNENA_FROM') . ' ' . $user->getName() . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
		$app             = Factory::getApplication();
		$menu_item       = $app->getMenu()->getActive();
		$config          = Factory::getApplication('site');
		$componentParams = $config->getParams('com_config');
		$robots          = $componentParams->get('robots');

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
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
			    $keywords = $this->config->board_title . ', ' . $user->getName();
				$this->setKeywords($keywords);
			}
			elseif (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title;
				$this->setKeywords($keywords);
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
		}

		if ($this->state->get('list.mode') == 'latest' && !empty($this->state->get('user')))
		{
			$this->setMetaData('robots', 'follow, noindex');
		}
		else
		{
			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$this->setMetaData('robots', $robots);
			}
			else
			{
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
			}
		}
	}
}
