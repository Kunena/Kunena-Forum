<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage CPanel
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$document = JFactory::getDocument();
$document->addStyleSheet( JUri::base(true).'/components/com_kunena/media/css/cpanel.css' );
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
	<div class="kadmin-right">
<div class="kadmin-welcome">
	<h3><?php echo JText::_('COM_KUNENA_WELCOME');?></h3>
	<p><?php echo JText::_('COM_KUNENA_WELCOME_DESC');?></p>
</div>
<div style="border:1px solid #ddd; background:#FBFBFB;">
	<table class="thisform">
		<tr class="thisform">
			<td width="100%" valign="top" class="thisform">
				<div id="cpanel">
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=config') ?>" title="<?php echo JText::_('COM_KUNENA_C_KCONFIGDESC');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/config.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_KCONFIG'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=categories') ?>" title="<?php echo JText::_('COM_KUNENA_C_FORUMDESC');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/categories.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_FORUM'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users') ?>" title="<?php echo JText::_('COM_KUNENA_C_USERDESC');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/users.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_USER'); ?> </span> </a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>" title="<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/templates.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=smilies') ?>" title="<?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/smileys.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER');?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=ranks') ?>" title="<?php echo JText::_('COM_KUNENA_A_RANK_MANAGER');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/ranks.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_RANK_MANAGER'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=attachments') ?>" title="<?php echo JText::_('COM_KUNENA_ATTACHMENTS_VIEW');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/files.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_ATTACHMENTS_VIEW'); ?> </span></a> </div>
					</div>
					<!-- div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=topicicons') ?>" title="<?php echo JText::_('COM_KUNENA_A_TOPICICONS_MANAGER');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/topicicons.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_TOPICICONS_MANAGER'); ?> </span></a> </div>
					</div -->
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trash') ?>" title="<?php echo JText::_('COM_KUNENA_TRASH_VIEW');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/trash.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" title="<?php echo JText::_('COM_KUNENA_A_VIEW_TOOLS');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/prune.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_VIEW_TOOLS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=stats') ?>" title="<?php echo JText::_('COM_KUNENA_STATS_GEN_STATS');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/stats.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_STATS_GEN_STATS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=report') ?>" title="<?php echo JText::_('COM_KUNENA_REPORT_SYSTEM');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/report.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=kunena') ?>" title="<?php echo JText::_('COM_KUNENA_PLUGINS_MANAGER');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/pluginsmanager.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_PLUGINS_MANAGER'); ?> </span></a> </div>
					</div>
					<?php if ( $this->config->version_check && JFactory::getUser()->authorise('core.manage', 'com_installer')) : ?>
					<div class="icon-container">
					<?php
					// FIXME: add LiveUpdate back
					/*
						require_once KPATH_ADMIN.'/liveupdate/liveupdate.php';
						echo LiveUpdate::getIcon();
					*/
					?>
					</div>
					<?php endif ?>
					<div class="icon-container">
						<div class="icon"> <a href="http://www.kunena.org" target="_blank" title="<?php echo JText::_('COM_KUNENA_C_SUPPORTDESC');?>"> <img src="<?php echo JUri::base(true); ?>/components/com_kunena/media/icons/large/support.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_SUPPORT'); ?> </span></a> </div>
					</div>
				</div>
			</td>
		</tr>
	</table>
	</div>
	</div>

	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>