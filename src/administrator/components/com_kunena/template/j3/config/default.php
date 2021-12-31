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

HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('dropdown.init');
HTMLHelper::_('behavior.tabstate');
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j3/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<div class="well well-small">
			<div class="module-title nav-header">
				<i class="icon-cogs"></i>
				<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CONFIG') ?>
			</div>
			<hr class="hr-condensed">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena'); ?>" method="post"
			      id="adminForm" name="adminForm">
				<input type="hidden" name="view" value="config"/>
				<input type="hidden" name="task" value=""/>
				<?php echo HTMLHelper::_('form.token') ?>

				<article class="data-block">
					<div class="data-container">
						<div class="tabbable-panel">
							<div class="tabbable-line">
								<ul class="nav nav-tabs">
									<li class="active">
										<a href="#tab_basics"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_BASICS'); ?></a>
									</li>
									<li>
										<a href="#tab_frontend"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_FRONTEND'); ?></a>
									</li>
									<li>
										<a href="#tab_users"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_USERS'); ?></a>
									</li>
									<li>
										<a href="#tab_security"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_SECURITY'); ?></a>
									</li>
									<li>
										<a href="#tab_avatars"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_AVATARS'); ?></a>
									</li>
									<li>
										<a href="#tab_uploads"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_UPLOADS'); ?></a>
									</li>
									<li>
										<a href="#tab_ranking"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_RANKING'); ?></a>
									</li>
									<li>
										<a href="#tab_bbcode"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_A_BBCODE'); ?></a>
									</li>
									<li>
										<a href="#tab_rss"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_ADMIN_RSS'); ?></a>
									</li>
									<li>
										<a href="#tab_extra"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_EXTRA'); ?></a>
									</li>
									<li>
										<a href="#tab_menu"
										   data-toggle="tab"><?php echo Text::_('COM_KUNENA_ADMIN_MENU_SETTINGS'); ?></a>
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
												<tr <?php if ($this->config->board_title != 'Kunena') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_BOARD_TITLE') ?></td>
													<td>
														<input type="text" name="cfg_board_title"
														       value="<?php echo $this->escape($this->config->board_title) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_BOARD_TITLE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->email != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_EMAIL') ?></td>
													<td>
														<input type="text" name="cfg_email"
														       value="<?php echo $this->escape($this->config->email) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_EMAIL_DESC2') ?></td>
												</tr>
												<tr <?php if ($this->config->email_sender_name != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_EMAIL_SENDER_NAME') ?></td>
													<td>
														<input type="text" name="cfg_email_sender_name"
														       value="<?php echo $this->escape($this->config->email_sender_name) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_EMAIL_SENDER_NAME_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->send_emails != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SEND_EMAILS') ?></td>
													<td><?php echo $this->lists ['send_emails'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SEND_EMAILS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->board_offline != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_BOARD_OFFLINE') ?></td>
													<td><?php echo $this->lists ['board_offline'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_BOARD_OFFLINE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->escape($this->config->offline_message) != 'The Forum is currently offline for maintenance.
Check back soon!') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_BOARD_OFFLINE_MES') ?></td>
													<td>
														<textarea name="cfg_offline_message" rows="3"
														          cols="50"><?php echo $this->escape($this->config->offline_message) ?></textarea>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_BOARD_OFFLINE_MES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->read_only != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_BOARD_READONLY') ?></td>
													<td><?php echo $this->lists ['read_only'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_BOARD_READONLY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->escape($this->config->sessiontimeout) != 1800) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_KUNENA_SESSION_TIMEOUT') ?>
													</td>
													<td><input type="text"
													           name="cfg_sessiontimeout"
													           value="<?php echo $this->escape($this->config->sessiontimeout);
													           ?>"/></td>
													<td><?php echo Text::_('COM_KUNENA_A_KUNENA_SESSION_TIMEOUT_DESC') ?>
													</td>
												</tr>
												<tr <?php if ($this->config->enablerss != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS') ?></td>
													<td><?php echo $this->lists ['enablerss'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->debug != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_DEBUG_MODE') ?></td>
													<td><?php echo $this->lists ['debug'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_DEBUG_MODE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->fallback_english != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_FALLBACK_ENGLISH') ?></td>
													<td><?php echo $this->lists ['fallback_english'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CFG_FALLBACK_ENGLISH_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->time_to_create_page != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_TIME_TO_CREATEPAGE') ?></td>
													<td><?php echo $this->lists ['time_to_create_page'] ?></td>
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
												<tr <?php if ($this->config->sef != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SEF') ?></td>
													<td><?php echo $this->lists ['sef'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SEF_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->access_component != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_ACCESS_COMPONENT') ?></td>
													<td><?php echo $this->lists ['access_component'] ?></td>
													<td><?php echo Text::sprintf('COM_KUNENA_CFG_ACCESS_COMPONENT_DESC', $this->lists ['componentUrl'], Text::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND')) ?></td>
												</tr>
												<tr <?php if ($this->config->legacy_urls != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_LEGACY_URLS') ?></td>
													<td><?php echo $this->lists ['legacy_urls'] ?></td>
													<td><?php echo Text::sprintf('COM_KUNENA_CFG_LEGACY_URLS_DESC', $this->lists['legacy_urls_version'], $this->lists['legacy_urls_desc']);
													if ($this->config->legacy_urls == 1): echo ' ' . Text::_('COM_KUNENA_CONFIG_LEGACY_URL_DEPRECATED_DESC'); endif; ?></td>
												</tr>
												<tr <?php if ($this->config->sef_redirect != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SEF_REDIRECT') ?></td>
													<td><?php echo $this->lists ['sef_redirect'] ?></td>
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
												<tr <?php if ($this->config->cache != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_CACHE') ?></td>
													<td><?php echo $this->lists ['cache'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CFG_CACHE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->cache_time != 60) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_CACHE_TIME') ?></td>
													<td><?php echo $this->lists ['cache_time'] ?></td>
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
												<tr <?php if ($this->config->threads_per_page != 20) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_THREADS') ?></td>
													<td>
														<input type="text" name="cfg_threads_per_page"
														       value="<?php echo $this->escape($this->config->threads_per_page) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_THREADS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->messages_per_page != 6) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_MESSAGES') ?></td>
													<td>
														<input type="text" name="cfg_messages_per_page"
														       value="<?php echo $this->escape($this->config->messages_per_page) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_MESSAGES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->messages_per_page_search != 15) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_MESSAGES_SEARCH') ?></td>
													<td>
														<input type="text" name="cfg_messages_per_page_search"
														       value="<?php echo $this->escape($this->config->messages_per_page_search) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_MESSAGES_DESC_SEARCH') ?></td>
												</tr>
												<tr <?php if ($this->config->showhistory != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_HISTORY') ?></td>
													<td><?php echo $this->lists ['showhistory'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_HISTORY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->historylimit != 6) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_HISTLIM') ?></td>
													<td>
														<input type="text" name="cfg_historylimit"
														       value="<?php echo $this->escape($this->config->historylimit) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_HISTLIM_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->post_dateformat != 'ago') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_POST_DATEFORMAT') ?></td>
													<td><?php echo $this->lists ['post_dateformat'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CFG_POST_DATEFORMAT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->post_dateformat_hover != 'datetime') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_POST_DATEFORMAT_HOVER') ?></td>
													<td><?php echo $this->lists ['post_dateformat_hover'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CFG_POST_DATEFORMAT_HOVER_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->shownew != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SHOWNEW') ?></td>
													<td><?php echo $this->lists ['shownew'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SHOWNEW_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showannouncement != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SHOW_ANNOUNCEMENT') ?></td>
													<td><?php echo $this->lists ['showannouncement'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SHOW_ANNOUNCEMENT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->avataroncat != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SHOW_AVATAR_ON_CAT') ?></td>
													<td><?php echo $this->lists ['avataroncat'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SHOW_AVATAR_ON_CAT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->listcat_show_moderators != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_LISTCAT_SHOW_MODERATORS') ?></td>
													<td><?php echo $this->lists ['listcat_show_moderators'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_LISTCAT_SHOW_MODERATORS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->catimagepath != 'category_images') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CATIMAGEPATH_LEGACY') ?></td>
													<td>
														<input type="text" name="cfg_catimagepath"
														       value="<?php echo $this->escape($this->config->catimagepath) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CATIMAGEPATH_LEGACY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showchildcaticon != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST') ?></td>
													<td><?php echo $this->lists ['showchildcaticon'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->enableforumjump != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_FORUM_JUMP') ?></td>
													<td><?php echo $this->lists ['enableforumjump'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_FORUM_JUMP_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->reportmsg != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_REPORT') ?></td>
													<td><?php echo $this->lists ['reportmsg'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_REPORT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->ordering_system != 'mesid') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_ORDERING_SYSTEM') ?></td>
													<td><?php echo $this->lists ['ordering_system'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_REPORT_ORDERING_SYSTEM_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->hide_ip != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_HIDE_IP') ?></td>
													<td><?php echo $this->lists ['hide_ip'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_HIDE_IP_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->latestcategory_in != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_LATESTCATEGORY_IN') ?></td>
													<td><?php echo $this->lists ['latestcategory_in'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_LATESTCATEGORY_IN_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->latestcategory != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_LATESTCATEGORY') ?></td>
													<td><?php echo $this->lists ['latestcategory'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_LATESTCATEGORY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->topicicons != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_TOPICICONS') ?></td>
													<td><?php echo $this->lists ['topicicons'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_TOPCIICONS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->lightbox != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_ENABLELIGHTBOX') ?></td>
													<td><?php echo $this->lists ['lightbox'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_ENABLELIGHTBOX_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->show_list_time != 720) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_SHOW_TOPICS_FROM_LAST_TIME') ?></td>
													<td><?php echo $this->lists ['show_list_time'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_SHOW_TOPICS_FROM_LAST_TIME_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->topic_layout != 'flat') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_TOPIC_LAYOUT') ?></td>
													<td><?php echo $this->lists ['topic_layout'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->pickup_category != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_PICKUP_CATEGORY') ?></td>
													<td><?php echo $this->lists ['pickup_category'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_PICKUP_CATEGORY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->article_display != 'intro') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_ARTICLE_DISPLAY') ?></td>
													<td><?php echo $this->lists ['article_display'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_ARTICLE_DISPLAY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->default_sort != 'asc') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_DEFAULT_SORT') ?></td>
													<td><?php echo $this->lists ['default_sort'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CFG_DEFAULT_SORT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->searchtime != 365) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CFG_SEARCH_TIME') ?></td>
													<td><?php echo $this->lists ['searchtime'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CFG_SEARCH_TIME_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->allow_change_subject != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ALLOW_CHANGE_SUBJECT_REPLIES_LABEL') ?></td>
													<td><?php echo $this->lists ['allow_change_subject'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ALLOW_CHANGE_SUBJECT_REPLIES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->ratingenabled != 0) : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="25%"><?php echo Text::_('COM_KUNENA_CONFIGURATION_RATING_ENABLED') ?></td>
													<td align="left" valign="top"
													    width="25%"><?php echo $this->lists ['ratingenabled'] ?></td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_CONFIGURATION_RATING_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->attach_start != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_ATTACHMENT_SHORTEN_NAME_START') ?></td>
													<td>
														<input type="text" name="cfg_attach_start" class="ksm-field"
														       value="<?php echo $this->escape($this->config->attach_start) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_ATTACHMENT_SHORTEN_NAME_START_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->attach_end != 14) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_ATTACHMENT_SHORTEN_NAME_END') ?></td>
													<td>
														<input type="text" name="cfg_attach_end" class="ksm-field"
														       value="<?php echo $this->escape($this->config->attach_end) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_ATTACHMENT_SHORTEN_NAME_END_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->quickreply != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_QUICK_REPLY') ?></td>
													<td><?php echo $this->lists ['quickreply'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_QUICK_REPLY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->disable_re != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_DISABLE_RE') ?></td>
													<td><?php echo $this->lists ['disable_re'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_DISABLE_RE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->display_filename_attachment != 0) : echo 'class="changed"'; endif; ?>>
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
												<tr <?php if ($this->config->username != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_DISPLAY_NAME') ?></td>
													<td><?php echo $this->lists ['username'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_USERNAME_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->askemail != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_ASK_EMAIL') ?></td>
													<td><?php echo $this->lists ['askemail'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_ASK_EMAIL_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showemail != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SHOWMAIL') ?></td>
													<td><?php echo $this->lists ['showemail'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SHOWMAIL_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showuserstats != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_USERSTATS') ?></td>
													<td><?php echo $this->lists ['showuserstats'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_USERSTATS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showkarma != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_KARMA') ?></td>
													<td><?php echo $this->lists ['showkarma'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_KARMA_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showthankyou != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_THANKYOU') ?></td>
													<td><?php echo $this->lists ['showthankyou'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_THANKYOU_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->thankyou_max != 10) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_DISPLAY_THANKYOU_NUMBERS') ?></td>
													<td>
														<input type="text" name="cfg_thankyou_max" class="ksm-field"
														       value="<?php echo $this->escape($this->config->thankyou_max) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_DISPLAY_THANKYOU_NUMBERS') ?></td>
												</tr>
												<tr <?php if ($this->config->useredit != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_USER_EDIT') ?></td>
													<td><?php echo $this->lists ['useredit'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->useredittime != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_TIME') ?></td>
													<td>
														<input type="text" name="cfg_useredittime"
														       value="<?php echo $this->escape($this->config->useredittime) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_TIME_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->useredittimegrace != 600) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_TIMEGRACE') ?></td>
													<td>
														<input type="text" name="cfg_useredittimegrace"
														       value="<?php echo $this->escape($this->config->useredittimegrace) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_USER_EDIT_TIMEGRACE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->editmarkup != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_USER_MARKUP') ?></td>
													<td><?php echo $this->lists ['editmarkup'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_USER_MARKUP_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->allowfavorites != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_FAVORITES') ?></td>
													<td><?php echo $this->lists ['allowfavorites'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_FAVORITES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->boxghostmessage != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_GHOSTMESSAGE') ?></td>
													<td><?php echo $this->lists ['boxghostmessage'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_GHOSTMESSAGE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showbannedreason != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SHOWBANNEDREASON_PROFILE') ?></td>
													<td><?php echo $this->lists ['showbannedreason'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SHOWBANNEDREASON_PROFILE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->show_imgfiles_manage_profile != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_DISPLAY_IMGFILES_TAB_MANAGEMENT_PROFILE') ?></td>
													<td><?php echo $this->lists ['show_imgfiles_manage_profile'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_DISPLAY_IMGFILES_TAB_MANAGEMENT_PROFILE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->user_report != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_USER_CAN_SEND_OWN_REPORT') ?></td>
													<td><?php echo $this->lists ['user_report'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_USER_CAN_SEND_OWN_REPORT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->log_moderation != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_LOG_COLLECT_DATA') ?></td>
													<td><?php echo $this->lists ['log_moderation'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_LOG_COLLECT_DATA_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->user_status != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_USER_STATUS') ?></td>
													<td><?php echo $this->lists ['user_status'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_USER_STATUS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->signature != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SIGNATURES') ?></td>
													<td><?php echo $this->lists ['signature'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SIGNATURES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->personal != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_PERSONAL') ?></td>
													<td><?php echo $this->lists ['personal'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_PERSONAL_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->social != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SOCIAL') ?></td>
													<td><?php echo $this->lists ['social'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SOCIAL_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->maxsig != 300) : echo 'class="changed"'; endif; ?>>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_SIGNATURE') ?></td>
													<td align="left" valign="top"><input type="text" name="cfg_maxsig"
													                                     value="<?php echo $this->escape($this->config->maxsig) ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_SIGNATURE_DESC') ?></td>
												</tr>
												</tbody>
											</table>
										</fieldset>
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
												<tr <?php if ($this->config->emailheader != 'media/kunena/email/hero-wide.png') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_EMAIL_HEADER') ?></td>
													<td><input name="cfg_emailheader" type="text"
													           value="<?php echo $this->escape($this->config->emailheader) ?>">
													</td>
													<td><?php echo Text::_('COM_KUNENA_EMAIL_HEADER_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->allowsubscriptions != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS') ?></td>
													<td><?php echo $this->lists ['allowsubscriptions'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->mailfull != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_MAILFULL') ?></td>
													<td><?php echo $this->lists ['mailfull'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_MAILFULL_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->plain_email != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_PLAINEMAIL') ?></td>
													<td><?php echo $this->lists ['plain_email'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_PLAINEMAIL_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->category_subscriptions != 'post') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_CATEGORY_SUBSCRIPTIONS') ?></td>
													<td><?php echo $this->lists ['category_subscriptions'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_CATEGORY_SUBSCRIPTIONS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->topic_subscriptions != 'every') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_TOPIC_SUBSCRIPTIONS') ?></td>
													<td><?php echo $this->lists ['topic_subscriptions'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_TOPIC_SUBSCRIPTIONS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->subscriptionschecked != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SUBSCRIPTIONSCHECKED') ?></td>
													<td><?php echo $this->lists ['subscriptionschecked'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SUBSCRIPTIONSCHECKED_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->email_recipient_count != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_COUNT') ?></td>
													<td><?php echo HTMLHelper::_('select.integerlist', 0, 100, 5, 'cfg_email_recipient_count', null, $this->escape($this->config->email_recipient_count)) ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_COUNT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->email_recipient_privacy != 'bcc') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY') ?></td>
													<td><?php echo $this->lists ['email_recipient_privacy'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->email_visible_address != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_VISIBLE_ADDRESS') ?></td>
													<td>
														<input type="text" name="cfg_email_visible_address"
														       value="<?php echo $this->escape($this->config->email_visible_address) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_VISIBLE_ADDRESS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->use_system_emails != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_USE_SYSTEM_EMAILS') ?></td>
													<td><?php echo $this->lists ['use_system_emails'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_USE_SYSTEM_EMAILS_DESC') ?></td>
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
												<tr <?php if ($this->config->pubwrite != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_PUBWRITE') ?></td>
													<td><?php echo $this->lists ['pubwrite'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_PUBWRITE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->hold_guest_posts != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_HOLD_GUEST_POSTS') ?></td>
													<td><?php echo $this->lists ['hold_guest_posts'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_HOLD_GUEST_POSTS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_allowed != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ALLOWED') ?></td>
													<td><?php echo $this->lists ['userlist_allowed'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ALLOWED_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->pubprofile != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PUBPROFILE') ?></td>
													<td><?php echo $this->lists ['pubprofile'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PUBPROFILE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->regonly != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_REGISTERED_ONLY') ?></td>
													<td><?php echo $this->lists ['regonly'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_REG_ONLY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->hold_newusers_posts != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_HOLD_NEWUSERS_POSTS') ?></td>
													<td>
														<input type="text" name="cfg_hold_newusers_posts"
														       class="ksm-field"
														       value="<?php echo $this->escape($this->config->hold_newusers_posts) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_HOLD_NEWUSERS_POSTS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userdeletetmessage != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_DELETEMESSAGE') ?></td>
													<td><?php echo $this->lists ['userdeletetmessage'] ?> </td>
													<td><?php echo Text::_('COM_KUNENA_A_DELETEMESSAGE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->mod_see_deleted != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_MOD_SEE_DELETED') ?></td>
													<td><?php echo $this->lists ['mod_see_deleted'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_MOD_SEE_DELETED_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->floodprotection != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_FLOOD') ?></td>
													<td>
														<input type="text" name="cfg_floodprotection"
														       value="<?php echo $this->escape($this->config->floodprotection) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_FLOOD_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->mailmod != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_MAILMOD') ?></td>
													<td><?php echo $this->lists ['mailmod'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_MAILMOD_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->mailadmin != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_MAILADMIN') ?></td>
													<td><?php echo $this->lists ['mailadmin'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_MAILADMIN_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->iptracking != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IP_TRACKING') ?></td>
													<td><?php echo $this->lists ['iptracking'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_IP_TRACKING_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->statslink_allowed != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_STATSLINK_ALLOWED') ?></td>
													<td><?php echo $this->lists ['statslink_allowed'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_STATSLINK_ALLOWED_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->max_links != 6) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MAX_LINKS') ?></td>
													<td>
														<input type="text" name="cfg_max_links"
														       value="<?php echo $this->escape($this->config->max_links) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MAX_LINKS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->teaser != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_TEASER') ?></td>
													<td><?php echo $this->lists ['teaser'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_TEASER_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->url_subject_topic != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_URL_SUBJECT') ?></td>
													<td><?php echo $this->lists['url_subject_topic'] ?> </td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_URL_SUBJECT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->moderator_permdelete != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MOD_PERDELETE') ?></td>
													<td><?php echo $this->lists['moderator_permdelete'] ?> </td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MOD_PERDELETE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->new_users_prevent_post_url_images != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PREVENT_NEW_USERS_POST_URL_IMAGE') ?></td>
													<td><?php echo $this->lists['new_users_prevent_post_url_images'] ?> </td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_PREVENT_NEW_USERS_POST_URL_IMAGE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->minimal_user_posts_add_url_image != 10) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_MINIMAL_NUMBER_OF_USER_POSTS_TO_ADD_URL_IMAGE') ?></td>
													<td>
														<input type="text" name="cfg_minimal_user_posts_add_url_image"
														       value="<?php echo $this->escape($this->config->minimal_user_posts_add_url_image) ?>"/>
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
												<tr <?php if ($this->config->captcha != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_CAPTCHA_FOR_WHO_TITLE') ?></td>
													<td><?php echo $this->lists ['captcha'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_CAPTCHA_FOR_WHO_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->captcha_post_limit != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_CAPTCHA_POST_LIMIT') ?></td>
													<td>
														<input type="text" name="cfg_captcha_post_limit"
														       class="ksm-field"
														       value="<?php echo $this->escape($this->config->captcha_post_limit) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_CAPTCHA_POST_LIMIT_DESC') ?></td>
												</tr>
												</tbody>
											</table>
										</fieldset>
										<fieldset>
											<legend><?php echo Text::_('COM_KUNENA_STOP_FORUM_SPAM_CONFIGURATION'); ?></legend>
											<table class="table table-striped">
												<tr <?php if ($this->config->stopforumspam_key != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="25%"><?php echo Text::_('COM_KUNENA_STOP_FORUM_SPAM_KEY') ?></td>
													<td align="left" valign="top" width="25%">
														<input type="text"
														       name="cfg_stopforumspam_key"
														       class="ksm-field-large"
														       value="<?php echo $this->escape($this->config->stopforumspam_key); ?>"/>
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
												<tr <?php if ($this->config->avatar_type != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AVATARTYPE') ?></td>
													<td><?php echo $this->lists ['avatar_type'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_AVATARTYPE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->defaultavatar != 'nophoto.png') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AVATAR_DEFAULT') ?></td>
													<td><input type="text" name="cfg_defaultavatar"
													           value="<?php echo $this->escape($this->config->defaultavatar) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_AVATAR_DEFAULT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->defaultavatarsmall != 's_nophoto.png') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AVATAR_DEFAULT_SMALL') ?></td>
													<td><input type="text" name="cfg_defaultavatarsmall"
													           value="<?php echo $this->escape($this->config->defaultavatarsmall) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_AVATAR_DEFAULT_SMALL_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->avatartypes != 'gif, jpeg, jpg, png') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_AVATAR_FILESALLOWED') ?></td>
													<td><input type="text" name="cfg_avatartypes"
													           value="<?php echo $this->escape($this->config->avatartypes) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_AVATAR_FILESALLOWED_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->allowavatarupload != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AVATARUPLOAD') ?></td>
													<td><?php echo $this->lists ['allowavatarupload'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_AVATARUPLOAD_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->allowavatargallery != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AVATARGALLERY') ?></td>
													<td><?php echo $this->lists ['allowavatargallery'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_AVATARGALLERY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->avatarsize != 2048) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AVSIZE') ?></td>
													<td>
														<input type="text" name="cfg_avatarsize" class="ksm-field"
														       value="<?php echo $this->escape($this->config->avatarsize) ?>"/>
														kB
													</td>
													<td></td>
												</tr>
												<tr <?php if ($this->config->avatarquality != 75) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_AVATAR_QUALITY') ?></td>
													<td class="nowrap">
														<input type="text" name="cfg_avatarquality" class="ksm-field"
														       value="<?php echo $this->escape($this->config->avatarquality) ?>"/>
														%
													</td>
													<td></td>
												</tr>
												<tr <?php if ($this->config->avatarresizemethod != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_AVATAR_RESIZE_METHOD') ?></td>
													<td><?php echo $this->lists ['avatarresizemethod'] ?></td>
													<td></td>
												</tr>
												<tr <?php if ($this->config->avatarcrop != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_AVATAR_CROP') ?></td>
													<td><?php echo $this->lists ['avatarcrop'] ?></td>
													<td></td>
												</tr>
												<tr <?php if ($this->config->avataredit != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AVATAREDIT') ?></td>
													<td><?php echo $this->lists ['avataredit'] ?></td>
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
												<tr <?php if ($this->config->attachment_limit != 8) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_ATTACHMENT_LIMIT') ?></td>
													<td>
														<input type="text" name="cfg_attachment_limit"
														       value="<?php echo $this->escape($this->config->attachment_limit) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_ATTACHMENT_LIMIT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->attachment_protection != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_ATTACHMENT_PROTECTION') ?></td>
													<td><?php echo $this->lists ['attachment_protection'] ?></td>
													<td><?php echo Text::sprintf('COM_KUNENA_A_ATTACHMENT_PROTECTION_DESC', Uri::root(false) . 'media/kunena/attachments/image.png') ?></td>
												</tr>
												<tr <?php if ($this->config->attachment_utf8 != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_ATTACHMENT_FILENAME_UTF8') ?></td>
													<td><?php echo $this->lists ['attachment_utf8'] ?></td>
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
												<tr <?php if ($this->config->image_upload != 'registered') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMAGE_UPLOAD') ?></td>
													<td><?php echo $this->lists ['image_upload'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_IMAGE_UPLOAD_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showimgforguest != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SHOWIMGFORGUEST') ?></td>
													<td><?php echo $this->lists ['showimgforguest'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SHOWIMGFORGUEST_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->imagetypes != 'jpg,jpeg,gif,png') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMAGEALLOWEDTYPES') ?></td>
													<td>
														<input type="text" name="cfg_imagetypes"
														       value="<?php echo $this->escape($this->config->imagetypes) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_IMAGEALLOWEDTYPES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->checkmimetypes != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMAGECHECKMIMETYPES') ?></td>
													<td><?php echo $this->lists ['checkmimetypes'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_IMAGECHECKMIMETYPES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->imagemimetypes != 'image/jpeg,image/jpg,image/gif,image/png') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMAGEALLOWEDMIMETYPES') ?></td>
													<td>
														<input type="text" name="cfg_imagemimetypes"
														       value="<?php echo $this->escape($this->config->imagemimetypes) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_IMAGEALLOWEDMIMETYPES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->imagesize != 150) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMGSIZE') ?></td>
													<td>
														<input type="text" name="cfg_imagesize" class="ksm-field"
														       value="<?php echo $this->escape($this->config->imagesize) ?>"/>
														kB
													</td>
													<td>
														<?php
														echo Text::sprintf('COM_KUNENA_A_IMGSIZE_DESC',
															ini_get('post_max_size'), ini_get('upload_max_filesize'),
															function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : ''
														)
														?>
													</td>
												</tr>
												<tr <?php if ($this->config->imagewidth != 800) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMGWIDTH') ?></td>
													<td>
														<input type="text" name="cfg_imagewidth" class="ksm-field"
														       value="<?php echo $this->escape($this->config->imagewidth) ?>"/>
														px
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_IMGWIDTH_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->imageheight != 800) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMGHEIGHT') ?></td>
													<td>
														<input type="text" name="cfg_imageheight" class="ksm-field"
														       value="<?php echo $this->escape($this->config->imageheight) ?>"/>
														px
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_IMGHEIGHT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->thumbwidth != 32) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMGTHUMBWIDTH') ?></td>
													<td>
														<input type="text" name="cfg_thumbwidth" class="ksm-field"
														       value="<?php echo $this->escape($this->config->thumbwidth) ?>"/>
														px
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_IMGTHUMBWIDTH_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->thumbheight != 32) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMGTHUMBHEIGHT') ?></td>
													<td>
														<input type="text" class="ksm-field" name="cfg_thumbheight"
														       value="<?php echo $this->escape($this->config->thumbheight) ?>"/>
														px
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_IMGTHUMBHEIGHT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->imagequality != 50) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_IMGQUALITY') ?></td>
													<td>
														<input type="text" name="cfg_imagequality" class="ksm-field"
														       value="<?php echo $this->escape($this->config->imagequality) ?>"/>
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
												<tr <?php if ($this->config->file_upload != 'registered') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_FILE_UPLOAD') ?></td>
													<td><?php echo $this->lists ['file_upload'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_FILE_UPLOAD_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showfileforguest != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SHOWFILEFORGUEST') ?></td>
													<td><?php echo $this->lists ['showfileforguest'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SHOWFILEFORGUEST_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->filetypes != 'txt,rtf,pdf,zip,tar.gz,tgz,tar.bz2') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_FILEALLOWEDTYPES') ?></td>
													<td>
														<input type="text" name="cfg_filetypes"
														       value="<?php echo $this->escape($this->config->filetypes) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_FILEALLOWEDTYPES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->filesize != 120) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_FILESIZE') ?></td>
													<td>
														<input type="text" name="cfg_filesize" class="ksm-field"
														       value="<?php echo $this->escape($this->config->filesize) ?>"/> <?php echo Text::_('COM_KUNENA_A_FILESIZE_KB') ?>
													</td>
													<td>
														<?php
														echo Text::sprintf('COM_KUNENA_A_FILESIZE_DESC',
															ini_get('post_max_size'), ini_get('upload_max_filesize'),
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
												<tr <?php if ($this->config->showranking != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RANKING') ?></td>
													<td><?php echo $this->lists ['showranking'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RANKING_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rankimages != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RANKINGIMAGES') ?></td>
													<td><?php echo $this->lists ['rankimages'] ?></td>
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
												<tr <?php if ($this->config->trimlongurls != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLS') ?></td>
													<td><?php echo $this->lists ['trimlongurls'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->trimlongurlsfront != 40) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLSFRONT') ?></td>
													<td>
														<input type="text" name="cfg_trimlongurlsfront"
														       value="<?php echo $this->escape($this->config->trimlongurlsfront) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLSFRONT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->trimlongurlsback != 20) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLSBACK') ?></td>
													<td>
														<input type="text" name="cfg_trimlongurlsback"
														       value="<?php echo $this->escape($this->config->trimlongurlsback) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_TRIMLONGURLSBACK_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->autolink != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AUTOLINK') ?></td>
													<td><?php echo $this->lists ['autolink'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_AUTOLINK_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->smartlinking != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SMARTAUTOLINK') ?></td>
													<td><?php echo $this->lists ['smartlinking'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SMARTAUTOLINK_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->autoembedyoutube != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDYOUTUBE') ?></td>
													<td><?php echo $this->lists ['autoembedyoutube'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDYOUTUBE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->autoembedebay != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDEBAY') ?></td>
													<td><?php echo $this->lists ['autoembedebay'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDEBAY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->autoembedinstagram != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDINSTAGRAM') ?></td>
													<td><?php echo $this->lists ['autoembedinstagram'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_AUTOEMBEDINSTAGRAM_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->autoembedsoundcloud != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_AUTOEMBEDSOUNDCLOUD') ?></td>
													<td><?php echo $this->lists ['autoembedsoundcloud'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_AUTOEMBEDSOUNDCLOUD_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->highlightcode != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_HIGHLIGHTCODE') ?></td>
													<td><?php echo $this->lists ['highlightcode'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_HIGHLIGHTCODE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->bbcode_img_secure != 'text') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE') ?></td>
													<td><?php echo $this->lists ['bbcode_img_secure'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->ebay_language != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_EBAYLANGUAGECODE') ?></td>
													<td><?php echo $this->lists ['ebay_language'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_EBAYLANGUAGECODE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->ebay_affiliate_id != 5337089937) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_EBAY_AFFILIATE_ID') ?></td>
													<td>
														<input type="text" name="cfg_ebay_affiliate_id"
														       value="<?php echo $this->escape($this->config->ebay_affiliate_id) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_EBAY_AFFILIATE_ID_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->ebay_api_key != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_EBAY_API_KEY_LABEL') ?></td>
													<td>
														<input type="text" name="cfg_ebay_api_key"
														       value="<?php echo $this->escape($this->config->ebay_api_key) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_EBAY_API_KEY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->ebay_cert_id != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_EBAY_CERTID_KEY_LABEL') ?></td>
													<td>
														<input type="text" name="cfg_ebay_cert_id"
														       value="<?php echo $this->escape($this->config->ebay_cert_id) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_EBAY_CERTID_KEY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->twitter_consumer_key != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_TWITTER_API_CONSUMER_KEY_LABEL') ?></td>
													<td>
														<input type="text" name="cfg_twitter_consumer_key"
														       value="<?php echo $this->escape($this->config->twitter_consumer_key) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_TWITTER_API_CONSUMER_KEY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->twitter_consumer_secret != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_TWITTER_API_CONSUMER_SECRET_LABEL') ?></td>
													<td>
														<input type="text" name="cfg_twitter_consumer_secret"
														       value="<?php echo $this->escape($this->config->twitter_consumer_secret) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_TWITTER_API_CONSUMER_SECRET_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->google_map_api_key != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIGURATION_GOOGLE_MAPS_API_KEY') ?></td>
													<td>
														<input type="text" name="cfg_google_map_api_key"
														       value="<?php echo $this->escape($this->config->google_map_api_key) ?>"/>
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
												<tr <?php if ($this->config->rss_type != 'topic') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_TYPE') ?></td>
													<td><?php echo $this->lists ['rss_type'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_TYPE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_specification != 'rss2.0') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_SPEC') ?></td>
													<td><?php echo $this->lists ['rss_specification'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_SPEC_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_timelimit != 'month') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_TIMELIMIT') ?></td>
													<td><?php echo $this->lists ['rss_timelimit'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_TIMELIMIT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_limit != 100) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_LIMIT') ?></td>
													<td>
														<input type="text" name="cfg_rss_limit"
														       value="<?php echo $this->escape($this->config->rss_limit) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_LIMIT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_included_categories != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_INCLUDED_CATEGORIES') ?></td>
													<td>
														<input type="text" name="cfg_rss_included_categories"
														       value="<?php echo $this->escape($this->config->rss_included_categories) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_INCLUDED_CATEGORIES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_excluded_categories != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_EXCLUDED_CATEGORIES') ?></td>
													<td>
														<input type="text" name="cfg_rss_excluded_categories"
														       value="<?php echo $this->escape($this->config->rss_excluded_categories) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_EXCLUDED_CATEGORIES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_allow_html != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_ALLOW_HTML') ?></td>
													<td><?php echo $this->lists ['rss_allow_html'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_ALLOW_HTML_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_author_format != 'name') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT') ?></td>
													<td><?php echo $this->lists ['rss_author_format'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_author_in_title != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_AUTHOR_IN_TITLE') ?></td>
													<td><?php echo $this->lists ['rss_author_in_title'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_AUTHOR_IN_TITLE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_word_count != '0') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_RSS_CHARACTERS_COUNT') ?></td>
													<td><?php echo $this->lists ['rss_word_count'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_RSS_CHARACTERS_COUNT_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_old_titles != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_OLD_TITLES') ?></td>
													<td><?php echo $this->lists ['rss_old_titles'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_OLD_TITLES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_cache != 900) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_CACHE') ?></td>
													<td><?php echo $this->lists ['rss_cache'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_CACHE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->rss_feedburner_url != '') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_RSS_FEEDBURNER_URL') ?></td>
													<td>
														<input type="text" class="ksm-field-large"
														       name="cfg_rss_feedburner_url"
														       value="<?php echo $this->escape($this->config->rss_feedburner_url) ?>"/>
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
												<tr <?php if ($this->config->userlist_rows != 30) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ROWS') ?></td>
													<td>
														<input type="text" name="cfg_userlist_rows"
														       value="<?php echo $this->escape($this->config->userlist_rows) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_online != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE') ?></td>
													<td><?php echo $this->lists ['userlist_online'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_avatar != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR') ?></td>
													<td><?php echo $this->lists ['userlist_avatar'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_posts != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_POSTS') ?></td>
													<td><?php echo $this->lists ['userlist_posts'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_karma != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_KARMA') ?></td>
													<td><?php echo $this->lists ['userlist_karma'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_email != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL') ?></td>
													<td><?php echo $this->lists ['userlist_email'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_joindate != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE') ?></td>
													<td><?php echo $this->lists ['userlist_joindate'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_lastvisitdate != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE') ?></td>
													<td><?php echo $this->lists ['userlist_lastvisitdate'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_userhits != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_HITS') ?></td>
													<td><?php echo $this->lists ['userlist_userhits'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->superadmin_userlist != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_SHOW_SUPERADMINS_IN_USERLIST') ?></td>
													<td><?php echo $this->lists ['superadmin_userlist'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_SHOW_SUPERADMINS_IN_USERLIST_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->show_session_type != 2) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_USER_SESSIONS_TYPE') ?></td>
													<td><?php echo $this->lists ['show_session_type'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_COM_A_SESSIONS_TYPE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->show_session_starttime != 1800) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_USER_SESSIONS_START_TIME') ?></td>
													<td>
														<input type="text" name="cfg_show_session_starttime"
														       value="<?php echo $this->escape($this->config->show_session_starttime) ?>"/>
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
												<tr <?php if ($this->config->showwhoisonline != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SHOWWHOIS') ?></td>
													<td><?php echo $this->lists ['showwhoisonline'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SHOWWHOISDESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showstats != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_SHOWSTATS') ?></td>
													<td><?php echo $this->lists ['showstats'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_SHOWSTATSDESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showgenstats != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_STATSGENERAL') ?></td>
													<td><?php echo $this->lists ['showgenstats'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_STATSGENERALDESC') ?></td>
												</tr>
												<tr <?php if ($this->config->showpopuserstats != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_USERSTATS') ?></td>
													<td><?php echo $this->lists ['showpopuserstats'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_USERSTATSDESC') ?></td>
												</tr>
												<tr <?php if ($this->config->popusercount != 5) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_USERNUM') ?></td>
													<td>
														<input type="text" name="cfg_popusercount"
														       value="<?php echo $this->escape($this->config->popusercount) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_USERNUM') ?></td>
												</tr>
												<tr <?php if ($this->config->showpopsubjectstats != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_USERPOPULAR') ?></td>
													<td><?php echo $this->lists ['showpopsubjectstats'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_USERPOPULARDESC') ?></td>
												</tr>
												<tr <?php if ($this->config->popsubjectcount != 5) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_NUMPOP') ?></td>
													<td>
														<input type="text" name="cfg_popsubjectcount"
														       value="<?php echo $this->escape($this->config->popsubjectcount) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_NUMPOP') ?></td>
												</tr>
												<tr <?php if ($this->config->showpoppollstats != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_POLLSSTATS') ?></td>
													<td><?php echo $this->lists ['showpoppollstats'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_POLLSTATSDESC') ?></td>
												</tr>
												<tr <?php if ($this->config->poppollscount != 5) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_POLLSPOP') ?></td>
													<td>
														<input type="text" name="cfg_poppollscount"
														       value="<?php echo $this->escape($this->config->poppollscount) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_POLLSPOP') ?></td>
												</tr>
												<tr <?php if ($this->config->showpopthankyoustats != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_THANKSSTATS') ?></td>
													<td><?php echo $this->lists ['showpopthankyoustats'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_THANKSSTATSDESC') ?></td>
												</tr>
												<tr <?php if ($this->config->popthankscount != 5) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_THANKSPOP') ?></td>
													<td>
														<input type="text" name="cfg_popthankscount"
														       value="<?php echo $this->escape($this->config->popthankscount) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_THANKSPOP') ?></td>
												</tr>
												<tr <?php if ($this->config->userlist_count_users != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_COM_A_WAY_COUNT_USERS_USERLIST') ?></td>
													<td><?php echo $this->lists ['userlist_count_users'] ?></td>
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
												<tr <?php if ($this->config->pollenabled != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_ENABLED') ?></td>
													<td><?php echo $this->lists ['pollenabled'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_ENABLED_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->pollnboptions != 4) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_NUMBER_OPTIONS') ?></td>
													<td>
														<input type="text" name="cfg_pollnboptions"
														       value="<?php echo $this->escape($this->config->pollnboptions) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_NUMBER_OPTIONS_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->polltimebtvotes != '00:15:00') : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_TIME_VOTES') ?></td>
													<td>
														<input type="text" name="cfg_polltimebtvotes"
														       value="<?php echo $this->escape($this->config->polltimebtvotes) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_TIME_VOTES_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->pollnbvotesbyuser != 100) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_NUMBER_VOTES_BY_USER') ?></td>
													<td>
														<input type="text" name="cfg_pollnbvotesbyuser"
														       value="<?php echo $this->escape($this->config->pollnbvotesbyuser) ?>"/>
													</td>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_NUMBER_VOTES_BY_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->pollallowvoteone != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_ALLOW_ONE_VOTE') ?></td>
													<td><?php echo $this->lists ['pollallowvoteone'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_ALLOW_ONE_VOTE_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->pollresultsuserslist != 1) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_SHOW_USER_LIST') ?></td>
													<td><?php echo $this->lists ['pollresultsuserslist'] ?></td>
													<td><?php echo Text::_('COM_KUNENA_A_POLL_SHOW_USER_LIST_DESC') ?></td>
												</tr>
												<tr <?php if ($this->config->allow_user_edit_poll != 0) : echo 'class="changed"'; endif; ?>>
													<td><?php echo Text::_('COM_KUNENA_CONFIG_POLL_ALLOW_USER_EDIT_POLL') ?></td>
													<td><?php echo $this->lists ['allow_user_edit_poll'] ?></td>
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
												<tr <?php if ($this->config->activemenuitem != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="5%"><?php echo Text::_('COM_KUNENA_A_ACTIVEMENU') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_activemenuitem"
													                      value="<?php echo $this->escape($this->config->activemenuitem); ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_ACTIVEMENU_DESC'); ?></td>
												</tr>
												<tr <?php if ($this->config->mainmenu_id != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="5%"><?php echo Text::_('COM_KUNENA_A_MAINMENU') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_mainmenu_id"
													                      value="<?php echo $this->escape($this->config->mainmenu_id); ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_MAINMENU_DESC'); ?></td>
												</tr>
												<tr <?php if ($this->config->home_id != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="5%"><?php echo Text::_('COM_KUNENA_A_HOMEID') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_home_id"
													                      value="<?php echo $this->escape($this->config->home_id); ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_HOMEID_DESC'); ?></td>
												</tr>
												<tr <?php if ($this->config->index_id != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="15%"><?php echo Text::_('COM_KUNENA_A_INDEXID') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_index_id"
													                      value="<?php echo $this->escape($this->config->index_id); ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_INDEXID_DESC'); ?></td>
												</tr>
												<tr <?php if ($this->config->moderators_id != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="15%"><?php echo Text::_('COM_KUNENA_A_MODERATORSID') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_moderators_id"
													                      value="<?php echo $this->escape($this->config->moderators_id); ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_MODERATORSID_DESC'); ?></td>
												</tr>
												<tr <?php if ($this->config->topiclist_id != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="15%"><?php echo Text::_('COM_KUNENA_A_TOPICLISTID') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_topiclist_id"
													                      value="<?php echo $this->escape($this->config->topiclist_id); ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_TOPICLISTID_DESC'); ?></td>
												</tr>
												<tr <?php if ($this->config->misc_id != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="15%"><?php echo Text::_('COM_KUNENA_A_MISCID') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_misc_id"
													                      value="<?php echo $this->escape($this->config->misc_id); ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_MISCID_DESC'); ?></td>
												</tr>
												<tr <?php if ($this->config->profile_id != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="15%"><?php echo Text::_('COM_KUNENA_A_PROFILEID') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_profile_id"
													                      value="<?php echo $this->escape($this->config->profile_id); ?>"/>
													</td>
													<td align="left"
													    valign="top"><?php echo Text::_('COM_KUNENA_A_PROFILEID_DESC'); ?></td>
												</tr>
												<tr <?php if ($this->config->search_id != '') : echo 'class="changed"'; endif; ?>>
													<td align="left" valign="top"
													    width="15%"><?php echo Text::_('COM_KUNENA_A_SEARCHID') ?></td>
													<td align="left" valign="top"
													    width="5%"><input type="text" name="cfg_search_id"
													                      value="<?php echo $this->escape($this->config->search_id); ?>"/>
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
			<?php // Load the setting comfirmation box form. ?>
			<?php echo $this->loadTemplateFile('setting'); ?>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
	</div>
</div>

