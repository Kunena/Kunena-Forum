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

class ComponentKunenaControllerApplicationSearchDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * @var KunenaModelSearch
	 */
	public $model;
	/**
	 * @var int
	 */
	public $total;

	protected function display() {
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Search')->setProperties($this->getProperties());

		return $content;
	}

	protected function before() {
		parent::before();

		require_once KPATH_SITE . '/models/search.php';
		$this->model = new KunenaModelSearch();
		$this->state = $this->model->getState();

		$this->message_ordering = $this->me->getMessageOrdering();
//TODO: Need to move the select markup outside of view.  Otherwise difficult to stylize

		$this->searchwords = $this->model->getSearchWords();
		$this->isModerator = ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());

		$this->results = array();
		$this->total = $this->model->getTotal();
		if ($this->total) {
			$this->results = $this->model->getResults();
			$this->search_class = ' open';
			$this->search_style = ' style="display: none;"';
			$this->search_title = JText::_('COM_KUNENA_TOGGLER_EXPAND');
		} else {
			$this->search_class = ' close';
			$this->search_style = '';
			$this->search_title = JText::_('COM_KUNENA_TOGGLER_COLLAPSE');
		}

		$this->selected=' selected="selected"';
		$this->checked=' checked="checked"';
		$this->error = $this->model->getError();

		$this->prepareDocument();
	}

	protected function prepareDocument(){
		$this->document->setTitle(JText::_('COM_KUNENA_SEARCH_ADVSEARCH'));

		// TODO: set keywords and description
	}
}
