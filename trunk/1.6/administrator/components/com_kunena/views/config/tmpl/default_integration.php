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

echo '<table><tr><td width="50%" valign="top">';

$settings  = JHtml::_('kconfig.setting', $config->get('user_profile_integration'), 'user_profile_integration', 'User Profiles', 'Select from the list of possible integration options', 'list', $this->option_lists['profile_integration'], 1);
$settings .= JHtml::_('kconfig.setting', $config->get('avatar_integration'), 'avatar_integration', 'Avatars', 'Select from the list of possible integration options', 'list', $this->option_lists['avatar_integration'], 1);
$settings .= JHtml::_('kconfig.setting', $config->get('pm_integration'), 'pm_integration', 'Private Messaging', 'Select from the list of possible integration options', 'list', $this->option_lists['pm_integration'], 1);
$settings .= JHtml::_('kconfig.setting', $config->get('userpoints_integration'), 'userpoints_integration', 'User Points', 'Select from the list of possible integration options', 'list', $this->option_lists['userpoints_integration'], 1);
$settings .= JHtml::_('kconfig.setting', NULL, NULL, NULL, 'In order to enable 3rd party component integration, additional components must be installed on your system. The integration options do not install these 3rd party packages and components for you.', 'info');
$settings .= JHtml::_('kconfig.setting', $config->get('private_forums_root'), 'private_forums_root', 'Private Forums Root', 'Root category for private and group forums.', 'list', $this->option_lists['private_forums_root'], 1);

echo JHtml::_('kconfig.section', JText::_('General'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('google_maps_api_key'), 'google_maps_api_key', 'Google Maps API Key', 'The API Key from google that allows you to display google maps on your site.', 'text', 30);
$settings .= JHtml::_('kconfig.setting', NULL, NULL, NULL, 'For more information about Google maps please visit <a href="http://code.google.com/apis/maps/" target="_blank">code.google.com/apis/maps/</a>.', 'info');

echo JHtml::_('kconfig.section', JText::_('Google Maps'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('recaptha_public_key'), 'recaptha_public_key', 'Public Key', 'Enter reCAPTCHA public key. For details see www.recapthca.com', 'text', 30);
$settings .= JHtml::_('kconfig.setting', $config->get('recaptha_private_key'), 'recaptha_private_key', 'Private Key', 'Enter reCAPTCHA private key. For details see www.recapthca.com', 'text', 30);
$settings .= JHtml::_('kconfig.setting', NULL, NULL, NULL, 'For more information about reCAPTCHA please visit <a href="http://www.recaptcha.com" target="_blank">www.recaptcha.com</a>.', 'info');

echo JHtml::_('kconfig.section', JText::_('reCAPTCHA'), $settings );

echo '</td><td valign="top">';

$settings  = JHtml::_('kconfig.setting', $config->get('jomsocial_activity_stream'), 'jomsocial_activity_stream', 'Activity Stream', 'Enable JomSocial activity stream integration', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('jomsocial_activity_create'), 'jomsocial_activity_create', 'Create Thread', 'Log New threads in JomSocial Activity Stream', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('jomsocial_activity_reply'), 'jomsocial_activity_reply', 'Reply Thread', 'Log replies in JomSocial Activity Stream', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('jomsocial_user_points'), 'jomsocial_user_points', 'User Points', 'Enable JomSocial user points integration', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('jomsocial_private_forums'), 'jomsocial_private_forums', 'Private Forums', 'Enable JomSocial private forum integartion for groups.', 'yes/no');

echo JHtml::_('kconfig.section', JText::_('JomSocial'), $settings );

$settings  = '';

echo JHtml::_('kconfig.section', JText::_('Community Builder'), $settings );

$settings  = '';

echo JHtml::_('kconfig.section', JText::_('Alpha Userpoints'), $settings );

echo '</td></tr></table>';
