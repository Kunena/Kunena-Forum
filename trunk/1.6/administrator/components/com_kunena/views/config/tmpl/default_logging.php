<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

$config = $this->options;

$settings  = JHtml::_('kconfig.setting', $config->get('enable_logging'), 'enable_logging', 'Enable Logging', 'Set to YES to enable event logging.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('logging_history'), 'logging_history', 'Logging History', 'Number of days to keep logging history.', 'text', 5, 0, 'days <em><sub>(0 for unlimited)</sub></em>');
$settings .= JHtml::_('kconfig.setting', $config->get('advanced_user_tracking'), 'advanced_user_tracking', 'Adanced User Tracking', 'Set to YES to enable advanced user tracking. It combines IP based and cookie based tracking to better identify annonymous users.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('guest_logging'), 'guest_logging', 'Guest Logging', 'Set to YES to enable logging of guest events.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('exclude_ips'), 'exclude_ips', 'Exclude IPs', 'Comma seperated list of IPs to exclude.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', $config->get('exclude_useragents'), 'exclude_useragents', 'Exclude Useragents', 'Comma seperated list of useragents to exclude.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', NULL, NULL, NULL, 'Please use <strong>extreme caution</strong> when enabling logging! Data volumes can be extreme on larger sites.', 'info');

echo JHtml::_('kconfig.section', JText::_('General'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('log_start_thread'), 'log_start_thread', 'Start Thread', 'Set to YES to log the creation of a new thread.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('log_reply_thread'), 'log_reply_thread', 'Reply Thread', 'Set to YES to log replies to an existing thread.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('log_edit_message'), 'log_edit_message', 'Edit Message', 'Set to YES to log modifications to an existing message.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('log_delete_message'), 'log_delete_message', 'Delete Message', 'Set to YES to log deletes of messages and threads.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('log_tag_message'), 'log_tag_message', 'Tag Message', 'Set to YES to log tagging of messages and threads.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('log_uploads'), 'log_uploads', 'Uploads', 'Set to YES to log uploads of files and images.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('log_mod_tasks'), 'log_mod_tasks', 'Moderator Tasks', 'Set to YES to log moderator tasks.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', NULL, NULL, NULL, 'Please use <strong>extreme caution</strong> when enabling logging! Data volumes can be extreme on larger sites.', 'info');

echo JHtml::_('kconfig.section', JText::_('Events'), $settings );
