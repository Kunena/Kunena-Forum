<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Categories Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerCategories extends KunenaController {
	protected $baseurl = null;
	protected $baseurl2 = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=categories';
		$this->baseurl2 = 'administrator/index.php?option=com_kunena&view=categories';
	}

	function lock() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'locked', 1);
		$this->redirectBack();
	}
	function unlock() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'locked', 0);
		$this->redirectBack();
	}
	function review() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'review', 1);
		$this->redirectBack();
	}
	function unreview() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'review', 0);
		$this->redirectBack();
	}
	function allow_anonymous() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_anonymous', 1);
		$this->redirectBack();
	}
	function deny_anonymous() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_anonymous', 0);
		$this->redirectBack();
	}
	function allow_polls() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_polls', 1);
		$this->redirectBack();
	}
	function deny_polls() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'allow_polls', 0);
		$this->redirectBack();
	}
	function publish() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'published', 1);
		$this->redirectBack();
	}
	function unpublish() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->setVariable($cid, 'published', 0);
		$this->redirectBack();
	}

	function add() {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack();
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$id = array_shift($cid);
		$this->setRedirect(KunenaRoute::_($this->baseurl2."&layout=create&catid={$id}", false));
	}

	function edit() {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack();
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$id = array_shift($cid);
		if (!$id) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_CATEGORIES_SELECTED' ), 'notice' );
			$this->redirectBack();
		} else {
			$this->setRedirect(KunenaRoute::_($this->baseurl2."&layout=edit&catid={$id}", false));
		}
	}

	function apply() {
		$category = $this->_save();
		if ($category->exists()) $this->setRedirect(KunenaRoute::_($this->baseurl2."&layout=edit&catid={$category->id}", false));
		else $this->setRedirect(KunenaRoute::_($this->baseurl2."&layout=create", false));
	}

	function save2new() {
		$this->_save();
		$this->setRedirect(KunenaRoute::_($this->baseurl2."&layout=create", false));
	}

	function save() {
		$this->_save();
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to save a category like a copy of existing one.
	 *
	 * @since	2.0.0-BETA2
	 */
	function save2copy() {
		$post = JRequest::get('post', JREQUEST_ALLOWRAW);

		list($title, $alias) = $this->_generateNewTitle($post['catid'], $post['alias'], $post['name']);
		$_POST['name']	= $title;
		$_POST['alias']	= $alias;
		$_POST['catid'] = 0;

		$this->_save();
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

    /**
     * @return KunenaForumCategory
     */
    protected function _save() {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack();
		}

		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		$accesstype = strtr(JRequest::getCmd('accesstype', 'joomla.level'), '.', '-');

		$post['access'] = JRequest::getInt("access-{$accesstype}", JRequest::getInt('access', null));
		$post['params'] = JRequest::getVar("params-{$accesstype}", array(), 'post', 'array');
		$success = false;

		$category = KunenaForumCategoryHelper::get ( intval ( $post ['catid'] ) );
		$parent = KunenaForumCategoryHelper::get (intval ( $post ['parent_id'] ) );

		if ($category->exists() && !$category->authorise ( 'admin' )) {
			// Category exists and user is not admin in category
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape ( $category->name ) ), 'notice' );

		} elseif (!$category->exists() && !$this->me->isAdmin ( $parent )) {
			// Category doesn't exist and user is not admin in parent, parent_id=0 needs global admin rights
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape ( $parent->name ) ), 'notice' );

		} elseif (! $category->isCheckedOut ( $this->me->userid )) {
			// Nobody can change id or statistics
			$ignore = array('option', 'view', 'task', 'catid', 'id', 'id_last_msg', 'numTopics', 'numPosts', 'time_last_msg', 'aliases', 'aliases_all');
			// User needs to be admin in parent (both new and old) in order to move category, parent_id=0 needs global admin rights
			if (!$this->me->isAdmin ( $parent ) || ($category->exists() && !$this->me->isAdmin ( $category->getParent() ))) {
				$ignore = array_merge($ignore, array('parent_id', 'ordering'));
				$post ['parent_id'] = $category->parent_id;
			}
			// Only global admin can change access control and class_sfx (others are inherited from parent)
			if (!$this->me->isAdmin ()) {
				$access = array('accesstype', 'access', 'pub_access', 'pub_recurse', 'admin_access', 'admin_recurse', 'channels', 'class_sfx', 'params');
				if (!$category->exists() || $parent->id != $category->parent_id) {
					// If category didn't exist or is moved, copy access and class_sfx from parent
					$category->bind($parent->getProperties(), $access, true);
				}
				$ignore = array_merge($ignore, $access);
			}

			$category->bind ( $post, $ignore );

			if (!$category->exists()) {
				$category->ordering = 99999;
			}
			$success = $category->save ();

			$aliases = explode(',', JRequest::getVar('aliases_all'));
			if ($aliases) {
				$aliases = array_diff($aliases, JRequest::getVar('aliases', array(), 'post', 'array'));
				foreach ($aliases as $alias) $category->deleteAlias($alias);
			}

			// Update read access
			$read = $this->app->getUserState("com_kunena.user{$this->me->userid}_read");
			$read[$category->id] = $category->id;
			$this->app->setUserState("com_kunena.user{$this->me->userid}_read", null);

			if (! $success) {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVE_FAILED', $category->id, $this->escape ( $category->getError () ) ), 'notice' );
			}
			$category->checkin();

		} else {
			// Category was checked out by someone else.
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape ( $category->name ) ), 'notice' );
		}

		if ($success) {
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVED', $this->escape ( $category->name ) ) );
		}

		if (!empty($post['rmmod'])) {
			foreach ((array) $post['rmmod'] as $userid=>$value) {
				$user = KunenaFactory::getUser($userid);
				if ($category->authorise('admin', null, false) && $category->removeModerator($user)) {
					$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_VIEW_CATEGORY_EDIT_MODERATOR_REMOVED', $this->escape ( $user->getName() ), $this->escape ( $category->name ) ) );
				}
			}
		}
		return $category;
	}

	function remove() {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack();
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		if (empty ( $cid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_CATEGORIES_SELECTED' ), 'notice' );
			$this->redirectBack();
		}

		$count = 0;
		$name = null;

		$categories = KunenaForumCategoryHelper::getCategories ( $cid );
		foreach ( $categories as $category ) {
			if (!$category->authorise ( 'admin' )) {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape ( $category->name ) ), 'notice' );
			} elseif (! $category->isCheckedOut ( $this->me->userid )) {
				if ($category->delete ()) {
					$count ++;
					$name = $category->name;
				} else {
					$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_DELETE_FAILED', $this->escape ( $category->getError () ) ), 'notice' );
				}
			} else {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape ( $category->name ) ), 'notice' );
			}
		}

		if ($count == 1 && $name)
			$this->app->enqueueMessage(JText::sprintf ('COM_KUNENA_A_CATEGORY_DELETED', $this->escape($name)));
		if ($count > 1)
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORIES_DELETED', $count ) );
		$this->redirectBack();
	}

	function cancel() {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack();
		}

		$id = JRequest::getInt('catid', 0);

		$category = KunenaForumCategoryHelper::get ( $id );
		if (!$category->authorise ( 'admin' )) {
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape ( $category->name ) ), 'notice' );
		} elseif (! $category->isCheckedOut ( $this->me->userid )) {
			$category->checkin ();
		} else {
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape ( $category->name ) ), 'notice' );
		}
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function saveorder() {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack();
		}

		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);
		$order = JRequest::getVar('order', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($order);

		if (empty ( $cid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_CATEGORIES_SELECTED' ), 'notice' );
			$this->redirectBack();
		}

		$success = false;

		$categories = KunenaForumCategoryHelper::getCategories ( $cid );
		foreach ( $categories as $category ) {
			if (! isset ( $order [$category->id] ) || $category->get ( 'ordering' ) == $order [$category->id])
				continue;
			if (!$category->getParent()->authorise ( 'admin' )) {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape ( $category->getParent()->name ) ), 'notice' );
			} elseif (! $category->isCheckedOut ( $this->me->userid )) {
				$category->set ( 'ordering', $order [$category->id] );
				$success = $category->save ();
				if (! $success) {
					$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVE_FAILED', $category->id, $this->escape ( $category->getError () ) ), 'notice' );
				}
			} else {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape ( $category->name ) ), 'notice' );
			}
		}

		if ($success) {
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_NEW_ORDERING_SAVED' ) );
		}
		$this->redirectBack();
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		if (!JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack();
		}

		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');

		// Get the model
		$model = $this->getModel('categories');
		// Save the ordering
		$return = $model->saveorder($pks, $order);
		if ($return) {
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

	function orderup() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->orderUpDown ( array_shift($cid), -1 );
		$this->redirectBack();
	}

	function orderdown() {
		$cid = JRequest::getVar('cid', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($cid);

		$this->orderUpDown ( array_shift($cid), 1 );
		$this->redirectBack();
	}

	protected function orderUpDown($id, $direction) {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (!$id) return;

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$category = KunenaForumCategoryHelper::get ( $id );
		if (!$category->getParent()->authorise ( 'admin' )) {
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape ( $category->getParent()->name ) ), 'notice' );
			return;
		}
		if ($category->isCheckedOut ( $this->me->userid )) {
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape ( $category->name ) ), 'notice' );
			return;
		}

		$db = JFactory::getDBO ();
		$row = new TableKunenaCategories ( $db );
		$row->load ( $id );

		// Ensure that we have the right ordering
		$where = 'parent_id=' . $db->quote ( $row->parent_id );
		$row->reorder ();
		$row->move ( $direction, $where );
	}

	protected function setVariable($cid, $variable, $value) {
		KunenaFactory::loadLanguage('com_kunena', 'admin');

		if (! JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}
		if (empty ( $cid )) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_CATEGORIES_SELECTED' ), 'notice' );
			return;
		}

		$count = 0;
		$name = null;

		$categories = KunenaForumCategoryHelper::getCategories ( $cid );
		foreach ( $categories as $category ) {
			if ($category->get ( $variable ) == $value)
				continue;
			if (!$category->authorise ( 'admin' )) {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_NO_ADMIN', $this->escape ( $category->name ) ), 'notice' );
			} elseif (! $category->isCheckedOut ( $this->me->userid )) {
				$category->set ( $variable, $value );
				if ($category->save ()) {
					$count ++;
					$name = $category->name;
				} else {
					$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVE_FAILED', $category->id, $this->escape ( $category->getError () ) ), 'notice' );
				}
			} else {
				$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_X_CHECKED_OUT', $this->escape ( $category->name ) ), 'notice' );
			}
		}

		if ($count == 1 && $name)
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVED', $this->escape ( $name ) ) );
		if ($count > 1)
			$this->app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORIES_SAVED', $count ) );
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $category_id  The id of the category.
	 * @param   string   $alias        The alias.
	 * @param   string   $name        The name.
	 *
	 * @return	array  Contains the modified title and alias.
	 *
	 * @since	2.0.0-BETA2
	 */
	protected function _generateNewTitle($category_id, $alias, $name) {
		while (  KunenaForumCategoryHelper::getAlias($category_id, $alias) ) {
			$name = JString::increment($name);
			$alias = JString::increment($alias, 'dash');
		}

		return array($name, $alias);
	}
}
