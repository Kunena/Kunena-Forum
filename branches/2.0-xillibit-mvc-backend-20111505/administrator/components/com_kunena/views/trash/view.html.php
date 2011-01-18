<?php
/**
 * @version		$Id: view.html.php 4192 2011-01-15 12:06:53Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Trasg view for Kunena backend
 */
class KunenaViewTrash extends KunenaView {
function display() {
	$this->assignRef ( 'state', $this->get ( 'State' ) );
	switch ($this->getLayout ()) {
			case 'purge' :
				$this->displayPurge ();
				$this->setToolBarPurge();
				break;
			case 'default' :
			   $this->displayDefault ();
				$this->setToolBarDefault();
				break;
	}
		parent::display ();
	}

	function displayPurge() {
		$this->purgeitems = $this->get('PurgeItems');
		$this->md5Calculated = $this->get('Md5');
	}

	function displayDefault() {
		$this->items = $this->get('Items');
		$this->navigation = $this->get ( 'Navigation' );
  }

	protected function setToolBarPurge() {
		// Set the titlebar text
		JToolBarHelper::spacer();
		JToolBarHelper::custom('purge','delete.png','delete_f2.png', 'COM_KUNENA_DELETE_PERMANENTLY');
		JToolBarHelper::spacer();
	}

	protected function setToolBarDefault() {
		// Set the titlebar text
		JToolBarHelper::spacer();
		JToolBarHelper::custom('restore','restore.png','restore_f2.png', 'COM_KUNENA_TRASH_RESTORE');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('purge','trash.png','trash_f2.png', 'COM_KUNENA_TRASH_PURGE');
		JToolBarHelper::spacer();
	}
}