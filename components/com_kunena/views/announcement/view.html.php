<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.cache.handler.output' );

/**
 * Announcement view
 */
class KunenaViewAnnouncement extends KunenaView {
	function displayDefault($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'announcement', $this->get ( 'Announcement' ) );
		$this->assignRef ( 'canEdit', $this->get ( 'CanEdit' ) );

		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);

		$this->display();
	}

	function displayCreate($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'announcement', $this->get ( 'NewAnnouncement' ) );
		$this->assignRef ( 'canEdit', $this->get ( 'CanEdit' ) );

		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);

		$this->display();
	}

	function displayEdit($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'announcement', $this->get ( 'Announcement' ) );
		$this->assignRef ( 'canEdit', $this->get ( 'CanEdit' ) );

		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);

		$this->display();
	}

	function displayList($tpl = null) {
		$this->app = JFactory::getApplication();
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'announcements', $this->get ( 'Announcements' ) );
		$this->assignRef ( 'canEdit', $this->get ( 'CanEdit' ) );

		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);

		$this->display();
	}
}