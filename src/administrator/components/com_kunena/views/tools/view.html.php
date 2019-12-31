<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * About view for Kunena cpanel
 *
 * @since Kunena
 */
class KunenaAdminViewTools extends KunenaView
{
	/**
	 * @since Kunena
	 */
	public function displayDefault()
	{
		$this->setToolBarDefault();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarDefault()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_FORUM_TOOLS'), 'tools');
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayPrune()
	{
		$this->forumList       = $this->get('PruneCategories');
		$this->listtrashdelete = $this->get('PruneListtrashdelete');
		$this->controloptions  = $this->get('PruneControlOptions');
		$this->keepSticky      = $this->get('PruneKeepSticky');

		$this->setToolBarPrune();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarPrune()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('prune', 'delete.png', 'delete_f2.png', 'COM_KUNENA_PRUNE', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools/prune-categories';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 */
	public function displaySubscriptions()
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
	 * @since Kunena
	 */
	public function displaySyncUsers()
	{
		$this->setToolBarSyncUsers();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarSyncUsers()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('syncusers', 'apply.png', 'apply_f2.png', 'COM_KUNENA_SYNC', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools/synchronize-users';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayRecount()
	{
		$this->setToolBarRecount();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarRecount()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('recount', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_RECOUNT', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools/recount-statistics';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayMenu()
	{
		$this->legacy    = KunenaMenuFix::getLegacy();
		$this->invalid   = KunenaMenuFix::getInvalid();
		$this->conflicts = KunenaMenuFix::getConflicts();

		$this->setToolBarMenu();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarMenu()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();

		if (!empty($this->legacy))
		{
			ToolbarHelper::custom('fixlegacy', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_MENU_TOOLBAR_FIXLEGACY', false);
		}

		ToolbarHelper::custom('trashmenu', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_TRASH_MENU', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools/menu-manager';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayPurgeReStatements()
	{
		$this->setToolBarPurgeReStatements();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarPurgeReStatements()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::trash('purgerestatements', 'COM_KUNENA_A_PURGE_RE_MENU_VALIDATE', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools/purge-re-prefixes';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayCleanupIP()
	{
		$this->setToolCleanupIP();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolCleanupIP()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('cleanupip', 'apply.png', 'apply_f2.png', 'COM_KUNENA_TOOLS_LABEL_CLEANUP_IP', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools/remove-stored-ip-addresses';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayDiagnostics()
	{
		$this->setToolBarDiagnostics();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarDiagnostics()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools/diagnostics';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayUninstall()
	{
		$this->setToolBarUninstall();

		$login              = KunenaLogin::getInstance();
		$this->isTFAEnabled = $login->isTFAEnabled();

		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarUninstall()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/tools/uninstall-kunena';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @since Kunena
	 */
	public function displayReport()
	{
		$this->systemreport           = $this->get('SystemReport');
		$this->systemreport_anonymous = $this->get('SystemReportAnonymous');
		$this->setToolBarReport();
		$this->display();
	}

	/**
	 * @since Kunena
	 */
	protected function setToolBarReport()
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'help');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/faq/configuration-report';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
