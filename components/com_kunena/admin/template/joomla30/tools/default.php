<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage CPanel
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTools $this */
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<div class="well well-small">
			<div class="module-title nav-header"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_FORUM_TOOLS') ?></div>
			<hr class="hr-condensed">
			<?php //TODO: Need to change all alt text eventually to appropriate descriptions when we redo languages. ?>
			<div id="dashboard-icons" class="btn-group">
				<?php //TODO: Move report to tools, using old view. ?>
				<a class="btn" href="index.php?option=com_kunena&view=report">
					<i class="icon-big icon-support" alt="<?php echo JText::_('COM_KUNENA_REPORT_SYSTEM') ?>"></i><br />
					<span><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=prune">
					<i class="icon-big icon-list-view" alt="<?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?>"></i><br />
					<span><?php echo JText::_('COM_KUNENA_C_PRUNETAB'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=syncusers">
					<i class="icon-big icon-shuffle" alt="<?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?>"></i><br />
					<span><?php echo JText::_('COM_KUNENA_SYNC_USERS'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=recount">
					<i class="icon-big icon-loop" alt="<?php echo JText::_('COM_KUNENA_A_RECOUNT'); ?>"></i><br />
					<span><?php echo JText::_('COM_KUNENA_A_RECOUNT'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=menu">
					<i class="icon-big icon-menu" alt="<?php echo JText::_('COM_KUNENA_A_MENU_MANAGER'); ?>"></i><br />
					<span><?php echo JText::_('COM_KUNENA_A_MENU_MANAGER'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=purgerestatements">
					<i class="icon-big icon-filter" alt="<?php echo JText::_('COM_KUNENA_A_PURGE_RE_STATEMENTS'); ?>"></i><br />
					<span><?php echo JText::_('COM_KUNENA_A_PURGE_RE_STATEMENTS'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=cleanupip">
					<i class="icon-big icon-location" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CLEANUP_IP'); ?>"></i><br />
					<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CLEANUP_IP'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=uninstall">
					<i class="icon-big icon-remove" alt="<?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PROCESS'); ?>"></i><br />
					<span><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PROCESS'); ?></span>
				</a>
				<?php if (KunenaForum::isDev()) : ?>
					<a class="btn" href="index.php?option=com_kunena&view=install">
						<img src="components/com_kunena/media/icons/large/install.png" alt="<?php echo JText::_('COM_KUNENA_GIT_INSTALL'); ?>" /><br />
						<span><?php echo JText::_('COM_KUNENA_GIT_INSTALL'); ?></span>
					</a>
				<?php endif; ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
