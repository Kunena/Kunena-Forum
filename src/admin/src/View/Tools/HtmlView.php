<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Tools;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Login\KunenaLogin;
use Kunena\Forum\Libraries\Menu\KunenaMenuFix;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * About view for Kunena cpanel
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $systemReport = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $systemReportAnonymous = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $listTrashDelete = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $forumList = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $controlOptions = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $keepSticky = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $legacy = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $conflicts = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $invalid = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $catSubscribersUsers = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $topicSubscribersUsers = [];

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $catTopicSubscribers = [];

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		if ($layout == 'default')
		{
			$this->setToolBar();
		}
		elseif ($layout == 'cleanupip')
		{
			$this->setToolBarCleanupIP();
		}
		elseif ($layout == 'diagnostics')
		{
			$this->setToolBarDiagnostics();
		}
		elseif ($layout == 'menu')
		{
			$this->legacy    = KunenaMenuFix::getLegacy();
			$this->invalid   = KunenaMenuFix::getInvalid();
			$this->conflicts = KunenaMenuFix::getConflicts();

			$this->setToolBarMenu();
		}
		elseif ($layout == 'prune')
		{
			$this->forumList       = $this->get('PruneCategories');
			$this->listTrashDelete = $this->get('PruneListtrashDelete');
			$this->controlOptions  = $this->get('PruneControlOptions');
			$this->keepSticky      = $this->get('PruneKeepSticky');

			$this->setToolBarPrune();
		}
		elseif ($layout == 'purgerestatements')
		{
			$this->setToolBarPurgeReStatements();
		}
		elseif ($layout == 'recount')
		{
			$this->setToolBarRecount();
		}
		elseif ($layout == 'report')
		{
			$this->systemReport          = $this->get('SystemReport');
			$this->systemReportAnonymous = $this->get('SystemReportAnonymous');

			$this->setToolBarReport();
		}
		elseif ($layout == 'subscriptions')
		{
			$app = Factory::getApplication();
			$id  = $app->input->get('id', 0, 'int');

			if ($id)
			{
				$topic          = KunenaTopicHelper::get($id);
				$acl            = KunenaAccess::getInstance();
				$catSubscribers = $acl->loadSubscribers($topic, KunenaAccess::CATEGORY_SUBSCRIPTION);

				$this->catSubscribersUsers   = KunenaUserHelper::loadUsers($catSubscribers);
				$topicSubscribers            = $acl->loadSubscribers($topic, KunenaAccess::TOPIC_SUBSCRIPTION);
				$this->topicSubscribersUsers = KunenaUserHelper::loadUsers($topicSubscribers);
				$this->catTopicSubscribers   = $acl->getSubscribers(
					$topic->getCategory()->id,
					$id,
					KunenaAccess::CATEGORY_SUBSCRIPTION | KunenaAccess::TOPIC_SUBSCRIPTION,
					1,
					1
				);
			}

			$this->setToolBarSubscriptions();
		}
		elseif ($layout == 'syncUsers')
		{
			$this->setToolBarSyncUsers();
		}
		elseif ($layout == 'uninstall')
		{
			$login              = KunenaLogin::getInstance();
			$this->isTFAEnabled = $login->isTFAEnabled();

			$this->setToolBarUninstall();
		}

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBar(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_FORUM_TOOLS'), 'tools');
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarCleanupIP(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('tools.cleanupip', 'apply.png', 'apply_f2.png', 'COM_KUNENA_TOOLS_LABEL_CLEANUP_IP', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/remove-stored-ip-addresses';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarDiagnostics(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/diagnostics';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarMenu(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();

		// Get the toolbar object instance
		$bar = Toolbar::getInstance('toolbar');

		if (!empty($this->legacy))
		{
			ToolbarHelper::custom('tools.fixLegacy', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_MENU_TOOLBAR_FIXLEGACY', false);
		}

		// TODO: check why the modal doesn't open
		/*
		HTMLHelper::_('bootstrap.renderModal', 'trashmenuconfirmationModal');

		$title = Text::_('COM_KUNENA_VIEW_TOOLS_RESTOREMENU_CONFIRMATION_TRASH');
		$dhtml = "<button data-bs-toggle=\"modal\" data-bs-target=\"#trashmenuconfirmationModal\" class=\"btn btn-small\">
					<i class=\"icon-apply\" title=\"$title\"> </i>
						$title</button>";
						$bar->appendButton('Custom', $dhtml, 'batch');*/

		ToolbarHelper::custom('tools.trashmenu', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_TRASH_MENU', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/menu-manager';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarPrune(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('tools.prune', 'delete.png', 'delete_f2.png', 'COM_KUNENA_PRUNE', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/prune-categories';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarPurgeReStatements(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::trash('tools.purgerestatements', 'COM_KUNENA_A_PURGE_RE_MENU_VALIDATE', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/purge-re-prefixes';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarRecount(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('tools.recount', 'apply.png', 'apply_f2.png', 'COM_KUNENA_A_RECOUNT', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/recount-statistics';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarReport(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'help');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/faq/configuration-report';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarSubscriptions(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'help');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/faq/configuration-report';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarSyncUsers(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('tools.syncUsers', 'apply.png', 'apply_f2.png', 'COM_KUNENA_SYNC', false);
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/synchronize-users';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarUninstall(): void
	{
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/uninstall-kunena';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}
}
