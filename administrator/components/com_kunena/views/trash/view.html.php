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
	function displayDefault() {
		$this->setToolBarDefault();
		$this->display();
	}

	function displayPurge() {
		$this->setToolBarPurge();
		$this->purgeitems = $this->get('PurgeItems');
		$this->md5Calculated = $this->get('Md5');
		$this->display();
	}

	function displayTopics () {
		$this->setToolBarTopics();
		$this->topics = $this->get('TopicsItems');
		$this->navigation = $this->get ( 'Navigation' );
		$this->display();
	}

	function displayMessages () {
		$this->setToolBarMessages();
		$this->messages = $this->get('MessagesItems');
		$this->navigation = $this->get ( 'Navigation' );
		$this->display();
	}

	protected function setToolBarDefault() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
	}

	protected function setToolBarPurge() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('purge','delete.png','delete_f2.png', 'COM_KUNENA_DELETE_PERMANENTLY');
		JToolBarHelper::spacer();
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