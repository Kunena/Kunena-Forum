<?php
/**
 * Kunena Component
 * @package         Kunena.Administrator.Template
 * @subpackage      CPanel
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();
use Joomla\CMS\Language\Text;

?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/j3/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<div class="well well-small">
			<div class="module-title nav-header">
				<i class="icon-tools"></i>
				<?php echo Text::_('COM_KUNENA_CPANEL_LABEL_TOOLS') ?>
			</div>
			<hr class="hr-condensed">
			<div id="dashboard-icons" class="btn-group">
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=report">
					<i class="icon-big icon-support"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=prune">
					<i class="icon-big icon-list-view"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_C_PRUNETAB'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=syncusers">
					<i class="icon-big icon-shuffle"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_SYNC_USERS'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=recount">
					<i class="icon-big icon-loop"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_A_RECOUNT'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=menu">
					<i class="icon-big icon-menu"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_A_MENU_MANAGER'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=purgerestatements">
					<i class="icon-big icon-filter"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_A_PURGE_RE_STATEMENTS'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=cleanupip">
					<i class="icon-big icon-location"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CLEANUP_IP'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=diagnostics">
					<i class="icon-big icon-health"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_LABEL_DIAGNOSTICS'); ?></span>
				</a>
				<a class="btn" href="index.php?option=com_kunena&view=tools&layout=uninstall">
					<i class="icon-big icon-remove"></i><br/>
					<span><?php echo Text::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PROCESS'); ?></span>
				</a>

				<?php if (KunenaForum::isDev())
					:
					?>
					<a class="btn" href="index.php?option=com_kunena&view=install">
						<i class="icon-big icon-tree-2"></i><br/>
						<span><?php echo Text::_('COM_KUNENA_GIT_INSTALL'); ?></span>
					</a>
				<?php endif; ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaAdminVersion::getLongVersionHTML(); ?>
	</div>
</div>
