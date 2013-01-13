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
	function displayDefault() {
		$this->items = $this->get('Items');
		$this->navigation = $this->get ( 'AdminNavigation' );
		$this->setToolBarDefault();

		$this->display ();
	}

	protected function setToolBarDefault() {
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
}
