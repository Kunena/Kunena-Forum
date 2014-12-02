<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once KPATH_ADMIN . '/controllers/categories.php';

/**
 * Kunena Category Controller
 *
 * @since		2.0
 */
class KunenaControllerCategory extends KunenaAdminControllerCategories {
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'index.php?option=com_kunena&view=category&layout=manage';
		$this->baseurl2 = 'index.php?option=com_kunena&view=category';
	}

	function jump() {
		$catid = JRequest::getInt('catid', 0);
		if (!$catid) $this->setRedirect(KunenaRoute::_('index.php?option=com_kunena&view=category&layout=list', false));
		else $this->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=category&catid={$catid}", false));
	}

	function markread() {
		if (! JSession::checkToken ('request')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$catid = JRequest::getInt('catid', 0);
		if (!$catid) {
			// All categories
			$session = KunenaFactory::getSession();
			$session->markAllCategoriesRead ();
			if (!$session->save ()) {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error' );
			} else {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_GEN_ALL_MARKED') );
			}
		} else {
			// One category
			$category = KunenaForumCategoryHelper::get($catid);
			if (!$category->authorise('read')) {
				$this->app->enqueueMessage ( $category->getError(), 'error' );
				$this->redirectBack ();
			}

			$session = KunenaFactory::getSession();
			if ($session->userid) {
				// Mark all unread topics in the category to read
				$userinfo = $category->getUserInfo();
				// FIXME: Joomla 2.5 ->toSql()
				$userinfo->allreadtime = JFactory::getDate()->toSql();
				if (!$userinfo->save()) {
					$this->app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error' );
				} else {
					$this->app->enqueueMessage ( JText::_('COM_KUNENA_GEN_FORUM_MARKED') );
				}
			}
		}
		$this->redirectBack ();
	}

	function subscribe() {
		if (! JSession::checkToken ('get')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$category = KunenaForumCategoryHelper::get(JRequest::getInt('catid', 0));
		if (!$category->authorise('read')) {
			$this->app->enqueueMessage ( $category->getError(), 'error' );
			$this->redirectBack ();
		}

		if ($this->me->exists()) {
			$success = $category->subscribe(1);
			if ($success) {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_GEN_CATEGORY_SUBCRIBED') );
			}
		}

		$this->redirectBack ();
	}

	function unsubscribe() {
		if (! JSession::checkToken ('request') ) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$catid = JRequest::getInt('catid', 0);
		$catids = $catid
			? array($catid)
			: array_keys(JRequest::getVar('categories', array(), 'post', 'array')); // Array or integer keys
		JArrayHelper::toInteger($catids);

		$categories = KunenaForumCategoryHelper::getCategories($catids);
		foreach($categories as $category) {
			if (!$category->authorise('read')) {
				$this->app->enqueueMessage ( $category->getError(), 'error' );
				continue;
			}
			if ($this->me->exists()) {
				$success = $category->subscribe(0);
				if ($success) {
					$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_GEN_CATEGORY_NAME_UNSUBCRIBED', $category->name) );
				}
			}
		}

		$this->redirectBack ();
	}
}
