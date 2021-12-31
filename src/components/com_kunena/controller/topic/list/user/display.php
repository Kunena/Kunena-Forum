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
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class ComponentKunenaControllerTopicListUserDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerTopicListUserDisplay extends ComponentKunenaControllerTopicListDisplay
{
	/**
	 * Prepare user's topic list.
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
		$this->state   = $this->model->getState();
		$this->me      = KunenaUserHelper::getMyself();
		$this->moreUri = null;

		$this->embedded = $this->getOptions()->get('embedded', true);

		if ($this->embedded)
		{
			$this->moreUri = new \Joomla\CMS\Uri\Uri('index.php?option=com_kunena&view=topics&layout=user&mode=' .
				$this->state->get('list.mode') . '&userid=' . $this->state->get('user') . '&limit=' . $this->state->get('list.limit')
			);
			$this->moreUri->setVar('Itemid', KunenaRoute::getItemID($this->moreUri));
		}

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
			$time = new \Joomla\CMS\Date\Date(KunenaFactory::getSession()->lasttime);
		}
		else
		{
			$time = new \Joomla\CMS\Date\Date(Factory::getDate()->toUnix() - ($time * 3600));
		}

		$holding = $this->getOptions()->get('topics_deletedtopics');

		if ($holding)
		{
			$hold = '0,2,3';
		}
		else
		{
			$hold = '0';
		}

		$this->user = KunenaUserHelper::get($this->state->get('user'));

		// Get categories for the filter.
		$categoryIds = $this->state->get('list.categories');
		$reverse     = !$this->state->get('list.categories.in');
		$authorise   = 'read';
		$order       = 'last_post_time';

		$finder = new KunenaForumTopicFinder;
		$finder
			->filterByMoved(false)
			->filterByHold(array($hold))
			->filterByTime($time);

		switch ($this->state->get('list.mode'))
		{
			case 'posted' :
				$finder
				->filterByUser($this->user, 'posted')
					->order('last_post_id', -1, 'ut');
				break;

			case 'started' :
			    $finder->filterByUser($this->user, 'owner');
				break;

			case 'favorites' :
			    $finder->filterByUser($this->user, 'favorited');
				break;

			case 'subscriptions' :
			    $finder->filterByUser($this->user, 'subscribed');
				break;

			case 'plugin':
				$pluginmode = $this->state->get('list.modetype');

				Factory::getApplication()->triggerEvent('onKunenaGetUserTopics', array($pluginmode, &$finder, &$order, &$categoryIds, $this));
				break;

			default :
				$finder
				->filterByUser($this->user, 'involved')
					->order('favorite', -1, 'ut');
				break;
		}

		$Itemid = Factory::getApplication()->input->getCmd('Itemid');
		$view   = Factory::getApplication()->input->getCmd('view');
		$layout = Factory::getApplication()->input->getCmd('layout');
		$format = Factory::getApplication()->input->getCmd('format');

		if (!$Itemid && $format != 'feed')
		{
			$controller = BaseController::getInstance("kunena");

			if (KunenaConfig::getInstance()->profile_id)
			{
				$itemidfix = KunenaConfig::getInstance()->profile_id;
			}
			else
			{
				$menu = $this->app->getMenu();

				if ($view == 'user' && $layout == 'default')
				{
					$getid = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=user"));
				}
				elseif ($view == 'topics' && $layout == 'user')
				{
					$getid = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=topics&layout=user&mode={$this->state->get('list.mode')}"));
				}
				else
				{
					$getid = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=user&mode={$this->state->get('list.mode')}"));
				}

				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			if ($view == 'user' && $layout == 'default')
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=user&Itemid={$itemidfix}", false));
			}
			elseif ($view == 'topics' && $layout == 'user')
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=user&mode={$this->state->get('list.mode')}&Itemid={$itemidfix}", false));
			}
			else
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=user&mode={$this->state->get('list.mode')}&Itemid={$itemidfix}", false));
			}

			$controller->redirect();
		}

		if ($categoryIds !== null)
		{
			$categories = KunenaForumCategoryHelper::getCategories($categoryIds, $reverse, $authorise);
			$finder->filterByCategories($categories);
		}

		$this->pagination = new KunenaPagination($finder->count(), $start, $limit);

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

		switch ($this->state->get('list.mode'))
		{
			case 'posted' :
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_POSTED');
				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=user&mode=posted');
				break;
			case 'started' :
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_STARTED');
				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=user&mode=started');
				break;
			case 'favorites' :
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_FAVORITES');
				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=user&mode=favorites');
				$actions          = array('unfavorite');
				break;
			case 'subscriptions' :
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_SUBSCRIPTIONS');
				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=user&mode=subscriptions');
				$actions          = array('unsubscribe');
				break;
			case 'plugin' :
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_PLUGIN_' . strtoupper($this->state->get('list.modetype')));
				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=user&mode=plugin');
				break;
			default :
				$this->headerText = Text::_('COM_KUNENA_VIEW_TOPICS_USERS_MODE_DEFAULT');
				$canonicalUrl = KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=user&mode=default');
		}

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
							$doc->_links[$canonicalUrl] = $value;
							unset($doc->_links[$key]);
							break;
						}
					}
				}
			}
		}

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

		$this->actions = $this->getTopicActions($this->topics, $actions);
	}

	protected function prepareDocument()
	{
		$this->setMetaData('og:url', Uri::current(), 'property');

		if (JFile::exists(JPATH_SITE . '/' . KunenaConfig::getInstance()->emailheader))
		{
			$image = Uri::base() . KunenaConfig::getInstance()->emailheader;
			$this->setMetaData('og:image', $image, 'property');
		}

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
