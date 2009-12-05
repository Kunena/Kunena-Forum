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

$settings  = JHtml::_('kconfig.setting', $config->get('enable_cron'), 'enable_cron', 'Enable cron', 'Enable cron processing. Removes email, notifications and other processing from user inline processing. Cron function must be setup properly', 'yes/no');
$settings .= JHtml::_('kconfig.setting', NULL, NULL, NULL, 'If enabled, please make sure that ==TODO: INSERT URL HERE== is setup with through cron.', 'info');

echo JHtml::_('kconfig.section', 'Automation', $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('login_link'), 'login_link', 'Login Link', 'Login Link override.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', $config->get('logout_link'), 'logout_link', 'Logout Link', 'Logout Link override.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', $config->get('password_link'), 'password_link', 'Password Link', 'Password Link override', 'text', 60);
$settings .= JHtml::_('kconfig.setting', $config->get('register_link'), 'register_link', 'Register Link', 'Register Link override', 'text', 60);
$settings .= JHtml::_('kconfig.setting', NULL, NULL, NULL, 'Any non-empty settings override the default behavior of Kunena.', 'info');

echo JHtml::_('kconfig.section', 'Links', $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('avatar_library_path'), 'avatar_library_path', 'Avatar Library Path', 'Avatar library path override.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', $config->get('avatar_upload_path'), 'avatar_upload_path', 'Avatar Upload Path', 'Avatar upload path override.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', $config->get('file_upload_path'), 'file_upload_path', 'File Upload Path', 'File upload path override.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', $config->get('image_upoad_path'), 'image_upoad_path', 'Image Upload Path', 'Image upload path override.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', $config->get('category_image_path'), 'category_image_path', 'Category Image Path', 'Category image path override.', 'text', 60);
$settings .= JHtml::_('kconfig.setting', NULL, NULL, NULL, 'Any non-empty settings override the default behavior of Kunena.', 'info');

echo JHtml::_('kconfig.section', 'Paths', $settings );
