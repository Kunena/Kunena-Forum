<?php
/**
 * @version $Id: view.html.php 4443 2011-02-18 19:51:15Z xillibit $
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
 * Topicicons view for Kunena backend
 */
class KunenaAdminViewTopicicons extends KunenaView {
  function display() {
		$this->assignRef ( 'navigation', $this->get ( 'AdminNavigation' ) );
		switch ($this->getLayout ()) {
			case 'add' :
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

	function displayDefault() {
		$this->template = $template = KunenaFactory::getTemplate();
		$this->topicicons = $this->get('Topicicons');
		$this->state = $this->get('state');
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_NEW_TOPICICON');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete', 'delete.png', 'delete_f2.png', 'COM_KUNENA_GEN_DELETE');
		JToolBarHelper::spacer();
	}

	function displayEdit() {
		$this->state = $this->get('state');
		$this->topicicon = $this->get('topicicon');
		$this->template = KunenaFactory::getTemplate();
		$this->listtopicicons = $this->get('topiciconslist');
	}

	protected function setToolBarEdit() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('ranks');
	}
}
