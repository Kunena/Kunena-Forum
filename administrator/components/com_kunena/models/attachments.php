<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
jimport( 'joomla.html.pagination' );

/**
 * Attachments Model for Kunena
 *
 * @since 2.0
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
		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.attachments.list.limit", 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.ordering', 'filter_order', 'a.filename', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.attachments.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.search', 'filter_search', '', 'string' );
		$this->setState ( 'list.search', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.filter_name', 'filter_name', '', 'string' );
		$this->setState ( 'list.filter_name', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.filter_type', 'filter_type', '', 'string' );
		$this->setState ( 'list.filter_type', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.filter_size', 'filter_size', '', 'string' );
		$this->setState ( 'list.filter_size', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.filter_dims', 'filter_dims', '', 'string' );
		$this->setState ( 'list.filter_dims', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.filter_username', 'filter_username', '', 'string' );
		$this->setState ( 'list.filter_username', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.attachments.list.filter_post', 'filter_post', '', 'string' );
		$this->setState ( 'list.filter_post', $value );
	}

	public function getItems() {
		$db = JFactory::getDBO ();

		$where = '';
		if ($this->getState ( 'list.search' )) {
			$where = ' WHERE LOWER( a.filename ) LIKE '.$db->Quote( '%'.$db->escape( $this->getState ( 'list.search' ), true ).'%', false ).' OR LOWER( a.filetype ) LIKE '.$db->Quote( '%'.$db->escape( $this->getState ( 'list.search' ), true ).'%', false );
		}

		$orderby = ' ORDER BY '. $this->getState ( 'list.ordering' ) .' '. $this->getState ( 'list.direction' );

		$db->setQuery ( "SELECT COUNT(*) FROM #__kunena_attachments AS a LEFT JOIN #__kunena_messages AS b ON a.mesid=b.id".$where.$orderby);
		$total = $db->loadResult ();
		KunenaError::checkDatabaseError();

		$this->setState ( 'list.total', $total );

		$query = "SELECT a.*, b.catid, b.thread FROM #__kunena_attachments AS a LEFT JOIN #__kunena_messages AS b ON a.mesid=b.id".$where.$orderby;
		$db->setQuery ( $query, $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		$uploaded = $db->loadObjectlist();
		if (KunenaError::checkDatabaseError()) return;

		return $uploaded;
	}

	public function getAdminNavigation() {
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
