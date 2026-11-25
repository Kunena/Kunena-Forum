<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      CPanel
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Administrator\Controller\CpanelController;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;
use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;
use Kunena\Forum\Libraries\Integration\KunenaPlugins;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\Version\KunenaVersion;

Factory::getApplication()->getDocument()->getWebAssetManager()
    ->registerAndUseStyle('kunena_dashboard', Uri::base() . 'components/com_kunena/media/css/dashboard.css', ['version' => 'auto']);

// Is there a newer Kunena release, or are there updates for the Kunena add-ons?
$updateInfo = CpanelController::getUpdateInfo();
$newVersion = \is_object($updateInfo) && !empty($updateInfo->version) && version_compare(KunenaForum::version(), $updateInfo->version, '<') ? $updateInfo->version : null;
$newAddons  = \is_object($updateInfo) ? (int) $updateInfo->addons : 0;
$hasUpdate  = $newVersion !== null || $newAddons > 0;
$updateUrl  = 'index.php?option=com_installer&view=update&filter_search=kunena';

// When did Joomla last ask the Kunena update servers? Shown in the time zone of the user.
$lastCheck = CpanelController::getLastUpdateCheck();

if (!$lastCheck) {
    $lastCheckText = Text::_('COM_KUNENA_CPANEL_LABEL_LAST_UPDATE_CHECK_NEVER');
    $lastCheckFull = '';
} else {
    $lastCheckDay  = HTMLHelper::_('date', $lastCheck, 'Y-m-d');
    $lastCheckFull = HTMLHelper::_('date', $lastCheck, Text::_('DATE_FORMAT_LC6'));

    if ($lastCheckDay === HTMLHelper::_('date', 'now', 'Y-m-d')) {
        $lastCheckText = Text::_('COM_KUNENA_CPANEL_LABEL_LAST_UPDATE_CHECK_TODAY');
    } elseif ($lastCheckDay === HTMLHelper::_('date', '-1 day', 'Y-m-d')) {
        $lastCheckText = Text::_('COM_KUNENA_CPANEL_LABEL_LAST_UPDATE_CHECK_YESTERDAY');
    } else {
        $lastCheckText = HTMLHelper::_('date', $lastCheck, Text::_('DATE_FORMAT_LC4'));
    }
}

$canDo          = ContentHelper::getActions('com_kunena');
$canConfigure   = $canDo->get('core.admin') || $canDo->get('core.options');
$settingsUrl    = 'index.php?option=com_config&view=component&component=com_kunena';
$sampleChoice   = $_POST['sample_data_choice'] ?? null;
$hasSampleData  = KunenaForum::versionSampleData();
$askSampleData  = $sampleChoice === null && !$hasSampleData;
$offerSampleData = $sampleChoice === 'yes' && !$hasSampleData;

if ($sampleChoice === 'no' && Session::checkToken('post')) {
    $this->model->setSampleDataFlag(1);
    $sampleSkipped = true;
}

// The pages to manage the forum: [view, label, icon, count, description].
$manage = [
    ['categories', 'COM_KUNENA_CPANEL_LABEL_CATEGORIES', 'sitemap', $this->categoriesCount, 'COM_KUNENA_CPANEL_TILE_CATEGORIES_DESC'],
    ['users', 'COM_KUNENA_CPANEL_LABEL_USERS', 'users', KunenaUserHelper::getTotalCount(), 'COM_KUNENA_CPANEL_TILE_USERS_DESC'],
    ['ranks', 'COM_KUNENA_CPANEL_LABEL_RANKS', 'ranking-star', KunenaUserHelper::getTotalRanks(), 'COM_KUNENA_CPANEL_TILE_RANKS_DESC'],
    ['smilies', 'COM_KUNENA_CPANEL_LABEL_EMOTICONS', 'face-smile', KunenaStatistics::getTotalEmoticons(), 'COM_KUNENA_CPANEL_TILE_EMOTICONS_DESC'],
    ['templates', 'COM_KUNENA_CPANEL_LABEL_TEMPLATES', 'palette', round(\count(KunenaTemplate::getInstance()->getTemplatePaths()) / 2) - 1, 'COM_KUNENA_CPANEL_TILE_TEMPLATES_DESC'],
    ['plugins', 'COM_KUNENA_CPANEL_LABEL_PLUGINS', 'plug', KunenaPlugins::getTotalPlugins(), 'COM_KUNENA_CPANEL_TILE_PLUGINS_DESC'],
];

