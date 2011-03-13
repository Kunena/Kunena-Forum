<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Trash view for Kunena backend
 */
class KunenaAdminViewTrash extends KunenaView {
	function display() {
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		switch ($this->getLayout ()) {
			case 'purge' :
				$this->displayPurge ();
				$this->setToolBarPurge();
				break;
			case 'default' :
				$this->setToolBarDefault();
				break;
			case 'messages' :
				$this->displayMessages ();
				$this->setToolBarMessages();
				break;
			case 'topics' :
				$this->displayTopics ();
				$this->setToolBarTopics();
				break;
		}
		parent::display ();
	}

	function displayPurge() {
		$this->purgeitems = $this->get('PurgeItems');
		$this->md5Calculated = $this->get('Md5');
	}

	protected function setToolBarPurge() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('purge','delete.png','delete_f2.png', 'COM_KUNENA_DELETE_PERMANENTLY');
		JToolBarHelper::spacer();
	}

	protected function setToolBarDefault() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
	}

	function displayTopics () {
		$this->topics = $this->get('TopicsItems');
		$this->navigation = $this->get ( 'Navigation' );
	}

	protected function setToolBarTopics() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('restore','restore.png','restore_f2.png', 'COM_KUNENA_TRASH_RESTORE');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('purge','trash.png','trash_f2.png', 'COM_KUNENA_TRASH_PURGE');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('trash');
	}

	function displayMessages () {
		$this->messages = $this->get('MessagesItems');
		$this->navigation = $this->get ( 'Navigation' );
	}

	protected function setToolBarMessages() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('restore','restore.png','restore_f2.png', 'COM_KUNENA_TRASH_RESTORE');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('purge','trash.png','trash_f2.png', 'COM_KUNENA_TRASH_PURGE');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('trash');
	}
}