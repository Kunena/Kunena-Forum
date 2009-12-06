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

$settings  = JHtml::_('kconfig.setting', $config->get('allow_user_edits'), 'allow_user_edits', 'Allow User Edits', 'Set to YES to allow users to edit their own posts', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('user_edit_time'), 'user_edit_time', 'User Edit Time', 'Amount of seconds a user can edit their own posts. Set to 0 for unlimited time', 'text', 5, 0, 'secs <em><sub>(0 for unlimited)</sub></em>');
$settings .= JHtml::_('kconfig.setting', $config->get('enable_guest_posts'), 'enable_guest_posts', 'Enable Guest Posts', 'Set to YES if you want to allow unregistered guests to post in the forum.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('require_guest_email'), 'require_guest_email', 'Require Guest eMail', 'Set to YES if guests are rquired to provide an email address', 'yes/no');

$settings .= JHtml::_('kconfig.setting', $config->get('file_upload_user_level'), 'file_upload_user_level', 'File Upload User Level', 'Minimum user level required to enable file uploads as part of posts', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('image_upload_user_level'), 'image_upload_user_level', 'Image Upload User Level', 'Minimum user level required to enable image uploads as part of posts', 'list');

echo JHtml::_('kconfig.section', JText::_('Permissions'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('enable_powerusers'), 'enable_powerusers', 'Enable Power Users', 'Enable Power User Functions. This allows you to assign simple moderation tasks like spam alerting to select users that aren\'t moderators.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('powerusers_flag_spam'), 'powerusers_flag_spam', 'Flag Spam', 'Enables Power Users to hide spam messages. Moderators get notification.', 'yes/no');

echo JHtml::_('kconfig.section', JText::_('Power Users'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('message_reporting'), 'message_reporting', 'Message Reporting', 'Enable or Disable reporting link on individual posts within forums', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('spam_protection'), 'spam_protection', 'Spam Protection', 'Enable or Disable spam protection for the forum', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('spam_protection_level'), 'spam_protection_level', 'Spam Protection Level', 'Select from various levels of spam protection', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('spam_min_post_count'), 'spam_min_post_count', 'Spam Min Post Count', 'Post count below which spam protection is enable for the selected user groups', 'text', 5, 0, 'posts <em><sub>(0 to disable)</sub></em>');
$settings .= JHtml::_('kconfig.setting', $config->get('spam_min_post_action'), 'spam_min_post_action', 'Spam Min Post Action', 'Select level of spam protection for users with post counts below above setting', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('flood_protection'), 'flood_protection', 'Flood Protection', 'Minimum amount of time [sec] between posts for a given user', 'text', 5, 0, 'secs <em><sub>(0 to disable)</sub></em>');

echo JHtml::_('kconfig.section', JText::_('Spam Protection'), $settings );
