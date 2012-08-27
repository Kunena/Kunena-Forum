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
 * Categories Model for Kunena
 *
 * @since 2.0
 */
class KunenaAdminModelCategories extends KunenaModel {
	protected $__state_set = false;
	protected $_admincategories = false;
	protected $_admincategory = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.categories.list.limit", 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.categories.list.ordering', 'filter_order', 'ordering', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.categories.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.categories.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.categories.list.search', 'search', '', 'string' );
		$this->setState ( 'list.search', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.categories.list.levels", 'levellimit', 10, 'int' );
		$this->setState ( 'list.levels', $value );

		$catid = $this->getInt ( 'catid', 0 );
		$layout = $this->getWord ( 'layout', 'edit' );
		$parent_id = 0;
		if ($layout == 'create') {
			$parent_id = $catid;
			$catid = 0;
		}
		$this->setState ( 'item.id', $catid );
		$this->setState ( 'item.parent_id', $parent_id );
	}

	public function getAdminCategories() {
		if ( $this->_admincategories === false ) {
			$params = array (
				'ordering'=>$this->getState ( 'list.ordering' ),
				'direction'=>$this->getState ( 'list.direction' ) == 'asc' ? 1 : -1,
				'search'=>$this->getState ( 'list.search' ),
				'unpublished'=>1,
				'action'=>'admin');
			$catid = $this->getState ( 'item.id', 0 );
			$categories = array();
			$orphans = array();
			if ($catid) {
				$categories = KunenaForumCategoryHelper::getParents($catid, $this->getState ( 'list.levels' ), array('unpublished'=>1, 'action'=>'none'));
				$categories[] = KunenaForumCategoryHelper::get($catid);
			} else {
				$orphans = KunenaForumCategoryHelper::getOrphaned($this->getState ( 'list.levels' ), $params);
			}
			$categories = array_merge($categories, KunenaForumCategoryHelper::getChildren($catid, $this->getState ( 'list.levels' ), $params));
			$categories = array_merge($orphans, $categories);

			$categories = KunenaForumCategoryHelper::getIndentation($categories);
			$this->setState ( 'list.total', count($categories) );
			if ($this->getState ( 'list.limit' )) $this->_admincategories = array_slice ( $categories, $this->getState ( 'list.start' ), $this->getState ( 'list.limit' ) );
			else $this->_admincategories = $categories;
			$admin = 0;
			$acl = KunenaAccess::getInstance();
			foreach ($this->_admincategories as $category) {
				$parent = $category->getParent();
				$siblings = array_keys(KunenaForumCategoryHelper::getCategoryTree($category->parent_id));
				$category->up = $this->me->isAdmin($parent) && reset($siblings) != $category->id;
				$category->down = $this->me->isAdmin($parent) && end($siblings) != $category->id;
				$category->reorder = $this->me->isAdmin($parent);
				// FIXME: stop creating access names manually
				if ($category->accesstype == 'joomla.level') {
					$groupname = $acl->getGroupName($category->accesstype, $category->access);
					if (version_compare(JVERSION, '1.6','>')) {
						// Joomla 1.6+
						$category->accessname = JText::_('COM_KUNENA_INTEGRATION_JOOMLA_LEVEL').': '.($groupname ? $groupname : JText::_('COM_KUNENA_NOBODY'));
					} else {
						// Joomla 1.5
						$category->accessname = JText::_('COM_KUNENA_INTEGRATION_JOOMLA_LEVEL').': '.($groupname ? JText::_($groupname) : JText::_('COM_KUNENA_NOBODY'));
					}
				} elseif ($category->accesstype != 'joomla.group') {
					$category->accessname = JText::_('COM_KUNENA_INTEGRATION_TYPE_'.strtoupper(preg_replace('/[^\w\d]+/', '_', $category->accesstype))).': '.$acl->getGroupName($category->accesstype, $category->access);
				} elseif (version_compare(JVERSION, '1.6','>')) {
					// Joomla 1.6+
					$groupname = $acl->getGroupName($category->accesstype, $category->pub_access);
					$category->accessname = JText::sprintf( $category->pub_recurse ? 'COM_KUNENA_A_GROUP_X_PLUS' : 'COM_KUNENA_A_GROUP_X_ONLY', $groupname ? JText::_( $groupname ) : JText::_('COM_KUNENA_NOBODY') );
					$groupname = $acl->getGroupName($category->accesstype, $category->admin_access);
					if ($groupname && $category->pub_access != $category->admin_access) {
						$category->accessname .= ' / '.JText::sprintf( $category->admin_recurse ? 'COM_KUNENA_A_GROUP_X_PLUS' : 'COM_KUNENA_A_GROUP_X_ONLY', JText::_( $groupname ));
					}
				} else {
					// Joomla 1.5
					$groupname = $acl->getGroupName($category->accesstype, $category->pub_access);
					if ($category->pub_access == 0) {
						$category->accessname = JText::_('COM_KUNENA_PUBLIC');
					} else if ($category->pub_access == - 1) {
						$category->accessname = JText::_('COM_KUNENA_ALLREGISTERED');
					} else if ($category->pub_access == 1 || !$groupname) {
						$category->accessname = JText::_('COM_KUNENA_NOBODY');
					} else {
						$category->accessname = JText::sprintf( $category->pub_recurse ? 'COM_KUNENA_A_GROUP_X_PLUS' : 'COM_KUNENA_A_GROUP_X_ONLY', JText::_( $groupname ));
					}
					$groupname = $acl->getGroupName($category->accesstype, $category->admin_access);
					if ($category->pub_access > 0 && $groupname && $category->pub_access != $category->admin_access) {
						$category->accessname .= ' / '.JText::sprintf( $category->admin_recurse ? 'COM_KUNENA_A_GROUP_X_PLUS' : 'COM_KUNENA_A_GROUP_X_ONLY', JText::_( $groupname ));
					}
				}
				if ($category->accesstype != 'joomla.group') {
					$category->admin_group = '';
				} else {
					$category->admin_group = JText::_ ( $acl->getGroupName($category->accesstype, $category->admin_access ));
				}
				if ($this->me->isAdmin($category) && $category->isCheckedOut(0)) {
					$category->editor = KunenaFactory::getUser($category->checked_out)->getName();
				} else {
					$category->checked_out = 0;
					$category->editor = '';
				}
				$admin += $this->me->isAdmin($category);
			}
			$this->setState ( 'list.count.admin', $admin );
		}
		if (!empty($orphans)) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CATEGORY_ORPHAN_DESC' ), 'notice' );
		}
		return $this->_admincategories;
	}

	public function getAdminNavigation() {
		$navigation = new JPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}

	public function getAdminCategory() {
		$category = KunenaForumCategoryHelper::get ( $this->getState ( 'item.id' ) );
		if (!$this->me->isAdmin($category)) {
			return false;
		}
		if ($this->_admincategory === false) {
			if ($category->exists ()) {
				if (!$category->isCheckedOut ( $this->me->userid ))
					$category->checkout ( $this->me->userid );
			} else {
				// New category is by default child of the first section -- this will help new users to do it right
				$db = JFactory::getDBO ();
				$db->setQuery ( "SELECT a.id, a.name FROM #__kunena_categories AS a WHERE parent_id='0' AND id!='$category->id' ORDER BY ordering" );
				$sections = $db->loadObjectList ();
				KunenaError::checkDatabaseError ();
				$category->parent_id = $this->getState ( 'item.parent_id' );
				$category->published = 0;
				$category->ordering = 9999;
				$category->pub_recurse = 1;
				$category->admin_recurse = 1;
				if (version_compare(JVERSION, '1.6','>')) {
					// Joomla 1.6+
					$category->accesstype = 'joomla.level';
					$category->access = 1;
					$category->pub_access = 1;
					$category->admin_access = 8;
				} else {
					// Joomla 1.5
					$category->accesstype = 'joomla.group';
					$category->access = 0;
					$category->pub_access = 0;
					$category->admin_access = 0;
				}
			}
			$this->_admincategory = $category;
		}
		return $this->_admincategory;
	}

	public function getAdminOptions() {
		$category = $this->getAdminCategory();
		if (!$category) return false;

		$catList = array ();
		$catList [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_TOPLEVEL' ) );

		// make a standard yes/no list
		$published = array ();
		$published [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_PUBLISHED' ) );
		$published [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_UNPUBLISHED' ) );

		// make a standard yes/no list
		$yesno = array ();
		$yesno [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_NO' ) );
		$yesno [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_YES' ) );

		// Anonymous posts default
		$post_anonymous = array ();
		$post_anonymous [] = JHTML::_ ( 'select.option', '0', JText::_ ( 'COM_KUNENA_CATEGORY_ANONYMOUS_X_REG' ) );
		$post_anonymous [] = JHTML::_ ( 'select.option', '1', JText::_ ( 'COM_KUNENA_CATEGORY_ANONYMOUS_X_ANO' ) );

		$cat_params = array ();
		$cat_params['ordering'] = 'ordering';
		$cat_params['toplevel'] = JText::_('COM_KUNENA_TOPLEVEL');
		$cat_params['sections'] = 1;
		$cat_params['unpublished'] = 1;
		$cat_params['catid'] = $category->id;
		$cat_params['action'] = 'admin';

		$channels_params = array();
		$channels_params['catid'] = $category->id;
		$channels_params['action'] = 'admin';
		$channels_options = array();
		$channels_options [] = JHTML::_ ( 'select.option', 'THIS', JText::_ ( 'COM_KUNENA_CATEGORY_CHANNELS_OPTION_THIS' ) );
		$channels_options [] = JHTML::_ ( 'select.option', 'CHILDREN', JText::_ ( 'COM_KUNENA_CATEGORY_CHANNELS_OPTION_CHILDREN' ) );
		if (empty($category->channels)) $category->channels = 'THIS';

		$topic_ordering_options = array();
		$topic_ordering_options[] = JHTML::_ ( 'select.option', 'lastpost', JText::_ ( 'COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_LASTPOST' ) );
		$topic_ordering_options[] = JHTML::_ ( 'select.option', 'creation', JText::_ ( 'COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_CREATION' ) );
		$topic_ordering_options[] = JHTML::_ ( 'select.option', 'alpha', JText::_ ( 'COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_ALPHA' ) );

		$aliases = array_keys($category->getAliases());

		$lists = array ();
		$lists ['accesstypes'] = KunenaAccess::getInstance()->getAccessTypesList($category);
		$lists ['accesslists'] = KunenaAccess::getInstance()->getAccessOptions($category);
		$lists ['categories'] = JHTML::_('kunenaforum.categorylist', 'parent_id', 0, null, $cat_params, 'class="inputbox"', 'value', 'text', $category->parent_id);
		$lists ['channels'] = JHTML::_('kunenaforum.categorylist', 'channels[]', 0, $channels_options, $channels_params, 'class="inputbox" multiple="multiple"', 'value', 'text', explode(',', $category->channels));
		$lists ['aliases'] = $aliases ? JHTML::_ ( 'kunenaforum.checklist', 'aliases', $aliases, true) : null;
		$lists ['published'] = JHTML::_ ( 'select.genericlist', $published, 'published', 'class="inputbox"', 'value', 'text', $category->published );
		$lists ['forumLocked'] = JHTML::_ ( 'select.genericlist', $yesno, 'locked', 'class="inputbox" size="1"', 'value', 'text', $category->locked );
		$lists ['forumReview'] = JHTML::_ ( 'select.genericlist', $yesno, 'review', 'class="inputbox" size="1"', 'value', 'text', $category->review );
		$lists ['allow_polls'] = JHTML::_ ( 'select.genericlist', $yesno, 'allow_polls', 'class="inputbox" size="1"', 'value', 'text', $category->allow_polls );
		$lists ['allow_anonymous'] = JHTML::_ ( 'select.genericlist', $yesno, 'allow_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->allow_anonymous );
		$lists ['post_anonymous'] = JHTML::_ ( 'select.genericlist', $post_anonymous, 'post_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->post_anonymous );
		$lists ['topic_ordering'] = JHTML::_ ( 'select.genericlist', $topic_ordering_options, 'topic_ordering', 'class="inputbox" size="1"', 'value', 'text', $category->topic_ordering );

		// TODO:
		/*
		$topicicons = array ();
		jimport( 'joomla.filesystem.folder' );
		$topiciconslist = JFolder::folders(JPATH_ROOT.'/media/kunena/topicicons');
		foreach( $topiciconslist as $icon ) {
			$topicicons[] = JHTML::_ ( 'select.option', $icon, $icon );
		}
		$lists ['category_iconset'] = JHTML::_ ( 'select.genericlist', $topicicons, 'iconset', 'class="inputbox" size="1"', 'value', 'text', $category->iconset );
		*/

		return $lists;
	}

	function getAdminModerators() {
		$category = $this->getAdminCategory();
		if (!$category) return false;

		$moderators = $category->getModerators(false);
		return $moderators;
	}
}