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
 * Smileys Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelSmilies extends JModelList {

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'code', 'a.code',
				'location', 'a.location',
				'greylocaktion', 'a.greylocation',
				'emoticonbar', 'a.emoticonbar',
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

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.smilies.list.filter_code', 'filter_code', '', 'string' );
		$this->setState ( 'list.filter_code', $value !== '' ? $value : null );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.smilies.list.filter_url', 'filter_url', '', 'string' );
		$this->setState ( 'list.filter_url', $value !== '' ? $value : null );

		// List state information.
		parent::populateState('a.id', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('list.filter_code');
		$id	.= ':'.$this->getState('list.filter_url');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.code, a.location, a.greylocation, a.emoticonbar'
			)
		);

		$query->from('#__kunena_smileys AS a');

		// Filter by access level.
		$code = $this->getState ( 'list.filter_code');
		if (!empty($code))
		{
			$code = $db->Quote('%'.$db->escape($code, true).'%');
			$query->where('(a.code LIKE '.$code.')');
		}

		// Filter by access level.
		$url = $this->getState ( 'list.filter_url');
		if (!empty($code))
		{
			$url = $db->Quote('%'.$db->escape($url, true).'%');
			$query->where('(a.location LIKE '.$url.')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol.' '.$orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/*public function getSmileys() {
		$rlist = array();
		$list = array();
		if (self::$_instances === false) {
			self::loadSmileys();
			$rlist = self::$_instances;
		}

		$params = array (
			'filter_code'=>$this->getState ( 'list.filter_code'),
			'filter_url'=>$this->getState ( 'list.filter_url'),
			'action'=>'admin');

		$action = isset($params['action']) ? (string) $params['action'] : 'read';

		foreach ( $rlist as $id => $instance ) {

			if (! isset ( self::$_instances [$id] ))
				continue;

			$instance = self::$_instances [$id];
			//print_r($instance);

			$filtered = isset($params['filter_code']) && (JString::stristr($instance->code, (string) $params['filter_code']) === false);
			$filtered |= isset($params['filter_url']) && (JString::stristr($instance->location, (string) $params['filter_url']) === false);

			if ($filtered && $action != 'admin') continue;

			//print_r($instance);

			if (!$filtered) $list [$id] = $instance;
		}
		return $list;
	}

	public function getSmiley() {
		$db = JFactory::getDBO ();

		if ($this->getState ( 'item.id' )) {
			$db->setQuery ( "SELECT * FROM #__kunena_smileys WHERE id = '{$this->getState('item.id')}'" );
			$selected = $db->loadObject ();
			if (KunenaError::checkDatabaseError ())
				return;

			return $selected;
		}
		return null;
	}*/

	public function getSmileyspaths() {
		$template = KunenaFactory::getTemplate();

		if ( $this->getState('item.id') ) {
			$selected = $this->getSmiley();
		}

		$smileypath = $template->getSmileyPath();
		$files1 = (array) JFolder::Files(JPATH_SITE.'/'.$smileypath,false,false,false,array('index.php','index.html'));
		$files1 = (array) array_flip($files1);
		foreach ($files1 as $key=>&$path) $path = $smileypath.$key;

		$smileypath = 'media/kunena/emoticons/';
		$files2 = (array) JFolder::Files(JPATH_SITE.'/'.$smileypath,false,false,false,array('index.php','index.html'));
		$files2 = (array) array_flip($files2);
		foreach ($files2 as $key=>&$path) $path = $smileypath.$key;

		$smiley_images = $files1 + $files2;
		ksort($smiley_images);

		$smiley_list = array();
		$i = 0;
		foreach ( $smiley_images as $file => $path ) {
			$smiley_list[] = JHtml::_ ( 'select.option', $path, $file );
		}
		$list = JHtml::_('select.genericlist', $smiley_list, 'smiley_url', 'class="inputbox" onchange="update_smiley(this.options[selectedIndex].value);" onmousemove="update_smiley(this.options[selectedIndex].value);"', 'value', 'text', !empty($selected) ? $selected->location : '' );

		return $list;
	}

	public function getAdminNavigation() {
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}

}
