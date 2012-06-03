<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
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

	function markread() {
		if (! JRequest::checkToken ('request')) {
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

			$db = JFactory::getDBO();
			$session = KunenaFactory::getSession();
			if ($session->userid) {
				// Mark all unread topics in the category to read
				$readTopics = $session->readtopics;
				$db->setQuery ( "SELECT id FROM #__kunena_topics WHERE category_id={$db->quote($category->id)} AND id NOT IN ({$readTopics}) AND last_post_time>={$db->quote($session->lasttime)}" );
				$readForum = $db->loadResultArray ();
				if (KunenaError::checkDatabaseError()) $this->redirectBack ();
				$readTopics = implode(',', array_merge(explode(',', $readTopics), $readForum));

				$session->readtopics = $readTopics;
				if (!$session->save ()) {
					$this->app->enqueueMessage ( JText::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error' );
				} else {
					$this->app->enqueueMessage ( JText::_('COM_KUNENA_GEN_FORUM_MARKED') );
				}
			}
		}
		$this->redirectBack ();
	}

	function subscribe() {
		if (! JRequest::checkToken ('get')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$category = KunenaForumCategoryHelper::get(JRequest::getInt('catid', 0));
		if (!$category->authorise('read')) {
			$this->app->enqueueMessage ( $category->getError(), 'error' );
			$this->redirectBack ();
		}

		$db = JFactory::getDBO();
		if ($this->me->exists()) {
			$success = $category->subscribe(1);
			if ($success) {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_GEN_CATEGORY_SUBCRIBED') );
			}
		}

		$this->redirectBack ();
	}

	function unsubscribe() {
		if (! JRequest::checkToken ('get') && !JRequest::checkToken() ) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->redirectBack ();
		}

		$db = JFactory::getDBO();
		$cat_seltected = array_keys(JRequest::getVar('categories', array (), 'post', 'array'));

		if ( !empty($cat_seltected) ) {
			$category = KunenaForumCategoryHelper::getCategories($cat_seltected);
			foreach($category as $cat) {
				if ($cat->authorise('read')) {
					if ($this->me->exists()) {
						$success = $cat->subscribe(0);
						if ($success) {
							$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_GEN_CATEGORY_NAME_UNSUBCRIBED', $cat->name) );
						}
					}
				}
			}
		} else {
			$category = KunenaForumCategoryHelper::get(JRequest::getInt('catid', 0));
			if (!$category->authorise('read')) {
				$this->app->enqueueMessage ( $category->getError(), 'error' );
				$this->redirectBack ();
			}


			if ($this->me->exists()) {
				$success = $category->subscribe(0);
				if ($success) {
					$this->app->enqueueMessage ( JText::_('COM_KUNENA_GEN_CATEGORY_UNSUBCRIBED') );
				}
			}
		}

		$this->redirectBack ();
	}
}