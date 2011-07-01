<?php
/**
 * @version $Id: view.html.php 4387 2011-02-08 16:19:37Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.cache.handler.output' );
kimport ( 'kunena.view' );

/**
 * Announcement view
 */
class KunenaViewAnnouncement extends KunenaView {
	function displayDefault($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'announcement', $this->get ( 'Announcement' ) );
		$this->assignRef ( 'canEdit', $this->get ( 'CanEdit' ) );
		$this->display();
	}

	function displayCreate($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'announcement', $this->get ( 'NewAnnouncement' ) );
		$this->assignRef ( 'canEdit', $this->get ( 'CanEdit' ) );
		$this->display();
	}

	function displayEdit($tpl = null) {
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'announcement', $this->get ( 'Announcement' ) );
		$this->assignRef ( 'canEdit', $this->get ( 'CanEdit' ) );
		$this->display();
	}

	function displayList($tpl = null) {
		$this->app = JFactory::getApplication();
		$this->config = KunenaFactory::getConfig();
		$this->assignRef ( 'announcements', $this->get ( 'Announcements' ) );
		$this->assignRef ( 'canEdit', $this->get ( 'CanEdit' ) );
		$this->display();
	}
}