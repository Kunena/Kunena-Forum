<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * About view for Kunena smilies backend
 */
class KunenaAdminViewSmilies extends KunenaView {
	function display($tpl = null) {
		$this->setToolbar();
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get ( 'Pagination' );
		$this->sortFields = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterCode	= $this->escape($this->state->get('filter.code'));
		$this->filterUrl = $this->escape($this->state->get('filter.url'));
		$this->listOrdering = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));

		return parent::display($tpl);
	}
	protected function setToolbar() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_EMOTICON_MANAGER'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_NEW_SMILIE');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete', 'delete.png', 'delete_f2.png', 'COM_KUNENA_GEN_DELETE');
		JToolBarHelper::spacer();
	}

	protected function getSortFields() {
		$sortFields = array();
		$sortFields[] = JHtml::_('select.option', 'a.code', JText::_('COM_KUNENA_EMOTICONS_CODE'));
		$sortFields[] = JHtml::_('select.option', 'a.location', JText::_('COM_KUNENA_EMOTICONS_URL'));
		$sortFields[] = JHtml::_('select.option', 'a.id', JText::_('COM_KUNENA_EMOTICONS_FIELD_LABEL_ID'));

		return $sortFields;
	}

	protected function getSortDirectionFields() {
		$sortDirection = array();
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}
}
