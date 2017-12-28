<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserListDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserListDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/List';

	public $state;

	public $me;

	public $total;

	public $users;

	public $pagination;

	/**
	 * Load user list.
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$config = KunenaConfig::getInstance();

		if (!$config->userlist_allowed && JFactory::getUser()->guest)
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		require_once KPATH_SITE . '/models/user.php';
		$this->model = new KunenaModelUser(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state = $this->model->getState();

		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaConfig::getInstance();

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');

		// Exclude super admins.
		if ($this->config->superadmin_userlist)
		{
			$filter = JAccess::getUsersByGroup(8);
		}
		else
		{
			$filter = array();
		}

		$finder = new KunenaUserFinder;
		$finder
			->filterByConfiguration($filter)
			->filterByName($this->state->get('list.search'));

		$this->total = $finder->count();
		$this->pagination = new KunenaPagination($this->total, $start, $limit);

		$alias = 'ku';
		$aliasList = array('id', 'name', 'username', 'email', 'block', 'registerDate', 'lastvisitDate');

		if (in_array($this->state->get('list.ordering'), $aliasList))
		{
			$alias = 'a';
		}

		$this->users = $finder
			->order($this->state->get('list.ordering'), $this->state->get('list.direction') == 'asc' ? 1 : - 1, $alias)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$page      = $this->pagination->pagesCurrent;
		$pages     = $this->pagination->pagesTotal;
		$pagesText = $page > 1 ? " ({$page}/{$pages})" : '';

		$app       = JFactory::getApplication();
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
				$title = JText::_('COM_KUNENA_VIEW_USER_LIST') . $pagesText;
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title . ', ' . JText::_('COM_KUNENA_VIEW_USER_LIST');
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::_('COM_KUNENA_VIEW_USER_LIST') . ': ' . $this->config->board_title;
				$this->setDescription($description);
			}
		}
	}
}
