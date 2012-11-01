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
 * Topicicons view for Kunena backend
 */
class KunenaAdminViewTopicicons extends KunenaView {
	function displayDefault() {
		$this->navigation = $this->get ( 'AdminNavigation' );
		$this->topicicons = $this->get('Topicicons');
		$this->state = $this->get('state');
		$this->iconsetlist = $this->get('iconsetlist');

		$this->setToolBarDefault();
		parent::display ();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_NEW_TOPICICON');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete', 'delete.png', 'delete_f2.png', 'COM_KUNENA_GEN_DELETE');
		JToolBarHelper::spacer();
	}

	function displayAdd() {
		$this->displayEdit();
	}

	function displayEdit() {
		$this->navigation = $this->get ( 'AdminNavigation' );
		$this->state = $this->get('state');
		$this->topicicon = $this->get('topicicon');
		$this->listtopicicons = $this->get('topiciconslist');

		$this->setToolBarEdit();
		parent::display ();
	}

	protected function setToolBarEdit() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('ranks');
	}
}
