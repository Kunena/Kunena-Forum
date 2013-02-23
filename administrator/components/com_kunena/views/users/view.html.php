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
 * Users view for Kunena backend
 */
class KunenaAdminViewUsers extends KunenaView {
	public function display($tpl = null) {
		$this->setToolbar();
		$this->items = $this->get('items');
		$this->pagination = $this->get('Pagination');
		$this->sortFields = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch = $this->escape($this->state->get('filter.search'));
		$this->filterUsername	= $this->escape($this->state->get('filter.username'));
		$this->filterEmail = $this->escape($this->state->get('filter.email'));
		$this->filterSignature = $this->escape($this->state->get('filter.signature'));
		$this->filterBlock = $this->escape($this->state->get('filter.block'));
		$this->filterBanned = $this->escape($this->state->get('filter.banned'));
		$this->filterModerator = $this->escape($this->state->get('filter.moderator'));
		$this->listOrdering = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));
		return parent::display($tpl);
	}

	protected function setToolbar() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_USER_MANAGER'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('logout', 'cancel.png', 'cancel_f2.png', 'COM_KUNENA_LOGOUT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('move', 'move.png', 'move_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('trashusermessages', 'trash.png', 'icon-32-move.png', 'COM_KUNENA_TRASH_USERMESSAGES');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete','delete.png','delete_f2.png', 'COM_KUNENA_USER_DELETE');
		JToolBarHelper::spacer();
	}

	/**
	 * Returns an array of locked filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function signatureOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function blockOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function bannedOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function moderatorOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	protected function getSortFields() {
		$sortFields = array();
		$sortFields[] = JHtml::_('select.option', 'a.username', JText::_('COM_KUNENA_USRL_USERNAME'));
		//$sortFields[] = JHtml::_('select.option', 'a.name', JText::_('COM_KUNENA_USRL_REALNAME'));
		$sortFields[] = JHtml::_('select.option', 'a.email', JText::_('COM_KUNENA_USRL_EMAIL'));
		$sortFields[] = JHtml::_('select.option', 'ku.signature', JText::_('COM_KUNENA_GEN_SIGNATURE'));
		$sortFields[] = JHtml::_('select.option', 'a.block', JText::_('COM_KUNENA_USRL_ENABLED'));
		$sortFields[] = JHtml::_('select.option', 'ku.banned', JText::_('COM_KUNENA_USRL_BANNED'));
		$sortFields[] = JHtml::_('select.option', 'ku.moderator', JText::_('COM_KUNENA_VIEW_MODERATOR'));
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
