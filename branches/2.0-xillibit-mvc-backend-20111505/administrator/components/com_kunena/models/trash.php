<?php
/**
 * @version		$Id: reportconfiguration.php 3901 2010-11-15 14:14:02Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );

/**
 * Trash Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelTrash extends JModel {
	protected $__state_set = false;
	protected $_items = false;
	protected $_items_order = false;
	protected $_object = false;


	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	Optional default value.
	 * @return	mixed	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null) {
		if (! $this->__state_set) {
			$this->__state_set = true;
		}
		return parent::getState ( $property );
	}

	/**
	 * Method to get all deleted items.
	 *
	 * @return	Array
	 * @since	1.6
	 */
	 public function getItems() {
		$kunena_db = &JFactory::getDBO ();
		$where 	= ' WHERE hold=2 ';
		$query = 'SELECT a.*, b.name AS cats_name, c.username FROM #__kunena_messages AS a
		INNER JOIN #__kunena_categories AS b ON a.catid=b.id
		LEFT JOIN #__users AS c ON a.userid=c.id'
		.$where;

		$kunena_db->setQuery ( $query );
		$trashitems = $kunena_db->loadObjectList ();
		//if (KunenaError::checkDatabaseError()) return;

		return $trashitems;
	}

	/**
	 * Method to get cids from session.
	 *
	 * @return	Array
	 * @since	1.6
	 */
	protected function _getCids() {
		$app = JFactory::getApplication ();
		$ids = $app->getUserState('com_kunena.purge');

		return $ids;
	}

	/**
	 * Method to get details on selected items.
	 *
	 * @return	Array
	 * @since	1.6
	 */
	public function getPurgeItems() {
		$kunena_db = &JFactory::getDBO ();
		$ids = $this->_getCids();

		$ids = implode ( ',', $ids );
		$kunena_db->setQuery ( "SELECT * FROM #__kunena_messages WHERE hold=2 AND id IN ($ids)");
		$items = $kunena_db->loadObjectList ();
		//if (KunenaError::checkDatabaseError()) return;

		return $items;
	}

	/**
	 * Method to hash datas.
	 *
	 * @return	hash
	 * @since	1.6
	 */
	public function getMd5() {
		$ids = $this->_getCids();

		return md5(serialize($ids));
	}

	public function getNavigation() {
		jimport ( 'joomla.html.pagination' );
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}
}
