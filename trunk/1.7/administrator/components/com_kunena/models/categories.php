<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );

/**
 * Categories Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelCategories extends JModel {
	protected $__state_set = false;
	protected $_items = false;
	protected $_object = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication ();

		// List state information
		$value = $app->getUserStateFromRequest ( "com_kunena.categories.list.limit", 'limit', $app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $app->getUserStateFromRequest ( 'com_kunena.categories.list.ordering', 'filter_order', 'ordering', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.categories.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $app->getUserStateFromRequest ( 'com_kunena.categories.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $app->getUserStateFromRequest ( 'com_kunena.categories.list.search', 'search', '', 'string' );
		$this->setState ( 'list.search', $value );

		$value = $app->getUserStateFromRequest ( "com_kunena.categories.list.levels", 'levellimit', 10, 'int' );
		$this->setState ( 'list.levels', $value );

		$value = JRequest::getInt ( 'catid', 0 );
		$this->setState ( 'item.id', $value );
	}

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
			$this->populateState ();
			$this->__state_set = true;
		}
		return parent::getState ( $property );
	}

	public function getItems() {
		if ( $this->_items === false ) {
			kimport ( 'error' );
			require_once KPATH_SITE . '/class.kunena.php';

			$db = JFactory::getDBO ();

			$direction = $this->getState ( 'list.direction' );
			switch ($this->getState ( 'list.ordering' )) {
				case 'id' :
					$order = ' ORDER BY a.id ' . $direction;
					break;
				case 'name' :
					$order = ' ORDER BY a.name ' . $direction;
					break;
				case 'ordering' :
				default :
					$order = ' ORDER BY a.ordering ' . $direction;
			}

			$where = '';
			$search = $this->getState ( 'list.search' );
			if ($search) {
				$where .= ' WHERE LOWER( a.name ) LIKE ' . $db->Quote ( '%' . $db->getEscaped ( $search, true ) . '%', false ) . ' OR LOWER( a.id ) LIKE ' . $db->Quote ( '%' . $db->getEscaped ( $search, true ) . '%', false );
			}

			jimport ( 'joomla.version' );
			$jversion = new JVersion ();
			if ($jversion->RELEASE == 1.5) {
				// Joomla 1.5
				$query = "SELECT a.*, a.parent>0 AS category, u.name AS editor, g.name AS groupname, g.id AS group_id, h.name AS admingroup
			FROM #__kunena_categories AS a
			LEFT JOIN #__users AS u ON u.id = a.checked_out
			LEFT JOIN #__core_acl_aro_groups AS g ON g.id = a.pub_access
			LEFT JOIN #__core_acl_aro_groups AS h ON h.id = a.admin_access
			" . $where . $order;
			} else {
				// Joomla 1.6
				$query = "SELECT a.*, a.parent>0 AS category, u.name AS editor, g.title AS groupname, h.title AS admingroup
			FROM #__kunena_categories AS a
			LEFT JOIN #__users AS u ON u.id = a.checked_out
			LEFT JOIN #__usergroups AS g ON g.id = a.pub_access
			LEFT JOIN #__usergroups AS h ON h.id = a.admin_access
			" . $where . $order;
			}
			$db->setQuery ( $query );
			$rows = $db->loadObjectList ( 'id' );
			KunenaError::checkDatabaseError ();

			// establish the hierarchy of the categories
			$children = array (0 => array () );

			// first pass - collect children
			foreach ( $rows as $v ) {
				$list = array ();
				$vv = $v;
				while ( $vv->parent > 0 && isset ( $rows [$vv->parent] ) && ! in_array ( $vv->parent, $list ) ) {
					$list [] = $vv->id;
					$vv = $rows [$vv->parent];
				}
				if ($vv->parent) {
					$v->parent = - 1;
					$v->published = 0;

					if (empty ( $search ))
						$v->name = JText::_ ( 'COM_KUNENA_CATEGORY_ORPHAN' ) . ' : ' . $v->name;
				}
				if ($v->pub_access == 0) {
					$v->groupname = JText::_ ( 'COM_KUNENA_EVERYBODY' );
				} else if ($v->pub_access == - 1) {
					$v->groupname = JText::_ ( 'COM_KUNENA_ALLREGISTERED' );
				} else if ($v->pub_access == 1) {
					$v->groupname = JText::_ ( 'COM_KUNENA_NOBODY' );
				} else {
					$v->groupname = JText::_ ( $v->groupname );
				}
				$v->admingroup = JText::_ ( $v->admingroup );
				if ($v->checked_out && ! JTable::isCheckedOut ( 0, intval ( $v->checked_out ) )) {
					$v->checked_out = 0;
					$v->editor = '';
				}
				$children [$v->parent] [] = $v;
				$v->location = count ( $children [$v->parent] ) - 1;
				$v->up = ($v->location > 0);
				$v->down = 0;
				if ($v->location) $children[$v->parent][$v->location-1]->down = 1;
			}

			if (isset ( $children [- 1] )) {
				$children [0] = array_merge ( $children [- 1], $children [0] );
				if (empty ( $search )) {
					$app = JFactory::getApplication ();
					$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CATEGORY_ORPHAN_DESC' ), 'notice' );
				}
			}

			// second pass - get an indent list of the items
			$levellimit = $this->getState ( 'list.levels' );
			$list = fbTreeRecurse ( 0, '', array (), $children, max ( 0, $levellimit - 1 ) );
			$this->setState ( 'list.total', count($list) );

			$levellist = JHTML::_ ( 'select.integerList', 1, 20, 1, 'levellimit', 'size="1" onchange="document.adminForm.submit();"', $levellimit );
			// slice out elements based on limits
			$this->_items = array_slice ( $list, $this->getState ( 'list.start' ), $this->getState ( 'list.limit' ) );
		}

		return $this->_items;
	}

	public function getNavigation() {
		jimport ( 'joomla.html.pagination' );
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}

	public function getItem() {
		if ($this->_object === false) {
			require_once KPATH_SITE . '/class.kunena.php';
			$app = JFactory::getApplication ();
			$my = JFactory::getUser ();
			kimport ( 'category' );
			$category = KunenaCategory::getInstance ( $this->getState ( 'item.id' ) );
			if ($category->isCheckedOut ( $my->id )) {
				$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_CHECKED_OUT', kescape ( $category->name ) ), 'notice' );
				$app->redirect ( JURI::base () . "index.php?option=com_kunena&view=categories" );
			}

			if ($category->exists ()) {
				$category->checkout ( $my->id );
			} else {
				// New category is by default child of the first section -- this will help new users to do it right
				$db = JFactory::getDBO ();
				$db->setQuery ( "SELECT a.id, a.name FROM #__kunena_categories AS a WHERE parent='0' AND id!='$category->id' ORDER BY ordering" );
				$sections = $db->loadObjectList ();
				KunenaError::checkDatabaseError ();
				$category->parent = empty ( $sections ) ? 0 : $sections [0]->id;
				$category->published = 0;
				$category->ordering = 9999;
				$category->pub_recurse = 1;
				$category->admin_recurse = 1;
				$category->pub_access = 0;
				$category->moderated = 1;
			}
			$this->_object = $category;
		}
		return $this->_object;
	}

	public function getOptions() {
		$category = $this->getItem();

		$catList = array ();
		$catList [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_TOPLEVEL' ) );

		// make a standard yes/no list
		$yesno = array ();
		$yesno [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_ANN_NO' ) );
		$yesno [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_ANN_YES' ) );

		//create custom group levels to include into the public group selectList
		$pub_groups = array ();
		$adm_groups = array ();
		$pub_groups [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_NOBODY' ) );
		$pub_groups [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_EVERYBODY' ) );
		$pub_groups [] = JHTML::_ ( 'select.option', - 1, JText::_ ( 'COM_KUNENA_ALLREGISTERED' ) );
		jimport ( 'joomla.version' );
		$jversion = new JVersion ();
		if ($jversion->RELEASE == 1.5) {
			$acl = JFactory::getACL ();
			// FIXME: not implemented in J1.6
			$pub_groups = array_merge ( $pub_groups, $acl->get_group_children_tree ( null, 'Registered', true ) );
			// create admin groups array for use in selectList:
			$adm_groups = array_merge ( $adm_groups, $acl->get_group_children_tree ( null, 'Public Backend', true ) );
		}

		// Anonymous posts default
		$post_anonymous = array ();
		$post_anonymous [] = JHTML::_ ( 'select.option', '0', JText::_ ( 'COM_KUNENA_CATEGORY_ANONYMOUS_X_REG' ) );
		$post_anonymous [] = JHTML::_ ( 'select.option', '1', JText::_ ( 'COM_KUNENA_CATEGORY_ANONYMOUS_X_ANO' ) );

		$lists = array ();
		$lists ['categories'] = CKunenaTools::KSelectList ( 'parent', $catList, 'class="inputbox"', true, 'parent', $category->parent );
		$lists ['pub_access'] = JHTML::_ ( 'select.genericlist', $pub_groups, 'pub_access', 'class="inputbox" size="4"', 'value', 'text', $category->pub_access );
		$lists ['admin_access'] = JHTML::_ ( 'select.genericlist', $adm_groups, 'admin_access', 'class="inputbox" size="4"', 'value', 'text', $category->admin_access );
		$lists ['pub_recurse'] = JHTML::_ ( 'select.genericlist', $yesno, 'pub_recurse', 'class="inputbox" size="1"', 'value', 'text', $category->pub_recurse );
		$lists ['admin_recurse'] = JHTML::_ ( 'select.genericlist', $yesno, 'admin_recurse', 'class="inputbox" size="1"', 'value', 'text', $category->admin_recurse );
		$lists ['forumLocked'] = JHTML::_ ( 'select.genericlist', $yesno, 'locked', 'class="inputbox" size="1"', 'value', 'text', $category->locked );
		$lists ['forumModerated'] = JHTML::_ ( 'select.genericlist', $yesno, 'moderated', 'class="inputbox" size="1"', 'value', 'text', $category->moderated );
		$lists ['forumReview'] = JHTML::_ ( 'select.genericlist', $yesno, 'review', 'class="inputbox" size="1"', 'value', 'text', $category->review );
		$lists ['allow_polls'] = JHTML::_ ( 'select.genericlist', $yesno, 'allow_polls', 'class="inputbox" size="1"', 'value', 'text', $category->allow_polls );
		$lists ['allow_anonymous'] = JHTML::_ ( 'select.genericlist', $yesno, 'allow_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->allow_anonymous );
		$lists ['post_anonymous'] = JHTML::_ ( 'select.genericlist', $post_anonymous, 'post_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->post_anonymous );
		return $lists;
	}

	function getModerators() {
		kimport('user');
		$category = $this->getItem();
		$moderators = KunenaUser::loadUsers($category->getModerators(false));
		return $moderators;
	}
}