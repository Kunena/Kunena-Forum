<?php
/**
 * @version		$Id$
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
 * About view for Kunena backend
 */
class KunenaAdminViewCategories extends KunenaView {
	function display() {
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		if ($this->state->get('item.id')) $this->setLayout ('edit');
		switch ($this->getLayout ()) {
			case 'create' :
			case 'edit' :
				$this->displayEdit ();
				$this->setToolBarEdit();
				break;
			case 'default' :
				$this->displayDefault ();
				$this->setToolBarDefault();
				break;
		}
		parent::display ();
	}

	function displayEdit() {
		$this->me = KunenaFactory::getUser();
		$this->assignRef ( 'category', $this->get ( 'AdminCategory' ) );
		$this->assignRef ( 'options', $this->get ( 'AdminOptions' ) );
		$this->assignRef ( 'moderators', $this->get ( 'AdminModerators' ) );
	}

	function displayDefault() {
		$this->assignRef ( 'categories', $this->get ( 'AdminCategories' ) );
		$this->assignRef ( 'navigation', $this->get ( 'AdminNavigation' ) );
	}

	protected function setToolBarEdit() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		//JToolBarHelper::back ( JText::_ ( 'Home' ), 'index.php?option=com_kunena' );
	}
	protected function setToolBarDefault() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::publish ();
		JToolBarHelper::unpublish ();
		JToolBarHelper::addNew ();
		JToolBarHelper::editList ();
		JToolBarHelper::deleteList ();
		//JToolBarHelper::back ( JText::_ ( 'Home' ), 'index.php?option=com_kunena' );
	}
}