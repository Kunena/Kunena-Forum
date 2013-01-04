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
class KunenaAdminModelSmilies extends KunenaModel {
	protected $__state_set = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.smilies.list.limit", 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.smilies.list.ordering', 'filter_order', 'ordering', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.smilies.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$id = $this->getInt ( 'id', 0 );
		$this->setState ( 'item.id', $id );
	}

	public function getSmileys() {
		$db = JFactory::getDBO ();

		$db->setQuery ( "SELECT COUNT(*) FROM #__kunena_smileys" );
		$total = $db->loadResult ();
		if (KunenaError::checkDatabaseError()) return;

		$this->setState ( 'list.total',$total );

		$db->setQuery ( "SELECT * FROM #__kunena_smileys", $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		$smileys = $db->loadObjectList ();
		if (KunenaError::checkDatabaseError()) return;

		return $smileys;
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
	}

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
			$smiley_list[] = JHTML::_ ( 'select.option', $path, $file );
		}
		$list = JHTML::_('select.genericlist', $smiley_list, 'smiley_url', 'class="inputbox" onchange="update_smiley(this.options[selectedIndex].value);" onmousemove="update_smiley(this.options[selectedIndex].value);"', 'value', 'text', !empty($selected) ? $selected->location : '' );

		return $list;
	}

	public function getAdminNavigation() {
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
