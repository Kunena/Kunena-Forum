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
 * Trash view for Kunena backend
 */
class KunenaAdminViewTrash extends KunenaView {
	function displayDefault() {
		$this->trash_items = $this->get('Trashitems');
		$this->navigation = $this->get ( 'Navigation' );
		$this->view_options_list = $this->get ( 'ViewOptions' );

		$this->sortFields = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch = $this->escape($this->state->get('list.search'));
		$this->filterTitle = $this->escape($this->state->get('list.filter_title'));
		$this->filterTopic	= $this->escape($this->state->get('list.filter_topic'));
		$this->filterCategory	= $this->escape($this->state->get('list.filter_category'));
		$this->filterIp = $this->escape($this->state->get('list.filter_ip'));
		$this->filterAuthor = $this->escape($this->state->get('list.filter_author'));
		$this->filterDate	= $this->escape($this->state->get('list.filter_date'));
		$this->listOrdering = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));

		$this->sortDirectionOrdering = $this->getSortDirectionOrdering();

		$this->setToolBarDefault();
		$this->display();
	}

	function displayPurge() {
		$this->purgeitems = $this->get('PurgeItems');
		$this->md5Calculated = $this->get('Md5');

		$this->setToolBarPurge();
		$this->display();
	}

	protected function setToolBarDefault() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_TRASH_MANAGER'), 'kunena.png' );
		JToolBarHelper::spacer();
		if (version_compare(JVERSION, '3', '>')) {
			JToolBarHelper::custom('restore','checkin.png','checkin_f2.png', 'COM_KUNENA_TRASH_RESTORE');
		} else {
			JToolBarHelper::custom('restore','restore.png','restore_f2.png', 'COM_KUNENA_TRASH_RESTORE');
		}
		JToolBarHelper::spacer();
		JToolBarHelper::custom('purge','trash.png','trash_f2.png', 'COM_KUNENA_TRASH_PURGE');
		JToolBarHelper::spacer();
	}

	protected function setToolBarPurge() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('purge','delete.png','delete_f2.png', 'COM_KUNENA_DELETE_PERMANENTLY');
		JToolBarHelper::spacer();
	}

	protected function getSortFields() {
		$sortFields = array();
		if ($this->state->get( 'list.view_selected') == 'topics') {
			$sortFields[] = JHtml::_('select.option', 'tt.subject', JText::_('COM_KUNENA_TRASH_TITLE'));
			$sortFields[] = JHtml::_('select.option', 'm.ip', JText::_('COM_KUNENA_TRASH_IP'));
			$sortFields[] = JHtml::_('select.option', 'tt.first_post_userid', JText::_('COM_KUNENA_TRASH_AUTHOR_USERID'));
			$sortFields[] = JHtml::_('select.option', 'tt.first_post_guest_name', JText::_('COM_KUNENA_TRASH_AUTHOR'));
			$sortFields[] = JHtml::_('select.option', 'tt.first_post_time', JText::_('COM_KUNENA_TRASH_DATE'));
		} else {
			$sortFields[] = JHtml::_('select.option', 'm.subject', JText::_('COM_KUNENA_TRASH_TITLE'));
			$sortFields[] = JHtml::_('select.option', 'm.ip', JText::_('COM_KUNENA_TRASH_IP'));
			$sortFields[] = JHtml::_('select.option', 'm.userid', JText::_('COM_KUNENA_TRASH_AUTHOR_USERID'));
			$sortFields[] = JHtml::_('select.option', 'm.name', JText::_('COM_KUNENA_TRASH_AUTHOR'));
			$sortFields[] = JHtml::_('select.option', 'm.time', JText::_('COM_KUNENA_TRASH_DATE'));
		}
		$sortFields[] = JHtml::_('select.option', 'a.id', JText::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	protected function getSortDirectionFields() {
		$sortDirection = array();
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}

	// TODO: remove it when J2.5 support is dropped
	protected function getSortDirectionOrdering() {
		$sortDirection = array();
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('COM_KUNENA_FIELD_LABEL_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('COM_KUNENA_FIELD_LABEL_DESCENDING'));

		return $sortDirection;
	}
}