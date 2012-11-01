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
 * About view for Kunena smilies backend
 */
class KunenaAdminViewSmilies extends KunenaView {
	function displayDefault() {
		$this->setToolBarDefault();
		$this->smileys = $this->get('Smileys');
		$this->state = $this->get('state');
		$this->navigation = $this->get ( 'AdminNavigation' );
		$this->display();
	}

	function displayAdd() {
		return $this->displayEdit();
	}

	function displayEdit() {
		$this->setToolBarEdit();
		$this->state = $this->get('state');
		$this->smiley_selected = $this->get('smiley');
		$this->smileypath = $this->ktemplate->getSmileyPath();
		$this->listsmileys = $this->get('Smileyspaths');
		$this->display();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_NEW_SMILIE');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete', 'delete.png', 'delete_f2.png', 'COM_KUNENA_GEN_DELETE');
		JToolBarHelper::spacer();
	}

	protected function setToolBarEdit() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('smileys');
	}
}
