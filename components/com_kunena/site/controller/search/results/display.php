<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class ComponentKunenaControllerSearchResultsDisplay extends KunenaControllerDisplay
{
	/**
	 * @var KunenaModelSearch
	 */
	public $model;
	/**
	 * @var int
	 */
	public $total;
	public $data = array();

	protected function display()
	{
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Search/Results')
			->setProperties($this->getProperties());

		return $content;
	}

	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/search.php';
		$this->model = new KunenaModelSearch();
		$this->state = $this->model->getState();

		$this->me = KunenaUserHelper::getMyself();
		$this->message_ordering = $this->me->getMessageOrdering();

		$this->searchwords = $this->model->getSearchWords();
		$this->isModerator = ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());

		$this->results = array();
		$this->total = $this->model->getTotal();
		$this->results = $this->model->getResults();

		$this->pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));

		$this->error = $this->model->getError();
	}
}
