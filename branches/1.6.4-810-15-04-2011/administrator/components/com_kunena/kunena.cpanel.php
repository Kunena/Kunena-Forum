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
/** ensure this file is being included by a parent file */
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
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showconfig" title="<?php echo JText::_('COM_KUNENA_C_FBCONFIGDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/kconfig.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_FBCONFIG'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showAdministration" title="<?php echo JText::_('COM_KUNENA_C_FORUMDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/kforumadm.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_FORUM'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showprofiles" title="<?php echo JText::_('COM_KUNENA_C_USERDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/kuser.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_USER'); ?> </span> </a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showTemplates" title="<?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/templatemanager.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_A_TEMPLATE_MANAGER'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseFiles" title="<?php echo JText::_('COM_KUNENA_C_FILESDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/kfiles.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_FILES'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=browseImages" title="<?php echo JText::_('COM_KUNENA_C_IMAGESDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/kimages.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_IMAGES'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=pruneforum" title="<?php echo JText::_('COM_KUNENA_C_PRUNETABDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/ktable.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=syncusers" title="<?php echo JText::_('COM_KUNENA_C_SYNCEUSERSDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/kusers.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="http://www.kunena.org" target="_blank" title="<?php echo JText::_('COM_KUNENA_C_SUPPORTDESC');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/ktechsupport.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_C_SUPPORT'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showsmilies" title="<?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/ksmiley.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_EMOTICONS_EMOTICON_MANAGER');?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=recount&amp;no_html=1" title="<?php echo JText::_('COM_KUNENA_RECOUNTFORUMS');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/kupgrade.png" align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_RECOUNTFORUMS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=ranks" title="<?php echo JText::_('COM_KUNENA_RANK_MANAGER');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/kranks.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_RANK_MANAGER'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=createmenu" title="<?php echo JText::_('COM_KUNENA_CREATE_MENU');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/menu.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_CREATE_MENU'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showtrashview" title="<?php echo JText::_('COM_KUNENA_TRASH_VIEW');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/trash.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_TRASH_VIEW'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showstats" title="<?php echo JText::_('COM_KUNENA_STATS_GEN_STATS');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/stats.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_STATS_GEN_STATS'); ?> </span></a> </div>
					</div>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;task=showsystemreport" title="<?php echo JText::_('COM_KUNENA_REPORT_SYSTEM');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/report_conf.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?> </span></a> </div>
					</div>
					<?php if (Kunena::isSvn()) { ?>
					<div class="icon-container">
						<div class="icon"> <a href="<?php echo JURI::base(); ?>index.php?option=com_kunena&amp;view=install" title="<?php echo JText::_('COM_KUNENA_SVN_INSTALL');?>"> <img src="<?php echo JURI::base(); ?>components/com_kunena/images/install.png"  align="middle" border="0" alt="" /> <span> <?php echo JText::_('COM_KUNENA_SVN_INSTALL'); ?> </span></a> </div>
					</div>
					<?php } ?>
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