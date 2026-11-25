<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Version\KunenaVersion;

// The tools: [layout, label, description, icon]. A layout of the view install is given as a full query.
$groups = [
    'COM_KUNENA_ADMIN_TOOLS' => [
        ['report', 'COM_KUNENA_REPORT_SYSTEM', 'COM_KUNENA_REPORT_SYSTEM_DESC', 'file-lines'],
        ['prune', 'COM_KUNENA_C_PRUNETAB', 'COM_KUNENA_C_PRUNETAB_DESC', 'scissors'],
        ['syncUsers', 'COM_KUNENA_SYNC_USERS', 'COM_KUNENA_SYNC_USERS_DESC', 'users-gear'],
        ['recount', 'COM_KUNENA_A_RECOUNT', 'COM_KUNENA_A_RECOUNT_DESC', 'calculator'],
        ['transfer', 'COM_KUNENA_TOOLS_TRANSFER', 'COM_KUNENA_TOOLS_TRANSFER_DESC', 'right-left'],
        ['searchindex', 'COM_KUNENA_TOOLS_SEARCHINDEX', 'COM_KUNENA_TOOLS_SEARCHINDEX_DESC', 'magnifying-glass'],
    ],
    'COM_KUNENA_ADMIN_TOOLS_CONTENT' => [
        ['moderation', 'COM_KUNENA_TOOLS_QUEUE', 'COM_KUNENA_TOOLS_QUEUE_DESC', 'gavel'],
        ['unanswered', 'COM_KUNENA_TOOLS_UNANSWERED', 'COM_KUNENA_TOOLS_UNANSWERED_DESC', 'comment-slash'],
        ['duplicatetitles', 'COM_KUNENA_TOOLS_TITLES', 'COM_KUNENA_TOOLS_TITLES_DESC', 'heading'],
        ['categoryhealth', 'COM_KUNENA_TOOLS_CATHEALTH', 'COM_KUNENA_TOOLS_CATHEALTH_DESC', 'folder-tree'],
        ['findreplace', 'COM_KUNENA_TOOLS_REPLACE', 'COM_KUNENA_TOOLS_REPLACE_DESC', 'arrow-right-arrow-left'],
        ['mergecategories', 'COM_KUNENA_TOOLS_CATMERGE', 'COM_KUNENA_TOOLS_CATMERGE_DESC', 'object-group'],
        ['duplicates', 'COM_KUNENA_TOOLS_DUPLICATES', 'COM_KUNENA_TOOLS_DUPLICATES_DESC', 'clone'],
        ['bbcodecheck', 'COM_KUNENA_TOOLS_BBCODE', 'COM_KUNENA_TOOLS_BBCODE_DESC', 'code'],
        ['brokenlinks', 'COM_KUNENA_TOOLS_LINKS', 'COM_KUNENA_TOOLS_LINKS_DESC', 'link-slash'],
        ['polls', 'COM_KUNENA_TOOLS_POLLS', 'COM_KUNENA_TOOLS_POLLS_DESC', 'square-poll-vertical'],
        ['drafts', 'COM_KUNENA_TOOLS_DRAFTS', 'COM_KUNENA_TOOLS_DRAFTS_DESC', 'file-pen'],
    ],
    'COM_KUNENA_ADMIN_TOOLS_USERS' => [
        ['spam', 'COM_KUNENA_TOOLS_SPAM', 'COM_KUNENA_TOOLS_SPAM_DESC', 'shield-virus'],
        ['profilespam', 'COM_KUNENA_TOOLS_PROFILESPAM', 'COM_KUNENA_TOOLS_PROFILESPAM_DESC', 'id-card'],
        ['banreview', 'COM_KUNENA_TOOLS_BANS', 'COM_KUNENA_TOOLS_BANS_DESC', 'user-lock'],
        ['thankyourings', 'COM_KUNENA_TOOLS_RINGS', 'COM_KUNENA_TOOLS_RINGS_DESC', 'handshake'],
        ['moderators', 'COM_KUNENA_TOOLS_MODERATORS', 'COM_KUNENA_TOOLS_MODERATORS_DESC', 'user-shield'],
        ['subscriberaddresses', 'COM_KUNENA_TOOLS_ADDRESSES', 'COM_KUNENA_TOOLS_ADDRESSES_DESC', 'envelope-circle-check'],
        ['copypermissions', 'COM_KUNENA_TOOLS_COPYPERMS', 'COM_KUNENA_TOOLS_COPYPERMS_DESC', 'copy'],
        ['usercleanup', 'COM_KUNENA_TOOLS_USERCLEANUP', 'COM_KUNENA_TOOLS_USERCLEANUP_DESC', 'user-xmark'],
        ['mergeusers', 'COM_KUNENA_TOOLS_MERGE', 'COM_KUNENA_TOOLS_MERGE_DESC', 'code-merge'],
        ['permissions', 'COM_KUNENA_TOOLS_PERMS', 'COM_KUNENA_TOOLS_PERMS_DESC', 'user-shield'],
        ['inactiveusers', 'COM_KUNENA_TOOLS_INACTIVE', 'COM_KUNENA_TOOLS_INACTIVE_DESC', 'user-clock'],
        ['subscriptionsoverview', 'COM_KUNENA_TOOLS_SUBS', 'COM_KUNENA_TOOLS_SUBS_DESC', 'bell'],
        ['accessoverview', 'COM_KUNENA_TOOLS_ACCESS', 'COM_KUNENA_TOOLS_ACCESS_DESC', 'table-cells'],
        ['userexport', 'COM_KUNENA_TOOLS_EXPORT', 'COM_KUNENA_TOOLS_EXPORT_DESC', 'file-zipper'],
    ],
    'COM_KUNENA_ADMIN_TOOLS_FILES' => [
        ['orphanfiles', 'COM_KUNENA_TOOLS_ORPHANS', 'COM_KUNENA_TOOLS_ORPHANS_DESC', 'file-circle-question'],
        ['missingfiles', 'COM_KUNENA_TOOLS_MISSINGFILES', 'COM_KUNENA_TOOLS_MISSINGFILES_DESC', 'file-circle-xmark'],
        ['caches', 'COM_KUNENA_TOOLS_CACHES', 'COM_KUNENA_TOOLS_CACHES_DESC', 'database'],
        ['storage', 'COM_KUNENA_TOOLS_STORAGE', 'COM_KUNENA_TOOLS_STORAGE_DESC', 'hard-drive'],
        ['optimizeimages', 'COM_KUNENA_TOOLS_IMAGES', 'COM_KUNENA_TOOLS_IMAGES_DESC', 'image'],
    ],
    'COM_KUNENA_ADMIN_TOOLS_SYSTEM' => [
        ['scheduledtasks', 'COM_KUNENA_TOOLS_TASKS', 'COM_KUNENA_TOOLS_TASKS_DESC', 'clock-rotate-left'],
        ['mailtest', 'COM_KUNENA_TOOLS_MAILTEST', 'COM_KUNENA_TOOLS_MAILTEST_DESC', 'envelope-circle-check'],
        ['mailpreview', 'COM_KUNENA_TOOLS_MAILS', 'COM_KUNENA_TOOLS_MAILS_DESC', 'envelope-open-text'],
        ['beforeupdate', 'COM_KUNENA_TOOLS_UPDATE', 'COM_KUNENA_TOOLS_UPDATE_DESC', 'rocket'],
        ['security', 'COM_KUNENA_TOOLS_SECURITY', 'COM_KUNENA_TOOLS_SECURITY_DESC', 'shield-halved'],
        ['settings', 'COM_KUNENA_TOOLS_SETTINGS', 'COM_KUNENA_TOOLS_SETTINGS_DESC', 'sliders'],
        ['translations', 'COM_KUNENA_TOOLS_TRANSLATIONS', 'COM_KUNENA_TOOLS_TRANSLATIONS_DESC', 'language'],
        ['overrides', 'COM_KUNENA_TOOLS_OVERRIDES', 'COM_KUNENA_TOOLS_OVERRIDES_DESC', 'pen-ruler'],
        ['gonetopics', 'COM_KUNENA_TOOLS_GONE', 'COM_KUNENA_TOOLS_GONE_DESC', 'ban'],
        ['databasetables', 'COM_KUNENA_TOOLS_TABLES', 'COM_KUNENA_TOOLS_TABLES_DESC', 'table'],
        ['menu', 'COM_KUNENA_A_MENU_MANAGER', 'COM_KUNENA_A_MENU_MANAGER_DESC', 'bars'],
        ['purgerestatements', 'COM_KUNENA_A_PURGE_RE_STATEMENTS', 'COM_KUNENA_A_PURGE_RE_STATEMENTS_DESC', 'eraser'],
        ['cleanupip', 'COM_KUNENA_CPANEL_LABEL_CLEANUP_IP', 'COM_KUNENA_CPANEL_LABEL_CLEANUP_IP_DESC', 'user-secret'],
        ['diagnostics', 'COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS', 'COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS_DESC', 'stethoscope'],
        ['setupsocials', 'COM_KUNENA_DIAGNOSTICS_LABEL_SETUPSOCIALS', 'COM_KUNENA_DIAGNOSTICS_LABEL_SETUPSOCIALS_DESC', 'share-nodes'],
        ['prunefiles', 'COM_KUNENA_TOOLS_PRUNE_FILES', 'COM_KUNENA_TOOLS_PRUNE_FILES_DESC', 'broom'],
    ],
];

