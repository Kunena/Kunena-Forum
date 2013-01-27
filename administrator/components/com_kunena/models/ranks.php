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
	protected static $_instances = false;

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

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.ranks.list.filter_title', 'filter_title', '', 'string' );
		$this->setState ( 'list.filter_title', $value !== '' ? $value : null );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.ranks.list.filter_special', 'filter_special', '', 'string' );
		$this->setState ( 'list.filter_special', $value !== '' ? (int) $value : null );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.ranks.list.filter_min', 'filter_min', '', 'string' );
		$this->setState ( 'list.filter_min', $value !== '' ? $value : null );

		$id = $this->getInt ( 'id', 0 );
		$this->setState ( 'item.id', $id );
	}

	public function getRanks() {
		$rlist = array();
		$list = array();
		if (self::$_instances === false) {
			self::loadRanks();
			$rlist = self::$_instances;
		}

		$params = array (
			'filter_title'=>$this->getState ( 'list.filter_title'),
			'filter_special'=>$this->getState ( 'list.filter_special'),
			'filter_min'=>$this->getState ( 'list.filter_min'),
			'action'=>'admin');

		$action = isset($params['action']) ? (string) $params['action'] : 'read';

		foreach ( $rlist as $id => $instance ) {

			if (! isset ( self::$_instances [$id] ))
				continue;

			$instance = self::$_instances [$id];
			//print_r($instance);

			$filtered = isset($params['filter_title']) && (JString::stristr($instance->rank_title, (string) $params['filter_title']) === false);
			$filtered |= isset($params['filter_special']) && $instance->rank_special != (int) $params['filter_special'];
			$filtered |= isset($params['filter_min']) && (JString::stristr($instance->rank_min, (string) $params['filter_min']) === false);

			if ($filtered && $action != 'admin') continue;

			//print_r($instance);

			if (!$filtered) $list [$id] = $instance;
		}
		return $list;
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
			$rank_list[] = JHtml::_ ( 'select.option', $path, $file );
		}
		$list = JHtml::_('select.genericlist', $rank_list, 'rank_image', 'class="inputbox" onchange="update_rank(this.options[selectedIndex].value);" onmousemove="update_rank(this.options[selectedIndex].value);"', 'value', 'text', isset($selected) ? $selected->rank_image : '' );

		return $list;
	}

	public function getAdminNavigation() {
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}

	public function loadRanks() {

		$db = JFactory::getDBO ();

		$db->setQuery ( "SELECT COUNT(*) FROM #__kunena_ranks" );
		$total = $db->loadResult ();
		KunenaError::checkDatabaseError();

		$this->setState ( 'list.total',$total );

		$db->setQuery ( "SELECT * FROM #__kunena_ranks", $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		$results = $db->loadObjectList ();

		KunenaError::checkDatabaseError();

		self::$_instances = array();

		foreach ( $results as $rank ) {
			self::$_instances [$rank->rank_id] = $rank;
		}
		unset ($results);

	}
}
