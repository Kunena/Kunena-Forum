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
$document->addStyleSheet( JURI::base(true).'/components/com_kunena/media/css/cpanel.css' );
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JURI::base().'components/com_kunena/media/css/admin.rtl.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-config"><?php echo JText::_('COM_KUNENA_A_VIEW_TOOLS') ?></div>
	<div style="border:1px solid #ddd; background:#FBFBFB;">
	<table class="thisform">
		<tr class="thisform">
			<td width="100%" valign="top" class="thisform">
				<div id="cpanel">
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools&layout=prune') ?>" title="<?php echo JText::_('COM_KUNENA_C_PRUNETABDESC');?>"> <img src="<?php echo JURI::base(true); ?>/components/com_kunena/media/icons/large/prune.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools&layout=syncusers') ?>" title="<?php echo JText::_('COM_KUNENA_C_SYNCEUSERSDESC');?>"> <img src="<?php echo JURI::base(true); ?>/components/com_kunena/media/icons/large/syncusers.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools&layout=recount') ?>" title="<?php echo JText::_('COM_KUNENA_RECOUNTFORUMS');?>"> <img src="<?php echo JURI::base(true); ?>/components/com_kunena/media/icons/large/recount.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_RECOUNT'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools&layout=menu') ?>" title="<?php echo JText::_('COM_KUNENA_A_MENU_MANAGER');?>"> <img src="<?php echo JURI::base(true); ?>/components/com_kunena/media/icons/large/menu.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_MENU_MANAGER'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools&layout=purgerestatements') ?>" title="<?php echo JText::_('COM_KUNENA_A_PURGE_RE_STATEMENTS');?>"> <img src="<?php echo JURI::base(true); ?>/components/com_kunena/media/icons/large/purgerestatements.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_PURGE_RE_STATEMENTS'); ?> </span></a> </div>
					</div>
					<?php if (KunenaForum::isDev()) { ?>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=install&task=prepare&start=1&'.JUtility::getToken().'=1') ?>" title="<?php echo JText::_('COM_KUNENA_GIT_INSTALL');?>"> <img src="<?php echo JURI::base(true); ?>/components/com_kunena/media/icons/large/install.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_GIT_INSTALL'); ?> </span></a> </div>
					</div>
					<?php } ?>
				</div>
		</td>
		</tr>
	</table>
</div>
</div>
</div>