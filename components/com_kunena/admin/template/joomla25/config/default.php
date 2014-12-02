<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Config
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewConfig $this */
?>
<div id="kunena" class="admin override">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div id="j-sidebar-container" class="span2">
					<div id="sidebar">
						<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
					</div>
				</div>
				<div id="j-main-container" class="span10">
					<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
						<input type="hidden" name="view" value="config" />
						<input type="hidden" name="task" value="" />
						<?php echo JHtml::_( 'form.token' ) ?>

						<div class="data-container">
							<div class="tabbable">
								<ul class="nav nav-tabs">
									<li class="active"><a href="#tab_basics" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_BASICS'); ?></a></li>
									<li><a href="#tab_frontend" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_FRONTEND'); ?></a></li>
									<li><a href="#tab_users" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_USERS'); ?></a></li>
									<li><a href="#tab_security" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_SECURITY'); ?></a></li>
									<li><a href="#tab_avatars" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_AVATARS'); ?></a></li>
									<li><a href="#tab_uploads" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_UPLOADS'); ?></a></li>
									<li><a href="#tab_ranking" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_RANKING'); ?></a></li>
									<li><a href="#tab_bbcode" data-toggle="tab"><?php echo JText::_('COM_KUNENA_A_BBCODE'); ?></a></li>
									<li><a href="#tab_rss" data-toggle="tab"><?php echo JText::_('COM_KUNENA_ADMIN_RSS'); ?></a></li>
									<li><a href="#tab_extra" data-toggle="tab"><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_EXTRA'); ?></a></li>
								</ul>

								<div class="tab-content">
									<div class="tab-pane active" id="tab_basics">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_BOARD_TITLE') ?></td>
														<td><input type="text" name="cfg_board_title" value="<?php echo $this->escape ( $this->config->board_title ) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_BOARD_TITLE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_EMAIL') ?></td>
														<td><input type="text" name="cfg_email" value="<?php echo $this->escape($this->config->email) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_EMAIL_DESC2') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SEND_EMAILS') ?></td>
														<td><?php echo $this->lists ['send_emails'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SEND_EMAILS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_BOARD_OFFLINE') ?></td>
														<td><?php echo $this->lists ['board_offline'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_BOARD_OFFLINE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_BOARD_OFFLINE_MES') ?></td>
														<td colspan="2">
															<textarea name="cfg_offline_message" rows="3" cols="50"><?php echo $this->escape ( $this->config->offline_message ) ?></textarea>
														</td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_KUNENA_SESSION_TIMEOUT') ?>
														</td>
														<td><input type="text"
																 name="cfg_sessiontimeout"
																 value="<?php echo $this->escape($this->config->sessiontimeout);
																 ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_KUNENA_SESSION_TIMEOUT_DESC') ?>
														</td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS') ?></td>
														<td><?php echo $this->lists ['enablerss'] ?></td>
														<td><img src="<?php echo JUri::base(true) ?>/components/com_kunena/images/livemarks.png" alt="" /> <?php echo JText::_('COM_KUNENA_A_RSS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_DEBUG_MODE') ?></td>
														<td><?php echo $this->lists ['debug'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_DEBUG_MODE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CFG_FALLBACK_ENGLISH') ?></td>
														<td><?php echo $this->lists ['fallback_english'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_CFG_FALLBACK_ENGLISH_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_VERSION_CHECK') ?></td>
														<td><?php echo $this->lists ['version_check'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_VERSION_CHECK_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_TIME_TO_CREATEPAGE') ?></td>
														<td><?php echo $this->lists ['time_to_create_page'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_TIME_TO_CREATEPAGE_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>

										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_SEO_SETTINGS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SEF') ?></td>
														<td><?php echo $this->lists ['sef'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SEF_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CFG_ACCESS_COMPONENT') ?></td>
														<td><?php echo $this->lists ['access_component'] ?></td>
														<td><?php echo JText::sprintf('COM_KUNENA_CFG_ACCESS_COMPONENT_DESC', $this->lists ['componentUrl'], JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND')) ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>

										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_CACHING_SETTINGS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CFG_CACHE') ?></td>
														<td><?php echo $this->lists ['cache'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_CFG_CACHE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CFG_CACHE_TIME') ?></td>
														<td><?php echo $this->lists ['cache_time'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_CFG_CACHE_TIME_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_frontend">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_LOOKS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_THREADS') ?></td>
														<td><input type="text" name="cfg_threads_per_page" value="<?php echo $this->escape($this->config->threads_per_page) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_THREADS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_MESSAGES') ?></td>
														<td><input type="text" name="cfg_messages_per_page" value="<?php echo $this->escape($this->config->messages_per_page) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_MESSAGES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_MESSAGES_SEARCH') ?></td>
														<td><input type="text" name="cfg_messages_per_page_search" value="<?php echo $this->escape($this->config->messages_per_page_search) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_MESSAGES_DESC_SEARCH') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_HISTORY') ?></td>
														<td><?php echo $this->lists ['showhistory'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_HISTORY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_HISTLIM') ?></td>
														<td><input type="text" name="cfg_historylimit" value="<?php echo $this->escape($this->config->historylimit) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_HISTLIM_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CFG_POST_DATEFORMAT') ?></td>
														<td><?php echo $this->lists ['post_dateformat'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_CFG_POST_DATEFORMAT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CFG_POST_DATEFORMAT_HOVER') ?></td>
														<td><?php echo $this->lists ['post_dateformat_hover'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_CFG_POST_DATEFORMAT_HOVER_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWNEW') ?></td>
														<td><?php echo $this->lists ['shownew'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWNEW_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SHOW_ANNOUNCEMENT') ?></td>
														<td><?php echo $this->lists ['showannouncement'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SHOW_ANNOUNCEMENT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SHOW_AVATAR_ON_CAT') ?></td>
														<td><?php echo $this->lists ['avataroncat'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SHOW_AVATAR_ON_CAT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_LISTCAT_SHOW_MODERATORS') ?></td>
														<td><?php echo $this->lists ['listcat_show_moderators'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_LISTCAT_SHOW_MODERATORS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CATIMAGEPATH') ?></td>
														<td><input type="text" name="cfg_catimagepath" value="<?php echo $this->escape($this->config->catimagepath) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_CATIMAGEPATH_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST') ?></td>
														<td><?php echo $this->lists ['showchildcaticon'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_TAWIDTH') ?></td>
														<td><input type="text" name="cfg_rtewidth" value="<?php echo $this->escape($this->config->rtewidth) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_TAWIDTH_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_TAHEIGHT') ?></td>
														<td><input type="text" name="cfg_rteheight" value="<?php echo $this->escape($this->config->rteheight) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_TAHEIGHT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_FORUM_JUMP') ?></td>
														<td><?php echo $this->lists ['enableforumjump'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_FORUM_JUMP_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_REPORT') ?></td>
														<td><?php echo $this->lists ['reportmsg'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_REPORT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_ORDERING_SYSTEM') ?></td>
														<td><?php echo $this->lists ['ordering_system'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_REPORT_ORDERING_SYSTEM_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_HIDE_IP') ?></td>
														<td><?php echo $this->lists ['hide_ip'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_HIDE_IP_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_LATESTCATEGORY_IN') ?></td>
														<td><?php echo $this->lists ['latestcategory_in'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_LATESTCATEGORY_IN_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_LATESTCATEGORY') ?></td>
														<td><?php echo $this->lists ['latestcategory'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_LATESTCATEGORY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_TOPICICONS') ?></td>
														<td><?php echo $this->lists ['topicicons'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_TOPCIICONS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_ENABLELIGHTBOX') ?></td>
														<td><?php echo $this->lists ['lightbox'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_ENABLELIGHTBOX_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_SHOW_TOPICS_FROM_LAST_TIME') ?></td>
														<td><?php echo $this->lists ['show_list_time'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_SHOW_TOPICS_FROM_LAST_TIME_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_USER_SESSIONS_TYPE') ?></td>
														<td><?php echo $this->lists ['show_session_type'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_SESSIONS_TYPE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_USER_SESSIONS_START_TIME') ?></td>
														<td><input type="text" name="cfg_show_session_starttime" value="<?php echo $this->escape($this->config->show_session_starttime) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_SESSIONS_START_TIME_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_WAY_COUNT_USERS_USERLIST') ?></td>
														<td><?php echo $this->lists ['userlist_count_users'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_WAY_COUNT_USERS_USERLIST_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_TOPIC_LAYOUT') ?></td>
														<td><?php echo $this->lists ['topic_layout'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_TOPIC_LAYOUT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_ENABLE_THREADED_LAYOUTS') ?></td>
														<td><?php echo $this->lists ['enable_threaded_layouts'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_ENABLE_THREADED_LAYOUTS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_ATTACHMENT_LIMIT') ?></td>
														<td><input type="text" name="cfg_attachment_limit" value="<?php echo $this->escape($this->config->attachment_limit) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_ATTACHMENT_LIMIT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_PICKUP_CATEGORY') ?></td>
														<td><?php echo $this->lists ['pickup_category'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_PICKUP_CATEGORY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_ARTICLE_DISPLAY') ?></td>
														<td><?php echo $this->lists ['article_display'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_ARTICLE_DISPLAY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CFG_DEFAULT_SORT') ?></td>
														<td><?php echo $this->lists ['default_sort'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_CFG_DEFAULT_SORT_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_LENGTHS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SUBJECTLENGTH') ?></td>
														<td><input type="text" name="cfg_maxsubject" value="<?php echo $this->escape($this->config->maxsubject) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_SUBJECTLENGTH_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SIGNATURE') ?></td>
														<td><input type="text" name="cfg_maxsig" value="<?php echo $this->escape($this->config->maxsig) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_SIGNATURE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_PESONNALTEXT') ?></td>
														<td><input type="text" name="cfg_maxpersotext" value="<?php echo $this->escape($this->config->maxpersotext) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_PESONNALTEXT_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_users">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_USER_RELATED') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_DISPLAY_NAME') ?></td>
														<td><?php echo $this->lists ['username'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_USERNAME_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_ASK_EMAIL') ?></td>
														<td><?php echo $this->lists ['askemail'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_ASK_EMAIL_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWMAIL') ?></td>
														<td><?php echo $this->lists ['showemail'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWMAIL_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_USERSTATS') ?></td>
														<td><?php echo $this->lists ['showuserstats'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_USERSTATS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_KARMA') ?></td>
														<td><?php echo $this->lists ['showkarma'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_KARMA_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_THANKYOU') ?></td>
														<td><?php echo $this->lists ['showthankyou'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_THANKYOU_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_DISPLAY_THANKYOU_NUMBERS') ?></td>
														<td><input type="text" name="cfg_thankyou_max" class="ksm-field" value="<?php echo $this->escape($this->config->thankyou_max) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_DISPLAY_THANKYOU_NUMBERS') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_USER_EDIT') ?></td>
														<td><?php echo $this->lists ['useredit'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_USER_EDIT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_USER_EDIT_TIME') ?></td>
														<td><input type="text" name="cfg_useredittime" value="<?php echo $this->escape($this->config->useredittime) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_USER_EDIT_TIME_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_USER_EDIT_TIMEGRACE') ?></td>
														<td><input type="text" name="cfg_useredittimegrace" value="<?php echo $this->escape($this->config->useredittimegrace) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_USER_EDIT_TIMEGRACE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_USER_MARKUP') ?></td>
														<td><?php echo $this->lists ['editmarkup'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_USER_MARKUP_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_FAVORITES') ?></td>
														<td><?php echo $this->lists ['allowfavorites'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_FAVORITES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_GHOSTMESSAGE') ?></td>
														<td><?php echo $this->lists ['boxghostmessage'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_GHOSTMESSAGE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWBANNEDREASON_PROFILE') ?></td>
														<td><?php echo $this->lists ['showbannedreason'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWBANNEDREASON_PROFILE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_DISPLAY_IMGFILES_TAB_MANAGEMENT_PROFILE') ?></td>
														<td><?php echo $this->lists ['show_imgfiles_manage_profile'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_DISPLAY_IMGFILES_TAB_MANAGEMENT_PROFILE_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS') ?></td>
														<td><?php echo $this->lists ['allowsubscriptions'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_MAILFULL') ?></td>
														<td><?php echo $this->lists ['mailfull'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_MAILFULL_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_CATEGORY_SUBSCRIPTIONS') ?></td>
														<td><?php echo $this->lists ['category_subscriptions'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_CATEGORY_SUBSCRIPTIONS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_TOPIC_SUBSCRIPTIONS') ?></td>
														<td><?php echo $this->lists ['topic_subscriptions'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_TOPIC_SUBSCRIPTIONS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONSCHECKED') ?></td>
														<td><?php echo $this->lists ['subscriptionschecked'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SUBSCRIPTIONSCHECKED_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_COUNT') ?></td>
														<td><?php echo JHtml::_('select.integerlist',0, 100, 5, 'cfg_email_recipient_count', null, $this->escape($this->config->email_recipient_count)) ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_COUNT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY') ?></td>
														<td><?php echo $this->lists ['email_recipient_privacy'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_RECIPIENT_PRIVACY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_VISIBLE_ADDRESS') ?></td>
														<td><input type="text" name="cfg_email_visible_address" value="<?php echo $this->escape($this->config->email_visible_address) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_SUBSCRIPTIONS_EMAIL_VISIBLE_ADDRESS_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_security">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_SECURITY_SETTINGS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_PUBWRITE') ?></td>
														<td><?php echo $this->lists ['pubwrite'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_PUBWRITE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_HOLD_GUEST_POSTS') ?></td>
														<td><?php echo $this->lists ['hold_guest_posts'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_HOLD_GUEST_POSTS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ALLOWED') ?></td>
														<td><?php echo $this->lists ['userlist_allowed'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ALLOWED_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_PUBPROFILE') ?></td>
														<td><?php echo $this->lists ['pubprofile'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_PUBPROFILE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_REGISTERED_ONLY') ?></td>
														<td><?php echo $this->lists ['regonly'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_REG_ONLY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_HOLD_NEWUSERS_POSTS') ?></td>
														<td><input type="text" name="cfg_hold_newusers_posts" class="ksm-field" value="<?php echo $this->escape($this->config->hold_newusers_posts) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_HOLD_NEWUSERS_POSTS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_DELETEMESSAGE') ?></td>
														<td><?php echo $this->lists ['userdeletetmessage'] ?> </td>
														<td><?php echo JText::_('COM_KUNENA_A_DELETEMESSAGE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_MOD_SEE_DELETED') ?></td>
														<td><?php echo $this->lists ['mod_see_deleted'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_MOD_SEE_DELETED_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ALLOW_USERNAME_CHANGE') ?></td>
														<td><?php echo $this->lists ['usernamechange'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ALLOW_USERNAME_CHANGE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_FLOOD') ?></td>
														<td><input type="text" name="cfg_floodprotection" value="<?php echo $this->escape($this->config->floodprotection) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_FLOOD_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_MAILMOD') ?></td>
														<td><?php echo $this->lists ['mailmod'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_MAILMOD_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_MAILADMIN') ?></td>
														<td><?php echo $this->lists ['mailadmin'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_MAILADMIN_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IP_TRACKING') ?></td>
														<td><?php echo $this->lists ['iptracking'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_IP_TRACKING_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_STATSLINK_ALLOWED') ?></td>
														<td><?php echo $this->lists ['statslink_allowed'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_STATSLINK_ALLOWED_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_CAPTCHA_CONFIGURATION') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_CAPTCHA_GUESTS') ?></td>
														<td><?php echo $this->lists ['captcha'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_CAPTCHA_GUESTS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_CAPTCHA_POST_LIMIT') ?></td>
														<td><input type="text" name="cfg_captcha_post_limit" class="ksm-field" value="<?php echo $this->escape($this->config->captcha_post_limit) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_CAPTCHA_POST_LIMIT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RECAPTCHA_PUBLICKEY') ?></td>
														<td><input type="text" name="cfg_recaptcha_publickey" class="ksm-field-large" value="<?php echo $this->escape($this->config->recaptcha_publickey) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_RECAPTCHA_PUBLICKEY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RECAPTCHA_PRIVATEKEY') ?></td>
														<td><input type="text" name="cfg_recaptcha_privatekey" class="ksm-field-large" value="<?php echo $this->escape($this->config->recaptcha_privatekey) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_RECAPTCHA_PRIVATEKEY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RECAPTCHA_THEME') ?></td>
														<td><?php echo $this->lists ['recaptcha_theme'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RECAPTCHA_THEME_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_STOP_FORUM_SPAM_CONFIGURATION') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_STOP_FORUM_SPAM_KEY') ?></td>
														<td><input type="text" name="cfg_stopforumspam_key" class="ksm-field-large" value="<?php echo $this->escape($this->config->stopforumspam_key) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_STOP_FORUM_SPAM_KEY_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_avatars">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_AVATAR_SETTINGS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_AVATARUPLOAD') ?></td>
														<td><?php echo $this->lists ['allowavatarupload'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_AVATARUPLOAD_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_AVATARGALLERY') ?></td>
														<td><?php echo $this->lists ['allowavatargallery'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_AVATARGALLERY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_AVSIZE') ?></td>
														<td><input type="text" name="cfg_avatarsize" class="ksm-field" value="<?php echo $this->escape($this->config->avatarsize) ?>" /> kB</td>
														<td></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_AVATAR_QUALITY') ?></td>
														<td class="nowrap"><input type="text" name="cfg_avatarquality" class="ksm-field" value="<?php echo $this->escape($this->config->avatarquality) ?>" /> %</td>
														<td></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_uploads">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_IMAGE') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMAGE_UPLOAD') ?></td>
														<td><?php echo $this->lists ['image_upload'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_IMAGE_UPLOAD_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SHOWIMGFORGUEST') ?></td>
														<td><?php echo $this->lists ['showimgforguest'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SHOWIMGFORGUEST_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMAGEALLOWEDTYPES') ?></td>
														<td><input type="text" name="cfg_imagetypes" value="<?php echo $this->escape($this->config->imagetypes) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_IMAGEALLOWEDTYPES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMAGECHECKMIMETYPES') ?></td>
														<td><?php echo $this->lists ['checkmimetypes'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_IMAGECHECKMIMETYPES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMAGEALLOWEDMIMETYPES') ?></td>
														<td><input type="text" name="cfg_imagemimetypes" value="<?php echo $this->escape($this->config->imagemimetypes) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_IMAGEALLOWEDMIMETYPES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMGSIZE') ?></td>
														<td><input type="text" name="cfg_imagesize" class="ksm-field" value="<?php echo $this->escape($this->config->imagesize) ?>" /> kB</td>
														<td>
															<?php
															echo JText::sprintf('COM_KUNENA_A_IMGSIZE_DESC',
																ini_get('post_max_size'), ini_get('upload_max_filesize'),
																function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : '')
															?>
														</td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMGWIDTH') ?></td>
														<td><input type="text" name="cfg_imagewidth" class="ksm-field" value="<?php echo $this->escape($this->config->imagewidth) ?>" /> px</td>
														<td><?php echo JText::_('COM_KUNENA_A_IMGWIDTH_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMGHEIGHT') ?></td>
														<td><input type="text" name="cfg_imageheight" class="ksm-field" value="<?php echo $this->escape($this->config->imageheight) ?>" /> px</td>
														<td><?php echo JText::_('COM_KUNENA_A_IMGHEIGHT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMGTHUMBWIDTH') ?></td>
														<td><input type="text" name="cfg_thumbwidth" class="ksm-field" value="<?php echo $this->escape($this->config->thumbwidth) ?>" /> px</td>
														<td><?php echo JText::_('COM_KUNENA_A_IMGTHUMBWIDTH_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMGTHUMBHEIGHT') ?></td>
														<td><input type="text" class="ksm-field" name="cfg_thumbheight" value="<?php echo $this->escape($this->config->thumbheight) ?>" /> px</td>
														<td><?php echo JText::_('COM_KUNENA_A_IMGTHUMBHEIGHT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_IMGQUALITY') ?></td>
														<td><input type="text" name="cfg_imagequality" class="ksm-field" value="<?php echo $this->escape($this->config->imagequality) ?>" /> %</td>
														<td><?php echo JText::_('COM_KUNENA_A_IMGQUALITY_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_FILE') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_FILE_UPLOAD') ?></td>
														<td><?php echo $this->lists ['file_upload'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_FILE_UPLOAD_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SHOWFILEFORGUEST') ?></td>
														<td><?php echo $this->lists ['showfileforguest'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SHOWFILEFORGUEST_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_FILEALLOWEDTYPES') ?></td>
														<td><input type="text" name="cfg_filetypes" value="<?php echo $this->escape($this->config->filetypes) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_FILEALLOWEDTYPES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_FILESIZE') ?></td>
														<td><input type="text" name="cfg_filesize" class="ksm-field" value="<?php echo $this->escape($this->config->filesize) ?>" /> <?php echo JText::_('COM_KUNENA_A_FILESIZE_KB') ?></td>
														<td>
															<?php
															echo JText::sprintf('COM_KUNENA_A_FILESIZE_DESC',
																ini_get('post_max_size'), ini_get('upload_max_filesize'),
																function_exists('php_ini_loaded_file') ? php_ini_loaded_file() : '')
															?>
														</td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_ranking">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_RANKING_SETTINGS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RANKING') ?></td>
														<td><?php echo $this->lists ['showranking'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RANKING_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RANKINGIMAGES') ?></td>
														<td><?php echo $this->lists ['rankimages'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RANKINGIMAGES_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_bbcode">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_BBCODE_SETTINGS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_DISEMOTICONS') ?></td>
														<td><?php echo $this->lists ['disemoticons'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_DISEMOTICONS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWSPOILERTAG') ?></td>
														<td><?php echo $this->lists ['showspoilertag'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWSPOILERTAG_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWVIDEOTAG') ?></td>
														<td><?php echo $this->lists ['showvideotag'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWVIDEOTAG_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWEBAYTAG') ?></td>
														<td><?php echo $this->lists ['showebaytag'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SHOWEBAYTAG_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_EBAYLANGUAGECODE') ?></td>
														<td><input type="text" name="cfg_ebaylanguagecode" value="<?php echo $this->escape($this->config->ebaylanguagecode) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_EBAYLANGUAGECODE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLS') ?></td>
														<td><?php echo $this->lists ['trimlongurls'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLSFRONT') ?></td>
														<td><input type="text" name="cfg_trimlongurlsfront" value="<?php echo $this->escape($this->config->trimlongurlsfront) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLSFRONT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLSBACK') ?></td>
														<td><input type="text" name="cfg_trimlongurlsback" value="<?php echo $this->escape($this->config->trimlongurlsback) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_TRIMLONGURLSBACK_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_AUTOLINK') ?></td>
														<td><?php echo $this->lists ['autolink'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_AUTOLINK_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_AUTOEMBEDYOUTUBE') ?></td>
														<td><?php echo $this->lists ['autoembedyoutube'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_AUTOEMBEDYOUTUBE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_AUTOEMBEDEBAY') ?></td>
														<td><?php echo $this->lists ['autoembedebay'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_AUTOEMBEDEBAY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_HIGHLIGHTCODE') ?></td>
														<td><?php echo $this->lists ['highlightcode'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_HIGHLIGHTCODE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE') ?></td>
														<td><?php echo $this->lists ['bbcode_img_secure'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_COM_A_BBCODE_IMG_SECURE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_EBAY_AFFILIATE_ID') ?></td>
														<td><input type="text" name="cfg_ebay_affiliate_id" value="<?php echo $this->escape($this->config->ebay_affiliate_id) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_EBAY_AFFILIATE_ID_DESC') ?></td>
													</tr>
													<?php /*
													// TODO: If you uncomment this feature, please remove forced disable from the KunenaConfig class.
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_KEYWORDS') ?></td>
														<td><?php echo $this->lists ['keywords'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_KEYWORDS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_USER_KEYWORDS') ?></td>
														<td><?php echo $this->lists ['userkeywords'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_USER_KEYWORDS_DESC') ?></td>
													</tr>
													*/ ?>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_rss">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_ADMIN_RSS_SETTINGS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_TYPE') ?></td>
														<td><?php echo $this->lists ['rss_type'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_TYPE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_SPEC') ?></td>
														<td><?php echo $this->lists ['rss_specification'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_SPEC_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_TIMELIMIT') ?></td>
														<td><?php echo $this->lists ['rss_timelimit'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_TIMELIMIT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_LIMIT') ?></td>
														<td><input type="text" name="cfg_rss_limit" value="<?php echo $this->escape($this->config->rss_limit) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_LIMIT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_INCLUDED_CATEGORIES') ?></td>
														<td><input type="text" name="cfg_rss_included_categories" value="<?php echo $this->escape($this->config->rss_included_categories) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_INCLUDED_CATEGORIES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_EXCLUDED_CATEGORIES') ?></td>
														<td><input type="text" name="cfg_rss_excluded_categories" value="<?php echo $this->escape($this->config->rss_excluded_categories) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_EXCLUDED_CATEGORIES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_ALLOW_HTML') ?></td>
														<td><?php echo $this->lists ['rss_allow_html'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_ALLOW_HTML_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT') ?></td>
														<td><?php echo $this->lists ['rss_author_format'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_AUTHOR_FORMAT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_AUTHOR_IN_TITLE') ?></td>
														<td><?php echo $this->lists ['rss_author_in_title'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_AUTHOR_IN_TITLE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_CONFIG_RSS_CHARACTERS_COUNT') ?></td>
														<td><?php echo $this->lists ['rss_word_count'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_CONFIG_RSS_CHARACTERS_COUNT_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_OLD_TITLES') ?></td>
														<td><?php echo $this->lists ['rss_old_titles'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_OLD_TITLES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_CACHE') ?></td>
														<td><?php echo $this->lists ['rss_cache'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_CACHE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_FEEDBURNER_URL') ?></td>
														<td><input type="text" class="ksm-field-large" name="cfg_rss_feedburner_url" value="<?php echo $this->escape($this->config->rss_feedburner_url) ?>"/></td>
														<td><?php echo JText::_('COM_KUNENA_A_RSS_FEEDBURNER_URL_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>

									<div class="tab-pane" id="tab_extra">
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ROWS') ?></td>
														<td><input type="text" name="cfg_userlist_rows" value="<?php echo $this->escape($this->config->userlist_rows) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE') ?></td>
														<td><?php echo $this->lists ['userlist_online'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR') ?></td>
														<td><?php echo $this->lists ['userlist_avatar'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_NAME') ?></td>
														<td><?php echo $this->lists ['userlist_name'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_NAME_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_POSTS') ?></td>
														<td><?php echo $this->lists ['userlist_posts'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_KARMA') ?></td>
														<td><?php echo $this->lists ['userlist_karma'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL') ?></td>
														<td><?php echo $this->lists ['userlist_email'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE') ?></td>
														<td><?php echo $this->lists ['userlist_joindate'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE') ?></td>
														<td><?php echo $this->lists ['userlist_lastvisitdate'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_HITS') ?></td>
														<td><?php echo $this->lists ['userlist_userhits'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_SHOW_SUPERADMINS_IN_USERLIST') ?></td>
														<td><?php echo $this->lists ['superadmin_userlist'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_SHOW_SUPERADMINS_IN_USERLIST_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_STATS') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SHOWWHOIS') ?></td>
														<td><?php echo $this->lists ['showwhoisonline'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SHOWWHOISDESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_SHOWSTATS') ?></td>
														<td><?php echo $this->lists ['showstats'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_SHOWSTATSDESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_STATSGENERAL') ?></td>
														<td><?php echo $this->lists ['showgenstats'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_STATSGENERALDESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_USERSTATS') ?></td>
														<td><?php echo $this->lists ['showpopuserstats'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_USERSTATSDESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_USERNUM') ?></td>
														<td><input type="text" name="cfg_popusercount" value="<?php echo $this->escape($this->config->popusercount) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_USERNUM') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_USERPOPULAR') ?></td>
														<td><?php echo $this->lists ['showpopsubjectstats'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_USERPOPULARDESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_NUMPOP') ?></td>
														<td><input type="text" name="cfg_popsubjectcount" value="<?php echo $this->escape($this->config->popsubjectcount) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_NUMPOP') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_POLLSSTATS') ?></td>
														<td><?php echo $this->lists ['showpoppollstats'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_POLLSTATSDESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_POLLSPOP') ?></td>
														<td><input type="text" name="cfg_poppollscount" value="<?php echo $this->escape($this->config->poppollscount) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_POLLSPOP') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_THANKSSTATS') ?></td>
														<td><?php echo $this->lists ['showpopthankyoustats'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_THANKSSTATSDESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_THANKSPOP') ?></td>
														<td><input type="text" name="cfg_popthankscount" value="<?php echo $this->escape($this->config->popthankscount) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_THANKSPOP') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
										<fieldset>
											<legend><?php echo JText::_('COM_KUNENA_A_POLL_TITLE') ?></legend>
											<table class="table table-striped">
												<thead>
													<tr>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_TITLE') ?></th>
														<th width="25%"><?php echo JText::_('COM_KUNENA_TABLEHEAD_OPTION') ?></th>
														<th><?php echo JText::_('COM_KUNENA_TABLEHEAD_DESCRIPTION') ?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_ENABLED') ?></td>
														<td><?php echo $this->lists ['pollenabled'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_ENABLED_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_NUMBER_OPTIONS') ?></td>
														<td><input type="text" name="cfg_pollnboptions" value="<?php echo $this->escape($this->config->pollnboptions) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_NUMBER_OPTIONS_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_TIME_VOTES') ?></td>
														<td><input type="text" name="cfg_polltimebtvotes" value="<?php echo $this->escape($this->config->polltimebtvotes) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_TIME_VOTES_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_NUMBER_VOTES_BY_USER') ?></td>
														<td><input type="text" name="cfg_pollnbvotesbyuser" value="<?php echo $this->escape($this->config->pollnbvotesbyuser) ?>" /></td>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_NUMBER_VOTES_BY_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_ALLOW_ONE_VOTE') ?></td>
														<td><?php echo $this->lists ['pollallowvoteone'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_ALLOW_ONE_VOTE_DESC') ?></td>
													</tr>
													<tr>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_SHOW_USER_LIST') ?></td>
														<td><?php echo $this->lists ['pollresultsuserslist'] ?></td>
														<td><?php echo JText::_('COM_KUNENA_A_POLL_SHOW_USER_LIST_DESC') ?></td>
													</tr>
												</tbody>
											</table>
										</fieldset>
									</div>
								</div>
							</div>
						</div>
					</form>
					<?php //Load the setting comfirmation box form. ?>
					<?php echo $this->loadTemplateFile('setting'); ?>
				</div>
				<div class="pull-right small">
					<?php echo KunenaVersion::getLongVersionHTML(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
