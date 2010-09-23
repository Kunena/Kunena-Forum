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

jimport ( 'joomla.application.component.controller' );

/**
 * Kunena Categories Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaControllerCategories extends KunenaController {
	public static $redirect = 'index.php?option=com_kunena&view=categories';

	function lock() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'locked', 1);
		$this->setRedirect(self::$redirect);
	}
	function unlock() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'locked', 0);
		$this->setRedirect(self::$redirect);
	}
	function moderate() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'moderated', 1);
		$this->setRedirect(self::$redirect);
	}
	function unmoderate() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'moderated', 0);
		$this->setRedirect(self::$redirect);
	}
	function review() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'review', 1);
		$this->setRedirect(self::$redirect);
	}
	function unreview() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'review', 0);
		$this->setRedirect(self::$redirect);
	}
	function allow_anonymous() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'allow_anonymous', 1);
		$this->setRedirect(self::$redirect);
	}
	function deny_anonymous() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'allow_anonymous', 0);
		$this->setRedirect(self::$redirect);
	}
	function allow_polls() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'allow_polls', 1);
		$this->setRedirect(self::$redirect);
	}
	function deny_polls() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'allow_polls', 0);
		$this->setRedirect(self::$redirect);
	}
	function publish() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'published', 1);
		$this->setRedirect(self::$redirect);
	}
	function unpublish() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->setVariable($cid, 'published', 0);
		$this->setRedirect(self::$redirect);
	}

	function add() {
		$this->setRedirect(self::$redirect.'&layout=new');
	}

	function edit() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$id = array_shift($cid);
		if (!$id) {
			$this->setRedirect(self::$redirect.'&layout=new');
		} else {
			$this->setRedirect(self::$redirect."&layout=edit&id={$id}");
		}
	}

	function save() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( self::$redirect );
		}

		$post = JRequest::get('post', JREQUEST_ALLOWRAW);
		$success = false;

		kimport ( 'category' );
		$my = JFactory::getUser ();
		$category = KunenaCategory::getInstance ( intval ( $post ['id'] ) );
		if (! $category->isCheckedOut ( $my->id )) {
			$category->bind ( $post );
			$success = $category->save ();
			if (! $success) {
				$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVE_FAILED', kescape ( $category->getError () ) ), 'notice' );
			}
			$category->checkin();
		} else {
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_CHECKED_OUT', kescape ( $category->name ) ), 'notice' );
		}

		if ($success) {
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVED', kescape ( $category->name ) ) );
		}
		$app->redirect ( self::$redirect );
	}

	function remove() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( self::$redirect );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );

		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_CATEGORIES_SELECTED' ), 'notice' );
			$app->redirect ( self::$redirect );
		}

		kimport ( 'category' );
		$count = 0;
		$my = JFactory::getUser ();
		$categories = KunenaCategory::getCategories ( $cid );
		foreach ( $categories as $category ) {
			if (! $category->isCheckedOut ( $my->id )) {
				if ($category->delete ()) {
					$count ++;
					$name = $category->name;
				} else {
					$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_DELETE_FAILED', kescape ( $category->getError () ) ), 'notice' );
				}
			} else {
				$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_CHECKED_OUT', kescape ( $category->name ) ), 'notice' );
			}
		}

		if ($count == 1)
			$app->redirect ( self::$redirect, JText::sprintf ( 'COM_KUNENA_A_CATEGORY_DELETED', kescape ( $name ) ) );
		if ($count > 1)
			$app->redirect ( self::$redirect, JText::sprintf ( 'COM_KUNENA_A_CATEGORIES_DELETED', $count ) );
	}

	function cancel() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( self::$redirect );
		}

		$id = JRequest::getInt('id', 0);

		kimport ( 'category' );
		$my = JFactory::getUser ();
		$category = KunenaCategory::getInstance ( $id );
		if (! $category->isCheckedOut ( $my->id )) {
			$category->checkin ();
		} else {
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_CHECKED_OUT', kescape ( $category->name ) ), 'notice' );
		}
		$app->redirect ( self::$redirect );
	}

	function saveorder() {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$app->redirect ( self::$redirect );
		}

		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$order = JRequest::getVar ( 'order', array (), 'post', 'array' );

		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_CATEGORIES_SELECTED' ), 'notice' );
			$app->redirect ( self::$redirect );
		}

		$success = false;

		kimport ( 'category' );
		$my = JFactory::getUser ();
		$categories = KunenaCategory::getCategories ( $cid );
		foreach ( $categories as $category ) {
			if (! isset ( $order [$category->id] ) || $category->get ( 'ordering' ) == $order [$category->id])
				continue;
			if (! $category->isCheckedOut ( $my->id )) {
				$category->set ( 'ordering', $order [$category->id] );
				$success = $category->save ();
				if (! $success) {
					$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVE_FAILED', kescape ( $category->getError () ) ), 'notice' );
				}
			} else {
				$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_CHECKED_OUT', kescape ( $category->name ) ), 'notice' );
			}
		}

		if ($success) {
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_NEW_ORDERING_SAVED' ) );
		}
		$app->redirect ( self::$redirect );
	}

	function orderup() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->orderUpDown ( array_shift($cid), -1 );
		$this->setRedirect(self::$redirect);
	}

	function orderdown() {
		$cid = JRequest::getVar ( 'cid', array (), 'post', 'array' );
		$this->orderUpDown ( array_shift($cid), 1 );
		$this->setRedirect(self::$redirect);
	}

	protected function orderUpDown($id, $direction) {
		if (!$id) return;

		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}

		$db = JFactory::getDBO ();
		kimport ( 'tables.kunenacategory' );
		$row = new TableKunenaCategory ( $db );
		$row->load ( $id );

		// Ensure that we have the right ordering
		$where = 'parent=' . $db->quote ( $row->parent );
		$row->reorder ( $where );
		$row->move ( $direction, $where );
	}

	protected function setVariable($cid, $variable, $value) {
		$app = JFactory::getApplication ();
		if (! JRequest::checkToken ()) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			return;
		}
		if (empty ( $cid )) {
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_A_NO_CATEGORIES_SELECTED' ), 'notice' );
			return;
		}

		$count = 0;

		kimport ( 'category' );
		$my = JFactory::getUser ();
		$categories = KunenaCategory::getCategories ( $cid );
		foreach ( $categories as $category ) {
			if ($category->get ( $variable ) == $value)
				continue;
			if (! $category->isCheckedOut ( $my->id )) {
				$category->set ( $variable, $value );
				if ($category->save ()) {
					$count ++;
					$name = $category->name;
				} else {
					$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVE_FAILED', kescape ( $category->getError () ) ), 'notice' );
				}
			} else {
				$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_CHECKED_OUT', kescape ( $category->name ) ), 'notice' );
			}
		}

		if ($count == 1)
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORY_SAVED', kescape ( $name ) ) );
		if ($count > 1)
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_A_CATEGORIES_SAVED', $count ) );
	}

}