<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Config
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

HTMLHelper::_('bootstrap.framework');

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');
?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config'); ?>"
                      method="post"
                      id="adminForm" name="adminForm">
                    <input type="hidden" name="view" value="config"/>
                    <input type="hidden" name="task" value=""/>
					<?php echo HTMLHelper::_('form.token') ?>

                    <article class="data-block">
                        <div class="data-container">
                            <div class="tabbable-panel">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="tab_basics-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_basics" type="button" role="tab"
                                                    aria-controls="tab_basics"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_BASICS'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_frontend-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_frontend" type="button" role="tab"
                                                    aria-controls="tab_frontend"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_FRONTEND'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_users-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_users" type="button" role="tab"
                                                    aria-controls="tab_users"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_USERS'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_emails-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_emails" type="button" role="tab"
                                                    aria-controls="tab_emails"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_SUBSCRIPTIONS'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_security-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_security" type="button" role="tab"
                                                    aria-controls="tab_security"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_SECURITY'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_avatars-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_avatars" type="button" role="tab"
                                                    aria-controls="tab_avatars"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_AVATARS'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_uploads-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_uploads" type="button" role="tab"
                                                    aria-controls="tab_uploads"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_UPLOADS'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_ranking-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_ranking" type="button" role="tab"
                                                    aria-controls="tab_ranking"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_RANKING'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_bbcode-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_bbcode" type="button" role="tab"
                                                    aria-controls="tab_bbcode"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_BBCODE'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_rss-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_rss" type="button" role="tab"
                                                    aria-controls="tab_rss"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_ADMIN_RSS'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_extra-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_extra" type="button" role="tab"
                                                    aria-controls="tab_extra"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_EXTRA'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab_menu-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab_menu" type="button" role="tab"
                                                    aria-controls="tab_menu"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_ADMIN_MENU_SETTINGS'); ?></button>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_basics">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_BASIC_SETTINGS') ?></legend>

                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('boardTitle') != 'Kunena')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_BOARD_TITLE') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_boardTitle"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('boardTitle')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_BOARD_TITLE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('email') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_EMAIL') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control" name="cfg_email"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('email')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_EMAIL_DESC2') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('email_sender_name') != '') : echo 'class="changed"'; endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_EMAIL_SENDER_NAME') ?></td>
                                                        <td>
                                                            <input type="text" name="cfg_email_sender_name"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('email_sender_name')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_EMAIL_SENDER_NAME_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('sendEmails') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SEND_EMAILS') ?></td>
                                                        <td><?php echo $this->lists ['sendEmails'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SEND_EMAILS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('boardOffline') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_BOARD_OFFLINE') ?></td>
                                                        <td><?php echo $this->lists ['boardOffline'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_BOARD_OFFLINE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->escape($this->config->getValueValue('offlineMessage')) != 'The Forum is currently offline for maintenance.
                                                                                                                                                                                                                                                                                                                Check back soon!')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_BOARD_OFFLINE_MES') ?></td>
                                                        <td>
														<textarea name="cfg_offlineMessage" rows="3"
                                                                  cols="50"><?php echo $this->escape($this->config->getValueValue('offlineMessage')) ?></textarea>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_BOARD_OFFLINE_MES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('readOnly') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_BOARD_READONLY') ?></td>
                                                        <td><?php echo $this->lists ['readOnly'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_BOARD_READONLY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->escape($this->config->getValueValue('sessionTimeOut')) != 1800)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_KUNENA_SESSION_TIMEOUT') ?>
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                   name="cfg_sessionTimeOut"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('sessionTimeOut'));
														           ?>"/></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_KUNENA_SESSION_TIMEOUT_DESC') ?>
                                                        </td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('enableRss') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS') ?></td>
                                                        <td><?php echo $this->lists ['enableRss'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('debug') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_DEBUG_MODE') ?></td>
                                                        <td><?php echo $this->lists ['debug'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_DEBUG_MODE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('profiler') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PROFILER') ?></td>
                                                        <td><?php echo $this->lists ['profiler'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PROFILER_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('fallbackEnglish') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_FALLBACK_ENGLISH') ?></td>
                                                        <td><?php echo $this->lists ['fallbackEnglish'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_FALLBACK_ENGLISH_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('timeToCreatePage') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_TIME_TO_CREATEPAGE') ?></td>
                                                        <td><?php echo $this->lists ['timeToCreatePage'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_TIME_TO_CREATEPAGE_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>

                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_SEO_SETTINGS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('sef') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SEF') ?></td>
                                                        <td><?php echo $this->lists ['sef'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SEF_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('accessComponent') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_ACCESS_COMPONENT') ?></td>
                                                        <td><?php echo $this->lists ['accessComponent'] ?></td>
                                                        <td><?php echo Text::sprintf('COM_KUNENA_CFG_ACCESS_COMPONENT_DESC', $this->lists ['componentUrl'], Text::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND')) ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('sefRedirect') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SEF_REDIRECT') ?></td>
                                                        <td><?php echo $this->lists ['sefRedirect'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SEF_REDIRECT_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>

                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_CACHING_SETTINGS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('cache') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_CACHE') ?></td>
                                                        <td><?php echo $this->lists ['cache'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_CACHE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('cacheTime') != 60)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_CACHE_TIME') ?></td>
                                                        <td><?php echo $this->lists ['cacheTime'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_CACHE_TIME_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_frontend">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_LOOKS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('threadsPerPage') != 20)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_THREADS') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_threadsPerPage"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('threadsPerPage')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_THREADS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('messagesPerPage') != 6)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_MESSAGES') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_messagesPerPage"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('messagesPerPage')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_MESSAGES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('messagesPerPageSearch') != 15)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_MESSAGES_SEARCH') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_messagesPerPageSearch"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('messagesPerPageSearch')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_MESSAGES_DESC_SEARCH') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showHistory') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HISTORY') ?></td>
                                                        <td><?php echo $this->lists ['showHistory'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HISTORY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('historyLimit') != 6)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HISTLIM') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_historyLimit"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('historyLimit')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HISTLIM_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('postDateFormat') != 'ago')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_POST_DATEFORMAT') ?></td>
                                                        <td><?php echo $this->lists ['postDateFormat'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_POST_DATEFORMAT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('postDateFormatHover') != 'datetime')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_POST_DATEFORMAT_HOVER') ?></td>
                                                        <td><?php echo $this->lists ['postDateFormatHover'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_POST_DATEFORMAT_HOVER_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showNew') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SHOWNEW') ?></td>
                                                        <td><?php echo $this->lists ['showNew'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SHOWNEW_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showAnnouncement') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOW_ANNOUNCEMENT') ?></td>
                                                        <td><?php echo $this->lists ['showAnnouncement'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOW_ANNOUNCEMENT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('avatarOnCategory') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOW_AVATAR_ON_CAT') ?></td>
                                                        <td><?php echo $this->lists ['avatarOnCategory'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOW_AVATAR_ON_CAT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('listCatShowModerators') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_LISTCAT_SHOW_MODERATORS') ?></td>
                                                        <td><?php echo $this->lists ['listCatShowModerators'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_LISTCAT_SHOW_MODERATORS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showChildCatIcon') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST') ?></td>
                                                        <td><?php echo $this->lists ['showChildCatIcon'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('enableForumJump') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FORUM_JUMP') ?></td>
                                                        <td><?php echo $this->lists ['enableForumJump'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FORUM_JUMP_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('reportMsg') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_REPORT') ?></td>
                                                        <td><?php echo $this->lists ['reportMsg'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_REPORT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('orderingSystem') != 'mesid')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_ORDERING_SYSTEM') ?></td>
                                                        <td><?php echo $this->lists ['orderingSystem'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_REPORT_ORDERING_SYSTEM_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('hideIp') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_HIDE_IP') ?></td>
                                                        <td><?php echo $this->lists ['hideIp'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_HIDE_IP_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('latestCategoryIn') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_LATESTCATEGORY_IN') ?></td>
                                                        <td><?php echo $this->lists ['latestCategoryIn'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_LATESTCATEGORY_IN_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('latestCategory') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_LATESTCATEGORY') ?></td>
                                                        <td><?php echo $this->lists ['latestCategory'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_LATESTCATEGORY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('topicIcons') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_TOPICICONS') ?></td>
                                                        <td><?php echo $this->lists ['topicIcons'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_TOPCIICONS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('lightbox') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_ENABLELIGHTBOX') ?></td>
                                                        <td><?php echo $this->lists ['lightbox'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_ENABLELIGHTBOX_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showListTime') != 720)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_SHOW_TOPICS_FROM_LAST_TIME') ?></td>
                                                        <td><?php echo $this->lists ['showListTime'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_SHOW_TOPICS_FROM_LAST_TIME_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('topicLayout') != 'flat')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_TOPIC_LAYOUT') ?></td>
                                                        <td><?php echo $this->lists ['topicLayout'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('pickupCategory') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_PICKUP_CATEGORY') ?></td>
                                                        <td><?php echo $this->lists ['pickupCategory'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_PICKUP_CATEGORY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('articleDisplay') != 'intro')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_ARTICLE_DISPLAY') ?></td>
                                                        <td><?php echo $this->lists ['articleDisplay'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_ARTICLE_DISPLAY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('defaultSort') != 'asc')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_DEFAULT_SORT') ?></td>
                                                        <td><?php echo $this->lists ['defaultSort'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_DEFAULT_SORT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('searchTime') != 365)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_SEARCH_TIME') ?></td>
                                                        <td><?php echo $this->lists ['searchTime'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CFG_SEARCH_TIME_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('allowChangeSubject') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ALLOW_CHANGE_SUBJECT_REPLIES_LABEL') ?></td>
                                                        <td><?php echo $this->lists ['allowChangeSubject'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ALLOW_CHANGE_SUBJECT_REPLIES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('ratingEnabled') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="25%"><?php echo Text::_('COM_KUNENA_CONFIGURATION_RATING_ENABLED') ?></td>
                                                        <td align="left" valign="top"
                                                            width="25%"><?php echo $this->lists ['ratingEnabled'] ?></td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_CONFIGURATION_RATING_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('attachStart') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_ATTACHMENT_SHORTEN_NAME_START') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_attachStart"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('attachStart')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_ATTACHMENT_SHORTEN_NAME_START_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('attachEnd') != 14)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_ATTACHMENT_SHORTEN_NAME_END') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_attachEnd"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('attachEnd')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_ATTACHMENT_SHORTEN_NAME_END_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('quickReply') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_QUICK_REPLY') ?></td>
                                                        <td><?php echo $this->lists ['quickReply'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_QUICK_REPLY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('disableRe') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_DISABLE_RE') ?></td>
                                                        <td><?php echo $this->lists ['disableRe'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_DISABLE_RE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('display_filename_attachment') != 0) : echo 'class="changed"'; endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_DISPLAY_FILENAME_ATTACHMENT') ?></td>
                                                        <td><?php echo $this->lists ['display_filename_attachment'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_DISPLAY_FILENAME_ATTACHMENT_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_users">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_USER_RELATED') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('username') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_DISPLAY_NAME') ?></td>
                                                        <td><?php echo $this->lists ['username'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USERNAME_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('askEmail') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_ASK_EMAIL') ?></td>
                                                        <td><?php echo $this->lists ['askEmail'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_ASK_EMAIL_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showEmail') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SHOWMAIL') ?></td>
                                                        <td><?php echo $this->lists ['showEmail'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SHOWMAIL_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showUserStats') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USERSTATS') ?></td>
                                                        <td><?php echo $this->lists ['showUserStats'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USERSTATS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showKarma') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_KARMA') ?></td>
                                                        <td><?php echo $this->lists ['showKarma'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_KARMA_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showThankYou') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_THANKYOU') ?></td>
                                                        <td><?php echo $this->lists ['showThankYou'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_THANKYOU_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('thankYouMax') != 10)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_DISPLAY_THANKYOU_NUMBERS') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_thankYouMax"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('thankYouMax')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_DISPLAY_THANKYOU_NUMBERS') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userEdit') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_EDIT') ?></td>
                                                        <td><?php echo $this->lists ['userEdit'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userEditTime') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_TIME') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_userEditTime"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('userEditTime')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_TIME_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userEditTimeGrace') != 600)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_TIMEGRACE') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_userEditTimeGrace"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('userEditTimeGrace')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_TIMEGRACE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('editMarkup') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_MARKUP') ?></td>
                                                        <td><?php echo $this->lists ['editMarkup'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_MARKUP_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('allowFavorites') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FAVORITES') ?></td>
                                                        <td><?php echo $this->lists ['allowFavorites'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FAVORITES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('boxGhostMessage') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_GHOSTMESSAGE') ?></td>
                                                        <td><?php echo $this->lists ['boxGhostMessage'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_GHOSTMESSAGE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showBannedReason') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SHOWBANNEDREASON_PROFILE') ?></td>
                                                        <td><?php echo $this->lists ['showBannedReason'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SHOWBANNEDREASON_PROFILE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showImgFilesManageProfile') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_DISPLAY_IMGFILES_TAB_MANAGEMENT_PROFILE') ?></td>
                                                        <td><?php echo $this->lists ['showImgFilesManageProfile'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_DISPLAY_IMGFILES_TAB_MANAGEMENT_PROFILE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userReport') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_CAN_SEND_OWN_REPORT') ?></td>
                                                        <td><?php echo $this->lists ['userReport'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USER_CAN_SEND_OWN_REPORT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('logModeration') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_LOG_COLLECT_DATA') ?></td>
                                                        <td><?php echo $this->lists ['logModeration'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_LOG_COLLECT_DATA_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userStatus') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_USER_STATUS') ?></td>
                                                        <td><?php echo $this->lists ['userStatus'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_USER_STATUS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('signature') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SIGNATURES') ?></td>
                                                        <td><?php echo $this->lists ['signature'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SIGNATURES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('personal') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_PERSONAL') ?></td>
                                                        <td><?php echo $this->lists ['personal'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_PERSONAL_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('social') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SOCIAL') ?></td>
                                                        <td><?php echo $this->lists ['social'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SOCIAL_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('maxSig') != 300)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_SIGNATURE') ?></td>
                                                        <td align="left" valign="top"><input type="text"
                                                                                             class="form-control"
                                                                                             name="cfg_maxSig"
                                                                                             value="<?php echo $this->escape($this->config->getValueValue('maxSig')) ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_SIGNATURE_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>
                                        <div class="tab-pane" id="tab_emails">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_SUBSCRIPTIONS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('emailHeader') != 'media/kunena/email/hero-wide.png')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_EMAIL_HEADER') ?></td>
                                                        <td><input name="cfg_emailHeader" type="text"
                                                                   class="inputbox form-control"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('emailHeader')) ?>">
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_EMAIL_HEADER_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('emailHeadersizey') != 560)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_EMAIL_HEADER_SIZEY') ?></td>
                                                        <td><input name="cfg_emailHeaderY" type="text"
                                                                   class="inputbox form-control"
                                                                   value="">
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_EMAIL_HEADER_SIZEY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('emailHeadersizex') != 560)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_EMAIL_HEADER_SIZEX') ?></td>
                                                        <td><input name="cfg_emailHeaderX" type="text"
                                                                   class="inputbox form-control"
                                                                   value="">
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_EMAIL_HEADER_SIZEX_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('allowSubscriptions') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS') ?></td>
                                                        <td><?php echo $this->lists ['allowSubscriptions'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('mailFull') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_MAILFULL') ?></td>
                                                        <td><?php echo $this->lists ['mailFull'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_MAILFULL_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('plainEmail') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_PLAINEMAIL') ?></td>
                                                        <td><?php echo $this->lists ['plainEmail'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_PLAINEMAIL_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('categorySubscriptions') != 'post')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_SUBSCRIPTIONS') ?></td>
                                                        <td><?php echo $this->lists ['categorySubscriptions'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_SUBSCRIPTIONS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('topicSubscriptions') != 'every')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TOPIC_SUBSCRIPTIONS') ?></td>
                                                        <td><?php echo $this->lists ['topicSubscriptions'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TOPIC_SUBSCRIPTIONS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('subscriptionsChecked') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SUBSCRIPTIONSCHECKED') ?></td>
                                                        <td><?php echo $this->lists ['subscriptionsChecked'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SUBSCRIPTIONSCHECKED_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('emailRecipientCount') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_COUNT') ?></td>
                                                        <td><?php echo HTMLHelper::_('select.integerlist', 0, 100, 5, 'cfg_emailRecipientCount', null, $this->escape($this->config->getValueValue('emailRecipientCount'))) ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_COUNT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('emailRecipientPrivacy') != 'bcc')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY') ?></td>
                                                        <td><?php echo $this->lists ['emailRecipientPrivacy'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('emailVisibleAddress') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_VISIBLE_ADDRESS') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_emailVisibleAddress"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('emailVisibleAddress')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_VISIBLE_ADDRESS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('useSystemEmails') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USE_SYSTEM_EMAILS') ?></td>
                                                        <td><?php echo $this->lists ['useSystemEmails'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USE_SYSTEM_EMAILS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('utmSource') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USE_UTM_SOURCE') ?></td>
                                                        <td><?php echo $this->lists ['utmSource'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_USE_UTM_SOURCE_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_security">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_SECURITY_SETTINGS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('pubWrite') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_PUBWRITE') ?></td>
                                                        <td><?php echo $this->lists ['pubWrite'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_PUBWRITE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('holdGuestPosts') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HOLD_GUEST_POSTS') ?></td>
                                                        <td><?php echo $this->lists ['holdGuestPosts'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HOLD_GUEST_POSTS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistAllowed') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ALLOWED') ?></td>
                                                        <td><?php echo $this->lists ['userlistAllowed'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ALLOWED_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('pubProfile') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PUBPROFILE') ?></td>
                                                        <td><?php echo $this->lists ['pubProfile'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PUBPROFILE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('regOnly') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_REGISTERED_ONLY') ?></td>
                                                        <td><?php echo $this->lists ['regOnly'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_REG_ONLY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('holdNewUsersPosts') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HOLD_NEWUSERS_POSTS') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_holdNewUsersPosts"
                                                                   class="ksm-field"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('holdNewUsersPosts')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HOLD_NEWUSERS_POSTS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userDeleteMessage') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_DELETEMESSAGE') ?></td>
                                                        <td><?php echo $this->lists ['userDeleteMessage'] ?> </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_DELETEMESSAGE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('modSeeDeleted') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_MOD_SEE_DELETED') ?></td>
                                                        <td><?php echo $this->lists ['modSeeDeleted'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_MOD_SEE_DELETED_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('floodProtection') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FLOOD') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_floodProtection"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('floodProtection')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FLOOD_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('mailModerators') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_MAILMOD') ?></td>
                                                        <td><?php echo $this->lists ['mailModerators'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_MAILMOD_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('mailAdministrators') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_MAILADMIN') ?></td>
                                                        <td><?php echo $this->lists ['mailAdministrators'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_MAILADMIN_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('ipTracking') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IP_TRACKING') ?></td>
                                                        <td><?php echo $this->lists ['ipTracking'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IP_TRACKING_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('statsLinkAllowed') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_STATSLINK_ALLOWED') ?></td>
                                                        <td><?php echo $this->lists ['statsLinkAllowed'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_STATSLINK_ALLOWED_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('maxLinks') != 6)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MAX_LINKS') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control" name="cfg_maxLinks"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('maxLinks')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MAX_LINKS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('teaser') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_TEASER') ?></td>
                                                        <td><?php echo $this->lists ['teaser'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_TEASER_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('urlSubjectTopic') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_URL_SUBJECT') ?></td>
                                                        <td><?php echo $this->lists['urlSubjectTopic'] ?> </td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_URL_SUBJECT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('moderatorPermDelete') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MOD_PERDELETE') ?></td>
                                                        <td><?php echo $this->lists['moderatorPermDelete'] ?> </td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MOD_PERDELETE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('new_users_prevent_post_url_images') != 0) : echo 'class="changed"'; endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PREVENT_NEW_USERS_POST_URL_IMAGE') ?></td>
                                                        <td><?php echo $this->lists['new_users_prevent_post_url_images'] ?> </td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PREVENT_NEW_USERS_POST_URL_IMAGE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('minimal_user_posts_add_url_image') != 10) : echo 'class="changed"'; endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MINIMAL_NUMBER_OF_USER_POSTS_TO_ADD_URL_IMAGE') ?></td>
                                                        <td>
                                                            <input type="text"
                                                                   name="cfg_minimal_user_posts_add_url_image"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('minimal_user_posts_add_url_image')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MINIMAL_NUMBER_OF_USER_POSTS_TO_ADD_URL_IMAGE_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_CAPTCHA_CONFIGURATION') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('captcha') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_CAPTCHA_FOR_WHO_TITLE') ?></td>
                                                        <td><?php echo $this->lists ['captcha'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_CAPTCHA_FOR_WHO_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('captchaPostLimit') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CAPTCHA_POST_LIMIT') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_captchaPostLimit"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('captchaPostLimit')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CAPTCHA_POST_LIMIT_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_STOP_FORUM_SPAM_CONFIGURATION'); ?></legend>
                                                <table class="table table-striped">
                                                    <tr <?php if ($this->config->getValueValue('stopForumSpamKey') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="25%"><?php echo Text::_('COM_KUNENA_STOP_FORUM_SPAM_KEY') ?></td>
                                                        <td align="left" valign="top" width="25%">
                                                            <input type="text" class="form-control ksm-field-large"
                                                                   name="cfg_stopForumSpamKey"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('stopForumSpamKey')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_STOP_FORUM_SPAM_KEY_DESC') ?></td>
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_avatars">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_AVATAR_SETTINGS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('avatarType') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATARTYPE') ?></td>
                                                        <td><?php echo $this->lists ['avatarType'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATARTYPE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('defaultAvatar') != 'nophoto.png')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATAR_DEFAULT') ?></td>
                                                        <td><input type="text" class="form-control"
                                                                   name="cfg_defaultAvatar"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('defaultAvatar')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATAR_DEFAULT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('defaultAvatarSmall') != 's_nophoto.png')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATAR_DEFAULT_SMALL') ?></td>
                                                        <td><input type="text" class="form-control"
                                                                   name="cfg_defaultAvatarSmall"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('defaultAvatarSmall')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATAR_DEFAULT_SMALL_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('avatarTypes') != 'gif, jpeg, jpg, png')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_AVATAR_FILESALLOWED') ?></td>
                                                        <td><input type="text" class="form-control"
                                                                   name="cfg_avatarTypes"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('avatarTypes')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_AVATAR_FILESALLOWED_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('allowAvatarUpload') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATARUPLOAD') ?></td>
                                                        <td><?php echo $this->lists ['allowAvatarUpload'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATARUPLOAD_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('allowAvatarGallery') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATARGALLERY') ?></td>
                                                        <td><?php echo $this->lists ['allowAvatarGallery'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATARGALLERY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('avatarSize') != 2048)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVSIZE') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_avatarSize"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('avatarSize')) ?>"/>
                                                            kB
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('avatarQuality') != 75)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_AVATAR_QUALITY') ?></td>
                                                        <td class="nowrap">
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_avatarQuality"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('avatarQuality')) ?>"/>
                                                            %
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('avatarCrop') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_AVATAR_CROP') ?></td>
                                                        <td><?php echo $this->lists ['avatarCrop'] ?></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('avatarEdit') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATAREDIT') ?></td>
                                                        <td><?php echo $this->lists ['avatarEdit'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AVATAREDIT_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_uploads">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_ATTACHMENTS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('attachmentLimit') != 8)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_ATTACHMENT_LIMIT') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_attachmentLimit"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('attachmentLimit')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_ATTACHMENT_LIMIT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('attachmentProtection') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_ATTACHMENT_PROTECTION') ?></td>
                                                        <td><?php echo $this->lists ['attachmentProtection'] ?></td>
                                                        <td><?php echo Text::sprintf('COM_KUNENA_A_ATTACHMENT_PROTECTION_DESC', Uri::root(false) . 'media/kunena/attachments/image.png') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('attachmentUtf8') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_ATTACHMENT_FILENAME_UTF8') ?></td>
                                                        <td><?php echo $this->lists ['attachmentUtf8'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_ATTACHMENT_FILENAME_UTF8_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_IMAGE') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('imageUpload') != 'registered')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMAGE_UPLOAD') ?></td>
                                                        <td><?php echo $this->lists ['imageUpload'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMAGE_UPLOAD_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showImgForGuest') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOWIMGFORGUEST') ?></td>
                                                        <td><?php echo $this->lists ['showImgForGuest'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOWIMGFORGUEST_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('imageTypes') != 'jpg,jpeg,gif,png')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMAGEALLOWEDTYPES') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_imageTypes"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('imageTypes')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMAGEALLOWEDTYPES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('checkMimeTypes') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMAGECHECKMIMETYPES') ?></td>
                                                        <td><?php echo $this->lists ['checkMimeTypes'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMAGECHECKMIMETYPES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('imageMimeTypes') != 'image/jpeg,image/jpg,image/gif,image/png')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMAGEALLOWEDMIMETYPES') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_imageMimeTypes"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('imageMimeTypes')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMAGEALLOWEDMIMETYPES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('imageSize') != 150)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGSIZE') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field" name="cfg_imageSize"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('imageSize')) ?>"/>
                                                            kB
                                                        </td>
                                                        <td>
															<?php
															echo Text::sprintf(
														'COM_KUNENA_A_IMGSIZE_DESC',
														ini_get('post_max_size'),
														ini_get('upload_max_fileSize'),
														function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : ''
													)
															?>
                                                        </td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('imageWidth') != 800)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGWIDTH') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_imageWidth"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('imageWidth')) ?>"/>
                                                            px
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGWIDTH_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('imageHeight') != 800)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGHEIGHT') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_imageHeight"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('imageHeight')) ?>"/>
                                                            px
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGHEIGHT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('thumbWidth') != 32)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGTHUMBWIDTH') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_thumbWidth"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('thumbWidth')) ?>"/>
                                                            px
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGTHUMBWIDTH_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('thumbHeight') != 32)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGTHUMBHEIGHT') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_thumbHeight"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('thumbHeight')) ?>"/>
                                                            px
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGTHUMBHEIGHT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('imageQuality') != 50)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGQUALITY') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field"
                                                                   name="cfg_imageQuality"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('imageQuality')) ?>"/>
                                                            %
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_IMGQUALITY_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_FILE') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('fileUpload') != 'registered')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FILE_UPLOAD') ?></td>
                                                        <td><?php echo $this->lists ['fileUpload'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FILE_UPLOAD_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showFileForGuest') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOWFILEFORGUEST') ?></td>
                                                        <td><?php echo $this->lists ['showFileForGuest'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOWFILEFORGUEST_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('fileTypes') != 'txt,rtf,pdf,zip,tar.gz,tgz,tar.bz2')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FILEALLOWEDTYPES') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control" name="cfg_fileTypes"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('fileTypes')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FILEALLOWEDTYPES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('fileSize') != 120)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_FILESIZE') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field" name="cfg_fileSize"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('fileSize')) ?>"/> <?php echo Text::_('COM_KUNENA_A_FILESIZE_KB') ?>
                                                        </td>
                                                        <td>
															<?php
															echo Text::sprintf(
														'COM_KUNENA_A_FILESIZE_DESC',
														ini_get('post_max_size'),
														ini_get('upload_max_fileSize'),
														function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : ''
													)
															?>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_ranking">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_RANKING_SETTINGS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('showRanking') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RANKING') ?></td>
                                                        <td><?php echo $this->lists ['showRanking'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RANKING_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rankImages') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RANKINGIMAGES') ?></td>
                                                        <td><?php echo $this->lists ['rankImages'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RANKINGIMAGES_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_bbcode">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_BBCODE_SETTINGS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('trimLongUrls') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLS') ?></td>
                                                        <td><?php echo $this->lists ['trimLongUrls'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('trimLongUrlsFront') != 40)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLSFRONT') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_trimLongUrlsFront"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('trimLongUrlsFront')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLSFRONT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('trimLongUrlsBack') != 20)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLSBACK') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_trimLongUrlsBack"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('trimLongUrlsBack')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLSBACK_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('autoLink') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AUTOLINK') ?></td>
                                                        <td><?php echo $this->lists ['autoLink'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AUTOLINK_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('smartLinking') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SMARTAUTOLINK') ?></td>
                                                        <td><?php echo $this->lists ['smartLinking'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SMARTAUTOLINK_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('autoEmbedYoutube') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDYOUTUBE') ?></td>
                                                        <td><?php echo $this->lists ['autoEmbedYoutube'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDYOUTUBE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('autoEmbedEbay') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDEBAY') ?></td>
                                                        <td><?php echo $this->lists ['autoEmbedEbay'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDEBAY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('autoEmbedInstagram') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDINSTAGRAM') ?></td>
                                                        <td><?php echo $this->lists ['autoEmbedInstagram'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDINSTAGRAM_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('autoEmbedSoundcloud') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_AUTOEMBEDSOUNDCLOUD') ?></td>
                                                        <td><?php echo $this->lists ['autoEmbedSoundcloud'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_AUTOEMBEDSOUNDCLOUD_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('highlightCode') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HIGHLIGHTCODE') ?></td>
                                                        <td><?php echo $this->lists ['highlightCode'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_HIGHLIGHTCODE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('bbcodeImgSecure') != 'text')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE') ?></td>
                                                        <td><?php echo $this->lists ['bbcodeImgSecure'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('ebayLanguage') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_EBAYLANGUAGECODE') ?></td>
                                                        <td><?php echo $this->lists ['ebayLanguage'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_EBAYLANGUAGECODE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('ebayAffiliateId') != 5337089937)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_EBAY_AFFILIATE_ID') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_ebayAffiliateId"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('ebayAffiliateId')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_EBAY_AFFILIATE_ID_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('ebayApiKey') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_EBAY_API_KEY_LABEL') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_ebayApiKey"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('ebayApiKey')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_EBAY_API_KEY_DESC') ?></td>
                                                    </tr>
													<tr <?php if ($this->config->getValueValue('ebayCertId') != '') : echo 'class="changed"'; endif; ?>>
														<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_EBAY_CERTID_KEY_LABEL') ?></td>
														<td>
															<input type="text" name="cfg_ebayCertId"
															   value="<?php echo $this->escape($this->config->getValueValue('ebayCertId')) ?>"/>
														</td>
														<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_EBAY_CERTID_KEY_DESC') ?></td>
													</tr>
                                                    <tr <?php if ($this->config->getValueValue('twitterConsumerKey') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_TWITTER_API_CONSUMER_KEY_LABEL') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_twitterConsumerKey"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('twitterConsumerKey')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_TWITTER_API_CONSUMER_KEY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('twitterConsumerSecret') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_TWITTER_API_CONSUMER_SECRET_LABEL') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_twitterConsumerSecret"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('twitterConsumerSecret')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_TWITTER_API_CONSUMER_SECRET_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('googleMapApiKey') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_GOOGLE_MAPS_API_KEY') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_googleMapApiKey"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('googleMapApiKey')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIGURATION_GOOGLE_MAPS_API_KEY_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_rss">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_ADMIN_RSS_SETTINGS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('rssType') != 'topic')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_TYPE') ?></td>
                                                        <td><?php echo $this->lists ['rssType'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_TYPE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssSpecification') != 'rss2.0')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_SPEC') ?></td>
                                                        <td><?php echo $this->lists ['rssSpecification'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_SPEC_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssTimeLimit') != '1 month')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_TIMELIMIT') ?></td>
                                                        <td><?php echo $this->lists ['rssTimeLimit'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_TIMELIMIT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssLimit') != 100)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_LIMIT') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control" name="cfg_rssLimit"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('rssLimit')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_LIMIT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssIncludedCategories') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_INCLUDED_CATEGORIES') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_rssIncludedCategories"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('rssIncludedCategories')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_INCLUDED_CATEGORIES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssExcludedCategories') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_EXCLUDED_CATEGORIES') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_rssExcludedCategories"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('rssExcludedCategories')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_EXCLUDED_CATEGORIES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssAllowHtml') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_ALLOW_HTML') ?></td>
                                                        <td><?php echo $this->lists ['rssAllowHtml'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_ALLOW_HTML_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssAuthorFormat') != 'name')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT') ?></td>
                                                        <td><?php echo $this->lists ['rssAuthorFormat'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssAuthorInTitle') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_AUTHOR_IN_TITLE') ?></td>
                                                        <td><?php echo $this->lists ['rssAuthorInTitle'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_AUTHOR_IN_TITLE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssWordCount') != '0')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_RSS_CHARACTERS_COUNT') ?></td>
                                                        <td><?php echo $this->lists ['rssWordCount'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_RSS_CHARACTERS_COUNT_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssOldTitles') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_OLD_TITLES') ?></td>
                                                        <td><?php echo $this->lists ['rssOldTitles'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_OLD_TITLES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssCache') != 900)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_CACHE') ?></td>
                                                        <td><?php echo $this->lists ['rssCache'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_CACHE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('rssFeedBurnerUrl') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_FEEDBURNER_URL') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control ksm-field-large"
                                                                   name="cfg_rssFeedBurnerUrl"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('rssFeedBurnerUrl')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_RSS_FEEDBURNER_URL_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane" id="tab_extra">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_SETTINGS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('userlistRows') != 30)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ROWS') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_userlistRows"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('userlistRows')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistOnline') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE') ?></td>
                                                        <td><?php echo $this->lists ['userlistOnline'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistAvatar') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR') ?></td>
                                                        <td><?php echo $this->lists ['userlistAvatar'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistPosts') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_POSTS') ?></td>
                                                        <td><?php echo $this->lists ['userlistPosts'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistKarma') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_KARMA') ?></td>
                                                        <td><?php echo $this->lists ['userlistKarma'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistEmail') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL') ?></td>
                                                        <td><?php echo $this->lists ['userlistEmail'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistJoinDate') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE') ?></td>
                                                        <td><?php echo $this->lists ['userlistJoinDate'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistLastVisitDate') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE') ?></td>
                                                        <td><?php echo $this->lists ['userlistLastVisitDate'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistUserHits') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_HITS') ?></td>
                                                        <td><?php echo $this->lists ['userlistUserHits'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistUserHits') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SHOW_SUPERADMINS_IN_USERLIST') ?></td>
                                                        <td><?php echo $this->lists ['superAdminUserlist'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_SHOW_SUPERADMINS_IN_USERLIST_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistUserHits') != 2)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_USER_SESSIONS_TYPE') ?></td>
                                                        <td><?php echo $this->lists ['showSessionType'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_SESSIONS_TYPE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showSessionStartTime') != 1800)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_USER_SESSIONS_START_TIME') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_showSessionStartTime"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('showSessionStartTime')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_SESSIONS_START_TIME_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_STATS') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('showWhoIsOnline') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOWWHOIS') ?></td>
                                                        <td><?php echo $this->lists ['showWhoIsOnline'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOWWHOISDESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showStats') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOWSTATS') ?></td>
                                                        <td><?php echo $this->lists ['showStats'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_SHOWSTATSDESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showGenStats') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_STATSGENERAL') ?></td>
                                                        <td><?php echo $this->lists ['showGenStats'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_STATSGENERALDESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showPopUserStats') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_USERSTATS') ?></td>
                                                        <td><?php echo $this->lists ['showPopUserStats'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_USERSTATSDESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('popUserCount') != 5)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_USERNUM') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_popUserCount"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('popUserCount')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_USERNUM') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showPopSubjectStats') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_USERPOPULAR') ?></td>
                                                        <td><?php echo $this->lists ['showPopSubjectStats'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_USERPOPULARDESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('popSubjectCount') != 5)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_NUMPOP') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_popSubjectCount"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('popSubjectCount')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_NUMPOP') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showPopPollStats') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_POLLSSTATS') ?></td>
                                                        <td><?php echo $this->lists ['showPopPollStats'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_POLLSTATSDESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('popPollsCount') != 5)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_POLLSPOP') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_popPollsCount"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('popPollsCount')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_POLLSPOP') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('showPopThankYouStats') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_THANKSSTATS') ?></td>
                                                        <td><?php echo $this->lists ['showPopThankYouStats'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_THANKSSTATSDESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('popThanksCount') != 5)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_THANKSPOP') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_popThanksCount"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('popThanksCount')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_THANKSPOP') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('userlistCountUsers') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_WAY_COUNT_USERS_USERLIST') ?></td>
                                                        <td><?php echo $this->lists ['userlistCountUsers'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_COM_A_WAY_COUNT_USERS_USERLIST_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_POLL_TITLE') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
                                                        <th width="25%"><?php echo Text::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
                                                        <th><?php echo Text::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr <?php if ($this->config->getValueValue('pollEnabled') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_ENABLED') ?></td>
                                                        <td><?php echo $this->lists ['pollEnabled'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_ENABLED_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('pollNbOptions') != 4)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_NUMBER_OPTIONS') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_pollNbOptions"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('pollNbOptions')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_NUMBER_OPTIONS_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('pollTimeBtVotes') != '00:15:00')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_TIME_VOTES') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_pollTimeBtVotes"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('pollTimeBtVotes')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_TIME_VOTES_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('pollNbVotesByUser') != 100)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_NUMBER_VOTES_BY_USER') ?></td>
                                                        <td>
                                                            <input type="text" class="form-control"
                                                                   name="cfg_pollNbVotesByUser"
                                                                   value="<?php echo $this->escape($this->config->getValueValue('pollNbVotesByUser')) ?>"/>
                                                        </td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_NUMBER_VOTES_BY_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('pollAllowVoteOne') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_ALLOW_ONE_VOTE') ?></td>
                                                        <td><?php echo $this->lists ['pollAllowVoteOne'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_ALLOW_ONE_VOTE_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('pollResultsUserslist') != 1)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_SHOW_USER_LIST') ?></td>
                                                        <td><?php echo $this->lists ['pollResultsUserslist'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_POLL_SHOW_USER_LIST_DESC') ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('allowUserEditPoll') != 0)
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_POLL_ALLOW_USER_EDIT_POLL') ?></td>
                                                        <td><?php echo $this->lists ['allowUserEditPoll'] ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_CONFIG_POLL_ALLOW_USER_EDIT_POLL_DESC') ?></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>
                                        <div class="tab-pane" id="tab_menu">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_ADMIN_MENU_SETTINGS_DESC') ?></legend>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr <?php if ($this->config->getValueValue('activeMenuItem') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="5%"><?php echo Text::_('COM_KUNENA_A_ACTIVEMENU') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_activeMenuItem"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('activeMenuItem')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_ACTIVEMENU_DESC'); ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('mainMenuId') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="5%"><?php echo Text::_('COM_KUNENA_A_MAINMENU') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_mainMenuId"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('mainMenuId')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_MAINMENU_DESC'); ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('homeId') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="5%"><?php echo Text::_('COM_KUNENA_A_HOMEID') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_homeId"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('homeId')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_HOMEID_DESC'); ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('indexId') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="15%"><?php echo Text::_('COM_KUNENA_A_INDEXID') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_indexId"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('indexId')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_INDEXID_DESC'); ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('moderatorsId') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="15%"><?php echo Text::_('COM_KUNENA_A_MODERATORSID') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_moderatorsId"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('moderatorsId')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_MODERATORSID_DESC'); ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('topicListId') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="15%"><?php echo Text::_('COM_KUNENA_A_TOPICLISTID') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_topicListId"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('topicListId')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_TOPICLISTID_DESC'); ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('miscId') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="15%"><?php echo Text::_('COM_KUNENA_A_MISCID') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_miscId"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('miscId')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_MISCID_DESC'); ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('profileId') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="15%"><?php echo Text::_('COM_KUNENA_A_PROFILEID') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_profileId"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('profileId')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_PROFILEID_DESC'); ?></td>
                                                    </tr>
                                                    <tr <?php if ($this->config->getValueValue('searchId') != '')
														:
														echo 'class="changed"';
													endif; ?>>
                                                        <td align="left" valign="top"
                                                            width="15%"><?php echo Text::_('COM_KUNENA_A_SEARCHID') ?></td>
                                                        <td align="left" valign="top"
                                                            width="5%"><input type="text" class="form-control"
                                                                              name="cfg_searchId"
                                                                              value="<?php echo $this->escape($this->config->getValueValue('searchId')); ?>"/>
                                                        </td>
                                                        <td align="left"
                                                            valign="top"><?php echo Text::_('COM_KUNENA_A_SEARCHID_DESC'); ?></td>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </form>
				<?php // Load the setting confirmation box form. ?>
				<?php echo $this->loadTemplate('setting'); ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>

