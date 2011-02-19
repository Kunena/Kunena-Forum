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
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.users_{$active}_list_start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		// FIXME: doesn't seem to work yet
		$value = $this->getUserStateFromRequest ( "com_kunena.users_{$active}_list_direction", 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.users_{$active}_list_search", 'search', '' );
		if (!empty($value) && $value != JText::_('COM_KUNENA_USRL_SEARCH')) $this->setState ( 'list.search', $value );

		$jversion = new JVersion ();
		$this->setState ( 'list.exclude', $jversion->RELEASE == '1.5' ? '62' : '42');
	}

	public function getQueryWhere() {
		$config = KunenaFactory::getConfig();
		if ($config->userlist_count_users == '0' ) $where = '1';
		elseif ($config->userlist_count_users == '1' ) $where = 'block=0 OR activation=""';
		elseif ($config->userlist_count_users == '2' ) $where = 'block=0 AND activation=""';
		return $where;
	}

	public function getTotal() {
		static $total = false;
		if ($total === false) {
			$db = JFactory::getDBO();
			$where = $this->getQueryWhere();
			$db->setQuery ( "SELECT COUNT(*) FROM #__users WHERE {$where}" );
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
			$where = $this->getQueryWhere();
			$query = "SELECT COUNT(*) FROM #__users AS u INNER JOIN #__kunena_users AS fu ON u.id=fu.userid WHERE ({$where})";
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
			$where = $this->getQueryWhere();
			$query = "SELECT *
				FROM #__users AS u
				INNER JOIN #__kunena_users AS ku ON ku.userid = u.id
				WHERE ({$where}) AND u.id NOT IN ({$exclude})";
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