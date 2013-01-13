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
}