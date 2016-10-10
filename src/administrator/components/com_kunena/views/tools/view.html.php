<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * About view for Kunena cpanel
 * @since Kunena
 */
class KunenaAdminViewTools extends KunenaView
{
	/**
	 *
	 * @since Kunena
	 */
	function displayDefault()
	{
		$this->systemreport = $this->get('SystemReport');

		$this->systemreport_anonymous = $this->get('SystemReportAnonymous');
		$this->setToolBarDefault();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayPrune()
	{
		$this->forumList       = $this->get('PruneCategories');
		$this->listtrashdelete = $this->get('PruneListtrashdelete');
		$this->controloptions  = $this->get('PruneControlOptions');
		$this->keepSticky      = $this->get('PruneKeepSticky');

		$this->setToolBarPrune();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displaySubscriptions()
	{
		$id = $this->app->input->get('id', 0, 'int');

		$topic           = KunenaForumTopicHelper::get($id);
		$acl             = KunenaAccess::getInstance();
		$cat_subscribers = $acl->loadSubscribers($topic, KunenaAccess::CATEGORY_SUBSCRIPTION);

		$this->cat_subscribers_users = KunenaUserHelper::loadUsers($cat_subscribers);

		$topic_subscribers             = $acl->loadSubscribers($topic, KunenaAccess::TOPIC_SUBSCRIPTION);
		$this->topic_subscribers_users = KunenaUserHelper::loadUsers($topic_subscribers);

		$this->cat_topic_subscribers = $acl->getSubscribers($topic->getCategory()->id, $id, KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION, 1, 1);

		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displaySyncUsers()
	{
		$this->setToolBarSyncUsers();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayRecount()
	{
		$this->setToolBarRecount();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayMenu()
	{
		$this->legacy    = KunenaMenuFix::getLegacy();
		$this->invalid   = KunenaMenuFix::getInvalid();
		$this->conflicts = KunenaMenuFix::getConflicts();

		$this->setToolBarMenu();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayPurgeReStatements()
	{
		$this->setToolBarPurgeReStatements();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayCleanupIP()
	{
		$this->setToolCleanupIP();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayDiagnostics()
	{
		$this->setToolBarDiagnostics();
		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	function displayUninstall()
	{
		$this->setToolBarUninstall();

		$login              = KunenaLogin::getInstance();
		$this->isTFAEnabled = $login->isTFAEnabled();

		$this->display();
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarDefault()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_FORUM_TOOLS'), 'tools');
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarPrune()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'tools');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('prune', 'delete.png', 'delete_f2.png', 'COM_KUNENA_PRUNE', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarSyncUsers()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'tools');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('syncusers', 'apply.png', 'apply_f2.png', 'COM_KUNENA_SYNC', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarRecount()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'tools');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('recount', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_RECOUNT', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarMenu()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'tools');
		JToolBarHelper::spacer();

		if (!empty($this->legacy))
		{
			JToolBarHelper::custom('fixlegacy', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_MENU_TOOLBAR_FIXLEGACY', false);
		}

		JToolBarHelper::custom('trashmenu', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_TRASH_MENU', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarPurgeReStatements()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'tools');
		JToolBarHelper::spacer();
		JToolBarHelper::trash('purgerestatements', 'COM_KUNENA_A_PURGE_RE_MENU_VALIDATE', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolCleanupIP()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'tools');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('cleanupip', 'apply.png', 'apply_f2.png', 'COM_KUNENA_TOOLS_LABEL_CLEANUP_IP', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarUninstall()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'tools');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 *
	 * @since Kunena
	 */
	protected function setToolBarDiagnostics()
	{
		JToolBarHelper::title(JText::_('COM_KUNENA'), 'tools');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		$help_url = 'https://www.kunena.org/docs/';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}
}
