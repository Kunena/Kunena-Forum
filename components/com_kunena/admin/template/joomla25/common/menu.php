<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Load Mootools
JHtml::_('behavior.framework', true);
JHtml::_('script','system/multiselect.js',false,true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$view = JRequest::getCmd('view', 'cpanel');
?>

<!-- Main navigation -->
<ul class="nav nav-list">
	<li<?php if ($view == 'cpanel') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena'); ?>"><i class="icon-22-cp"></i> <?php echo JText::_('COM_KUNENA_MENU_DESC_DASHBOARD'); ?></a></li>
	<li<?php if ($view == 'categories' || $view == 'category') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=categories'); ?>"><i class="icon-22-categories"></i> <?php echo JText::_('COM_KUNENA_C_FORUM'); ?></a></li>
	<li<?php if ($view == 'users' || $view == 'user') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=users'); ?>"><i class="icon-22-users"></i> <?php echo JText::_('COM_KUNENA_C_USER'); ?></a></li>
	<li<?php if ($view == 'attachments' || $view == 'attachment') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=attachments'); ?>"><i class="icon-22-files"></i> <?php echo JText::_('COM_KUNENA_MENU_DESC_FILEMANAGER'); ?></a></li>
	<li<?php if ($view == 'smilies' || $view == 'smiley') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=smilies'); ?>"><i class="icon-22-smilies"></i> <?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER'); ?></a></li>
	<li<?php if ($view == 'ranks' || $view == 'rank') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=ranks'); ?>"><i class="icon-22-ranks"></i> <?php echo JText::_('COM_KUNENA_A_RANK_MANAGER'); ?></a></li>
	<li<?php if ($view == 'templates' || $view == 'template') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=templates'); ?>"><i class="icon-22-template"></i> <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?></a></li>
	<li<?php if ($view == 'config') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=config'); ?>"><i class="icon-22-config"></i> <?php echo JText::_('COM_KUNENA_MENU_DESC_CONFIGURATION'); ?></a></li>
	<li<?php if ($view == 'plugins') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=plugins'); ?>"><i class="icon-22-pluginsmanager"></i> <?php echo JText::_('COM_KUNENA_PLUGIN_MANAGER'); ?></a></li>
	<li<?php if ($view == 'tools') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=tools'); ?>"><i class="icon-22-tools"></i> <?php echo JText::_('COM_KUNENA_A_VIEW_TOOLS'); ?></a></li>
	<li<?php if ($view == 'trash') echo ' class="active"';?>><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=trash'); ?>"><i class="icon-22-trash"></i> <?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?></a></li>
</ul>
<?php /*
<div id="kadmin-menu">
	<a class="kadmin-mainmenu icon-cp-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>"><?php echo JText::_('COM_KUNENA_CP'); ?></a>
	<a class="kadmin-mainmenu icon-config-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config') ?>"><?php echo JText::_('COM_KUNENA_C_KCONFIG'); ?></a>
	<a class="kadmin-mainmenu icon-adminforum-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories') ?>"><?php echo JText::_('COM_KUNENA_C_FORUM'); ?></a>
	<a class="kadmin-mainmenu icon-profiles-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users') ?>"><?php echo JText::_('COM_KUNENA_C_USER'); ?></a>
	<a class="kadmin-mainmenu icon-template-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?></a>
	<a class="kadmin-mainmenu icon-smilies-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=smilies') ?>"><?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER'); ?></a>
	<a class="kadmin-mainmenu icon-ranks-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=ranks') ?>"><?php echo JText::_('COM_KUNENA_A_RANK_MANAGER'); ?></a>
	<a class="kadmin-mainmenu icon-files-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=attachments') ?>"><?php echo JText::_('COM_KUNENA_ATTACHMENTS_VIEW'); ?></a>
	<!-- a class="kadmin-mainmenu icon-topicicons-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=topicicons') ?>"><?php echo JText::_('COM_KUNENA_A_TOPICICONS_MANAGER'); ?></a -->
	<a class="kadmin-mainmenu icon-trash-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trash') ?>"><?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?></a>
	<a class="kadmin-mainmenu icon-prune-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"><?php echo JText::_('COM_KUNENA_A_VIEW_TOOLS'); ?></a>
	<a class="kadmin-mainmenu icon-stats-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=stats') ?>"><?php echo JText::_('COM_KUNENA_STATS_GEN_STATS'); ?></a>
	<a class="kadmin-mainmenu icon-systemreport-sm" href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=report') ?>"><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></a>
	<a class="kadmin-mainmenu icon-pluginsmanager-sm" href="<?php echo JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=kunena') ?>"><?php echo JText::_('COM_KUNENA_PLUGIN_MANAGER'); ?></a>
	<a class="kadmin-mainmenu icon-support-sm" href="http://www.kunena.org" target="_blank"><?php echo JText::_('COM_KUNENA_C_SUPPORT'); ?></a>
</div>
 */?>
