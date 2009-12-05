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

$settings  = JHtml::_('kconfig.setting', $config->get('enable_announcements'), 'enable_announcements', 'Enable Announcements', 'Set to YES if you want to enable teh announcement feature.', 'yes/no');

echo JHtml::_('kconfig.section', JText::_('Announcements'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('highlight_new'), 'highlight_new', 'Highlight New', 'Set to YES if you want to have new messages highlighted.', 'yes/no');
$settings .= JHtml::_('kconfig.setting', $config->get('template'), 'template', 'Template', 'Select from the list of installed templates.', 'list');

echo JHtml::_('kconfig.section', JText::_('Template'), $settings );

$settings  = JHtml::_('kconfig.setting', $config->get('rules_article'), 'rules_article', 'Rules Article Link', 'Link to the rules article of the forum. If empty no rules link will be shown.', 'text', 30);
$settings .= JHtml::_('kconfig.setting', $config->get('help_article'), 'help_article', 'Help Article Link', 'Link to the help article of the forum. If empty no help link will be shown.', 'text', 30);

echo JHtml::_('kconfig.section', JText::_('Links'), $settings );
