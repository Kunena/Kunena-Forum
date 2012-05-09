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

/**
 * Topicicons Model for Kunena
 *
 * @since 2.0
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
		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.topicicons.list.limit", 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.topicicons.list.ordering', 'filter_order', 'name', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.topicicons.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.topicicons.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$id = $this->getInt ( 'id', 0 );
		$this->setState ( 'item.id', $id );

		$iconset = $this->getString ( 'iconset' );
		$this->setState ( 'list.iconset', $iconset );
		if ($id == '0') $this->app->setUserState('com_kunena.iconset',$iconset );
	}

	public function getTopicicons() {
	  $iconset = $this->app->getUserState('com_kunena.iconset');

	  $set = 'default';
	  if ( $iconset != '0' ) $set = $iconset;
		$topicicons_xml = simplexml_load_file(JPATH_ROOT."/media/kunena/topicicons/{$set}/topicicons.xml");
		$topicicons_array = array();
		$filename = array();

		foreach($topicicons_xml->icons as $icons) {

			foreach($icons->icon as $icon) {
				$topicicons = new stdClass();
				$id = (Int) $icon->attributes()->id;

				$topicicons->id = (Int) $icon->attributes()->id;
				$topicicons->name = (String) $icon->attributes()->name;
				$topicicons->published = (Int) $icon->attributes()->published;
				$topicicons->ordering = (Int) $icon->attributes()->ordering;
				$topicicons->isdefault = (Int) $icon->attributes()->isdefault;
				$topicicons->title = (String) $icon->attributes()->title;
				$topicicons->filename = (String) $icon->attributes()->src;

				$topicicons_array[$id] = $topicicons;
			}
		}

		$this->setState ( 'list.total', count($topicicons_array) );

		return $topicicons_array;
	}

	public function getTopicicon() {
		$id = $this->getState ( 'item.id' );
		if ( $id ) {
		$topicicon_details = new stdClass();
		$id = (Int) $id;

		$iconset = $this->app->getUserState('com_kunena.iconset');
		$set = 'default';
	  if ( $iconset != '0' ) $set = $iconset;
		$topicicons_xml = simplexml_load_file(JPATH_ROOT."/media/kunena/topicicons/{$set}/topicicons.xml");
		$topicicon=$topicicons_xml->icons->icon[$id];

		$topicicon_details->id = (Int) $topicicon['id'];
		$topicicon_details->name = (String) $topicicon['name'];
		$topicicon_details->published = (Int) $topicicon['published'];
		$topicicon_details->filename =(String) $topicicon['src'];
		$topicicon_details->title =(String) $topicicon['title'];

		return $topicicon_details;
		}
		return;
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

	public function getIconsetlist() {
	  jimport( 'joomla.filesystem.folder' );
	  $topicicons = array ();
    $topiciconslist = JFolder::folders(JPATH_ROOT.'/media/kunena/topicicons');
    $topicicons[] = JHTML::_ ( 'select.option', '', JText::_('COM_KUNENA_A_ICONSET_LIST') );
    foreach( $topiciconslist as $icon ) {
			$topicicons[] = JHTML::_ ( 'select.option', $icon, $icon );
		}
		return JHTML::_ ( 'select.genericlist', $topicicons, 'iconset', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text' );
  }

	public function getAdminNavigation() {
		$navigation = new KunenaHtmlPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
