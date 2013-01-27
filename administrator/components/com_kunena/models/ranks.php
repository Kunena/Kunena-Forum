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
class KunenaAdminModelRanks extends JModelList {

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'rank_id', 'a.rank_id',
				'rank_title', 'a.rank_title',
				'rank_min', 'a.rank_min',
				'rank_special', 'a.rank_special',
				'rank_image', 'a.rank_image',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {

		// List state information
		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.ranks.list.filter_title', 'filter_title', '', 'string' );
		$this->setState ( 'list.filter_title', $value !== '' ? $value : null );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.ranks.list.filter_special', 'filter_special', '', 'string' );
		$this->setState ( 'list.filter_special', $value !== '' ? (int) $value : null );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.ranks.list.filter_min', 'filter_min', '', 'string' );
		$this->setState ( 'list.filter_min', $value !== '' ? $value : null );

		// List state information.
		parent::populateState('a.rank_id', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.filter_title');
		$id	.= ':'.$this->getState('list.filter_special');
		$id	.= ':'.$this->getState('list.filter_min');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
					'list.select',
					'a.rank_id, a.rank_title, a.rank_min, a.rank_special, a.rank_image'
			)
		);

		$query->from('#__kunena_ranks AS a');

		// Filter by access level.
		$title = $this->getState ( 'list.filter_title');
		if (!empty($title))
		{
			$title = $db->Quote('%'.$db->escape($title, true).'%');
			$query->where('(a.rank_title LIKE '.$title.')');
		}

		$special = $this->getState('list.filter_special');
		if (is_numeric($special))
		{
			$query->where('a.rank_special = ' . (int) $special);
		}
		elseif ($special === '')
		{
			$query->where('(a.rank_special = 0 OR a.rank_special = 1)');
		}

		$min = $this->getState ( 'list.filter_min');
		if (!empty($min))
		{
			$min = $db->Quote('%'.$db->escape($min, true).'%');
			$query->where('(a.rank_min LIKE '.$min.')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol.' '.$orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/*public function getRanks() {
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
	}*/

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

}
