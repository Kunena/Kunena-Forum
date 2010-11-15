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

jimport ( 'joomla.application.component.view' );

/**
 * About view for Kunena backend
 */
class KunenaViewManage extends JView {
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

		$this->addStyleSheet ( 'css/kunena.forum.css' );
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
		$admin = KunenaFactory::getUser()->getAllowedCategories('admin');
		if (empty($admin)) {
			$this->setError(JText::_('COM_KUNENA_NO_ACCESS'));
		}
		$this->assignRef ( 'categories', $this->get ( 'Items' ) );
		$this->assignRef ( 'navigation', $this->get ( 'Navigation' ) );
		$header = JText::_('COM_KUNENA_ADMIN');
		$this->assign ( 'header', $header );
		$this->setTitle ( $header );
	}

	function displayNoAccess($errors = array()) {
		$output = '';
		foreach ($errors as $error) $output .= "<div>{$error}</div>";
		$this->common->setLayout ( 'default' );
		$this->common->assign ( 'header', JText::_('COM_KUNENA_ACCESS_DENIED'));
		$this->common->assign ( 'body', $output);
		$this->common->display();
	}

	function displayCommon($layout) {
		$this->common->setLayout ( $layout );
		$this->common->display();
	}

	function addStyleSheet($filename) {
		if (!KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.css$/u', '-min.css', $filename );
		}
		$document = JFactory::getDocument ();
		$template = KunenaFactory::getTemplate();
		return $document->addStyleSheet ( $template->getFile($filename) );
	}

	function addScript($filename) {
		if (!KunenaFactory::getConfig ()->debug && !KunenaForum::isSvn()) {
			// If we are in debug more, make sure we load the unpacked css
			$filename = preg_replace ( '/\.js$/u', '-min.js', $filename );
		}
		$document = JFactory::getDocument ();
		$template = KunenaFactory::getTemplate();
		return $document->addScript ( $template->getFile($filename) );
	}

	function setTitle($title) {
		$this->document->setTitle ( $this->document->getTitle() .' :: '. strip_tags($title) );
	}
}