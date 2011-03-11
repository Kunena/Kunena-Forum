<?php
/**
 * @version $Id$
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
 * Smileys Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
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
		$app = JFactory::getApplication ();

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.smilies.list.limit", 'limit', $app->getCfg ( 'list_limit' ), 'int' );
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

   	 	if ( $this->getState('item.id') ) {
      		$db->setQuery ( "SELECT * FROM #__kunena_smileys WHERE id = '{$this->getState('item.id')}'" );
		 	$rankselected = $db->loadObject ();
		  	if (KunenaError::checkDatabaseError()) return;

	 	   	return $rankselected;
		}
		return;
	}

	public function getSmileyspaths() {
		$template = KunenaFactory::getTemplate();

		$smileyselected = '';
		if ( $this->getState('item.id') ) {
			$smileyselected = $this->getSmiley();
			$smileyselected = $smileyselected->smiley_id;
		}

		$smileypath = $template->getSmileyPath();
		$smiley_images = (array)JFolder::Files(KPATH_SITE.'/'.$smileypath,false,false,false,array('index.php','index.html'));

		$smiley_list = array();
		$i = 0;
		foreach ( $smiley_images as $id => $row ) {
			$smiley_list[] = JHTML::_ ( 'select.option', $smiley_images [$id], $smiley_images [$id] );
		}
		$list = JHTML::_('select.genericlist', $smiley_list, 'smiley_url', 'class="inputbox" onchange="update_smiley(this.options[selectedIndex].value);" onmousemove="update_smiley(this.options[selectedIndex].value);"', 'value', 'text', isset($smileyselected) ? $smileyselected : '' );

		return $list;
	}

	public function getAdminNavigation() {
		$navigation = new KunenaHtmlPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
