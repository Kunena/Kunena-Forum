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
	public function signatureOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'Yes');
		$options[]	= JHtml::_('select.option', '0', 'No');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function blockOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '0', 'On');
		$options[]	= JHtml::_('select.option', '1', 'Off');

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function bannedOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'On');
		$options[]	= JHtml::_('select.option', '0', 'Off');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function moderatorOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'Yes');
		$options[]	= JHtml::_('select.option', '0', 'No');

		return $options;
	}
}
