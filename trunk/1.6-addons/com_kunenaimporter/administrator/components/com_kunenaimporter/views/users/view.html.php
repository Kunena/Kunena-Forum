<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
defined('_JEXEC') or die();

jimport ( 'joomla.application.component.view' );

class KunenaImporterViewUsers extends JView {
	function display($tpl = null) {
		global $mainframe, $option;

		$db = JFactory::getDBO ();
		$currentUser = JFactory::getUser ();
		$acl = JFactory::getACL ();

		$filter_order = $mainframe->getUserStateFromRequest ( "$option.filter_order", 'filter_order', 'a.username', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest ( "$option.filter_order_Dir", 'filter_order_Dir', 'asc', 'word' );
		$filter_type = $mainframe->getUserStateFromRequest ( "$option.filter_type", 'filter_type', 'unmapped', 'string' );
		$search = $mainframe->getUserStateFromRequest ( "$option.search", 'search', '', 'string' );
		$search = JString::strtolower ( $search );

		$limit = $mainframe->getUserStateFromRequest ( 'global.list.limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest ( $option . '.limitstart', 'limitstart', 0, 'int' );

		$where = array ();
		if (isset ( $search ) && $search != '') {
			$searchEscaped = $db->Quote ( '%' . $db->getEscaped ( $search, true ) . '%', false );
			$where [] = 'a.username LIKE ' . $searchEscaped . ' OR a.email LIKE ' . $searchEscaped . ' OR a.name LIKE ' . $searchEscaped;
		}
		switch ($filter_type) {
			case 'unmapped':
				$where [] = " (a.id = 0 OR a.id IS NULL) ";
				break;
			case 'mapped':
				$where [] = " a.id > 0 ";
				break;
			default:
		}
		// exclude any child group id's for this user
		$pgids = $acl->get_group_children ( $currentUser->get ( 'gid' ), 'ARO', 'RECURSE' );

		if (is_array ( $pgids ) && count ( $pgids ) > 0) {
			JArrayHelper::toInteger ( $pgids );
			$where [] = 'a.gid NOT IN (' . implode ( ',', $pgids ) . ')';
		}
		$filter = '';

		$orderby = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;
		$where = (count ( $where ) ? ' WHERE (' . implode ( ') AND (', $where ) . ')' : '');

		$query = 'SELECT COUNT(*)' . ' FROM #__kunenaimporter_users AS a' . $filter . $where;
		$db->setQuery ( $query );
		$total = $db->loadResult ();

		jimport ( 'joomla.html.pagination' );
		$pagination = new JPagination ( $total, $limitstart, $limit );

		$query = 'SELECT a.*, g.name AS groupname' . ' FROM #__kunenaimporter_users AS a LEFT JOIN #__core_acl_aro_groups AS g ON g.id = a.gid' . $filter . $where . $orderby;
		$db->setQuery ( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList ();

		$n = count ( $rows );

		// get list of Groups for dropdown filter
		$types [] = JHTML::_ ( 'select.option', '', 'All users' );
		$types [] = JHTML::_ ( 'select.option', 'unmapped', 'Unmapped users' );
		$types [] = JHTML::_ ( 'select.option', 'mapped', 'Mapped users' );
		$lists ['type'] = JHTML::_ ( 'select.genericlist', $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', "$filter_type" );

		// get list of Log Status for dropdown filter
		$logged [] = JHTML::_ ( 'select.option', 0, '- ' . JText::_ ( 'Select Log Status' ) . ' -' );
		$logged [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'Logged In' ) );

		// table ordering
		$lists ['order_Dir'] = $filter_order_Dir;
		$lists ['order'] = $filter_order;

		// search filter
		$lists ['search'] = $search;

		$this->assignRef ( 'user', JFactory::getUser () );
		$this->assignRef ( 'lists', $lists );
		$this->assignRef ( 'items', $rows );
		$this->assignRef ( 'pagination', $pagination );

		JToolBarHelper::title ( JText::_ ( 'Forum Importer: Migrate Users' ), 'kunenaimporter.png' );
		JToolBarHelper::custom ( 'mapusers', 'upload', 'upload', JText::_ ( 'Import All' ), false );
		JToolBarHelper::custom ( 'truncatemap', 'delete', 'delete', JText::_ ( 'Delete All' ), false );
		/*		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();*/

		parent::display ( $tpl );
	}
}