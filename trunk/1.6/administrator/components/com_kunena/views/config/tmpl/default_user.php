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

$settings  = JHtml::_('kconfig.setting', $config->get('user_name_display'), 'user_name_display', 'User Name Display', 'Select how the username should be displayed', 'list');
$settings .= JHtml::_('kconfig.setting', $config->get('enable_name_change'), 'enable_name_change', 'Enable Name Change', 'Set to YES to enable users to change their user name.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('enable_signature'), 'enable_signature', 'Enable Signature', 'Set to YES to enable users to have a signature.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('max_sig_length'), 'max_sig_length', 'Signature Length', 'Maximum length of the user signature.', 'text', 5, 0, 'characters <em><sub>(0 for unlimited)</sub></em>');
$settings .= JHtml::_('kconfig.setting', $config->get('max_sig_img_width'), 'max_sig_img_width', 'Signature Image Width', 'Maximum width of signature images.', 'text', 5, 0, 'px <em><sub>(0 for unlimited)</sub></em>');
$settings .= JHtml::_('kconfig.setting', $config->get('max_sig_img_height'), 'max_sig_img_height', 'Signature Image Height', 'Maximum height of signature images.', 'text', 5, 0, 'px <em><sub>(0 for unlimited)</sub></em>');

echo JHtml::_('kconfig.section', JText::_('Profile'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('enable_favorites'), 'enable_favorites', 'Enable Favorites', 'Set to YES to enable users to set favorites', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('enable_subscriptions'), 'enable_subscriptions', 'Enable Subscriptions', 'Set to YES to enable users to subscribe to forums and threads', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('auto_subscribe'), 'auto_subscribe', 'Auto Subscribe', 'Set to YES users should be automatically subscribed to threads they reply to.', 'yes/no');

echo JHtml::_('kconfig.section', JText::_('Favorites'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('enable_avatars'), 'enable_avatars', 'Enable Avatars', 'Set to YES to enable avatars.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('show_avatar_gallery'), 'show_avatar_gallery', 'Show Avatar Galery', 'Set to YES to show a gallery of avatars for users to choose from.', 'yes/no');

echo JHtml::_('kconfig.section', JText::_('Avatars'), $settings );