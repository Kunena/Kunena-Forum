<?php
/**
 * @version $Id: attachments.php 4611 2011-03-11 18:35:52Z mahagr $
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
kimport ( 'kunena.html.pagination' );

/**
 * Topicicons Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelTopicicons extends KunenaModel {
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
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.topicicons.list.limit", 'limit', $app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.topicicons.list.ordering', 'filter_order', 'filename', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.topicicons.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.topicicons.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$id = $this->getInt ( 'id', 0 );
		$this->setState ( 'item.id', $id );
	}

	public function getTopicicons() {
		$db = JFactory::getDBO ();

		$orderby = ' ORDER BY '. $this->getState ( 'list.ordering' ) .' '. $this->getState ( 'list.direction' );

		$query = "SELECT * FROM #__kunena_topics_icons".$orderby;
		$db->setQuery ( $query, $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		$topicicons = $db->loadObjectlist();
		if (KunenaError::checkDatabaseError()) return;

		foreach ( $topicicons as $icon ) {
			$icon->up = '';
			$icon->down = '';
		}

		$this->setState ( 'list.total', count($topicicons) );

		return $topicicons;
	}

	public function getTopicicon() {
		$db = JFactory::getDBO ();

		$id = $this->getState ( 'item.id' );

		$query = "SELECT * FROM #__kunena_topics_icons WHERE id='$id'";
		$db->setQuery ( $query );
		$topicicon = $db->loadObject();
		if (KunenaError::checkDatabaseError()) return;

		return $topicicon;
	}

	public function gettopiciconslist() {
		$topicons = $this->getTopicicons();

		$topiciconselected = '';
		if ( $this->getState('item.id') ) {
			$topiciconselected = $this->getTopicicon();
			$topiciconselected = $topiciconselected->id;
		}

		$icons =  array ();
		foreach($topicons as $icon) {
			$icons [] = JHTML::_ ( 'select.option', $icon->filename, $icon->filename );
		}
		return JHTML::_ ( 'select.genericlist', $icons, 'topiciconslist', 'class="inputbox" onchange="update_topicicon(this.options[selectedIndex].value);" onmousemove="update_topicicon(this.options[selectedIndex].value);"', 'value', 'text', isset($topiciconselected) ? $topiciconselected : '' );
	}

	public function getAdminNavigation() {
		$navigation = new KunenaHtmlPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
