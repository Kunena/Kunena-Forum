<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserListDisplay
 *
 * @since  3.1
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
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/user.php';
		$model = new KunenaModelUser;
		$this->state = $model->getState();

		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaConfig::getInstance();

		$start = $this->state->get('list.start');
		$limit = $this->state->get('list.limit');

		// Exclude super admins.
		// TODO: figure out a better way...
		$db = JFactory::getDbo();
		$query = "SELECT user_id FROM `#__user_usergroup_map` WHERE group_id=8";
		$db->setQuery($query);
		$superadmins = (array) $db->loadColumn();

		$finder = new KunenaUserFinder;
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

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$page = $this->pagination->pagesCurrent;
		$pages = $this->pagination->pagesTotal;
		$pagesText = $page > 1 ? " ({$page}/{$pages})" : '';

		$title = JText::_('COM_KUNENA_VIEW_USER_LIST') . $pagesText;
		$this->setTitle($title);
	}
}
