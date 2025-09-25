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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;
use Kunena\Forum\Libraries\Icons\KunenaSvgIcons;
use Kunena\Forum\Libraries\Integration\KunenaPlugins;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\Version\KunenaVersion;

?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <?php
            function setSampleDataFlag($value = 1)
            {
                $db = Factory::getContainer()->get('DatabaseDriver');
                $query = $db->getQuery(true);

                $query->update($db->quoteName('#__kunena_version'))
                    ->set($db->quoteName('sampleData') . ' = ' . $db->quote($value));

                $db->setQuery($query);

                try {
                    return $db->execute();
                } catch (Exception $e) {
                    return false;
                }
            }

            if (!isset($_POST['sample_data_choice']) && !KunenaForum::versionSampleData()) : ?>
                <div class="row clearfix">
                    <div class="col-xl-3 col-md-3">
                        <div class="card proj-t-card bg-warning">
                            <div class="card-body">
                                <div class="row align-items-center mb-30">
                                    <div class="col-auto">
                                        <i class="fas fa-database text-white f-30"></i>
                                    </div>
                                    <div class="col pl-0">
                                        <h6 class="mb-0 text-white"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_INSTALL'); ?></h6>
                                        <h6 class="mb-0 text-white"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_SAMPLE_DATA'); ?></h6>
                                    </div>
                                </div>
                                <div>
                                    <form method="post" id="sampleDataForm">
                                        <ul id="sample-data-wrapper" class="list-group list-group-flush sample-data">
                                            <li class="list-group-item sampledata-kunena">
                                                <div class="d-flex flex-column">
                                                    <div class="sample-data__title mb-3">
                                                        <?php echo Text::_('COM_KUNENA_CPANEL_SAMPLE_DATA_QUESTION'); ?>
                                                    </div>
                                                    <div class="btn-group justify-content-center">
                                                        <button type="submit" name="sample_data_choice" value="yes" class="btn btn-secondary btn-sm mx-1">
                                                            <?php echo Text::_('JYES'); ?>
                                                        </button>
                                                        <button type="submit" name="sample_data_choice" value="no" class="btn btn-secondary btn-sm mx-1">
                                                            <?php echo Text::_('JNO'); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                                <h6 class="pt-badge bg-cyan"><i class="fas fa-exclamation text-white f-18"></i></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else :
                if (isset($_POST['sample_data_choice']) && $_POST['sample_data_choice'] === 'no') {
                    setSampleDataFlag(1);
                    echo '<div class="alert alert-success">' . Text::_('COM_KUNENA_CPANEL_SAMPLE_DATA_SKIPPED') . '</div>';
                } elseif (isset($_POST['sample_data_choice']) && $_POST['sample_data_choice'] === 'yes') {
                    if (!KunenaForum::versionSampleData()) : ?>
                        <div class="row clearfix">
                            <div class="col-xl-3 col-md-3">
                                <div class="card proj-t-card bg-warning">
                                    <div class="card-body">
                                        <div class="row align-items-center mb-30">
                                            <div class="col-auto">
                                                <i class="fas fa-database text-white f-30"></i>
                                            </div>
                                            <div class="col pl-0">
                                                <h6 class="mb-0 text-white"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_INSTALL'); ?></h6>
                                                <h6 class="mb-0 text-white"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_SAMPLE_DATA'); ?></h6>
                                            </div>
                                        </div>
                                        <div>
                                            <ul id="sample-data-wrapper" class="list-group list-group-flush sample-data">
                                                <li class="list-group-item sampledata-kunena">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="sample-data__title me-2">
                                                            <span class="sample-data__icon icon-comments me-1" aria-hidden="true"></span>
                                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_KUNENA_FORUM_SAMPLE_DATA'); ?>
                                                        </div>
                                                        <button type="button"
                                                            class="btn btn-secondary btn-sm apply-sample-data"
                                                            data-type="kunena"
                                                            data-steps="1">
                                                            <span class="icon-upload" aria-hidden="true"></span>
                                                            <?php echo Text::_('COM_KUNENA_CPANEL_BUTTON_INSTALL'); ?>
                                                        </button>
                                                    </div>
                                                    <p class="sample-data__desc small mt-1">
                                                        <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_INSTALL_SAMPLE_DATA') ?>
                                                    </p>
                                                </li>
                                                <li class="list-group-item sampledata-progress-kunena d-none">
                                                    <div class="progress mb-3">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


            <?php endif;
                }
            endif; ?>
            <?php if (!$this->KunenaMenusExists) : ?>
                <div class="row clearfix">
                    <div class="col-xl-3 col-md-3">
                        <div class="card proj-t-card bg-warning">
                            <div class="card-body">
                                <div class="row align-items-center mb-30">
                                    <div class="col-auto">
                                        <i class="fas fa-database text-white f-30"></i>
                                    </div>
                                    <div class="col pl-0">
                                        <h6 class="mb-0 text-white"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_INSTALL'); ?></h6>
                                        <h6 class="mb-0 text-white"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_KUNENA_MENU'); ?></h6>
                                    </div>
                                </div>
                                <div>
                                    <ul class="list-group list-group-flush sample-data">
                                        <li class="list-group-item sampledata-kunena">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="sample-data__title me-2">
                                                    <span class="sample-data__icon icon-comments me-1" aria-hidden="true"></span>
                                                    <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_KUNENA_MENU') ?>
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-sm apply-sample-data" data-type="kunena" data-steps="1">
                                                    <span class="icon-upload" aria-hidden="true"></span> <a href="<?php echo Uri::base() ?>index.php?option=com_kunena&view=tools&layout=menu"><?php echo Text::_('COM_KUNENA_CPANEL_BUTTON_INSTALL') ?></a>
                                                    <span class="visually-hidden"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_KUNENA_MENU') ?></span>
                                                </button>
                                            </div>
                                            <p class="sample-data__desc small mt-1"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_INSTALL_MENUS'); ?></p>
                                        </li>
                                        <li class="list-group-item sampledata-progress-kunena d-none">
                                            <div class="progress mb-3">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <h6 class="pt-badge bg-cyan"><i class="fas fa-exclamation text-white f-18"></i></h6>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row clearfix">
                <!--                <div class="col-xl-4 col-md-12">-->
                <!--                    <div class="card proj-t-card">-->
                <!--                        <div class="card-body">-->
                <!--                            <div class="row align-items-center mb-30">-->
                <!--                                <div class="col-auto">-->
                <!--                                    <i class="fas fa-eye text-cyan f-30"></i>-->
                <!--                                </div>-->
                <!--                                <div class="col pl-0">-->
                <!--                                    <h6 class="mb-0">Ticket Answered</h6>-->
                <!--                                    <h6 class="mb-0 text-cyan">Live Update</h6>-->
                <!--                                </div>-->
                <!--                            </div>-->
                <!--                            <div class="row align-items-center text-center">-->
                <!--                                <div class="col">-->
                <!--                                    <h6 class="mb-0">327</h6></div>-->
                <!--                                <div class="col"><i class="fas fa-exchange-alt text-cyan f-18"></i></div>-->
                <!--                                <div class="col">-->
                <!--                                    <h6 class="mb-0">10 Days</h6></div>-->
                <!--                            </div>-->
                <!--                            <h6 class="pt-badge bg-cyan"><i class="fas fa-exclamation text-white f-18"></i></h6>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <div class="col-xl-12 col-md-12">
                    <div class="card proj-t-card">
                        <div class="card-body">
                            <div class="row align-items-center mb-30">
                                <div class="col-auto">
                                    <i class="fas fa-cloud-download-alt text-cyan f-30"></i>
                                </div>
                                <div class="col pl-0">
                                    <h6 class="mb-0"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_KUNENA_VERSION_CHECK'); ?></h6>
                                    <h6 class="mb-0 text-cyan"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_LAST_UPDATE_CHECK'); ?> <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_LAST_UPDATE_CHECK_TODAY'); ?></h6>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col pl-5">
                                    <?php echo KunenaSvgIcons::loadsvg('kunenalogo'); ?>
                                </div>
                                <div class="col">
                                    <h6 class="mb-0"><?php echo strtoupper(KunenaForum::version()); ?></h6>
                                    <h6 class="mb-0 text-cyan"><?php echo strtoupper(KunenaForum::versionDate()); ?></h6>
                                </div>
                            </div>
                            <h6 class="pt-badge bg-cyan"><i class="fas fa-check text-white f-18"></i></h6>
                        </div>
                    </div>
                </div>
                <!--                <div class="col-xl-4 col-md-6">-->
                <!--                    <div class="card proj-t-card">-->
                <!--                        <div class="card-body">-->
                <!--                            <div class="row align-items-center mb-30">-->
                <!--                                <div class="col-auto">-->
                <!--                                    <i class="fas fa-lightbulb text-cyan f-30"></i>-->
                <!--                                </div>-->
                <!--                                <div class="col pl-0">-->
                <!--                                    <h6 class="mb-0">Unique Innovation</h6>-->
                <!--                                    <h6 class="mb-0 text-cyan">Last Check</h6>-->
                <!--                                </div>-->
                <!--                            </div>-->
                <!--                            <div class="row align-items-center text-center">-->
                <!--                                <div class="col">-->
                <!--                                    <h6 class="mb-0">Today</h6></div>-->
                <!--                                <div class="col"><i class="fas fa-exchange-alt text-cyan f-18"></i></div>-->
                <!--                                <div class="col">-->
                <!--                                    <h6 class="mb-0">248</h6></div>-->
                <!--                            </div>-->
                <!--                            <h6 class="pt-badge bg-cyan">73%</h6>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->

                <div class="col-xl-3 col-md-12">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=categories'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CATEGORIES') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan">
                                        <?php echo $this->categoriesCount; ?>
                                    </h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list-alt bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=users'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_USERS') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan"><?php echo KunenaUserHelper::getTotalCount(); ?></h3>
                                    <p class="mb-0"><?php echo $this->lastUserRegisteredDate; ?></p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=attachments'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_FILES') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan"><?php echo KunenaAttachmentHelper::getTotalAttachments(); ?></h3>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-photo-video bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=smilies'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_EMOTICONS') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan"><?php echo KunenaStatistics::getTotalEmoticons() ?></h3>
                                    <p class="mb-0"><?php echo Text::_('COM_KUNENA_EDITOR_SMILIES') ?></p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-grin-squint-tears bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-12">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_config&view=component&component=com_kunena'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CONFIG') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan">&nbsp;</h3>
                                    <p class="mb-0">&nbsp;</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-edit bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=stats');
                                                    ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_STAT_FORUMSTATS')
                                            ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan">&nbsp;</h3>
                                    <p class="mb-0">&nbsp;</p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-bar bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=templates'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_TEMPLATES') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan"><?php echo round(count(KunenaTemplate::getInstance()->getTemplatePaths()) / 2) - 1; ?></h3>
                                    <p class="mb-0"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_TEMPLATES_INSTALLED'); ?></p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-palette bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=ranks'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_RANKS') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan"><?php echo KunenaUserHelper::getTotalRanks(); ?></h3>
                                    <p class="mb-0"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_RANKS') ?></p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bars bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-12">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=plugins'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_PLUGINS') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan"><?php echo KunenaPlugins::getTotalPlugins(); ?></h3>
                                    <p class="mb-0"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_PLUGINS') ?></p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-plug bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=trashs'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_TRASH') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan"><?php echo $this->messagesTopicsInTrashBin; ?></h3>
                                    <p class="mb-0"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_ITEMS'); ?></p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-trash-alt bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=logs'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_LOG_MANAGER') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan"><?php echo $this->numberOfLogs; ?></h3>
                                    <p class="mb-0"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_ITEMS'); ?></p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card proj-t-card comp-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-25">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools'); ?>" class="stretched-link">
                                            <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_TOOLS') ?>
                                        </a>
                                    </h6>
                                    <h3 class="fw-700 text-cyan">9</h3>
                                    <p class="mb-0"><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_ITEMS'); ?></p>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tools bg-cyan"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>