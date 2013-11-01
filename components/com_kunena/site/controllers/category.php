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
			$this->redirectBack ();
			return;
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
				return;
			}

			$session = KunenaFactory::getSession();
			if ($session->userid) {
				// Mark all unread topics in the category to read
				$userinfo = $category->getUserInfo();
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
			return;
		}

		$category = KunenaForumCategoryHelper::get(JRequest::getInt('catid', 0));
		if (!$category->authorise('read')) {
			$this->app->enqueueMessage ( $category->getError(), 'error' );
			$this->redirectBack ();
			return;
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

		$this->redirectBack ();
	}

	/**
	 * Method to approve topics in selected categories on the index page
	 *
	 * @return void
	 */
	public function approveTopicsInCategories()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->redirectBack();

			return;
		}

		$categories = $this->app->input->get('categories', array ( 0 ), 'array');

		$cats = array_keys($categories);

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')->from('#__kunena_topics')->where('category_id IN (' . implode(',', $cats) . ')')->where('hold!=0');
		$db->setQuery($query);
		$topics_id = $db->loadObjectList();

		if (KunenaError::checkDatabaseError() )
		{
			return array(0, array());
		}

		$topics_list = array();

		foreach ($topics_id as $id)
		{
			$topics_list[] = $id->id;
		}

		$topics = KunenaForumTopicHelper::getTopics($topics_list);

		$success = 0;

		foreach ( $topics as $topic )
		{
			if ($topic->authorise('approve') && $topic->publish(KunenaForum::PUBLISHED))
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

		$this->redirectBack();
	}
}
