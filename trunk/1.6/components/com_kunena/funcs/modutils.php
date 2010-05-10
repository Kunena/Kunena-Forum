<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

class CKunenaModutils {

	function __construct() {
		$this->func		= 'modutils';
		$this->do		= JRequest::getCmd ( 'do', '' );
		$this->action	= JRequest::getCmd ( 'action', '' );

		$this->_app		= & JFactory::getApplication ();
		$this->_config	= & CKunenaConfig::getInstance ();
		$this->_session = KunenaFactory::getSession ();
		$this->_db		= &JFactory::getDBO ();

		$this->document	= JFactory::getDocument ();
		$this->my		= &JFactory::getUser ();

		// Has to be at least moderator
		if ( ! CKunenaTools::isModerator ( $this->my->id )) {
			// do something nasty
			die();
		}

		// How to handle errors...
		//if ( !$success ) {
		//	$errors = $message->getErrors ();
		//	foreach ( $errors as $field => $error ) {
		//		$this->_app->enqueueMessage ( $field . ': ' . $error, 'error' );
		//	}
		//	$this->redirectBack ();
		//}

		return;
	}


	protected function displayForm( $template = 'index' ) {
		CKunenaTools::loadTemplate ( '/modutils/' . $template . '.php' );
	}


	public function display() {
		switch ( $this->do ) {

			// Forms & Pages

			case 'userban' :
				$this->displayForm ( 'userban.add' );
				break;

			case 'userbandetails' :
				$this->displayForm ( 'userban.view' );
				break;

			case 'userbanlist' :
				$this->displayForm ( 'userban.list' );
				break;

			case 'ipban' :
				$this->displayForm ( 'ipban.add' );
				break;

			case 'ipbandetails' :
				$this->displayForm ( 'ipban.view' );
				break;

			case 'ipbanlist' :
				$this->displayForm ( 'ipban.list' );
				break;

			// Actions

			case 'douserban' :
				$this->displayForm ( 'ipban.add' );
				break;

			case 'douserunban' :
				$this->displayForm ( 'ipban.add' );
				break;

			case 'doipban' :
				$this->displayForm ( 'ipban.add' );
				break;

			case 'doipunban' :
				$this->displayForm ( 'ipban.add' );
				break;

			default :
				$this->_app->enqueueMessage ( 'Wrong page?', 'error' );
		}
	}


	/**
	 * kept as example for now
	 * @deprecated
	 */
	protected function moderatorProtection() {
		//if ( !CKunenaTools::isModerator( $this->my->id ) ) {
		//	$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' ), 'notice' );
		//	return true;
		//}
		//return false;
	}


	/**
	 * kept as example for now
	 * @deprecated
	 */
	function setTitle($title) {
		//$this->document->setTitle ( $title . ' - ' . stripslashes ( $this->_config->board_title ) );
	}


	/**
	 * kept as example for now
	 * @deprecated
	 */
	function redirectBack() {
		//$httpReferer = JRequest::getVar ( 'HTTP_REFERER', JURI::base ( true ), 'server' );
		//$this->_app->redirect ( $httpReferer );
	}
}
?>