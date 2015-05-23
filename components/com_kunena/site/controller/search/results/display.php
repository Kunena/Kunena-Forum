<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Search
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerSearchResultsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerSearchResultsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Search/Results';

	/**
	 * @var KunenaModelSearch
	 */
	public $model;

	/**
	 * @var int
	 */
	public $total;

	public $data = array();

	/**
	 * Prepare search results display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/search.php';
		$this->model = new KunenaModelSearch(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state = $this->model->getState();

		$this->me = KunenaUserHelper::getMyself();
		$this->message_ordering = $this->me->getMessageOrdering();

		$this->searchwords = $this->model->getSearchWords();
		$this->isModerator = ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());

		$this->results = array();
		$this->total = $this->model->getTotal();
		$this->results = $this->model->getResults();

		$this->pagination = new KunenaPagination(
			$this->total,
			$this->state->get('list.start'),
			$this->state->get('list.limit')
		);

		$this->error = $this->model->getError();
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->setTitle(JText::_('COM_KUNENA_SEARCH_ADVSEARCH'));
		$keywords = $this->config->board_title . ', ' . JText::_('COM_KUNENA_SEARCH_ADVSEARCH') . ', '. $this->searchwords;
		$this->setKeywords($keywords);
		$description = JText::_('COM_KUNENA_SEARCH_ADVSEARCH') . ': ' . $this->config->board_title ;
		$this->setDescription($description);
	}
}
