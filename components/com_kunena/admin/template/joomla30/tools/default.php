<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage CPanel
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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
					<img src="components/com_kunena/media/icons/large/report.png" alt="<?php echo JText::_('COM_KUNENA_REPORT_SYSTEM') ?>" /><br />
					<span><?php echo JText::_('COM_KUNENA_REPORT_SYSTEM'); ?></span>
				</a>
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
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=cleanupip">
					<img src="components/com_kunena/media/icons/large/cleanupip.png" alt="<?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CLEANUP_IP'); ?>" /><br />
					<span><?php echo JText::_('COM_KUNENA_CPANEL_LABEL_CLEANUP_IP'); ?></span>
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
