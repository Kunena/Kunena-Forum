<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class ComponentKunenaControllerUserListDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserListDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'User/List';

	/**
	 * @var
	 * @since Kunena
	 */
	public $state;

	/**
	 * @var
	 * @since Kunena
	 */
	public $me;

	/**
	 * @var
	 * @since Kunena
	 */
	public $total;

	/**
	 * @var
	 * @since Kunena
	 */
	public $users;

	/**
	 * @var
	 * @since Kunena
	 */
	public $pagination;

	/**
	 * Load user list.
	 *
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$config = KunenaConfig::getInstance();

		if (!$config->userlist_allowed && Factory::getUser()->guest)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		require_once KPATH_SITE . '/models/user.php';
		$this->model = new KunenaModelUser(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state = $this->model->getState();

		$this->me     = KunenaUserHelper::getMyself();
		$this->config = KunenaConfig::getInstance();

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');

		$Itemid = $this->input->getInt('Itemid');
		$format = $this->input->getCmd('format');

		if (!$Itemid && $format != 'feed' && KunenaConfig::getInstance()->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=user&layout=list&Itemid={$itemid}", false));
			$controller->redirect();
		}

		// Exclude super admins.
		if ($this->config->superadmin_userlist)
		{
			$filter = \Joomla\CMS\Access\Access::getUsersByGroup(8);
		}
		else
		{
			$filter = array();
		}

		$finder = new KunenaUserFinder;
		$finder
			->filterByConfiguration($filter)
			->filterByName($this->state->get('list.search'));

		$this->total      = $finder->count();
		$this->pagination = new KunenaPagination($this->total, $start, $limit);

		$alias     = 'ku';
		$aliasList = array('id', 'name', 'username', 'email', 'block', 'registerDate', 'lastvisitDate');

		if (in_array($this->state->get('list.ordering'), $aliasList))
		{
			$alias = 'a';
		}

		$this->users = $finder
			->order($this->state->get('list.ordering'), $this->state->get('list.direction') == 'asc' ? 1 : -1, $alias)
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();
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
		$page      = $this->pagination->pagesCurrent;
		$pages     = $this->pagination->pagesTotal;
		$pagesText = ($page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');

		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title') . $pagesText;
				$this->setTitle($title);
			}
			else
			{
				$title = Text::_('COM_KUNENA_VIEW_USER_LIST') . $pagesText;
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title . ', ' . Text::_('COM_KUNENA_VIEW_USER_LIST') . $pagesText;
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = Text::_('COM_KUNENA_VIEW_USER_LIST') . ': ' . $this->config->board_title . $pagesText;
				$this->setDescription($description);
			}
		}
	}
}