$maintenance = [
    ['attachments', 'COM_KUNENA_CPANEL_LABEL_FILES', 'paperclip', KunenaAttachmentHelper::getTotalAttachments(), 'COM_KUNENA_CPANEL_TILE_FILES_DESC'],
    ['trashs', 'COM_KUNENA_CPANEL_LABEL_TRASH', 'trash-can', $this->messagesTopicsInTrashBin, 'COM_KUNENA_CPANEL_TILE_TRASH_DESC'],
    ['mailsqueues', 'COM_KUNENA_CPANEL_LABEL_MAILQUEUES', 'envelope', $this->numberOfMailsqueues, 'COM_KUNENA_CPANEL_TILE_MAILQUEUES_DESC'],
    ['logs', 'COM_KUNENA_LOG_MANAGER', 'clipboard-list', $this->numberOfLogs, 'COM_KUNENA_CPANEL_TILE_LOGS_DESC'],
    ['stats', 'COM_KUNENA_STAT_FORUMSTATS', 'chart-column', null, 'COM_KUNENA_CPANEL_TILE_STATS_DESC'],
    ['tools', 'COM_KUNENA_CPANEL_LABEL_TOOLS', 'wrench', null, 'COM_KUNENA_CPANEL_TILE_TOOLS_DESC'],
];

// The tabs of the configuration: [tab, label, icon, description]. The tab opens from the hash of the link.
$settings = [
    ['general', 'COM_KUNENA_CPANEL_SETTINGS_GENERAL', 'sliders', 'COM_KUNENA_CPANEL_SETTINGS_GENERAL_DESC'],
    ['frontend', 'COM_KUNENA_A_FRONTEND', 'display', 'COM_KUNENA_CPANEL_SETTINGS_FRONTEND_DESC'],
    ['users', 'COM_KUNENA_A_USERS', 'user-gear', 'COM_KUNENA_CPANEL_SETTINGS_USERS_DESC'],
    ['emails', 'COM_KUNENA_SUBSCRIPTIONS', 'bell', 'COM_KUNENA_CPANEL_SETTINGS_EMAILS_DESC'],
    ['security', 'COM_KUNENA_A_SECURITY', 'shield-halved', 'COM_KUNENA_CPANEL_SETTINGS_SECURITY_DESC'],
    ['avatars', 'COM_KUNENA_A_AVATARS', 'circle-user', 'COM_KUNENA_CPANEL_SETTINGS_AVATARS_DESC'],
    ['uploads', 'COM_KUNENA_A_UPLOADS', 'cloud-arrow-up', 'COM_KUNENA_CPANEL_SETTINGS_UPLOADS_DESC'],
    ['ranking', 'COM_KUNENA_A_RANKING', 'medal', 'COM_KUNENA_CPANEL_SETTINGS_RANKING_DESC'],
    ['bbcode', 'COM_KUNENA_A_BBCODE', 'pen-to-square', 'COM_KUNENA_CPANEL_SETTINGS_BBCODE_DESC'],
    ['rss', 'COM_KUNENA_ADMIN_RSS', 'rss', 'COM_KUNENA_CPANEL_SETTINGS_RSS_DESC'],
    ['extra', 'COM_KUNENA_ADMIN_CONFIG_EXTRA', 'square-poll-vertical', 'COM_KUNENA_CPANEL_SETTINGS_EXTRA_DESC'],
    ['menu', 'COM_KUNENA_ADMIN_MENU_SETTINGS', 'bars', 'COM_KUNENA_CPANEL_SETTINGS_MENU_DESC'],
    ['permissions', 'JCONFIG_PERMISSIONS_LABEL', 'key', 'COM_KUNENA_CPANEL_SETTINGS_PERMISSIONS_DESC'],
];

$tile = function (array $item): void {
    [$view, $label, $icon, $count, $desc] = $item; ?>
    <a class="kd-tile" href="<?php echo Route::_('index.php?option=com_kunena&view=' . $view); ?>">
        <span class="kd-tile__icon fa-solid fa-<?php echo $icon; ?>" aria-hidden="true"></span>
        <span class="kd-tile__body">
            <span class="kd-tile__title"><?php echo Text::_($label); ?></span>
            <span class="kd-tile__desc"><?php echo Text::_($desc); ?></span>
        </span>
        <?php if ($count !== null) : ?>
            <span class="kd-tile__count"><?php echo $this->escape((string) $count); ?></span>
        <?php endif; ?>
    </a>
<?php };
?>

