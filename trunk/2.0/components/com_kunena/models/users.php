<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.model' );
jimport ( 'joomla.version' );

/**
 * Users Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelUsers extends KunenaModel {
	protected function populateState() {
		$app = JFactory::getApplication ();
		$active = $app->getMenu ()->getActive ();
		$active = $active ? (int) $active->id : 0;

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.users_{$active}_list_limit", 'limit', 0, 'int' );
		if ($value < 1) $value = 30;
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.users_{$active}_list_ordering", 'filter_order', 'id', 'cmd' );
		// FIXME:
		$value = 'id';
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.users_{$active}_list_start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.users_{$active}_list_direction", 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.users_{$active}_list_search", 'search', '' );
		if (!empty($value) && $value != JText::_('COM_KUNENA_USRL_SEARCH')) $this->setState ( 'list.search', $value );

		$jversion = new JVersion ();
		$this->setState ( 'list.exclude', $jversion->RELEASE == '1.5' ? '62' : '42');
	}

	public function getTotal() {
		static $total = false;
		if ($total === false) {
			$db = JFactory::getDBO();
			$db->setQuery ( "SELECT COUNT(*) FROM #__users WHERE block=0 AND activation=''" );
			$total = $db->loadResult ();
			KunenaError::checkDatabaseError();
		}
		return $total;
	}

	public function getCount() {
		static $total = false;
		if ($total === false) {
			$search = $this->getState ( 'list.search');
			$exclude = $this->getState ( 'list.exclude');
			$db = JFactory::getDBO();
			$query = "SELECT COUNT(*) FROM #__users AS u INNER JOIN #__kunena_users AS fu ON u.id=fu.userid WHERE (u.block=0 AND u.activation='')";
			if ($search) {
				$query .= " AND (u.name LIKE '%{$db->getEscaped($search)}%' OR u.username LIKE '%{$db->getEscaped($search)}%') AND u.id NOT IN ({$exclude})";
			}
			$db->setQuery ( $query );
			$total = $db->loadResult ();
			KunenaError::checkDatabaseError();
		}
		return $total;
	}

	public function getItems() {
		static $items = false;
		if ($items === false) {
			$search = $this->getState ( 'list.search');
			$exclude = $this->getState ( 'list.exclude');
			$db = JFactory::getDBO();
			$query = "SELECT *
				FROM #__users AS u
				INNER JOIN #__kunena_users AS ku ON ku.userid = u.id
				WHERE (u.block=0 OR u.activation!='') AND u.id NOT IN ({$exclude})";
			if ($search) {
				$query .= " AND (u.name LIKE '%{$db->getEscaped($search)}%' OR u.username LIKE '%{$db->getEscaped($search)}%')";
			}
			$query .= " ORDER BY {$db->nameQuote($this->getState ( 'list.ordering'))} {$this->getState ( 'list.direction')}";

			$db->setQuery ( $query, $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
			$items = $db->loadObjectList ('id');
			KunenaError::checkDatabaseError();

			// Prefetch all users/avatars to avoid user by user queries during template iterations
			KunenaUserHelper::loadUsers(array_keys($items));
		}
		return $items;
	}
}