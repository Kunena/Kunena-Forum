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

$settings  = JHtml::_('kconfig.setting', $config->get('communication_type'), 'communication_type', 'Communication Type', 'Select between plain text and HTML for the default communication type.', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('max_daily_email'), 'max_daily_email', 'Max Daily eMail', 'Maximum number of daily eMail sent to a user. Set to 0 for unlimited.', 'text', 4, 0, 'emails/user/day <em><sub>(0 for unlimited)</sub></em>');
$settings .= JHtml::_('kconfig.setting', $config->get('reinvite_frequency'), 'reinvite_frequency', 'Re-Invite Frequency', 'Days without activity before a re-invitation message is sent. 0 to disable.', 'text', 4, 0, 'days <em><sub>(0 to disable)</sub></em>');
$settings .= JHtml::_('kconfig.setting', $config->get('reinvite_template'), 'reinvite_template', 'Re-Invite Template', 'eMail template for re-invite. These are generated when a user has not visited the forum for the number of days configured above.', 'editor', 40, 20);

echo JHtml::_('kconfig.section', 'General', $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('subscription_type'), 'subscription_type', 'Subscription Type', 'Select between individual, first individual, daily or weekly summary', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('subscription_message_text'), 'subscription_message_text', 'Include Message Text', 'Enabl to include message text in subscription notification.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('subscription_template'), 'subscription_template', 'Subscription Template', 'Format the template to your specific needs throught. Default Joomla editor.', 'editor', 40, 20);

echo JHtml::_('kconfig.section', 'Subscriptions', $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('notify_adminstrators'), 'notify_adminstrators', 'Notify Adminstrators', 'Select actions that trigger a notification for Administrators.', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('notify_moderators'), 'notify_moderators', 'Notify Moderators', 'Select actions that trigger a notification for Moderators.', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('notification_type'), 'notification_type', 'Notification Type', 'Select between email and PM for admin and moderator notifications.', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('notification_template'), 'notification_template', 'Notification Template', 'eMail template for Admins and Moderators for select user events.', 'editor', 40, 20);

echo JHtml::_('kconfig.section', 'Notifications', $settings );
