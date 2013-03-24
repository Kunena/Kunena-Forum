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
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<div class="well well-small">
			<div class="module-title nav-header"><?php echo JText::_('Forum Tools') ?></div>
			<hr class="hr-condensed">
			<div id="dashboard-icons" class="btn-group">
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=prune">
					<img src="components/com_kunena/media/icons/large/prune.png" alt="<?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?>" /><br />
					<span><?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=syncusers">
					<img src="components/com_kunena/media/icons/large/syncusers.png" alt="<?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?>" /><br />
					<span><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=recount">
					<img src="components/com_kunena/media/icons/large/recount.png" alt="<?php echo JText::_('COM_KUNENA_A_RECOUNT'); ?>" /><br />
					<span><?php echo JText::_('COM_KUNENA_A_RECOUNT'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=menu">
					<img src="components/com_kunena/media/icons/large/menu.png" alt="<?php echo JText::_('COM_KUNENA_A_MENU_MANAGER'); ?>" /><br />
					<span><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=purgerestatements">
					<img src="components/com_kunena/media/icons/large/purgerestatements.png" alt="<?php echo JText::_('COM_KUNENA_A_PURGE_RE_STATEMENTS'); ?>" /><br />
					<span><?php echo JText::_('COM_KUNENA_A_PURGE_RE_STATEMENTS'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=cleanipadresses">
					<img src="components/com_kunena/media/icons/large/cleanadressesip.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CLEAN_IP_ADRESSES'); ?>" /><br />
					<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CLEAN_IP_ADRESSES'); ?></span>
				</a>
				<?php if (KunenaForum::isDev()) { ?>
					<a class="btn" href="index.php?option=com_kunena&view=install&task=prepare&start=1&<?php echo JSession::getFormToken().'=1'; ?>">
						<img src="components/com_kunena/media/icons/large/install.png" alt="<?php echo JText::_('COM_KUNENA_GIT_INSTALL'); ?>" /><br />
						<span><?php echo JText::_('COM_KUNENA_GIT_INSTALL'); ?></span>
					</a>
				<?php } ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
