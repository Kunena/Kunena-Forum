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
kimport ( 'kunena.html.pagination' );

/**
 * User Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModelUser extends KunenaModel {
	protected function populateState() {
		$app = JFactory::getApplication ();
		$active = $app->getMenu ()->getActive ();
		$active = $active ? (int) $active->id : 0;

		$layout = $this->getCmd ( 'layout', 'default' );
		$this->setState ( 'layout', $layout );

		// TODO: create state for item
		if ($layout != 'list') {
			return;
		}

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
		if ($config->userlist_count_users == '1' ) $where = '(block=0 OR activation="")';
		elseif ($config->userlist_count_users == '2' ) $where = '(block=0 AND activation="")';
		elseif ($config->userlist_count_users == '3' ) $where = 'block=0';
		else $where = '1';
		return $where;
	}

	public function getQuerySearch() {
		// TODO: add strict search from the beginning of the name
		$config = KunenaFactory::getConfig();
		$search = $this->getState ( 'list.search');
		$exclude = $this->getState ( 'list.exclude');
		$where = array();
		if ($search) {
			$db = JFactory::getDBO();
			if ($config->userlist_name) $where[] = "u.name LIKE '%{$db->getEscaped($search)}%'";
			if ($config->userlist_username || !$where) $where[] = "u.username LIKE '%{$db->getEscaped($search)}%'";
			$where = 'AND ('.implode(' OR ', $where).')';
		} else {
			$where = '';
		}

		return "{$where} AND u.id NOT IN ({$exclude})";
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
			$db = JFactory::getDBO();
			$where = $this->getQueryWhere();
			$search = $this->getQuerySearch();
			$query = "SELECT COUNT(*) FROM #__users AS u INNER JOIN #__kunena_users AS fu ON u.id=fu.userid WHERE {$where} {$search}";
			$db->setQuery ( $query );
			$total = $db->loadResult ();
			KunenaError::checkDatabaseError();
		}
		return $total;
	}

	public function getItems() {
		static $items = false;
		if ($items === false) {
			$db = JFactory::getDBO();
			$where = $this->getQueryWhere();
			$search = $this->getQuerySearch();
			$moderator = intval(KunenaFactory::getUser ()->isModerator());
			$query = "SELECT *, IF(ku.hideEmail=0 OR {$moderator},u.email,'') AS email
				FROM #__users AS u
				INNER JOIN #__kunena_users AS ku ON ku.userid = u.id
				WHERE {$where} {$search}";
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