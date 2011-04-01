<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );
kimport ( 'kunena.html.pagination' );

/**
 * Users View
 */
class KunenaViewUsers extends KunenaView {
	function displayDefault($tpl = null) {
		$this->app = JFactory::getApplication();
		$this->config = KunenaFactory::getConfig();
		$this->total = $this->get ( 'Total' );
		$this->count = $this->get ( 'Count' );
		$this->users = $this->get ( 'Items' );
		// TODO: Deprecated:
		$this->pageNav = $this->getPagination(7);
		parent::display($tpl);
	}

	function displayUserList() {
		echo $this->loadTemplate('list');
	}

	function displayUserRow($user) {
		$this->user = KunenaFactory::getUser($user->id);
		$this->rank_image = $this->user->getRank (0, 'image');
		$this->rank_title = $this->user->getRank (0, 'title');
		echo $this->loadTemplate('row');
	}

	function getPagination($maxpages) {
		$pagination = new KunenaHtmlPagination ( $this->count, $this->state->get('list.start'), $this->state->get('list.limit') );
		$pagination->setDisplay($maxpages);
		return $pagination->getPagesLinks();
	}
}