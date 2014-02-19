<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
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
			$this->setRedirectBack();
			return;
		}

		$catid = JRequest::getInt('catid', 0);
		$children = JRequest::getBool('children', 0);
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
				$this->setRedirectBack();
				return;
			}

			$session = KunenaFactory::getSession();
			if ($session->userid) {
				$categories = array($category->id => $category);
				if ($children) {
					// Include all child categories.
					$categories += $category->getChildren(-1);
				}

				// Mark all unread topics in selected categories as read.
				KunenaForumCategoryUserHelper::markRead(array_keys($categories));
				if (count($categories) > 1) {
					$this->app->enqueueMessage(JText::_('COM_KUNENA_GEN_ALL_MARKED'));
				} else {
					$this->app->enqueueMessage(JText::_('COM_KUNENA_GEN_FORUM_MARKED'));
				}
			}
		}

		$this->setRedirectBack();
	}

	function subscribe() {
		if (! JSession::checkToken ('get')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirectBack();
			return;
		}

		$category = KunenaForumCategoryHelper::get(JRequest::getInt('catid', 0));
		if (!$category->authorise('read')) {
			$this->app->enqueueMessage ( $category->getError(), 'error' );
			$this->setRedirectBack();
			return;
		}

		if ($this->me->exists()) {
			$success = $category->subscribe(1);
			if ($success) {
				$this->app->enqueueMessage ( JText::_('COM_KUNENA_GEN_CATEGORY_SUBCRIBED') );
			}
		}

		$this->setRedirectBack();
	}

	function unsubscribe() {
		if (! JSession::checkToken ('request') ) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirectBack();
			return;
		}

		$catid = JRequest::getInt('catid', 0);
		$catids = $catid ? array($catid) : array_keys(JRequest::getVar('categories', array(), 'post', 'array'));

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

		$this->setRedirectBack();
	}

	/**
	 * Method to approve topics in selected categories on the index page
	 *
	 * @since 3.1
	 *
	 * @return void
	 */
	public function approveTopicsInCategories()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$categories = $this->app->input->get('categories', array ( 0 ), 'array');

		$success = 0;

		$cats = array_keys($categories);

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')->from('#__kunena_messages')->where('catid IN (' . implode(',', $cats) . ')')->where('hold!=0');
		$db->setQuery($query);
		$topics_id = $db->loadObjectList();

		if (KunenaError::checkDatabaseError() )
		{
			return array(0, array());
		}

		$topic_id_list = array();
		foreach($topics_id as $id) {
			$topic_id_list[] = $id->id;
		}

		$messages_objects = KunenaForumMessageHelper::loadMessagesInTopics($topic_id_list, array(), 1);

		foreach($messages_objects as $message)
		{
			if ($message->authorise('approve') && $message->publish(KunenaForum::PUBLISHED))
			{
				$success++;
			}
		}

		if ($success)
		{
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_CATEGORIES_APPROVE_SUCCESS', $success));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_CATEGORIES_NOTHING_TO_APPROVE'));
		}

		$this->setRedirectBack();
	}
}
