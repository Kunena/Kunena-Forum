<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
**/
defined( '_JEXEC' ) or die();

$styles = <<<EOF
.kadmin-welcome {
	clear:both;
	margin:10px 0;
	padding:10px;
	font-size:12px;
	color:#536482;
	line-height:140%;
	border:1px solid #ddd;
	margin-bottom: 25px;
}
.kadmin-welcome h3 {
	margin:0;
	padding:0;
}
table.thisform {
	width: 100%;
	padding: 10px;
	border-collapse: collapse;
}
table.thisform tr.row0 {
	background-color: #F7F8F9;
}
table.thisform tr.row1 {
	background-color: #eeeeee;
}
table.thisform th {
	font-size: 15px;
	font-weight: normal;
	font-variant: small-caps;
	padding-top: 6px;
	padding-bottom: 2px;
	padding-left: 4px;
	padding-right: 4px;
	text-align: left;
	height: 25px;
	color: #666666;
	background: url(../images/background.gif);
	background-repeat: repeat;
}
table.thisform td {
	padding: 3px;
	text-align: left;
}
div.icon a {
	text-decoration:none;
}
div.icon-container {
	float:left;
}
EOF;
$document = JFactory::getDocument();
$document->addStyleDeclaration($styles);
?>

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
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=config" title="<?php echo JText::_('COM_KUNENA_C_FBCONFIGDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/config.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_FBCONFIG'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=categories" title="<?php echo JText::_('COM_KUNENA_C_FORUMDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/categories.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_FORUM'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showprofiles" title="<?php echo JText::_('COM_KUNENA_C_USERDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/users.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_USER'); ?> </span> </a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showTemplates" title="<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/templates.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=smilies" title="<?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/smileys.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER');?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=ranks" title="<?php echo JText::_('COM_KUNENA_RANK_MANAGER');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/ranks.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_RANK_MANAGER'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseFiles" title="<?php echo JText::_('COM_KUNENA_C_FILESDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/files.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_FILES'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseImages" title="<?php echo JText::_('COM_KUNENA_C_IMAGESDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/images.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_IMAGES'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=prune" title="<?php echo JText::_('COM_KUNENA_C_PRUNETABDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/prune.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=syncusers" title="<?php echo JText::_('COM_KUNENA_C_SYNCEUSERSDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/syncusers.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=recount&amp;no_html=1" title="<?php echo JText::_('COM_KUNENA_RECOUNTFORUMS');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/recount.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_RECOUNTFORUMS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=trash" title="<?php echo JText::_('COM_KUNENA_TRASH_VIEW');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/trash.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=stats" title="<?php echo JText::_('COM_KUNENA_STATS_GEN_STATS');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/stats.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_STATS_GEN_STATS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=report" title="<?php echo JText::_('COM_KUNENA_REPORT_SYSTEM');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/report.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=createmenu" title="<?php echo JText::_('COM_KUNENA_CREATE_MENU');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/menu.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_CREATE_MENU'); ?> </span></a> </div>
					</div>
					<?php if (KunenaForum::isSvn()) { ?>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=install" title="<?php echo JText::_('COM_KUNENA_SVN_INSTALL');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/install.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_SVN_INSTALL'); ?> </span></a> </div>
					</div>
					<?php } ?>
					<div class="icon-container">
						<div class="icon"> <a href="http://www.kunena.org" target="_blank" title="<?php echo JText::_('COM_KUNENA_C_SUPPORTDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/media/icons/large/support.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_SUPPORT'); ?> </span></a> </div>
					</div>
				</div>
		</td>
		</tr>
	</table>
</div>
<?php if ($kunena_config->version_check) : ?>
<div class="kadmin-welcome">
	<?php echo checkLatestVersion(); ?>
</div>
<?php endif; ?>