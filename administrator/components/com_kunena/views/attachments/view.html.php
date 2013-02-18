<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Attachments view for Kunena backend
 */
class KunenaAdminViewAttachments extends KunenaView {
	function display($tpl = null) {
		$this->setToolbar();
		$this->items = $this->get('Items');
		$this->state = $this->get('state');
		$this->pagination = $this->get ( 'Pagination' );
		$this->sortFields = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch	= $this->escape($this->state->get('list.search'));
		$this->filterTitle	= $this->escape($this->state->get('filter.title'));
		$this->filterType	= $this->escape($this->state->get('filter.type'));
		$this->filterSize	= $this->escape($this->state->get('filter.size'));
		$this->filterDimensions	= $this->escape($this->state->get('filter.dims'));
		$this->filterUsername = $this->escape($this->state->get('filter.username'));
		$this->filterPost	= $this->escape($this->state->get('filter.post'));
		$this->listOrdering	= $this->escape($this->state->get('list.ordering'));
		$this->listDirection	= $this->escape($this->state->get('list.direction'));

		return parent::display($tpl);

	}

	protected function setToolbar() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_FILE_MANAGER'), 'kunena.png' );
		JToolBarHelper::spacer();
		if (version_compare(JVERSION, '3', '>')) {
			JToolBarHelper::custom('delete','trash.png','trash_f2.png', 'COM_KUNENA_GEN_DELETE');
		} else {
			JToolBarHelper::custom('delete','delete.png','delete_f2.png', 'COM_KUNENA_GEN_DELETE');
		}
		JToolBarHelper::spacer();
	}

	protected function getSortFields() {
		$sortFields = array();
		$sortFields[] = JHtml::_('select.option', 'a.filename', JText::_('COM_KUNENA_FILENAME'));
		$sortFields[] = JHtml::_('select.option', 'a.filetype', JText::_('COM_KUNENA_ATTACHMENTS_FILETYPE'));
		$sortFields[] = JHtml::_('select.option', 'a.size', JText::_('COM_KUNENA_FILESIZE'));
		$sortFields[] = JHtml::_('select.option', 'a.id', JText::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	protected function getSortDirectionFields() {
		$sortDirection = array();
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}
}
