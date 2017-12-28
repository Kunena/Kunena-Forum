<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Category
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategoryTopicsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Category/Item';

	public $headerText;

	/**
	 * @var KunenaForumCategory
	 */
	public $category;

	public $total;

	public $topics;

	/**
	 * @var KunenaPagination
	 */
	public $pagination;

	/**
	 * @var KunenaUser
	 */
	public $me;

	/**
	 * Prepare category display.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/category.php';
		$this->model = new KunenaModelCategory;

		$this->me = KunenaUserHelper::getMyself();

		$catid = $this->input->getInt('catid');
		$limitstart = $this->input->getInt('limitstart', 0);
		$limit = $this->input->getInt('limit', 0);

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
		$hold = $access->getAllowedHold($this->me, $catid);
		$moved = 1;
		$params = array(
			'hold' => $hold,
			'moved' => $moved
		);

		switch ($topic_ordering)
		{
			case 'alpha':
				$params['orderby'] = 'tt.ordering DESC, tt.subject ASC ';
				break;
			case 'creation':
				$params['orderby'] = 'tt.ordering DESC, tt.first_post_time ' . $direction;
				break;
			case 'lastpost':
			default:
				$params['orderby'] = 'tt.ordering DESC, tt.last_post_time ' . $direction;
		}

		list($this->total, $this->topics) = KunenaForumTopicHelper::getLatestTopics($catid, $limitstart, $limit, $params);

		if ($this->total > 0)
		{
			// Collect user ids for avatar prefetch when integrated.
			$userlist = array();
			$lastpostlist = array();

			foreach ($this->topics as $topic)
			{
				$userlist[intval($topic->first_post_userid)] = intval($topic->first_post_userid);
				$userlist[intval($topic->last_post_userid)] = intval($topic->last_post_userid);
				$lastpostlist[intval($topic->last_post_id)] = intval($topic->last_post_id);
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
		$doc = JFactory::getDocument();
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
							$canonicalUrl = $this->category->getUrl();
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
	 */
	protected function prepareDocument()
	{
		$page         = $this->pagination->pagesCurrent;
		$pages        = $this->pagination->pagesTotal;

		$pagesText = ($pages > 1  && $page > 1 ? " - " . JText::_('COM_KUNENA_PAGES') . " {$page}" : '');


		$parentText   = $this->category->getParent()->name;
		$categoryText = $this->category->name;
		$categorydesc = $this->category->description;

		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		$doc = JFactory::getDocument();
		$config = JFactory::getConfig();
		$robots = $config->get('robots');

		if ($robots == '' && $this->topics)
		{
			$doc->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow' && $this->topics)
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow' && $this->topics)
		{
			$doc->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$doc->setMetaData('robots', 'nofollow, noindex');
		}

		$pagdata = $this->pagination->getData();

		if ($pagdata->previous->link)
		{
			$pagdata->previous->link = str_replace( '?limitstart=0', '', $pagdata->previous->link);
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
							$canonicalUrl = KunenaRoute::_();
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
				$title = JText::sprintf("{$categoryText}{$pagesText}");
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = JText::_('COM_KUNENA_CATEGORIES') . ", {$parentText}, {$categoryText}, {$this->config->board_title}";
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
				$doc->setMetaData('robots', $robots);
			}
		}
	}
}
