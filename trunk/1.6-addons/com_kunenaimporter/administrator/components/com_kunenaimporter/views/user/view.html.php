<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
defined('_JEXEC') or die();

jimport ( 'joomla.application.component.view' );

/**
 * HTML View class for the Users component
 *
 * @static
 * @package		Joomla
 * @subpackage	Users
 * @since 1.0
 */
class KunenaImporterViewUser extends JView {
	function display($tpl = null) {
		$cid = JRequest::getVar ( 'cid', array (0 ), 'get', 'array' );
		$edit = JRequest::getVar ( 'edit', true );
		$me = JFactory::getUser ();
		JArrayHelper::toInteger ( $cid, array (0 ) );

		$db = JFactory::getDBO ();
		$user = JTable::getInstance ( 'ExtUser', 'CKunenaTable' );
		$user->load ( $cid [0] );

		$importer = $this->getModel ( 'import' );
		$items = $importer->findPotentialUsers ( $user->extid, $user->extusername, $user->email, $user->registerDate );
		$myuser = JFactory::getUser ();
		$acl = JFactory::getACL ();

		// Check for post data in the event that we are returning
		// from a unsuccessful attempt to save data
		/*		$post = JRequest::get('post');
		if ( $post ) {
			$user->bind($post);
		}*/

		// build the html select list
		$lists ['block'] = JHTML::_ ( 'select.booleanlist', 'block', 'class="inputbox" size="1"', $user->get ( 'block' ) );

		// build the html select list
		$lists ['sendEmail'] = JHTML::_ ( 'select.booleanlist', 'sendEmail', 'class="inputbox" size="1"', $user->get ( 'sendEmail' ) );

		$this->assignRef ( 'me', $me );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'user', $user );
		$this->assignRef ( 'items', $items );

		JToolBarHelper::makeDefault ( 'select', 'Select' );
		JToolBarHelper::back ();

		parent::display ( $tpl );
	}
}