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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Controller\BaseController;

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
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/topics.php';
		$this->model = new KunenaModelTopics(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state    = $this->model->getState();
		$this->me       = KunenaUserHelper::getMyself();
		$this->moreUri  = null;
		$holding        = $this->getOptions()->get('topics_deletedtopics');
		$this->embedded = $this->getOptions()->get('embedded', true);

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');

		$Itemid = $this->input->getInt('Itemid');
		$format = $this->input->getCmd('format');

		if (!$Itemid && $format != 'feed' && KunenaConfig::getInstance()->sef_redirect)
		{
			if (KunenaConfig::getInstance()->topiclist_id)
			{
				$itemidfix = KunenaConfig::getInstance()->topiclist_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=topics&mode={$this->state->get('list.mode')}"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topics&mode={$this->state->get('list.mode')}&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

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

		if (!isset($categoryIds))
		{
			$categoryIds = false;
		}

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

		$doc = Factory::getDocument();

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

		$params      = Factory::getApplication()->getMenu()->getActive()->params;
		$title       = $params->get('page_title');
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
					$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
				}

				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&mode=topics');
				break;
			case 'sticky' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_STICKY');
				}

				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&mode=sticky');
				break;
			case 'locked' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_LOCKED');
				}

				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&mode=locked');
				break;
			case 'noreplies' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_NOREPLIES');
				}

				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&mode=noreplies');
				break;
			case 'unapproved' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_UNAPPROVED');
				}

				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&mode=unapproved');
				break;
			case 'deleted' :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_DELETED');
				}

				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&mode=deleted');
				break;
			case 'replies' :
			default :
				if (!empty($title) && $pageheading)
				{
					$this->headerText = $title;
				}
				else
				{
					$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_DEFAULT_MODE_TOPICS');
				}

				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&mode=replies');
				break;
		}

		$doc = Factory::getDocument();

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
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$page       = $this->pagination->pagesCurrent;
		$total      = $this->pagination->pagesTotal;
		$headerText = $this->headerText . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');

		$config    = Factory::getConfig();
		$robots    = $config->get('robots');
		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		$this->setMetaData('og:url', Uri::current(), 'property');

		if (JFile::exists(JPATH_SITE . '/' . KunenaConfig::getInstance()->emailheader))
		{
			$image = Uri::base() . KunenaConfig::getInstance()->emailheader;
			$this->setMetaData('og:image', $image, 'property');
			$this->setMetaData('twitter:image', $image, 'property');
		}

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
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title') . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setTitle($title);
			}
			else
			{
				$this->title = $this->headerText;
				$this->setTitle($headerText);
			}

			$this->setMetaData('og:type', 'article', 'property');
			$this->setMetaData('og:description', $headerText, 'property');
			$this->setMetaData('og:title', $headerText, 'property');

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
				$description = $params->get('menu-meta_description') . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
				$this->setDescription($description);
			}
			else
			{
				$description = Text::_('COM_KUNENA_ALL_DISCUSSIONS') . ': ' . $this->config->board_title . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
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
