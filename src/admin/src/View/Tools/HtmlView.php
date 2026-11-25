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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Install\KunenaObsoleteFiles;
use Kunena\Forum\Libraries\Menu\KunenaMenuFix;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Joomla\CMS\Component\ComponentHelper;
use Kunena\Forum\Libraries\Tools\KunenaAccessOverview;
use Kunena\Forum\Libraries\Tools\KunenaBBCodeCheck;
use Kunena\Forum\Libraries\Tools\KunenaCacheFolders;
use Kunena\Forum\Libraries\Tools\KunenaDuplicatePosts;
use Kunena\Forum\Libraries\Tools\KunenaImageOptimizer;
use Kunena\Forum\Libraries\Tools\KunenaMailPreview;
use Kunena\Forum\Libraries\Tools\KunenaUpdateCheck;
use Kunena\Forum\Libraries\Tools\KunenaBrokenLinks;
use Kunena\Forum\Libraries\Tools\KunenaDraftsSchedule;
use Kunena\Forum\Libraries\Tools\KunenaGoneTopics;
use Kunena\Forum\Libraries\Tools\KunenaMissingAttachments;
use Kunena\Forum\Libraries\Tools\KunenaPollsOverview;
use Kunena\Forum\Libraries\Tools\KunenaProfileSpam;
use Kunena\Forum\Libraries\Tools\KunenaSecurityAudit;
use Kunena\Forum\Libraries\Tools\KunenaTranslationCheck;
use Kunena\Forum\Libraries\Tools\KunenaCategoryPermissions;
use Kunena\Forum\Libraries\Tools\KunenaModeratorActivity;
use Kunena\Forum\Libraries\Tools\KunenaSettingsSnapshot;
use Kunena\Forum\Libraries\Tools\KunenaSubscriberAddresses;
use Kunena\Forum\Libraries\Tools\KunenaAvatarFiles;
use Kunena\Forum\Libraries\Tools\KunenaBanReview;
use Kunena\Forum\Libraries\Tools\KunenaCategoryHealth;
use Kunena\Forum\Libraries\Tools\KunenaDuplicateTitles;
use Kunena\Forum\Libraries\Tools\KunenaLanguageOverrides;
use Kunena\Forum\Libraries\Tools\KunenaSettingsHistory;
use Kunena\Forum\Libraries\Tools\KunenaThankyouRings;
use Kunena\Forum\Libraries\Tools\KunenaUnansweredTopics;
use Kunena\Forum\Libraries\Tools\KunenaBigTopics;
use Kunena\Forum\Libraries\Tools\KunenaContentLimits;
use Kunena\Forum\Libraries\Tools\KunenaModeratorEdits;
use Kunena\Forum\Libraries\Tools\KunenaReportsOverview;
use Kunena\Forum\Libraries\Tools\KunenaWarningsOverview;
use Kunena\Forum\Libraries\Tools\KunenaCategoryMerge;
use Kunena\Forum\Libraries\Tools\KunenaCategoryTransfer;
use Kunena\Forum\Libraries\Tools\KunenaDatabaseTables;
use Kunena\Forum\Libraries\Tools\KunenaFindReplace;
use Kunena\Forum\Libraries\Tools\KunenaInactiveUsers;
use Kunena\Forum\Libraries\Tools\KunenaMailTest;
use Kunena\Forum\Libraries\Tools\KunenaModerationQueue;
use Kunena\Forum\Libraries\Tools\KunenaOrphanFiles;
use Kunena\Forum\Libraries\Tools\KunenaPermissionCheck;
use Kunena\Forum\Libraries\Tools\KunenaScheduledTasks;
use Kunena\Forum\Libraries\Tools\KunenaSearchIndex;
use Kunena\Forum\Libraries\Tools\KunenaSpamFinder;
use Kunena\Forum\Libraries\Tools\KunenaStorageReport;
use Kunena\Forum\Libraries\Tools\KunenaSubscriptions;
use Kunena\Forum\Libraries\Tools\KunenaTokenRestore;
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
    public const TOOLS = [
        'orphanfiles', 'caches', 'usercleanup', 'spam', 'searchindex', 'transfer', 'mergeusers', 'restoretokens', 'scheduledtasks', 'mailtest',
        'findreplace', 'mergecategories', 'moderation', 'inactiveusers', 'subscriptionsoverview', 'permissions', 'storage', 'databasetables',
        'duplicates', 'bbcodecheck', 'optimizeimages', 'accessoverview', 'userexport', 'mailpreview', 'beforeupdate',
        'translations', 'security', 'missingfiles', 'profilespam', 'brokenlinks', 'polls', 'gonetopics', 'drafts',
        'settings', 'moderators', 'subscriberaddresses', 'copypermissions',
        'unanswered', 'banreview', 'thankyourings', 'categoryhealth', 'duplicatetitles', 'overrides', 'avatarfiles', 'settingshistory',
        'reports', 'warnings', 'moderatoredits', 'contentlimits', 'bigtopics',
    ];

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
     * The address of the suggestions of the user fields (task tools.searchUsers, with the token of the session).
     *
     * @var     string
     * @since   Kunena 8.0
     */
    protected string $usersEndpoint = '';

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

            // The suggestions of the user fields: the address with the token of the session.
            $this->usersEndpoint = Route::_('index.php?option=com_kunena&task=tools.searchUsers&format=json&' . Session::getFormToken() . '=1', false);

            // The confirmations, the "select all" boxes, the dialog and the suggestions of the tools. A full URL: the asset
            // manager would look for a relative one in the media folder.
            Text::script('COM_KUNENA_TOOLS_CONFIRM');
            Text::script('COM_KUNENA_TOOLS_USER_NO_MATCH');
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

            case 'restoretokens':
                $folders = KunenaForum::isDev() ? KunenaTokenRestore::linkedFolders() : [];

                return ['dev' => KunenaForum::isDev(), 'folders' => $folders, 'files' => $folders ? KunenaTokenRestore::find($folders) : []];

            case 'scheduledtasks':
                Factory::getApplication()->getLanguage()->load('plg_task_kunena', JPATH_ADMINISTRATOR)
                    || Factory::getApplication()->getLanguage()->load('plg_task_kunena', JPATH_PLUGINS . '/task/kunena');

                return ['tasks' => KunenaScheduledTasks::listOnSite()] + KunenaScheduledTasks::environment();

            case 'mailtest':
                $settings = KunenaMailTest::settings();

                return ['settings' => $settings, 'problems' => KunenaMailTest::problems($settings), 'me' => (string) $this->getCurrentUser()->email];
            case 'findreplace':
                $options = KunenaFindReplace::options([
                    'search'   => $input->get('search', '', 'raw'),
                    'replace'  => $input->get('replace', '', 'raw'),
                    'subjects' => $input->getInt('subjects', 0),
                    'case'     => $input->getInt('case', 0),
                ]);
                $error = $options['search'] !== '' ? KunenaFindReplace::check($options) : null;

                return [
                    'options' => $options,
                    'error'   => $error,
                    'found'   => $options['search'] !== '' && $error === null ? KunenaFindReplace::findOnSite($options) : null,
                ];

            case 'mergecategories':
                $rows = KunenaCategoryTransfer::rowsOnSite();
                $from = $input->getInt('from', 0);
                $into = $input->getInt('into', 0);

                return [
                    'categories' => KunenaCategoryTransfer::sortTree($rows),
                    'from'       => $from,
                    'into'       => $into,
                    'error'      => $from && $into ? KunenaCategoryMerge::check($rows, $from, $into) : null,
                    'counts'     => $from ? KunenaCategoryMerge::count($from) : null,
                ];

            case 'moderation':
                return ['unapproved' => KunenaModerationQueue::unapprovedOnSite(), 'reports' => KunenaModerationQueue::reportsOnSite()];

            case 'inactiveusers':
                $criteria = KunenaInactiveUsers::criteria(['kind' => $input->getCmd('kind', ''), 'months' => $input->getInt('months', 0)]);

                return ['criteria' => $criteria, 'found' => KunenaInactiveUsers::findOnSite($criteria)];

            case 'subscriptionsoverview':
                $userid = $input->getInt('userid', 0);
                $search = trim($input->getString('search', ''));

                return KunenaSubscriptions::overviewOnSite() + [
                    'userid'   => $userid,
                    'search'   => $search,
                    'matches'  => $search !== '' ? $this->findUsers($search) : [],
                    'user'     => $userid ? KunenaUserCleanup::summary($userid) : null,
                    'userSubs' => $userid ? KunenaSubscriptions::ofUserOnSite($userid) : null,
                ];

            case 'permissions':
                $userid     = $input->getInt('userid', 0);
                $catid      = $input->getInt('catid', 0);
                $search     = trim($input->getString('search', ''));
                $categories = KunenaCategoryTransfer::sortTree(KunenaCategoryTransfer::rowsOnSite());
                $data       = ['userid' => $userid, 'catid' => $catid, 'search' => $search, 'categories' => $categories, 'matches' => $search !== '' ? $this->findUsers($search) : [], 'result' => null];
                $category   = $catid ? KunenaCategoryHelper::get($catid) : null;

                // A guest is user 0: the checker answers for him too.
                if ($category && $category->exists() && ($userid === 0 || KunenaUserCleanup::summary($userid))) {
                    $user               = KunenaUserHelper::get($userid);
                    $data['userName']   = $userid ? $user->getName() : Text::_('COM_KUNENA_TOOLS_PERMS_GUEST');
                    $data['result']     = KunenaPermissionCheck::check($category, $user);
                    $data['profile']    = KunenaPermissionCheck::profile($user, $category);
                    $data['category']   = KunenaPermissionCheck::categoryAccess($category);
                    $data['rules']      = KunenaPermissionCheck::rules();
                }

                return $data;

            case 'storage':
                return KunenaStorageReport::reportOnSite();

            case 'databasetables':
                return ['supported' => KunenaDatabaseTables::isSupported(), 'tables' => KunenaDatabaseTables::listOnSite()];

            case 'duplicates':
                return ['groups' => KunenaDuplicatePosts::findOnSite()];

            case 'bbcodecheck':
                // No preview here: the BBCode of Kunena renders with the layouts of the template of the site.
                return KunenaBBCodeCheck::findOnSite();

            case 'optimizeimages':
                return KunenaImageOptimizer::findOnSite() + ['webp' => KunenaImageOptimizer::canWebp()];

            case 'accessoverview':
                return ['categories' => KunenaAccessOverview::listOnSite()];

            case 'userexport':
                $userid = $input->getInt('userid', 0);
                $search = trim($input->getString('search', ''));

                return [
                    'userid'  => $userid,
                    'search'  => $search,
                    'matches' => $search !== '' ? $this->findUsers($search) : [],
                    'summary' => $userid ? KunenaUserCleanup::summary($userid) : null,
                ];

            case 'mailpreview':
                $languages = KunenaMailPreview::languagesOnSite();
                $template  = $input->getString('template', array_key_first(KunenaMailPreview::TEMPLATES));
                $language  = $input->getString('language', '');

                if (!isset($languages[$language])) {
                    $language = (string) ComponentHelper::getParams('com_languages')->get('site', 'en-GB');
                }

                return [
                    'template'  => isset(KunenaMailPreview::TEMPLATES[$template]) ? $template : array_key_first(KunenaMailPreview::TEMPLATES),
                    'language'  => $language,
                    'languages' => $languages,
                    'rendered'  => KunenaMailPreview::renderOnSite(isset(KunenaMailPreview::TEMPLATES[$template]) ? $template : array_key_first(KunenaMailPreview::TEMPLATES), $language),
                    'edit'      => KunenaMailPreview::editableOnSite(isset(KunenaMailPreview::TEMPLATES[$template]) ? $template : array_key_first(KunenaMailPreview::TEMPLATES), $language),
                    'html'      => KunenaMailPreview::htmlOnSite(),
                    'me'        => (string) $this->getCurrentUser()->email,
                ];

            case 'beforeupdate':
                return ['checks' => KunenaUpdateCheck::checksOnSite()];

            case 'translations':
                $tag = $input->getString('language', '');
                $tag = \in_array($tag, KunenaTranslationCheck::tagsOnSite(), true) ? $tag : '';

                return ['summary' => KunenaTranslationCheck::summaryOnSite(), 'language' => $tag, 'detail' => $tag !== '' ? KunenaTranslationCheck::checkOnSite($tag) : null];

            case 'security':
                return ['checks' => KunenaSecurityAudit::checksOnSite()];

            case 'missingfiles':
                return KunenaMissingAttachments::findOnSite();

            case 'profilespam':
                $maxPosts = $input->getInt('maxposts', KunenaProfileSpam::MAX_POSTS[1]);
                $maxPosts = \in_array($maxPosts, KunenaProfileSpam::MAX_POSTS, true) ? $maxPosts : KunenaProfileSpam::MAX_POSTS[1];

                return ['maxPosts' => $maxPosts, 'items' => KunenaProfileSpam::findOnSite($maxPosts)];

            case 'brokenlinks':
                return KunenaBrokenLinks::findOnSite();

            case 'polls':
                $state = $input->getCmd('state', 'all');
                $state = \in_array($state, KunenaPollsOverview::STATES, true) ? $state : 'all';

                return ['state' => $state, 'polls' => KunenaPollsOverview::listOnSite($state, time())];

            case 'gonetopics':
                return KunenaGoneTopics::listOnSite();

            case 'drafts':
                $age = $input->getInt('age', KunenaDraftsSchedule::AGES[2]);
                $age = \in_array($age, KunenaDraftsSchedule::AGES, true) ? $age : KunenaDraftsSchedule::AGES[2];

                return ['age' => $age, 'drafts' => KunenaDraftsSchedule::draftsOnSite($age, time()), 'scheduled' => KunenaDraftsSchedule::scheduledOnSite()];

            case 'settings':
                // A snapshot which was uploaded waits in the session for its preview.
                $import  = Factory::getApplication()->getUserState('com_kunena.admin.tools.settings');
                $current = KunenaSettingsSnapshot::currentOnSite();

                return [
                    'changed' => KunenaSettingsSnapshot::changed($current, KunenaSettingsSnapshot::defaultsOnSite()),
                    'preview' => \is_array($import) ? KunenaSettingsSnapshot::preview($import, $current) : null,
                ];

            case 'moderators':
                return KunenaModeratorActivity::reportOnSite($input->getInt('days', KunenaModeratorActivity::PERIODS[1]), time());

            case 'subscriberaddresses':
                return KunenaSubscriberAddresses::findOnSite();

            case 'copypermissions':
                $rows       = array_column(KunenaCategoryTransfer::sortTree(KunenaCategoryTransfer::rowsOnSite()), null, 'id');
                $source     = $input->getInt('source', 0);
                $targets    = array_map('intval', (array) $input->get('targets', [], 'array'));
                $moderators = (bool) $input->getInt('moderators', 0);
                $preview    = $source && $targets ? KunenaCategoryPermissions::previewOnSite($rows, $source, $targets, $moderators) : [];
                $users      = [];

                foreach ($preview as $change) {
                    $users = array_merge($users, $change['add'], $change['remove']);
                }

                return [
                    'rows' => $rows, 'source' => $source, 'targets' => $targets, 'moderators' => $moderators, 'preview' => $preview,
                    'names' => KunenaCategoryPermissions::namesOnSite($users),
                ];

            case 'unanswered':
                return KunenaUnansweredTopics::reportOnSite($input->getInt('days', KunenaUnansweredTopics::AGES[0]), time());

            case 'banreview':
                return KunenaBanReview::reviewOnSite(time());

            case 'thankyourings':
                return KunenaThankyouRings::findOnSite();

            case 'categoryhealth':
                return KunenaCategoryHealth::checkOnSite($input->getInt('days', KunenaCategoryHealth::PERIODS[1]), time());

            case 'duplicatetitles':
                return KunenaDuplicateTitles::findOnSite();

            case 'overrides':
                return ['files' => KunenaLanguageOverrides::checkOnSite()];

            case 'avatarfiles':
                return KunenaAvatarFiles::checkOnSite();

            case 'settingshistory':
                return KunenaSettingsHistory::historyOnSite($input->getCmd('setting', ''));

            case 'reports':
                return KunenaReportsOverview::reportOnSite($input->getInt('days', KunenaReportsOverview::PERIODS[1]), time());

            case 'warnings':
                return KunenaWarningsOverview::reportOnSite($input->getInt('days', KunenaWarningsOverview::PERIODS[0]), time());

            case 'moderatoredits':
                return KunenaModeratorEdits::reportOnSite($input->getInt('days', KunenaModeratorEdits::PERIODS[1]), time(), (bool) $input->getInt('noreason', 0));

            case 'contentlimits':
                return KunenaContentLimits::findOnSite();

            case 'bigtopics':
                return KunenaBigTopics::findOnSite($input->getInt('size', KunenaBigTopics::SIZES[1]));
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
        return KunenaUserCleanup::search($search);
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