if (PluginHelper::isEnabled('system', 'modifysocials')) {
    $groups['COM_KUNENA_ADMIN_TOOLS_SYSTEM'][] = ['managesocials', 'COM_KUNENA_DIAGNOSTICS_LABEL_MODIFYSOCIALS', 'COM_KUNENA_DIAGNOSTICS_LABEL_MODIFYSOCIALS_DESC', 'pen-to-square'];
}

// The tools for the developers of Kunena, in their own section: only in an internal version (versionname INTERNAL of the
// package), never on a site which runs a release.
if (KunenaForum::isInternal()) {
    $groups['COM_KUNENA_ADMIN_TOOLS_DEVELOPMENT'] = [
        ['view=install', 'COM_KUNENA_GIT_INSTALL', 'COM_KUNENA_GIT_INSTALL_DESC', 'code-branch'],
        ['restoretokens', 'COM_KUNENA_TOOLS_TOKENS', 'COM_KUNENA_TOOLS_TOKENS_DESC', 'rotate-left'],
    ];
}

// The search of the tools and the tools used last. A full URL: the asset manager would look for a relative one in the media folder.
$this->getDocument()->getWebAssetManager()->registerAndUseScript(
    'kunena_admin_tools_index',
    Uri::base() . 'components/com_kunena/media/js/kunena-tools-index.js',
    ['version' => 'auto'],
    ['defer' => true]
);
?>

