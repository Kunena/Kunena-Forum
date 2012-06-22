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
 * About view for Kunena backend
 */
class KunenaAdminViewCategories extends KunenaView {
	function displayCreate() {
		return $this->displayEdit();
	}

	function displayEdit() {
		$this->category = $this->get ( 'AdminCategory' );
		// FIXME: better access control and gracefully handle no rights
		// Prevent fatal error if no rights:
		if (!$this->category) return;
		$this->options = $this->get ( 'AdminOptions' );
		$this->moderators = $this->get ( 'AdminModerators' );
		$this->setToolBarEdit();
		$this->display();
	}

	function displayDefault() {
		$this->categories = $this->get ( 'AdminCategories' );
		$this->navigation = $this->get ( 'AdminNavigation' );
		$this->setToolBarDefault();
		$this->display();
	}

	protected function setToolBarEdit() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		if (version_compare(JVERSION, '1.7','>')) {
			JToolBarHelper::apply('apply');
			JToolBarHelper::save('save');
			JToolBarHelper::save2new('save2new');

			// If an existing item, can save to a copy.
			if ($this->category->exists()) {
				JToolBarHelper::save2copy('save2copy');
			}
		} else {
			JToolBarHelper::save();
		}
		JToolBarHelper::cancel();
	}
	protected function setToolBarDefault() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::publish ();
		JToolBarHelper::unpublish ();
		JToolBarHelper::addNew ();
		JToolBarHelper::editList ();
		JToolBarHelper::deleteList ();
		//JToolBarHelper::back ( JText::_ ( 'Home' ), 'index.php?option=com_kunena' );
	}
}