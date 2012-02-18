<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * About view for Kunena backend
 */
class KunenaAdminViewCategories extends KunenaView {
	function displayCreate() {
		return $this->displayEdit();
	}

	function displayEdit() {
		$this->assignRef ( 'category', $this->get ( 'AdminCategory' ) );
		$this->assignRef ( 'options', $this->get ( 'AdminOptions' ) );
		$this->assignRef ( 'moderators', $this->get ( 'AdminModerators' ) );
		$this->setToolBarEdit();
		$this->display();
	}

	function displayDefault() {
		$this->assignRef ( 'categories', $this->get ( 'AdminCategories' ) );
		$this->assignRef ( 'navigation', $this->get ( 'AdminNavigation' ) );
		$this->setToolBarDefault();
		$this->display();
	}

	protected function setToolBarEdit() {
		// Set the titlebar text
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		if (version_compare(JVERSION, '1.7','>')) {
			JToolBarHelper::apply('apply');
			JToolBarHelper::save('save');
			JToolBarHelper::save2new('save2new');

			// If an existing item, can save to a copy.
			if ($this->category->exists()) {
				//JToolBarHelper::save2copy('save2copy');
			}
		} else {
			JToolBarHelper::save();
		}
		JToolBarHelper::cancel('categories');
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