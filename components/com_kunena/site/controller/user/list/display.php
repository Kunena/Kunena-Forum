<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerUserListDisplay
 */
class ComponentKunenaControllerUserListDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/List';

	public $state;
	public $me;
	public $total;
	public $users;
	public $pagination;

	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/user.php';
		$model = new KunenaModelUser();
		$this->state = $model->getState();

		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaConfig::getInstance();

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');

		// Exclude super admins.
		$superadmins = JAccess::getUsersByGroup(8);

		$finder = new KunenaUserFinder();
		$finder
			->filterByConfiguration($superadmins)
			->filterByName($this->state->get('list.search'));

		$this->total = $finder->count();
		$this->pagination = new KunenaPagination($this->total, $start, $limit);

		$this->users = $finder
			->order($this->state->get('list.ordering'), $this->state->get('list.direction'))
			->start($this->pagination->limitstart)
			->limit($this->pagination->limit)
			->find();
	}

	protected function prepareDocument(){
		$page = $this->pagination->pagesCurrent;
		$pages = $this->pagination->pagesTotal;
		$pagesText = $page > 1 ? " ({$page}/{$pages})" : '';

		$title = JText::_('COM_KUNENA_VIEW_USER_LIST') . $pagesText;
		$this->setTitle($title);
	}
}
