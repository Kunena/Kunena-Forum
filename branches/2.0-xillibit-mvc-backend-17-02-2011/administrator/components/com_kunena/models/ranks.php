<?php
/**
 * @version $Id: stats.php 4387 2011-02-08 16:19:37Z mahagr $
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
 * Ranks Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelRanks extends KunenaModel {
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
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.ranks.list.limit", 'limit', $app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.ranks.list.ordering', 'filter_order', 'ordering', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.ranks.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$id = $this->getInt ( 'id', 0 );
		$this->setState ( 'item.id', $id );
	}

	public function getRanks() {
		$db = JFactory::getDBO ();

		$db->setQuery ( "SELECT COUNT(*) FROM #__kunena_ranks" );
		$total = $db->loadResult ();
		if (KunenaError::checkDatabaseError()) return;

		$this->setState ( 'list.total',$total );

		$db->setQuery ( "SELECT * FROM #__kunena_ranks", $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		$ranks = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		return $ranks;
	}

	public function getRank() {
		$db = JFactory::getDBO ();

		if ( $this->getState('item.id') ) {
			$db->setQuery ( "SELECT * FROM #__kunena_ranks WHERE rank_id = '{$this->getState('item.id')}'" );
			$rankselected = $db->loadObject ();
			if (KunenaError::checkDatabaseError()) return;

			return $rankselected;
		}
		return;
	}

	public function getRankspaths() {
		$template = KunenaFactory::getTemplate();

		$rankselected = '';
		if ( $this->getState('item.id') ) {
			$rankselected = $this->getRank();
		}

		$rankpath = $template->getRankPath();
		$rank_images = (array)JFolder::Files(KPATH_SITE.'/'.$rankpath,false,false,false,array('index.php','index.html'));

		$rank_list = array();
		$i = 0;
		foreach ( $rank_images as $id => $row ) {
			$rank_list[] = JHTML::_ ( 'select.option', $rank_images [$id], $rank_images [$id] );
		}
		$list = JHTML::_('select.genericlist', $rank_list, 'rank_image', 'class="inputbox" onchange="update_rank(this.options[selectedIndex].value);" onmousemove="update_rank(this.options[selectedIndex].value);"', 'value', 'text', isset($rankselected) ? $rankselected->rank_id : '' );

		return $list;
	}

	public function getAdminNavigation() {
		kimport ( 'kunena.html.pagination' );
		$navigation = new KunenaHtmlPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
