<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Category
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
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategoryTopicsDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Category/Item';

	/**
	 * @var
	 * @since Kunena
	 */
	public $headerText;

	/**
	 * @var KunenaForumCategory
	 * @since Kunena
	 */
	public $category;

	/**
	 * @var
	 * @since Kunena
	 */
	public $total;

	/**
	 * @var
	 * @since Kunena
	 */
	public $topics;

	/**
	 * @var KunenaPagination
	 * @since Kunena
	 */
	public $pagination;

	/**
	 * @var KunenaUser
	 * @since Kunena
	 */
	public $me;

	/**
	 * Prepare category display.
	 *
	 * @return void
	 *
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/category.php';
		$this->model = new KunenaModelCategory;

		$this->me = KunenaUserHelper::getMyself();

		$catid      = $this->input->getInt('catid');
		$limitstart = $this->input->getInt('limitstart', 0);
		$limit      = $this->input->getInt('limit', 0);
		$Itemid     = $this->input->getInt('Itemid');
		$format     = $this->input->getCmd('format');

		if (!$Itemid && $format != 'feed' && KunenaConfig::getInstance()->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}&Itemid={$itemid}", false));
			$controller->redirect();
		}

		if ($limit < 1 || $limit > 100)
		{
			$limit = $this->config->threads_per_page;
		}

		// TODO:
		$direction = 'DESC';

		$this->category = KunenaForumCategoryHelper::get($catid);
		$this->category->tryAuthorise();

		$this->headerText = $this->category->name;

		$topic_ordering = $this->category->topic_ordering;

		$access = KunenaAccess::getInstance();
		$hold   = $access->getAllowedHold($this->me, $catid);
		$moved  = 1;
		$params = array(
			'hold'  => $hold,
			'moved' => $moved,
		);

		switch ($topic_ordering)
		{
			case 'alpha':
				$params['orderby'] = 'tt.ordering DESC, tt.subject ASC ';
				break;
			case 'creation':
				$params['orderby'] = 'tt.ordering DESC, tt.first_post_time ' . $direction;
				break;
			case 'views':
				$params['orderby'] = 'tt.ordering DESC, tt.hits ' . $direction;
				break;
			case 'posts':
				$params['orderby'] = 'tt.ordering DESC, tt.posts ' . $direction;
				break;
			case 'lastpost':
			default:
				$params['orderby'] = 'tt.ordering DESC, tt.last_post_time ' . $direction;
		}

		list($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($catid, $limitstart, $limit, $params);

		if ($limitstart > 1 && !$this->topics)
		{
			$controller = JControllerLegacy::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}&Itemid={$itemid}", false));
			$controller->redirect();
		}

		if ($this->total > 0)
		{
			// Collect user ids for avatar prefetch when integrated.
			$userlist     = array();
			$lastpostlist = array();

			foreach ($this->topics as $topic)
			{
				$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
				$userlist[intval($topic->last_post_userid)]  = intval($topic->last_post_userid);
				$lastpostlist[intval($topic->last_post_id)]  = intval($topic->last_post_id);
			}

			// Prefetch all users/avatars to avoid user by user queries during template iterations.
			if (!empty($userlist))
			{
				KunenaUserHelper::loadUsers($userlist);
			}

			KunenaForumTopicHelper::getUserTopics(array_keys($this->topics));
			$lastreadlist = KunenaForumTopicHelper::fetchNewStatus($this->topics);

			// Fetch last / new post positions when user can see unapproved or deleted posts.
			if ($lastreadlist || $this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus())
			{
				KunenaForumMessageHelper::loadLocation($lastpostlist + $lastreadlist);
			}
		}

		$config = KunenaConfig::getInstance();

		if (!$config->read_only)
		{
			$this->topicActions = $this->model->getTopicActions();
		}

		$this->actionMove = $this->model->getActionMove();

		$this->pagination = new KunenaPagination($this->total, $limitstart, $limit);
		$this->pagination->setDisplayedPages(5);
		$doc  = Factory::getDocument();
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
							$canonicalUrl               = $this->category->getUrl();
							$doc->_links[$canonicalUrl] = $value;
							unset($doc->_links[$key]);
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	protected function prepareDocument()
	{
		$page  = $this->pagination->pagesCurrent;
		$pages = $this->pagination->pagesTotal;

		$pagesText    = ($pages > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
		$parentText   = $this->category->getParent()->name;
		$categoryText = $this->category->name;
		$categorydesc = $this->category->description;

		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();
		$doc       = Factory::getDocument();
		$config    = Factory::getConfig();
		$robots    = $config->get('robots');

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

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title') . $pagesText;
				$this->setTitle($title);
			}
			else
			{
				$title = Text::sprintf("{$categoryText}{$pagesText}");
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = Text::_('COM_KUNENA_CATEGORIES') . ", {$parentText}, {$categoryText}, {$this->config->board_title}";
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$description = substr($description, 0, 140) . '... ' . $pagesText;
				$this->setDescription($description);
			}
			elseif (!empty($categorydesc))
			{
				$categorydesc = substr($categorydesc, 0, 140) . '... ' . $pagesText;
				$this->setDescription($categorydesc);
			}
			else
			{
				$description = "{$parentText} - {$categoryText}{$pagesText} - {$this->config->board_title}";
				$description = substr($description, 0, 140) . '...';
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
