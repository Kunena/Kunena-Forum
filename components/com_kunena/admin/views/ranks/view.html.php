<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * About view for Kunena ranks backend
 */
class KunenaAdminViewRanks extends KunenaView {
	function display($tpl = null) {
		$this->setToolbar();
		$this->items = $this->get('Items');
		$this->state = $this->get('state');
		$this->pagination = $this->get ( 'Pagination' );

		$this->sortFields = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch = $this->escape($this->state->get('filter.search'));
		$this->filterTitle = $this->escape($this->state->get('filter.title'));
		$this->filterSpecial = $this->escape($this->state->get('filter.special'));
		$this->filterMinPostCount = $this->escape($this->state->get('filter.min'));
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->listOrdering = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));

		return parent::display($tpl);
	}

	protected function setToolbar() {
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->pagination = $this->get ( 'Pagination' );
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_RANK_MANAGER'), 'ranks' );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_NEW_RANK');
		//TODO: Implement flag to hide options, personal preference option.
		//if($this->filterActive || $this->pagination->total > 0) {
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
		//}
		JToolBarHelper::spacer();
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public static function specialOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	protected function getSortFields() {
		$sortFields = array();
		$sortFields[] = JHtml::_('select.option', 'title', JText::_('JGLOBAL_TITLE'));
		$sortFields[] = JHtml::_('select.option', 'special', JText::_('COM_KUNENA_RANKS_SPECIAL'));
		$sortFields[] = JHtml::_('select.option', 'min', JText::_('COM_KUNENA_RANKSMIN'));
		$sortFields[] = JHtml::_('select.option', 'id', JText::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

    protected function getSortDirectionFields() {
        $sortDirection = array();
		//$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
		//$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));
        // TODO: remove it when J2.5 support is dropped
        $sortDirection[] = JHtml::_('select.option', 'asc', JText::_('COM_KUNENA_FIELD_LABEL_ASCENDING'));
        $sortDirection[] = JHtml::_('select.option', 'desc', JText::_('COM_KUNENA_FIELD_LABEL_DESCENDING'));

        return $sortDirection;
    }
}
