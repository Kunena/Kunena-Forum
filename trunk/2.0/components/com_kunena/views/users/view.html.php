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
jimport ( 'joomla.html.pagination' );

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
		$this->pageNav = new JPagination ( $this->total, $this->state->get('list.start'), $this->state->get('list.limit') );
		$this->setTitle(JText::_('COM_KUNENA_VIEW_USERS_DEFAULT'));
		parent::display($tpl);
	}
}