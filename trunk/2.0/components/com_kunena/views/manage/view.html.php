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
kimport ( 'kunena.forum.category.helper' );

/**
 * About view for Kunena backend
 */
class KunenaViewManage extends KunenaView {
	function display() {
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		if ($this->state->get('item.id')) $this->setLayout ('edit');
		switch ($this->getLayout ()) {
			case 'new' :
			case 'edit' :
				$this->displayEdit ();
				break;
			case 'default' :
				$this->displayDefault ();
				break;
		}

		// FIXME: remove
		$lang = JFactory::getLanguage();
		if (KunenaForum::isSVN()) {
			$lang->load('com_kunena',KPATH_ADMIN);
			$lang->load('com_kunena.install',KPATH_ADMIN);
		} else {
			$lang->load('com_kunena',JPATH_ADMINISTRATOR);
			$lang->load('com_kunena.install',JPATH_ADMINISTRATOR);
		}

		$this->addStyleSheet ( 'css/kunena.forum.css' );
		$this->addStyleSheet ( 'css/kunena.skinner.css' );
		$this->addStyleSheet ( 'css/kunena.manage.css' );

		echo '<div id="Kunena">';
		$this->displayCommon('menu');
		$this->displayCommon('loginbox');
		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
		} else {
			parent::display ();
		}
		$this->displayCommon('footer');
		echo '</div>';
	}

	function displayEdit() {
		$this->assignRef ( 'me', KunenaFactory::getUser() );
		$this->assignRef ( 'category', $this->get ( 'Item' ) );
		$this->assignRef ( 'options', $this->get ( 'Options' ) );
		$this->assignRef ( 'moderators', $this->get ( 'Moderators' ) );
		if ($this->category === false) {
			$this->setError(JText::_('COM_KUNENA_NO_ACCESS'));
			return;
		}
		$header = $this->category->exists() ? JText::sprintf('COM_KUNENA_CATEGORY_EDIT', $this->escape($this->category->name)) : JText::_('COM_KUNENA_CATEGORY_NEW');
		$this->assign ( 'header', $header );
		$this->setTitle ( $header );
		$this->setLayout('edit');
	}

	function displayDefault() {
		$admin = KunenaForumCategoryHelper::getCategories(false, false, 'admin');
		if (empty($admin)) {
			$this->setError(JText::_('COM_KUNENA_NO_ACCESS'));
		}
		$this->assignRef ( 'categories', $this->get ( 'Items' ) );
		$this->assignRef ( 'navigation', $this->get ( 'Navigation' ) );
		$header = JText::_('COM_KUNENA_ADMIN');
		$this->assign ( 'header', $header );
		$this->setTitle ( $header );
	}
}