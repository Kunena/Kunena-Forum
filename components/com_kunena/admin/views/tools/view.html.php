<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * About view for Kunena cpanel
 */
class KunenaAdminViewTools extends KunenaView {
	function displayDefault() {
		$this->setToolBarDefault();
		$this->display ();
	}

	function displayPrune() {
		$this->forumList = $this->get('PruneCategories');
		$this->listtrashdelete = $this->get('PruneListtrashdelete');
		$this->controloptions = $this->get('PruneControlOptions');
		$this->keepSticky = $this->get('PruneKeepSticky');

		$this->setToolBarPrune();
		$this->display ();
	}

	function displaySubscriptions()
	{
		$id = $this->app->input->get('id', 0, 'int');

		$topic = KunenaForumTopicHelper::get($id);
		$acl = KunenaAccess::getInstance();
		$cat_subscribers = $acl->loadSubscribers($topic, KunenaAccess::CATEGORY_SUBSCRIPTION);

		$this->cat_subscribers_users = KunenaUserHelper::loadUsers($cat_subscribers);

		$topic_subscribers = $acl->loadSubscribers($topic, KunenaAccess::TOPIC_SUBSCRIPTION);
		$this->topic_subscribers_users = KunenaUserHelper::loadUsers($topic_subscribers);

		$this->cat_topic_subscribers = $acl->getSubscribers($topic->getCategory()->id, $id, KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION, 1, 1);

		$this->display();
	}

	function displaySyncUsers() {
		$this->setToolBarSyncUsers();
		$this->display ();
	}

	function displayRecount() {
		$this->setToolBarRecount();
		$this->display ();
	}

	function displayMenu() {
		$this->legacy = KunenaMenuFix::getLegacy();
		$this->invalid = KunenaMenuFix::getInvalid();
		$this->conflicts = KunenaMenuFix::getConflicts();

		$this->setToolBarMenu();
		$this->display ();
	}

	function displayPurgeReStatements() {
		$this->setToolBarPurgeReStatements();
		$this->display ();
	}

	function displayCleanupIP() {
		$this->setToolCleanupIP();
		$this->display ();
	}

	function displayUninstall()
	{
		$this->setToolBarUninstall();
		$this->isTFAEnabled = $this->isTFAEnabled();
		$this->display();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_FORUM_TOOLS'), 'tools' );
	}

	protected function setToolBarPrune() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'tools' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('prune', 'delete.png', 'delete_f2.png', 'COM_KUNENA_PRUNE', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarSyncUsers() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'tools' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('syncusers', 'apply.png', 'apply_f2.png', 'COM_KUNENA_SYNC', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarRecount() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'tools' );
		JToolBarHelper::spacer();

		if (version_compare(JVERSION, '3.0', '>')) {
			$bar = JToolbar::getInstance('toolbar');
			$uri = 'index.php?option=com_kunena&view=tools&task=recount&format=json&' . JSession::getFormToken() .'=1';

			$layout = KunenaLayout::factory('Button/AjaxTask')
				->set('title', JText::_('COM_KUNENA_A_RECOUNT'))
				->set('uri', $uri)
				->set('dataTarget', '#recountModal')
				->set('dataForm', '#adminForm');

			$bar->appendButton('Custom', $layout, 'recount');
		} else {
			JToolBarHelper::custom('recount', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_RECOUNT', false);
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarMenu() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'tools' );
		JToolBarHelper::spacer();
		if (!empty($this->legacy)) JToolBarHelper::custom('fixlegacy', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_MENU_TOOLBAR_FIXLEGACY', false);
		if (version_compare(JVERSION, '3', '>')) {
			JToolBarHelper::custom('trashmenu', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_TRASH_MENU', false);
		} else {
			JToolBarHelper::custom('trashmenu', 'restore.png', 'restore_f2.png', 'COM_KUNENA_A_TRASH_MENU', false);
		}
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarPurgeReStatements() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'tools' );
		JToolBarHelper::spacer();
		JToolBarHelper::trash('purgerestatements', 'COM_KUNENA_A_PURGE_RE_MENU_VALIDATE', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolCleanupIP() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'tools' );
		JToolBarHelper::spacer();
		JToolBarHelper::custom('cleanupip', 'apply.png', 'apply_f2.png', 'COM_KUNENA_TOOLS_LABEL_CLEANUP_IP', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
	}

	protected function setToolBarUninstall()
	{
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'tools' );
		JToolBarHelper::spacer();
	}

	/**
	 * Checks if the Two Factor Authentication method is globally enabled and if the
	 * user has enabled a specific TFA method on their account. Only if both conditions
	 * are met will this method return true;
	 *
	 * @param   integer  $userId  The user ID to check. Skip to use the current user.
	 *
	 * @return  boolean  True if TFA is enabled for this user
	 */
	protected function isTFAEnabled($userId = null)
	{
		if ( !version_compare(JVERSION, '3.2', '>') )
		{
			return false;
		}

		// Include the necessary user model and helper
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';

		// Is TFA globally turned off?
		$twoFactorMethods = UsersHelper::getTwoFactorMethods();

		if (count($twoFactorMethods) <= 1)
		{
			return false;
		}

		// Do we need to get the User ID?
		if (empty($userId))
		{
			$userId = JFactory::getUser()->id;
		}

		// Has this user turned on TFA on their account?
		$model = new UsersModelUser;
		$otpConfig = $model->getOtpConfig($userId);

		return !(empty($otpConfig->method) || ($otpConfig->method == 'none'));
	}
}
