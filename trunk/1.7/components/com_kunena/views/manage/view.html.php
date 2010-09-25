<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.view' );

/**
 * About view for Kunena backend
 */
class KunenaViewManage extends JView {
	function display() {
		switch ($this->getLayout ()) {
			case 'new' :
			case 'edit' :
				$this->displayEdit ();
				break;
			case 'default' :
				$this->displayDefault ();
				break;
		}
		parent::display ();
	}

	function displayEdit() {
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		$this->assignRef ( 'category', $this->get ( 'Item' ) );
		$this->assignRef ( 'options', $this->get ( 'Options' ) );
		$this->assignRef ( 'moderators', $this->get ( 'Moderators' ) );
	}

	function displayDefault() {
		$this->assignRef ( 'state', $this->get ( 'State' ) );
		$this->assignRef ( 'categories', $this->get ( 'Items' ) );
		$this->assignRef ( 'navigation', $this->get ( 'Navigation' ) );
	}
}