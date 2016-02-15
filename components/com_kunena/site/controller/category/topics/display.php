<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Category
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
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

		$this->headerText = JText::_('COM_KUNENA_THREADS_IN_FORUM') . ': ' . $this->category->name;

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
			KunenaForumTopicHelper::getKeywords(array_keys($this->topics));
			$lastreadlist = KunenaForumTopicHelper::fetchNewStatus($this->topics);

			// Fetch last / new post positions when user can see unapproved or deleted posts.
			if ($lastreadlist || $this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus())
			{
				KunenaForumMessageHelper::loadLocation($lastpostlist + $lastreadlist);
			}
		}

		$this->topicActions = $this->model->getTopicActions();
		$this->actionMove = $this->model->getActionMove();

		$this->pagination = new KunenaPagination($this->total, $limitstart, $limit);
		$this->pagination->setDisplayedPages(5);
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
		$pagesText    = $page > 1 ? " ({$page}/{$pages})" : '';
		$parentText   = $this->category->getParent()->displayField('name');
		$categoryText = $this->category->displayField('name');

		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive(); // get the active item

		if ($menu_item)
		{
			$params             = $menu_item->params; // get the params
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
				$title = JText::sprintf('COM_KUNENA_VIEW_CATEGORY_DEFAULT', "{$parentText} / {$categoryText}{$pagesText}");
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
				$this->setDescription($description);
			}
			else
			{
				$description = "{$parentText} - {$categoryText}{$pagesText} - {$this->config->board_title}";
				$this->setDescription($description);
			}
		}
	}
}
