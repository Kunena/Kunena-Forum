<?php
/**
 * @version $Id: categories.php 4387 2011-02-08 16:19:37Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
kimport('kunena.model');
kimport ( 'kunena.error' );

/**
 * Attachments Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelAttachments extends KunenaModel {
	protected $__state_set = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication ();

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.attachments.list.limit", 'limit', $app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.ordering', 'filter_order', 'filename', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.attachments.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.search', 'search', '', 'string' );
		$this->setState ( 'list.search', $value );
	}

	public function getItems() {
		$db = JFactory::getDBO ();

		$where = '';
		if ($this->getState ( 'list.search' )) {
			$where = ' WHERE LOWER( a.filename ) LIKE '.$db->Quote( '%'.$db->getEscaped( $this->getState ( 'list.search' ), true ).'%', false ).' OR LOWER( a.filetype ) LIKE '.$db->Quote( '%'.$db->getEscaped( $this->getState ( 'list.search' ), true ).'%', false );
		}

		$orderby = ' ORDER BY '. $this->getState ( 'list.ordering' ) .' '. $this->getState ( 'list.direction' );

		$query = "SELECT a.*, b.catid, b.thread FROM #__kunena_attachments AS a LEFT JOIN #__kunena_messages AS b ON a.mesid=b.id".$where.$orderby;
		$db->setQuery ( $query, $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		$uploaded = $db->loadObjectlist();
		if (KunenaError::checkDatabaseError()) return;

		$this->setState ( 'list.total', count($uploaded) );

		return $uploaded;
	}

	public function getAdminNavigation() {
		kimport ( 'kunena.html.pagination' );
		$navigation = new KunenaHtmlPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