<div id="kunena" class="kd">
    <div class="kd-panel kd-tools-search">
        <label for="kd-tools-search" class="visually-hidden"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCH'); ?></label>
        <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
        <input type="search" class="form-control" id="kd-tools-search" autocomplete="off"
               placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_SEARCH')); ?>">
    </div>
    <p class="kd-form__hint" id="kd-tools-none" hidden><?php echo Text::_('COM_KUNENA_TOOLS_SEARCH_NONE'); ?></p>

    <?php // Filled by kunena-tools-index.js with the tools used last in this browser. ?>
    <section class="kd-section" id="kd-tools-recent" aria-labelledby="kd-tools-recent-title" hidden>
        <h2 id="kd-tools-recent-title" class="kd-section__title"><?php echo Text::_('COM_KUNENA_TOOLS_RECENT'); ?></h2>
        <div class="kd-cards"></div>
    </section>

    <?php $section = 0; ?>
    <?php foreach ($groups as $title => $tools) : ?>
        <section class="kd-section" aria-labelledby="kd-tools-<?php echo ++$section; ?>">
            <h2 id="kd-tools-<?php echo $section; ?>" class="kd-section__title"><?php echo Text::_($title); ?></h2>
            <div class="kd-cards">
                <?php foreach ($tools as [$layout, $label, $desc, $icon]) :
                    $query = str_starts_with($layout, 'view=') ? $layout : 'view=tools&layout=' . $layout; ?>
                    <a class="kd-card" href="<?php echo Route::_('index.php?option=com_kunena&' . $query); ?>" data-kd-tool="<?php echo $this->escape($layout); ?>">
                        <span class="kd-card__icon fa-solid fa-<?php echo $icon; ?>" aria-hidden="true"></span>
                        <span class="kd-card__title"><?php echo Text::_($label); ?></span>
                        <span class="kd-card__desc"><?php echo Text::_($desc); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
