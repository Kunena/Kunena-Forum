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

$settings  = JHtml::_('kconfig.setting', $this, 'enabl_guest_posts', 'Enable Guest Posts::Set to YES if you want to allow unregistered guests to post in the forum.', 'Enable Guest Posts', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $this, 'allow_user_edits', 'Allow User Edits::Set to YES to allow users to edit their own posts', 'Allow User Edits', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $this, 'user_edit_time', 'User Edit Time::Amount of seconds a user can edit their own posts. Set to 0 for unlimited time', 'User Edit Time', 'text', 5);

echo JHtml::_('kconfig.section', JText::_('Permissions'), $settings );
