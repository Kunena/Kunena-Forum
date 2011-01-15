<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
kimport('kunena.forum.message.helper');
kimport('kunena.forum.topic.helper');

class CKunenaPost {
	public $allow = 0;

	function __construct() {
		$this->do = JRequest::getCmd ( 'do', '' );
		$this->action = JRequest::getCmd ( 'action', '' );

		$this->_app = JFactory::getApplication ();
		$this->config = KunenaFactory::getConfig ();
		$this->_session = KunenaFactory::getSession ();
		$this->_db = JFactory::getDBO ();
		$this->document = JFactory::getDocument ();
		require_once (KPATH_SITE . DS . 'lib' .DS. 'kunena.poll.class.php');
		$this->poll = CKunenaPolls::getInstance();

		$this->my = JFactory::getUser ();
		$this->me = KunenaFactory::getUser ();

		$this->catid = JRequest::getInt ( 'catid', 0 );

		$this->allow = 1;

		$this->cat_default_allow = null;

		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		$this->numLink = null;
		$this->replycount= null;
	}

	// Helper functions

	function display() {
		if (! $this->allow)
			return;
		if ($this->action == "post") {
			$this->post ();
			return;
		} else if ($this->action == "cancel") {
			$this->_app->enqueueMessage ( JText::_ ( 'COM_KUNENA_SUBMIT_CANCEL' ) );
			return;
		}

		switch ($this->do) {
			case 'new' :
				$this->newtopic ( $this->do );
				break;

			case 'reply' :
			case 'quote' :
				$this->reply ( $this->do );
				break;

			case 'edit' :
				$this->edit ();
				break;

			case 'moderate' :
				$this->moderate (false);
				break;

			case 'moderatethread' :
				$this->moderate (true);
				break;
		}
	}

	function setTitle($title) {
		$this->document->setTitle ( $title . ' - ' . $this->config->board_title );
	}
}
