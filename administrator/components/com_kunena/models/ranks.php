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
 * Ranks Model for Kunena
 *
 * @since 2.0
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
		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.ranks.list.limit", 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
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
			$selected = $db->loadObject ();
			if (KunenaError::checkDatabaseError()) return;

			return $selected;
		}
		return null;
	}

	public function getRankspaths() {
		$template = KunenaFactory::getTemplate();

		if ( $this->getState('item.id') ) {
			$selected = $this->getRank();
		}

		$rankpath = $template->getRankPath();
		$files1 = (array) JFolder::Files(JPATH_SITE.'/'.$rankpath,false,false,false,array('index.php','index.html'));
		$files1 = (array) array_flip($files1);
		foreach ($files1 as $key=>&$path) $path = $rankpath.$key;

		$rankpath = 'media/kunena/ranks/';
		$files2 = (array) JFolder::Files(JPATH_SITE.'/'.$rankpath,false,false,false,array('index.php','index.html'));
		$files2 = (array) array_flip($files2);
		foreach ($files2 as $key=>&$path) $path = $rankpath.$key;

		$rank_images = $files1 + $files2;
		ksort($rank_images);

		$rank_list = array();
		$i = 0;
		foreach ( $rank_images as $file => $path ) {
			$rank_list[] = JHTML::_ ( 'select.option', $path, $file );
		}
		$list = JHTML::_('select.genericlist', $rank_list, 'rank_image', 'class="inputbox" onchange="update_rank(this.options[selectedIndex].value);" onmousemove="update_rank(this.options[selectedIndex].value);"', 'value', 'text', isset($selected) ? $selected->rank_image : '' );

		return $list;
	}

	public function getAdminNavigation() {
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
