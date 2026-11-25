<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Tools;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Install\KunenaObsoleteFiles;
use Kunena\Forum\Libraries\Menu\KunenaMenuFix;
use Kunena\Forum\Libraries\Tools\KunenaCacheFolders;
use Kunena\Forum\Libraries\Tools\KunenaCategoryTransfer;
use Kunena\Forum\Libraries\Tools\KunenaOrphanFiles;
use Kunena\Forum\Libraries\Tools\KunenaScheduledTasks;
use Kunena\Forum\Libraries\Tools\KunenaSearchIndex;
use Kunena\Forum\Libraries\Tools\KunenaSpamFinder;
use Kunena\Forum\Libraries\Tools\KunenaUserCleanup;
use Kunena\Forum\Libraries\Tools\KunenaUserMerge;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * About view for Kunena cpanel
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The layouts of the tools of Kunena 8.0 which get their data from loadTool().
     *
     * @since   Kunena 8.0
     */
    public const TOOLS = ['orphanfiles', 'caches', 'usercleanup', 'spam', 'searchindex', 'transfer', 'mergeusers', 'scheduledtasks'];

    /**
     * The data of the tool of the layout (loadTool()).
     *
     * @var     array
     * @since   Kunena 8.0
     */
    protected array $tool = [];

    /**
     * Is the current user an administrator of Kunena (core.admin): only he may run a tool which changes the site.
     *
     * @var     boolean
     * @since   Kunena 8.0
     */
    protected bool $canAdmin = false;

    /**
     * The files which Kunena no longer ships, found on the site (tool "Prune old files"): path => size, time, status.
     *
     * @var     array
     * @since   Kunena 8.0
     */
    protected array $obsoleteFiles = [];

    /**
     * Who may delete them: an administrator of Kunena (core.admin).
     *
     * @var     boolean
     * @since   Kunena 8.0
     */
    protected bool $canDeleteFiles = false;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $systemReport = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $systemReportAnonymous = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $listTrashDelete = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $forumList = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $controlOptions = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $keepSticky = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $legacy = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $conflicts = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $invalid = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $catSubscribersUsers = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $topicSubscribersUsers = [];

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected mixed $catTopicSubscribers = [];

    /**
     * The diagnostics check whose rows are shown ('' for the list of the checks).
     *
     * @var     string
     * @since   Kunena 8.0
     */
    protected string $check = '';

    /**
     * The rows the check found, at most KunenaDiagnostics::ITEMS_LIMIT of them.
     *
     * @var     array
     * @since   Kunena 8.0
     */
    protected array $items = [];

    /**
     * The number of rows the check found.
     *
     * @var     integer
     * @since   Kunena 8.0
     */
    protected int $itemsTotal = 0;

    /**
     * The markers of the fields of the check (for example 'invalid').
     *
     * @var     array
     * @since   Kunena 8.0
     */
    protected array $fieldInfo = [];

    /**
     * @param   string|null  $tpl  tpl
     *
     * @return  mixed
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $layout = $this->getLayout();
        $model  = $this->getModel();

        if ($layout == 'default') {
            $this->setToolBar();
        } elseif ($layout == 'cleanupip') {
            $this->setToolBarCleanupIP();
        } elseif ($layout == 'diagnostics') {
            $check = Factory::getApplication()->getInput()->getCmd('check', '');

            if (KunenaDiagnostics::isCheck($check)) {
                $this->check      = $check;
                $this->items      = KunenaDiagnostics::getItems($check, KunenaDiagnostics::ITEMS_LIMIT);
                $this->itemsTotal = KunenaDiagnostics::count($check);
                $this->fieldInfo  = KunenaDiagnostics::getFieldInfo($check);
            }

            $this->setToolBarDiagnostics();
        } elseif ($layout == 'menu') {
            $this->legacy    = KunenaMenuFix::getLegacy();
            $this->invalid   = KunenaMenuFix::getInvalid();
            $this->conflicts = KunenaMenuFix::getConflicts();

            $this->setToolBarMenu();
        } elseif ($layout == 'prune') {
            $this->forumList       = $model->getPruneCategories();
            $this->listTrashDelete = $model->getPruneListtrashDelete();
            $this->controlOptions  = $model->getPruneControlOptions();
            $this->keepSticky      = $model->getPruneKeepSticky();

            $this->setToolBarPrune();
        } elseif ($layout == 'purgerestatements') {
            $this->setToolBarPurgeReStatements();
        } elseif ($layout == 'setupsocials') {
            $this->setToolBarSetupsocials();

            $this->socialsIsPresent       = $model->getSocialsIsPresent();
        } elseif ($layout == 'modifysocials') {
            $this->setToolBarModifysocials();

            $this->socialsIsPresent       = $model->getSocialsIsPresent();
        } elseif ($layout == 'deletesocials') {
            $this->setToolBarDeletesocials();

            $this->listSocialsNetwork       = $model->getConfiguredSocials();
        } elseif ($layout == 'recount') {
            $this->setToolBarRecount();
        } elseif ($layout == 'report') {
            $this->systemReport          = $model->getSystemReport();
            $this->systemReportAnonymous = $model->getSystemReportAnonymous();

            $this->setToolBarReport();
        } elseif ($layout == 'subscriptions') {
            $app = Factory::getApplication();
            $id  = $app->getInput()->get('id', 0, 'int');

            if ($id) {
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
        } elseif ($layout == 'syncUsers') {
            $this->setToolBarSyncUsers();
        } elseif ($layout == 'prunefiles') {
            $this->obsoleteFiles  = KunenaObsoleteFiles::findOnSite();
            $this->canDeleteFiles = $this->getCurrentUser()->authorise('core.admin', 'com_kunena');

            $this->setToolBarPruneFiles();
        } elseif (\in_array($layout, self::TOOLS, true)) {
            $this->canAdmin = $this->getCurrentUser()->authorise('core.admin', 'com_kunena');
            $this->tool     = $this->loadTool($layout);

            $this->setToolBarTool();

            // The confirmations, the "select all" boxes and the dialog of the tools. A full URL: the asset manager would look for
            // a relative one in the media folder.
            Text::script('COM_KUNENA_TOOLS_CONFIRM');
            $this->getDocument()->getWebAssetManager()->registerAndUseScript(
                'kunena_admin_tools',
                Uri::base() . 'components/com_kunena/media/js/kunena-tools.js',
                ['version' => 'auto'],
                ['defer' => true],
                ['core']
            );
        }

        return parent::display($tpl);
    }

    /**
     * The toolbar of a tool of Kunena 8.0: Close is a link back to the tools (the pages have no form adminForm which the task
     * tools.cancel would submit).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    protected function setToolBarTool(): void
    {
        ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
        ToolbarHelper::spacer();
        ToolbarHelper::link(Route::_('index.php?option=com_kunena&view=tools'), 'JTOOLBAR_CLOSE', 'cancel');
        ToolbarHelper::spacer();
        ToolbarHelper::help('COM_KUNENA', false, 'https://docs.kunena.org/en/');
    }

    /**
     * The data of a tool of Kunena 8.0.
     *
     * @param   string  $layout  The layout (TOOLS).
     *
     * @return  array
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    protected function loadTool(string $layout): array
    {
        $input = Factory::getApplication()->getInput();

        switch ($layout) {
            case 'orphanfiles':
                return [
                    'attachments' => KunenaOrphanFiles::findOnSite(KunenaOrphanFiles::ATTACHMENTS),
                    'avatars'     => KunenaOrphanFiles::findOnSite(KunenaOrphanFiles::AVATARS),
                    'missing'     => KunenaOrphanFiles::findMissingOnSite(),
                ];

            case 'caches':
                return ['folders' => KunenaCacheFolders::measureOnSite()];

            case 'usercleanup':
                $userid = $input->getInt('userid', 0);
                $search = trim($input->getString('search', ''));

                return [
                    'userid'  => $userid,
                    'search'  => $search,
                    'summary' => KunenaUserCleanup::summary($userid),
                    'matches' => $search !== '' ? $this->findUsers($search) : [],
                ];

            case 'spam':
                $days = max(1, min(365, $input->getInt('days', 30)));

                return ['days' => $days, 'suspects' => KunenaSpamFinder::findOnSite($days)];

            case 'searchindex':
                return KunenaSearchIndex::status();

            case 'transfer':
                return ['categories' => KunenaCategoryTransfer::sortTree(KunenaCategoryTransfer::rowsOnSite())];

            case 'mergeusers':
                $from = $input->getInt('from', 0);
                $into = $input->getInt('into', 0);

                return [
                    'from'      => KunenaUserCleanup::summary($from),
                    'into'      => KunenaUserCleanup::summary($into),
                    'counts'    => $from > 0 ? KunenaUserMerge::count($from) : [],
                    'error'     => $from || $into ? KunenaUserMerge::check($from, $into) : null,
                    'fromQuery' => $input->getString('from_search', ''),
                    'intoQuery' => $input->getString('into_search', ''),
                    'fromMatch' => trim($input->getString('from_search', '')) !== '' ? $this->findUsers($input->getString('from_search', '')) : [],
                    'intoMatch' => trim($input->getString('into_search', '')) !== '' ? $this->findUsers($input->getString('into_search', '')) : [],
                ];

            case 'scheduledtasks':
                Factory::getApplication()->getLanguage()->load('plg_task_kunena', JPATH_ADMINISTRATOR)
                    || Factory::getApplication()->getLanguage()->load('plg_task_kunena', JPATH_PLUGINS . '/task/kunena');

                return ['tasks' => KunenaScheduledTasks::listOnSite()] + KunenaScheduledTasks::environment();

        }

        return [];
    }

    /**
     * The users whose name, username or e-mail contains the text, or whose id it is.
     *
     * @param   string  $search  The text.
     *
     * @return  array  id => [username, name, email].
     *
     * @since   Kunena 8.0
     */
    protected function findUsers(string $search): array
    {
        $search = trim($search);

        if ($search === '') {
            return [];
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $like  = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search) . '%';
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'username', 'name', 'email']))
            ->from($db->quoteName('#__users'))
            ->where('(' . $db->quoteName('username') . ' LIKE :like1 OR ' . $db->quoteName('name') . ' LIKE :like2 OR ' . $db->quoteName('email') . ' LIKE :like3'
                . (ctype_digit($search) ? ' OR ' . $db->quoteName('id') . ' = ' . (int) $search : '') . ')')
            ->bind(':like1', $like)
            ->bind(':like2', $like)
            ->bind(':like3', $like)
            ->order($db->quoteName('username'));

        return (array) $db->setQuery($query, 0, 20)->loadAssocList('id');
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

        $canDo = ContentHelper::getActions('com_kunena');

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            ToolbarHelper::link(ConfigHelper::link(), 'JTOOLBAR_OPTIONS', 'options');
        }

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

        if (!empty($this->legacy)) {
            ToolbarHelper::custom('tools.fixLegacy', 'edit.png', 'edit_f2.png', 'COM_KUNENA_A_MENU_TOOLBAR_FIXLEGACY', false);
        }

        // TODO: check why the modal doesn't open
        /*
        // Get the toolbar object instance
        $bar = $this->getDocument()->getToolbar();

        HTMLHelper::_('bootstrap.renderModal', 'trashmenuconfirmationModal');

        $title = Text::_('COM_KUNENA_VIEW_TOOLS_RESTOREMENU_CONFIRMATION_TRASH');
        $dhtml = "<button data-bs-toggle=\"modal\" data-bs-target=\"#trashmenuconfirmationModal\" class=\"btn btn-sm\">
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
     * @since   Kunena 8.0
     */
    protected function setToolBarPruneFiles(): void
    {
        ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
        ToolbarHelper::spacer();
        ToolbarHelper::cancel('tools.cancel', 'JTOOLBAR_CLOSE');
        ToolbarHelper::spacer();
        ToolbarHelper::help('COM_KUNENA', false, 'https://docs.kunena.org/en/');
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
     * @since   Kunena 7.0.2
     */
    protected function setToolBarSetupsocials(): void
    {
        ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
        ToolbarHelper::spacer();
        ToolbarHelper::custom('tools.setupsocials', 'apply.png', 'apply_f2.png', 'COM_KUNENA_ADMIN_LAUNCH_SETUPSOCIALS', false);
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();
        ToolbarHelper::spacer();
        $helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/setup-socials';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }

    /**
     * @return  void
     *
     * @since   Kunena 7.1.0
     */
    protected function setToolBarModifysocials(): void
    {
        ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
        ToolbarHelper::spacer();
        ToolbarHelper::custom('tools.modifysocials', 'apply.png', 'apply_f2.png', 'COM_KUNENA_ADMIN_LAUNCH_MODIFYSOCIALS', false);
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();
        ToolbarHelper::spacer();
        $helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/modify-socials';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }

    /**
     * @return  void
     *
     * @since   Kunena 7.1.0
     */
    protected function setToolBarDeletesocials(): void
    {
        ToolbarHelper::title(Text::_('COM_KUNENA'), 'tools');
        ToolbarHelper::spacer();
        ToolbarHelper::custom('tools.deletesocials', 'apply.png', 'apply_f2.png', 'COM_KUNENA_ADMIN_SAVE_SOCIALS_DELETED', false);
        ToolbarHelper::spacer();
        ToolbarHelper::cancel();
        ToolbarHelper::spacer();
        $helpUrl = 'https://docs.kunena.org/en/manual/backend/tools/modify-socials';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }
}
