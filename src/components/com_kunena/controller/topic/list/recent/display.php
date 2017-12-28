<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerTopicListRecentDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicListRecentDisplay extends ComponentKunenaControllerTopicListDisplay
{
	/**
	 * Prepare recent topics list.
	 *
	 * @return void
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
		$holding       = $this->getOptions()->get('topics_deletedtopics');

		$this->embedded = $this->getOptions()->get('embedded', true);

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');

		// Handle &sel=x parameter.
		$time = $this->state->get('list.time');

		if ($time < 0)
		{
			$time = null;
		}
		elseif ($time == 0)
		{
			$time = new JDate(KunenaFactory::getSession()->lasttime);
		}
		else
		{
			$time = new JDate(JFactory::getDate()->toUnix() - ($time * 3600));
		}

		if ($holding)
		{
			$hold = '0,2,3';
		}
		else
		{
			$hold = '0';
		}

		// Get categories for the filter.
		$categoryIds = $this->state->get('list.categories');
		$reverse     = !$this->state->get('list.categories.in');
		$authorise   = 'read';
		$order       = 'last_post_time';

		$finder = new KunenaForumTopicFinder;
		$finder->filterByMoved(false);

		switch ($this->state->get('list.mode'))
		{
			case 'topics' :
				$order = 'first_post_time';
				$finder
					->filterByHold(array($hold))
					->filterByTime($time, null, false);
				break;
			case 'sticky' :
				$finder
					->filterByHold(array($hold))
					->where('a.ordering', '>', 0)
					->filterByTime($time);
				break;
			case 'locked' :
				$finder
					->filterByHold(array($hold))
					->where('a.locked', '>', 0)
					->filterByTime($time);
				break;
			case 'noreplies' :
				$finder
					->filterByHold(array($hold))
					->where('a.posts', '=', 1)
					->filterByTime($time);
				break;
			case 'unapproved' :
				$authorise = 'topic.approve';
				$finder
					->filterByHold(array(1))
					->filterByTime($time);
				break;
			case 'deleted' :
				$authorise = 'topic.undelete';
				$finder
					->filterByHold(array(2, 3))
					->filterByTime($time);
				break;
			case 'replies' :
			default :
				$finder
					->filterByHold(array($hold))
					->filterByTime($time);
				break;
		}

		$categories = KunenaForumCategoryHelper::getCategories($categoryIds, $reverse, $authorise);
		$finder->filterByCategories($categories);

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

		$doc = JFactory::getDocument();

		$page = $this->pagination->pagesCurrent;

		$pagdata = $this->pagination->getData();

		if ($pagdata->previous->link)
		{
			$pagdata->previous->link = str_replace('?limitstart=0', '', $pagdata->previous->link);
			$doc->addHeadLink($pagdata->previous->link, 'prev');
		}

		if ($pagdata->next->link)
		{
			$doc->addHeadLink($pagdata->next->link, 'next');
		}

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

		$this->topics = $finder
			->order($order, -1)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();

		if ($this->topics)
		{
			$this->prepareTopics();
		}

		$actions = array('delete', 'approve', 'undelete', 'move', 'permdelete');

		$params = JFactory::getApplication()->getMenu()->getActive()->params;
		$title = $params->get('page_title');
		$pageheading = $params->get('show_page_heading');

		switch ($this->state->get('list.mode'))
		{
			case 'topics' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
				}

				$canonicalUrl = 'index.php?option=com_kunena&view=topics&mode=topics';
				break;
			case 'sticky' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_STICKY');
				}

				$canonicalUrl = 'index.php?option=com_kunena&view=topics&mode=sticky';
				break;
			case 'locked' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_LOCKED');
				}

				$canonicalUrl = 'index.php?option=com_kunena&view=topics&mode=locked';
				break;
			case 'noreplies' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_NOREPLIES');
				}

				$canonicalUrl = 'index.php?option=com_kunena&view=topics&mode=noreplies';
				break;
			case 'unapproved' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_UNAPPROVED');
				}

				$canonicalUrl = 'index.php?option=com_kunena&view=topics&mode=unapproved';
				break;
			case 'deleted' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DELETED');
				}

				$canonicalUrl = 'index.php?option=com_kunena&view=topics&mode=deleted';
				break;
			case 'replies' :
			default :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = JText::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
				}

				$canonicalUrl = 'index.php?option=com_kunena&view=topics&mode=replies';
			break;
		}

		$doc = JFactory::getDocument();

		foreach ($doc->_links as $key => $value)
		{
			if (is_array($value))
			{
				if (array_key_exists('relation', $value))
				{
					if ($value['relation'] == 'canonical')
					{
						$doc->_links[$canonicalUrl] = $value;
						unset($doc->_links[$key]);
						break;
					}
				}
			}
		}

		$this->actions = $this->getTopicActions($this->topics, $actions);
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$page = $this->pagination->pagesCurrent;
		$total = $this->pagination->pagesTotal;
		$headerText = $this->headerText . ($total > 1 && $page > 1 ? " - " . JText::_('COM_KUNENA_PAGES') . " {$page}" : '');
		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive();

		$config = JFactory::getConfig();
		$robots = $config->get('robots');

		if ($robots == '')
		{
			$doc->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow')
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$doc->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$doc->setMetaData('robots', 'nofollow, noindex');
		}

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title') . ($total > 1 && $page > 1 ? " - " . JText::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setTitle($title);
			}
			else
			{
				$this->title = $this->headerText;
				$this->setTitle($headerText);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title;
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description') . ($total > 1 && $page > 1 ? " - " . JText::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::_('COM_KUNENA_ALL_DISCUSSIONS') . ': ' . $this->config->board_title . ($total > 1 && $page > 1 ? " - " . JText::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$doc->setMetaData('robots', $robots);
			}
		}
	}
}
