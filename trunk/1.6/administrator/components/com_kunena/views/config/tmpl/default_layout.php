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

$settings  = JHtml::_('kconfig.setting', $config->get('template'), 'template', 'Template', 'Select from the list of installed templates.', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('default_view'), 'default_view', 'Default View', 'Select the default view for the forums.', 'list');

echo JHtml::_('kconfig.section', JText::_('Template'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('date_format'), 'date_format', 'Date Format', 'Default date format for the forum.', 'text', 10, 0, 'e.g.: %m/%d/%Y <em><sub><a href="http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_date-format" target="_blank">Available Date Formats</a></sub></em>');
$settings .= JHtml::_('kconfig.setting', $config->get('enable_time_since'), 'enable_time_since', 'Time Since', 'Set to YES if you want main views use the time since display instead of dates and times.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('highlight_new'), 'highlight_new', 'Highlight New', 'Set to YES if you want to have new messages highlighted.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('enable_voting'), 'enable_voting', 'Enable Voting', 'Set to YES if you want to enable the voting functions.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('enable_ranking'), 'enable_ranking', 'Enable Ranking', 'Set to YES if you want to enable the ranking functions.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('enable_tagging'), 'enable_tagging', 'Enable Tagging', 'Set to YES if you want to enable the tagging functions.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('show_rank_images'), 'show_rank_images', 'Display Rank Images', 'Set to YES if you want to display rank specific images as part of the profile information displayed.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('show_user_stats'), 'show_user_stats', 'Show User Stats', 'Set to YES if you want to display user stas when profile information is displayed.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('show_edit_comments'), 'show_edit_comments', 'Show Edit Comments', 'Set to YES if you want to display comments when a message was edited.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('show_ip_tracking'), 'show_ip_tracking', 'Show IP Tracking', 'Set to YES if you want to display the IP tracking symbol below messages. Moderators and Admins always see this information.', 'yes/no');

echo JHtml::_('kconfig.section', JText::_('General'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('enable_announcements'), 'enable_announcements', 'Enable Announcements', 'Set to YES if you want to enable teh announcement feature.', 'yes/no');

echo JHtml::_('kconfig.section', JText::_('Announcements'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('rules_article'), 'rules_article', 'Rules Article Link', 'Link to the rules article of the forum. If empty no rules link will be shown.', 'text', 30);
$settings .= JHtml::_('kconfig.setting', $config->get('help_article'), 'help_article', 'Help Article Link', 'Link to the help article of the forum. If empty no help link will be shown.', 'text', 30);

echo JHtml::_('kconfig.section', JText::_('Links'), $settings );
