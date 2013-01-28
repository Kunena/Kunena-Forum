<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Models
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
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
	 */
	protected function populateState() {
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');
		$this->context = 'com_kunena.admin.categories';
		if ($layout) {
			$this->context .= '.'.$layout;
		}

		// List state information.
		$value = $this->getUserStateFromRequest ( $this->context.'.list.start', 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.list.limit', 'limit', $this->app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.list.ordering', 'filter_order', 'ordering', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.search', 'filter_search', '', 'string' );
		$this->setState ( 'filter.search', $value );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.published', 'filter_published', '', 'string' );
		$this->setState ( 'filter.published', $value !== '' ? (int) $value : null );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.title', 'filter_title', '', 'string' );
		$this->setState ( 'filter.title', $value !== '' ? $value : null );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.type', 'filter_type', '', 'string' );
		$this->setState ( 'filter.type', $value !== '' ? $value : null );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.access', 'filter_access', '', 'string' );
		$this->setState ( 'filter.access', $value !== '' ? (int) $value : null );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.locked', 'filter_locked', '', 'string' );
		$this->setState ( 'filter.locked', $value !== '' ? (int) $value : null );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.review', 'filter_review', '', 'string' );
		$this->setState ( 'filter.review', $value !== '' ? (int) $value : null );

		$value = $this->getUserStateFromRequest ( $this->context.'.filter.anonymous', 'filter_anonymous', '', 'string' );
		$this->setState ( 'filter.anonymous', $value !== '' ? (int) $value : null );

		// TODO: implement
		$value = $this->getUserStateFromRequest ( $this->context.".filter.levels", 'levellimit', 10, 'int' );
		$this->setState ( 'filter.levels', $value );

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
				'search'=>$this->getState ( 'filter.search' ),
				'unpublished'=>1,
				'published'=>$this->getState ( 'filter.published'),
				'filter_title'=>$this->getState ( 'filter.title'),
				'filter_type'=>$this->getState ( 'filter.type'),
				'filter_access'=>$this->getState ( 'filter.access'),
				'filter_locked'=>$this->getState ( 'filter.locked'),
				'filter_review'=>$this->getState ( 'filter.review'),
				'filter_anonymous'=>$this->getState ( 'filter.anonymous'),
				'action'=>'admin');

			$catid = $this->getState ( 'item.id', 0 );
			$categories = array();
			$orphans = array();

			if ($catid) {
				$categories = KunenaForumCategoryHelper::getParents($catid, $this->getState ( 'filter.levels' ), array('unpublished'=>1, 'action'=>'none'));
				$categories[] = KunenaForumCategoryHelper::get($catid);
			} else {
				$orphans = KunenaForumCategoryHelper::getOrphaned($this->getState ( 'filter.levels' ), $params);
			}

			$categories = array_merge($categories, KunenaForumCategoryHelper::getChildren($catid, $this->getState ( 'filter.levels' ), $params));
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
					$category->accessname = JText::_('COM_KUNENA_INTEGRATION_JOOMLA_LEVEL').': '.($groupname ? $groupname : JText::_('COM_KUNENA_NOBODY'));
				} elseif ($category->accesstype != 'joomla.group') {
					$category->accessname = JText::_('COM_KUNENA_INTEGRATION_TYPE_'.strtoupper(preg_replace('/[^\w\d]+/', '_', $category->accesstype))).': '.$acl->getGroupName($category->accesstype, $category->access);
				} else {
					$groupname = $acl->getGroupName($category->accesstype, $category->pub_access);
					$category->accessname = JText::sprintf( $category->pub_recurse ? 'COM_KUNENA_A_GROUP_X_PLUS' : 'COM_KUNENA_A_GROUP_X_ONLY', $groupname ? JText::_( $groupname ) : JText::_('COM_KUNENA_NOBODY') );
					$groupname = $acl->getGroupName($category->accesstype, $category->admin_access);
					if ($groupname && $category->pub_access != $category->admin_access) {
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
				$category->accesstype = 'joomla.level';
				$category->access = 1;
				$category->pub_access = 1;
				$category->admin_access = 8;

			}
			$this->_admincategory = $category;
		}
		return $this->_admincategory;
	}

	public function getAdminOptions() {
		$category = $this->getAdminCategory();
		if (!$category) return false;

		$catList = array ();
		$catList [] = JHtml::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_TOPLEVEL' ) );

		// make a standard yes/no list
		$published = array ();
		$published [] = JHtml::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_PUBLISHED' ) );
		$published [] = JHtml::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_UNPUBLISHED' ) );

		// make a standard yes/no list
		$yesno = array ();
		$yesno [] = JHtml::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_NO' ) );
		$yesno [] = JHtml::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_YES' ) );

		// Anonymous posts default
		$post_anonymous = array ();
		$post_anonymous [] = JHtml::_ ( 'select.option', '0', JText::_ ( 'COM_KUNENA_CATEGORY_ANONYMOUS_X_REG' ) );
		$post_anonymous [] = JHtml::_ ( 'select.option', '1', JText::_ ( 'COM_KUNENA_CATEGORY_ANONYMOUS_X_ANO' ) );

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
		$channels_options [] = JHtml::_ ( 'select.option', 'THIS', JText::_ ( 'COM_KUNENA_CATEGORY_CHANNELS_OPTION_THIS' ) );
		$channels_options [] = JHtml::_ ( 'select.option', 'CHILDREN', JText::_ ( 'COM_KUNENA_CATEGORY_CHANNELS_OPTION_CHILDREN' ) );
		if (empty($category->channels)) $category->channels = 'THIS';

		$topic_ordering_options = array();
		$topic_ordering_options[] = JHtml::_ ( 'select.option', 'lastpost', JText::_ ( 'COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_LASTPOST' ) );
		$topic_ordering_options[] = JHtml::_ ( 'select.option', 'creation', JText::_ ( 'COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_CREATION' ) );
		$topic_ordering_options[] = JHtml::_ ( 'select.option', 'alpha', JText::_ ( 'COM_KUNENA_CATEGORY_TOPIC_ORDERING_OPTION_ALPHA' ) );

		$aliases = array_keys($category->getAliases());

		$lists = array ();
		$lists ['accesstypes'] = KunenaAccess::getInstance()->getAccessTypesList($category);
		$lists ['accesslists'] = KunenaAccess::getInstance()->getAccessOptions($category);
		$lists ['categories'] = JHtml::_('kunenaforum.categorylist', 'parent_id', 0, null, $cat_params, 'class="inputbox"', 'value', 'text', $category->parent_id);
		$lists ['channels'] = JHtml::_('kunenaforum.categorylist', 'channels[]', 0, $channels_options, $channels_params, 'class="inputbox" multiple="multiple"', 'value', 'text', explode(',', $category->channels));
		$lists ['aliases'] = $aliases ? JHtml::_ ( 'kunenaforum.checklist', 'aliases', $aliases, true) : null;
		$lists ['published'] = JHtml::_ ( 'select.genericlist', $published, 'published', 'class="inputbox"', 'value', 'text', $category->published );
		$lists ['forumLocked'] = JHtml::_ ( 'select.genericlist', $yesno, 'locked', 'class="inputbox" size="1"', 'value', 'text', $category->locked );
		$lists ['forumReview'] = JHtml::_ ( 'select.genericlist', $yesno, 'review', 'class="inputbox" size="1"', 'value', 'text', $category->review );
		$lists ['allow_polls'] = JHtml::_ ( 'select.genericlist', $yesno, 'allow_polls', 'class="inputbox" size="1"', 'value', 'text', $category->allow_polls );
		$lists ['allow_anonymous'] = JHtml::_ ( 'select.genericlist', $yesno, 'allow_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->allow_anonymous );
		$lists ['post_anonymous'] = JHtml::_ ( 'select.genericlist', $post_anonymous, 'post_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->post_anonymous );
		$lists ['topic_ordering'] = JHtml::_ ( 'select.genericlist', $topic_ordering_options, 'topic_ordering', 'class="inputbox" size="1"', 'value', 'text', $category->topic_ordering );

		// TODO:
		/*
		$topicicons = array ();
		jimport( 'joomla.filesystem.folder' );
		$topiciconslist = JFolder::folders(JPATH_ROOT.'/media/kunena/topicicons');
		foreach( $topiciconslist as $icon ) {
			$topicicons[] = JHtml::_ ( 'select.option', $icon, $icon );
		}
		$lists ['category_iconset'] = JHtml::_ ( 'select.genericlist', $topicicons, 'iconset', 'class="inputbox" size="1"', 'value', 'text', $category->iconset );
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