<div id="kunena" class="kd">

    <section class="kd-hero" aria-labelledby="kd-hero-title">
        <div class="kd-hero__brand">
            <span class="kd-hero__logo" aria-hidden="true"><?php echo KunenaSvgIcons::loadsvg('kunenalogo'); ?></span>
            <div>
                <h2 id="kd-hero-title" class="kd-hero__title">Kunena <?php echo $this->escape(KunenaForum::version()); ?></h2>
                <p class="kd-hero__meta">
                    <?php echo $this->escape(KunenaForum::versionDate()); ?>
                    <span aria-hidden="true">·</span>
                    <span<?php echo $lastCheckFull ? ' title="' . $this->escape($lastCheckFull) . '"' : ''; ?>>
                        <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_LAST_UPDATE_CHECK') . ' ' . $this->escape($lastCheckText); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="kd-hero__status">
            <?php if ($hasUpdate) : ?>
                <a class="kd-pill kd-pill--warn" href="<?php echo $updateUrl; ?>">
                    <span class="fa-solid fa-circle-arrow-up" aria-hidden="true"></span>
                    <?php echo $newVersion !== null ? Text::sprintf('COM_KUNENA_CPANEL_UPDATE_AVAILABLE', $this->escape($newVersion)) : Text::sprintf('COM_KUNENA_CPANEL_UPDATE_ADDONS_AVAILABLE', $newAddons); ?>
                </a>
            <?php else : ?>
                <span class="kd-pill kd-pill--ok">
                    <span class="fa-solid fa-circle-check" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_CPANEL_UP_TO_DATE'); ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Uri::root(); ?>index.php?option=com_kunena" target="_blank" rel="noopener">
                <?php echo Text::_('COM_KUNENA_CPANEL_VIEW_FORUM'); ?>
            </a>
            <?php if ($canConfigure) : ?>
                <a class="kd-btn kd-btn--primary" href="<?php echo Route::_($settingsUrl); ?>">
                    <span class="fa-solid fa-gear" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_CPANEL_ALL_SETTINGS'); ?>
                </a>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($sampleSkipped)) : ?>
        <div class="alert alert-success"><?php echo Text::_('COM_KUNENA_CPANEL_SAMPLE_DATA_SKIPPED'); ?></div>
    <?php endif; ?>

    <?php if ($askSampleData || $offerSampleData || !$this->KunenaMenusExists) : ?>
        <section class="kd-section" aria-labelledby="kd-start-title">
            <h3 id="kd-start-title" class="kd-section__title"><?php echo Text::_('COM_KUNENA_CPANEL_SECTION_GETSTARTED'); ?></h3>
            <div class="kd-steps">
                <?php if ($askSampleData) : ?>
                    <form method="post" class="kd-step">
                        <?php echo HTMLHelper::_('form.token'); ?>
                        <span class="kd-step__icon fa-solid fa-database" aria-hidden="true"></span>
                        <div class="kd-step__body">
                            <strong><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_SAMPLE_DATA'); ?></strong>
                            <span><?php echo Text::_('COM_KUNENA_CPANEL_SAMPLE_DATA_QUESTION'); ?></span>
                        </div>
                        <div class="kd-step__actions">
                            <button type="submit" name="sample_data_choice" value="yes" class="kd-btn kd-btn--primary"><?php echo Text::_('JYES'); ?></button>
                            <button type="submit" name="sample_data_choice" value="no" class="kd-btn kd-btn--ghost"><?php echo Text::_('JNO'); ?></button>
                        </div>
                    </form>
                <?php elseif ($offerSampleData) : ?>
                    <div id="sample-data-wrapper" class="kd-step">
                        <span class="kd-step__icon fa-solid fa-database" aria-hidden="true"></span>
                        <div class="kd-step__body">
                            <strong><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_KUNENA_FORUM_SAMPLE_DATA'); ?></strong>
                            <span><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_INSTALL_SAMPLE_DATA'); ?></span>
                            <div class="sampledata-progress-kunena d-none">
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                                </div>
                            </div>
                        </div>
                        <div class="kd-step__actions">
                            <button type="button" class="kd-btn kd-btn--primary apply-sample-data" data-type="kunena" data-steps="1">
                                <span class="fa-solid fa-download" aria-hidden="true"></span>
                                <?php echo Text::_('COM_KUNENA_CPANEL_BUTTON_INSTALL'); ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$this->KunenaMenusExists) : ?>
                    <div class="kd-step">
                        <span class="kd-step__icon fa-solid fa-bars" aria-hidden="true"></span>
                        <div class="kd-step__body">
                            <strong><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_KUNENA_MENU'); ?></strong>
                            <span><?php echo Text::_('COM_KUNENA_CPANEL_MENU_MISSING_DESC'); ?></span>
                        </div>
                        <div class="kd-step__actions">
                            <a class="kd-btn kd-btn--primary" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=menu'); ?>">
                                <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_INSTALL_MENUS'); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="kd-stats" aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_CPANEL_SECTION_OVERVIEW')); ?>">
        <div class="kd-stat">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_USERS'); ?></span>
            <span class="kd-stat__value"><?php echo (int) KunenaUserHelper::getTotalCount(); ?></span>
            <span class="kd-stat__hint"><?php echo Text::_('COM_KUNENA_CPANEL_LAST_REGISTERED'); ?> <?php echo $this->lastUserRegisteredDate; ?></span>
        </div>
        <div class="kd-stat">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CATEGORIES'); ?></span>
            <span class="kd-stat__value"><?php echo $this->escape($this->categoriesCount); ?></span>
            <span class="kd-stat__hint"><?php echo Text::_('COM_KUNENA_CPANEL_SECTIONS_CATEGORIES'); ?></span>
        </div>
        <div class="kd-stat">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_FILES'); ?></span>
            <span class="kd-stat__value"><?php echo (int) KunenaAttachmentHelper::getTotalAttachments(); ?></span>
            <span class="kd-stat__hint"><?php echo Text::_('COM_KUNENA_CPANEL_FILES_HINT'); ?></span>
        </div>
        <div class="kd-stat<?php echo $this->numberOfMailsqueues ? ' kd-stat--attention' : ''; ?>">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_MAILQUEUES'); ?></span>
            <span class="kd-stat__value"><?php echo (int) $this->numberOfMailsqueues; ?></span>
            <span class="kd-stat__hint"><?php echo Text::_('COM_KUNENA_CPANEL_MAILQUEUE_HINT'); ?></span>
        </div>
    </section>

    <div class="kd-columns">
        <section class="kd-section" aria-labelledby="kd-manage-title">
            <h3 id="kd-manage-title" class="kd-section__title"><?php echo Text::_('COM_KUNENA_CPANEL_SECTION_MANAGE'); ?></h3>
            <div class="kd-tiles">
                <?php foreach ($manage as $item) {
                    $tile($item);
                } ?>
            </div>
        </section>

        <section class="kd-section" aria-labelledby="kd-maintenance-title">
            <h3 id="kd-maintenance-title" class="kd-section__title"><?php echo Text::_('COM_KUNENA_CPANEL_SECTION_MAINTENANCE'); ?></h3>
            <div class="kd-tiles">
                <?php foreach ($maintenance as $item) {
                    $tile($item);
                } ?>
            </div>
        </section>
    </div>

    <?php if ($canConfigure) : ?>
        <section class="kd-section" aria-labelledby="kd-settings-title">
            <div class="kd-section__head">
                <div>
                    <h3 id="kd-settings-title" class="kd-section__title"><?php echo Text::_('COM_KUNENA_CPANEL_SECTION_SETTINGS'); ?></h3>
                    <p class="kd-section__desc"><?php echo Text::_('COM_KUNENA_CPANEL_SECTION_SETTINGS_DESC'); ?></p>
                </div>
                <label class="kd-search">
                    <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                    <span class="visually-hidden"><?php echo Text::_('COM_KUNENA_CPANEL_SETTINGS_FILTER'); ?></span>
                    <input type="search" id="kd-settings-filter" placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_CPANEL_SETTINGS_FILTER')); ?>" autocomplete="off">
                </label>
            </div>
            <div class="kd-settings" id="kd-settings">
                <?php foreach ($settings as [$tab, $label, $icon, $desc]) : ?>
                    <a class="kd-setting" href="<?php echo Route::_($settingsUrl) . '#' . $tab; ?>">
                        <span class="kd-setting__icon fa-solid fa-<?php echo $icon; ?>" aria-hidden="true"></span>
                        <span class="kd-setting__title"><?php echo Text::_($label); ?></span>
                        <span class="kd-setting__desc"><?php echo Text::_($desc); ?></span>
                    </a>
                <?php endforeach; ?>
                <p class="kd-settings__empty" hidden><?php echo Text::_('COM_KUNENA_CPANEL_SETTINGS_NO_MATCH'); ?></p>
            </div>
        </section>
        <script>
            (function () {
                var input = document.getElementById('kd-settings-filter');
                var items = document.querySelectorAll('#kd-settings .kd-setting');
                var empty = document.querySelector('#kd-settings .kd-settings__empty');

                input.addEventListener('input', function () {
                    var query = input.value.trim().toLowerCase();
                    var shown = 0;

                    items.forEach(function (item) {
                        var match = !query || item.textContent.toLowerCase().indexOf(query) > -1;
                        item.hidden = !match;
                        shown += match ? 1 : 0;
                    });

                    empty.hidden = shown > 0;
                });
            })();
        </script>
    <?php endif; ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
