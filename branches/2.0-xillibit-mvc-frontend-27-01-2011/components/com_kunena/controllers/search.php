<?php
/**
 * @version		$Id: category.php 3965 2010-12-08 13:40:51Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.controller' );

/**
 * Kunena Search Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaControllerSearch extends KunenaController {
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->config = KunenaFactory::getConfig();
		$this->app = JFactory::getApplication ();
	}

	public function results() {
		require_once KPATH_SITE . '/lib/kunena.link.class.php';
		DEFINE ( 'KUNENA_URL_LIST_SEPARATOR', ' ' );
		$defaults = array ('titleonly' => 0, 'searchuser' => '', 'exactname' => 0, 'childforums' => 0, 'starteronly' => 0, 'replyless' => 0, 'replylimit' => 0, 'searchdate' => '365', 'beforeafter' => 'after', 'sortby' => 'lastpost', 'order' => 'dec', 'catids' => '0', 'show' => '0' );

		// Search words
		$q = JRequest::getVar ( 'q', '' );
		$q = JString::trim ( $q );
		$params ['titleonly'] = JRequest::getInt ( 'titleonly', $defaults ['titleonly'] );
		$params ['searchuser'] = JRequest::getVar ( 'searchuser', $defaults ['searchuser'] );
		$params ['starteronly'] = JRequest::getInt ( 'starteronly', $defaults ['starteronly'] );
		$params ['exactname'] = JRequest::getInt ( 'exactname', $defaults ['exactname'] );
		$params ['replyless'] = JRequest::getInt ( 'replyless', $defaults ['replyless'] );
		$params ['replylimit'] = JRequest::getInt ( 'replylimit', $defaults ['replylimit'] );
		$params ['searchdate'] = JRequest::getVar ( 'searchdate', $defaults ['searchdate'] );
		$params ['beforeafter'] = JRequest::getVar ( 'beforeafter', $defaults ['beforeafter'] );
		$params ['sortby'] = JRequest::getVar ( 'sortby', $defaults ['sortby'] );
		$params ['order'] = JRequest::getVar ( 'order', $defaults ['order'] );
		$params ['childforums'] = JRequest::getInt ( 'childforums', $defaults ['childforums'] );
		$params ['catids'] = strtr ( JRequest::getVar ( 'catids', '0', 'get' ), KUNENA_URL_LIST_SEPARATOR, ',' );
		$params ['show'] = JRequest::getInt ( 'show', $defaults ['show'] );
		$params ['limitstart'] = JRequest::getInt ( 'limitstart', 0 );
		$params ['limit'] = JRequest::getInt ( 'limit', $this->config->messages_per_page_search );
		extract ( $params );

		$this->app->setUserState('com_kunena.search', $params);
		$this->app->setUserState('com_kunena.searchword', $q);

		$this->app->redirect ( CKunenaLink::GetSearchURL('search','results') );
	}

}