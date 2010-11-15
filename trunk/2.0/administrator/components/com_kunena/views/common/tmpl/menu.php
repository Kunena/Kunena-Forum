<?php
/**
 * @version		$Id: api.php 3251 2010-08-21 08:07:08Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined('_JEXEC') or die;
?>
<div id="kadmin-menu">
	<a class="kadmin-mainmenu icon-cp-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena"><?php echo JText::_('COM_KUNENA_CP'); ?></a>
	<a class="kadmin-mainmenu icon-config-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showconfig"><?php echo JText::_('COM_KUNENA_C_FBCONFIG'); ?></a>
	<a class="kadmin-mainmenu icon-adminforum-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=categories"><?php echo JText::_('COM_KUNENA_C_FORUM'); ?></a>
	<a class="kadmin-mainmenu icon-profiles-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showprofiles"><?php echo JText::_('COM_KUNENA_C_USER'); ?></a>
	<a class="kadmin-mainmenu icon-template-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showTemplates"><?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?></a>
	<a class="kadmin-mainmenu icon-smilies-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showsmilies"><?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER'); ?></a>
	<a class="kadmin-mainmenu icon-ranks-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=ranks"><?php echo JText::_('COM_KUNENA_RANK_MANAGER'); ?></a>
	<a class="kadmin-mainmenu icon-files-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseFiles"><?php echo JText::_('COM_KUNENA_C_FILES'); ?></a>
	<a class="kadmin-mainmenu icon-images-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseImages"><?php echo JText::_('COM_KUNENA_C_IMAGES'); ?></a>
	<a class="kadmin-mainmenu icon-prune-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=pruneforum"><?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?></a>
	<a class="kadmin-mainmenu icon-syncusers-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=syncusers"><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></a>
	<a class="kadmin-mainmenu icon-recount-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=recount"><?php echo JText::_('COM_KUNENA_RECOUNTFORUMS'); ?></a>
	<a class="kadmin-mainmenu icon-trash-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showtrashview"><?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?></a>
	<a class="kadmin-mainmenu icon-stats-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showstats"><?php echo JText::_('COM_KUNENA_STATS_GEN_STATS'); ?></a>
	<a class="kadmin-mainmenu icon-systemreport-sm" href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showsystemreport"><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></a>
	<a class="kadmin-mainmenu icon-support-sm" href="http://www.kunena.org" target="_blank"><?php echo JText::_('COM_KUNENA_C_SUPPORT'); ?></a>
</div>
