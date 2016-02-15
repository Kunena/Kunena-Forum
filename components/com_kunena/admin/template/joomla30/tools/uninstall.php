<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Prune
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaAdminViewTools $this */
?>

<div class="alert alert-error">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_ALERTBOX_WARNING') ?></h4>
	<?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_ALERTBOX_DESC') ?>
</div>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN.'/template/joomla30/common/menu.php'; ?></div>
		</div>
	</div>

	<div id="j-main-container" class="span10">

		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="uninstall" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_TITLE'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<td colspan="2"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_DESC') ?></td>
					</tr>
					<tr>
						<td width="20%"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_LOGIN') ?></td>
						<td>
							<div>
								<input class="span3" type="text" name="username" value="" />
							</div>
						</td>
					</tr>
					<tr>
						<td width="20%"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_PASSWORD') ?></td>
						<td>
							<div>
								<input class="span3" type="password" name="password" value="" />
							</div>
						</td>
					</tr>
					<?php if ($this->isTFAEnabled): ?>
					<tr>
						<td width="20%"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_SECRETKEY') ?></td>
						<td>
							<div>
								<input class="span3" type="text" name="secretkey" value="" />
							</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td></td>
						<td>
							<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
									<h3 id="myModalLabel"><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_TITLE'); ?></h3>
								</div>
								<div class="modal-body">
									<p><?php echo JText::_('COM_KUNENA_TOOLS_LABEL_UNINSTALL_DESC') ?></p>
								</div>
								<div class="modal-footer">
									<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
									<button type="submit" class="btn btn-danger"><?php echo JText::_('COM_KUNENA_TOOLS_BUTTON_UNINSTALL_PROCESS') ?></button>
								</div>
							</div>

							<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModal"><?php echo JText::_('COM_KUNENA_TOOLS_BUTTON_UNINSTALL_PROCESS') ?></button>
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
